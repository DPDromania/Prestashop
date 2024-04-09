<?php

if (!defined('_PS_VERSION_'))
	exit;

require_once(_PS_MODULE_DIR_ . 'dpdgeopost/config.api.php');
require_once(_DPDGEOPOST_CLASSES_DIR_ . 'controller.php');
require_once(_DPDGEOPOST_MODULE_DIR_ . 'dpdgeopost.rest.php');
require_once(_DPDGEOPOST_CLASSES_DIR_ . 'messages.controller.php');
require_once(_DPDGEOPOST_CLASSES_DIR_ . 'DpdGeoPostPickupPoint.php');

require_once(_DPDGEOPOST_MODELS_DIR_ . 'ObjectModel.php');
require_once(_DPDGEOPOST_MODELS_DIR_ . 'CSV.php');
require_once(_DPDGEOPOST_MODELS_DIR_ . 'Configuration.php');
require_once(_DPDGEOPOST_MODELS_DIR_ . 'Shipment.php');
require_once(_DPDGEOPOST_MODELS_DIR_ . 'Manifest.php');
require_once(_DPDGEOPOST_MODELS_DIR_ . 'Parcel.php');
require_once(_DPDGEOPOST_MODELS_DIR_ . 'Pickup.php');
require_once(_DPDGEOPOST_MODELS_DIR_ . 'Carrier.php');
require_once(_DPDGEOPOST_MODELS_DIR_ . 'PostcodeSearch.php');
require_once(_DPDGEOPOST_MODELS_DIR_ . 'DpdPostcodeAddress.php');
require_once(_DPDGEOPOST_MODELS_DIR_ . 'Pudo.php');
require_once(_DPDGEOPOST_MODELS_DIR_ . 'DpdAddressSearch.php');
require_once(_DPDGEOPOST_MODELS_DIR_ . 'DpdAddressSearch.php');


if (version_compare(_PS_VERSION_, '1.5', '<'))
	require_once(_DPDGEOPOST_MODULE_DIR_ . 'backward_compatibility/backward.php');

class DpdGeopost extends Module
{
	private $_html = '';
	public  $module_url;

	public  $id_carrier; // mandatory field for carrier recognision in front office
	private static $parcels = array(); // used to cache parcel setup for price calculation in front office
	private static $products = array(); // used to cache producrs
	private static $carriers = array(); // DPD carriers prices cache, used in front office

	private static $addresses = array();

	const CURRENT_INDEX = 'index.php?tab=AdminModules&token=';

	public function __construct()
	{
		$this->name = 'dpdgeopost';
		$this->tab = 'shipping_logistics';
		$this->version = '3.0.2';
		$this->author = 'DPD Romania';
        $this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('DPD GeoPost');
		$this->description = $this->l('DPD GeoPost shipping module');


		if (version_compare(_PS_VERSION_, '1.5', '<')) {
			$this->context = new Context;
			$this->smarty = $this->context->smarty;
			$this->context->smarty->assign('ps14', true);
		}

		if (defined('_PS_ADMIN_DIR_')) {
			$this->module_url = self::CURRENT_INDEX . Tools::getValue('token') . '&configure=' . $this->name;
		}

		$this->checkDbStructure();
	}

