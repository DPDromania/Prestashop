<?php

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');

$module_instance = Module::getInstanceByName('dpdgeopost');
$filename = 'dpdgeopost.ajax';

if(Tools::isSubmit('codRemoved')) {

    if(!Module::isEnabled('dpdpayment')) {
        echo json_encode(array('status'=>'not_enabled'));
        exit();
    }

    $cart_id = Tools::getValue('cart');

    $cart = new Cart($cart_id);
    $address = new Address($cart->id_address_delivery);

    echo json_encode(array('status'=>'did something'));
    exit();
}

if(Tools::isSubmit('codChosen')) {

    if(!Module::isEnabled('dpdpayment')) {
        echo json_encode(array('status'=>'not_enabled'));
        exit();
    }


    $cart_id = Tools::getValue('cart');

    $cart = new Cart($cart_id);
    $address = new Address($cart->id_address_delivery);



    echo json_encode(array('status'=>'did something', 'cart'=>$cart));
    exit();
}

if(Tools::isSubmit('filterCity')) {
	$filterCity = Tools::getValue('filterCity');
	$filterCountry = Tools::getValue('filterCountry');

	$pudoService = new DpdGeopostPudo();
    $options = array();
	if($filterCity) {
        $options = $pudoService->listOfficesDropDownOptions($filterCity, $filterCountry);
    }


    $optionsHtml = '' ;
    $optionsHtml .="<option > ---- </option> ";

	if(empty($options)) {
        $optionsHtml ="<option > Nu am gasit oficii in orasul introdus </option> ";
    }

    foreach($options as $id => $value) {
        $optionsHtml .= "<option value='{$id}' > {$value} </option>";
    }


	echo json_encode(array('html' => $optionsHtml, 'empty'=>empty($options)));
	exit;
}

if(Tools::isSubmit('autocomplete') && Tools::isSubmit('type')  ) {

	$response = array();

	$searchType =  Tools::getValue('type');
	$searchValue = Tools::getValue('value');
	$siteId = Tools::getValue('siteid');
	$countryId = Tools::getValue('countryid');

	$addressService = new DpdAddressSearch();

	if($searchType == 'country') {
		$countries = $addressService->listCountries(array('name'=>$searchValue));
		foreach($countries as $country) {
			$response[] = array(
				'id' => $country['id'],
				'label' => $country['name'],
				'value' => $country['name'],
                'text' => $country['name'],
			);
		}
	}

	if($searchType == 'city') {
		$options = array();
		if($searchValue) {
			$options['name'] = $searchValue;
		}

		if($countryId) {
			$options['countryId'] = $countryId;
		}

		$stateName = Tools::getValue('state');

        if($stateName) {
            $options['region'] = trim($stateName);
        }
		$cities = $addressService->listCities($options);

		foreach($cities as $city) {
			$response[] = array(
				'id' => $city['id'],
				'label' => $city['name'] . " (" . $city['municipality'] . ', ' . $city['region'] . ')',
				'value' => $city['name'],
				'municipality' => $city['municipality'] . ', ' . $city['region'],
                'text' => $city['name'] . " (" . $city['municipality'] . ', ' . $city['region'] . ')',
                'postcode' => $city['postCode'],
			);
		}
	}

	if($searchType == 'street') {
		$streets = $addressService->listStreets(array('name'=> $searchValue, 'siteId' => $siteId));
		foreach($streets as $street) {
			$response[] = array(
				'id' => $street['id'],
				'label' => $street['type'] . ' ' . $street['name'],
                'text' => $street['type'] . ' ' . $street['name'],
				'value' => $street['type'] . ' ' . $street['name'],
				'type' => $street['type']
			);
		}
	}

	if($searchType == 'postcode') {
		$postcodeList = $addressService->listPostcodes(array());
		if(isset($postcodeList[$siteId])) {
			foreach($postcodeList[$siteId] as $postcode) {
				if(stripos($postcode, $searchValue) === 0) {
					$response[] = array(
						'id' => $postcode,
						'label' => $postcode,
                        'text' => $postcode,
						'value' => $postcode
					);
				}

			}
		}

	}


    if($searchType == 'office') {
	    $pudoService = new DpdGeopostPudo();
	    $offices = $pudoService->listOffices(false, false, $siteId, $countryId, $searchValue);

	    foreach ($offices as $office) {
	        $response[] = array(
	            'id' => $office['id'],
                'text' => $office['nameEn']
            );
        }
    }

    $responseForSelect2 = array(
        'results' => $response,
        'pagination' => array('more'=>false)
    );
	echo json_encode($responseForSelect2);
	exit;
}

