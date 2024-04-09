<?php

if (!defined('_PS_VERSION_'))
	exit;

/* URI constants */

if (!defined('_DPDGEOPOST_MODULE_URI_'))
	define('_DPDGEOPOST_MODULE_URI_', _MODULE_DIR_.'dpdgeopost/');

if (!defined('_DPDGEOPOST_MODULE_DIR_'))
	define('_DPDGEOPOST_MODULE_DIR_', _PS_MODULE_DIR_.'dpdgeopost/');

if (!defined('_DPDGEOPOST_CSS_URI_'))
	define('_DPDGEOPOST_CSS_URI_', _DPDGEOPOST_MODULE_URI_.'css/');

if (!defined('_DPDGEOPOST_JS_URI_'))
	define('_DPDGEOPOST_JS_URI_', _DPDGEOPOST_MODULE_URI_.'js/');

if (!defined('_DPDGEOPOST_IMG_URI_'))
	define('_DPDGEOPOST_IMG_URI_', _DPDGEOPOST_MODULE_URI_.'img/');

if (!defined('_DPDGEOPOST_IMG_DIR_'))
	define('_DPDGEOPOST_IMG_DIR_', _DPDGEOPOST_MODULE_DIR_.'img/');

if (!defined('_DPDGEOPOST_AJAX_URI_'))
	define('_DPDGEOPOST_AJAX_URI_', _DPDGEOPOST_MODULE_URI_.'dpdgeopost.ajax.php');

if (!defined('_DPD_ADMIN_AJAX_URL_'))
	define('_DPD_ADMIN_AJAX_URL_', _DPDGEOPOST_MODULE_URI_.'dpdgeopost.php');

if (!defined('_DPDGEOPOST_PDF_URI_'))
	define('_DPDGEOPOST_PDF_URI_', _DPDGEOPOST_MODULE_URI_.'dpdgeopost.pdf.php');

/* Directories constants */

if (!defined('_DPDGEOPOST_CLASSES_DIR_'))
	define('_DPDGEOPOST_CLASSES_DIR_', _DPDGEOPOST_MODULE_DIR_.'classes/');

if (!defined('_DPDGEOPOST_TPL_DIR_'))
	define('_DPDGEOPOST_TPL_DIR_', _DPDGEOPOST_MODULE_DIR_.'views/templates/');

if (!defined('_DPDGEOPOST_MODELS_DIR_'))
	define('_DPDGEOPOST_MODELS_DIR_', _DPDGEOPOST_MODULE_DIR_.'models/');

/*  */

if (!defined('_DPDGEOPOST_CSV_DB_'))
	define('_DPDGEOPOST_CSV_DB_', 'dpdgeopost_price_rules');

if (!defined('_DPDGEOPOST_CARRIER_DB_'))
	define('_DPDGEOPOST_CARRIER_DB_', 'dpdgeopost_carrier');

if (!defined('_DPDGEOPOST_SHIPMENT_DB_'))
	define('_DPDGEOPOST_SHIPMENT_DB_', 'dpdgeopost_shipment');

if (!defined('_DPDGEOPOST_PARCEL_DB_'))
	define('_DPDGEOPOST_PARCEL_DB_', 'dpdgeopost_parcel');

if (!defined('_DPDGEOPOST_REFERENCE_DB_'))
	define('_DPDGEOPOST_REFERENCE_DB_', 'dpdgeopost_reference');

if (!defined('_DPDGEOPOST_REST_DPD_POSTCODES_DB_'))
	define('_DPDGEOPOST_REST_DPD_POSTCODES_DB_', 'rest_dpd_postcodes');

if (!defined('_DPDGEOPOST_REST_DPD_ADDRESS_DB_'))
	define('_DPDGEOPOST_REST_DPD_ADDRESS_DB_', 'dpd_postcode_address');

if (!defined('_DPDGEOPOST_CSV_DELIMITER_'))
	define('_DPDGEOPOST_CSV_DELIMITER_', ';');

if (!defined('_DPDGEOPOST_CSV_FILENAME_'))
	define('_DPDGEOPOST_CSV_FILENAME_', 'dpdgeopost');

if (!defined('_DPDGEOPOST_CLASSIC_ID_'))
	define('_DPDGEOPOST_CLASSIC_ID_', 2002);

