<?php
/** **/

class DpdGeopostCSV extends DpdGeopostObjectModel
{
	public $id_shop;
	public $date_add;
	public $date_upd;

	public $id_csv;
	public $country;
	public $region;
	public $zip;
	public $weight_from;
	public $weight_to;
	public $shipping_price;
	public $shipping_price_percentage;
	public $currency;
	public $method_id;
	public $cod_surcharge;
	public $cod_surcharge_percentage;
	public $cod_min_surcharge;

	const COLUMN_COUNTRY 					= 0;
	const COLUMN_REGION 					= 1;
	const COLUMN_ZIP 						= 2;
	const COLUMN_WEIGHT_FROM 				= 3;
	const COLUMN_WEIGHT_TO 					= 4;
	const COLUMN_SHIPPING_PRICE 			= 5;
	const COLUMN_SHIPPING_PERCENTAGE		= 6;
	const COLUMN_CURRENCY					= 7;
	const COLUMN_METHOD_ID 					= 8;
	const COLUMN_COD_SURCHARGE 				= 9;
	const COLUMN_COD_SURCHARGE_PERCENTAGE	= 10;
	const COLUMN_COD_MIN_SURCHARGE			= 11;

	const CSV_FILE 							= 'DPD_GEOPOST_CSV_FILE';

	public static $definition = array(
		'table' => _DPDGEOPOST_CSV_DB_,
		'primary' => 'id_csv',
		'multilang' => false,
		'multishop' => false,
		'fields' => array(
			'id_csv'					=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_shop'					=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'country'					=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'region'					=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'zip'						=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'weight_from'				=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'weight_to'					=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'shipping_price'			=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'shipping_price_percentage'	=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'currency'					=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'method_id'					=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'cod_surcharge'				=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'cod_surcharge_percentage'	=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'cod_min_surcharge'			=>	array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
			'date_add'					=>	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd'					=>	array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		)
	);

	public static function getAllData($limit = '')
	{
		return DB::getInstance()->executeS('
			SELECT `id_csv`, `country`, `region`, `zip`, `weight_from`, `weight_to`, `shipping_price`, `shipping_price_percentage`, `currency`,
				`method_id`, `cod_surcharge`, `cod_surcharge_percentage`, `cod_min_surcharge`
			FROM `'._DB_PREFIX_._DPDGEOPOST_CSV_DB_.'`
			WHERE `id_shop` = "'.(int)Context::getContext()->shop->id.'"
			'.$limit
		);
	}

	public static function deleteAllData()
	{
		return DB::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_._DPDGEOPOST_CSV_DB_.'`
			WHERE `id_shop` = "'.(int)Context::getContext()->shop->id.'"
		');
	}

	public static function getCSVData()
	{
		return DB::getInstance()->executeS('
			SELECT `country`, `region`, `zip`, `weight_from`, `weight_to`, `shipping_price`, `shipping_price_percentage`, `currency`,
				`shipping_price_percentage`, `method_id`, `cod_surcharge`, `cod_surcharge_percentage`, `cod_min_surcharge`
			FROM `'._DB_PREFIX_._DPDGEOPOST_CSV_DB_.'`
			WHERE `id_shop` = "'.(int)Context::getContext()->shop->id.'"
		');
	}
}