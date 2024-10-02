<?php
/** **/

if (!defined('_PS_VERSION_'))
	exit;

class DpdGeopostConfiguration extends DpdGeopostObjectModel
{
	const PRODUCTION_MODE 							= 'DPD_GEOPOST_PRODUCTION_MODE';
	const DEBUG_MODE								= 'DPD_GEOPOST_DEBUG_MODE';
	const DEBUG_FILE								= 'DPD_GEOPOST_DEBUG_FILE';
	const ADDRESS_VALIDATION 						= 'DPD_GEOPOST_ADDRESS_VALIDATION';

    const API_URL = 'https://api.dpd.ro/v1/';
	const SERVICE_CLASSIC = 'DPD_GEOPOST_SERVICE_CLASSIC';

    const SERVICE_CARGO_REGIONAL = 'DPD_GEOPOST_SERVICE_CARGO_REGIONAL';
    const SERVICE_CLASSIC_INTERNATIONAL_CR = 'DPD_GEOPOST_SERVICE_CLASSIC_INTERNATIONAL_CR';
    const SERVICE_CLASIC_POLONIA_CR = 'DPD_GEOPOST_SERVICE_CLASIC_POLONIA_CR';
    const SERVICE_CARGO_NATIONAL = 'DPD_GEOPOST_SERVICE_CARGO_NATIONAL';
    const SERVICE_INTERNATIONAL_EXPRESS = 'DPD_GEOPOST_SERVICE_INTERNATIONAL_EXPRESS';

	const SERVICE_CLASSIC_1_PARCEL = 'DPD_GEOPOST_SERVICE_CLASSIC_1_PARCEL';
	const SERVICE_LOCCO = 'DPD_GEOPOST_SERVICE_LOCCO';
	const SERVICE_LOCCO_1_PARCEL = 'DPD_GEOPOST_SERVICE_LOCCO_1_PARCEL';
	const SERVICE_CLASSIC_BALKAN = 'DPD_GEOPOST_SERVICE_CLASSIC_BALKAN';
    const SERVICE_STANDARD_LOCKER = 'DPD_GEOPOST_SERVICE_STANDARD_LOCKER';

	const SERVICE_CLASSIC_INTERNATIONAL = 'DPD_GEOPOST_SERVICE_CLASSIC_INTERNATIONAL';
	const SERVICE_CLASSIC_PALLET_ONE_ROMANIA = 'DPD_GEOPOST_SERVICE_CLASSIC_PALLET_ONE_ROMANIA';
	const SERVICE_CLASSIC_POLAND = 'DPD_GEOPOST_SERVICE_CLASSIC_POLAND';
	const SERVICE_STANDARD_24 = 'DPD_GEOPOST_SERVICE_STANDARD_24';
	const SERVICE_FASTIUS_EXPRESS = 'DPD_GEOPOST_SERVICE_FASTIUS_EXPRESS';
	const SERVICE_FASTIUS_EXPRESS_2H = 'DPD_GEOPOST_SERVICE_FASTIUS_EXPRESS_2H';
	const SERVICE_PALLET_ONE_ROMANIA = 'DPD_GEOPOST_SERVICE_PALLET_ONE_ROMANIA';

    const SERVICE_TIRES = 'DPD_TIRES';

	const PACKING_METHOD 							= 'DPD_GEOPOST_PACKING_METHOD';
	const COUNTRY 									= 'DPD_GEOPOST_COUNTRY';
	const PRODUCTION_URL 							= 'DPD_GEOPOST_PRODUCTION_URL';
	const TEST_URL 									= 'DPD_GEOPOST_TEST_URL';
	const USERNAME 									= 'DPD_GEOPOST_USERNAME';
	const PASSWORD 									= 'DPD_GEOPOST_PASSWORD';
	const TIMEOUT 									= 'DPD_GEOPOST_TIMEOUT';
	const SENDER_ID 								= 'DPD_GEOPOST_SENDER_ID';
	const PAYER_ID 									= 'DPD_GEOPOST_PAYER_ID';
	const SEND_INSURANCE_VALUE 									= 'DPD_GEOPOST_SEND_INSURANCE_VALUE';
	const WEIGHT_CONVERSATION_RATE					= 'DPD_GEOPOST_WEIGHT_RATE';
    const WEIGHT_DEFAULT_VALUE                      = 'DPD_GEOPOST_WEIGHT_DEFAULT_VALUE';
	const PRICE_CALCULATION							= 'DPD_GEOPOST_PRICE_CALCULATION';
	const PRICE_CALCULATION_INCLUDE_SHIPPING = 'DPD_GEOPOST_PRICE_CALCULATION_INCLUDE_SHIPPING';

	const CARRIER_CLASSIC_ID = 'DPD_GEOPOST_CARRIER_CLASSIC_ID';

    const CARRIER_CARGO_REGIONAL_ID = 'DPD_GEOPOST_SERVICE_CARGO_REGIONAL_ID';
    const CARRIER_CLASSIC_INTERNATIONAL_CR_ID = 'DPD_GEOPOST_SERVICE_CLASSIC_INTERNATIONAL_CR_ID';
    const CARRIER_CLASIC_POLONIA_CR_ID = 'DPD_GEOPOST_SERVICE_CLASIC_POLONIA_CR_ID';
    const CARRIER_CARGO_NATIONAL_ID = 'DPD_GEOPOST_SERVICE_CARGO_NATIONAL_ID';
    const CARRIER_INTERNATIONAL_EXPRESS_ID = 'DPD_GEOPOST_SERVICE_INTERNATIONAL_EXPRESS_ID';

	const CARRIER_CLASSIC_1_PARCEL_ID = 'DPD_GEOPOST_CARRIER_CLASSIC_1_PARCEL_ID';
	const CARRIER_LOCCO_ID = 'DPD_GEOPOST_CARRIER_LOCCO_ID';
	const CARRIER_LOCCO_1_PARCEL_ID = 'DPD_GEOPOST_CARRIER_LOCCO_1_PARCEL_ID';
	const CARRIER_CLASSIC_BALKAN_ID = 'DPD_GEOPOST_CARRIER_CLASSIC_BALKAN_ID';

	const CARRIER_CLASSIC_INTERNATIONAL_ID = 'DPD_GEOPOST_CARRIER_CLASSIC_INTERNATIONAL_ID';
	const CARRIER_CLASSIC_PALLET_ONE_ROMANIA_ID = 'DPD_GEOPOST_CARRIER_CLASSIC_PALLET_ONE_ROMANIA_ID';
	const CARRIER_CLASSIC_POLAND_ID = 'DPD_GEOPOST_CARRIER_CLASSIC_POLAND_ID';
	const CARRIER_STANDARD_24_ID = 'DPD_GEOPOST_CARRIER_STANDARD_24_ID';
	const CARRIER_FASTIUS_EXPRESS_ID = 'DPD_GEOPOST_CARRIER_FASTIUS_EXPRESS_ID';
	const CARRIER_FASTIUS_EXPRESS_2H_ID = 'DPD_GEOPOST_CARRIER_FASTIUS_EXPRESS_2H_ID';
	const CARRIER_PALLET_ONE_ROMANIA_ID = 'DPD_GEOPOST_CARRIER_PALLET_ONE_ROMANIA_ID';

