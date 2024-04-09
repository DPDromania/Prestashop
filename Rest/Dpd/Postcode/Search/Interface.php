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
interface Rest_Dpd_Postcode_Search_Interface
{


    /**
     * @return boolean
     */
    public function installPostcodeDatabase();

    /**
     * @return boolean
     */
    public function updatePostcodeDatabase();


    /**
     * @param array $address
     *      $address contain next keys
     *      MANDATORY
     *      country
     *      city
     *
     * OPTIONAL
     *      region
     *      address
     *      street
     *      postcode - if postcode is provided then the function will validate it
     *                and return if it is valid for the input address
     *
     * @return string - postcode or null
     */
    public function search(array $address);


    /**
     * @param string $postcode
     * - perform a search in database to see if the postcode is valid
     *
     * @return mixed
     */
    public function isValid($postcode);


}