if(Tools::isSubmit('autoselect') && Tools::isSubmit('field') && Tools::isSubmit('value') && Tools::isSubmit('address')) {
	$response = array();

	$addressField = Tools::getValue('field');
	$addressValue = Tools::getValue('value');
	$addressText  = Tools::getValue('label');
	$addressId = Tools::getValue('address');

	$address = new Address($addressId);
	switch($addressField) {
		case 'street':

			$address->dpd_street = $addressValue;

			break;
		case 'city':
			$address->dpd_site = $addressValue;
			break;
		case 'postcode':
			$address->dpd_postcode = $addressValue;
			$address->postcode = $addressValue;
		case 'extra':

			$address->dpd_block = $addressValue;

			break;
		case 'country':
			$address->dpd_country = $addressValue;
			break;
	}
	$address->update();

	echo json_encode($response);
	die();
}


if(false &&  Tools::isSubmit('updateDpdAddress') && Tools::isSubmit('address') ) {
	$response = array();

	$addressId = Tools::getValue('address');
	$dpd_site = Tools::getValue('dpd_site');
	$dpd_street = Tools::getValue('dpd_street');
	$dpd_extra = Tools::getValue('dpd_extra');

	$address = new Address($addressId);
	$address->dpd_site = $dpd_site;
	$address->dpd_street = $dpd_street;
	$address->dpd_block = $dpd_extra;

	$address->update();

	$addressCheck = new Address($addressId);


	$response = array(
		array($address->dpd_site, $address->dpd_street, $address->dpd_block),
		array($addressCheck->dpd_site, $addressCheck->dpd_street, $addressCheck->dpd_block),
	);

	echo json_encode($response);
	die();
}


if (!Tools::isSubmit('token') || (Tools::isSubmit('token')) && Tools::getValue('token') != sha1(_COOKIE_KEY_.$module_instance->name)) exit;



if (Tools::isSubmit('action') && Tools::getValue('action') == 'updateAddress') {

    $address_id = Tools::getValue('address_id', '');
    if(!$address_id) {
        echo json_encode(array('error' => 'Address could not be identified'));
    }

    $address = new Address($address_id);

    $originalCountry = $address->dpd_country;
    $originalSite = $address->dpd_site;

    $shipmentType = Tools::getValue('shipment_type', '');
    $isOfficeDelivery = (!empty($shipmentType) && $shipmentType == 'pickup');

    $address->dpd_country = (int)Tools::getValue('country_id', '');
    $address->dpd_state = Tools::getValue('state', '');
    $address->dpd_site = Tools::getValue('city_id', '');

    $locationChanged = ($originalCountry != $address->dpd_country) || ($originalSite != $address->dpd_site);

    $address->dpd_shipment_type = $shipmentType;

    if($isOfficeDelivery) {
        if($locationChanged) {
            $address->dpd_street = '';
            $address->dpd_block = '';
        }

        $office_id = Tools::getValue('office_id', '');
        if(!empty($office_id)) $address->dpd_office = $office_id;
    } else {
        if($locationChanged) {
            $address->dpd_office = '';
        }

        $street_id = Tools::getValue('street_id', '');
        if(!empty($street_id)) $address->dpd_street = $street_id;

        $postcode = Tools::getValue('postcode', '');
        if(!empty($postcode)) $address->dpd_postcode = $postcode;

        $block = Tools::getValue('block', '');
        $nblock = str_ireplace(':', '', $block);
        if(!empty($nblock)) {
            $address2 = '';
            list($nr, $bl, $ap) = explode(':', $block);
            if(!empty($bl)) $address2 .= ' bl. '.$bl;
            if(!empty($ap)) $address2 .= ' bl. '.$ap;
            $address->address2 = $address2;
            $address->dpd_block = $block;
        }
    }

    // unknown reason, shouldn't be possible since dpd_street has only numeric ids in it
    if(false && $address->dpd_street && !is_numeric($address->dpd_street)) {
        // strada nenormalizata, probabil introdusa custom, gen 'str. principala'

        $address->address1 = $address->dpd_street;
        $address->dpd_street = '';
    }

    $address->update();

    $addressService = new DpdAddressSearch();

    $countryInWs = $addressService->getCountryById($address->dpd_country);
    if(!empty($countryInWs)) {
        $countryFound = Country::getByIso($countryInWs['isoAlpha3']);
        if($countryFound) {
            $address->id_country = $countryFound;
            $address->update();
        }
    }

    $siteInWs = $addressService->getSiteById($address->dpd_site);
    if(!empty($siteInWs)) {

        // replace the city name
        $address->city = $siteInWs['nameEn'];

        // replace the state/county name

        $stateIdFound = State::getIdByName($siteInWs['regionEn']);
        if($stateIdFound) {
            $address->id_state = $stateIdFound;
        }
        $address->save();
    }

    $validationResponse = $addressService->validateAddress($address_id);
    if(isset($validationResponse['valid']) && $validationResponse['valid'] === true && !$isOfficeDelivery) {
        $newAddress1 = Tools::getValue('street_name', '');
        // if the name of the new address is not at all in the older address1, then replace address1
        if(!empty($newAddress1) && stripos($address->address1, $newAddress1) === false) {
            list($nr, $bl, $ap) = explode(':', $address->dpd_block);
            if(!empty($nr)) {
                $newAddress1 .= ' nr. ' .$nr;
            }

            $address->address1 = $newAddress1;
            $address->update();
        }

    }

    echo json_encode($validationResponse);
    die();

}

