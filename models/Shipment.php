<?php

/** **/

if (!defined('_PS_VERSION_'))
    exit;

class DpdGeopostShipment extends DpdGeopostWs
{
    public const FILENAME = 'Shipment';
    public const PAYMENT_TYPE = 'CASH';
    public const PALLET_WEIGHT = 50;

    public const OFFICE_TYPE_OFFICE = 'OFFICE';
    public const OFFICE_TYPE_LOCKER = 'APT';

    protected $targetNamespace = 'http://it4em.yurticikargo.com.tr/eshop/shipment';

    protected $serviceName = 'ShipmentServiceImpl';

    public $id_order;
    public $id_shipment;
    public $shipment_reference;
    public $id_manifest;
    public $label_printed;
    public $date_pickup;
    private $data = array();
    public $parcels;

    public function __construct($id_order = null, $shipment = null)
    {
        parent::__construct();

        $this->id_shipment = $shipment;

        if ($this->id_order = (float)$id_order) {
            if ($shipmentDate = self::getShipmentData($id_order))
                foreach ($shipmentDate as $element => $value) {
                    $this->$element = $value;
                }
        }

        if ($this->id_shipment) {
            $result = $this->wsrest_shipment_info(array('shipmentIds' => array($this->id_shipment)));
            if (isset($result['shipmentResultList']) && isset($result['shipmentResultList']['shipment']))
                $this->data = $result['shipmentResultList']['shipment'];


            $this->parcels = $result['shipments'][0]['content']['parcels'];
        }



    }

    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function calculatePriceForOrder($id_method, $id_address, $products = array(), $extra_params = array(), $withCurrency = true)
    {
        /* method ID is not defined, nothing to do here... */
        if (!$id_method) {
            return false;
        }

        $order = new Order($this->id_order);

        if (!Validate::isLoadedObject($order)) {
            self::$errors[] = $this->l('Order does not exists');
            return false;
        }

        if ($this->parcels)
            $parcels = $this->parcels;
        else {
            if (!$products)
                $products = $order->getProductsDetail();

            $parcels = $this->putProductsToParcels($products);
        }

        if ($result = $this->calculate($id_method, $id_address, $parcels, $order, $extra_params)) {
            if (version_compare(_PS_VERSION_, '1.5', '<'))
                $order_price = $order->total_paid;
            else
                $order_price = $order->total_shipping_tax_incl;

            if ($result['price'] > Tools::convertPrice($order_price, $order->id_currency, false))
                self::$notices[] = $this->l('Shipping costs more than client paid.');

            if($withCurrency) {
                return Tools::displayPrice($result['price'], $result['id_currency']);
            } else {
                return $result['price'];
            }
        }

        return false;
    }