	public function install()
	{
		if (!function_exists('curl_init')) {
            $this->_errors[] = $this->l('Missing php-curl extension.');
            return false;
        }

		$sql = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDGEOPOST_CSV_DB_ . '` (
				`id_csv` int(11) NOT NULL AUTO_INCREMENT,
				`id_shop` int(11) NOT NULL,
				`date_add` datetime DEFAULT NULL,
				`date_upd` datetime DEFAULT NULL,
				`country` varchar(255) NOT NULL,
				`region` varchar(255) NOT NULL,
				`zip` varchar(255) NOT NULL,
				`weight_from` varchar(255) NOT NULL,
				`weight_to` varchar(255) NOT NULL,
				`shipping_price` varchar(255) NOT NULL,
				`shipping_price_percentage` varchar(255) NOT NULL DEFAULT 0,
				`currency` varchar(255) NOT NULL,
				`method_id` varchar(11) NOT NULL,
				`cod_surcharge` varchar(255) NOT NULL DEFAULT "0",
				`cod_surcharge_percentage` varchar(255) NOT NULL DEFAULT "0",
				`cod_min_surcharge` varchar(255) NOT NULL DEFAULT "0",
				PRIMARY KEY (`id_csv`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

		if (!Db::getInstance()->execute($sql)) {
            $this->_errors[] = $this->l('Error creating ' . _DPDGEOPOST_CSV_DB_ . ' table');
            return false;
        }

		$sql = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDGEOPOST_PARCEL_DB_ . '` (
				`id_parcel` int(10) NOT NULL AUTO_INCREMENT,
				`id_order` int(10) NOT NULL,
				`parcelReferenceNumber` varchar(30) NOT NULL,
				`id_product` int(10) NOT NULL,
				`id_product_attribute` int(10) NOT NULL,
				`date_add` datetime DEFAULT NULL,
				`date_upd` datetime DEFAULT NULL,
				PRIMARY KEY (`id_parcel`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

		if (!Db::getInstance()->execute($sql)) {
            $this->_errors[] = $this->l('Error creating ' . _DPDGEOPOST_PARCEL_DB_ . ' table');
            return false;
        }

		$sql = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDGEOPOST_CARRIER_DB_ . '` (
				`id_dpd_geopost_carrier` int(10) NOT NULL AUTO_INCREMENT,
				`id_carrier` int(10) NOT NULL,
				`id_reference` int(10) NOT NULL,
				`date_add` datetime NOT NULL,
				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_dpd_geopost_carrier`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';

		if (!Db::getInstance()->execute($sql)) {
            $this->_errors[] = $this->l('Error creating ' . _DPDGEOPOST_CARRIER_DB_ . ' table');
            return false;
        }

		$sql = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDGEOPOST_SHIPMENT_DB_ . '` (
				`id_shipment` BIGINT(20) NOT NULL,
				`id_order` int(10) NOT NULL,
				`id_manifest` BIGINT(20) NOT NULL DEFAULT "0",
				`label_printed` int(1) NOT NULL DEFAULT "0",
				`date_pickup` datetime DEFAULT NULL,
				PRIMARY KEY (`id_order`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';

		if (!Db::getInstance()->execute($sql)) {
            $this->_errors[] = $this->l('Error creating ' . _DPDGEOPOST_SHIPMENT_DB_ . ' table');
            return false;
        }

		$sql = '
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . _DPDGEOPOST_REFERENCE_DB_ . '` (
				`id_order` int(10) NOT NULL,
				`reference` varchar(9) NOT NULL,
				PRIMARY KEY (`id_order`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';

		if (!Db::getInstance()->execute($sql)) {
            $this->_errors[] = $this->l('Error creating ' . _DPDGEOPOST_SHIPMENT_DB_ . ' table');
            return false;
        }

		require_once(_DPDGEOPOST_MODULE_DIR_ . DIRECTORY_SEPARATOR . 'upgrade' . DIRECTORY_SEPARATOR . 'Upgrade-1.0.php');
		require_once(_DPDGEOPOST_MODULE_DIR_ . DIRECTORY_SEPARATOR . 'upgrade' . DIRECTORY_SEPARATOR . 'Upgrade-1.1.php');
		require_once(_DPDGEOPOST_MODULE_DIR_ . DIRECTORY_SEPARATOR . 'upgrade' . DIRECTORY_SEPARATOR . 'Upgrade-1.2.php');
		require_once(_DPDGEOPOST_MODULE_DIR_ . DIRECTORY_SEPARATOR . 'upgrade' . DIRECTORY_SEPARATOR . 'Upgrade-1.3.php');
		require_once(_DPDGEOPOST_MODULE_DIR_ . DIRECTORY_SEPARATOR . 'upgrade' . DIRECTORY_SEPARATOR . 'Upgrade-1.4.php');
		require_once(_DPDGEOPOST_MODULE_DIR_ . DIRECTORY_SEPARATOR . 'upgrade' . DIRECTORY_SEPARATOR . 'Upgrade-2.1.php');
		require_once(_DPDGEOPOST_MODULE_DIR_ . DIRECTORY_SEPARATOR . 'upgrade' . DIRECTORY_SEPARATOR . 'Upgrade-2.2.php');
		require_once(_DPDGEOPOST_MODULE_DIR_ . DIRECTORY_SEPARATOR . 'upgrade' . DIRECTORY_SEPARATOR . 'Upgrade-2.3.php');
        require_once(_DPDGEOPOST_MODULE_DIR_ . DIRECTORY_SEPARATOR . 'upgrade' . DIRECTORY_SEPARATOR . 'Upgrade-2.9.php');
        require_once(_DPDGEOPOST_MODULE_DIR_ . DIRECTORY_SEPARATOR . 'upgrade' . DIRECTORY_SEPARATOR . 'Upgrade-3.0.php');

		upgrade_module_1_0(null);
		upgrade_module_1_1(null);
		upgrade_module_1_2(null);
		upgrade_module_1_3(null);
		upgrade_module_1_4(null);
		upgrade_module_2_1(null);
		upgrade_module_2_2(null);
		upgrade_module_2_3(null);
        upgrade_module_2_9(null);
        upgrade_module_3_0();

		$current_date = date('Y-m-d H:i:s');
		$currency = Currency::getDefaultCurrency();

		if (version_compare(_PS_VERSION_, '1.5', '<'))
			$shops = array('1' => 1);
		else
			$shops = Shop::getShops();

		foreach (array_keys($shops) as $id_shop) {
			$sql = "
				INSERT INTO `" . _DB_PREFIX_ . _DPDGEOPOST_CSV_DB_ . "`
					(`id_shop`, `date_add`, `date_upd`, `country`, `region`, `zip`, `weight_from`, `weight_to`, `shipping_price`, `currency`, `method_id`)
				VALUES
					('" . (int)$id_shop . "', '" . pSQL($current_date) . "', '" . pSQL($current_date) . "', '*', '*', '*', '0', '0.5', 0, '" . pSQL($currency->iso_code) . "', '" . (int)_DPDGEOPOST_CLASSIC_ID_ . "'),
					('" . (int)$id_shop . "', '" . pSQL($current_date) . "', '" . pSQL($current_date) . "', '*', '*', '*', '0', '0.5', 0, '" . pSQL($currency->iso_code) . "', '" . (int)_DPDGEOPOST_INTERNATIONAL_ID_ . "'),
					('" . (int)$id_shop . "', '" . pSQL($current_date) . "', '" . pSQL($current_date) . "', '*', '*', '*', '0', '0.5', 0, '" . pSQL($currency->iso_code) . "', '" . (int)_DPDGEOPOST_REGIONAL_EXPRESS_ID_ . "'),
					('" . (int)$id_shop . "', '" . pSQL($current_date) . "', '" . pSQL($current_date) . "', '*', '*', '*', '0', '0.5', 0, '" . pSQL($currency->iso_code) . "', '*')
				";

			if (!Db::getInstance()->execute($sql)) {
                $this->_errors[] = $this->l('Error creating inserting shops in ' . _DPDGEOPOST_CSV_DB_ . ' table');
                return false;
            }
		}

		if (!parent::install()) {
            return false;
        }

		if (version_compare(_PS_VERSION_, '1.5', '<')) {
			if (!$this->registerHook('paymentTop')) {
                $this->_errors[] = $this->l('Could not register paymentTop hook');
                return false;
            }

			if (!$this->registerHook('updateCarrier')) {
                $this->_errors[] = $this->l('Could not register updateCarrier hook');
                return false;
            }
		} else
			if (!$this->registerHook('paymentTop')) {
                $this->_errors[] = $this->l('Could not register paymentTop hook');
                return false;
            }

		$this->registerHook('displayBackOfficeHeader');
		$this->registerHook('header');
		$this->registerHook('displayFooter');
		$this->registerHook('actionFrontControllerSetMedia');
		$this->registerHook('displayHeader');
		$this->registerHook('extraCarrier');
		$this->registerHook('actionCarrierUpdate');
		$this->registerHook('displayCarrierExtraContent');
		$this->registerHook('displayCarrierList');
		$this->registerHook('actionValidateStepComplete');
		$this->registerHook('displayAdditionalCustomerAddressFields');

		$this->installStates();
		if (!(bool)$this->registerHook('displayAdminOrder')) {
            $this->_errors[] = $this->l('Could not register displayAdminOrder hook');
        }

        return true;
	}

	public function uninstall()
	{
		require_once(_DPDGEOPOST_CLASSES_DIR_ . 'service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_classic.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_classic_cod.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_locco.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_locco_cod.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_international.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_international_cod.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_regionalexpress.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_regionalexpress_cod.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_hungary.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_hungary_cod.service.php');
		require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_standard_locker.service.php');

        return
            parent::uninstall() &&
			$this->unregisterHook('extraCarrier') &&
			$this->unregisterHook('actionCarrierUpdate') &&
			$this->unregisterHook('displayCarrierExtraContent') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('header') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayCarrierList') &&
            $this->registerHook('actionValidateStepComplete') &&
            $this->registerHook('displayAdditionalCustomerAddressFields');


            DpdGeopostCarrierClassicService::delete() &&
			DpdGeopostCarrierClassicCODService::delete() &&
			DpdGeopostCarrierLoccoService::delete() &&
			DpdGeopostCarrierLoccoCODService::delete() &&
			DpdGeopostCarrierInternationalService::delete() &&
			DpdGeopostCarrierInternationalCODService::delete() &&
			DpdGeopostCarrierRegionalExpressService::delete() &&
			DpdGeopostCarrierRegionalExpressCODService::delete() &&
			DpdGeopostCarrierHungaryService::delete() &&
			DpdGeopostCarrierHungaryCODService::delete() &&
            DpdGeopostCarrierStandardLockerService::delete() &&
			$this->dropTables() &&
			$this->dropTriggers() &&
			$this->dropColumns() &&
			DpdGeopostConfiguration::deleteConfiguration();
	}

    /**
     * @return bool
     */
	private function dropTables(): bool
	{
		try {
            DB::getInstance()->Execute('
			DROP TABLE IF EXISTS
				`' . _DB_PREFIX_ . _DPDGEOPOST_CSV_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDGEOPOST_PARCEL_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDGEOPOST_CARRIER_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDGEOPOST_SHIPMENT_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDGEOPOST_REFERENCE_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDGEOPOST_REST_DPD_ADDRESS_DB_ . '`,
				`' . _DB_PREFIX_ . _DPDGEOPOST_REST_DPD_POSTCODES_DB_ . '`
		');
            return true;
        } catch (Throwable $ex) {
            $this->_errors[]  = $ex->getMessage();
            return false;
        }
	}


    /**
     * @return bool
     */
	private function dropColumns(): bool
	{
		try {
            $dbPrefix = _DB_PREFIX_;
            DB::getInstance()->Execute("ALTER TABLE `{$dbPrefix}address` DROP COLUMN IF EXISTS dpd_postcode");
            DB::getInstance()->Execute("ALTER TABLE `{$dbPrefix}address` DROP COLUMN IF EXISTS dpd_office");
            DB::getInstance()->Execute("ALTER TABLE `{$dbPrefix}address` DROP COLUMN IF EXISTS dpd_office_type");
            DB::getInstance()->Execute("ALTER TABLE `{$dbPrefix}address` DROP COLUMN IF EXISTS dpd_office_name");
            DB::getInstance()->Execute("ALTER TABLE `{$dbPrefix}address` DROP COLUMN IF EXISTS dpd_block");
            DB::getInstance()->Execute("ALTER TABLE `{$dbPrefix}address` DROP COLUMN IF EXISTS dpd_complex");
            DB::getInstance()->Execute("ALTER TABLE `{$dbPrefix}address` DROP COLUMN IF EXISTS dpd_street");
            DB::getInstance()->Execute("ALTER TABLE `{$dbPrefix}address` DROP COLUMN IF EXISTS dpd_site");
            DB::getInstance()->Execute("ALTER TABLE `{$dbPrefix}address` DROP COLUMN IF EXISTS dpd_state");
            DB::getInstance()->Execute("ALTER TABLE `{$dbPrefix}address` DROP COLUMN IF EXISTS dpd_country");
            DB::getInstance()->Execute("ALTER TABLE `{$dbPrefix}address` DROP COLUMN IF EXISTS dpd_shipment_type");

            return true;
        } catch (Throwable $ex) {
            $this->_errors[]  = $ex->getMessage();
            return false;
        }
	}

    /**
     * @return bool
     */
	private function dropTriggers(): bool
	{
        try {
            DB::getInstance()->Execute('
			DROP TRIGGER IF EXISTS `dpd_trigger_update_address`
		');
            return true;
        } catch (Throwable $ex) {
            $this->_errors[]  = $ex->getMessage();
            return false;
        }
	}

	/**
	 * module configuration page
	 * @return page HTML code
	 */

	private function setGlobalVariablesForAjax()
	{
		require_once(_DPDGEOPOST_CLASSES_DIR_ . 'csv.controller.php');
		$this->context->smarty->assign(array(
			'download_csv_action'	=> DpdGeopostCSVController::SETTINGS_DOWNLOAD_CSV_ACTION,
			'dpd_geopost_ajax_uri' 	=> _DPDGEOPOST_AJAX_URI_,
			'dpd_geopost_token'		=> sha1(_COOKIE_KEY_ . $this->name),
			'dpd_geopost_id_shop' 	=> (int)$this->context->shop->id,
			'dpd_geopost_id_lang' 	=> (int)$this->context->language->id
		));
	}

	public function getContent()
	{

		$this->displayFlashMessagesIfIsset();

		if (version_compare(_PS_VERSION_, '1.5', '<')) {
			$this->addJS(_DPDGEOPOST_JS_URI_ . 'backoffice.js');
			$this->addCSS(_DPDGEOPOST_CSS_URI_ . 'backoffice.css');
			$this->addCSS(_DPDGEOPOST_CSS_URI_ . 'toolbar.css');
		} else {
			$this->context->controller->addJS(_DPDGEOPOST_JS_URI_ . 'backoffice.js');
			$this->context->controller->addCSS(_DPDGEOPOST_CSS_URI_ . 'backoffice.css');
		}

		$this->setGlobalVariablesForAjax();
		$this->context->smarty->assign('dpd_geopost_other', DpdGeopostConfiguration::OTHER);
		$this->_html .= $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_ . 'admin/prepare.tpl');

		switch (Tools::getValue('menu')) {
			case 'configuration':
				require_once(_DPDGEOPOST_CLASSES_DIR_ . 'configuration.controller.php');
				DpdGeopostConfigurationController::init();

				$this->context->smarty->assign('path', array($this->displayName, $this->l('Settings')));
				$this->displayNavigation();
				if (!version_compare(_PS_VERSION_, '1.5', '<'))
					$this->displayShopRestrictionWarning();

				$configuration_controller = new DpdGeopostConfigurationController();
				$this->_html .= $configuration_controller->getSettingsPage();
				break;
			case 'csv':
				require_once(_DPDGEOPOST_CLASSES_DIR_ . 'csv.controller.php');
				DpdGeopostCSVController::init();

				$this->context->smarty->assign('path', array($this->displayName, $this->l('Price rules')));
				$this->displayNavigation();

				if (!version_compare(_PS_VERSION_, '1.5', '<'))
					if (Shop::getContext() != Shop::CONTEXT_SHOP) {
						$this->_html .= $this->displayWarnings(array($this->l('CSV management is disabled when all shops or group of shops are selected')));
						break;
					}
				$csv_controller = new DpdGeopostCSVController();
				$this->_html .= $csv_controller->getCSVPage();
				break;
			case 'help':
				$this->context->smarty->assign('path', array($this->displayName, $this->l('Help')));
				$this->displayNavigation();
				break;
			case 'postcodeUpdate':
				require_once(_DPDGEOPOST_CLASSES_DIR_ . 'postcode.controller.php');

				$this->context->smarty->assign('path', array($this->displayName, $this->l('Postcode update manager')));
				$this->displayNavigation();

				$postcode_controller = new DpdGeopostPostcodeController();
				$this->_html .= $postcode_controller->getPostcodeUpdateForm();
				break;
			case 'postcodeUpdate_upload':
				require_once(_DPDGEOPOST_CLASSES_DIR_ . 'postcode.controller.php');

				$postcode_controller = new DpdGeopostPostcodeController();
				$this->_html .= $postcode_controller->uploadAndImport();

				break;
			case 'postcodeUpdate_import':
				require_once(_DPDGEOPOST_CLASSES_DIR_ . 'postcode.controller.php');

				$postcode_controller = new DpdGeopostPostcodeController();
				$this->_html .= $postcode_controller->import();

				break;
			case 'shipment_list':
			default:
				require_once(_DPDGEOPOST_CLASSES_DIR_ . 'shipmentsList.controller.php');
				if (Tools::isSubmit('printManifest')) {
					$shipment_controller = new DpdGeopostShipmentController();
					$this->_html .= $shipment_controller->getShipmentList();
					$this->_html = '';
					break;
				}
				if (version_compare(_PS_VERSION_, '1.5', '<')) {
					includeDatepicker(null);
					$this->addJS(_DPDGEOPOST_JS_URI_ . 'jquery.bpopup.min.js');
				} else {
					$this->context->controller->addJqueryUI(array(
						'ui.slider', // for datetimepicker
						'ui.datepicker' // for datetimepicker
					));

					$this->context->controller->addJS(array(
						_DPDGEOPOST_JS_URI_ . 'jquery.bpopup.min.js',
						_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js' // for datetimepicker
					));

					$this->addCSS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css'); // for datetimepicker
				}

				$this->context->smarty->assign('path', array($this->displayName, $this->l('Shipments')));
				$this->displayNavigation();

				if (
					!version_compare(_PS_VERSION_, '1.5', '<') &&
					Configuration::getGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE') &&
					count(Shop::getShops(0)) > 1 &&
					Shop::getContext() != Shop::CONTEXT_SHOP
				) {
					$this->_html .= $this->displayWarnings(array($this->l('Shipments functionality is disabled when all shops or group of shops are chosen')));
					break;
				}
				$shipment_controller = new DpdGeopostShipmentController();
				$this->_html .= $shipment_controller->getShipmentList();
				break;
		}

		return $this->_html;
	}

	private function displayShopRestrictionWarning()
	{
		if (Configuration::getGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE') && count(Shop::getShops(0)) > 1 && Shop::getContext() == Shop::CONTEXT_GROUP)
			$this->_html .= $this->displayWarnings(array($this->l('You have chosen a group of shops, all the changes will be set for all shops in this group')));
		if (Configuration::getGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE') && count(Shop::getShops(0)) > 1 && Shop::getContext() == Shop::CONTEXT_ALL)
			$this->_html .= $this->displayWarnings(array($this->l('You have chosen all shops, all the changes will be set for all shops')));
	}

	public function outputHTML($html)
	{
		$this->_html .= $html;
	}

	public function addCSS($css_uri)
	{
		$this->context->controller->addCSS($css_uri);
	}

	public function addJS($js_uri)
	{
		$this->context->controller->addJS($js_uri);
	}

	private function displayNavigation()
	{
		$this->context->smarty->assign('module_link', $this->module_url);
		$this->_html .= $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_ . 'admin/navigation.tpl');
	}

	/* adds success message into session */
	public static function addFlashMessage($msg)
	{
		$messages_controller = new DpdGeopostMessagesController();
		$messages_controller->setSuccessMessage($msg);
	}

	public static function addFlashError($msg)
	{
		$messages_controller = new DpdGeopostMessagesController();

		if (is_array($msg)) {
			foreach ($msg as $message)
				$messages_controller->setErrorMessage($message);
		} else
			$messages_controller->setErrorMessage($msg);
	}

	/* displays success message only untill page reload */
	private function displayFlashMessagesIfIsset()
	{
		$messages_controller = new DpdGeopostMessagesController();

		if ($success_message = $messages_controller->getSuccessMessage())
			$this->_html .= $this->displayConfirmation($success_message);

		if ($error_message = $messages_controller->getErrorMessage())
			$this->_html .= $this->displayErrors($error_message);
	}

	public function displayErrors($errors)
	{
		$this->context->smarty->assign('errors', $errors);
		return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_ . 'admin/errors.tpl');
	}

	public function displayWarnings($warnings)
	{
		$this->context->smarty->assign('warnings', $warnings);
		return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_ . 'admin/warnings.tpl');
	}

	public static function getInputValue($name, $default_value = null)
	{

		return (Tools::isSubmit($name)) ? Tools::getValue($name) : $default_value;
	}

	public static function getMethodIdByCarrierId($id_carrier)
	{
		if (!$id_reference = self::getReferenceIdByCarrierId($id_carrier)) {
            return false;
        }
        switch ($id_reference) {
			case Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_ID):
				return _DPDGEOPOST_CLASSIC_ID_;

            case Configuration::get(DpdGeopostConfiguration::CARRIER_CARGO_REGIONAL_ID):
                return _DPDGEOPOST_CARGO_REGIONAL_ID_;

            case Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_INTERNATIONAL_CR_ID):
                return _DPDGEOPOST_CLASSIC_INTERNATIONAL_CR_ID_;

            case Configuration::get(DpdGeopostConfiguration::CARRIER_CLASIC_POLONIA_CR_ID):
                return _DPDGEOPOST_CLASIC_POLONIA_CR_ID_;

            case Configuration::get(DpdGeopostConfiguration::CARRIER_CARGO_NATIONAL_ID):
                return _DPDGEOPOST_CARGO_NATIONAL_ID_;

            case Configuration::get(DpdGeopostConfiguration::CARRIER_INTERNATIONAL_EXPRESS_ID):
                return _DPDGEOPOST_INTERNATIONAL_EXPRESS_ID_;


			case Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_1_PARCEL_ID):
				return _DPDGEOPOST_CLASSIC_1_PARCEL_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_LOCCO_ID):
				return _DPDGEOPOST_LOCCO_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_LOCCO_1_PARCEL_ID):
				return _DPDGEOPOST_LOCCO_1_PARCEL_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_BALKAN_ID):
				return _DPDGEOPOST_CLASSIC_BALKAN_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_INTERNATIONAL_ID):
				return _DPDGEOPOST_CLASSIC_INTERNATIONAL_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_PALLET_ONE_ROMANIA_ID):
				return _DPDGEOPOST_CLASSIC_PALLET_ONE_ROMANIA_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_POLAND_ID):
				return _DPDGEOPOST_CLASSIC_POLAND_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_STANDARD_24_ID):
				return _DPDGEOPOST_STANDARD_24_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_FASTIUS_EXPRESS_ID):
				return _DPDGEOPOST_FASTIUS_EXPRESS_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_FASTIUS_EXPRESS_2H_ID):
				return _DPDGEOPOST_FASTIUS_EXPRESS_2H_ID_;
			case Configuration::get(DpdGeopostConfiguration::CARRIER_PALLET_ONE_ROMANIA_ID):
				return _DPDGEOPOST_PALLET_ONE_ROMANIA_ID_;
            case Configuration::get(DpdGeopostConfiguration::CARRIER_DPD_LOCKER_ID):
                return _DPDGEOPOST_LOCKER_ID_;
            default:
				return false;
		}
	}

    /**
     * @param $id_carrier
     * @return bool
     */
	public static function isCODCarrier($id_carrier): bool
	{

		if (!$id_reference = self::getReferenceIdByCarrierId($id_carrier))
			return false;

		switch ($id_reference) {
			case Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_COD_ID):
			case Configuration::get(DpdGeopostConfiguration::CARRIER_LOCCO_COD_ID):
			case Configuration::get(DpdGeopostConfiguration::CARRIER_INTERNATIONAL_COD_ID):
			case Configuration::get(DpdGeopostConfiguration::CARRIER_REGIONAL_EXPRESS_COD_ID):
			case Configuration::get(DpdGeopostConfiguration::CARRIER_HUNGARY_COD_ID):
				return true;
			default:
				return false;
		}
	}

	private static function getReferenceIdByCarrierId($id_carrier)
	{
		if (version_compare(_PS_VERSION_, '1.5', '<'))
			return DpdGeopostCarrier::getReferenceByIdCarrier($id_carrier);

		return Db::getInstance()->getValue(
			'
			SELECT `id_reference`
			FROM `' . _DB_PREFIX_ . 'carrier`
			WHERE `id_carrier`=' . (int)$id_carrier
		);
	}

	private function getCarriersForCurrentModule() {
	    $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
	        SELECT `id_carrier` 
	        FROM `' . _DB_PREFIX_ . 'carrier`
            WHERE external_module_name = \'dpdgeopost\'
	    ');

	    $carriers = array();
        foreach ($rows as $row) {
            $carriers[] = $row['id_carrier'];
	    }
        return $carriers;
    }

	private function getModuleLink($tab)
	{
		# the ps15 way
		if (method_exists($this->context->link, 'getAdminLink'))
			return $this->context->link->getAdminLink($tab) . '&configure=' . $this->name;

		# the ps14 way
		return 'index.php?tab=' . $tab . '&configure=' . $this->name . '&token=' . Tools::getAdminToken($tab . (int)(Tab::getIdFromClassName($tab)) . (int)$this->context->cookie->id_employee);
	}

	public static function getPaymentModules()
	{
		if (version_compare(_PS_VERSION_, '1.5', '<'))
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT DISTINCT h.`id_hook`, m.`name`, hm.`position`
				FROM `' . _DB_PREFIX_ . 'module_country` mc
				LEFT JOIN `' . _DB_PREFIX_ . 'module` m ON m.`id_module` = mc.`id_module`
				INNER JOIN `' . _DB_PREFIX_ . 'module_group` mg ON (m.`id_module` = mg.`id_module`)
				LEFT JOIN `' . _DB_PREFIX_ . 'hook_module` hm ON hm.`id_module` = m.`id_module`
				LEFT JOIN `' . _DB_PREFIX_ . 'hook` h ON hm.`id_hook` = h.`id_hook`
				WHERE h.`name` = \'payment\'
				AND m.`active` = 1
				ORDER BY hm.`position`, m.`name` DESC
			');
		return Module::getPaymentModules();
	}

	public function hookDisplayBackOfficeHeader($params)
	{

		if (Tools::getValue('tab') == 'AdminAddresses' || Tools::getValue('tab') == 'adminaddresses') {
			if (version_compare(_PS_VERSION_, '1.5', '<')) { } else { }

			// Include baseDir for v1.6
			$protocol_content =  $_SERVER['REQUEST_SCHEME'] . '://' . Tools::getHttpHost().__PS_BASE_URI__.(!Configuration::get('PS_REWRITING_SETTINGS') ? 'index.php' : '');
			$inlineScriptsbaseDir = '
			<script type="text/javascript">
				if (typeof baseDir === "undefined") { 
					var baseDirNew = "' . $protocol_content . '";
				};
			</script>';
			
			$includeScript = '<script type="text/javascript" src="' . _DPDGEOPOST_JS_URI_ . 'address-autocomplete.js"></script><script type="text/javascript" src="' . _DPDGEOPOST_JS_URI_ . 'jquery-ui.min.js"></script>';
			$includeCss = '<link href="' . _DPDGEOPOST_CSS_URI_ . 'address-autocomplete.css" rel="stylesheet" type="text/css"/>';
			$inlineScripts = '<script type="text/javascript">' .
				'
                                var dpd_token = "' . sha1(_COOKIE_KEY_ . 'dpdgeopost') . '";
                                var dpd_search_postcode_test = "' . $this->l('DPD - search postcode') . '";
                                var dpd_search_postcode_empty_result_alert = "' . $this->l('There were no suggestions to the address given county. Please enter post code manually.') . '";
                                var dpd_address_validation_length = "' . $this->l('Address length should be less then 70 characters.') . '";
                                ' .
				'</script>';
			return $inlineScriptsbaseDir . $inlineScripts . $includeScript . '' . $includeCss;
		}
		return '';
	}

	public function hookDisplayAdminOrder($params)
	{
		$this->displayFlashMessagesIfIsset();
		$order = new Order((int)$params['id_order']);

        $customer = new Customer($order->id_customer);
		$shipment = new DpdGeopostShipment((int)$params['id_order']);


		$parcelIds = array();

		if($shipment->parcels && !empty($shipment->parcels)) {
			foreach($shipment->parcels as $parcel) {
				$parcelIds[] = $parcel['id'];
			}
		}

		$activeVouchers = $shipment->getActiveVouchers($parcelIds);
		$hasVouchers = false;
		if(!empty($activeVouchers)) {
			$hasVouchers = true;
		}

        $deliveryAddress = new Address($order->id_address_delivery);
        if (empty($deliveryAddress->dpd_country) && $deliveryAddress->country == 'Romania') {
            $deliveryAddress->dpd_country =  642;
        }
        $states = State::getStatesByIdCountry($deliveryAddress->id_country);

        if (!empty($deliveryAddress->dpd_office)) {
            $deliveryAddress->dpd_shipment_type = 'pickup';
        }
       
        if(is_array($states)) {
            $states = array_column($states, 'name');
        }

		$products = $shipment->getParcelsSetUp($order->getProductsDetail());

		if ($shipment->parcels) {
            DpdGeopostParcel::addParcelDataToProducts($products, $order->id);
        }

		$id_method = self::getMethodIdByCarrierId($order->id_carrier);

		$order_total_price = version_compare(_PS_VERSION_, '1.5', '<') ? (float)$order->total_paid : (float)$order->total_paid_tax_incl;

		$productsNameString = '';
		foreach ($products as $product) {
			$productsNameString .= '|' . $product['product_name'];
		}

		$extra_params = array();
		$sendInsuranceValue = Configuration::get(DpdGeopostConfiguration::SEND_INSURANCE_VALUE);
		if ($sendInsuranceValue) {
			$extra_params['highInsurance'] = array(
				'total_paid'        => $order_total_price,
				'currency_iso_code' => $this->context->currency->iso_code,
				'content'         => $productsNameString
			);
		}

		$price = $shipment->calculatePriceForOrder((int)$id_method, $order->id_address_delivery, $products, $extra_params);
		$price_no_currency = $shipment->calculatePriceForOrder((int)$id_method, $order->id_address_delivery, $products, $extra_params, false);

		$dpdAddress = new DpdGeopostDpdPostcodeAddress();
		$dpdAddress->loadDpdAddressByAddressId($order->id_address_delivery);

		$street = pSQL($deliveryAddress->address1) . (($deliveryAddress->address2) ? ' ' . pSQL($deliveryAddress->address2) : '');

		$carrier = new Carrier((int)$order->id_carrier, $order->id_lang);

        if($deliveryAddress->dpd_shipment_type == null) {
            $deliveryAddress->dpd_shipment_type = 'delivery';
        }

        $selectedOffice = '';
        $selectedOfficeId = '';
        if($deliveryAddress->dpd_office && $deliveryAddress->dpd_shipment_type == 'pickup') {
            $pudo = new DpdGeopostPudo();
            $officeById = $pudo->getOfficeById($deliveryAddress->dpd_office);

            if($officeById && !empty($officeById['office']) && isset($officeById['office']['id']) ) {
                $selectedOffice = $officeById['office']['nameEn'];
                $selectedOfficeId = $deliveryAddress->dpd_office;
            }
        }


        $dpdSearch = new DpdAddressSearch();


        $foundStreets = array();
        if($deliveryAddress->dpd_street) {
            $streetById = $dpdSearch->getStreetById($deliveryAddress->dpd_street);
            if(!empty($streetById)) $foundStreets = array($streetById);
        } else {
            $maybeStreetName = $this->extractStreetName($deliveryAddress->id);
            $foundStreets = $dpdSearch->getStreetByName($maybeStreetName, $deliveryAddress->dpd_site);
        }

        $streetNr = '';
        $streetBl = '';
        $streetAp = '';

        if (!empty($deliveryAddress->dpd_block)) {
            list($streetNr, $streetBl, $streetAp) = explode(':', $deliveryAddress->dpd_block);
        }

		$path = [0];
        $assets = [];
		if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $path[] = 1;
			$this->addJS(_DPDGEOPOST_JS_URI_ . 'jquery.bpopup.min.js');
            $this->addJS(_DPDGEOPOST_JS_URI_ . 'select2.min.js');
			$this->addJS(_DPDGEOPOST_JS_URI_ . 'adminOrder.js');

            $this->addJS(_DPDGEOPOST_JS_URI_ . 'normalization_form.js');
            $this->addCSS(_DPDGEOPOST_CSS_URI_ . 'normalization_form.css');

			$this->addCSS(_DPDGEOPOST_CSS_URI_ . 'adminOrder.css');
            $this->addCSS(_DPDGEOPOST_CSS_URI_ . 'select2.min.css');

		} else {
            $path[] = 2;
            $path[] = _DPDGEOPOST_JS_URI_;

            $assets[] = '<script src="'. _DPDGEOPOST_JS_URI_ . 'jquery.bpopup.min.js"></script>';
            $assets[] = '<script src="'. _DPDGEOPOST_JS_URI_ . 'select2.min.js"></script>';
            $assets[] = '<script src="'. _DPDGEOPOST_JS_URI_ . 'adminOrder.js"></script>';
            $assets[] = '<script src="'. _DPDGEOPOST_JS_URI_ . 'normalization_form.js"></script>';

            $assets[] = '<link rel="stylesheet" type="text/css" href="'. _DPDGEOPOST_CSS_URI_ . 'adminOrder.css" >';
            $assets[] = '<link rel="stylesheet" type="text/css" href="'. _DPDGEOPOST_CSS_URI_ . 'select2.min.css" >';
            $assets[] = '<link rel="stylesheet" type="text/css" href="'. _DPDGEOPOST_CSS_URI_ . 'normalization_form.css" >';

//			$this->context->controller->addJS(_DPDGEOPOST_JS_URI_ . 'jquery.bpopup.min.js');
//            $this->context->controller->addJS(_DPDGEOPOST_JS_URI_ . 'select2.min.js');
//			$this->context->controller->addJS(_DPDGEOPOST_JS_URI_ . 'adminOrder.js');
//
//			$this->context->controller->addCSS(_DPDGEOPOST_CSS_URI_ . 'adminOrder.css');
//            $this->context->controller->addCSS(_DPDGEOPOST_CSS_URI_ . 'select2.min.css');
//
//
//
//                $this->context->controller->addJS(_DPDGEOPOST_JS_URI_ . 'normalization_form.js');
//                $this->context->controller->addCSS(_DPDGEOPOST_CSS_URI_ . 'normalization_form.css');

		}

        $this->context->smarty->assign(array(
            'streetNr' => $streetNr,
            'streetBl' => $streetBl,
            'streetAp' => $streetAp,
            'foundStreets' => $foundStreets,
            'selectedOffice' => $selectedOffice,
            'selectedOfficeId' => $selectedOfficeId,
            'order' => $order,
            'deliveryAddress' => $deliveryAddress,
            'dpdAddress' => $dpdAddress,
            'order_country' => $deliveryAddress->country,
            'streetLengthErrors' => (strlen($street) > 70),
            'module_link' => $this->getModuleLink('AdminModules'),
            'settings' => new DpdGeopostConfiguration,
            'total_weight' => DpdGeopostShipment::convertWeight($order->getTotalWeight()),
            'shipment' => $shipment,
            'selected_shipping_method_id' => $id_method,
            'ws_shippingPrice' => $price > 0 ? $price : '---',
            'ws_shippingPrice_noCurrency' => $price_no_currency > 0 ? $price_no_currency : '---',
            'products' => $products,
            'customer_addresses' => $customer->getAddresses($this->context->language->id),
            'error_message' => reset(DpdGeopostShipment::$errors),
            'carrier_url' => $carrier->url,
            'hasVouchers' => $hasVouchers,
            'activeVouchers' => $activeVouchers,
            'path' => implode(':', $path),
            'assets' => implode("\n", $assets),
            'states' => $states

        ));

		$this->setGlobalVariablesForAjax();

		return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_ . 'hook/adminOrder.tpl');
	}

	public function hookPaymentTop($params)
	{
		if (!$this->getMethodIdByCarrierId((int)$this->context->cart->id_carrier)) //Check if DPD carrier is chosen
			return;

		if (Configuration::get('PS_ALLOW_MULTISHIPPING'))
			return;

		if (version_compare(_PS_VERSION_, '1.5', '<'))
			return $this->disablePaymentMethods();

		if (!Validate::isLoadedObject($this->context->cart) || !$this->context->cart->id_carrier)
			return;

		$is_cod_carrier = $this->isCODCarrier((int)$this->context->cart->id_carrier);
		$cod_payment_method = Configuration::get(DpdGeopostConfiguration::COD_MODULE);

		$cache_id = 'exceptionsCache';
		$exceptionsCache = (Cache::isStored($cache_id)) ? Cache::retrieve($cache_id) : array(); // existing cache
		$controller = (Configuration::get('PS_ORDER_PROCESS_TYPE') == 0) ? 'order' : 'orderopc';
		$id_hook = Hook::getIdByName('displayPayment'); // ID of hook we are going to manipulate

		if ($paymentModules = self::getPaymentModules()) {
			foreach ($paymentModules as $module) {
				if (
					$module['name'] == $cod_payment_method && !$is_cod_carrier ||
					$module['name'] != $cod_payment_method && $is_cod_carrier
				) {
					$module_instance = Module::getInstanceByName($module['name']);

					if (Validate::isLoadedObject($module_instance)) {
						$key = (int)$id_hook . '-' . (int)$module_instance->id;
						$exceptionsCache[$key][$this->context->shop->id][] = $controller;
					}
				}
			}

			Cache::store($cache_id, $exceptionsCache);
		}
	}

	private function disablePaymentMethods()
	{
		$is_cod_carrier = $this->isCODCarrier((int)$this->context->cart->id_carrier);
		$cod_payment_method = Configuration::get(DpdGeopostConfiguration::COD_MODULE);

		if ($paymentModules = self::getPaymentModules()) {
			foreach ($paymentModules as $module) {
				if (
					$module['name'] == $cod_payment_method && !$is_cod_carrier ||
					$module['name'] != $cod_payment_method && $is_cod_carrier
				) {
					$module_instance = Module::getInstanceByName($module['name']);
					if (Validate::isLoadedObject($module_instance)) {
						$module_instance->active = 0;
						$module_instance->currencies = array();
					}
				}
			}
		}
	}

	public function hookUpdateCarrier($params)
	{
		$id_reference = (int)DpdGeopostCarrier::getReferenceByIdCarrier((int)$params['id_carrier']);
		$id_carrier = (int)$params['carrier']->id;

		$dpdgeopost_carrier = new DpdGeopostCarrier();
		$dpdgeopost_carrier->id_carrier = (int)$id_carrier;
		$dpdgeopost_carrier->id_reference = (int)$id_reference;
		$dpdgeopost_carrier->save();
	}

	public function getOrderShippingCost($cart, $price)
	{
        return $this->getOrderShippingCostExternal($cart);
	}

    /**
     * called when carrier price is required
     * @param $cart
     * @param $shipping_cost
     * @param $products
     * @return false|float|int|mixed|null
     */
	public function getPackageShippingCost($cart, $shipping_cost, $products)
	{
		if (!Configuration::get('PS_ALLOW_MULTISHIPPING')) {
            return $this->getOrderShippingCostExternal($cart);
        }

		return $this->getOrderShippingCostExternal($cart, $products);
	}

    /**
     * @param $cart
     * @param $products
     * @param $calculate_for_cart
     * @return false|float|int|mixed|null
     */
	public function getOrderShippingCostExternal($cart, $products = array(), $calculate_for_cart = true)
	{
        if (!$this->id_carrier)
			return false;



       $cache_key = $this->getCacheKey($cart, $products);

//		if (isset(self::$carriers[$this->id_carrier][$cache_key]))
//			return self::$carriers[$this->id_carrier][$cache_key];

		$id_address_delivery = empty($products) ? (int)$cart->id_address_delivery : (int)$this->getIdAddressDeliveryByProducts($products);
		$id_country = (int)Tools::getValue('id_country');

		if ($id_country)
			$zone = Country::getIdZone($id_country);
		else
			$zone = Address::getZoneById($id_address_delivery);

        if (!$id_method = self::getMethodIdByCarrierId($this->id_carrier)) {
			self::$carriers[$this->id_carrier][$cache_key] = false;
			return false;
		}

        $carrier = new Carrier((int)$this->id_carrier);


		if (!Validate::isLoadedObject($carrier))
			return false;

		$is_cod_method = $this->isCODCarrier($this->id_carrier);
		$carrier_shipping_method = $carrier->getShippingMethod();
		$order_total_price = empty($products) ? $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING) : $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, $products, $this->id_carrier);
		$total_weight = empty($products) ? $cart->getTotalWeight() : $cart->getTotalWeight($products);
		$cart_total = $carrier_shipping_method == Carrier::SHIPPING_METHOD_WEIGHT ? DpdGeopostShipment::convertWeight($total_weight) : $order_total_price;

		$configuration = new DpdGeopostConfiguration();
		$price_rule = DpdGeopostShipment::getPriceRule($cart_total, $id_method, $id_address_delivery, $is_cod_method);
		$additional_shipping_cost = $this->calculateAdditionalShippingCost($cart, $products);
		$additional_shipping_cost = Tools::convertPrice($additional_shipping_cost);

		$handling_charges = $carrier->shipping_handling ? Configuration::get('PS_SHIPPING_HANDLING') : 0;
		$handling_charges = Tools::convertPrice($handling_charges);

        //$configuration->price_calculation_method = DpdGeopostConfiguration::WEB_SERVICES;


        $extraPriceOnCodSurcharge = 0;

        // set in dpdpayment/validation
        if(!empty($_SESSION['cod_selected_price'])) {
            $extraPriceOnCodSurcharge = $_SESSION['cod_selected_price'];
        }


        $payer = DpdGeopostConfiguration::getSettingStatic(DpdGeopostConfiguration::COURIER_SERVICE_PAYER, DpdGeopostConfiguration::COURIER_SERVICE_PAYER_SENDER);

        if( strtolower($payer )== strtolower(DpdGeopostConfiguration::COURIER_SERVICE_PAYER_RECIPIENT) ) {
            $configuration->price_calculation_method = DpdGeopostConfiguration::WEB_SERVICES;
        }

        if ($configuration->price_calculation_method == DpdGeopostConfiguration::PRESTASHOP) {
			if ($carrier_shipping_method == Carrier::SHIPPING_METHOD_WEIGHT)
				$carrier_price = $carrier->getDeliveryPriceByWeight($cart_total, $zone);
			else
				$carrier_price = $carrier->getDeliveryPriceByPrice($cart_total, $zone);

			// $default_currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT', null, null, 0));
			$default_currency = new Currency(intval(Configuration::get('PS_CURRENCY_DEFAULT')));

			$carrier_price = $this->convertPriceByCurrency($carrier_price, $default_currency->iso_code);

			$shipping_price_with_charges = $carrier_price + $additional_shipping_cost + $handling_charges;

			$cod_price = 0;
			if (!empty($price_rule) && $is_cod_method) {
				if ($price_rule['cod_surcharge'] !== '')
					$cod_price = $this->convertPriceByCurrency($price_rule['cod_surcharge'], $price_rule['currency']);
				elseif ($price_rule['cod_surcharge_percentage'] !== '') {
					$percentage_starting_price = $configuration->cod_percentage_calculation == DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART ? $order_total_price : $order_total_price + $shipping_price_with_charges;
					$cod_price = $this->calculateCODSurchargePercentage($percentage_starting_price, $price_rule['cod_surcharge_percentage'], $price_rule['cod_min_surcharge'], $price_rule['currency']);
				}
			}

            $price = $shipping_price_with_charges + $extraPriceOnCodSurcharge;

			//

            return $price;
			//self::$carriers[$this->id_carrier][$cache_key] = $price;
			//return self::$carriers[$this->id_carrier][$cache_key];
		} elseif ($configuration->price_calculation_method == DpdGeopostConfiguration::WEB_SERVICES) {
			$shipment = new DpdGeopostShipment;

			if (!self::$parcels) {
				$cart_products = empty($products) ? $cart->getProducts() : $products;
				self::$products = $cart_products;
				self::$parcels = $shipment->putProductsToParcels($cart_products);
			}

			$extra_params = array();

			if ($is_cod_method) {
				$extra_params['cod'] = array(
					'total_paid'        => $order_total_price,
					'currency_iso_code' => $this->context->currency->iso_code,
					'reference'         => (int)$this->context->cart->id
				);
			}

			$productsNameString = '';
			if (count(self::$products)) {
				foreach (self::$products as $key => $product) {
					$productsNameString .= '|' . $product['name'];
				}
			}
			$sendInsuranceValue = Configuration::get(DpdGeopostConfiguration::SEND_INSURANCE_VALUE);
			if ($sendInsuranceValue) {
				$extra_params['highInsurance'] = array(
					'total_paid'        => $order_total_price,
					'currency_iso_code' => $this->context->currency->iso_code,
					'content'         => $productsNameString
				);
			}


            $result = $shipment->calculate($id_method, $id_address_delivery, self::$parcels, null, $extra_params);



            if ($result === false || !isset($result['price']) || !isset($result['id_currency'])) {
				self::$carriers[$this->id_carrier][$cache_key] = false;
				return false;
			}

			$result_price_in_default_currency = Tools::convertPrice($result['price'], new Currency((int)$result['id_currency']), false);
			$result['price'] = Tools::convertPrice($result_price_in_default_currency);

			$result['price'] += $additional_shipping_cost + $handling_charges;


			if (!empty($price_rule)) {
				if ($price_rule['shipping_price_percentage'] !== '') {
					$surcharge = $result['price'] * $price_rule['shipping_price_percentage'] / 100;
					$result['price'] += $surcharge;
				}

				if ($is_cod_method && $price_rule['cod_surcharge'] !== '')
					$result['price'] += $this->convertPriceByCurrency($price_rule['cod_surcharge'], $price_rule['currency']);
				elseif ($is_cod_method && $price_rule['cod_surcharge_percentage'] !== '') {
					$percentage_starting_price = $configuration->cod_percentage_calculation == DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART ? $order_total_price : $order_total_price + $result['price'];
					$result['price'] += $this->calculateCODSurchargePercentage($percentage_starting_price, $price_rule['cod_surcharge_percentage'], $price_rule['cod_min_surcharge'], $price_rule['currency']);
				}
			}


            return $result['price'] + $extraPriceOnCodSurcharge;
			//return self::$carriers[$this->id_carrier][$cache_key];
		} elseif ($configuration->price_calculation_method == DpdGeopostConfiguration::CSV) {
			if (empty($price_rule))
				return false;

			if ($price_rule['shipping_price'] !== '')
				$carrier_price = $this->convertPriceByCurrency($price_rule['shipping_price'], $price_rule['currency']);
			elseif ($price_rule['shipping_price_percentage'] !== '')
				$carrier_price = $order_total_price * $price_rule['shipping_price_percentage'] / 100;
			else
				return false;

			$shipping_price_with_charges = $carrier_price + $additional_shipping_cost + $handling_charges;

			$cod_price = 0;
			if ($is_cod_method) {
				if ($price_rule['cod_surcharge'] !== '')
					$cod_price = $this->convertPriceByCurrency($price_rule['cod_surcharge'], $price_rule['currency']);
				elseif ($price_rule['cod_surcharge_percentage'] !== '') {
					$percentage_starting_price = $configuration->cod_percentage_calculation == DpdGeopostConfiguration::COD_PERCENTAGE_CALCULATION_CART ? $order_total_price : $order_total_price + $shipping_price_with_charges;
					$cod_price = $this->calculateCODSurchargePercentage($percentage_starting_price, $price_rule['cod_surcharge_percentage'], $price_rule['cod_min_surcharge'], $price_rule['currency']);
				}
			}

			$price = $shipping_price_with_charges + $cod_price + $extraPriceOnCodSurcharge;
            return $price;
//			self::$carriers[$this->id_carrier][$cache_key] = $price;
//			return self::$carriers[$this->id_carrier][$cache_key];
		}

		return false;
	}

	private function getCacheKey($cart, $products)
	{
		if (empty($products))
			$products = $cart->getProducts();

		$cache_key = '';

		foreach ($products as $product)
			for ($i = 0; $i < $product['cart_quantity']; $i++)
				$cache_key .= $product['id_product'] . '_' . $product['id_product_attribute'] . ';';

		return $cache_key;
	}

	private function getIdAddressDeliveryByProducts($products)
	{
		foreach ($products as $product)
			return $product['id_address_delivery'];
	}

	private function calculateAdditionalShippingCost($cart, $products)
	{
		$additional_shipping_price = 0;
		$cart_products = empty($products) ? $cart->getProducts() : $products;

		foreach ($cart_products as $product)
			$additional_shipping_price += (int)$product['cart_quantity'] * (float)$product['additional_shipping_cost'];

		return $additional_shipping_price;
	}

	/**
	 * Convert price from given currency to current context currency
	 *
	 * @param (float) $price - price which will be converted from given currency
	 * @param (string) $iso_code_currency - iso code of given currency
	 *
	 * @return (float) converted price without currency sign
	 */
	private function convertPriceByCurrency($price, $iso_code_currency)
	{
		$currency = new Currency(Currency::getIdByIsoCode($iso_code_currency));

		if (!Validate::isLoadedObject($currency))
			return 0;

		$price_in_default_currency = Tools::convertPrice($price, $currency, false);

		return Tools::convertPrice($price_in_default_currency);
	}

	/**
	 * Calculate percentage value of given price. If minimum value is higher than percentage
	 * value than it is returned minimum value converted into current context currency
	 *
	 * @param (float) $order_total_price - price which will be used to calculate percentage value
	 * @param (float) $cod_surcharge_percentage - percentage factor
	 * @param (float) $cod_min_surcharge - price in given currency which will be used as minimum value
	 * @param (string) $iso_code_currency - iso code of given currency
	 *
	 * @return (float) calculated price without currency sign
	 */
	private function calculateCODSurchargePercentage($order_total_price, $cod_surcharge_percentage, $cod_min_surcharge, $iso_code_currency)
	{
		$surcharge_percentage = $order_total_price * $cod_surcharge_percentage / 100;
		if ($cod_min_surcharge !== '' && ($min_surcharge = $this->convertPriceByCurrency($cod_min_surcharge, $iso_code_currency))  > $surcharge_percentage)
			$surcharge_percentage = $min_surcharge;

		return $surcharge_percentage;
	}


	public function hookHeader($params)
	{
		$html = "";
		$includeScript = array();
		$includeCss = array();
		$inlineScripts = "";

		if (Tools::getValue('controller') === 'address') {

			// $includeScript[] = '<script type="text/javascript" src="' . _PS_JS_DIR_ . 'jquery/plugins/autocomplete/jquery.autocomplete.js"></script>';
			// $includeScript[] = '<script type="text/javascript" src="' . _DPDGEOPOST_JS_URI_ . 'front-address-autocomplete.js"></script>';

			// $includeCss[] = '<link href="' . _PS_JS_DIR_ . 'jquery/plugins/autocomplete/jquery.autocomplete.css" rel="stylesheet" type="text/css"/>';
			// $includeCss[] = '<link href="' . _DPDGEOPOST_CSS_URI_ . 'address-autocomplete.css" rel="stylesheet" type="text/css"/>';

            $includeCss[] = '<link href="' . _DPDGEOPOST_CSS_URI_ . 'select2.min.css" rel="stylesheet" type="text/css"/>';

			$inlineScripts = '<script type="text/javascript">' .
				'
                                var dpd_token = "' . sha1(_COOKIE_KEY_ . 'dpdgeopost') . '";
                                var dpd_search_postcode_test = "' . $this->l('DPD - search postcode') . '";
                                var dpd_search_postcode_empty_result_alert = "' . $this->l('There were no suggestions to the address given county. Please enter post code manually.') . '";
                                var dpd_address_validation_length = "' . $this->l('Address length should be less then 70 characters.') . '";
                                ' .
				'</script>';
		}

		$html = implode("\n", array($inlineScripts, implode("\n", $includeScript), implode("\n", $includeCss)));

		return $html;
	}

    public function hookActionFrontControllerSetMedia($params)
    {

        // On every page
        $this->context->controller->registerJavascript(
            'module-'.$this->name.'-js',
            'modules' . DIRECTORY_SEPARATOR  .$this->name. DIRECTORY_SEPARATOR  .'js' . DIRECTORY_SEPARATOR  . 'front.js',
            [
                'position' => 'footer',
                'inline' => false,
                'priority' => 100,
                'version' => ''
            ]
        );


    }


	public function hookDisplayFooter($params)
	{
		$includeScript = array();
		$includeCss = array();
		$inlineScripts = "";

		if (Tools::getValue('controller') == 'order' || Tools::getValue('controller') == 'address') {
            $inlineScripts .= "<script>console.log('hookDisplayFooter')</script>";
            $inlineScripts .= "<script>window._DPDGEOPOST_AJAX_URI_ = '" . _DPDGEOPOST_AJAX_URI_ . "' </script>";
            $inlineScripts .= "<script>window._DPD_NORMALIZER_ON_CHECKOUT_ = true; ' </script>";
            $inlineScripts .= "<script>window._DPD_TOKEN_ = '" . sha1(_COOKIE_KEY_. $this->name) . "' </script>";

            $includeScript[] = '<script type="text/javascript" src="' . _DPDGEOPOST_JS_URI_ . 'select2.min.js"></script>';
			$includeScript[] = '<script type="text/javascript" src="' . _DPDGEOPOST_JS_URI_ . 'normalization_form.js"></script>';
		}

		$html = implode("\n", array($inlineScripts, implode("\n", $includeScript), implode("\n", $includeCss)));;


		return $html;
	}

    /**
     * Load the required css in header
     * @param $params
     * @return string
     */
	public function hookDisplayHeader($params)
	{

		$includeScript = array();
		$includeCss = array();
		$inlineScripts = "";
        $rows = $this->getCarriersForCurrentModule();

        $inlineScripts .= "<script>window._DPDGEOPOST_AJAX_URI_ = '" . _DPDGEOPOST_AJAX_URI_ . "' </script>";
        $inlineScripts .= "<script>window._DPD_TOKEN_ = '" . sha1(_COOKIE_KEY_. $this->name) . "' </script>";
        $inlineScripts .= "<script>window._ADMIN_AJAX_URL_ = '" . _DPD_ADMIN_AJAX_URL_ . "' </script>";


        if (Configuration::get('DPD_SHOW_NORMALIZATION_FORM') != "no" && Tools::getValue('controller') == 'order' || Tools::getValue('controller') == 'address') {

			$inlineScripts .= "<script>window._DPD_CARRIERS = ".json_encode($rows)." </script>";
            $inlineScripts .= "<script>window._DPD_NORMALIZER_ON_CHECKOUT_ = true;</script>";
            $includeCss[] = '<link href="' . _DPDGEOPOST_CSS_URI_ . 'select2.min.css" rel="stylesheet" type="text/css"/>';
            $includeCss[] = '<link href="' . _DPDGEOPOST_CSS_URI_ . 'normalization_form.css" rel="stylesheet" type="text/css"/>';
            $includeScript[] = '<script defer type="text/javascript" src="' . _DPDGEOPOST_JS_URI_ . 'select2.min.js"></script>';
			$includeScript[] = '<script defer type="text/javascript" src="' . _DPDGEOPOST_JS_URI_ . 'normalization_form.js"></script>';
		}

		$html = implode("\n", array($inlineScripts, implode("\n", $includeScript), implode("\n", $includeCss)));

		return $html;
	}


    /**
     * Load the required data in front header
     * @param $params
     * @return string
     */
    public function hookDisplayFrontHeader($params)
    {

    }

    public function hookActionValidateStepComplete($params)
    {

        if ($params['step_name'] !== 'delivery') {
            return;
        }
        $cart = new Cart($params['cart']->id);
        $id_method = self::getMethodIdByCarrierId($params['cart']->id_carrier);
        $address = new Address($cart->id_address_delivery);
        switch ($id_method) {
            case _DPDGEOPOST_LOCKER_ID_:
                if (empty($address->dpd_office)) {
                    $this->context->controller->errors[] = $this->l('Please select one locker from the map');
                    $params['completed'] = false;
                } else {
                    $shipment = new DpdGeopostShipment;
                    $cart_products =  $cart->getProducts();
                    $parcels = $shipment->putProductsToParcels($cart_products);
                    $result = $shipment->calculate($id_method, $cart->id_address_delivery, $parcels, null, []);
                    if ($result === false) {
                        if (count($shipment::$errors) == 0) {
                            $this->context->controller->errors[] = 'Nu se poate face livre la officiul selectat.';
                        } else {
                            $this->context->controller->errors[] = $shipment::$errors;
                        }

                        $params['completed'] = false;
                    }
                }

                break;
        }


    }

	private function checkDbStructure()
	{
		//check if table `ps_dpdgeopost_shipment` exists; if so, make sure column `id_shipment` has type BIGINT
		$shipmentTableName = _DB_PREFIX_ . _DPDGEOPOST_SHIPMENT_DB_;
		$db = Db::getInstance();
		$queryTableExists = 'SELECT 1 FROM `' . $shipmentTableName . '`';
		$tableExists = false;
		try {
			$tableExists = $db->getValue($queryTableExists);
		} catch (Exception $ex) {
			//do nothing
		}

		if ($tableExists) {
			$queryColumnType = 'SHOW FIELDS FROM `' . $shipmentTableName . '` where Field =\'id_shipment\'';
			$idShipmentField = $db->executeS($queryColumnType);
			$field = $idShipmentField[0];

			if (strtolower($field['Type']) !== 'bigint(20)') {
				$queryAlterTable = 'ALTER TABLE `' . $shipmentTableName . '` CHANGE COLUMN `id_shipment` `id_shipment` BIGINT NOT NULL FIRST;';
				$db->execute($queryAlterTable);
			}


			$queryColumnType = 'SHOW FIELDS FROM `' . $shipmentTableName . '` where Field =\'id_manifest\'';
			$idManifestField = $db->executeS($queryColumnType);
			$field = $idManifestField[0];

			if (strtolower($field['Type']) !== 'bigint(20)') {
				$queryAlterTable = 'ALTER TABLE `' . $shipmentTableName . '` CHANGE COLUMN `id_manifest` `id_manifest` BIGINT NOT NULL AFTER `shipment_reference`;';
				$db->execute($queryAlterTable);
			}
		}
	}

	function hookExtraCarrier($params)
	{
		return;
	}

	public function hookActionCarrierUpdate($params)
	{
		return '';
	}

	public function hookDisplayCarrierList($params)
	{
        // return $this->hookDisplayCarrierList($params);
	}

    public function hookDisplayAdditionalCustomerAddressFields($params)
    {
        $cart = new Cart( $params['cart']->id);
        $address = new Address($cart->id_address_delivery);
        $locker_address = 'dddddddddd';
        if (!empty($address->dpd_office)) {
            $locker_address = 'Livrare la locker';
        }
        $this->smarty->assign('locker_address', $locker_address);
        return $this->display(__FILE__, 'address_extra_fields.tpl');
    }

	public function hookDisplayCarrierExtraContent($params)
	{

        if ($params['carrier']['id_reference'] == Configuration::get(DpdGeopostConfiguration::CARRIER_DPD_LOCKER_ID))
        {
            $cart = new Cart( $params['cart']->id);
            $this->smarty->assign('dpd_id_cart', $params['cart']->id);
            $this->smarty->assign('dpd_id_delivery_address', $cart->id_address_delivery);

            $address = new Address($cart->id_address_delivery);

            $pudo = new DpdGeopostPudo();
            $result = $pudo->listSites($address->city, 642);

            $this->smarty->assign('dpd_office_name', $address->dpd_office_name);
            if (isset($result['sites']) && count($result['sites']) > 0 ) {
                $this->smarty->assign('dpd_site_id', $result['sites'][0]['id']);

            } else {
                $this->smarty->assign('dpd_site_id', 0);
            }

            return $this->display(__FILE__, 'locker_options_map.tpl');
        }


        if (Configuration::get('DPD_SHOW_NORMALIZATION_FORM') == "no") {

            return;
        }


		$pudo = new DpdGeopostPudo();
		$offices = array();

		$address = new Address($params['cart']->id_address_delivery);
		$state = new State($address->id_state);

		$address->dpd_postcode = $address->postcode;
        //$convert_address_str = explode(',', $address->address1);

        $re = '/\D*(\s?)/mi';
        preg_match_all($re, $address->address1, $matches);

        $street_types = array(
            'str.', 'ale.', 'int.', 'fdt.', 'pta.', 'bld.', 'drm.', 'cal.', 'sos.', 'psj.', 'spl.',
            'str ', 'ale ', 'int ', 'fdt ', 'pta ', 'bld ', 'drm ', 'cal ', 'sos ', 'psj ', 'spl '
        );
        $convert_address_str = '';

        if(isset($matches[0]) && isset($matches[0][0])) {
            $convert_address_str = trim($matches[0][0]);
        }

		$convert_address_nr = '';
		$convert_address_bl = '';
		$convert_address_ap = '';


		$addressSearch = new DpdAddressSearch();

		$countryInDb = new Country($address->id_country);
		$countriesInService = array();

		if($countryInDb) {
			if(is_array($countryInDb->name)) {
				$countryNameInDb = false;
				foreach($countryInDb->name as $countryName) {
					$countryNameInDb = $countryName;
					break;
				}
				$countriesInService = $addressSearch->listCountries(array('name'=> $countryNameInDb ));
			} else if(is_scalar($countryInDb->name)) {
				$countriesInService = $addressSearch->listCountries(array('name'=> $countryInDb->name ));
			}

		}

		$countryWsID = false;
		if(!empty($countriesInService)) {
			$countryWsID = $countriesInService[0]['id'];

			$streetTypesWs = $countriesInService[0]['streetTypes'];

			//$street_types = array();
			foreach($streetTypesWs as $st) {
				if(isset($st['name']) && $st['name'] )  {
                    $street_types[] = $st['name'];
                    $street_types[] = str_ireplace('.', ' ', $st['name']);
                }

				if(isset($st['nameEn']) && $st['nameEn'] ) {
				    $street_types[] = $st['nameEn'];
                    $street_types[] = str_ireplace('.', ' ', $st['nameEn']);
                }
			}

		}

		$this->context->smarty->assign('country_id', $countryWsID);

		if(!in_array( $countryWsID, array('642', '100') ))
		{
			return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_ . 'hook/displayCarrierList.tpl');
		}
		$this->context->smarty->assign('dpd_should_normalize', $countryWsID);


		$listCitiesRequest = array('name' => trim($address->city), 'countryId' => $countryWsID);
		if($state) {
			$listCitiesRequest['region'] = trim($state->name);
		}


		$cities = $addressSearch->listCities($listCitiesRequest);

		foreach($street_types as $street_type) {
			if(stripos($convert_address_str, $street_type) === 0) {
				$convert_address_str = str_ireplace($street_type, '', $convert_address_str);

				break;
			}
		}

		$convert_address_str = trim($convert_address_str);

        $city = '';
        $streetIsRequired = true;
