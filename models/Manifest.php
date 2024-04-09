<?php
/** **/

if (!defined('_PS_VERSION_'))
	exit;

class DpdGeopostManifest extends DpdGeopostWs
{
	protected 	$targetNamespace = 'http://it4em.yurticikargo.com.tr/eshop/manifest';
	protected 	$serviceName = 'ManifestServiceImpl';

	public		$id_manifest;
	public		$manifestReferenceNumber = null; // generated random string later on if not defined
	public		$manifestNotes = null; // comment
	public		$shipments = array(); // array of Shipment ID's

	private	  	$manifestPrintOption = 'PrintManifestWithUnprintedParcels'; // 'PrintOnlyManifest';
	private		$printOption = 'Pdf';
	private		$shipmentReferenceList = array();
	private		$action = 'closeAndPrint';


	public function printManifest()
	{
		foreach ($this->shipments as $id_shipment)
			if (Db::getInstance()->getValue('
				SELECT `id_manifest`
				FROM `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
				WHERE `id_shipment`='.(double)$id_shipment)
			)
				$this->action = 'reprint';

		$params = $this->formatRequestParams();

		$printOptions = array(
			'manifestPrintOption' => pSQL($this->manifestPrintOption),
			'printOption' => pSQL($this->printOption)
		);


		return call_user_func(array($this, $this->action), $params, $printOptions);
	}

	private function closeAndPrint($params, $printOptions)
	{
		$result = $this->closeManifest('manifest', $params, $printOptions);

		if (!reset(self::$errors))
		{
			if (isset($result['pdfManifestFile']))
			{
				$this->id_manifest = (double)$result['manifestId'];

				if(!$this->updateManifestStatus())
				{
					self::$errors[] = $this->l('Could not update manifest status locally');
					return false;
				}

				return base64_decode($result['pdfManifestFile']);
			}
			else
			{
				self::$errors[] = $this->l('PDF file cannot be generated');
				return false;
			}
		}
		else
			return false;

		return false;
	}

	private function reprint($params, $printOptions)
	{
		$result = $this->reprintManifest(null, $params, $printOptions);

		if (!reset(self::$errors))
		{
			if (isset($result['pdfManifestFile']))
				return base64_decode($result['pdfManifestFile']);
			else
			{
				self::$errors[] = $this->l('PDF file cannot be generated');
				return false;
			}
		}
		else
			return false;

		return false;
	}

	private function formatRequestParams()
	{
		if (!$this->manifestReferenceNumber)
			$this->manifestReferenceNumber = $this->generateReference();

		$params = array(
			'manifestReferenceNumber' => pSQL($this->manifestReferenceNumber),
			'manifestNotes' => pSQL($this->manifestNotes)
		);

		foreach ($this->shipments as $id_shipment)
			if ($this->action == 'reprint')
				$this->shipmentReferenceList[] = array('id' => (double)$this->getIdManifestByIdShipment($id_shipment));
			else
				$this->shipmentReferenceList[] = array('id' => (double)$id_shipment);

		$name = ($this->action == 'closeAndPrint') ? 'shipmentReferenceList' : 'manifestReference';

		$params[] = array(
			'name' => pSQL($name),
			'data' => $this->shipmentReferenceList
		);

		return $params;
	}

	private function getIdManifestByIdShipment($id_shipment)
	{
		return (int)Db::getInstance()->getValue('
			SELECT `id_manifest`
			FROM `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
			WHERE `id_shipment`='.(double)$id_shipment
		);
	}

	private function generateReference()
	{
		return Tools::strtoupper(Tools::passwdGen(9));
	}

	private function updateManifestStatus()
	{
		if (!$this->id_manifest)
			return false;

		foreach ($this->shipments as $id_shipment)
			if (!Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_._DPDGEOPOST_SHIPMENT_DB_.'`
				SET `id_manifest`='.(double)$this->id_manifest.'
				WHERE `id_shipment`='.(double)$id_shipment)
			)
				return false;
		return true;
	}
}
