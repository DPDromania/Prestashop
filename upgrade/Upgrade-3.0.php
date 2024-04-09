<?php
if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_3_0()
{
    $result =  Db::getInstance()->getRow('SELECT COUNT(*) as Total FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'address" AND column_name="dpd_office_type";');
    if (isset($result['Total']) && $result['Total'] == 1) {
        return true;
    }

    Db::getInstance()->execute( 'ALTER TABLE `' . _DB_PREFIX_ . 'address` ADD COLUMN `dpd_office_type` VARCHAR(255) NULL DEFAULT NULL ' );
    Db::getInstance()->execute( 'ALTER TABLE `' . _DB_PREFIX_ . 'address` ADD COLUMN `dpd_office_name` VARCHAR(255) NULL DEFAULT NULL ' );

    return true;
}