//        $address->dpd_street = '';
//        $address->dpd_site = '';
//        $address->dpd_block = '';

		if(empty($cities)) {
			$this->context->smarty->assign('city_error', "City {$address->city} not found. Please select one from the list above");
			$this->context->smarty->assign('street_error', "Street {$convert_address_str} in {$address->city} not found. Please select one from the list above");
		} else {
			$city = $cities[0];
			$city_id = $city['id'];

            $offices = $pudo->listOffices(false, false, $city_id);

            if(stripos($convert_address_str, 'nr.') !== FALSE) {
                $parts = explode('nr.', $convert_address_str);
                $convert_address_str = trim($parts[0]);
            }

			$streets = $addressSearch->listStreets(array('name' => $convert_address_str, 'siteId' => $city_id));

			$address->dpd_site = $city_id;
			$this->context->smarty->assign('city_id', $city_id);

			if(empty($streets)) {
			    /* we didn't find a street by name
                 employ the following strategy
                 - get all streets for site:
			     --- no street found, street becomes optional and whatever it is in Address street name is used in addressNote
			     --- just one street found, street id becomes default, just use it
			     --- more than one street, continue as usual.
			    */

			    $allStreetsInSite = $addressSearch->listStreets(array('siteId' => $city_id));

			    if(empty($allStreetsInSite)) {
			        // no street found, street becomes optional and whatever it is in Address street name is used in addressNote
                    $streetIsRequired  = false;
                }

			    if(count($allStreetsInSite) == 1) {
                    $streetIsRequired  = false;
                    $address->dpd_street = $allStreetsInSite[0]['id'];
                    $this->context->smarty->assign('street_id', $allStreetsInSite[0]['id']);
                }

			    if(count($allStreetsInSite) >= 2) {
                    $this->context->smarty->assign('street_error', "Street {$convert_address_str} in {$address->city} not found. Please select one from the list above");
                    $streetIsRequired = true;

                }

			} else {
                $street = $streets[0];
                $streeIsFound = false;
                foreach ($streets as $streetFromApi) {
                    if($streetFromApi['id'] == $address->dpd_street) {
                        $street = $streetFromApi;
                        $streeIsFound = true;
                        break;
                    }
                }

				if(count($streets) > 1) {
					$nr_streets = count($streets);
					$this->context->smarty->assign('street_error', "We found {$nr_streets} streets named {$convert_address_str} in {$address->city}. Please select the right one from the list above");

				} else {
					$address->dpd_street = $streets[0]['id'];
					$this->context->smarty->assign('street_id', $streets[0]['id']);
				}
                $streetIsRequired = true;
			}
		}