    public function calculate($id_method, $id_address, $parcels, Order $order = null, $extra_params = array())
    {
        $address = new Address($id_address);
        $country = new Country($address->id_country);

        $postcodeSearch = new DpdGeopostPostcodeSearch();


        $dpdAddressSearch = new DpdAddressSearch();
        $countryWs = $dpdAddressSearch->getCountryByIsoCode($country->iso_code);


        $street = pSQL($address->address1) . (($address->address2) ? ' ' . pSQL($address->address2) : '');

        $pickupDate = false;
        $pickupOfficeId = pSql((int)$address->dpd_office);

        //when selecting the pickup point we need to compute the price to see if delivery is possible
        if (isset($extra_params['dpd_office_id'])) {
            $pickupOfficeId = $extra_params['dpd_office_id'];
        }

        if ($id_method == _DPDGEOPOST_LOCKER_ID_ && empty($pickupOfficeId)) {
            $pudoService = new DpdGeopostPudo();
            $offices = $pudoService->listOffices($address->city);
            if (count($offices) > 0) {
                $pickupOfficeId = pSql((int)$offices[0]['id']);
            }
        }

        $deliveryType = pSQL($address->dpd_shipment_type);
        $isOfficeDelivery = ($deliveryType == 'pickup' && !empty($pickupOfficeId)) && $id_method == _DPDGEOPOST_LOCKER_ID_;

        $recipient = array(
            'phone1' => array(
                'number' => $address->phone_mobile ? pSQL($address->phone_mobile) : pSQL($address->phone),
            ),
            'clientName' => pSQL($address->firstname) . ' ' . pSQL($address->lastname),
            'privatePerson' => true
        );
        $service = array(
            'serviceIds' => array($this->mapNewServiceIds((int)$id_method)),
            //'pickupDate' => $pickupDate ? $pickupDate : date('Y-m-d'),
            'autoAdjustPickupDate' => true,
        );



        if ($id_method == _DPDGEOPOST_LOCKER_ID_) {
            $service = [
                'serviceIds' => array(_DPDGEOPOST_STANDARD_24_ID_),
                'autoAdjustPickupDate' => true,
            ];

            $recipient = array(
                'phone1' => array(
                    'number' => $address->phone_mobile ? pSQL($address->phone_mobile) : pSQL($address->phone),
                ),
                'clientName' => pSQL($address->firstname) . ' ' . pSQL($address->lastname),
                'privatePerson' => true,
                "pickupOfficeId" => $pickupOfficeId
            );
        }

        $params = [
            'recipient' => $recipient,
            'service' => $service,
            'payment' => array(
                'courierServicePayer' => DpdGeopostConfiguration::getSettingStatic(DpdGeopostConfiguration::COURIER_SERVICE_PAYER, DpdGeopostConfiguration::COURIER_SERVICE_PAYER_SENDER),
            )

        ];

        if ($params['payment']['courierServicePayer'] == DpdGeopostConfiguration::COURIER_SERVICE_PAYER_THIRD_PARTY) {
            $params['payment']['thirdPartyClientId'] = DpdGeopostConfiguration::getSettingStatic(DpdGeopostConfiguration::COURIER_SERVICE_PAYER_THIRD_PARTY_ID, '');
        }



        if ($isOfficeDelivery && !empty($pickupOfficeId)) {
            $params['recipient']['pickupOfficeId'] = $pickupOfficeId;
        } else {
            $params['recipient']['addressLocation'] = array(
                'countryId' => $countryWs['id'],
                'siteName' => pSQL($address->city),
                'streetName' => substr($street, 0, 69),
                'streetNo' => '',
            );

            $postCode = $postcodeSearch->extractPostCodeForShippingRequest($address);
            /*
            if (!empty($postCode)) {
                $params['recipient']['addressLocation']['postCode'] = $postCode;
            }
            */
        }

        if (!empty($parcels)) {
            $defaultWeight = (float)Configuration::get(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE);
            if (empty($defaultWeight)) {
                $defaultWeight = 0.1;
            }

            $totalWeight = 0;
            
            $params['content'] = array(
                'contents' => 'parcels',
                'parcels' => array()
            );
            // SETARE PARCELE
            $seqNo = 1;
            foreach ($parcels as $parcel) {
                $currentWeight = isset($parcel['weight']) && floatval($parcel['weight']) > 0 ? $parcel['weight'] : $defaultWeight;
                $size = array(
                    "width" => isset($parcel['width']) ? $parcel['width']: 1,
                    "depth" => isset($parcel['depth']) ? $parcel['depth'] : 1,
                    "height"  =>  isset($parcel['height']) ? $parcel['height']: 1
                );
                $params['content']['parcels'][] = array(
                    'weight' => $currentWeight,
                    "size" => $size,
                    "seqNo" => $seqNo
                );
                $totalWeight += $currentWeight;
                $seqNo++;
            }
            
            if($totalWeight >= self::PALLET_WEIGHT) {
                $params['content']['contents'] = 'pallet';
            }
            
            
        }


        $service = array();
        $service['additionalServices'] = array();
        $service['additionalServices']['saturdayDelivery'] = false;

        if ($order !== null) {
            $cod_method = Configuration::get(DpdGeopostConfiguration::COD_MODULE);

            if ($cod_method !== null && $order->module == $cod_method) {
                $currency = new Currency((int)$order->id_currency);

                $service['additionalServices']['cod'] = array(
                    'amount' => version_compare(_PS_VERSION_, '1.5', '<') ? (float)$order->total_paid : (float)$order->total_paid_tax_incl,
                    'currencyCode' => pSQL($currency->iso_code),
                    'processingType' => self::PAYMENT_TYPE,
                    //'referenceNumber' => version_compare(_PS_VERSION_, '1.5', '<') ? pSQL($order->secure_key) : pSQL(self::getOrderReference((int)$order->id))
                );
            }
        }

        if (!empty($extra_params['cod'])) {
            $service['additionalServices']['cod'] = array(
                'amount' => (float)$extra_params['cod']['total_paid'],
                'currencyCode' => pSQL($extra_params['cod']['currency_iso_code']),
                'processingType' => self::PAYMENT_TYPE,
                //'referenceNumber' => pSQL($extra_params['cod']['reference'])
            );
        }

        $sendInsuranceValue = Configuration::get(DpdGeopostConfiguration::SEND_INSURANCE_VALUE);
        if ($sendInsuranceValue && !empty($extra_params['highInsurance'])) {
            // $service['additionalServices']['highInsurance'] = array(
            // 	'goodsValue' => (float)$extra_params['highInsurance']['total_paid'],
            // 	'currency' =>  pSQL($extra_params['highInsurance']['currency_iso_code']),
            // 	'content' => $extra_params['highInsurance']['content'],
            // );
        }

        $additionalServicesOptionTest =  Configuration::get(DpdGeopostConfiguration::DPD_PAYMENT_OPTIONS);
        $additionalServicesReturn =  Configuration::get(DpdGeopostConfiguration::DPD_RETURN_PAY);

        $service['additionalServices']['obpd']['returnShipmentServiceId'] = _DPDGEOPOST_STANDARD_24_ID_;

        $hasAdditionalServices = false;
        if ($additionalServicesOptionTest != '') {
            $hasAdditionalServices = true;
            $service['additionalServices']['obpd']['option'] = $additionalServicesOptionTest;
        }

        if ($additionalServicesReturn != '') {
            $hasAdditionalServices = true;
            $service['additionalServices']['obpd']['returnShipmentPayer'] = $additionalServicesReturn;
        }

        if ($hasAdditionalServices) {
            $params['service']['additionalServices'] = $service['additionalServices'];
        }


        $senderId = DpdGeopostConfiguration::getSettingStatic(DpdGeopostConfiguration::SENDER_ID, false);

        $senderOfficeId = Configuration::get(DpdGeopostConfiguration::SENDER_DROPOFF_OFFICE);

        if ($senderId && !$senderOfficeId) {
            $params['sender']['clientId'] = $senderId;
        }

        if ($senderOfficeId) {
            $params['sender']['dropoffOfficeId'] = $senderOfficeId;
        }

        if (isset($params['recipient']['addressLocation']) && isset($params['recipient']['addressLocation']['siteName'])) {
            $countryId = $params['recipient']['addressLocation']['countryId'];
            $siteName = $params['recipient']['addressLocation']['siteName'];

            $findSiteRequest = array(
                "countryId" => $countryId,
                "name" => $siteName,
            );

            $countryState = new State($address->id_state);

            if ($countryState) {
                $findSiteRequest['region'] = $countryState->name;
            }

            $locationsFound = $this->wsrest_location_site($findSiteRequest);

            if (!empty($locationsFound) && !empty($locationsFound['sites'])) {
                $locationFound = $locationsFound['sites'][0];
                unset($params['recipient']['addressLocation']);
                $params['recipient']['addressLocation'] = array(
                    "countryId" => $countryId,
                    "siteId" => $locationFound["id"]
                );
            }

        }

        if ($id_method == _DPDGEOPOST_LOCKER_ID_ && !empty($pickupOfficeId)) {
            unset($params['recipient']['addressLocation'] );
        }

        $result = $this->wsrest_calculate($params);

        if (!reset(self::$errors)) {

            if (!isset($result['calculations'][0]) || !isset($result['calculations'][0]['price']) || !isset($result['calculations'][0]['price']['currencyLocal'])) {
                if (isset($result['calculations'][0]['error']['message'])) {
                    self::$errors[] = sprintf($this->l($result['calculations'][0]['error']['message']));
                } else {
                    self::$errors[] = sprintf($this->l('Unable to communicate with DPD service.'));
                }
                return false;
            }

            if ($id_currency = Currency::getIdByIsoCode($result['calculations'][0]['price']['currencyLocal'], $this->context->shop->id)) {
                return array(
                    'price' => (float)$result['calculations'][0]['price']['totalLocal'],
                    'id_currency' => (int)$id_currency
                );
            } else {
                self::$errors[] = sprintf($this->l('Currency %s is not installed, price cannot be calculated'), $result['calculations'][0]['price']['currencyLocal']);
                return false;
            }
        }

        return false;
    }