	const CARRIER_CLASSIC_COD_ID					= 'DPD_GEOPOST_COD_CLASSIC_ID';
	const CARRIER_LOCCO_COD_ID					    = 'DPD_GEOPOST_COD_LOCCO_ID';
	const CARRIER_INTERNATIONAL_COD_ID			    = 'DPD_GEOPOST_COD_INTERNATIONAL_ID';
	const CARRIER_REGIONAL_EXPRESS_COD_ID				    = 'DPD_GEOPOST_COD_REGIONAL_EXPRESS_ID';
	const CARRIER_HUNGARY_COD_ID				    = 'DPD_GEOPOST_COD_HUNGARY_ID';

    const CARRIER_DPD_LOCKER_ID				    = 'DPD_GEOPOST_COD_LOCKER_ID';

    const CARRIER_TIERS_ID = 'DPD_GEOPOST_CARRIER_TIRES_ID';
    const IS_COD_CARRIER_CLASSIC					= 'DPD_GEOPOST_IS_COD_CLASSIC';
	const IS_COD_CARRIER_LOCCO					    = 'DPD_GEOPOST_IS_COD_LOCCO';
	const IS_COD_CARRIER_INTERNATIONAL			    = 'DPD_GEOPOST_IS_COD_INTERNATIONAL';
	const IS_COD_CARRIER_REGIONAL_EXPRESS   		= 'DPD_GEOPOST_IS_COD_REGIONAL_EXPRESS';
	const IS_COD_CARRIER_HUNGARY   			        = 'DPD_GEOPOST_IS_COD_HUNGARY';



	const OTHER 									= 'other';
	const ONE_PRODUCT								= 'one_product';
	const ALL_PRODUCTS								= 'all_products';

	const WEB_SERVICES								= 'webservices';
	const PRESTASHOP								= 'prestashop';
	const CSV										= 'csv';

	const FILE_NAME 								= 'Configuration';
	const MEASUREMENT_ROUND_VALUE					= 6;
	const COD_MODULE								= 'DPD_GEOPOST_COD_MODULE';

	const COD_PERCENTAGE_CALCULATION				= 'DPD_GEOPOST_COD_CALCULATION';
	const COD_PERCENTAGE_CALCULATION_CART			= 'DPD_GEOPOST_CALCULATION_CART';
	const COD_PERCENTAGE_CALCULATION_CART_SHIPPING	= 'DPD_GEOPOST_CALCULATION_SHIPPING';

	const SENDER_DROPOFF_OFFICE = 'SENDER_DROPOFF_OFFICE';
	const SENDER_NAME = 'SENDER_NAME';
	const SENDER_ADDITIONAL_NAME = 'SENDER_ADDITIONAL_NAME';
	const SENDER_STREET = 'SENDER_STREET';
	const SENDER_CITY = 'SENDER_CITY';
	const SENDER_POSTCODE = 'SENDER_POSTCODE';
	const SENDER_COUNTRY = 'SENDER_COUNTRY';
	const SENDER_TELEPHONE = 'SENDER_TELEPHONE';
	const SENDER_EMAIL_ADDRESS = 'SENDER_EMAIL_ADDRESS';

	const COURIER_SERVICE_PAYER = 'COURIER_SERVICE_PAYER';
	const COURIER_SERVICE_PAYER_SENDER = 'SENDER';
	const COURIER_SERVICE_PAYER_RECIPIENT = 'RECIPIENT';
	const COURIER_SERVICE_PAYER_THIRD_PARTY = 'THIRD_PARTY';
	const COURIER_SERVICE_PAYER_THIRD_PARTY_ID = 'THIRD_PARTY_ID';

    const DPD_SHOW_NORMALIZATION_FORM = 'DPD_SHOW_NORMALIZATION_FORM';

    const DPD_PRINT_FORMAT = 'DPD_GEOPOST_PRINT_FORMAT';
    const DPD_PRINT_FORMAT_PDF = 'pdf';
    const DPD_PRINT_FORMAT_ZPL = 'zpl';
    const DPD_PRINT_FORMAT_HTML = 'html';

    const DPD_PAPER_SIZE = 'DPD_GEOPOST_PAPER_SIZE';
    const DPD_PAPER_SIZE_A6 = 'A6';
    const DPD_PAPER_SIZE_A4= 'A4';
    const DPD_PAPER_SIZE_A4x4Ag = 'A4_4xA6';

    const DPD_PAYMENT_OPTIONS = 'DPD_GEOPOST_PAYMENT_OPTIONS';
    const DPD_PAYMENT_OPTIONS_OPEN = 'OPEN';
    const DPD_PAYMENT_OPTIONS_TEST = 'TEST';

    const DPD_RETURN_PAY = 'DPD_GEOPOST_RETURN_PAY';

    const DPD_RETURN_PAY_SENDER = 'SENDER';
    const DPD_RETURN_PAY_RECIPIENT= 'RECIPIENT';

	public $production_mode					= 0;
	public $debug_mode						= 0;
	public $debug_file						= 'rest_dpd.log';
	public $address_validation				= 0;

	public $active_services_international	= 0;
	public $active_services_regional_express		= 0;
	public $active_services_hungary		    = 0;


	public $active_services_international_predict	= 0;
	public $active_services_regional_express_predict		= 0;

	public $active_services_classic = 0;

	public $active_service_cargo_regional = 0;
	public $active_service_classic_international_cr = 0;
	public $active_service_classic_polonia_cr = 0;
	public $active_service_cargo_national = 0;
	public $active_service_international_express = 0;


	public $active_services_classic_1_parcel = 0;
	public $active_services_locco = 0;
	public $active_services_locco_1_parcel = 0;
	public $active_services_classic_balkan = 0;

	public $active_services_classic_international = 0;
	public $active_services_classic_pallet_one_romania = 0;
	public $active_services_classic_poland = 0;
	public $active_services_standard_24 = 0;
	public $active_services_fastius_express = 0;
	public $active_services_fastius_express_2h = 0;
	public $active_services_pallet_one_romania = 0;

    public $active_services_locker = 0;

    public $active_services_tires = 0;

	public $active_services_classic_predict = 0;
	public $active_services_classic_1_parcel_predict = 0;
	public $active_services_locco_predict = 0;
	public $active_services_locco_1_parcel_predict = 0;
	public $active_services_classic_balkan_predict = 0;
	public $active_services_classic_international_predict = 0;
	public $active_services_classic_pallet_one_romania_predict = 0;
	public $active_services_classic_poland_predict = 0;
	public $active_services_standard_24_predict = 0;
	public $active_services_fastius_express_predict = 0;
	public $active_services_fastius_express_2h_predict = 0;
	public $active_services_pallet_one_romania_predict= 0;


	protected $predict_values = array();