//		$address->address1 = $convert_address_str;

		$str_number = preg_replace("/[^0-9]/", "",$convert_address_nr);
		$bl_number = preg_replace("/[^0-9]/", "",$convert_address_bl);
		$ap_number = preg_replace("/[^0-9]/", "",$convert_address_ap);
		if($convert_address_nr) $address->address1 .= ', nr. ' . $str_number;
		if($convert_address_bl) $address->address1 .= ', bl. ' . $bl_number;
		if($convert_address_ap) $address->address1 .= ', ap. ' . $ap_number;


//		$address->dpd_block = "{$str_number}:{$bl_number}:{$ap_number}";
        $address->dpd_postcode = $city['postCode'];

        $selectedPuDo = false;
        if($address->dpd_office) {
            $selectedPuDo  = $addressSearch->getOfficeById($address->dpd_office);
        }

        $foundStreets =  array();

        if($address->dpd_street) {
            $streetById = $addressSearch->getStreetById($address->dpd_street);
            if(!empty($streetById)) $foundStreets = array($streetById);
        } else {
            $maybeStreetName = $this->extractStreetName($address->id);
            $foundStreets = $addressSearch->getStreetByName($maybeStreetName, $address->dpd_block);
        }

        list($streetNr, $streetBl, $streetAp) = explode(':', $address->dpd_block);
		// $address->update();

        $this->context->smarty->assign('foundStreets', $foundStreets);
        $this->context->smarty->assign('streetNr', $streetNr);
        $this->context->smarty->assign('streetBl', $streetBl);
        $this->context->smarty->assign('streetAp', $streetAp);



        $this->context->smarty->assign('selectedPuDo', $selectedPuDo );
        $this->context->smarty->assign('country_ws_id', $countryWsID);
        $this->context->smarty->assign('country_name', $address->country);
		$this->context->smarty->assign('street_is_required', $streetIsRequired);
		$this->context->smarty->assign('ws_city', $city);
		$this->context->smarty->assign('ws_postcode', $city['postCode']);
		$this->context->smarty->assign('offices', $offices);
		$this->context->smarty->assign('address', $address);
		$this->context->smarty->assign('converted_address_str', $convert_address_str);
		$this->context->smarty->assign('converted_address_nr', preg_replace("/[^0-9]/", "", $convert_address_nr) );
		$this->context->smarty->assign('converted_address_bl', preg_replace("/[^0-9]/", "", $convert_address_bl) );
		$this->context->smarty->assign('converted_address_ap', preg_replace("/[^0-9]/", "", $convert_address_ap) );
		$this->context->smarty->assign('state_name', $state->name);

		return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_ . 'hook/displayCarrierList.tpl');
