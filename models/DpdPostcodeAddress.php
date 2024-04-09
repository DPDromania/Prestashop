<?php

class DpdGeopostDpdPostcodeAddress extends ObjectModel
{
    /** @var integer address id which postcode cached data belongs to */
    public $dpd_postcode_id = null;

    public $id_address = null;


    /** @var string * */
    public $hash;

    /** @var string Company (optional) */
    public $auto_postcode;

    /** @var string is postcode relevant or not for the address */
    public $relevance;



    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'   => 'dpd_postcode_address',
        'primary' => 'dpd_postcode_id',
        'fields'  => array(
            'dpd_postcode_id'    => array('type' => self::TYPE_INT),
            'id_address'    => array('type' => self::TYPE_INT),
            'hash'          => array('type' => self::TYPE_STRING),
            'auto_postcode' => array('type' => self::TYPE_STRING),
            'hash'          => array('type' => self::TYPE_STRING),
            'relevance'     => array('type' => self::TYPE_STRING),
            'date_add'      => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
            'date_upd'      => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
        ),
    );


    /**
     * Build an address postcode
     *
     * @param integer $id_address Existing address id in order to load object (optional)
     */
    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id);
    }



    public function loadDpdAddressByAddressId($address_id)
    {
        $sql = 'SELECT * FROM `' . pSQL(_DB_PREFIX_ . $this->def['table']) . '`
								WHERE `' . bqSQL('id_address') . '` = ' . (int)$address_id;
        if ($object_datas_lang = Db::getInstance()->executeS($sql)) {
            foreach ($object_datas_lang as $row)
                foreach ($row as $key => $value) {
                    if (array_key_exists($key, $this)) {
                        $this->{$key} = $value;
                    }
                }
        }
        return $this;
    }
    /**
     * Returns fields required for an address in an array hash
     *
     * @return array hash values
     */
    public static function getFieldsValidate()
    {
        $tmp_addr = new DpdGeopostDpdPostcodeAddress();
        $out      = $tmp_addr->fieldsValidate;

        unset($tmp_addr);

        return $out;
    }
}
