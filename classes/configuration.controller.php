<?php

/** **/

if (!defined('_PS_VERSION_'))
    exit;

class DpdGeopostConfigurationController extends DpdGeopostController
{
    public $available_services_ids = array();

    const SETTINGS_SAVE_ACTION = 'saveModuleSettings';
    const FILENAME = 'configuration.controller';

    public function getSettingsPage()
    {
        require_once(_DPDGEOPOST_MODELS_DIR_ . '/Pudo.php');
        require_once(_DPDGEOPOST_MODELS_DIR_ . '/ClientContracts.php');

        $configuration = new DpdGeopostConfiguration();

        $pudoService = new DpdGeopostPudo();
        $dropoffOffices = $pudoService->listOffices();



        $clientContractsService = new ClientContracts();
        $contractOptions = $clientContractsService->listContracts();
        $availableServices = $clientContractsService->availableServices();

        $availableServices['25052'] = [
            'id' => 25052,
            'name' => 'DPD OOH Locker/Office',
            'nameEn' => 'DPD OOH Locker/Office',
            'additionalServices' => [

            ]
        ];

        $nameMap = array(
            2304 => array(
                'name' => DpdGeopostConfiguration::SERVICE_CLASSIC_POLAND,
                'checked' => DpdGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_CLASSIC_POLAND, $configuration->active_services_classic_poland) == 1,
            ),
            2432 => array(
                'name' => DpdGeopostConfiguration::SERVICE_PALLET_ONE_ROMANIA,
                'checked' => DpdGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_PALLET_ONE_ROMANIA, $configuration->active_services_pallet_one_romania) == 1,
            ),
            2113 => array(
                'name' => DpdGeopostConfiguration::SERVICE_LOCCO,
                'checked' => DpdGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_LOCCO, $configuration->active_services_locco) == 1,
            ),
            2212 => array(
                'name' => DpdGeopostConfiguration::SERVICE_CLASSIC_BALKAN,
                'checked' => DpdGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_CLASSIC_BALKAN, $configuration->active_services_classic_balkan) == 1,
            ),
            // @todo cargo regional
            2214 => array(
                'name' => DpdGeopostConfiguration::SERVICE_CARGO_REGIONAL,
                'checked' => DpdGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_CARGO_REGIONAL, $configuration->active_service_cargo_regional) == 1,
            ),
            2505 => array(
                'name' => DpdGeopostConfiguration::SERVICE_STANDARD_24,
                'checked' => DpdGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_STANDARD_24, $configuration->active_services_standard_24) == 1,
            ),
            2412 => array(
                'name' => DpdGeopostConfiguration::SERVICE_CLASSIC_PALLET_ONE_ROMANIA,
                'checked' => DpdGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_CLASSIC_PALLET_ONE_ROMANIA, $configuration->active_services_classic_pallet_one_romania) == 1,
            ),
            2002 => array(
                'name' => DpdGeopostConfiguration::SERVICE_CLASSIC,
                'checked' => DpdGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_CLASSIC, $configuration->active_services_classic) == 1
            ),
            // @todo clasic international cr (rutier)
            2323 => array(
                'name' => DpdGeopostConfiguration::SERVICE_CLASSIC_INTERNATIONAL_CR,
                'checked' => DpdGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_CLASSIC_INTERNATIONAL_CR, $configuration->active_service_classic_international_cr) == 1,
            ),
            // @todo clasic polonia cr
            2324 => array(
                'name' => DpdGeopostConfiguration::SERVICE_CLASIC_POLONIA_CR,
                'checked' => DpdGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_CLASIC_POLONIA_CR, $configuration->active_service_classic_polonia_cr) == 1,
            ),
            // @todo cargo national
            2005 => array(
                'name' => DpdGeopostConfiguration::SERVICE_CARGO_NATIONAL,
                'checked' => DpdGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_CARGO_NATIONAL, $configuration->active_service_cargo_national) == 1,
            ),
            // @todo international express (aerian)
            2302 => array(
                'name' => DpdGeopostConfiguration::SERVICE_INTERNATIONAL_EXPRESS,
                'checked' => DpdGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_INTERNATIONAL_EXPRESS, $configuration->active_service_international_express) == 1,
            ),
            2303 => array(
                'name' => DpdGeopostConfiguration::SERVICE_CLASSIC_INTERNATIONAL,
                'checked' => DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_CLASSIC_INTERNATIONAL, $configuration->active_services_classic_international) == 1,
            ),


            2003 => array(
                'name' => DpdGeopostConfiguration::SERVICE_CLASSIC_1_PARCEL,
                'checked' => DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_CLASSIC_1_PARCEL, $configuration->active_services_classic_1_parcel) == 1,
            ),
            2114 => array(
                'name' => DpdGeopostConfiguration::SERVICE_LOCCO_1_PARCEL,
                'checked' => DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_LOCCO_1_PARCEL, $configuration->active_services_locco_1_parcel) == 1,
            ),
            2111 => array(
                'name' => DpdGeopostConfiguration::SERVICE_FASTIUS_EXPRESS,
                'checked' => DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_FASTIUS_EXPRESS, $configuration->active_services_fastius_express) == 1,
            ),
            2112 => array(
                'name' => DpdGeopostConfiguration::SERVICE_FASTIUS_EXPRESS_2H,
                'checked' => DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_FASTIUS_EXPRESS_2H, $configuration->active_services_fastius_express_2h) == 1,
            ),
            2704 => array(
                'name' => DpdGeopostConfiguration::SERVICE_TIRES,
                'checked' => DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_TIRES, $configuration->active_services_tires) == 1,
            ),
            25052 => array(
                'name' => DpdGeopostConfiguration::SERVICE_STANDARD_LOCKER,
                'checked' => DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_STANDARD_LOCKER, $configuration->active_services_locker) == 1,
            ),
        );


        if (is_countable($availableServices)) {
            foreach ($availableServices as $serviceId => &$availableService) {
                if (!isset($nameMap[$serviceId])) {
                    $availableService['isChecked'] = false;
                    $availableService['htmlName'] = $availableService['name'];
                    continue;
                }
                $availableService['isChecked'] = $nameMap[$serviceId]['checked'];
                $availableService['htmlName'] = $nameMap[$serviceId]['name'];
            }
        }
        //echo '<pre>';print_r(DPDGeopost::getInputValue(DpdGeopostConfiguration::SERVICE_STANDARD_LOCKER, $configuration->active_services_locker));exit;
        $this->context->smarty->assign(array(
            'saveAction' => $this->module_instance->module_url,
            'available_countries' => $configuration->countries,
            'settings' => $configuration,
            'dropoffoptions' => $dropoffOffices,
            'selectedDropOff' => $configuration->sender_dropoff_office_id,
            'contractoptions' => $contractOptions,
            'selectedContract' => $configuration->sender_id,
            'availableServices' => $availableServices
        ));

        return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_ . 'admin/configuration.tpl');
    }

    public static function init()
    {
        $controller = new DpdGeopostConfigurationController;

        if (Tools::isSubmit(self::SETTINGS_SAVE_ACTION)) {
            $controller->validateSettings();

            if (!self::$errors)
                $controller->createDeleteCarriers();

            if (!self::$errors)
                $controller->saveSettings();
            else
                $controller->module_instance->outputHTML($controller->module_instance->displayErrors(self::$errors));
        }

        $configuration = new DpdGeopostConfiguration();

        if (!$configuration->checkRequiredFields())
            $controller->module_instance->outputHTML($controller->module_instance->displayWarnings(array($controller->l('Module is not fully configured yet.'))));
    }

    private function createDeleteCarriers()
    {
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_classic.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_classic_1_parcel.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_locco.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_locco_1_parcel.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_balkan.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_international.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_classic_pallet_one_romania.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_poland.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_standard_24.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_fastius_express.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_fastius_express_2h.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_pallet_one_romania.service.php');

        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_cargo_regional.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_classic_international_cr.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_classic_polonia_cr.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_cargo_national.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_international_express.service.php');
        require_once(_DPDGEOPOST_CLASSES_DIR_ . 'dpd_standard_locker.service.php');
        require_once (_DPDGEOPOST_CLASSES_DIR_ . 'dpd_tires.service.php');

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_CLASSIC)) {
            if (!DpdGeopostCarrierClassicService::install())
                self::$errors[] = $this->l('Could not save DPD Classic service');
        } else {
            if (!DpdGeopostCarrierClassicService::delete())
                self::$errors[] = $this->l('Could not delete DPD Classic service');
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_CARGO_REGIONAL)) {
            if (!DpdGeopostCarrierCargoRegionalService::install())
                self::$errors[] = $this->l('Could not save DPD Cargo Regional');
        } else {
            if (!DpdGeopostCarrierCargoRegionalService::delete())
                self::$errors[] = $this->l('Could not delete DPD Cargo Regional');
        }


        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_CLASSIC_INTERNATIONAL_CR)) {
            if (!DpdGeopostCarrierClassicInternationalCRService::install())
                self::$errors[] = $this->l('Could not save DPD Classic International CR Service');
        } else {
            if (!DpdGeopostCarrierClassicInternationalCRService::delete())
                self::$errors[] = $this->l('Could not delete DPD DPD Classic International CR Service');
        }
        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_CLASIC_POLONIA_CR)) {
            if (!DpdGeopostCarrierClassicPoloniaCRService::install())
                self::$errors[] = $this->l('Could not save DPD Classic Polonia CR service');
        } else {
            if (!DpdGeopostCarrierClassicPoloniaCRService::delete())
                self::$errors[] = $this->l('Could not delete DPD Classic Polonia CR service');
        }
        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_CARGO_NATIONAL)) {
            if (!DpdGeopostCarrierCargoNationalService::install())
                self::$errors[] = $this->l('Could not save DPD Cargo National service');
        } else {
            if (!DpdGeopostCarrierCargoNationalService::delete())
                self::$errors[] = $this->l('Could not delete DPD Cargo National service');
        }
        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_INTERNATIONAL_EXPRESS)) {
            if (!DpdGeopostCarrierInternationalExpressService::install())
                self::$errors[] = $this->l('Could not save DPD International Express Service');
        } else {
            if (!DpdGeopostCarrierInternationalExpressService::delete())
                self::$errors[] = $this->l('Could not delete DPD International Express Service');
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_CLASSIC_1_PARCEL)) {
            if (!DpdGeopostCarrierClassic1ParcelService::install())
                self::$errors[] = $this->l('Could not save DPD Classic 1 Parcel service');
        } else {
            if (!DpdGeopostCarrierClassic1ParcelService::delete())
                self::$errors[] = $this->l('Could not delete DPD Classic 1 Parcel service');
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_LOCCO)) {
            if (!DpdGeopostCarrierLoccoService::install())
                self::$errors[] = $this->l('Could not save DPD Locco service');
        } else {
            if (!DpdGeopostCarrierLoccoService::delete())
                self::$errors[] = $this->l('Could not delete DPD Locco service');
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_LOCCO_1_PARCEL)) {
            if (!DpdGeopostCarrierLocco1ParcelService::install())
                self::$errors[] = $this->l('Could not save DPD Locco 1 Parcel service');
        } else {
            if (!DpdGeopostCarrierLocco1ParcelService::delete())
                self::$errors[] = $this->l('Could not delete DPD Locco 1 Parcel service');
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_CLASSIC_BALKAN)) {
            if (!DpdGeopostCarrierBalkanService::install())
                self::$errors[] = $this->l('Could not save DPD REGIONAL CEE service');
        } else {
            if (!DpdGeopostCarrierBalkanService::delete()) {
                self::$errors[] = $this->l('Could not delete DPD Regional CEE service');
            }
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_CLASSIC_INTERNATIONAL)) {
            if (!DpdGeopostCarrierInternationalService::install())
                self::$errors[] = $this->l('Could not save DPD Classic International');
        } else {
            if (!DpdGeopostCarrierInternationalService::delete()) {
                self::$errors[] = $this->l('Could not delete DPD Classic International');
            }
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_CLASSIC_PALLET_ONE_ROMANIA)) {
            if (!DpdGeopostCarrierClassicPalletOneRomaniaService::install()) {
                self::$errors[] = $this->l('Could not save DPD Classic Pallet One Romania');
            }
        } else {
            if (!DpdGeopostCarrierClassicPalletOneRomaniaService::delete()) {
                self::$errors[] = $this->l('Could not delete DPD Classic Pallet One Romania');
            }
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_CLASSIC_POLAND)) {
            if (!DpdGeopostCarrierPolandService::install()) {
                self::$errors[] = $this->l('Could not save DPD Classic Poland');
            }
        } else {
            if (!DpdGeopostCarrierPolandService::delete()) {
                self::$errors[] = $this->l('Could not delete DPD Classic Poland');
            }
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_STANDARD_24)) {
            if (!DpdGeopostCarrierStandard24Service::install()) {
                self::$errors[] = $this->l('Could not save DPD Standard 24');
            }
        } else {
            if (!DpdGeopostCarrierStandard24Service::delete()) {
                self::$errors[] = $this->l('Could not delete DPD Standard 24');
            }
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_FASTIUS_EXPRESS)) {
            if (!DpdGeopostCarrierFastiusExpress2HService::install()) {
                self::$errors[] = $this->l('Could not save DPD Fastius Express');
            }
        } else {
            if (!DpdGeopostCarrierFastiusExpress2HService::delete()) {
                self::$errors[] = $this->l('Could not delete DPD Fastius Express');
            }
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_FASTIUS_EXPRESS_2H)) {
            if (!DpdGeopostCarrierFastiusExpress2HService::install()) {
                self::$errors[] = $this->l('Could not save DPD Fastius Express 2H');
            }
        } else {
            if (!DpdGeopostCarrierFastiusExpress2HService::delete()) {
                self::$errors[] = $this->l('Could not delete DPD Fastius Express 2H');
            }
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_PALLET_ONE_ROMANIA)) {
            if (!DpdGeopostCarrierFastiusExpress2HService::install()) {
                self::$errors[] = $this->l('Could not save DPD Fastius Express 2H');
            }
        } else {
            if (!DpdGeopostCarrierFastiusExpress2HService::delete()) {
                self::$errors[] = $this->l('Could not delete DPD Fastius Express 2H');
            }
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_TIRES)) {
            if (!DpdGeopostTiresService::install()) {
                self::$errors[] = $this->l('Could not save DPD TIRES');
            }
        } else {
            if (!DpdGeopostTiresService::delete()) {
                self::$errors[] = $this->l('Could not delete DPD TIRES');
            }
        }

        if (Tools::getValue(DpdGeopostConfiguration::SERVICE_STANDARD_LOCKER)) {
            if (!DpdGeopostCarrierStandardLockerService::install()) {
                self::$errors[] = $this->l('Could not save DPD Standard Locker');
            }
        } else {
            if (!DpdGeopostCarrierStandardLockerService::delete()) {
                self::$errors[] = $this->l('Could not delete DPD Standard Locker');
            }
        }
    }

    private function validateSettings()
    {
        if (!Tools::getValue(DpdGeopostConfiguration::COUNTRY))
            self::$errors[] = $this->l('DPD Country can not be empty');

        if (!Tools::getValue(DpdGeopostConfiguration::USERNAME))
            self::$errors[] = $this->l('Web Service Username can not be empty');

        if (!Tools::getValue(DpdGeopostConfiguration::PASSWORD))
            self::$errors[] = $this->l('Web Service Password can not be empty');

//		if (
//			Tools::getValue(DpdGeopostConfiguration::COUNTRY) == DpdGeopostConfiguration::OTHER &&
//			!Tools::getValue(DpdGeopostConfiguration::PRODUCTION_URL) &&
//			!Tools::getValue(DpdGeopostConfiguration::TEST_URL)
//		)
//			self::$errors[] = $this->l('At least one WS URL must be entered');

        if (Tools::getValue(DpdGeopostConfiguration::PRODUCTION_URL) !== '' && !Validate::isUrl(Tools::getValue(DpdGeopostConfiguration::PRODUCTION_URL)))
            self::$errors[] = $this->l('Production WS URL is not valid');

//		if (Tools::getValue(DpdGeopostConfiguration::TEST_URL) !== '' && !Validate::isUrl(Tools::getValue(DpdGeopostConfiguration::TEST_URL)))
//			self::$errors[] = $this->l('Test WS URL is not valid');

//        if (Tools::getValue(DpdGeopostConfiguration::PASSWORD) !== '' && !Validate::isPasswd(Tools::getValue(DpdGeopostConfiguration::PASSWORD)))
//            self::$errors[] = $this->l('Web Service Password is not valid');

        if (Tools::getValue(DpdGeopostConfiguration::TIMEOUT) !== '' && !Validate::isUnsignedInt(Tools::getValue(DpdGeopostConfiguration::TIMEOUT)))
            self::$errors[] = $this->l('Web Service Connection Timeout is not valid');

        if (!Tools::getValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE))
            self::$errors[] = $this->l('Weight conversation rate can not be empty');
        elseif (!Validate::isFloat(Tools::getValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE)) || Validate::isFloat(Tools::getValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE)) && Tools::getValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE) < 0)
            self::$errors[] = $this->l('Weight conversation rate is not valid');

        $this->validateCODMethods();
    }

    private function validateCODMethods()
    {
        $payment_module_selected = false;
        foreach (DpdGeopost::getPaymentModules() as $payment_module) {
            if (Tools::isSubmit($payment_module['name'])) {
                $payment_module_selected = true;
                break;
            }
        }

        if (!$payment_module_selected) {
            if (
                Tools::isSubmit(DpdGeopostConfiguration::IS_COD_CARRIER_CLASSIC) ||
                Tools::isSubmit(DpdGeopostConfiguration::IS_COD_CARRIER_LOCCO) ||
                Tools::isSubmit(DpdGeopostConfiguration::IS_COD_CARRIER_INTERNATIONAL) ||
                Tools::isSubmit(DpdGeopostConfiguration::IS_COD_CARRIER_REGIONAL_EXPRESS) ||
                Tools::isSubmit(DpdGeopostConfiguration::IS_COD_CARRIER_HUNGARY)
            )
                self::$errors[] = $this->l('COD payment method must be selected to enable COD services');
        }
    }

    private function saveSettings()
    {
        if (DpdGeopostConfiguration::saveConfiguration()) {
            DpdGeopost::addFlashMessage($this->l('Settings saved successfully'));
            Tools::redirectAdmin($this->module_instance->module_url . '&menu=configuration');
        } else
            DpdGeopost::addFlashError($this->l('Could not save settings'));
    }

    public function testConnectivity()
    {
        $ws_production_url = Tools::getValue('production_ws_url');

        $error_message = '';

        if (!$ws_production_url || $ws_production_url && !Validate::isUrl($ws_production_url)) {
            $error_message = $this->module_instance->l('Production URL is not valid', self::FILENAME);
        }

        return $error_message;
    }
}