	public $active_services_same_day 		= 0;
	public $packaging_method				= self::ALL_PRODUCTS;
	public $dpd_country_select				= '';
	public $ws_production_url				= self::API_URL;
	public $ws_test_url						= '';
	public $ws_username						= '';
	public $ws_password						= '';
	public $ws_timeout						= 10;
	public $sender_id						= '';
	public $payer_id						= '';
	public $send_insurance_value						= 0;
	public $weight_conversation_rate		= 1;
    public $weight_default_value            = 1;
	public $price_calculation_method		= self::PRESTASHOP;

	public $is_cod_carrier_classic			= 0;
	public $is_cod_carrier_10				= 0;
	public $is_cod_carrier_12				= 0;
	public $is_cod_carrier_locco				= 0;
	public $is_cod_carrier_international	= 0;
	public $is_cod_carrier_regional_express			= 0;
	public $is_cod_carrier_hungary			= 0;
	public $is_cod_carrier_same_day			= 0;
	public $sender_dropoff_office_id = 0;

	public $cod_percentage_calculation		= self::COD_PERCENTAGE_CALCULATION_CART;
	public $courier_service_payer 			= self::COURIER_SERVICE_PAYER_RECIPIENT;
	public $courier_service_payer_third_party_id = self::COURIER_SERVICE_PAYER_THIRD_PARTY_ID;

	public $countries 						= array();

	public $module_instance;

    public $print_format;

    public $paper_size;

    public $payment_options;

    public $return_pay;

	public $services = array();

	public function __construct()
	{
		$this->module_instance = Module::getInstanceByName('dpdgeopost');
		$this->services = self::getServicesList();
		$this->getSettings();

		foreach($this->services as $service => &$details) {
			$details['id_service'] = (int) Configuration::get($service . '_ID');
		}
		$this->setAvailableCountries();
	}

	public static function getServicesList() {

		// return array(
		// 		self::SERVICE_CLASSIC          => array(
		// 				'label' => 'DPD Classic',
		// 				'data' => 0,
		// 				'method_id' => _DPDGEOPOST_CLASSIC_ID_
		// 		),
		// 		self::SERVICE_INTERNATIONAL    => array(
		// 				'label' => 'DPD International',
		// 				'data' => 0,
		// 				'method_id' => _DPDGEOPOST_INTERNATIONAL_ID_
		// 		),
		// 		self::SERVICE_REGIONAL_EXPRESS => array(
		// 				'label' => 'DPD Regional Express',
		// 				'data' => 0,
		// 				'method_id' => _DPDGEOPOST_REGIONAL_EXPRESS_ID_
		// 		),
		// );

		return array(
			self::SERVICE_CLASSIC  => array(
				'label' => 'DPD CLASSIC National Romania (maxim 10 Parcels) 1111',
				'data' => 0,
				'method_id' => _DPDGEOPOST_CLASSIC_ID_
			),
			self::SERVICE_CLASSIC_1_PARCEL  => array(
				'label' => 'DPD CLASSIC National Romania (1 parcel)',
				'data' => 0,
				'method_id' => _DPDGEOPOST_CLASSIC_1_PARCEL_ID_
			),
			self::SERVICE_LOCCO  => array(
				'label' => 'DPD CLASSIC Locco Romania  (maxim 10 parcels) Example Bucharest - Bucharest shiping local ',
				'data' => 0,
				'method_id' => _DPDGEOPOST_LOCCO_ID_
			),
			self::SERVICE_LOCCO_1_PARCEL  => array(
				'label' => 'DPD CLASSIC Locco Romania  (1 parcel)   Example Bucharest - Bucharest shiping local (sender address - recipient addres the same city/region)',
				'data' => 0,
				'method_id' => _DPDGEOPOST_LOCCO_1_PARCEL_ID_
			),
			self::SERVICE_CLASSIC_BALKAN  => array(
				'label' => 'DPD REGIONAL CEE',
				'data' => 0,
				'method_id' => _DPDGEOPOST_CLASSIC_BALKAN_ID_
			),
			self::SERVICE_CLASSIC_INTERNATIONAL  => array(
				'label' => ' DPD CLASSIC INTERNATIONAL (1 parcel)',
				'data' => 0,
				'method_id' => _DPDGEOPOST_CLASSIC_INTERNATIONAL_ID_
			),
			self::SERVICE_CLASSIC_PALLET_ONE_ROMANIA  => array(
				'label' => 'DPD CLASSIC PALLET ONE Romania',
				'data' => 0,
				'method_id' => _DPDGEOPOST_CLASSIC_PALLET_ONE_ROMANIA_ID_
			),
			self::SERVICE_CLASSIC_POLAND  => array(
				'label' => 'DPD CLASSIC Poland',
				'data' => 0,
				'method_id' => _DPDGEOPOST_CLASSIC_POLAND_ID_
			),
			self::SERVICE_STANDARD_24  => array(
				'label' => 'DPD Standard 24',
				'data' => 0,
				'method_id' => _DPDGEOPOST_STANDARD_24_ID_
			),
			self::SERVICE_FASTIUS_EXPRESS  => array(
				'label' => 'DPD Fastius Express',
				'data' => 0,
				'method_id' => _DPDGEOPOST_FASTIUS_EXPRESS_ID_
			),
			self::SERVICE_FASTIUS_EXPRESS_2H  => array(
				'label' => 'DPD Fastius Express 2h',
				'data' => 0,
				'method_id' => _DPDGEOPOST_FASTIUS_EXPRESS_2H_ID_
			),
			self::SERVICE_PALLET_ONE_ROMANIA  => array(
				'label' => 'DPD PALLET ONE Romania',
				'data' => 0,
				'method_id' => _DPDGEOPOST_PALLET_ONE_ROMANIA_ID_
			),
            self::SERVICE_CARGO_REGIONAL => array(
                'label' => 'DPD Cargo Regional',
                'data' => 0,
                'method_id' => _DPDGEOPOST_CARGO_REGIONAL_ID_
            ),
            self::SERVICE_CLASSIC_INTERNATIONAL_CR => array(
                'label' => 'DPD Clasic International CR (Rutier)',
                'data' => 0,
                'method_id' => _DPDGEOPOST_CLASSIC_INTERNATIONAL_CR_ID_
            ),
            self::SERVICE_CLASIC_POLONIA_CR => array(
                'label' => 'DPD Clasic Polonia CR',
                'data' => 0,
                'method_id' => _DPDGEOPOST_CLASIC_POLONIA_CR_ID_
            ),
            self::SERVICE_CARGO_NATIONAL => array(
                'label' => 'DPD Cargo National',
                'data' => 0,
                'method_id' => _DPDGEOPOST_CARGO_NATIONAL_ID_
            ),
            self::SERVICE_INTERNATIONAL_EXPRESS => array(
                'label' => 'DPD International Express (Aerian)',
                'data' => 0,
                'method_id' => _DPDGEOPOST_INTERNATIONAL_EXPRESS_ID_
            ),
            self::SERVICE_TIRES => array(
                'label' => 'DPD TIRES',
                'data' => 0,
                'method_id' => _DPDGEOPOST_INTERNATIONAL_EXPRESS_ID_
            ),
            self::SERVICE_STANDARD_LOCKER => array(
                'label' => 'DPD Locker',
                'data' => 0,
                'method_id' => _DPDGEOPOST_LOCKER_ID_
            ),
        );

	}