    public function save($id_method, $id_address, $parcels, $extraOptions = array())
    {

        if (!$this->validateParcelsWeights($parcels)) {
            return false;
        }

        $order = new Order($this->id_order);


        if (!Validate::isLoadedObject($order)) {
            self::$errors[] = $this->l('Order does not exists');
            return false;
        }

        $currentCurrency = new Currency($order->id_currency);
        if (!Validate::isLoadedObject($currentCurrency)) {
            self::$errors[] = $this->l('Currency does not exists');
            return false;
        }

        $address = new Address($id_address);
        if (!Validate::isLoadedObject($address)) {
            self::$errors[] = $this->l('Address does not exists');
            return false;
        }

        $customer = new Customer((int)$address->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            self::$errors[] = $this->l('Customer does not exists');
            return false;
        }

        $country = new Country($address->id_country);
        $dpdAddressSearch = new DpdAddressSearch();
        $countryWs = $dpdAddressSearch->getCountryByIsoCode($country->iso_code);

        $street = pSQL($address->address1) . (($address->address2) ? ' ' . pSQL($address->address2) : '');

        $receiverName = '';
//        if ($address->company) {
//            $receiverName .= pSQL($address->company) . ' - ';
//        }
        $receiverName .= pSQL($address->firstname) . ' ' . pSQL($address->lastname);



        $params = array(
            'receiverName' => $receiverName,
            'receiverFirmName' => pSQL($address->company),
            'receiverCountryCode' => pSQL($country->iso_code),
            'receiverZipCode' => pSQL($address->postcode),
            'receiverCity' => pSQL($address->city),
            'receiverStreet' => $street,
            'receiverHouseNo' => '',
            'receiverPhoneNo' => $address->phone_mobile ? pSQL($address->phone_mobile) : pSQL($address->phone),
            'mainServiceCode' => $this->mapNewServiceIds((int)$id_method),
            'shipmentReferenceNumber' => version_compare(_PS_VERSION_, '1.5', '<') ? pSQL($order->secure_key) : pSQL(self::getOrderReference((int)$order->id)),
            'additionalInfo' => 'additional info test',
            'receiverEmail' => pSQL($customer->email)
        );

        if ($params['mainServiceCode'] == _DPDGEOPOST_LOCKER_ID_) {
            $params['mainServiceCode'] = _DPDGEOPOST_STANDARD_24_ID_;
        }

        if ($order !== null) {
            $cod_method = Configuration::get(DpdGeopostConfiguration::COD_MODULE);

            if ($cod_method !== null && $order->module == $cod_method) {
                $currency = new Currency((int)$order->id_currency);

                $params['additionalServices'] = array(
                    'cod' => array(
                        'amount' => version_compare(
                            _PS_VERSION_,
                            '1.5',
                            '<'
                        ) ? (float)$order->total_paid : (float)$order->total_paid_tax_incl,
                        'currency' => pSQL($currency->iso_code),
                        'paymentType' => self::PAYMENT_TYPE,
                        'referenceNumber' => version_compare(
                            _PS_VERSION_,
                            '1.5',
                            '<'
                        ) ? pSQL($order->secure_key) : pSQL(self::getOrderReference((int)$order->id)),
                    ),
                );
            }

            $currency = new Currency((int)$order->id_currency);
            $products = $order->getProductsDetail();
            $productsNameString = '';
            foreach ($products as $product) {
                $productsNameString .= '|' . $product['product_name'];
            }

            if (empty($params['additionalServices'])) {
                $params['additionalServices'] = [];
            }
            $configuration = new DpdGeopostConfiguration();
            if ($configuration->getPredictSettingByMethodId($id_method)) {
                $params['additionalServices']['predictSms'] = array(
                    'telephoneNr' => $address->phone_mobile ? pSQL($address->phone_mobile) : pSQL($address->phone),
                );
            }
            $sendInsuranceValue = Configuration::get(DpdGeopostConfiguration::SEND_INSURANCE_VALUE);
            if ($sendInsuranceValue) {
                $params['additionalServices']['highInsurance'] = array(
                    'goodsValue' => version_compare(
                        _PS_VERSION_,
                        '1.5',
                        '<'
                    ) ? (float)$order->total_paid : (float)$order->total_paid_tax_incl,
                    'currency' => pSQL($currency->iso_code),
                    'content' => $productsNameString,
                );
            }
        }

        //		$params['additionalServices']['saturdayDelivery'] = false;


        $pickupOfficeId = pSQL($address->dpd_office);
        $pickupDate = false;
        $shipmentPayload = array(
            'recipient' => array(
                'phone1' => array(
                    'number' => $params['receiverPhoneNo'],
                ),
                'clientName' =>  ($address->company && trim($address->company)) ? $address->company : $params['receiverName'],
                'privatePerson' =>  ($address->company && trim($address->company)) ? false : true,
                'email' => $params['receiverEmail']
            ),
            'service' => array(
                //'pickupDate' => $pickupDate ? $pickupDate : date('Y-m-d'),
                'autoAdjustPickupDate' => true,
                'serviceId' => $params['mainServiceCode'],
                'saturdayDelivery' => false,
            ),
            'content' => array(),
            'payment' => array(
                'courierServicePayer' => DpdGeopostConfiguration::getSettingStatic(DpdGeopostConfiguration::COURIER_SERVICE_PAYER, DpdGeopostConfiguration::COURIER_SERVICE_PAYER_SENDER),
            ),
             'ref1' => $params['shipmentReferenceNumber']
            
        );

        if($address->company && trim($address->company)) {
            $shipmentPayload['recipient']['contactName'] = $params['receiverName'];
        }

        if ($shipmentPayload['payment']['courierServicePayer'] == DpdGeopostConfiguration::COURIER_SERVICE_PAYER_THIRD_PARTY) {
            $shipmentPayload['payment']['thirdPartyClientId'] = DpdGeopostConfiguration::getSettingStatic(DpdGeopostConfiguration::COURIER_SERVICE_PAYER_THIRD_PARTY_ID, '');
        }

        $shipmentType = pSQL($address->dpd_shipment_type);
        $isPickupDelivery = $shipmentType == 'pickup' && !empty($pickupOfficeId);
        if ($isPickupDelivery) {
            $shipmentPayload['recipient']['pickupOfficeId'] = $pickupOfficeId;
        } else {

            if (in_array($countryWs['id'], array('642', '100'))) {
                $shipmentPayload['recipient']['address'] = array(
                    'countryId' => $countryWs['id'],
                    'siteName' => pSQL($address->city),
                    'streetName' => pSQL($address->address1) . (($address->address2) ? ' ' . pSQL($address->address2) : ''),
                    'streetNo' => 1,
                );
            } else {
                $shipmentPayload['recipient']['address'] = array(
                    'countryId' => $countryWs['id'],
                    'siteName' => pSQL($address->city),
                    'postCode' => pSQL($address->postcode),
                    'addressLine1' => pSQL($address->address1),
                    'addressLine2' => pSQL($address->address2),
                );
            }

            $addressIsNormalized = false;
            if (in_array($countryWs['id'], array('642', '100'))) {
                if ($address->dpd_site) {
                    unset($shipmentPayload['recipient']['address']['siteType']);
                    unset($shipmentPayload['recipient']['address']['siteName']);
                    $shipmentPayload['recipient']['address']['siteId'] = $address->dpd_site;
                }

                if ($address->dpd_street) {
                    unset($shipmentPayload['recipient']['address']['streetName']);
                    unset($shipmentPayload['recipient']['address']['streetType']);
                    $shipmentPayload['recipient']['address']['streetId'] = $address->dpd_street;
                    $addressIsNormalized = true;
                }


                if ($address->dpd_block) {
                    $chunks = explode(':', $address->dpd_block);

                    $street_no = $chunks[0];

                    $block_no = $chunks[1];

                    $app_no = $chunks[2];

                    if (empty($street_no)) {
                        $street_no = (int)strpbrk($address->address1 . ' ' . $address->address2, "0123456789");
                    }

                    $shipmentPayload['recipient']['address']['streetNo'] = $street_no;
                    $shipmentPayload['recipient']['address']['blockNo'] = $block_no;
                    $shipmentPayload['recipient']['address']['apartmentNo'] = $app_no;
                }
            }


            if(!$addressIsNormalized) {
                $shipmentPayload['recipient']['address']['addressNote'] = substr(trim($address->address1) . ' ' . trim($address->address2), 0, 200);
            }

        }


        $parcelsForDelivery = $this->prepareParcelsDataForWS($order, $parcels);


        $parcelsCount = 1;

        $defaultWeight = (float)Configuration::get(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE);
        if (empty($defaultWeight)) {
            $defaultWeight = 0.1;
        }

        if (!empty($parcelsForDelivery)) {
            $shipmentContent = array(
                'contents' => 'parcels',
                'package' => 'parcels',
                'parcels' => array()
            );

            $totalWeight = 0;
            $seqNo = 1;

            if (is_countable($parcelsForDelivery['data'])) {
                foreach ($parcelsForDelivery['data'] as $parcel) {
                    $parcelData = array(
                        'seqNo' => $seqNo,
                        'weight' => isset($parcel['weight']) && floatval($parcel['weight']) > 0 ? $parcel['weight'] : $defaultWeight
                        //'ref1' => $parcel['parcelReferenceNumber']
                    );

                    if (isset($parcel['size'])) $parcelData['size'] = $parcel['size'];
                    $totalWeight += $parcelData['weight'];
                    /*if(!empty($extraOptions) && isset($extraOptions['shipment_reference'])) {
                        $parcelData['ref2'] = substr($extraOptions['shipment_reference'], 0, 30);
                    }*/

                    $shipmentContent['parcels'][] = $parcelData;
                    $seqNo++;
                }

                if ($isPickupDelivery && $address->dpd_office_type !== self::OFFICE_TYPE_OFFICE) {
                    //for locker only weight matters
                    unset($shipmentContent['parcels']);

                    $parcelData = array(
                        'seqNo' => 1,
                        'weight' => $totalWeight
                    );

                    if (isset($parcelsForDelivery['data'][0]['size'])) {
                        $parcelData['size'] = $parcelsForDelivery['data'][0]['size'];
                    }

                    $shipmentContent['parcels'][] = $parcelData;
                }

            }
            $parcelsCount = count( $shipmentContent['parcels']);

            if($totalWeight >= self::PALLET_WEIGHT) {
                $shipmentContent['contents'] = 'pallet';
                $shipmentContent['package'] = 'pallet';
            }
            $shipmentPayload['content'] = $shipmentContent;
        }

        if (!empty($extraOptions)) {

            if (isset($extraOptions['rod_enabled']) && $extraOptions['rod_enabled'] == '1') {
                $params['returns']['rod'] = array(
                    "enabled" => true,
                );
            }

            if (isset($extraOptions['swap_enabled']) && $extraOptions['swap_enabled'] == '1') {
                $params['returns']['swap'] = array(
                    'serviceId' => $params['mainServiceCode'],
                    'parcelsCount' => $parcelsCount
                );
            }

            if (isset($extraOptions['reusable_enabled']) && $extraOptions['reusable_enabled'] == '1' && $params['mainServiceCode'] == 2505) {
                $params['returns']['swap'] = array(
                    'serviceId' => 2007,
                    'parcelsCount' => $parcelsCount
                );
            }

            if (isset($extraOptions['voucher_enabled']) && $extraOptions['voucher_enabled'] == '1') {
                $params['returns']['returnVoucher'] = array(
                    'serviceId' => $params['mainServiceCode'],
                    'payer' => DpdGeopostConfiguration::getSettingStatic(DpdGeopostConfiguration::COURIER_SERVICE_PAYER, DpdGeopostConfiguration::COURIER_SERVICE_PAYER_SENDER)
                );
            }

            if (isset($extraOptions['shipment_note'])) {
                $shipmentPayload['shipmentNote'] = substr($extraOptions['shipment_note'], 0, 200);
            }

            if(isset($extraOptions['shipment_reference'])) {
                $shipmentPayload['ref2'] = substr($extraOptions['shipment_reference'], 0, 30);
            }
        }

        // SET send insurance value "Normal Price"



        // $payer by default este sender chiar daca s-a setat recipient in admin
        // $payer poate fi recipient, dar nu obligatoriu doar daca s-a selectate DPDCOD
        $payer = DpdGeopostConfiguration::COURIER_SERVICE_PAYER_SENDER;

        $includesShipping = false;
        if (DpdGeopostShipment::selectedPaymentIsDPDCOD($order)) {
            $payer = DpdGeopostConfiguration::getSettingStatic(DpdGeopostConfiguration::COURIER_SERVICE_PAYER, DpdGeopostConfiguration::COURIER_SERVICE_PAYER_SENDER);
            $cod_amount = floatval($order->total_paid_tax_incl); // + floatVal($codAmount);


            $prestashopRules = true;

            if($prestashopRules) {
                // DACA prestashop rules SI dpdpayment SI payer = recipient ATUNCI Calcul Shipment Presta Rules, fara shipping (si fara COD Tax, fiindca este incluse in shipping)
                if ($payer == DpdGeopostConfiguration::COURIER_SERVICE_PAYER_RECIPIENT) {
                    $cod_amount = $cod_amount - floatval($order->total_shipping_tax_incl) ;
                }

                // DACA prestashop rules SI dpdpayment SI payer = sender ATUNCI Calcul Shipment Presta Rules + COD Tax (este inclusa by default in shipping)
                if ($payer == DpdGeopostConfiguration::COURIER_SERVICE_PAYER_SENDER) {
                    $cod_amount = $cod_amount;
                }
            }

            if(!$prestashopRules) {
                // DACA webservice SI dpdpayment ATUNCI valoare dpdpayment vine direct din webservice, ignoram ce este setat in admin si excludem shipping tax
                $cod_amount = $cod_amount - floatval($order->total_shipping_tax_incl);

                if(Configuration::get(DpdGeopostConfiguration::PRICE_CALCULATION_INCLUDE_SHIPPING) == 'yes') {
                    $includesShipping = true;
                }

            }


            if (pSQL($currentCurrency->iso_code) == 'HUF') {
                $cod_amount = ceil($cod_amount);
            }

            $shipmentPayload['service']['additionalServices']['cod'] = array(
                'amount' => $cod_amount,
                'currencyCode' => pSQL($currentCurrency->iso_code),
                'includeShippingPrice' => $includesShipping
            );
        }

        $shipmentPayload['payment']['courierServicePayer'] = $payer;

        $SEND_INSURANCE_VALUE = Configuration::get(DpdGeopostConfiguration::SEND_INSURANCE_VALUE);
        if ($SEND_INSURANCE_VALUE) {
            $shipmentPayload['service']['additionalServices']['declaredValue']['amount'] = (float)$order->total_paid_tax_incl;
        }

        if (isset($params['returns'])) {
            $shipmentPayload['service']['additionalServices']['returns'] = $params['returns'];
        }

        $additionalServicesOptionTest = Configuration::get(DpdGeopostConfiguration::DPD_PAYMENT_OPTIONS);
        $additionalServicesReturn =  Configuration::get(DpdGeopostConfiguration::DPD_RETURN_PAY);

        $shipmentPayload['service']['additionalServices']['obpd']['returnShipmentServiceId'] = _DPDGEOPOST_STANDARD_24_ID_;
        if ($additionalServicesOptionTest != '') {
            $shipmentPayload['service']['additionalServices']['obpd']['option'] = $additionalServicesOptionTest;
        }

        if ($additionalServicesReturn) {
            $shipmentPayload['service']['additionalServices']['obpd']['returnShipmentPayer'] = $additionalServicesReturn;
        }

        $senderId = Configuration::get(DpdGeopostConfiguration::SENDER_ID);
        $senderOfficeId = Configuration::get(DpdGeopostConfiguration::SENDER_DROPOFF_OFFICE);

        if ($senderId && !$senderOfficeId) {
            $shipmentPayload['sender']['clientId'] = $senderId;
        }

        if ($senderOfficeId) {
            $shipmentPayload['sender']['dropoffOfficeId'] = $senderOfficeId;
        }


        $params[] = $parcelsForDelivery;

        if ($this->id_shipment) {
            $result = $this->update($shipmentPayload, $order, $params);
        } else {
            $result = $this->create($shipmentPayload, $order, $params);
        }

        if (!$result) {
            return false;
        }


        if (isset($result['answer']['id'])) {
            $this->getAndSaveTrackingInfo($result['answer']['id']);
        }


        if ($id_method == _DPDGEOPOST_LOCKER_ID_) {
            $id_method = _DPDGEOPOST_STANDARD_24_ID_;
        }
        if (!$this->updateOrder($id_address, $id_method, $order))
            return false;

        return $result;
    }

