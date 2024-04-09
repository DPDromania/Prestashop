<?php
/** **/

if (!defined('_PS_VERSION_'))
	exit;

class DpdGeopostCarrier extends DpdGeopostObjectModel
{
	public $id_dpd_geopost_carrier;

	public $id_carrier;

	public $id_reference;

	public $date_add;

	public $date_upd;

	public function __construct($id_dpd_geopost_carrier = null)
	{
		parent::__construct($id_dpd_geopost_carrier);
	}

	public static $definition = array(
		'table' => _DPDGEOPOST_CARRIER_DB_,
		'primary' => 'id_dpd_geopost_carrier',
		'multilang_shop' => true,
		'multishop' => true,
		'fields' => array(
			'id_dpd_geopost_carrier'	=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_carrier'				=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_reference'				=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'date_add' 					=> 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' 					=> 	array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		)
	);

	public static function getReferenceByIdCarrier($id_carrier)
	{
		return DB::getInstance()->getValue('
			SELECT `id_reference`
			FROM `'._DB_PREFIX_._DPDGEOPOST_CARRIER_DB_.'`
			WHERE `id_carrier` = "'.(int)$id_carrier.'"
		');
	}

	public static function getIdCarrierByReference($id_reference)
	{
        return DB::getInstance()->getValue('
			SELECT MAX(`id_carrier`)
			FROM `'._DB_PREFIX_._DPDGEOPOST_CARRIER_DB_.'`
			WHERE `id_reference` = "'.(int)$id_reference.'"
		');
	}
}