	public static function getCourierServicePayerList() {
		$list = array(
			array( 'id' => self::COURIER_SERVICE_PAYER_SENDER, 'value' => 'Sender'),
			array( 'id' => self::COURIER_SERVICE_PAYER_RECIPIENT, 'value' => 'Recipient'),
			array( 'id' => self::COURIER_SERVICE_PAYER_THIRD_PARTY, 'value' => 'Third Party')
		);

		return $list;
	}

	public static function saveConfiguration()
	{
		$success = true;

		$success &= Configuration::updateValue(self::PRODUCTION_MODE, 						(int)Tools::getValue(self::PRODUCTION_MODE));
		$success &= Configuration::updateValue(self::DEBUG_MODE, 							(int)Tools::getValue(self::DEBUG_MODE));
		$success &= Configuration::updateValue(self::DEBUG_FILE, 							Tools::getValue(self::DEBUG_FILE));
		$success &= Configuration::updateValue(self::ADDRESS_VALIDATION, 					(int)Tools::getValue(self::ADDRESS_VALIDATION));

/*		$success &= Configuration::updateValue(self::SERVICE_CLASSIC, 						(int)Tools::getValue(self::SERVICE_CLASSIC));
		$success &= Configuration::updateValue(self::SERVICE_LOCCO, 						    (int)Tools::getValue(self::SERVICE_LOCCO));
		$success &= Configuration::updateValue(self::SERVICE_INTERNATIONAL,				    (int)Tools::getValue(self::SERVICE_INTERNATIONAL));
		$success &= Configuration::updateValue(self::SERVICE_REGIONAL_EXPRESS,					    (int)Tools::getValue(self::SERVICE_REGIONAL_EXPRESS));
		$success &= Configuration::updateValue(self::SERVICE_HUNGARY,					    (int)Tools::getValue(self::SERVICE_HUNGARY)); */

		$success &= Configuration::updateValue(self::SERVICE_CLASSIC, (int)Tools::getValue(self::SERVICE_CLASSIC) );

        $success &= Configuration::updateValue(self::SERVICE_CARGO_REGIONAL, (int)Tools::getValue(self::SERVICE_CARGO_REGIONAL) );
        $success &= Configuration::updateValue(self::SERVICE_CLASSIC_INTERNATIONAL_CR, (int)Tools::getValue(self::SERVICE_CLASSIC_INTERNATIONAL_CR) );
        $success &= Configuration::updateValue(self::SERVICE_CLASIC_POLONIA_CR, (int)Tools::getValue(self::SERVICE_CLASIC_POLONIA_CR) );
        $success &= Configuration::updateValue(self::SERVICE_CARGO_NATIONAL, (int)Tools::getValue(self::SERVICE_CARGO_NATIONAL) );
        $success &= Configuration::updateValue(self::SERVICE_INTERNATIONAL_EXPRESS, (int)Tools::getValue(self::SERVICE_INTERNATIONAL_EXPRESS) );

		$success &= Configuration::updateValue(self::SERVICE_CLASSIC_1_PARCEL, (int)Tools::getValue(self::SERVICE_CLASSIC_1_PARCEL) );
		$success &= Configuration::updateValue(self::SERVICE_LOCCO, (int)Tools::getValue(self::SERVICE_LOCCO) );
		$success &= Configuration::updateValue(self::SERVICE_LOCCO_1_PARCEL, (int)Tools::getValue(self::SERVICE_LOCCO_1_PARCEL) );
		$success &= Configuration::updateValue(self::SERVICE_CLASSIC_BALKAN, Tools::getValue(self::SERVICE_CLASSIC_BALKAN) );
		$success &= Configuration::updateValue(self::SERVICE_CLASSIC_INTERNATIONAL, (int)Tools::getValue(self::SERVICE_CLASSIC_INTERNATIONAL) );
		$success &= Configuration::updateValue(self::SERVICE_CLASSIC_PALLET_ONE_ROMANIA, (int)Tools::getValue(self::SERVICE_CLASSIC_PALLET_ONE_ROMANIA) );
		$success &= Configuration::updateValue(self::SERVICE_CLASSIC_POLAND, (int)Tools::getValue(self::SERVICE_CLASSIC_POLAND) );
		$success &= Configuration::updateValue(self::SERVICE_STANDARD_24, (int)Tools::getValue(self::SERVICE_STANDARD_24) );
		$success &= Configuration::updateValue(self::SERVICE_FASTIUS_EXPRESS, (int)Tools::getValue(self::SERVICE_FASTIUS_EXPRESS) );
		$success &= Configuration::updateValue(self::SERVICE_FASTIUS_EXPRESS_2H, (int)Tools::getValue(self::SERVICE_FASTIUS_EXPRESS_2H) );
		$success &= Configuration::updateValue(self::SERVICE_PALLET_ONE_ROMANIA, (int)Tools::getValue(self::SERVICE_PALLET_ONE_ROMANIA) );
        //$success &= Configuration::updateValue(self::SERVICE_DPD_TIRES, (int)Tools::getValue(self::SERVICE_DPD_TIRES) );

        $success &= Configuration::updateValue(self::SERVICE_STANDARD_LOCKER, (int)Tools::getValue(self::SERVICE_STANDARD_LOCKER) );


        $success &= Configuration::updateValue(self::PACKING_METHOD, 						Tools::getValue(self::PACKING_METHOD));
		$success &= Configuration::updateValue(self::COUNTRY, 								Tools::getValue(self::COUNTRY));
		$success &= Configuration::updateValue(self::USERNAME, 								Tools::getValue(self::USERNAME));
		$success &= Configuration::updateValue(self::PASSWORD, 								Tools::getValue(self::PASSWORD));
		$success &= Configuration::updateValue(self::TIMEOUT, 								(int)Tools::getValue(self::TIMEOUT));
		$success &= Configuration::updateValue(self::SENDER_ID, 							Tools::getValue(self::SENDER_ID));
		$success &= Configuration::updateValue(self::PAYER_ID, 								Tools::getValue(self::PAYER_ID));
		$success &= Configuration::updateValue(self::SEND_INSURANCE_VALUE, 		Tools::getValue(self::SEND_INSURANCE_VALUE));
        $success &= Configuration::updateValue(DpdGeopostConfiguration::PRICE_CALCULATION_INCLUDE_SHIPPING, 		Tools::getValue(DpdGeopostConfiguration::PRICE_CALCULATION_INCLUDE_SHIPPING));
		$success &= Configuration::updateValue(self::WEIGHT_CONVERSATION_RATE,				(float)Tools::getValue(self::WEIGHT_CONVERSATION_RATE));
        $success &= Configuration::updateValue(self::WEIGHT_DEFAULT_VALUE,                  (float)Tools::getValue(self::WEIGHT_DEFAULT_VALUE));
		$success &= Configuration::updateValue(self::PRICE_CALCULATION,						Tools::getValue(self::PRICE_CALCULATION));

		$success &= Configuration::updateValue(self::COD_PERCENTAGE_CALCULATION,			Tools::getValue(self::COD_PERCENTAGE_CALCULATION));

		$success &= Configuration::updateValue(self::IS_COD_CARRIER_CLASSIC,				(int)Tools::isSubmit(self::IS_COD_CARRIER_CLASSIC));
		$success &= Configuration::updateValue(self::IS_COD_CARRIER_LOCCO,			    	(int)Tools::isSubmit(self::IS_COD_CARRIER_LOCCO));
		$success &= Configuration::updateValue(self::IS_COD_CARRIER_INTERNATIONAL,			(int)Tools::isSubmit(self::IS_COD_CARRIER_INTERNATIONAL));
		$success &= Configuration::updateValue(self::IS_COD_CARRIER_REGIONAL_EXPRESS,				(int)Tools::isSubmit(self::IS_COD_CARRIER_REGIONAL_EXPRESS));
		$success &= Configuration::updateValue(self::IS_COD_CARRIER_HUNGARY,				(int)Tools::isSubmit(self::IS_COD_CARRIER_HUNGARY));
		$success &= Configuration::updateValue(self::SENDER_DROPOFF_OFFICE, Tools::getValue(self::SENDER_DROPOFF_OFFICE));
		$success &= Configuration::updateValue(self::COURIER_SERVICE_PAYER, Tools::getValue(self::COURIER_SERVICE_PAYER));
		$success &= Configuration::updateValue(self::COURIER_SERVICE_PAYER_THIRD_PARTY_ID, Tools::getValue(self::COURIER_SERVICE_PAYER_THIRD_PARTY_ID));
		$success &= Configuration::updateValue(self::DPD_SHOW_NORMALIZATION_FORM, Tools::getValue(self::DPD_SHOW_NORMALIZATION_FORM));


        $success &= Configuration::updateValue(self::PACKING_METHOD, Tools::getValue(self::PACKING_METHOD));

        $success &= Configuration::updateValue(self::DPD_PRINT_FORMAT, 								Tools::getValue(self::DPD_PRINT_FORMAT));
        $success &= Configuration::updateValue(self::DPD_PAPER_SIZE, 								Tools::getValue(self::DPD_PAPER_SIZE));
        $success &= Configuration::updateValue(self::DPD_PAYMENT_OPTIONS, 								Tools::getValue(self::DPD_PAYMENT_OPTIONS));
        $success &= Configuration::updateValue(self::DPD_RETURN_PAY, 								Tools::getValue(self::DPD_RETURN_PAY));

		$success &= self::savePredictionValues();

		if (Tools::getValue(self::COUNTRY) == self::OTHER)
		{
			$success &= Configuration::updateValue(self::PRODUCTION_URL, 					Tools::getValue(self::PRODUCTION_URL));
			$success &= Configuration::updateValue(self::TEST_URL, 							Tools::getValue(self::TEST_URL));
		}

		$payment_module_selected = '';
		foreach (DpdGeopost::getPaymentModules() as $payment_module)
		{
			if (Tools::isSubmit($payment_module['name']))
				$payment_module_selected = pSQL($payment_module['name']);
		}

		$success &= Configuration::updateValue(self::COD_MODULE,				$payment_module_selected);

       return $success;
	}

