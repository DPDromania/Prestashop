<?php
if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_2_1($module)
{
    Db::getInstance()->execute(
        '
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'dpdgeopost_carrier_cart` (
                `id_dpdgeopost_carrier_cart` int(11) NOT NULL AUTO_INCREMENT,
                `id_cart` int(11) NOT NULL,
                `pickup_point` text NOT NULL,
                `date_add` datetime NOT NULL,
                PRIMARY KEY (`id_dpdgeopost_carrier_cart`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        '
    );


    return true;
}