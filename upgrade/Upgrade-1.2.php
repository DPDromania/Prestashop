<?php
if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_1_2($module)
{
    Db::getInstance()->execute("DROP TRIGGER IF EXISTS dpd_trigger_update_address");

    Db::getInstance()->execute(
        '
            CREATE TRIGGER dpd_trigger_update_address
                AFTER UPDATE ON ' . _DB_PREFIX_ . 'address
                FOR EACH ROW
                       UPDATE ' . _DB_PREFIX_ . 'dpd_postcode_address SET ' . _DB_PREFIX_ . 'dpd_postcode_address.auto_postcode = NEW.postcode, ' . _DB_PREFIX_ . 'dpd_postcode_address.relevance = 1 WHERE ' . _DB_PREFIX_ . 'dpd_postcode_address.id_address = NEW.id_address AND ' . _DB_PREFIX_ . 'dpd_postcode_address.dpd_postcode_id > 0
    ');

    return true;
}