if (Tools::isSubmit('action') && Tools::getValue('action') == 'validateAddress') {
    $address_id = Tools::getValue('address_id', '');
    if(!$address_id) {
        echo json_encode(array('error' => 'Address could not be identified'));
    }

    $address = new Address($address_id);
    $addressService = new DpdAddressSearch();
    $validationResponse = $addressService->validateAddress($address_id);
    echo json_encode($validationResponse);
    die();
}


if (Tools::isSubmit('action') && Tools::getValue('action') == 'validateZipCode') {
    $country_id = Tools::getValue('country_id', '');
    $zip_code = Tools::getValue('zip_code', '');

    if(!$country_id || ! $zip_code) {
        die(json_encode(array('error' => 'Country or zipcpde could not be identified')));
    }


    require_once(_DPDGEOPOST_MODELS_DIR_ . 'DpdPostalCode.php');
    $postalCodeService = new DpdPostalCode();
    $validationResponse = $postalCodeService->validateZipCode($country_id, $zip_code);
    echo json_encode($validationResponse);
    die();
}

if (Tools::isSubmit('action') && Tools::getValue('action') == 'update_locker') {

    header('Content-Type: application/json');
    $cart_id = Tools::getValue('cart_id');
    $delivery_address_id = Tools::getValue('delivery_address_id');
    $address = new Address($delivery_address_id);
    $shipment = new DpdGeopostShipment;
    $id_method = _DPDGEOPOST_LOCKER_ID_;
    $extra_params = [];




    if (empty($address->id)) {
        die(json_encode(['error' => 'Could not find delivery address by id:' . $delivery_address_id]));
    }

    $dpd_office_id = Tools::getValue('dpd_office_id');
    $dpd_office_type = Tools::getValue('dpd_office_type');
    $dpd_office_name = Tools::getValue('dpd_office_name');
    if (empty($dpd_office_id)) {
        die(json_encode(['error' => 'Empty dpd office id:']));
    }
    $extra_params['dpd_office_id'] = $dpd_office_id;
    $cart = new Cart($cart_id);
    $cart_products =  $cart->getProducts();
    $parcels = $shipment->putProductsToParcels($cart_products);

    $result = $shipment->calculate($id_method, $delivery_address_id, $parcels, null, $extra_params);

    if (isset($result['price'])) {
        try {
            $address->dpd_office = $dpd_office_id;
            $address->dpd_shipment_type = 'pickup';
            $address->dpd_office_type = $dpd_office_type;
            $address->dpd_office_name = $dpd_office_name;
            $address->update();

            //$cart->update();

            die(json_encode(['message' => 'Address updated.','price' => $result['price'], 'cart' => $cart, 'shipping' => $cart->getCarrierCost($id_method)]));
        } catch (Throwable $ex) {
            die(json_encode(['error' => $ex->getMessage()]));
        }
    } else {
            die(json_encode(['error' => $shipment::$errors]));
    }
}
if(Tools::isSubmit('action') && Tools::getValue('action') == 'admin_change_address') {

    $addressService = new DpdAddressSearch();
    $address_id_in_db = Tools::getValue('address_id');
    $country_id_in_db = Tools::getValue('country_id');
    $state_id_in_db = Tools::getValue('state_id');
    $city_text = Tools::getValue('city');
    $street_text = Tools::getValue('street');

    $country_id_in_ws = Tools::getValue('country_id_ws');
    if($country_id_in_ws == 'false') $country_id_in_ws = false;
    $city_id_in_ws = Tools::getValue('city_id_ws');
    if($city_id_in_ws == 'false') $city_id_in_ws = false;
    $street_id_in_ws = Tools::getValue('street_id_ws');
    if($street_id_in_ws == 'false') $street_id_in_ws = false;

    $response = array(
        'message' => false,
        'continue' => false,
        'context' => 'initial',
        'options' => array(),
        'data' => array(
            'country_id' => $country_id_in_ws,
            'city_id' => $city_id_in_ws,
            'street_id' => $street_id_in_ws
        )
    );


    if($response['data']['country_id'] === false) {

        if($country_id_in_db) {
            $countryObjectInDb = new Country($country_id_in_db);
        } else {
            $response['message'] = 'You must select a country';
            $response['continue'] = true;
            echo json_encode($response);
            exit();
        }

        $countryInWs = $addressService->getCountryByIsoCode($countryObjectInDb->iso_code);

        if(!$countryInWs) {
            $response['message'] = 'Can not check country';
            $response['continue'] = false;
            echo json_encode($response);
            exit();
        }

        $response['data']['country_id'] = $countryInWs['id'];
    }


    if($state_id_in_db) {
        $stateObjectInDb = new State($state_id_in_db);
    } else {
        $response['message'] = 'You must select a state';
        $response['continue'] = true;
        echo json_encode($response);
        exit();
    }


    if($response['data']['city_id'] == false ) {
        $cityInWs = $addressService->listCities(array(
            'countryId' => $response['data']['country_id'],
            'region' => $stateObjectInDb->name,
            'name' => $city_text
        ));


        if($cityInWs === false) {
            $response['message'] = 'Can not find location';
            $response['continue'] = true;
            echo json_encode($response);
            exit();
        } else {
            if(count($cityInWs) >= 2) {
                $response['message'] = 'We found ' . count($cityInWs) . ' locations with this name. Please confirm the proper one and recheck again.' ;
                $response['options']['city'] = $cityInWs;
                echo json_encode($response);
                exit();
            }
        }


        $response['data']['city_id'] = $cityInWs[0]['id'];
    }


    if($response['data']['street_id'] == false) {

        $streetInWs = $addressService->listStreets(array(
            'countryId' => $response['data']['country_id'],
            'siteId' => $response['data']['city_id'],
            'name' => $street_text
        ));

        $streetsInLocation = $addressService->listStreets(array(
            'countryId' => $response['data']['country_id'],
            'siteId' => $response['data']['city_id']
        ));


        if($streetInWs == false) {
            $suffix = ' Make sure to keep only the street name in the Address 1 field. Street Number and any extra info should be in Address 2 field.';
            if(count($streetsInLocation) >= 2) {
                $response['message'] = 'No street with this name found. ' . $suffix;
                echo json_encode($response);
                exit();
            }

            if(count($streetsInLocation) == 0) {
                $response['message'] = 'Address is not defined with DPD, normalization will be done only to location level. ' . $suffix;
            }

            if(count($streetsInLocation) == 1) {
                $response['message'] = 'Address set to main street of this location.';
                $response['data']['street_id'] = $streetInWs[0]['id'];
            }

        } elseif (count($streetInWs) == 1) {
            $response['message'] = 'Address set and checked.';
            $response['data']['street_id'] = $streetInWs[0]['id'];
        } elseif(count($streetInWs) >= 2) {
            $response['message'] = 'Found ' . count($streetInWs)  . ' related streets. Please confirm the proper one and recheck again.';
            $response['options']['street'] = $streetInWs;
            echo json_encode($response);
            exit();
        }

    }

    $addressObjectInDb = new Address($address_id_in_db);
    if(isset($response['data']['city_id'])) {
        $addressObjectInDb->dpd_site = $response['data']['city_id'];
        $addressObjectInDb->update(true);
    }

    if(isset($response['data']['street_id'])) {
        $addressObjectInDb->dpd_street = $response['data']['street_id'];
        $addressObjectInDb->update(true);
    }

    $response['finish'] = true;
    $response['message'] = 'Address is set and checked with DPD.';
    echo json_encode($response);
    exit();
}

