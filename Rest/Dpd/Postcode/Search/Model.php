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
abstract class Rest_Dpd_Postcode_Search_Model
{

    protected $_autoQuoteIdentifiers = true;

    protected static $_connection = null;

    const TABLE_NAME = 'ps_rest_dpd_postcodes';

    protected static function getConnection()
    {
        return self::$_connection;
    }

    /**
     * quote an input string by the connection adapter
     *
     * @param $string
     *
     * @return mixed
     */
    public function quote($string)
    {
        return "'" . $string . "'";
    }

    /**
     * Returns the symbol the adapter uses for delimited identifiers.
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return '`';
    }


    /**
     * Quote an identifier.
     *
     * @param  string $value The identifier or expression.
     * @param boolean $auto  If true, heed the AUTO_QUOTE_IDENTIFIERS config option.
     *
     * @return string        The quoted identifier and alias.
     */
    protected function _quoteIdentifier($value, $auto = false)
    {
        if ($auto === false || $this->_autoQuoteIdentifiers === true) {
            $q = $this->getQuoteIdentifierSymbol();

            return ($q . str_replace("$q", "$q$q", $value) . $q);
        }

        return $value;
    }

    /**
     * apply general filters on address array
     *
     * @param $data
     *
     * @return string
     */
    public function applyFiltersForAddress(&$data)
    {
        if (!is_array($data)) {
            $data = $this->applyTextFilter($data);
        } else {
            foreach ($data as &$value) {
                $value = $this->applyTextFilter($value);
            }
        }

        return $data;
    }


    protected function applyTextFilter($string)
    {
        $search  = array('Ă', 'ă', 'Â', 'â', 'Î', 'î', 'Ş', 'ş', 'Ţ', 'ţ', 'Ş', 'ş', 'Ţ', 'ţ', "\s", "\t", "\r\n");
        $replace = array('A', 'a', 'A', 'a', 'I', 'i', 'S', 's', 'T', 't', 'S', 's', 'T', 't', " ", ' ', ' ');
        $string  = str_replace($search, $replace, $string);

        // iconv is returning an notice sometimes unexpected
        ob_start();
        $temp = @iconv("UTF-8", "ISO-8859-1//TRANSLIT", $string);
        $out1 = ob_get_contents();
        ob_end_clean();

        if (!empty($temp)) {
            $string = $temp;
        }
        $string = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $string = preg_replace("/[^a-zA-Z0-9.\\\ \/-]+/", "", $string);

        return strtolower(trim($string));
    }


}