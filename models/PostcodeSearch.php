<?php

class DpdGeopostPostcodeSearch
{


    protected static $_instance = null;


    public static function getInstance()
    {
        if (!class_exists('Rest_Dpd_Postcode_Search')) {
            require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Rest' . DIRECTORY_SEPARATOR . 'Dpd' . DIRECTORY_SEPARATOR . 'Postcode' . DIRECTORY_SEPARATOR . 'Search.php';
            $db              = Db::getInstance();
            self::$_instance = new Rest_Dpd_Postcode_Search('mysql', $db);
        }

        return self::$_instance;

    }


    public function getSearchPostcodeModel()
    {
        return self::getInstance();
    }

    /**
     * process a search on postcode table store it to address entity
     * at the end return it for the api call
     *
     * @param $addressObject
     *
     * @return string
     * @throws PrestaShopException
     */
    public function extractPostCodeForShippingRequest($addressObject)
    {
        $idState     = $addressObject->id_state;
        $countryName = pSQL($addressObject->country);

        $regionName = State::getNameById($idState);

        $address = array(
            'country'  => $countryName,
            'region'   => $regionName,
            'city'     => pSQL($addressObject->city),
            'address'  => pSQL($addressObject->address1) . (($addressObject->address2) ? ' ' . pSQL($addressObject->address2) : ''),
            'postcode' => pSQL($addressObject->postcode)
        );


        if (false && $this->isEnabledAutocompleteForPostcode($countryName)) {
            $dpdPostcodeAddress = new DpdGeopostDpdPostcodeAddress();
            $dpdPostcodeAddress->loadDpdAddressByAddressId($addressObject->id);
            $currentHash = $this->generateAddressHash($address);

            if (
                !empty($dpdPostcodeAddress->id_address) &&
                $currentHash == $dpdPostcodeAddress->hash
            ) {
                return $dpdPostcodeAddress->auto_postcode;
            }

            if (
                empty($dpdPostcodeAddress->id_address) ||
                $currentHash != $dpdPostcodeAddress->hash
            ) {
                $postcodeRelevance = new stdClass();
                $postCode          = $this->search($address, $postcodeRelevance);

                $dpdPostcodeAddress->auto_postcode = $postCode;
                $dpdPostcodeAddress->id_address = $addressObject->id;

                $dpdPostcodeAddress->hash       = $currentHash;
                if ($this->isValid($postCode, $postcodeRelevance)) {
                    $dpdPostcodeAddress->relevance  = 1;

                    $addressObject->postcode = $postCode;
                    $addressObject->save();

                } else {
                    $dpdPostcodeAddress->relevance = 0;
                }

                if(!empty($dpdPostcodeAddress->dpd_postcode_id)){
                    $dpdPostcodeAddress->id = $dpdPostcodeAddress->dpd_postcode_id;
                }
                $dpdPostcodeAddress->save();
            } else {
                return $dpdPostcodeAddress->auto_postcode;
            }


        } else {
            $postCode = $addressObject->postcode;
        }

        return $postCode;
    }

    /**
     * this hash will be used for trigger the postcode expiration
     *
     * @param $address
     *
     * @return string
     */
    protected function generateAddressHash($address)
    {
        if (!is_array($address)) {
            return '';
        }
        unset($address['postcode']);
        $hash = implode('', $address);

        return md5($hash);
    }

    /**
     * it is used to create a list of relevant addresses for given address.
     * used in admin panel to validate the postcode
     *
     * @param array $address The content will be the edit form for address from admin
     *                       $address contain next keys
     *                       MANDATORY
     *                       country
     *                       city
     *
     * OPTIONAL
     *      region
     *      address
     *      street
     */
    public function findAllSimilarAddressesForAddress($address)
    {
        if (!empty($address['country_id'])) {
            $countryObj = new Country();
            $countryName = $countryObj->getNameById($address['lang_id'], $address['country_id']);

            $address['country'] = $countryName;
        }

        if ($this->isEnabledAutocompleteForPostcode($countryName)) {
            if ($address['region_id']) {
                $regionName        = State::getNameById($address['region_id']);
                $address['region'] = $regionName;
            }
            if(empty($address['region'])){
                $regions = $this->getSearchPostcodeModel()->identifyRegionByCity($address['city']);
                if($regions && count($regions)==1) {
                    $address['region'] = array_pop($regions);
                }
            }

            $foundAddresses = $this->getSearchPostcodeModel()->searchSimilarAddresses($address);

            return $foundAddresses;
        }

        return false;
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
     * @param null  $postcodeRelevance
     *
     * @return string
     */
    public function search($address, $postcodeRelevance = null)
    {
        $foundPostCode = $this->getSearchPostcodeModel()->search($address, $postcodeRelevance);
        if (isset($address['postcode']) && strlen($address['postcode']) > 4) {
            if ($foundPostCode == $address['postcode']) {
                return $foundPostCode;
            } elseif (!empty($foundPostCode)) {
                //mark the response as not exactly the same
                return $foundPostCode;
            }

            return $address['postcode'];
        }

        return $foundPostCode;
    }

    /**
     * test if found postcode relevance is enough for considering the postcode useful in the rest of checkout process
     *
     * @param          $postCode
     * @param stdClass $relevance
     *
     * @return int
     */
    public function isValid($postCode, stdClass $relevance = null)
    {
        if (empty($relevance)) {
            return 0;
        }
        if (!empty($relevance->percent) && $relevance->percent > Rest_Dpd_Postcode_Search::SEARCH_RESULT_RELEVANCE_THRESHOLD_FOR_VALIDATION) {
            return 1;
        }

        return 0;
    }


    public function isEnabledAutocompleteForPostcode($countryName)
    {
        $isValid = $this->getSearchPostcodeModel()->isEnabled($countryName);
        if (empty($isValid)) {
            return false;
        }

        $value = 1;//Mage::getStoreConfig('carriers/restDpd/postcode_autocomplete_checkout');

        return !empty($value);
    }




    /**
     * return the path do database files CSV
     *
     * @return string
     */
    public function getPathToDatabaseUpgradeFiles(){
        return _PS_UPLOAD_DIR_ . 'dpd' . DIRECTORY_SEPARATOR . 'postcode_update' . DIRECTORY_SEPARATOR;
    }


    /**
     *
     * call the library function for postcode update
     *
     * @param $fileName
     *
     * @return bool
     * @throws Exception
     */
    public function updateDatabase($fileName){
        $result = $this->getSearchPostcodeModel()->updateDatabase($fileName);
        if(empty($result)){
            throw new Exception('An error occurred while updating postcode database. Please run again the import script. (A database backup is always created in rest_dpd_postcodes_backup table.)');
        }
        return true;
    }



}