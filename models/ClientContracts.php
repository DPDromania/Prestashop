<?php
/** **/

if (!defined('_PS_VERSION_'))
    exit;

require_once(_PS_MODULE_DIR_ . '/dpdgeopost/dpdgeopost.rest.php');

class ClientContracts extends DpdGeopostWs
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

    public function listContracts()
    {
        $contracts_ws =  $this->wsrest_client_contract(array());
        $contracts = array();
        if(isset($contracts_ws['clients'])) {
            $contracts = $contracts_ws['clients'];
        }

        return $contracts;
    }

    public function listOfficesDropDownOptions($cityFilter = false) {
        $offices = $this->listOffices();
        $options = array();

        foreach($offices as $office) {
            $options[$office['id']] = $office['name'];
        }

        return $options;
    }

    public function availableServices() {
        $services_ws = $this->wsrest_services(array());

        if(isset($services_ws['services']) && !empty($services_ws['services'])) {
            $servicesById = array();
            foreach ($services_ws['services'] as $service) {
                $servicesById[$service['id']] = $service;
            }

            return $servicesById;
        }

        return null;
    }
}
