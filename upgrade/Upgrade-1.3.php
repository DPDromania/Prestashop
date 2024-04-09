<?php
if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_1_3($module)
{


    $result =  Db::getInstance()->getRow('SELECT COUNT(*) as Total FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'dpdgeopost_shipment" AND column_name="shipment_reference";');
    if (isset($result['Total']) && $result['Total'] == 1) {
        return true;
    }

    Db::getInstance()->execute(
        '
        ALTER TABLE `'._DB_PREFIX_.'dpdgeopost_shipment`
            ADD COLUMN `shipment_reference` VARCHAR(45) NULL DEFAULT NULL AFTER `id_order`;

    ');

    return true;
}