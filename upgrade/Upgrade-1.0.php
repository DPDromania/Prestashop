<?php
/**
 * Created by PhpStorm.
 * User: george.babarus
 * Date: 12/5/2014
 * Time: 4:36 PM
 */

function upgrade_module_1_0($module)
{
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Rest' . DIRECTORY_SEPARATOR . 'Dpd' . DIRECTORY_SEPARATOR . 'Postcode' . DIRECTORY_SEPARATOR . 'Search.php';
    $db = Db::getInstance();
    $dpdSearchModule = new Rest_Dpd_Postcode_Search('mysql',$db);

    $dpdSearchModule->installPostcodeDatabase();

    return true;
}