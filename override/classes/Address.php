<?php
class Address extends AddressCore
{

    public $dpd_country;
    public $dpd_state;
    public $dpd_site;
    public $dpd_street;
    public $dpd_complex;
    public $dpd_block;
    public $dpd_office;
    public $dpd_office_type;
    public $dpd_office_name;
    public $dpd_postcode;

    public $dpd_shipment_type;

    public function __construct($id_address = null, $id_lang = null)
    {
        self::$definition['fields']['dpd_country'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false);
        self::$definition['fields']['dpd_state'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false);
        self::$definition['fields']['dpd_site'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false);
        self::$definition['fields']['dpd_street'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false);
        self::$definition['fields']['dpd_complex'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false);
        self::$definition['fields']['dpd_block'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false);
        self::$definition['fields']['dpd_office'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false);
        self::$definition['fields']['dpd_office_type'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false);
        self::$definition['fields']['dpd_office_name'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false);
        self::$definition['fields']['dpd_postcode'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false);
        self::$definition['fields']['dpd_shipment_type'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false);

        parent::__construct($id_address, $id_lang);
    }
}