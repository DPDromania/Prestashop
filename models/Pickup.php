<?php
/** **/

if (!defined('_PS_VERSION_'))
	exit;

class DpdGeopostPickup extends DpdGeopostWs
{
	protected $targetNamespace = 'http://it4em.yurticikargo.com.tr/eshop/pickuporder';
	protected $serviceName = 'PickupOrderServiceImpl';

	public $id_shipment;

	public $date;

	public $fromTime;

	public $toTime;

	public $contactEmail;

	public $contactName;

	public $contactPhone;

	public $specialInstruction = null;

	public $referenceNumber;

	public function arrange()
	{
		$pieces_sorted_by_country = $this->formatPieces();

		if (self::$errors)
			return false;

		foreach ($pieces_sorted_by_country as $pieces)
		{

			$phoneNumber = pSQL($this->contactPhone);

			$pickupParams = array(
				'visitEndTime' => "16:00",
				'contactName' => pSQL($this->contactName),
				'explicitShipmentIdList' => $this->id_shipment,
			);

			if($phoneNumber) {
				$pickupParams['phoneNumber'] = array(
					'number' => $phoneNumber
				);
			}

			$pickupResult = $this->wsrest_pickup($pickupParams);

			if (!self::$errors)
				$this->recordPickUpDate($pickupResult);
			else
				return false;
		}

		if (self::$errors)
			return false;

		return true;
	}

	private function recordPickupDateSingular($id_shipment, $pickupDate) {
		if(!Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
				SET `date_pickup`="'.pSQL($id_shipment).'"
				WHERE `id_shipment`='.(double)$id_shipment
		))

		return true;
	}

	private function recordPickUpDate($pickupResult)
	{

        if (!isset($pickupResult['orders'])) {
            return true;
        }

		foreach($pickupResult['orders'] as $order) {
			foreach($order['shipmentIds'] as $shipmentId) {
				$time = date('Y-m-d H:i', strtotime($order['pickupPeriodTo']));


				if (!Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
					SET `date_pickup`="'.pSQL($time).'"
					WHERE `id_shipment`='.(double)$shipmentId )
				) {
					self::$errors[] = sprintf($this->l('Pickup was successfully created, but could not be recorded locally for shipment #%d'));
				}

			}
		}


		return true;
	}

	private function formatPieces()
	{
		if (!$this->id_shipment)
		{
			self::$errors[] = $this->l('Shipment ID is missing');
			return false;
		}

		$pieces = array();

		if (!is_array($this->id_shipment))
			$this->id_shipment = array($this->id_shipment);

		foreach ($this->id_shipment as $id_shipment)
		{
			$id_order = (int)DpdGeopostShipment::getOrderIdByShipmentId($id_shipment);
			$shipment = new DpdGeopostShipment($id_order);

			if (!(int)$shipment->id_order)
			{
				self::$errors[] = sprintf($this->l('Order #%d does not exists'), (int)$shipment->id_order);
				return false;
			}

			if (!isset($pieces[$shipment->receiverCountryCode]))
				$pieces[$shipment->receiverCountryCode] = array();

			$pieces[$shipment->receiverCountryCode][] = array(
				'serviceCode' => (int)$shipment->mainServiceCode,
				'quantity' => count($shipment->parcels),
				'weight' => (float)$shipment->getTotalParcelsWeight(),
				'destinationCountryCode' => pSQL($shipment->receiverCountryCode),
				'id_shipment' => (int)$id_shipment
			);
		}

		return $pieces;
	}
}
