<?php

if (!defined('_PS_VERSION_'))
	exit;

require_once(_PS_MODULE_DIR_ . '/dpdgeopost/classes/controller.php');

class DpdGeopostWS extends DpdGeopostController
{

	protected $config;
	private $endpoint;

	private $credentials = array();

	protected $targetNamespace;
	protected $serviceName;

	const	applicationType = 9;
	const 	debug 			= true;
	const 	FILENAME 		= 'dpdgeopost.ws';

	const 	DEBUG_FILENAME			= 'DPDGEOPOST_DEBUG_FILENAME';
	const 	DEBUG_POPUP				= false;
	const 	DEBUG_FILENAME_LENGTH 	= 16;

	public function __construct()
	{
		parent::__construct();
		$this->config = new DpdGeopostConfiguration;
	}

	/** $name must be without /v1/ */
	public function __call($name, $payload)
	{

		self::$errors = array();

		if(stripos($name, 'wsrest') === false) {
			return false;
		}

		$methodName = str_ireplace('wsrest_', '', $name);
		$path = str_ireplace('_', '/', $methodName);
		//list($_t, $path) = explode('_', $name);

		if ($this->loadWSData()) {
			$this->loadEndpoint();

			$payload = array_merge($payload[0], $this->credentials);
            $production_url = trim($this->config->ws_production_url);
			$urlMethod = $production_url . $path;

			//$payload = $this->trimRequest($payload);

            $dataToLog = array(
                'url'  => $urlMethod,
                'debug_backtrace' => debug_backtrace(2, 4),
                'payload_raw' => $payload,
            );

            $ct = date('Y-m-d-H-i');

			$data_string = json_encode($payload);

            $requestkey = 'nor_';
			if(isset($payload['_raw'])) {
			    $requestkey = 'raw_';
            }

            $current_minute = floatval(date('i'));
			$current_cache_period = floor($current_minute / 10);

            $requestkey =  $requestkey . 'cache__' . date('Y_m_d_H_') .$current_cache_period .'_00' . '__' . sha1($urlMethod . '/' . $data_string );


            $cache_is_active = false;
            if($cache_is_active && !in_array($path, array(
                    'shipment',
                    'shipment/cancel',
                    'shipment/add_parcel',
                    'shipment/finalize',
                    'shipment/info',
                    'track',
                    'pickup'
            ))) {
                $foundCache = $this->getCache($requestkey);
                return $foundCache;
            }

			$ch = curl_init($urlMethod);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt(
				$ch,
				CURLOPT_HTTPHEADER,
				array(
					'Content-Type: application/json',
					'Content-Length: ' . strlen($data_string)
				)
			);

			$callResult = curl_exec($ch);

			$error = curl_error($ch);
			$error_no = curl_errno($ch);
			$times = array(
			    'url' => curl_getinfo($ch,  CURLINFO_EFFECTIVE_URL ),
			    'total' => curl_getinfo($ch, CURLINFO_TOTAL_TIME),
                'connect' => curl_getinfo($ch,  CURLINFO_CONNECT_TIME ),
                'pretransfer' => curl_getinfo($ch,   CURLINFO_PRETRANSFER_TIME  ),
                'pretransfer' => curl_getinfo($ch,   CURLINFO_PRETRANSFER_TIME  ),
                'dns' => curl_getinfo($ch,    CURLINFO_NAMELOOKUP_TIME   ),
            );

            curl_close($ch);


			$callResultDecoded = json_decode($callResult, true);

            $dataToLog['times'] = $times;
            $dataToLog['result_raw'] = $callResult;
            $dataToLog['payload_json'] = $data_string;
            $dataToLog['result_json'] = $callResultDecoded;


            $logFile = _PS_CACHE_DIR_ . DIRECTORY_SEPARATOR . date('Y-m-d H_i') . '.txt';

            if(false && ($methodName == 'calculate' || $methodName == 'shipment')) {
                file_put_contents($logFile, print_r($dataToLog, true), FILE_APPEND);
            }


			if(isset($payload['_raw']) && $payload['_raw']) {
                //self::$request_caches[$requestkey] = $callResult;
                $this->saveCache($requestkey, $callResult);
			} else {
                //self::$request_caches[$requestkey] = $callResultDecoded;
                $this->saveCache($requestkey, $callResultDecoded);
            }

			//return self::$request_caches[$requestkey];
            return $this->getCache($requestkey);
		}

		return false;
	}


