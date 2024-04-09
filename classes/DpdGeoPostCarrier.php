<?php

class DpdGeoPostCarrier extends ObjectModel {

    public $id_dpdgeopost_carrier;
    public $id_carrier;
    public $id_reference;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     *
     */
    public static $definition = array(
        'table' => 'dpdgeopost_carrier',
        'primary' => 'id_dpdgeopost_carrier',
        'multilang' => false,
        'fields' => array(
            'id_dpd_geopost_carrier' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'id_carrier' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'id_reference' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false
            ),
        )
    );


    public static function getDpdGeoPostCarrierId($id_carrier)
    {

        $id_dpdgeopost_carrier = Db::getInstance()->getValue('
            SELECT `id_dpdgeopost_carrier`
            FROM `'._DB_PREFIX_.'dpdgeopost_carrier`
            WHERE `id_cart` = '.(int)$id_carrier
        );

        return new DpdGeoPostPickupPoint( (int) $id_dpdgeopost_carrier );
    }

}