<?php
/** **/

if (!defined('_PS_VERSION_'))
    exit;

require_once(_PS_MODULE_DIR_ . '/dpdgeopost/dpdgeopost.rest.php');

class DpdAddressSearch extends DpdGeopostWs
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function listCountries($options)
    {
        $countries_ws = $this->wsrest_location_country($options);


        $countries = array();
        if (isset($countries_ws['countries'])) {
            $countries = $countries_ws['countries'];
        }

        return $countries;
    }

    public function getCountryByIsoCode($isoCode)
    {
        $countries_ws = $this->wsrest_location_country(array(
            'isoAlpha2' => $isoCode
        ));


        $country = false;
        if (isset($countries_ws['countries']) && count( $countries_ws['countries']) > 0) {
            $country = $countries_ws['countries'][0];
        }

        return $country;
    }

    public function listCities($options)
    {

        if (!isset($options['countryId']) || empty(trim($options['countryId']))) {
            $options['countryId'] = '642';
        }

        $cities_ws = $this->wsrest_location_site($options);

        $cities = array();
        if (isset($cities_ws['sites']) && !empty($cities_ws['sites'])) {
            $cities = $cities_ws['sites'];
        }

        return $cities;
    }

    public function listStreets($options)
    {

        if (!isset($options['countryId']) || empty(trim($options['countryId']))) {
            $options['countryId'] = '642';
        }

        $streets_ws = $this->wsrest_location_street($options);

        $street = array();
        if (isset($streets_ws['streets'])) {
            $street = $streets_ws['streets'];
        }

        return $street;
    }

    public function listPostcodes($countryId = false)
    {

        if ($countryId) {
            $method_name_ws = 'wsrest_location_postcode_csv_' . $countryId;
        } else {
            $method_name_ws = 'wsrest_location_postcode_csv_642';
        }

        $postcodes_ws = $this->$method_name_ws(array('_raw' => true));

        $lines = explode("\n", $postcodes_ws);
        $postCodes = array();
        foreach ($lines as $line) {
            $chunks = explode(',', $line);
            $postCode = $chunks[0];
            $siteId = $chunks[1];

            if (!isset($postCodes[$siteId])) $postCodes[$siteId] = array();

            $siteId = trim($siteId);
            $postCodes[$siteId][] = $postCode;
        }

        return $postCodes;

    }

    public function listStatesByCountryIdAndStateName($countryId, $stateName)
    {
        $options = array(
            'countryId' => $countryId,
            'name' => $stateName
        );

        $states_ws = $this->wsrest_location_state($options);

        $states = array();
        if (isset($states_ws['states']) && !empty($states_ws['states'])) {
            $states = $states_ws['states'];
        }

        return $states;
    }

    public function validateAddress($addressId)
    {

        $dataToValidate = array();

        $address = new Address($addressId);
        $validationResponse = array();

        if ($address->dpd_shipment_type == 'pickup') {

            $dataToValidate['countryId'] = $address->dpd_country;
            if ($address->dpd_site) {
                $dataToValidate['siteId'] = $address->dpd_site;
            } else {
                $dataToValidate['siteName'] = $address->city;
            }

            if(empty($address->dpd_office)) {
                $validationResponse = array(
                    'valid' => false,
                    'error' => array(
                        'context' => 'office.not-provided',
                        'message' => 'Este necesar sa selectati un oficiu sau un automat'
                    )
                );
            } else {
                $validationResponse = array(
                    'valid' => true
                );
            }

         } else {
            $prestaCountry = new Country($address->id_country);
            $prestaCountryIsRoBg = in_array($prestaCountry->iso_code, array('RO', 'BG'));
            if (in_array($address->dpd_country, array(642, 100)) || $prestaCountryIsRoBg) {
                $dataToValidate['countryId'] = $address->dpd_country;

                if ($address->dpd_site) {
                    $dataToValidate['siteId'] = $address->dpd_site;
                } else {
                    $dataToValidate['siteName'] = $address->city;
                }

                if ($address->dpd_postcode) {
                    $dataToValidate['postCode'] = $address->dpd_postcode;
                }

                if ($address->dpd_street) {
                    $dataToValidate['streetId'] = $address->dpd_street;
                } else {
                    $dataToValidate['streetName'] = $address->address1;
                    $dataToValidate['streetType'] = '';
                }

                if ($address->dpd_block) {
                    list($strNo, $_temp1, $temp_2) = explode(':', $address->dpd_block);
                    if ($strNo) {
                        $dataToValidate['streetNo'] = $strNo;
                    } else {
                        $dataToValidate['streetNo'] = 1;
                    }

                }
            }
            $validationResponse = $this->wsrest_validation_address(array('address' => $dataToValidate));
            $validationResponse['debug'] = $dataToValidate;
        }

        return $validationResponse;
    }

    public function getStreetById($streetId)
    {
        $options = array();
        $findStreetUrl = 'wsrest_location_street_' . $streetId;
        $street_ws = $this->$findStreetUrl($options);


        if (isset($street_ws['street']) && !empty($street_ws['street'])) {
            return $street_ws['street'];
        }

        return array();
    }

    public function getCountryById($countryId) {
        $options = array();
        $findCountryUrl = 'wsrest_location_country_' . $countryId;
        $country_ws = $this->$findCountryUrl($options);

        if(isset($country_ws['country']) && !empty($country_ws['country'])) {
            return $country_ws['country'];
        }

        return array();
    }

    public function getSiteById($siteId) {
        $options = array();
        $findSiteUrl = 'wsrest_location_site_'.$siteId;
        $site_ws = $this->$findSiteUrl($options);

        if(isset($site_ws['site']) && !empty($site_ws['site'])) {
            return $site_ws['site'];
        }

        return array();
    }

    public function getStreetByName($streetName, $siteId)
    {
        $options = array(
            'siteId' => $siteId,
            'name' => $streetName
        );

        $streets_ws = $this->wsrest_location_street($options);

        $streets = array();
        if (isset($streets_ws['streets']) && !empty($streets_ws['streets'])) {
            $streets = $streets_ws['streets'];
        }

        return $streets;
    }

    function getOfficeById($officeId) {
        $methodName = 'wsrest_location_office_'. $officeId;

        $office_ws = $this->$methodName(array());

        if($office_ws['office'] && is_array($office_ws['office'])) {
            return $office_ws['office'];
        }
        return false;
    }

}
