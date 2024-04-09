<?php
/** **/

if (!defined('_PS_VERSION_'))
	exit;


class DpdGeopostShipmentController extends DpdGeopostController
{
	const DEFAULT_ORDER_BY 	= 'id_shipment';
	const DEFAULT_ORDER_WAY = 'desc';
	const FILENAME 			= 'shipmentsList.controller';

	public function __construct()
	{
		parent::__construct();
		$this->init();
	}

	private function init()
	{
		if (Tools::isSubmit('printManifest')) {

			$shipmentIds = Tools::getValue('ShipmentsBox');

			if (is_array($shipmentIds) && !empty($shipmentIds)) {
				$manifest = new DpdGeopostManifest;
				$manifest->shipments = $shipmentIds;

				$pdfContent= $manifest->printManifest();

				if ($pdfContent = $manifest->printManifest()) {

					foreach ($shipmentIds as $shipmentId) {
						$shipment = new DpdGeopostShipment;
						$result = $shipment->getAndSaveTrackingInfo($shipmentId);
					}
					Tools::redirectAdmin($this->module_instance->module_url . '&menu=shipment_list');
					exit();
					/*header('Content-type: application/pdf');
					header('Content-Disposition: attachment; filename="manifest_'.time().'.pdf"');
					echo $pdfContent;
					die();*/
				} else {
					$this->module_instance->outputHTML(
						$this->module_instance->displayError(
							reset(DpdGeopostManifest::$errors)
						)
					);
				}
			} else {
				$this->module_instance->outputHTML($this->module_instance->displayErrors(array($this->l('No selected shipments'))));
			}
		}

		if (Tools::isSubmit('printLabels')) {
			if ($shipmentIds = Tools::getValue('ShipmentsBox')) {

				foreach($shipmentIds as $shipmentId) {
					$individualShipment = new DpdGeopostShipment(null, $shipmentId);
					$tracking = $individualShipment->getAndSaveTrackingInfo($shipmentId);
				}


				$shipment = new DpdGeopostShipment;
				if ($pdfContent = $shipment->getLabelsPdf($shipmentIds)) {
					header('Content-type: application/pdf');
					header('Content-Disposition: attachment; filename="shipment_labels' . time() . '.pdf"');
					echo $pdfContent;
					die();
				} else {
					$this->module_instance->outputHTML(
						$this->module_instance->displayError(
							reset(DpdGeopostManifest::$errors)
						)
					);
				}
			} else {
				$this->module_instance->outputHTML(
					$this->module_instance->displayError(
						$this->l('Select at least one shipment')
					)
				);
			}
		}

		if (Tools::isSubmit('changeOrderStatus')) {
			if ($shipmentIds = Tools::getValue('ShipmentsBox')) {
				foreach ($shipmentIds as $id_shipment) {
					$id_order = DpdGeopostShipment::getOrderIdByShipmentId((int)$id_shipment);

					if (!self::changeOrderStatusToShipped($id_order)) {
						self::$errors[] = sprintf($this->l('Can not continue: shipment #%d order status could not be updated'), $id_shipment);
						break;
					}
				}

				if (self::$errors) {
					$this->module_instance->outputHTML(
						$this->module_instance->displayError(
							reset(self::$errors)
						)
					);
				} else {
					DpdGeopost::addFlashMessage($this->l('Selected orders statuses were successfully updated'));
					Tools::redirectAdmin($this->module_instance->module_url . '&menu=shipment_list');
				}
			} else {
				$this->module_instance->outputHTML(
					$this->module_instance->displayError(
						$this->l('Select at least one shipment')
					)
				);
			}
		}
	}

	public function getShipmentList()
	{
		$keys_array = array('id_shipment', 'date_shipped', 'id_order', 'date_add', 'carrier', 'customer', 'quantity', 'manifest', 'date_pickup');

		if (Tools::isSubmit('submitFilterButtonShipments'))
			foreach ($_POST as $key => $value) {
				if (strpos($key, 'ShipmentsFilter_') !== false) // looking for filter values in $_POST
					{
						if (is_array($value))
							$this->context->cookie->$key = serialize($value);
						else
							$this->context->cookie->$key = pSQL($value);
					}
			}

		if (Tools::isSubmit('submitResetShipments')) {
			foreach ($keys_array as $key) {
				if ($this->context->cookie->__isset('ShipmentsFilter_' . $key)) {
					$this->context->cookie->__unset('ShipmentsFilter_' . $key);
					$_POST['ShipmentsFilter_' . $key] = null;
				}
			}
		}

		$page = (int)Tools::getValue('submitFilterShipments');
		if (!$page)
			$page = 1;
		$selected_pagination = (int)Tools::getValue('pagination', $this->pagination[0]);
		$start = ($selected_pagination * $page) - $selected_pagination;

		$order_by = Tools::getValue('ShipmentOrderBy', self::DEFAULT_ORDER_BY);
		$order_way = Tools::getValue('ShipmentOrderWay', self::DEFAULT_ORDER_WAY);

		$filter = $this->getFilterQuery($keys_array, 'Shipments');

		$shipment = new DpdGeopostShipment();
		$shipments = $shipment->getShipmentList($order_by, $order_way, $filter, $start, $selected_pagination);
		$list_total = count($shipment->getShipmentList($order_by, $order_way, $filter, null, null));

		$total_pages = ceil($list_total / $selected_pagination);

		if (!$total_pages)
			$total_pages = 1;

		$shipments_count = count($shipments);
		for ($i = 0; $i < $shipments_count; $i++) {
			$order = new Order((int)$shipments[$i]['id_order']);
			$carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
			$shipments[$i]['carrier_url'] = $carrier->url;
		}

		$this->context->smarty->assign(array(
			'full_url' 				=> $this->module_instance->module_url . '&menu=shipment_list&ShipmentOrderBy=' . $order_by . '&ShipmentOrderWay=' . $order_way,
			'employee' 				=> $this->context->employee,
			'shipments'			  	=> $shipments,
			'page'				  	=> $page,
			'selected_pagination'   => $selected_pagination,
			'pagination'			=> $this->pagination,
			'total_pages'			=> $total_pages,
			'list_total'			=> $list_total,
			'order_by'	   			=> $order_by,
			'order_way'	  			=> $order_way,
			'order_link'			=> 'index.php?controller=AdminOrders&vieworder&token=' . Tools::getAdminTokenLite('AdminOrders')
		));

		return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_ . 'admin/shipment_list.tpl');
	}

	public static function changeOrderStatusToShipped($id_order)
	{
		if (!$id_order)
			return false;

		$order = new Order((int)$id_order);

		if ($order->current_state == Configuration::get('PS_OS_SHIPPING'))
			return true;

		if ($order->setCurrentState((int)Configuration::get('PS_OS_SHIPPING'), (int)Context::getContext()->employee->id) === false)
			return false;

		return true;
	}
}