    private static function selectedPaymentIsDPDCOD($order)
    {
        if (Module::isEnabled('dpdpayment')) {
            $payment_module = $order->module;

            if ($payment_module == 'dpdpayment') {
                return true;
            }
        }

        return false;
    }


    private static function getOrderReference($id_order)
    {
        $reference = DB::getInstance()->getValue('
			SELECT `reference`
			FROM `' . _DB_PREFIX_ . _DPDGEOPOST_REFERENCE_DB_ . '`
			WHERE `id_order` = "' . (int)$id_order . '"
		');

        return $reference ? $reference : self::createOrderReference((int)$id_order);
    }

    private
    static function createOrderReference($id_order)
    {
        $reference = strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));

        DB::getInstance()->Execute('
			INSERT INTO `' . _DB_PREFIX_ . _DPDGEOPOST_REFERENCE_DB_ . '`
				(`id_order`, `reference`)
			VALUES
				("' . (int)$id_order . '", "' . pSQL($reference) . '")
		');

        return $reference;
    }

    private function validateParcelsWeights($parcels)
    {
        $are_parcels_valid = true;

        if (empty($parcels)) {
            return $are_parcels_valid;
        }

        foreach ($parcels as $parcel)
            if (!Validate::isUnsignedFloat($parcel['weight'])) {
                self::$errors[] = sprintf($this->l('Parcel total weight "%s" is not valid'), $parcel['weight']);
                $are_parcels_valid = false;
            }
        return $are_parcels_valid;
    }