if (!defined('_DPDGEOPOST_CLASSIC_1_PARCEL_ID_'))
	define('_DPDGEOPOST_CLASSIC_1_PARCEL_ID_', 2003);

if (!defined('_DPDGEOPOST_LOCCO_ID_'))
	define('_DPDGEOPOST_LOCCO_ID_', 2113);

if (!defined('_DPDGEOPOST_LOCCO_1_PARCEL_ID_'))
	define('_DPDGEOPOST_LOCCO_1_PARCEL_ID_', 2114);

if (!defined('_DPDGEOPOST_CLASSIC_BALKAN_ID_'))
	define('_DPDGEOPOST_CLASSIC_BALKAN_ID_', 2212);

if (!defined('_DPDGEOPOST_CLASSIC_INTERNATIONAL_ID_'))
	define('_DPDGEOPOST_CLASSIC_INTERNATIONAL_ID_', 2303);

if (!defined('_DPDGEOPOST_CLASSIC_PALLET_ONE_ROMANIA_ID_'))
	define('_DPDGEOPOST_CLASSIC_PALLET_ONE_ROMANIA_ID_', 2412);

if (!defined('_DPDGEOPOST_CLASSIC_POLAND_ID_'))
	define('_DPDGEOPOST_CLASSIC_POLAND_ID_', 2304);

if (!defined('_DPDGEOPOST_STANDARD_24_ID_'))
	define('_DPDGEOPOST_STANDARD_24_ID_', 2505 );

if (!defined('_DPDGEOPOST_FASTIUS_EXPRESS_ID_'))
	define('_DPDGEOPOST_FASTIUS_EXPRESS_ID_', 2111  );

if (!defined('_DPDGEOPOST_FASTIUS_EXPRESS_2H_ID_'))
	define('_DPDGEOPOST_FASTIUS_EXPRESS_2H_ID_', 2112  );

if (!defined('_DPDGEOPOST_PALLET_ONE_ROMANIA_ID_'))
	define('_DPDGEOPOST_PALLET_ONE_ROMANIA_ID_', 2432  );


if (!defined('_DPDGEOPOST_INTERNATIONAL_ID_'))
	define('_DPDGEOPOST_INTERNATIONAL_ID_', 40033);

if (!defined('_DPDGEOPOST_REGIONAL_EXPRESS_ID_'))
	define('_DPDGEOPOST_REGIONAL_EXPRESS_ID_', 40107);

if (!defined('_DPDGEOPOST_HUNGARY_ID_'))
	define('_DPDGEOPOST_HUNGARY_ID_', 40171);

if (!defined('_DPDGEOPOST_LOCKER_ID_'))
    define('_DPDGEOPOST_LOCKER_ID_', 25051 );

if(!defined('_DPDGEOPOST_CARGO_REGIONAL_ID_')) define('_DPDGEOPOST_CARGO_REGIONAL_ID_', 2214);

if(!defined('_DPDGEOPOST_CLASSIC_INTERNATIONAL_CR_ID_')) define('_DPDGEOPOST_CLASSIC_INTERNATIONAL_CR_ID_', 2323);

if(!defined('_DPDGEOPOST_CLASIC_POLONIA_CR_ID_')) define('_DPDGEOPOST_CLASIC_POLONIA_CR_ID_', 2324);

if(!defined('_DPDGEOPOST_CARGO_NATIONAL_ID_')) define('_DPDGEOPOST_CARGO_NATIONAL_ID_', 2005);

if(!defined('_DPDGEOPOST_INTERNATIONAL_EXPRESS_ID_')) define('_DPDGEOPOST_INTERNATIONAL_EXPRESS_ID_', 2302);



if (!defined('_DPDGEOPOST_DEFAULT_WEIGHT_UNIT_'))
	define('_DPDGEOPOST_DEFAULT_WEIGHT_UNIT_', 'kg');

if (!defined('_DPDGEOPOST_TRACKING_URL_'))
	define('_DPDGEOPOST_TRACKING_URL_', 'https://tracking.dpd.ro/?shipmentNumber=@&language=ro');

if (!defined('_DPDGEOPOST_COOKIE_'))
	define('_DPDGEOPOST_COOKIE_', 'dpdgeopost_cookie');
