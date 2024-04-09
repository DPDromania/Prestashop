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
class Rest_Dpd_Postcode_Search_Model_Mysql extends Rest_Dpd_Postcode_Search_Model implements Rest_Dpd_Postcode_Search_Model_Interface
{


    public function __construct($connection)
    {

        self::$_connection = $connection;

    }


    /**
     * here will be added the search similar addresses logic for input address
     *
     * will return null if nothing found
     * input parameter is already sanitized
     *
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
     * @return mixed
     */
    public function searchSimilarAddresses($address)
    {
        if (empty($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_CITY])) {
            return false;
        }

        if (empty($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_REGION])) {
            //this is disabled because some platforms may not collect region field
            //return false;
        }

        $changesMade = $this->processRegionAndCity($address);

        $results      = false;
        $strictSearch = $this->strictSearch($address);

        if ($strictSearch > 0) {
            $results = $this->strictSearch($address, false);

            return $results;
        }


        //try locate the city addresses
        $sql = 'SELECT * FROM `' . self::TABLE_NAME . '` WHERE `region` LIKE %s AND `city` LIKE %s  LIMIT ' . (Rest_Dpd_Postcode_Search::SEARCH_APPLY_SIMILARITY_MAX_THRESHOLD + 1) . ' ';
        $sql = sprintf($sql,
            $this->quote($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_REGION] . '%'),
            $this->quote($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_CITY] . '%')
        );

        $results = array();
        foreach ($this->query($sql) as $row) {
            $results[] = $row;
        }
        if (count($results) > 0) {
            return $results;
        }


        return $results;
    }

    /**
     *
     * @param $city
     *
     * @return array|bool
     */
    public function identifyRegionByCity($city)
    {
        //try locate the city addresses
        $sql = 'SELECT DISTINCT `region` FROM `' . self::TABLE_NAME . '` WHERE `city` = %s ';
        $sql = sprintf($sql,
            $this->quote($city)
        );

        $results = array();
        foreach ($this->query($sql) as $row) {
            $results[] = $row['region'];
        }
        if (count($results) > 0) {
            return $results;
        }

        return false;
    }


    /**
     * here will be added the search logic for input address
     *
     * will return null if nothing found
     * input parameter is already sanitized
     *
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
     * @return mixed
     */
    public function search($address, stdClass $relevance = null)
    {
        if (empty($relevance)) {
            $relevance = new stdClass();
        }
        $result = $this->tryStrictSearchAndReturn($address);

        if ($result !== false) {
            $relevance->percent = 100;

            return $result;
        }

        if ($this->processRegionAndCity($address)) {
            $result = $this->tryStrictSearchAndReturn($address);

            if ($result !== false) {
                $relevance->percent = 95;

                return $result;
            }
        }

        // the city and region is already checked by processRegionAndCity
        $result = $this->tryLocateAtLeastTheCity($address, $relevance);
        if ($result !== false) {
            return $this->returnPostalCode($result);
        }


        return null;
    }


    /**
     * @param $address
     *
     * if nothing found for city and address we have to check the city
     * if we have only one entry for this city then this one will be returned
     * else the similarity algorithm will be applied on all addresses
     *
     * @return array
     */
    private function tryLocateAtLeastTheCity($address, stdClass $relevance = null)
    {
        if (empty($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_CITY])) {
            return false;
        }

        if (empty($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_REGION])) {
            return false;
        }

        $sql = 'SELECT * FROM `' . self::TABLE_NAME . '` WHERE `region` LIKE %s AND `city` LIKE %s  LIMIT ' . (Rest_Dpd_Postcode_Search::SEARCH_APPLY_SIMILARITY_MAX_THRESHOLD + 1) . ' ';
        $sql = sprintf($sql,
            $this->quote($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_REGION] . '%'),
            $this->quote($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_CITY] . '%')
        );

        $results = array();
        foreach ($this->query($sql) as $row) {
            $results[] = $row;
        }
        if (count($results) > Rest_Dpd_Postcode_Search::SEARCH_APPLY_SIMILARITY_MAX_THRESHOLD) {
            if (Rest_Dpd_Postcode_Search::SEARCH_CAN_RETURN_RANDOM_VALUES) {
                $relevance->percent = 60;

                return array(array_pop($results));
            } else {
                $relevance->percent = 0;

                return false;
            }
        }
        if (is_array($results) && count($results) == 1) {
            $relevance->percent = 95;

            return $results;
        } elseif (is_array($results) && count($results) > 1) {
            $relevance->percent = 85;

            return $this->processSimilarity($address, $results);
        }


    }

    /**
     *   try the strict search meaning the search on city region and words of address
     *   calling the strictSearch
     *    if one item is returned then the postal code was found
     *    else the similarity algorithm will be applied on all results
     *
     * @param $address
     *
     * @return bool|string
     */
    private function tryStrictSearchAndReturn($address)
    {
        $results      = false;
        $strictSearch = $this->strictSearch($address);

        if ($strictSearch == 1) {
            $results = $this->strictSearch($address, false);

            return $this->returnPostalCode($results);
        } elseif ($strictSearch < Rest_Dpd_Postcode_Search::SEARCH_APPLY_SIMILARITY_MAX_THRESHOLD && $strictSearch > Rest_Dpd_Postcode_Search::SEARCH_APPLY_SIMILARITY_MIN_THRESHOLD) {
            $strictSearch = $this->strictSearch($address, false);
            $results      = $this->processSimilarity($address, $strictSearch);
        }

        if (is_array($results) && count($results) == 1) {
            return $this->returnPostalCode($results);
        }

        return false;
    }


    /**
     * @param array $address
     */
    private function processRegionAndCity(array &$address)
    {
        $oldCity   = $address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_CITY];
        $oldRegion = $address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_REGION];

        $validCity = $this->isCityValid($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_CITY]);
        if ($validCity == 0 && strlen($oldCity) > 2) {
            $address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_CITY] = $this->processCitySimilarity($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_CITY]);
        }
        $validRegion = $this->isRegionValid($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_REGION]);
        if ($validRegion == 0 && strlen($oldRegion) > 1) {
            $address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_REGION] = $this->processRegionSimilarity($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_REGION]);
        }
        if (
            $oldCity == $address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_CITY] && $oldRegion == $address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_REGION]
        ) {
            return false;
        }

        return true;
    }


    /**
     * process the result and return the found entry
     *
     * @param $results
     *
     * @return bool|string
     */
    private function returnPostalCode($results)
    {
        if (is_array($results) && count($results) == 1) {
            $tmp = array_pop($results);

            return (string)(!empty($tmp[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_POSTCODE]) ? $tmp[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_POSTCODE] : false);
        }
        if (is_array($results) && count($results) > 1) {
            $tmp = $results;

            return (string)(!empty($tmp[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_POSTCODE]) ? $tmp[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_POSTCODE] : false);
        }

        return false;
    }

    /**
     * @param $address
     * @param $results
     *
     * @return array
     */
    private function processSimilarity($address, $results)
    {
        $houseNumber = $this->findHouseNumber($address['address']);

        $foundHouseNumbers = array();

        if (!is_array($results) || count($results) == 0) {
            return false;
        } elseif (is_array($results) && count($results) == 1) {
            return $results;
        }
        $similarityArray   = array();
        $addressIdentifier = $address['address'];
        foreach ($results as $key => $tempAddress) {
            $tempIdentifier = $tempAddress['address'];
            $percent        = 0;
            similar_text($addressIdentifier, $tempIdentifier, $percent);
            $similarityArray[$key] = $percent;

            if (!empty($houseNumber)) {
                $dbAddressNumbers = $this->extractHouseNumbersFromDatabaseAddress($tempAddress['address']);
                $checkResult      = $this->checkIfNumberInInterval($houseNumber, $dbAddressNumbers);
                if ($checkResult) {
                    $foundHouseNumbers[] = $key;
                }
            }
        }

        if (count($foundHouseNumbers) == 1) {
            $key = array_pop($foundHouseNumbers);

            return array($results[$key]);
        } elseif (count($foundHouseNumbers) > 1) {
            // we have to make the similarity count higher for $foundHouseNumbers
            $similarityAverage = array_sum($similarityArray) / count($similarityArray);
            //we want to not add too much noise in our decision
            //that is why we have to increase only the results if are up then a threshold

            $delta = (max($similarityArray) - $similarityAverage);

            foreach ($foundHouseNumbers as $key) {
                // we have to ensure that the result is not false positive case
                if ($similarityArray[$key] > $similarityAverage - $delta * Rest_Dpd_Postcode_Search::SEARCH_HOUSE_NUMBER_CONSTANT1) {
                    //we have to be sure that this result will be increased enough
                    $similarityArray[$key] += $delta * Rest_Dpd_Postcode_Search::SEARCH_HOUSE_NUMBER_CONSTANT2;
                }
            }
        }

        $maxs = array_keys($similarityArray, max($similarityArray));
        $max  = $maxs[0];

        return array($results[$max]);
    }


    private function checkIfNumberInInterval($nr, $interval)
    {
        $nr = intval($nr);
        if (count($interval) == 1) {
            $interval = array_pop($interval);
        }
        if (!is_array($interval) && intval($interval) && $nr == $interval) {
            return true;
        } elseif (!is_array($interval)) {
            return false;
        }
        $lastValue = null;
        foreach ($interval as $key => $value) {
            $value = intval($value);
            if (!empty($lastValue)) {
                if ($nr <= $value && $nr >= $lastValue) {
                    return true;
                }
            }
            $lastValue = $value;
        }

        return false;
    }


    /**
     * extract all numbers from database address field
     *
     *
     *
     * @param $address
     *
     * @return array
     */
    private function extractHouseNumbersFromDatabaseAddress($address)
    {
        $numbers = array();
        $address = str_replace('.', ' ', $address);
        $address = str_replace('/', ' ', $address);
        $address = str_replace('\\', ' ', $address);
        $address = str_replace('-', ' ', $address);
        $words   = explode(' ', $address);
        foreach ($words as $word) {
            if (strlen($word) == 0) {
                continue;
            }
            if (intval($word)) {
                $numbers[] = intval($word);
            } elseif ($word == 't') {
                $numbers[] = 9999999;
            }
        }

        return $numbers;

    }

    /**
     * find the address house number - for given address string
     *
     * @param $address
     */
    function findHouseNumber($address)
    {
        $canSkipWords             = Rest_Dpd_Postcode_Search::SEARCH_HOUSE_NUMBER_IDENTIFIER_CAN_SKIP_WORDS;
        $numbers                  = array();
        $houseNumberIdentifiers   = Rest_Dpd_Postcode_Search_Model_CachedData::getHouseNumberIdentifier();
        $houseNumber              = null;
        $address                  = str_replace('.', ' ', $address);
        $address                  = str_replace('/', ' ', $address);
        $address                  = str_replace('\\', ' ', $address);
        $address                  = str_replace('-', ' ', $address);
        $words                    = explode(' ', $address);
        $lastWordWasTheIdentifier = false;
        $skipCount                = 0;
        foreach ($words as $word) {
            if (strlen($word) == 0) {
                continue;
            }
            if (intval($word)) {
                $numbers[] = intval($word);
            }
            if ($lastWordWasTheIdentifier == true && intval($word)) {
                $houseNumber = $word;

                return $houseNumber;
            } elseif ($lastWordWasTheIdentifier == true) {
                $skipCount++;
                if ($skipCount > $canSkipWords) {
                    $lastWordWasTheIdentifier = false;
                }
            }

            if (in_array($word, $houseNumberIdentifiers)) {
                $lastWordWasTheIdentifier = true;
            }
        }
        if (count($numbers) == 1) {
            return array_pop($numbers);
        }

        return null;
    }


    /**
     * check in database if there is a valid city name
     *
     * @param $string
     *
     * @return mixed
     */
    private function isCityValid($string)
    {
        $sql = 'SELECT  COUNT(*) AS count FROM `' . self::TABLE_NAME . '` WHERE `city` = %s ';
        $sql = sprintf($sql,
            $this->quote($string)
        );

        $results = array();
        foreach ($this->query($sql) as $row) {
            $results[] = $row;
        }

        return @$results[0]['count'];

    }


    /**
     * check in database if there is a valid region name
     *
     * @param $string
     *
     * @return mixed
     */
    private function isRegionValid($string)
    {
        $sql = 'SELECT  COUNT(*) AS count FROM `' . self::TABLE_NAME . '` WHERE `region` = %s ';
        $sql = sprintf($sql,
            $this->quote($string)
        );

        $results = array();
        foreach ($this->query($sql) as $row) {
            $results[] = $row;
        }

        return @$results[0]['count'];

    }

    /**
     * return all cities
     *
     * @return array
     */
    public function getAllCities()
    {
        $sql     = 'SELECT  city  FROM `' . self::TABLE_NAME . '` WHERE 1 ';
        $results = array();

        foreach ($this->query($sql) as $row) {
            $results[] = $row;
        }

        return $results;

    }

    /**
     * return an array representing the stored values for address identified by postcode
     *
     * @param $postcode
     *
     * @return bool|mixed
     */
    public function getAddressByPostcode($postcode)
    {

        $sql = 'SELECT  *  FROM `' . self::TABLE_NAME . '` WHERE postcode = %s LIMIT 1 ';
        $sql = sprintf($sql,
            $this->quote($postcode)
        );

        $results = array();

        foreach ($this->query($sql) as $row) {
            $results[] = $row;
        }
        if (count($results) == 1) {
            return array_pop($results);
        }

        return false;
    }

    /**
     * check for miss typing on the input for the city
     *
     * is using an similarity algorithm on an array
     * available cities are cached in php
     *
     * @param $cityInput
     *
     * @return mixed
     */
    public function processCitySimilarity($cityInput)
    {
        //use cached database
        $cities = Rest_Dpd_Postcode_Search_Model_CachedData::getCities();
        if (!empty($cities)) {
            $results         = array();
            $similarityArray = array();
            $i               = 0;
            foreach ($cities as $city) {
                $results[$i] = $city;
                similar_text($cityInput, $city, $percent);
                $similarityArray[$i] = $percent;
                $i++;
            }
            $maxs = array_keys($similarityArray, max($similarityArray));
            $max  = $maxs[0];

            if (!empty($results[$max]) && max($similarityArray) >= Rest_Dpd_Postcode_Search::SEARCH_APPLY_SIMILARITY_CITY_PERCENTAGE_THRESHOLD) {
                return $results[$max];
            }
        }

        $sql             = 'SELECT DISTINCT city  FROM `' . self::TABLE_NAME . '` WHERE 1 ';
        $results         = array();
        $similarityArray = array();
        $i               = 0;
        foreach ($this->query($sql) as $row) {
            $city        = $row['city'];
            $results[$i] = $city;
            similar_text($cityInput, $city, $percent);
            $similarityArray[$i] = $percent;
            $i++;
        }
        $maxs = array_keys($similarityArray, max($similarityArray));
        $max  = $maxs[0];

        if (!empty($results[$max])) {
            return $results[$max];
        }

        return $cityInput;

    }

    /**
     * check for miss typing on the input for the region
     *
     * is using an similarity algorithm on an array
     * available cities are cached in php
     *
     * @param $regionInput
     *
     * @return mixed
     */
    public function processRegionSimilarity($regionInput)
    {
        //use cached database
        $regions = Rest_Dpd_Postcode_Search_Model_CachedData::getRegions();
        if (!empty($regions)) {
            $results         = array();
            $similarityArray = array();
            $i               = 0;
            foreach ($regions as $region) {
                $results[$i] = $region;
                similar_text($regionInput, $region, $percent);
                $similarityArray[$i] = $percent;
                $i++;
            }
            $maxs = array_keys($similarityArray, max($similarityArray));
            $max  = $maxs[0];

            if (!empty($results[$max]) && max($similarityArray) >= Rest_Dpd_Postcode_Search::SEARCH_APPLY_SIMILARITY_CITY_PERCENTAGE_THRESHOLD) {
                return $results[$max];
            }
        }

        $sql             = 'SELECT DISTINCT region  FROM `' . self::TABLE_NAME . '` WHERE 1 ';
        $results         = array();
        $similarityArray = array();
        $i               = 0;
        foreach ($this->query($sql) as $row) {
            $region      = $row['region'];
            $results[$i] = $region;
            similar_text($regionInput, $region, $percent);
            $similarityArray[$i] = $percent;
            $i++;
        }
        $maxs = array_keys($similarityArray, max($similarityArray));
        $max  = $maxs[0];

        if (!empty($results[$max])) {
            return $results[$max];
        }

        return $regionInput;

    }

    /**
     * perform a strict search in database by looking at city region and address
     * address field is searched using LIKE statment
     *
     * @param      $address
     * @param bool $count
     *
     * @return array
     */
    private function strictSearch($address, $count = true)
    {
        $street = $address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_ADDRESS];
        $words  = explode(' ', $street);

        $sql      = 'SELECT ' . ($count === true ? ' COUNT(*) AS count ' : ' * ') . ' FROM `' . self::TABLE_NAME . '` WHERE `region` LIKE %s AND `city` LIKE %s ';
        $sql      = sprintf($sql,
            $this->quote($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_REGION] . "%"),
            $this->quote($address[Rest_Dpd_Postcode_Search::ADDRESS_FIELD_CITY] . "%")
        );
        $wordsNew = array();
        foreach ($words as $val) {
            $filteredVal = preg_replace("/[^a-zA-Z]+/", "", $val);
            if (strlen($filteredVal) > 4) {
                $wordsNew[] = $filteredVal;
            }
        }
        $wordsNew = array_unique($wordsNew);
        $i        = 0;
        if (count($wordsNew)) {
            $sql .= ' AND (';
        }
        foreach ($wordsNew as $val) {
            if ($i != 0) {
                $sql .= ' OR ';
            }
            $sql .= ' address LIKE ' . $this->quote('%' . $val . '%') . ' ';
            $i++;
        }
        if (count($wordsNew)) {
            $sql .= ' ) ';
        }

        $sql     = $sql . ' LIMIT ' . Rest_Dpd_Postcode_Search::STRICT_SEARCH_LIMIT;
        $results = array();
        foreach ($this->query($sql) as $row) {
            $results[] = $row;
        }
        if ($count == true) {
            return $results[0]['count'];
        }

        return $results;
    }


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
    public function query($sql, $bind = array())
    {
        return $this->getConnection()->query($sql);
    }


    /**
     * update postcode database using a CSV file
     *  - store all entry from csv file in a temporary table
     *  - create a backup of the main table - postcode
     *  - sync temporary table with main postcode table
     *  - if errors occur return to the back-up table
     *
     * @param $file
     */
    public function updateDatabase($file)
    {
        if (!file_exists($file)) {
            return false;
        }

        $pathInfo = pathinfo($file);
        if ($pathInfo['extension'] != 'csv') {
            return false;
        }

        $mappedColumns = array();
        $columns       = array();
        $rows          = array();
        if (($handle = fopen($file, "r")) !== false) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $data = $this->applyFiltersForAddress($data);
                $num  = count($data);
                if ($row == 0) {
                    for ($c = 0; $c < $num; $c++) {
                        $columns[$c] = $data[$c];
                    }
                    $mappedColumns = $this->mapTheColumns($columns);
                    if ($mappedColumns === false) {
                        throw new Exception('Wrong CSV format, please use/request a new CSV file.');
                    }
                    $row++;
                    continue;
                }
                $tempRow = array();
                for ($c = 0; $c < $num; $c++) {
                    if (!isset($mappedColumns[$c])) {
                        continue;
                    }

                    if ($mappedColumns[$c] == 'saturday') {
                        $tempRow[$mappedColumns[$c]] = ($data[$c] == 'da' ? 1 : 0);
                        continue;
                    }
                    $tempRow[$mappedColumns[$c]] = $data[$c];
                }
                $rows[] = $tempRow;

                $row++;
            }
            fclose($handle);
        }


        $chunks = array_chunk($rows, 500);

        $table = self::TABLE_NAME . '_temp';
        $this->dropTableIfExist($table);
        $this->createTemporaryTable($table);
        $toBeInsertedRowsCount = 0;
        $insertErrors          = 0;
        foreach ($chunks as $chunk) {
            $toBeInsertedRowsCount += count($chunk);
            try {
                $this->insertMultiple($table, $chunk);
            } catch (Exception $e) {
                //for some reasons we have to skip this chunk
                $insertErrors++;
                //for now we have to brake the import, even a single entry goes wrong.
                throw new Exception('Some rows in the csv file are wrong formatted, or the content is invalid. Please use an other CSV file.');
            }
        }

        $tempTableRows = $this->countTemporaryTableRows($table);
        if ($tempTableRows / $toBeInsertedRowsCount > 0.9 && $insertErrors < 3) {
            $databaseBackupIsStable = 0;
            try {
                $this->createADatabaseBackup();
                $databaseBackupIsStable = 1;
                $this->switchTemporaryTable($table);
            } catch (Exception $e) {
                if($databaseBackupIsStable===1) {
                    $this->restoreDatabaseBackup();
                }
                throw new Exception('Database synchronization problem. Please run the import again.');
            }
        } else {
            throw new Exception('Many rows from csv were wrong formatted. The postcode database was not imported. Please check your CSV file and run the import again.');
        }

        return true;
    }


    protected function countTemporaryTableRows($table)
    {

        $result = $this->query('
                    SELECT COUNT(*) AS count FROM `' . $table . '`;
            ');
        $r      = array();
        foreach ($result as $result) {
            $r[] = $result;
        }
        if (isset($r[0], $r[0]['count'])) {
            return $r[0]['count'];
        }

        return false;
    }


    /**
     * save all stored data into the main table of postcode module
     *
     * @param $table
     *
     * @return int
     *
     */
    protected function switchTemporaryTable($table)
    {
        $this->truncateTableIfExist(self::TABLE_NAME);

        $this->query('
                    ALTER TABLE  `' . self::TABLE_NAME . '`
                    CHANGE COLUMN `city` `city` VARCHAR(50) NOT NULL ;
        ');
        $this->query('
                    INSERT INTO `' . self::TABLE_NAME . '` SELECT * FROM `' . $table . '`;
        ');
        $this->query('
                    TRUNCATE TABLE `' . $table . '`;
        ');

        return 1;
    }

    /**
     * @param $columns
     *
     * @return array | boolean  on error
     */
    protected function mapTheColumns($columns)
    {
        $mappingRule = array(
            'cod postal'      => 'postcode',
            'judet'           => 'region',
            'localitate'      => 'city',
            'tip artera'      => 'road_type',
            'denumire'        => 'address',
            'zona de livrare' => 'zone',
            'livrare sambata' => 'saturday',
        );

        $mappedColumns = array();
        foreach ($columns as $key => $column) {
            if (!isset($mappingRule[$column])) {
                continue;
            }
            $mappedColumns[$key] = $mappingRule[$column];
        }
        //if mandatory fields are missing, then stop the import
        if (!in_array('city', $mappedColumns) || !in_array('city', $mappedColumns)) {
            return false;
        }

        return $mappedColumns;

    }

    /**
     * Inserts a table row with specified data.
     *
     * @param mixed $table The table to insert data into.
     * @param array $bind  Column-value pairs.
     *
     * @return int The number of affected rows.
     */
    public function insert($table, array $bind)
    {
        foreach ($bind as &$value) {
            $value = $this->quote($value);
        }


        // build the statement
        $sql = "INSERT INTO "
            . $this->_quoteIdentifier($table, true)
            . ' (postcode, region, city, road_type, address, d_depo,  d_sort, zone, saturday, route) '
            . ' VALUES (' . implode(", ", $bind) . ')';

        $bind   = array_values($bind);
        $stmt   = $this->getConnection()->prepare($sql);
        $result = $stmt->execute($bind);

        return count($result);
    }

    /**
     * insert an chunk of data in one statement
     *
     * @param       $table
     * @param array $chunk
     *
     * @return array
     */
    public function insertMultiple($table, array $chunk)
    {
        if (count($chunk) == 0) {
            return false;
        }

        $firstItem = array_pop($chunk);
        $keys      = array_keys($firstItem);
        $sql       = "INSERT INTO " . $this->_quoteIdentifier($table, true) . "
            (`" . implode('`,`', $keys) . "`) VALUES
            ('" . implode('\',\'', $firstItem) . "')";

        foreach ($chunk as $item) {
            $sql .= "
            , ('" . implode('\',\'', $item) . "')";
        }

        $result = $this->query($sql);

        return $result;
    }

    /**
     * store a database backup of the last stable version  of the table
     *
     * @return bool
     */
    protected function createADatabaseBackup()
    {
        $this->dropTableIfExist(self::TABLE_NAME . '_backup');
        $this->createTemporaryTable(self::TABLE_NAME . '_backup');
        $this->query('
                    INSERT INTO `' . self::TABLE_NAME . '_backup' . '` SELECT * FROM `' . self::TABLE_NAME . '`;
        ');

        return true;
    }


    /**
     * in case of emergency problems we have to restore last
     * stable version of the postcode database
     *
     * @return bool
     */
    protected function restoreDatabaseBackup()
    {
        $this->query('
                    TRUNCATE TABLE `' . self::TABLE_NAME . '`;
        ');

        $this->query('
                    INSERT INTO `' . self::TABLE_NAME . '` SELECT * FROM `' . self::TABLE_NAME . '_backup' . '`;
        ');

        return true;
    }

    /**
     *
     * @return bool
     */
    public function rollbackDatabase(){
        try {
            return $this->restoreDatabaseBackup();
        } catch (Exception $e) {
            return  false;
        }
    }

    protected function createTemporaryTable($table)
    {
        $this->query('
                    CREATE TABLE IF NOT EXISTS `' . $table . '` (
                      `id` int(10) unsigned NULL,
                      `postcode` varchar(10) NOT NULL,
                      `region` varchar(50) NULL,
                      `city` varchar(50) NOT NULL,
                      `road_type` varchar(15) NULL,
                      `address` varchar(100) NULL,
                      `d_depo` int(5) NULL,
                      `d_sort` varchar(5) NULL,
                      `zone` varchar(5) NULL,
                      `saturday` tinyint(1) NULL,
                      `route` varchar(5) NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
            ');
        $this->query('
                    ALTER TABLE `' . $table . '`
                     ADD PRIMARY KEY (`id`), ADD KEY `postcode` (`postcode`), ADD KEY `city` (`city`), ADD KEY `region` (`region`), ADD KEY `region_2` (`region`,`city`), ADD KEY `address` (`address`), ADD KEY `region_3` (`region`,`city`,`address`);
            ');
        $this->query('
                   ALTER TABLE `' . $table . '`
                    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
            ');

        return true;
    }

    public function dropTableIfExist($table)
    {
        return $this->query('DROP TABLE IF EXISTS `' . $table . '`');
    }

    /**
     * return true or false if the table was truncated
     *
     * @param $table
     *
     * @return bool
     */
    public function truncateTableIfExist($table)
    {
        try {
            $this->query('
                TRUNCATE `' . $table . '`;
            ');
        } catch (Exception $e) {
            //the table may not exist
            return false;
        }

        return true;
    }


    /**
     *
     */
    public function install()
    {
        $this->dropTableIfExist(self::TABLE_NAME);

        try {
            $this->query('
                    CREATE TABLE IF NOT EXISTS `' . self::TABLE_NAME . '` (
                      `id` int(10) unsigned NULL,
                      `postcode` varchar(10) NOT NULL,
                      `region` varchar(50) NULL,
                      `city` varchar(50) NOT NULL,
                      `road_type` varchar(15) NULL,
                      `address` varchar(100) NULL,
                      `d_depo` int(5) NULL,
                      `d_sort` varchar(5) NULL,
                      `zone` varchar(5) NULL,
                      `saturday` tinyint(1) NULL,
                      `route` varchar(5) NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
            ');
            $this->query('
                    ALTER TABLE `' . self::TABLE_NAME . '`
                     ADD PRIMARY KEY (`id`), ADD KEY `postcode` (`postcode`), ADD KEY `city` (`city`), ADD KEY `region` (`region`), ADD KEY `region_2` (`region`,`city`), ADD KEY `address` (`address`), ADD KEY `region_3` (`region`,`city`,`address`);
            ');
            $this->query('
                   ALTER TABLE `' . self::TABLE_NAME . '`
                    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
            ');
        } catch (Exception $e) {
            //table exists, we have to empty the table
            $this->truncateTableIfExist(self::TABLE_NAME);
        }

        $insertSql = $this->getInsertScripts();

        $insertSqlChunks = explode(';', $insertSql);
        foreach ($insertSqlChunks as $query) {
            try {
                $this->query(
                    $query . ';'
                );
            } catch (Exception $e) {
                //for any error we have to keep inserting into database
                continue;
            }
        }


    }

    public function uninstall()
    {
        try {
            $this->query('DROP TABLE IF EXISTS `' . self::TABLE_NAME . '`');
        } catch (Exception $e) {
            //the table may not exist
        }

        return true;
    }

    /**
     * return install script for rest_dpd_postcodes
     *
     * @return string
     */
    public function getInsertScripts()
    {
        $sqlData = Rest_Dpd_Postcode_Search::getBasePath() . DIRECTORY_SEPARATOR . 'Rest' . DIRECTORY_SEPARATOR . 'Dpd' . DIRECTORY_SEPARATOR . 'Postcode' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'data.sql';;

        $insertSql = file_get_contents($sqlData);
        $insertSql = str_replace('rest_dpd_postcodes', self::TABLE_NAME, $insertSql);

        return $insertSql;
    }

}