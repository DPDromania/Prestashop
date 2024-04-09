<?php
/** **/

if (!defined('_PS_VERSION_'))
	exit;


class DpdGeopostCarrierInternationalCODService extends DpdGeopostService
{
	const FILENAME = 'dpd_international_cod.service';

	public function __construct()
	{
		parent::__construct();
	}

	public static function install()
	{
		$id_carrier = (int)Configuration::get(DpdGeopostConfiguration::CARRIER_INTERNATIONAL_COD_ID);
		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$id_carrier);
			$carrier = new Carrier((int)$id_carrier);
		}
		else
			$carrier = Carrier::getCarrierByReference($id_carrier);

		if ($id_carrier && Validate::isLoadedObject($carrier))
			if (!$carrier->deleted)
				return true;
			else
			{
				$carrier->deleted = 0;
				return (bool)$carrier->save();
			}

		$carrier_international_cod_service = new DpdGeopostCarrierInternationalCODService();

		$carrier = new Carrier();
		$carrier->name = $carrier_international_cod_service->module_instance->l('DPD International + COD', self::FILENAME);
		$carrier->active = 1;
		$carrier->is_free = 0;
		$carrier->shipping_handling = 0;
		$carrier->shipping_external = 1;
		$carrier->shipping_method = 1;
        $carrier->max_width = 999999;
        $carrier->max_height = 999999;
        $carrier->max_height = 999999;
        $carrier->max_weight = 999999;
        $carrier->max_depth = 999999;
		$carrier->grade = 0;
		$carrier->is_module = 1;
		$carrier->need_range = 1;
		$carrier->range_behavior = 0;
		$carrier->external_module_name = $carrier_international_cod_service->module_instance->name;
		$carrier->url = _DPDGEOPOST_TRACKING_URL_;

		$delay = array();
		foreach (Language::getLanguages(false) as $language)
			$delay[$language['id_lang']] = $carrier_international_cod_service->module_instance->l('DPD International + COD', self::FILENAME);
		$carrier->delay = $delay;

		if (!$carrier->save())
			return false;

        self::setDefaultRangeValues($carrier);

        $dpdgeopost_carrier = new DpdGeopostCarrier();
		$dpdgeopost_carrier->id_carrier = (int)$carrier->id;
		$dpdgeopost_carrier->id_reference = (int)$carrier->id;

		if (!$dpdgeopost_carrier->save())
			return false;

		if (!copy(_DPDGEOPOST_IMG_DIR_.DpdGeopostCarrierInternationalCODService::IMG_DIR.'/'._DPDGEOPOST_INTERNATIONAL_ID_.'.'.DpdGeopostCarrierInternationalCODService::IMG_EXTENTION, _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg'))
			return false;

		foreach ($carrier_international_cod_service->continents as $continent => $value)
			if ($value && !$carrier->addZone($continent))
				return false;

		$groups = array();
		foreach (Group::getGroups((int)Context::getContext()->language->id) as $group)
			$groups[] = $group['id_group'];

		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			if (!self::setGroups14((int)$carrier->id, $groups))
				return false;
		}
		else
			if (!$carrier->setGroups($groups))
				return false;

		if (!Configuration::updateValue(DpdGeopostConfiguration::CARRIER_INTERNATIONAL_COD_ID, (int)$carrier->id))
			return false;

		return true;
	}

	public static function delete()
	{
		return (bool)self::deleteCarrier((int)Configuration::get(DpdGeopostConfiguration::CARRIER_INTERNATIONAL_COD_ID));
	}
}
