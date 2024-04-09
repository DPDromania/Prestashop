<?php
require_once(_PS_MODULE_DIR_ . '/dpdgeopost/dpdgeopost.rest.php');

class DpdPostalCode extends DpdGeopostWs
{
    public function validateZipCode($countryId, $zipCode)
    {
        $options['countryId'] = 642;
        $options['postCode'] = $zipCode;
        $response =  $this->wsrest_validation_postcode($options);
        return $response;
    }
}