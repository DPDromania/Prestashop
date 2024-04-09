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

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Search' . DIRECTORY_SEPARATOR . 'Abstract.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Search' . DIRECTORY_SEPARATOR . 'Interface.php';


class Rest_Dpd_Postcode_Search extends Rest_Dpd_Postcode_Search_Abstract implements Rest_Dpd_Postcode_Search_Interface
{

    public function __construct($_searchModel = null, $connection = null)
    {
        $this->_searchModel = $_searchModel;
        $this->_connection  = $connection;
        $this->_init();
    }


    public function _init()
    {
        switch ($this->_searchModel) {
            case self::MYSQL_ADAPTER : {
                $this->_initMysqlModel();
                break;
            }
            default: {
            $this->_initDefaultModel();
            }
        }
    }


    public function _initMysqlModel()
    {
        if (empty($this->_connection)) {
            throw new Exception('Rest DPD Postcode - database connection missing');
        }
        $this->_searchModel = new Rest_Dpd_Postcode_Search_Model_Mysql($this->_connection);

        return true;
    }

    public function _initDefaultModel()
    {
        $this->_initMysqlModel();

        return true;
    }


    /**
     * @return boolean
     */
    public function installPostcodeDatabase()
    {
        $this->_searchModel->install();

        return true;
    }


    /**
     * @return boolean
     */
    public function uninstallPostcodeDatabase()
    {
        $this->_searchModel->uninstall();

        return true;
    }


    /**
     * @return boolean
     */
    public function updatePostcodeDatabase()
    {
        return true;
    }


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
     *
     * @return string - postcode or null
     */
    public function search(array $address, stdClass $relevance = null)
    {
        $address = $this->filterAddressInput($address);

        $postcode = $this->_searchModel->search($address, $relevance);
        if (!empty($postcode)) {
            return $postcode;
        }

        return null;
    }

    public function updateDatabase($file){
        if (!file_exists($file)){
            return false;
        }

        return $this->_searchModel->updateDatabase($file);

    }

    /**
     * return the database to the last known stable state
     * @param $file
     *
     * @return mixed
     */
    public function rollbackDatabase(){
        return $this->_searchModel->rollbackDatabase();
    }


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
     *
     * @return string - postcode or null
     */
    public function searchSimilarAddresses(array $address)
    {
        $address = $this->filterAddressInput($address);

        $address = $this->_searchModel->searchSimilarAddresses($address);

        return $address;
    }


    public function getAddressByPostcode($postcode)
    {
        return $this->_searchModel->getAddressByPostcode($postcode);
    }


    public function processCitySimilarity($city)
    {
        return $this->_searchModel->processCitySimilarity($city);
    }


    public function identifyRegionByCity($city)
    {
        return $this->_searchModel->identifyRegionByCity($city);
    }

    /**
     * @param string $postcode
     * - perform a search in database to see if the postcode is valid
     *
     * @return mixed
     */
    public function isValid($postcode)
    {


    }

    public function isEnabled($country)
    {
        $country = $this->applyInputFilter(0, $country);
        if ($country == 'romania') {
            return true;
        }

        return false;
    }


}


Rest_Dpd_Postcode_Search::autoloadRegister();