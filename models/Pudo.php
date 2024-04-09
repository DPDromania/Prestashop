<?php
/** **/

if (!defined('_PS_VERSION_'))
    exit;

require_once(_PS_MODULE_DIR_ . '/dpdgeopost/dpdgeopost.rest.php');

class DpdGeopostPudo extends DpdGeopostWs
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

    public function listOffices($cityFilter = false, $countryFilter = false, $siteId = false, $countryId = false, $officeName = false)
    {

        $options = array();
        if($cityFilter) {
            $options['name'] = $cityFilter;
        }

        if($siteId) {
            $options['siteId'] = $siteId;
        }


        if($countryFilter) {
            $country = new Country($countryFilter);
            $addressWs = new DpdAddressSearch();
            $country = $addressWs->getCountryByIsoCode($country->iso_code);
            if(!empty($country)) {
                $options['countryId'] = $country['id'];
            }
        }

        if($countryId) {
            $options['countryId'] = $countryId;
        }

        if($officeName) {
            $options['name'] = $officeName;
        }



        $offices_ws =  $this->wsrest_location_office($options);

        $offices = array();
        if(isset($offices_ws['offices'])) {
            $offices = $offices_ws['offices'];
        }

        return $offices;
    }

    public function listSites($city, $countryId)
    {
        $options = [
            'name' => $city,
            'countryId' => $countryId
        ];
        return $this->wsrest_location_site($options);
    }

    public function listOfficesDropDownOptions($cityFilter = false, $countryFilter = false) {
        $offices = $this->listOffices($cityFilter, $countryFilter);
        $options = array();

        foreach($offices as $office) {
            if (isset($office['address']['streetName'])) {
                $options[$office['id']] = $office['nameEn'] . ' (' . $office['address']['streetName'] . ')';
            } else {
                $options[$office['id']] = $office['nameEn'];
            }
        }

        return $options;
    }



    public function getOfficeById($officeId) {
        $location_method = 'wsrest_location_office_'.$officeId ;

        $office = $this->$location_method(array());

        return $office;
    }
}
