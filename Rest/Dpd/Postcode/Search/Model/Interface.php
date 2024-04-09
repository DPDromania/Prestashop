<?php

/**
 * Rest_Dpd – shipping carrier extension - postcode validation
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Rest
 * @package    Rest_Dpd
 * @copyright  Copyright (c) 2019 Stimasoft SRL
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface Rest_Dpd_Postcode_Search_Model_Interface
{


    /**
     * here will be added the search logic for input address
     *
     * will return null if nothing found
     * input parameter is already sanitized
     *
     * @param $address
     *
     * @return mixed
     */
    public function search($address);


    /**
     * Fetches all SQL result rows as a sequential array.
     * Uses the current fetchMode for the adapter.
     *
     * @param string|Zend_Db_Select $sql       An SQL SELECT statement.
     * @param mixed                 $bind      Data to bind into SELECT placeholders.
     * @param mixed                 $fetchMode Override current fetch mode.
     *
     * @return array
     */
    public function query($sql, $bind = array());


    /**
     * Inserts a table row with specified data.
     *
     * @param array $bind Column-value pairs.
     *
     * @return int The number of affected rows.
     */
    public function insert($table, array $bind);


    function applyFiltersForAddress(&$data);


}