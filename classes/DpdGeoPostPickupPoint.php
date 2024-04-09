<?php

class DpdGeoPostPickupPoint extends ObjectModel {

    public $id_dpdgeopost_carrier_cart;
    public $id_cart;
    public $pickup_point;
    public $date_add;

    /**
     * @see ObjectModel::$definition
     *
     */
    public static $definition = array(
        'table' => 'dpdgeopost_carrier_cart',
        'primary' => 'id_dpdgeopost_carrier_cart',
        'multilang' => false,
        'fields' => array(
            'id_cart' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'pickup_point' => array(
                'type' => self::TYPE_STRING
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false
            )
        )
    );


    public static function getPickupPointByCartId($id_cart)
    {

        $id_dpdgeopost_carrier_cart = Db::getInstance()->getValue('
            SELECT `id_dpdgeopost_carrier_cart`
            FROM `'._DB_PREFIX_.'dpdgeopost_carrier_cart`
            WHERE `id_cart` = '.(int)$id_cart
        );

        return new DpdGeoPostPickupPoint( (int) $id_dpdgeopost_carrier_cart );
    }

}