if (Tools::isSubmit('testConnectivity'))
{
	require_once(_DPDGEOPOST_CLASSES_DIR_.'configuration.controller.php');
	$configuration_controller = new DpdGeopostConfigurationController();
	$error_message = $configuration_controller->testConnectivity();
	die($error_message ? $error_message : true);
}

if (Tools::isSubmit('calculatePrice'))
{
	$shipment = new DpdGeopostShipment((int)Tools::getValue('id_order'));
	$price = $shipment->calculatePriceForOrder((int)Tools::getValue('method_id'), (int)Tools::getValue('id_address'));

	die(json_encode(array(
		'price'  => empty($price) ? '---' : $price,
		'error'  => reset(DpdGeopostShipment::$errors),
		'notice' => reset(DpdGeopostShipment::$notices)
	)));
}

if (Tools::isSubmit('saveShipment'))
{


	$shipment = new DpdGeopostShipment((int)Tools::getValue('id_order'));

	$extraOptions = array(
		'swap_enabled'    => Tools::getValue('swap_enabled'),
		'rod_enabled'     => Tools::getValue('rod_enabled'),
		'voucher_enabled' => Tools::getValue('voucher_enabled'),
		'shipment_note'   => Tools::getValue('shipment_note'),
        'shipment_reference' => Tools::getValue('shipment_reference'),
        'reusable_enabled' => Tools::getValue('reusable_enabled')
	);


	$message = $shipment->save((int)Tools::getValue('method_id'), (int)Tools::getValue('id_address'), Tools::getValue('parcels'), $extraOptions);

    if ($message && !DpdGeopostShipment::$errors) {
        $module_instance->addFlashMessage($message);
    }

	$error_messages = '';
	if (DpdGeopostShipment::$errors) {
        foreach (DpdGeopostShipment::$errors as $error)
            $error_messages .= $error . '<br />';
    }
	die(json_encode(array(
		'error' => $message && !$error_messages ? null : $error_messages
	)));
}

