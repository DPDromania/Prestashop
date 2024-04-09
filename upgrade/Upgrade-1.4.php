<?php
if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_1_4($module)
{
    Db::getInstance()->execute(
        '
            ALTER TABLE `'._DB_PREFIX_.'configuration`
            CHANGE COLUMN `name` `name` VARCHAR(255) NOT NULL ;
    ');

    return true;
}