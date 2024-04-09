<?php
if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_1_1($module)
{
    return Db::getInstance()->execute(
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dpd_postcode_address` (
              `dpd_postcode_id` int(11) NOT NULL AUTO_INCREMENT,
              `id_address` int(11) NOT NULL,
              `hash` varchar(100) NULL,
              `auto_postcode` varchar(6) NULL,
              `relevance` int(2) NULL,
              `date_add` datetime DEFAULT NULL,
              `date_upd` datetime DEFAULT NULL,
              PRIMARY KEY (`dpd_postcode_id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');


}