if (Tools::isSubmit('deleteShipment'))
{
	$shipment = new DpdGeopostShipment((int)Tools::getValue('id_order'));

	if ($result = $shipment->delete())
		$module_instance->addFlashMessage($module_instance->l('Shipment successfully deleted', $filename));

	die(json_encode(array(
		'error' => $result ? null : reset(DpdGeopostShipment::$errors)
	)));
}

if (Tools::isSubmit('arrangePickup'))
{
	$pickup_data = Tools::getValue('dpdgeopost_pickup_data');

	$pickup = new DpdGeopostPickup;
	$pickup->id_shipment = Tools::getValue('shipmentIds');
	$pickup->date = isset($pickup_data['date']) ? pSQL($pickup_data['date']) : null;
	$pickup->fromTime = isset($pickup_data['fromTime']) ? pSQL($pickup_data['fromTime']) : null;
	$pickup->toTime = isset($pickup_data['toTime']) ? pSQL($pickup_data['toTime']) : null;
	$pickup->contactEmail = isset($pickup_data['contactEmail']) ? pSQL($pickup_data['contactEmail']) : null;
	$pickup->contactName = isset($pickup_data['contactName']) ? pSQL($pickup_data['contactName']) : null;
	$pickup->contactPhone = isset($pickup_data['contactPhone']) ? pSQL($pickup_data['contactPhone']) : null;
	$pickup->specialInstruction = isset($pickup_data['specialInstruction']) ? pSQL($pickup_data['specialInstruction']) : null;
	$pickup->referenceNumber = Tools::passwdGen();

	if ($result = $pickup->arrange())
		$module_instance->addFlashMessage(
			sprintf($module_instance->l('Pickup successfully arranged at %s %s - %s', $filename), $pickup->date, $pickup->fromTime, $pickup->toTime)
		);

	die(json_encode(array(
		'error' => $result ? null : reset(DpdGeopostShipment::$errors)
	)));
}

if (Tools::isSubmit('downloadModuleCSVSettings'))
{
	include_once(dirname(__FILE__).'/classes/csv.controller.php');
	$controller = new DpdGeopostCSVController;
	$controller->generateCSV();
}


if(Tools::isSubmit('submitAddaddress') || Tools::getValue('action') === 'postcode-recommendation' ){
    $data = array(
    );

    $lang_id = (int)$context->language->id;
    $address = array(
        'city' => Tools::getValue('city'),
        'country_id' => Tools::getValue('id_country'),
        'region_id' => Tools::getValue('id_state'),
        'lang_id' => $lang_id,
        'address' => Tools::getValue('address1').' '.Tools::getValue('address2')
    );

    $postcodeSearch = new DpdGeopostPostcodeSearch();
	$postcode = $postcodeSearch->search($address);
	if($postcode !== null && $postcode !== false) {
		$data = array(
			array(
				'label' => $postcode,
				'postcode' => $postcode,
			)
		);
	} else {
    	$results = $postcodeSearch->findAllSimilarAddressesForAddress($address);
		foreach ($results as $address){
			$data[] = array(
					'label'     =>$address['postcode'] . ' - ' .  ( $address['address'] ? $address['address'] . ', ': '' )  .  $address['city'] . ', ' . $address['region'],
					'postcode'  => $address['postcode']
			);
		}
	}
    echo json_encode($data);
    exit;
}