	private function getCache($requestkey) {
        $cache_dir = _PS_CACHE_DIR_ . DIRECTORY_SEPARATOR . date('Y-m-d');
        if(!is_dir($cache_dir)) mkdir($cache_dir);
        $cache_log = $cache_dir . DIRECTORY_SEPARATOR . '000_cache_log';
        if(is_dir($cache_dir)) {
            $cache_file = $cache_dir . DIRECTORY_SEPARATOR . $requestkey;

            if(is_file($cache_file)) {
                file_put_contents($cache_log, date('Y-m-d H:i:s') . "\t" .  'getting: ' .  "\t" .  $requestkey . "\n", FILE_APPEND);
                $content = file_get_contents($cache_file);
                return unserialize($content);
            }
        }
        file_put_contents($cache_log, date('Y-m-d H:i:s') . "\t" . 'missed: ' . "\t" .  $requestkey . "\n", FILE_APPEND);

        return false;
    }

    private function saveCache($requestkey, $data) {
        $cache_dir = _PS_CACHE_DIR_ . DIRECTORY_SEPARATOR . date('Y-m-d');
        if(!is_dir($cache_dir)) mkdir($cache_dir);


        if(is_dir($cache_dir)) {
            $cache_file = $cache_dir . DIRECTORY_SEPARATOR . $requestkey;
            $cache_log = $cache_dir . DIRECTORY_SEPARATOR . '000_cache_log';
            file_put_contents($cache_file, serialize($data));
            file_put_contents($cache_log, date('Y-m-d H:i:s') . "\t" . 'saving: ' . "\t" . $requestkey . "\n", FILE_APPEND);
        }
    }

	private function loadWSData()
	{

        if ($this->config->ws_username && $this->config->ws_password) {
            $this->credentials = array(
                'userName' => pSQL($this->config->ws_username),
                'password' => pSQL($this->config->ws_password)
            );

            return true;
        } else {
            self::$errors[] = $this->l('WS username / password is missing');
            return false;
        }

	}

	private function loadEndpoint()
	{

	    if($this->config->ws_production_url) {
            $this->endpoint = $this->config->ws_production_url;
        } else {
            self::$errors[] = $this->l('DPD API URL is missing.');
            return false;
        }

		return true;
	}

	protected function getError($result)
	{
		$transaction_id = isset($result['transactionId']) ? '. ' . $this->module_instance->l('Transaction Id:') . ' ' . $result['transactionId'] : '';

		if (isset($result['detail']))
			return $result['detail']['EShopException']['error']['text'] . $transaction_id;

		if (isset($result['priceList']['error']['text']))
			return $result['priceList']['error']['text'] . $transaction_id;

		if (isset($result['resultList']['error']['text']))
			return $result['resultList']['error']['text'] . $transaction_id;

		if (isset($result['error']['text']))
			return $result['error']['text'] . $transaction_id;

		if (isset($result['prestashop_message']))
			return $result['prestashop_message'] . $transaction_id;

		return null;
	}

	private function createDebugFileIfNotExists()
	{
		if ((!$debug_filename = Configuration::get(self::DEBUG_FILENAME)) || !$this->isDebugFileName($debug_filename)) {
			$debug_filename = Tools::passwdGen(self::DEBUG_FILENAME_LENGTH) . '.html';
			Configuration::updateValue(self::DEBUG_FILENAME, $debug_filename);
		}

		if (!file_exists(_DPDGEOPOST_MODULE_DIR_ . $debug_filename)) {
			$file = fopen(_DPDGEOPOST_MODULE_DIR_ . $debug_filename, 'w');
			fclose($file);
		}

		return $debug_filename;
	}

	private function isDebugFileName($debug_filename)
	{
		return Tools::strlen($debug_filename) == (int)self::DEBUG_FILENAME_LENGTH + 5 && preg_match('#^[a-zA-Z0-9]+\.html$#', $debug_filename);
	}

	private function trimRequest($request) {
		if(!is_array($request) && is_string($request) ) {
			return trim($request);
		}

		if(!is_array($request)) {
			return $request;
		}

		return array_map( array($this, 'trimRequest'), $request);
	}

}