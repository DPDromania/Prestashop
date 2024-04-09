<?php
if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_2_3($module)
{

    $result =  Db::getInstance()->getRow('SELECT COUNT(*) as Total FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'address" AND column_name="dpd_country";');
    if (isset($result['Total']) && $result['Total'] == 1) {
        return true;
    }

    Db::getInstance()->execute( 'ALTER TABLE `' . _DB_PREFIX_ . 'address` ADD COLUMN `dpd_country` VARCHAR(255) NULL DEFAULT NULL ' );
    Db::getInstance()->execute( 'ALTER TABLE `' . _DB_PREFIX_ . 'address` ADD COLUMN `dpd_state` VARCHAR(255) NULL DEFAULT NULL ' );
    Db::getInstance()->execute( 'ALTER TABLE `' . _DB_PREFIX_ . 'address` ADD COLUMN `dpd_site` VARCHAR(255) NULL DEFAULT NULL ' );
    Db::getInstance()->execute( 'ALTER TABLE `' . _DB_PREFIX_ . 'address` ADD COLUMN `dpd_street` VARCHAR(255) NULL DEFAULT NULL ' );
    Db::getInstance()->execute( 'ALTER TABLE `' . _DB_PREFIX_ . 'address` ADD COLUMN `dpd_complex` VARCHAR(255) NULL DEFAULT NULL ' );
    Db::getInstance()->execute( 'ALTER TABLE `' . _DB_PREFIX_ . 'address` ADD COLUMN `dpd_block` VARCHAR(255) NULL DEFAULT NULL ' );
    Db::getInstance()->execute( 'ALTER TABLE `' . _DB_PREFIX_ . 'address` ADD COLUMN `dpd_office` VARCHAR(255) NULL DEFAULT NULL ' );
    Db::getInstance()->execute( 'ALTER TABLE `' . _DB_PREFIX_ . 'address` ADD COLUMN `dpd_postcode` VARCHAR(255) NULL DEFAULT NULL ' );

    return true;
}