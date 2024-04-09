<?php
if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_2_2($module)
{
    // Db::getInstance()->execute( 'ALTER TABLE `' . _DB_PREFIX_ . 'address` ADD `id_dpdoffice` VARCHAR(255) NULL DEFAULT NULL ' );

    return true;
}