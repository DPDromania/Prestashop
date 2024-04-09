<?php

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/dpdgeopost.php'); /*module core*/

$module_instance = new DpdGeopost;

if (!Tools::isSubmit('token') || (Tools::isSubmit('token')) && Tools::getValue('token') != sha1(_COOKIE_KEY_.$module_instance->name)) exit;

if (Tools::isSubmit('printLabels'))
{
	$shipment = new DpdGeopostShipment((int)Tools::getValue('id_order'));

    $dpd_print_format = Configuration::get(DpdGeopostConfiguration::DPD_PRINT_FORMAT);

	$pdf_file_contents = $shipment->getLabelsPdf();

	if ($pdf_file_contents) {
        switch(strtoupper($dpd_print_format)) {
            case 'PDF':
                header('Content-type: application/pdf');
                header('Content-Disposition: attachment; filename="shipment_labels_' . (int)Tools::getValue('id_order') . '.pdf"');
                break;

            case 'HTML':
                header('Content-type: application/html');
                header('Content-Disposition: attachment; filename="shipment_labels_' . (int)Tools::getValue('id_order') . '.html"');
                break;

            case 'ZPL':
                header('Content-type: application/zpl');
                header('Content-Disposition: attachment; filename="shipment_labels_' . (int)Tools::getValue('id_order') . '.zpl"');
                break;

        }

		echo $pdf_file_contents;
	} else {
		echo reset(DpdGeopostShipment::$errors);
		exit;
	}
}

if(Tools::isSubmit('printVouchers')) {
	$shipment = new DpdGeopostShipment((int)Tools::getValue('id_order'));

	$pdf_file_contents = $shipment->getVouchersPdf();

	if ($pdf_file_contents) {
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="shipment_vouchers_'.(int)Tools::getValue('id_order').'.pdf"');
		echo $pdf_file_contents;
	} else {
		echo reset(DpdGeopostShipment::$errors);
		exit;
	}
}