	private function getSettings()
	{
		$this->production_mode					= $this->getSetting(self::PRODUCTION_MODE, 						$this->production_mode);
		$this->debug_mode						= $this->getSetting(self::DEBUG_MODE, 							$this->debug_mode);
		$this->debug_file						= $this->getSetting(self::DEBUG_FILE, 							$this->debug_file);
		$this->address_validation				= $this->getSetting(self::ADDRESS_VALIDATION, 					$this->address_validation);
		//$this->active_services_classic			= $this->getSetting(self::SERVICE_CLASSIC, 						$this->active_services_classic);


		$this->active_services_classic = $this->getSetting(self::SERVICE_CLASSIC, $this->active_services_classic);
		$this->active_services_classic_1_parcel = $this->getSetting(self::SERVICE_CLASSIC_1_PARCEL, $this->active_services_classic_1_parcel);
		$this->active_services_locco = $this->getSetting(self::SERVICE_LOCCO, $this->active_services_locco);
		$this->active_services_locco_1_parcel = $this->getSetting(self::SERVICE_LOCCO_1_PARCEL, $this->active_services_locco_1_parcel);
		$this->active_services_classic_balkan = $this->getSetting(self::SERVICE_CLASSIC_BALKAN, $this->active_services_classic_balkan);
		$this->active_services_classic_international = $this->getSetting(self::SERVICE_CLASSIC_INTERNATIONAL, $this->active_services_classic_international);
		$this->active_services_classic_pallet_one_romania = $this->getSetting(self::SERVICE_CLASSIC_PALLET_ONE_ROMANIA, $this->active_services_classic_pallet_one_romania);
		$this->active_services_classic_poland = $this->getSetting(self::SERVICE_CLASSIC_POLAND, $this->active_services_classic_poland);
		$this->active_services_standard_24 = $this->getSetting(self::SERVICE_STANDARD_24, $this->active_services_standard_24);
		$this->active_services_fastius_express = $this->getSetting(self::SERVICE_FASTIUS_EXPRESS, $this->active_services_fastius_express);
		$this->active_services_fastius_express_2h = $this->getSetting(self::SERVICE_FASTIUS_EXPRESS_2H, $this->active_services_fastius_express_2h);
		$this->active_services_pallet_one_romania = $this->getSetting(self::SERVICE_PALLET_ONE_ROMANIA, $this->active_services_pallet_one_romania);

        $this->active_service_cargo_regional = $this->getSetting(self::SERVICE_CARGO_REGIONAL, $this->active_service_cargo_regional);
        $this->active_service_classic_international_cr = $this->getSetting(self::SERVICE_CLASSIC_INTERNATIONAL_CR, $this->active_service_classic_international_cr);
        $this->active_service_classic_polonia_cr = $this->getSetting(self::SERVICE_CLASIC_POLONIA_CR, $this->active_service_classic_polonia_cr);
        $this->active_service_cargo_national = $this->getSetting(self::SERVICE_CARGO_NATIONAL, $this->active_service_cargo_national);
        $this->active_service_international_express = $this->getSetting(self::SERVICE_INTERNATIONAL_EXPRESS, $this->active_service_international_express);


		// $this->active_services_locco			    = $this->getSetting(self::SERVICE_LOCCO, 						    $this->active_services_locco);
		// $this->active_services_international    = $this->getSetting(self::SERVICE_INTERNATIONAL,				$this->active_services_international);
		// $this->active_services_regional_express		    = $this->getSetting(self::SERVICE_REGIONAL_EXPRESS, 					$this->active_services_regional_express);
		// $this->active_services_hungary		    = $this->getSetting(self::SERVICE_HUNGARY, 						$this->active_services_hungary);


		$this->packaging_method					= $this->getSetting(self::PACKING_METHOD, 						$this->packaging_method);
		$this->dpd_country_select				= $this->getSetting(self::COUNTRY, 								$this->dpd_country_select);
		$this->ws_production_url				= $this->getSetting(self::PRODUCTION_URL, 						$this->ws_production_url);

        if (empty($this->ws_production_url)) {
            $this->ws_production_ur = self::API_URL;
        }

		$this->ws_test_url						= $this->getSetting(self::TEST_URL, 							$this->ws_test_url);
		$this->ws_username						= $this->getSetting(self::USERNAME, 							$this->ws_username);
		$this->ws_password						= $this->getSetting(self::PASSWORD, 							$this->ws_password);
		$this->ws_timeout						= $this->getSetting(self::TIMEOUT, 								$this->ws_timeout);
		$this->sender_id						= $this->getSetting(self::SENDER_ID, 							$this->sender_id);
		$this->payer_id							= $this->getSetting(self::PAYER_ID, 							$this->payer_id);
		$this->send_insurance_value				= $this->getSetting(self::SEND_INSURANCE_VALUE, $this->send_insurance_value);
		$this->weight_conversation_rate			= $this->getSetting(self::WEIGHT_CONVERSATION_RATE,				$this->weight_conversation_rate);
        $this->weight_default_value             = $this->getSetting(self::WEIGHT_DEFAULT_VALUE, $this->weight_default_value);
		$this->price_calculation_method			= $this->getSetting(self::PRICE_CALCULATION, 					$this->price_calculation_method);

		$this->is_cod_carrier_classic			= $this->getSetting(self::IS_COD_CARRIER_CLASSIC, 				$this->is_cod_carrier_classic);
		$this->is_cod_carrier_locco			    = $this->getSetting(self::IS_COD_CARRIER_LOCCO, 			    	$this->is_cod_carrier_locco);
		$this->is_cod_carrier_international		= $this->getSetting(self::IS_COD_CARRIER_INTERNATIONAL,			$this->is_cod_carrier_international);
		$this->is_cod_carrier_regional_express			= $this->getSetting(self::IS_COD_CARRIER_REGIONAL_EXPRESS, 				$this->is_cod_carrier_regional_express);
		$this->is_cod_carrier_hungary			= $this->getSetting(self::IS_COD_CARRIER_HUNGARY, 				$this->is_cod_carrier_hungary);

		$this->cod_percentage_calculation		= $this->getSetting(self::COD_PERCENTAGE_CALCULATION, 			$this->cod_percentage_calculation);
		$this->sender_dropoff_office_id			= $this->getSetting(self::SENDER_DROPOFF_OFFICE, $this->sender_dropoff_office_id);
		$this->courier_service_payer			= $this->getSetting(self::COURIER_SERVICE_PAYER, $this->courier_service_payer);
		$this->courier_service_payer_third_party_id = $this->getSetting(self::COURIER_SERVICE_PAYER_THIRD_PARTY_ID, $this->courier_service_payer_third_party_id);

        $this->print_format = $this->getSetting(self::DPD_PRINT_FORMAT, $this->print_format);
        $this->paper_size = $this->getSetting(self::DPD_PAPER_SIZE, $this->paper_size);
        $this->payment_options = $this->getSetting(self::DPD_PAYMENT_OPTIONS, $this->payment_options);
        $this->return_pay = $this->getSetting(self::DPD_RETURN_PAY, $this->return_pay);

		$carrier_classic_id = (int)Configuration::get(self::CARRIER_CLASSIC_ID);
		$carrier_classic_1_parcel_id = (int)Configuration::get(self::CARRIER_CLASSIC_1_PARCEL_ID);
		$carrier_locco_id = (int)Configuration::get(self::CARRIER_LOCCO_ID);
		$carrier_locco_1_parcel_id = (int)Configuration::get(self::CARRIER_LOCCO_1_PARCEL_ID);
		$carrier_classic_balkan_id = (int)Configuration::get(self::CARRIER_CLASSIC_BALKAN_ID);
		$carrier_classic_international_id = (int)Configuration::get(self::CARRIER_CLASSIC_INTERNATIONAL_ID);
		$carrier_classic_pallet_one_romania_id = (int)Configuration::get(self::CARRIER_CLASSIC_PALLET_ONE_ROMANIA_ID);
		$carrier_classic_poland_id = (int)Configuration::get(self::CARRIER_CLASSIC_POLAND_ID);
		$carrier_standard_24_id = (int)Configuration::get(self::CARRIER_STANDARD_24_ID);
		$carrier_fastius_express_id = (int)Configuration::get(self::CARRIER_FASTIUS_EXPRESS_ID);
		$carrier_fastius_express_2h_id = (int)Configuration::get(self::CARRIER_FASTIUS_EXPRESS_2H_ID);
		$carrier_pallet_one_romania_id = (int)Configuration::get(self::CARRIER_PALLET_ONE_ROMANIA_ID);
        $carrier_tires_id = (int)Configuration::get(self::CARRIER_TIERS_ID);
        $carrier_standard_locker_id = (int)Configuration::get(self::CARRIER_DPD_LOCKER_ID);

        $carrier_cargo_regional_id = (int)Configuration::get(self::CARRIER_CARGO_REGIONAL_ID);
        $carrier_classic_international_cr_id =(int)Configuration::get(self::CARRIER_CLASSIC_INTERNATIONAL_CR_ID);
        $carrier_classic_polonia_cr_id =  (int)Configuration::get(self::CARRIER_CLASIC_POLONIA_CR_ID);
        $carrier_cargo_national_id = (int)Configuration::get(self::CARRIER_CARGO_NATIONAL_ID);
        $carreir_international_express_id = (int)Configuration::get(self::CARRIER_INTERNATIONAL_EXPRESS_ID);

		if($carrier_classic_id) {
			if(version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_classic_id);
				$carrier = new Carrier((int)$id_carrier);
			} else {
				$carrier = Carrier::getCarrierByReference((int) $carrier_classic_id);
			}

			if(Validate::isLoadedObject($carrier)) {
				$this->active_services_classic = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			} else {
				$this->active_services_classic = 0;
			}
		}