    public function create($params, $order, $oldParams)
    {
        //$result = $this->createShipment('shipmentList', $params, array('priceOption' => 'WithoutPrice'));
        $result = $this->wsrest_shipment($params);

        if (!reset(self::$errors)) {
            if (isset($result['error'])) {
                self::$errors[] = $this->l('Could not receive properly contact web services. Technical error for support:' . $result['error']['context']);
                return false;
            }

            $this->id_shipment = $result['id'];
            $this->shipment_reference = $result['id'];

            $insertSql = '
				INSERT INTO `' . _DB_PREFIX_ . _DPDGEOPOST_SHIPMENT_DB_ . '`
					(`id_order`, `id_shipment`,`shipment_reference`)
				VALUES
					(' . (int)$this->id_order . ', ' . (float)$this->id_shipment . ', \'' . $this->shipment_reference . '\')
			';

            if (!Db::getInstance()->execute($insertSql)) {
                self::$errors[] = $this->l('Shipment could not be created locally');
                return false;
            } elseif (!$this->saveParcelsLocally($oldParams[0]['data'], $order->id)) {
                self::$errors[] = $this->l('Parcels could not be saved locally');
                return false;
            }
        }


        return array('message' => $this->l('Your shipment is successfully saved'), 'answer' => $result);
    }


    public function update($params, $order, $oldParams)
    {
        $result = $this->wsrest_shipment($params);

        if (!reset(self::$errors)) {
            if (isset($result['error'])) {
                self::$errors[] = $this->l('Could not receive properly contact web services. Technical error for support:' . $result['error']['context']);
                return false;
            }

            $this->id_shipment = $result['id'];

            if (!$this->saveParcelsLocally($oldParams[0]['data'], $order->id)) {
                self::$errors[] = $this->l('Parcels could not be saved locally');
                return false;
            }
        }

        return $this->l('Your shipment is successfully saved');
    }

    public
    function delete()
    {
        if (!$this->id_order) {
            self::$errors[] = $this->l('Order does not exists');
            return false;
        }

        $this->wsrest_shipment_cancel(array(
            'shipmentId' => $this->id_shipment,
            'comment' => 'Shipment removed from prestashop'
        ));

        if (!reset(self::$errors)) {
            if (
                !Db::getInstance()->execute('
				DELETE FROM `' . _DB_PREFIX_ . _DPDGEOPOST_SHIPMENT_DB_ . '`
				WHERE `id_order`=' . (int)$this->id_order) || !DpdGeopostParcel::clearOrderParcels((int)$this->id_order)
            ) {
                self::$errors[] = $this->l('Shipment could not be deleted locally');
                return false;
            }
        }

        return true;
    }

    public
    function getStatus()
    {
        $this->getShipmentStatus('shipmentReferenceList', array('id' => $this->id_shipment));
    }


    public
    function search($date_from = '20080909', $date_to = '20150909')
    {
        $result = $this->searchShipment('searchParams', array('startDate' => $date_from, 'endDate' => $date_to));
        return isset($result['shipmentInfoList']) ? $result['shipmentInfoList'] : array();
    }

    public function getLabelsPdf($shipment_ids = null)
    {

        if (!$shipment_ids && !$this->id_order)
            return array('error' => $this->l('Order does not exists'));

        if (!$shipment_ids)
            $shipment_ids = array($this->id_shipment);

        $parcels = array();

        foreach ($shipment_ids as $id_shipment) {

            $shipment = new DpdGeopostShipment(null, $id_shipment);

            $ws_parcels = array();
            if (!$shipment->parcels) {

                $result = $this->wsrest_shipment_info(array('shipmentIds' => array($this->id_shipment)));
                $ws_parcels = $result['shipments'][0]['content']['parcels'];
            } else {

                $ws_parcels = $shipment->parcels;
            }


            foreach ($ws_parcels as $parcel) {

                $parcels[] = array(
                    'parcel' => array(
                        'id' => $parcel['id']
                    )
                );
            }
        }

        $dpd_print_format = Configuration::get(DpdGeopostConfiguration::DPD_PRINT_FORMAT);
        $dpd_paper_size = Configuration::get(DpdGeopostConfiguration::DPD_PAPER_SIZE);
        $dpd_payment_options = Configuration::get(DpdGeopostConfiguration::DPD_PAYMENT_OPTIONS);
        $dpd_return_pay = Configuration::get(DpdGeopostConfiguration::DPD_RETURN_PAY);

        
        $request = array(
            'parcels' => $parcels,
            'paperSize' => $dpd_paper_size,
            'format' => $dpd_print_format,
            'option' => $dpd_payment_options,
            'returnShipmentPayer' => $dpd_return_pay,
            '_raw' => true
        );
        //$result = $this->getShipmentLabel(null, array($params), array('printOption' => 'Pdf'));
        //echo '<pre>';print_r($request);exit;
        $result = $this->wsrest_print($request);

        if (!reset(self::$errors)) {

            foreach ($shipment_ids as $id_shipment) {
                if (!$this->setLabelPrinted((float)$id_shipment)) {
                    return false;
                }
            }

            return $result;
        } else {
            self::$errors[] = $this->l('Label PDF file cannot be generated');
            return false;
        }


        return false;
    }

    public
    function getVouchersPdf()
    {
        $parcelIds = array();
        foreach ($this->parcels as $parcel) {
            $parcelIds[] = $parcel['id'];
        }
        $activeVouchers = $this->getActiveVouchers($parcelIds);

        $request = array(
            'shipmentIds' => $activeVouchers,
            '_raw' => true
        );

        $result = $this->wsrest_print_voucher($request);

        if (!reset(self::$errors)) {

            return $result;
        } else {
            self::$errors[] = $this->l('Voucher PDF file cannot be generated');
            return false;
        }


        return false;
    }

    private function updateOrder($id_address, $id_method, &$order)
    {
        $id_carrier = $this->getIdcarrierFromIdMethod($id_method);

        if ($id_carrier && Validate::isLoadedObject(new Carrier($id_carrier))) {
            if (
                ($order->id_address_delivery != $id_address) ||
                ($order->id_carrier != $id_carrier) ||
                ($order->getWsShippingNumber() != $this->id_shipment)
            ) {
                $order->id_address_delivery = (int)$id_address;
                $order->id_carrier = (int)$id_carrier;

                if ($id_order_carrier = (int)$order->getIdOrderCarrier()) {
                    $order_carrier = new OrderCarrier((int)$id_order_carrier);
                    $order_carrier->id_carrier = $order->id_carrier;
                    $order_carrier->update();
                }

                if (!$order->update()) {
                    self::$errors[] = $this->l('Order could not be updated');
                    return false;
                }
            }

            return true;
        } else {
            self::$errors[] = $this->l('Carrier does not exists. Order could not be updated.');
            return false;
        }

        return false;
    }


    public
    function getAndSaveTrackingInfo($shipmentId)
    {
        $result = $this->getShipmentDataById($shipmentId);

        if (empty($result)) {
            return false;
        }
        //obtain a tracking number from api
        $referenceNumber = $result['shipment_reference'];
        $id_order = $result['id_order'];
        $order = new Order($id_order);

        //if we already have a tracking number is not longer needed to call the api method
        $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());

        if (!empty($order_carrier->tracking_number)) {
            return $order_carrier->tracking_number;
        }


        if (empty($order) || empty($order->id_address_delivery)) {
            return false;
        }

        $addressId = $order->id_address_delivery;

        $this->addTrackingNumber($order, $shipmentId, $order->id_carrier, $addressId);

        return true;
    }

    protected
    function getShipmentStatusWs($shipmentId, $shippingReference)
    {
        $params = array(
            "parcels" => array(
                array(
                    "id" => $shipmentId
                )
            )
        );

        $result = $this->wsrest_track($params);

        if (empty($result['parcels'])) {
            return false;
        }

        return true;
    }


    private
    function addTrackingNumber(&$order, $tracking_number, $id_carrier, $id_address)
    {
        if (version_compare(_PS_VERSION_, '1.5', '<'))
            return $this->addShippingNumber($order, $tracking_number, $id_carrier, $id_address);

        $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
        if (!Validate::isLoadedObject($order_carrier)) {
            self::$errors[] = $this->l('The order carrier ID is invalid.');
            return false;
        } elseif (!Validate::isTrackingNumber($tracking_number)) {
            self::$errors[] = $this->l('The tracking number is incorrect.');
            return false;
        } else {
            $order->id_address_delivery = (int)$id_address;
            $order->shipping_number = $tracking_number;
            $order->id_carrier = (int)$id_carrier;
            $order->update();

            $order_carrier->tracking_number = pSQL($tracking_number);
            if ($order_carrier->update()) {
                $customer = new Customer((int)$order->id_customer);
                $carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
                if (!Validate::isLoadedObject($customer)) {
                    self::$errors[] = $this->l('Can\'t load Customer object');
                    return false;
                }
                if (!Validate::isLoadedObject($carrier))
                    return false;
                $templateVars = array(
                    '{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
                    '{firstname}' => pSQL($customer->firstname),
                    '{lastname}' => pSQL($customer->lastname),
                    '{id_order}' => (int)$order->id,
                    '{shipping_number}' => pSQL($order->shipping_number),
                    '{order_name}' => pSQL($order->getUniqReference())
                );
                if (@Mail::Send(
                    (int)$order->id_lang,
                    'in_transit',
                    Mail::l('Package in transit', (int)$order->id_lang),
                    $templateVars,
                    $customer->email,
                    $customer->firstname . ' ' . $customer->lastname,
                    null,
                    null,
                    null,
                    null,
                    _PS_MAIL_DIR_,
                    true,
                    (int)$order->id_shop
                )) {
                    Hook::exec('actionAdminOrdersTrackingNumberUpdate', array('order' => $order, 'customer' => $customer, 'carrier' => $carrier));
                    return true;
                } else {
                    //self::$errors[] = $this->l('An error occurred while sending an email to the customer.');
                    return true;
                }
            } else {
                self::$errors[] = $this->l('The order carrier cannot be updated.');
                return false;
            }
        }
    }

    private
    function addShippingNumber(&$order, $shipping_number, $id_carrier, $id_address)
    {
        $order->id_address_delivery = (int)$id_address;
        $order->shipping_number = (int)$shipping_number;
        $order->id_carrier = (int)$id_carrier;
        $order->update();
        if ($shipping_number) {
            $customer = new Customer((int)($order->id_customer));
            $carrier = new Carrier((int)($order->id_carrier));
            if (!Validate::isLoadedObject($customer) or !Validate::isLoadedObject($carrier)) {
                self::$errors[] = $this->l('Customer / Carrier not found');
                return false;
            }
            $templateVars = array(
                '{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
                '{firstname}' => pSQL($customer->firstname),
                '{lastname}' => pSQL($customer->lastname),
                '{order_name}' => sprintf("#%06d", (int)($order->id)),
                '{id_order}' => (int)$order->id
            );
            @Mail::Send(
                (int)$order->id_lang,
                'in_transit',
                Mail::l('Package in transit', (int)$order->id_lang),
                $templateVars,
                $customer->email,
                $customer->firstname . ' ' . $customer->lastname,
                null,
                null,
                null,
                null,
                _PS_MAIL_DIR_,
                true
            );
        }

        return true;
    }

    private
    function saveParcelsLocally($data, $id_order)
    {
        DpdGeopostParcel::clearOrderParcels($id_order);

        foreach ($data as $parcel) {
            $products = explode(',', $parcel['products']);

            foreach ($products as $product) {
                list($id_product, $id_product_attribute) = explode('_', trim($product));

                $dpdParcel = new DpdGeopostParcel;
                $dpdParcel->id_order = (int)$id_order;
                $dpdParcel->parcelReferenceNumber = pSQL($parcel['parcelReferenceNumber']);
                $dpdParcel->id_product = (int)$id_product;
                $dpdParcel->id_product_attribute = (int)$id_product_attribute;

                if (!$dpdParcel->save())
                    return false;
            }
        }

        return true;
    }

    private
    function prepareParcelsDataForWS(Order $order, $parcels)
    {
        $products =  $order->getProductsDetail();
        $variantDimensions = $this->productVariantDimensions($products);

        if (is_countable($parcels)) {
            foreach ($parcels as $parcel_number => $data) {
                $parcels[$parcel_number]['weight'] = pSQL($data['weight']);
                $parcels[$parcel_number]['parcelReferenceNumber'] = (int)$order->id . pSQL($parcel_number) . '_' . mt_rand(0, 99);

                if (isset($variantDimensions[$data['products']])) {
                    $parcels[$parcel_number]['size'] = $variantDimensions[$data['products']];
                }

            }
        }

        return array(
            'name' => 'parcels',
            'data' => $parcels
        );
    }

    private
    function getProductWeights($products)
    {
        $weights = array();

        foreach ($products as $product) {
            $this->extractAndFormatProductData($product);
            $weights[$product['id_product'] . '_' . $product['id_product_attribute']] = $product['product_weight'];
        }

        return $weights;
    }

    /**
     * Adds parcel data (description) for each product. Products are also split by quantity.
     * Ex. Product with quantity 2 will be split into two separate products.
     *
     * @param    array $products order/cart products data ($order->getProductsDetail()|$cart->getProducts())
     * @return    array
     * @access    public    products with parcel data
     */

    public function getParcelsSetUp($products)
    {
        $parcels = array();


        foreach ($products as $product) {
            $quantity = isset($product['product_quantity']) ? (int)$product['product_quantity'] : (int)$product['quantity'];

            $this->extractAndFormatProductData($product);

            if ($this->config->packaging_method != DpdGeopostConfiguration::ONE_PRODUCT) {
                for ($i = 0; $i < $quantity; $i++) {
                    if (empty($parcels))
                        $product['description'] = $product['id_product'] . '_' . $product['id_product_attribute'];
                    else {
                        $product['description'] = '';
                        $parcels[0]['description'] .= ', ' . $product['id_product'] . '_' . $product['id_product_attribute'];
                    }
                }
            } else {
                $product['description'] = $product['id_product'] . '_' . $product['id_product_attribute'];
            }

            for ($i = 0; $i < $quantity; $i++)
                $parcels[] = $product;
        }

        return $parcels;
    }

    public function putProductsToParcels($products)
    {
        $parcels = array();
        $all_products_in_one_parcel = ($this->config->packaging_method == DpdGeopostConfiguration::ALL_PRODUCTS) ? true : false;
        $defaultWeight = (float)Configuration::get(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE);
        if (empty($defaultWeight)) {
            $defaultWeight = 0.1;
        }

        foreach ($products as &$product) {
            $this->extractAndFormatProductData($product);
            $parcel = array();

            $product_weight = $product['product_weight'];
            if (empty($product_weight)) {
                $product_weight = $defaultWeight;
            }

            $parcel['description'] = $product['id_product'] . '_' . $product['id_product_attribute'];
            $parcel['weight'] = ((float)$product_weight) * $product['cart_quantity'];
            $parcel['width'] = $product['product_width'];
            $parcel['height'] = $product['product_height'];
            $parcel['depth'] = $product['product_depth'];

            if ($all_products_in_one_parcel && !empty($parcels)) {
                $parcels[0]['description'] .= ', ' . $parcel['description'];
            } else
                $parcels[] = $parcel;
        }
        return $parcels;
    }

    private
    function extractAndFormatProductData(&$product)
    {
        
        $id_product = isset($product['product_id']) ? (int)$product['product_id'] : (int)$product['id_product'];
        $id_product_attribute = isset($product['product_attribute_id']) ? (int)$product['product_attribute_id'] : (int)$product['id_product_attribute'];
        $product_name = isset($product['product_name']) ? $product['product_name'] : $product['name'];
        $fields = array(
            'quantity', 'cart_quantity', 'product_quantity'
        );
        $cart_quantity = 0;
        foreach ($fields as $quantity_field) {
            if (array_key_exists($quantity_field, $product)) {
                $cart_quantity = $product[$quantity_field];
            }
        }
        $product_weight = isset($product['product_weight']) ? self::convertWeight($product['product_weight']) : self::convertWeight($product['weight']);

        $product = [
            'id_product' => (int)$id_product,
            'id_product_attribute' => pSQL($id_product_attribute),
            'product_name' => pSQL($product_name),
            'product_weight' => (float)$product_weight,
            'cart_quantity' => (float)$cart_quantity,
            'product_width' => isset($product['width']) ? (float)$product['width'] : 1,
            'product_height' => isset($product['height']) ? (float)$product['height'] : 1,
            'product_depth' => isset($product['depth']) ?  (float)$product['depth'] : 1,
        ];
    }

    public
    static function convertWeight($weight)
    {
        if (!$conversation_rate = Configuration::get(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE))
            $conversation_rate = 1;

        return (float)$weight * (float)$conversation_rate;
    }

    private function getIdcarrierFromIdMethod($id_method)
    {
        switch ($id_method) {
            case _DPDGEOPOST_CLASSIC_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_ID);

            case _DPDGEOPOST_CLASSIC_1_PARCEL_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_1_PARCEL_ID);

            case _DPDGEOPOST_LOCCO_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_LOCCO_ID);