//        return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_ . 'hook/displayCarrierListTestLoad.tpl');

	}

    public function installStates() {
        $countries_table = _DB_PREFIX_.'countries';
        $states_table = _DB_PREFIX_.'counties';

        $countriesAndStates = array(
            'BG' => array(
                array('name' => 'Blagoevgrad', 'iso'=>'E') ,
                array('name' => 'Burgas', 'iso'=>'AA') ,
                array('name' => 'Dobrich', 'iso'=>'TX') ,
                array('name' => 'Gabrovo', 'iso'=>'EB') ,
                array('name' => 'Haskovo', 'iso'=>'X') ,
                array('name' => 'Kardzhali', 'iso'=>'K') ,
                array('name' => 'Kyustendil', 'iso'=>'KH') ,
                array('name' => 'Lovech', 'iso'=>'OB') ,
                array('name' => 'Montana', 'iso'=>'M'),
                array('name' => 'Pazardzhik', 'iso'=>'PA'),
                array('name' => 'Pernik', 'iso'=>'PK'),
                array('name' => 'Pleven', 'iso'=>'Eh'),
                array('name' => 'Plovdiv', 'iso'=>'PB'),
                array('name' => 'Razgrad', 'iso'=>'PP'),
                array('name' => 'Ruse', 'iso'=>'P'),
                array('name' => 'Shumen', 'iso'=>'H') ,
                array('name' => 'Silistra', 'iso'=>'CC') ,
                array('name' => 'Sliven', 'iso'=>'CH'),
                array('name' => 'Smolyan', 'iso'=>'CM'),
                array('name' => 'Sofia City Province', 'iso'=>'SCP'),
                array('name' => 'Sofia', 'iso'=>'CO'),
                array('name' => 'Stara Zagora', 'iso'=>'CT'),
                array('name' => 'Targovishte', 'iso'=>'T'),
                array('name' => 'Varna', 'iso'=>'B'),
                array('name' => 'Veliko Tarnovo', 'iso'=>'BT'),
                array('name' => 'Vidin', 'iso'=>'BH'),
                array('name' => 'Vratsa', 'iso'=>'BP'),
                array('name' => 'Yambol', 'iso'=>'Y'),
            )
        );



        foreach ($countriesAndStates as $countryISO => $stateData) {

            $countryIdByISO = Country::getByIso($countryISO);
            $states = State::getStatesByIdCountry($countryIdByISO);

            if(count($states) > 0) continue;

            $country = new Country($countryIdByISO);
            foreach ($stateData as $stateElements) {
                $state = new State();
                $state->id_country = $countryIdByISO;
                $state->name = $stateElements['name'];
                $state->iso_code = $stateElements['iso'];
                $state->id_zone = $country->id_zone;
                $state->add();
            }


        }

    }

    public function extractStreetName($deliveryAddress) {
        $address = new Address($deliveryAddress);

        $re = '/\D*(\s?)/mi';
        preg_match_all($re, $address->address1, $matches);

        $street_types = array(
            'str.', 'ale.', 'int.', 'fdt.', 'pta.', 'bld.', 'drm.', 'cal.', 'sos.', 'psj.', 'spl.',
            'str ', 'ale ', 'int ', 'fdt ', 'pta ', 'bld ', 'drm ', 'cal ', 'sos ', 'psj ', 'spl '
        );

        $convert_address_str = '';


        if(isset($matches[0]) && isset($matches[0][0])) {
            $convert_address_str = trim($matches[0][0]);

        }

        $convert_address_nr = '';
        $convert_address_bl = '';
        $convert_address_ap = '';

        $addressSearch = new DpdAddressSearch();


        $countryWsID = $address->dpd_country;

        if($countryWsID) {
            $countryInDb = new Country($address->id_country);
            $countriesInService = array();
            if($countryInDb) {
                if(is_array($countryInDb->name)) {
                    $countryNameInDb = false;
                    foreach($countryInDb->name as $countryName) {
                        $countryNameInDb = $countryName;
                        break;
                    }
                    $countriesInService = $addressSearch->listCountries(array('name'=> $countryNameInDb ));
                } else if(is_scalar($countryInDb->name)) {
                    $countriesInService = $addressSearch->listCountries(array('name'=> $countryInDb->name ));
                }


            }

            if(!empty($countriesInService)) {
                // $countryWsID = $countriesInService[0]['id'];
                $streetTypesWs = $countriesInService[0]['streetTypes'];

                foreach($streetTypesWs as $st) {
                    if(isset($st['name']) && $st['name'] )  {
                        $street_types[] = $st['name'];
                        $street_types[] = str_ireplace('.', ' ', $st['name']);
                    }

                    if(isset($st['nameEn']) && $st['nameEn'] ) {
                        $street_types[] = $st['nameEn'];
                        $street_types[] = str_ireplace('.', ' ', $st['nameEn']);
                    }
                }
            }
        }

        foreach ($street_types as $street_type) {
            if(stripos($convert_address_str, $street_type) === 0) {
                $convert_address_str = str_ireplace($street_type, '', $convert_address_str);
                break;
            }
        }

        return trim($convert_address_str);
    }

}