		// aici
        if($carrier_cargo_regional_id) {
            if(version_compare(_PS_VERSION_, '1.5', '<'))
            {
                $id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_cargo_regional_id);
                $carrier = new Carrier((int)$id_carrier);
            } else {
                $carrier = Carrier::getCarrierByReference((int) $carrier_cargo_regional_id);
            }

            if(Validate::isLoadedObject($carrier)) {
                $this->active_service_cargo_regional = ($carrier->active && !$carrier->deleted) ? 1 : 0;
            } else {
                $this->active_service_cargo_regional = 0;
            }
        }

        if($carrier_classic_international_cr_id) {
            if(version_compare(_PS_VERSION_, '1.5', '<'))
            {
                $id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_classic_international_cr_id);
                $carrier = new Carrier((int)$id_carrier);
            } else {
                $carrier = Carrier::getCarrierByReference((int) $carrier_classic_international_cr_id);
            }

            if(Validate::isLoadedObject($carrier)) {
                $this->active_service_classic_international_cr = ($carrier->active && !$carrier->deleted) ? 1 : 0;
            } else {
                $this->active_service_classic_international_cr = 0;
            }
        }

        if($carrier_classic_polonia_cr_id) {
            if(version_compare(_PS_VERSION_, '1.5', '<'))
            {
                $id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_classic_polonia_cr_id);
                $carrier = new Carrier((int)$id_carrier);
            } else {
                $carrier = Carrier::getCarrierByReference((int) $carrier_classic_polonia_cr_id);
            }

            if(Validate::isLoadedObject($carrier)) {
                $this->active_service_classic_polonia_cr = ($carrier->active && !$carrier->deleted) ? 1 : 0;
            } else {
                $this->active_service_classic_polonia_cr = 0;
            }
        }


        if($carrier_cargo_national_id) {
            if(version_compare(_PS_VERSION_, '1.5', '<'))
            {
                $id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_cargo_national_id);
                $carrier = new Carrier((int)$id_carrier);
            } else {
                $carrier = Carrier::getCarrierByReference((int) $carrier_cargo_national_id);
            }

            if(Validate::isLoadedObject($carrier)) {
                $this->active_service_cargo_national = ($carrier->active && !$carrier->deleted) ? 1 : 0;
            } else {
                $this->active_service_cargo_national = 0;
            }
        }

        $carrier_international_express_id = (int)Configuration::get(self::CARRIER_INTERNATIONAL_EXPRESS_ID);
        if($carrier_cargo_national_id) {
            if(version_compare(_PS_VERSION_, '1.5', '<'))
            {
                $id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_international_express_id);
                $carrier = new Carrier((int)$id_carrier);
            } else {
                $carrier = Carrier::getCarrierByReference((int) $carrier_international_express_id);
            }

            if(Validate::isLoadedObject($carrier)) {
                $this->active_service_international_express = ($carrier->active && !$carrier->deleted) ? 1 : 0;
            } else {
                $this->active_service_international_express = 0;
            }
        }


		if($carrier_classic_1_parcel_id) {
			if(version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_classic_1_parcel_id);
				$carrier = new Carrier((int)$id_carrier);
			} else {
				$carrier = Carrier::getCarrierByReference((int) $carrier_classic_1_parcel_id);
			}

			if(Validate::isLoadedObject($carrier)) {
				$this->active_services_classic_1_parcel = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			} else {
				$this->active_services_classic_1_parcel = 0;
			}
		}
		if($carrier_locco_id) {
			if(version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_locco_id);
				$carrier = new Carrier((int)$id_carrier);
			} else {
				$carrier = Carrier::getCarrierByReference((int) $carrier_locco_id);
			}

			if(Validate::isLoadedObject($carrier)) {
				$this->active_services_locco = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			} else {
				$this->active_services_locco = 0;
			}
		}
		if($carrier_locco_1_parcel_id) {
			if(version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_locco_1_parcel_id);
				$carrier = new Carrier((int)$id_carrier);
			} else {
				$carrier = Carrier::getCarrierByReference((int) $carrier_locco_1_parcel_id);
			}

			if(Validate::isLoadedObject($carrier)) {
				$this->active_services_locco_1_parcel = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			} else {
				$this->active_services_locco_1_parcel = 0;
			}
		}
		if($carrier_classic_balkan_id) {
			if(version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_classic_balkan_id);
				$carrier = new Carrier((int)$id_carrier);
			} else {
				$carrier = Carrier::getCarrierByReference((int) $carrier_classic_balkan_id);
			}

			if(Validate::isLoadedObject($carrier)) {
				$this->active_services_classic_balkan = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			} else {
				$this->active_services_classic_balkan = 0;
			}
		}
		if($carrier_classic_international_id) {
			if(version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_classic_international_id);
				$carrier = new Carrier((int)$id_carrier);
			} else {
				$carrier = Carrier::getCarrierByReference((int) $carrier_classic_international_id);
			}

			if(Validate::isLoadedObject($carrier)) {
				$this->active_services_classic_international = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			} else {
				$this->active_services_classic_international = 0;
			}
		}
		if($carrier_classic_pallet_one_romania_id) {
			if(version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_classic_pallet_one_romania_id);
				$carrier = new Carrier((int)$id_carrier);
			} else {
				$carrier = Carrier::getCarrierByReference((int) $carrier_classic_pallet_one_romania_id);
			}

			if(Validate::isLoadedObject($carrier)) {
				$this->active_services_classic_pallet_one_romania = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			} else {
				$this->active_services_classic_pallet_one_romania = 0;
			}
		}
		if($carrier_classic_poland_id) {
			if(version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_classic_poland_id);
				$carrier = new Carrier((int)$id_carrier);
			} else {
				$carrier = Carrier::getCarrierByReference((int) $carrier_classic_poland_id);
			}

			if(Validate::isLoadedObject($carrier)) {
				$this->active_services_classic_poland = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			} else {
				$this->active_services_classic_poland = 0;
			}
		}
		if($carrier_standard_24_id) {
			if(version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_standard_24_id);
				$carrier = new Carrier((int)$id_carrier);
			} else {
				$carrier = Carrier::getCarrierByReference((int) $carrier_standard_24_id);
			}

			if(Validate::isLoadedObject($carrier)) {
				$this->active_services_standard_24 = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			} else {
				$this->active_services_standard_24 = 0;
			}
		}
		if($carrier_fastius_express_id) {
			if(version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_fastius_express_id);
				$carrier = new Carrier((int)$id_carrier);
			} else {
				$carrier = Carrier::getCarrierByReference((int) $carrier_fastius_express_id);
			}

			if(Validate::isLoadedObject($carrier)) {
				$this->active_services_fastius_express = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			} else {
				$this->active_services_fastius_express = 0;
			}
		}
		if($carrier_fastius_express_2h_id) {
			if(version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_fastius_express_2h_id);
				$carrier = new Carrier((int)$id_carrier);
			} else {
				$carrier = Carrier::getCarrierByReference((int) $carrier_fastius_express_2h_id);
			}

			if(Validate::isLoadedObject($carrier)) {
				$this->active_services_fastius_express_2h = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			} else {
				$this->active_services_fastius_express_2h = 0;
			}
		}
		if($carrier_pallet_one_romania_id) {
			if(version_compare(_PS_VERSION_, '1.5', '<'))
			{
				$id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_pallet_one_romania_id);
				$carrier = new Carrier((int)$id_carrier);
			} else {
				$carrier = Carrier::getCarrierByReference((int) $carrier_pallet_one_romania_id);
			}

			if(Validate::isLoadedObject($carrier)) {
				$this->active_services_pallet_one_romania = ($carrier->active && !$carrier->deleted) ? 1 : 0;
			} else {
				$this->active_services_pallet_one_romania = 0;
			}
		}
        if($carrier_tires_id) {
            if(version_compare(_PS_VERSION_, '1.5', '<'))
            {
                $id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_tires_id);
                $carrier = new Carrier((int)$id_carrier);
            } else {
                $carrier = Carrier::getCarrierByReference((int) $carrier_tires_id);
            }

            if(Validate::isLoadedObject($carrier)) {
                $this->active_services_tires = ($carrier->active && !$carrier->deleted) ? 1 : 0;
            } else {
                $this->active_services_tires = 0;
            }
        }
        if($carrier_standard_locker_id) {
            if(version_compare(_PS_VERSION_, '1.5', '<'))
            {
                $id_carrier = (int)DpdGeopostCarrier::getIdCarrierByReference((int)$carrier_standard_locker_id);
                $carrier = new Carrier((int)$id_carrier);
            } else {
                $carrier = Carrier::getCarrierByReference((int) $carrier_standard_locker_id);
            }

            if(Validate::isLoadedObject($carrier)) {
                $this->active_services_locker = ($carrier->active && !$carrier->deleted) ? 1 : 0;
            } else {
                $this->active_services_locker = 0;
            }
        }


		$this->getPredictSettings();
	}


	public function getPredictSetting($service) {
		if(array_key_exists($service, $this->services) && array_key_exists('data', $this->services[$service])) {
			return (int) $this->services[$service]['data'];
		} else {
			return 0;
		}
	}

	public function getPredictSettingByMethodId($method_id) {
		$services_my_method_id = array();
		foreach($this->services as $details) {
			$services_my_method_id[$details['method_id']] = $details;
		}
		return array_key_exists($method_id, $services_my_method_id) ? $services_my_method_id[$method_id]['data'] : false;
	}

	public static function savePredictionValues() {
		$services = self::getServicesList();
		$success = true;
		foreach($services as $service => $details) {
			$predict_field = $service . '_predict';
			$success &= Configuration::updateValue($predict_field, (int)Tools::getValue($predict_field));
		}

		return $success;
	}
	private function getPredictSettings() {
		foreach($this->services as $service => $details) {
		    //disable zip predict
			$this->setPredictSetting($service,  0);
		}
	}

	protected function setPredictSetting($service, $value) {
		if(array_key_exists($service, $this->services) && array_key_exists('data', $this->services[$service])) {
			$this->services[$service]['data'] = $value;
		}
	}
	private function getSetting($name, $default_value)
	{
		return Configuration::get($name) !== false ? Configuration::get($name) : $default_value;
	}

	static public function getSettingStatic($name, $default_value)
	{
		return Configuration::get($name) !== false ? Configuration::get($name) : $default_value;
	}

	private function setAvailableCountries()
	{
		$this->countries = array(
			'EE' => array(
				'title' 		=> $this->module_instance->l('Estonia', self::FILE_NAME),
				'ws_uri_prod' 	=> 'https://integration.dpd.ee:8443/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> 'https://integrationtest.dpd.ee:8183/IT4EMWebServices/eshop/'
			),
			'LV' => array(
				'title' 		=> $this->module_instance->l('Latvia', self::FILE_NAME),
				'ws_uri_prod' 	=> 'https://integration.dpd.lv:8443/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> ''
			),
			'LT' => array(
				'title' 		=> $this->module_instance->l('Lithuania', self::FILE_NAME),
				'ws_uri_prod' 	=> 'https://integration.dpd.lt:8443/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> ''
			),
			'PL' => array(
				'title' 		=> $this->module_instance->l('Poland', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			),
			'CS' => array(
				'title' 		=> $this->module_instance->l('Czech Respublic', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			),
			'SK' => array(
				'title' 		=> $this->module_instance->l('Slovakia', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			),
			'HU' => array(
				'title' 		=> $this->module_instance->l('Hungary', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			),
			'SI' => array(
				'title' 		=> $this->module_instance->l('Slovenia', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			),
			'HR' => array(
				'title' 		=> $this->module_instance->l('Croatia', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			),
			'RO' => array(
				'title' 		=> $this->module_instance->l('Romania', self::FILE_NAME),
				'ws_uri_prod' 	=> 'https://nou.dpdonline.ro/IT4EMWebServices/eshop/',
				'ws_uri_test'	=> 'https://nou.dpdonline.ro/IT4EMWebServices/eshop/'
			),
			'BG' => array(
				'title' 		=> $this->module_instance->l('Bulgaria', self::FILE_NAME),
				'ws_uri_prod' 	=> '',
				'ws_uri_test'	=> ''
			)
		);
	}

	public static function deleteConfiguration()
	{
		Configuration::deleteByName(self::PRODUCTION_MODE);
		Configuration::deleteByName(self::ADDRESS_VALIDATION);
		Configuration::deleteByName(self::SERVICE_CLASSIC);
		Configuration::deleteByName(self::SERVICE_LOCCO);
		Configuration::deleteByName(self::PACKING_METHOD);
		Configuration::deleteByName(self::COUNTRY);
		Configuration::deleteByName(self::USERNAME);
		Configuration::deleteByName(self::PASSWORD);
		Configuration::deleteByName(self::TIMEOUT);
		Configuration::deleteByName(self::SENDER_ID);
		Configuration::deleteByName(self::PAYER_ID);
		Configuration::deleteByName(self::SEND_INSURANCE_VALUE);
		Configuration::deleteByName(self::PRODUCTION_URL);
		Configuration::deleteByName(self::TEST_URL);
		Configuration::deleteByName(self::WEIGHT_CONVERSATION_RATE);
        Configuration::deleteByName(self::WEIGHT_DEFAULT_VALUE);
		Configuration::deleteByName(self::PRICE_CALCULATION);
		Configuration::deleteByName(self::CARRIER_CLASSIC_ID);
		Configuration::deleteByName(self::CARRIER_LOCCO_ID);
		Configuration::deleteByName(self::IS_COD_CARRIER_CLASSIC);
		Configuration::deleteByName(self::IS_COD_CARRIER_LOCCO);
		Configuration::deleteByName(self::IS_COD_CARRIER_INTERNATIONAL);
		Configuration::deleteByName(self::IS_COD_CARRIER_REGIONAL_EXPRESS);
		Configuration::deleteByName(self::IS_COD_CARRIER_HUNGARY);
		Configuration::deleteByName(self::CARRIER_CLASSIC_COD_ID);
		Configuration::deleteByName(self::CARRIER_LOCCO_COD_ID);
		Configuration::deleteByName(self::CARRIER_INTERNATIONAL_COD_ID);
		Configuration::deleteByName(self::CARRIER_REGIONAL_EXPRESS_COD_ID);
		Configuration::deleteByName(self::CARRIER_HUNGARY_COD_ID);
		Configuration::deleteByName(self::COD_MODULE);
        Configuration::deleteByName(self::CARRIER_DPD_LOCKER_ID);
        return true;
	}

	public function checkRequiredFields()
	{
		if (!$this->dpd_country_select ||
			!$this->ws_username ||
			!$this->ws_password ||
			($this->dpd_country_select == self::OTHER && !$this->ws_production_url && !$this->ws_test_url)
		)
			return false;

		return true;
	}


}