            case _DPDGEOPOST_LOCCO_1_PARCEL_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_LOCCO_1_PARCEL_ID);

            case _DPDGEOPOST_CLASSIC_BALKAN_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_BALKAN_ID);

            case _DPDGEOPOST_CLASSIC_INTERNATIONAL_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_INTERNATIONAL_ID);

            case _DPDGEOPOST_CLASSIC_PALLET_ONE_ROMANIA_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_PALLET_ONE_ROMANIA_ID);

            case _DPDGEOPOST_CLASSIC_POLAND_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_CLASSIC_POLAND_ID);

            case _DPDGEOPOST_STANDARD_24_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_STANDARD_24_ID);

            case _DPDGEOPOST_FASTIUS_EXPRESS_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_FASTIUS_EXPRESS_ID);

            case _DPDGEOPOST_FASTIUS_EXPRESS_2H_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_FASTIUS_EXPRESS_2H_ID);

            case _DPDGEOPOST_PALLET_ONE_ROMANIA_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_PALLET_ONE_ROMANIA_ID);


            case _DPDGEOPOST_INTERNATIONAL_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_INTERNATIONAL_ID);

            case _DPDGEOPOST_REGIONAL_EXPRESS_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_REGIONAL_EXPRESS_ID);

            case _DPDGEOPOST_HUNGARY_ID_:
                return Configuration::get(DpdGeopostConfiguration::CARRIER_HUNGARY_ID);

            default:
                return false;
        }
    }

    public
    static function getShipmentData($id_order)
    {
        return Db::getInstance()->getRow(
            '
			SELECT `id_order`, `id_shipment`, `shipment_reference`, `id_manifest`, `date_pickup`, `label_printed`
			FROM `' . _DB_PREFIX_ . _DPDGEOPOST_SHIPMENT_DB_ . '`
			WHERE `id_order`=' . (int)$id_order
        );
    }

    public
    static function getShipmentDataById($id_shipment)
    {
        return Db::getInstance()->getRow(
            '
			SELECT `id_order`, `id_shipment`, `shipment_reference`, `id_manifest`, `date_pickup`, `label_printed`
			FROM `' . _DB_PREFIX_ . _DPDGEOPOST_SHIPMENT_DB_ . '`
			WHERE `id_shipment`=' . (float)$id_shipment
        );
    }

    private
    static function sortParcelsByReferenceNumber($a, $b)
    {
        return $a['parcelReferenceNumber'] - $b['parcelReferenceNumber'];
    }

    public
    function isPickupArranged()
    {
        return (!$this->date_pickup || $this->date_pickup == '0000-00-00 00:00:00') ? false : true;
    }

    public
    function getShipmentList($order_by, $order_way, $filter, $start, $pagination)
    {
        $shipments = DB::getInstance()->executeS(
            '
			SELECT
				s.`id_shipment`								AS `id_shipment`,
				s.`id_order`								AS `id_order`,
				s.`id_manifest`								AS `manifest`,
				s.`label_printed`							AS `label`,
				s.`date_pickup` 							AS `date_pickup`,
				o.`date_add` 								AS `date_add`,
				1							AS `shipping_number`,
				CONCAT(a.`firstname`, " ", a.`lastname`) 	AS `customer`,

				(SELECT MAX(oh.`date_add`)
				 FROM `' . _DB_PREFIX_ . 'order_history` oh
				 WHERE oh.`id_order` = s.`id_order`
					AND oh.`id_order_state` = "' . pSQL(Configuration::get('PS_OS_SHIPPING')) . '")	AS `date_shipped`,

				(SELECT COUNT(od.`product_quantity`)
				 FROM `' . _DB_PREFIX_ . 'order_detail`			od
				 WHERE od.`id_order` = o.`id_order`)		AS `quantity`,

				(SELECT car.`name`
				 FROM `' . _DB_PREFIX_ . 'carrier` car
				 WHERE car.`id_carrier` = o.`id_carrier`)	AS `carrier`
			FROM `' . _DB_PREFIX_ . _DPDGEOPOST_SHIPMENT_DB_ . '` s
			LEFT JOIN `' . _DB_PREFIX_ . 'orders` 				o 	ON (o.`id_order` = s.`id_order`)
			LEFT JOIN `' . _DB_PREFIX_ . 'address` 				a 	ON (a.`id_address` = o.`id_address_delivery`)' . (version_compare(_PS_VERSION_, '1.5', '<') ? ' ' : 'WHERE o.`id_shop` = "' . (int)Context::getContext()->shop->id . '" ') .
            $filter . ($order_by && $order_way ? ' ORDER BY ' . pSQL($order_by) . ' ' . pSQL($order_way) : '') . ($start !== null && $pagination !== null ? ' LIMIT ' . (int)$start . ', ' . (int)$pagination : '')
        );

        if (!$shipments)
            $shipments = array();

        return $shipments;
    }

    /**
     * Get first matching CSV rule depending on given parameters
     *
     * @param (float) $total_weight - Current customer cart total products weight
     * @param (int) $id_method - Id one of described carriers IDs used in DpdGeopost module
     * @param (object) $cart - Current customer cart object
     * @param (bool) $is_cod_carrier - is current shipping method COD
     *
     * @return (array) first matching CSV rule
     */
    public
    static function getPriceRule($total_weight, $id_method, $id_address_delivery, $is_cod_carrier)
    {
        if (!$id_method)
            return false;

        $id_country = (int)Tools::getValue('id_country');
        if ($id_country)
            $country_iso_code = Country::getIsoById((int)$id_country);

        $id_state = (int)Tools::getValue('id_state');
        if ($id_state) {
            $state = new State((int)$id_state);
            $state_iso_code = $state->iso_code;
        }

        $postcode = (int)Tools::getValue('zipcode');
        $address = new Address($id_address_delivery);

        if (!isset($country_iso_code))
            $country_iso_code = Country::getIsoById((int)$address->id_country);

        if (!isset($state_iso_code)) {
            $state = new State((int)$address->id_state);
            $state_iso_code = $state->iso_code;
        }
        if (!$postcode)
            $postcode = $address->postcode;

        $price_rules = DB::getInstance()->executeS('
			SELECT `shipping_price`, `shipping_price_percentage`, `currency`, `cod_surcharge`, `cod_surcharge_percentage`, `cod_min_surcharge`
			FROM `' . _DB_PREFIX_ . _DPDGEOPOST_CSV_DB_ . '`
			WHERE `weight_from` <= ' . pSQL($total_weight) . '
				AND `weight_to` >= ' . pSQL($total_weight) . '
				AND (`country` = "' . pSQL($country_iso_code) . '" OR `country` = "*")
				AND (`region` = "' . pSQL($state_iso_code) . '" OR `region` = "*")
				AND (`zip` = "' . pSQL($postcode) . '" OR `zip` = "*")
				AND (`method_id` = "' . (int)$id_method . '" OR `method_id` = "*")
				AND `id_shop` = "' . (int)Context::getContext()->shop->id . '"
		');

        if (!$price_rules)
            return false;

        self::validateCurrencies($price_rules);

        if (!$price_rules)
            return false;

        self::validateCODRules($price_rules, $is_cod_carrier);

        if (!$price_rules)
            return false;

        return reset($price_rules);
    }

    private
    static function validateCODRules(&$price_rules, $is_cod_carrier)
    {
        foreach ($price_rules as $key => $price_rule)
            if (($price_rule['cod_surcharge'] !== '' || $price_rule['cod_surcharge_percentage'] !== '') && !$is_cod_carrier || ($price_rule['cod_surcharge'] === '' && $price_rule['cod_surcharge_percentage'] === '') && $is_cod_carrier
            )
                unset($price_rules[$key]);
    }

    private
    static function validateCurrencies(&$price_rules)
    {
        foreach ($price_rules as $key => $price_rule)
            if (!Currency::getIdByIsoCode($price_rule['currency']))
                unset($price_rules[$key]);
    }

    private
    static function getCarrierIdByReference($id_reference)
    {
        $id_carrier = Db::getInstance()->getValue('
			SELECT `id_carrier`
			FROM `' . _DB_PREFIX_ . 'carrier`
			WHERE id_reference = ' . (int)$id_reference . '
				AND deleted = 0 ORDER BY id_carrier DESC
		');

        if (!$id_carrier)
            return false;
        return (int)$id_carrier;
    }

    public
    static function getOrderIdByShipmentId($id_shipment)
    {
        if (!$id_shipment)
            return false;

        return DB::getInstance()->getValue('
			SELECT `id_order`
			FROM `' . _DB_PREFIX_ . _DPDGEOPOST_SHIPMENT_DB_ . '`
			WHERE `id_shipment` = "' . (float)$id_shipment . '"
		');
    }

    public
    function getTotalParcelsWeight()
    {

        if (!$this->parcels)
            return false;

        $total_weight = 0;

        foreach ($this->parcels as $parcel) {
            if (isset($parcel['weight'])) {
                $total_weight += (float)$parcel['weight'];
            }
        }

        return $total_weight;
    }

    private
    function setLabelPrinted($id_shipment)
    {
        return DB::getInstance()->Execute('
			UPDATE `' . _DB_PREFIX_ . _DPDGEOPOST_SHIPMENT_DB_ . '`
			SET `label_printed` = "1"
			WHERE `id_shipment` = "' . (float)$id_shipment . '"
		');
    }

    private
    function mapNewServiceIds($oldServiceId)
    {
        $services = [
            1 => 2505,
            25051 => 25051,
            25052 => 25052,
            40033 => 2303, //international
            40107 => 2212, //bulgaria
            40171 => 2212, //ungaria
            2205 => 2212, //greece,
            2212 => 2212
        ];

        return isset($services[$oldServiceId]) ? $services[$oldServiceId] : $oldServiceId;
    }

    public
    function getActiveVouchers($ids)
    {
        $request = array('shipmentIds' => $ids);

        $response = $this->wsrest_shipment_info($request);

        $parcelsWithVouchers = array();

        if (!empty($response) && isset($response['shipments'])) {
            foreach ($response['shipments'] as $shipment) {
                if (isset($shipment['service']) && isset($shipment['service']['additionalServices']) && isset($shipment['service']['additionalServices']['returns'])) {
                    if (isset($shipment['service']['additionalServices']['returns']['returnVoucher'])) {
                        $parcelsWithVouchers[] = $shipment['id'];
                    }
                }
            }
        }

        return $parcelsWithVouchers;
    }

    public function productVariantDimensions($products) {
        $variants = array();

        foreach ($products as $product) {
            $variants[ implode('_', array($product['product_id'], $product['product_attribute_id'] )) ] = array(
                //'weight' => floatval($product['weight']),
                'width' => floatval($product['width']),
                'height' => floatval($product['height']),
                'depth' => floatval($product['depth'])
            );
        }

        return $variants;
    }
}
