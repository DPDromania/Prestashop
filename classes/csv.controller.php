<?php
/** **/

if (!defined('_PS_VERSION_'))
	exit;


class DpdGeopostCSVController extends DpdGeopostController
{
	private $csv_titles 				= array();
	private $csv_available_formats 		= array();

	const SETTINGS_SAVE_CSV_ACTION 		= 'saveModuleCSVSettings';
	const SETTINGS_DOWNLOAD_CSV_ACTION 	= 'downloadModuleCSVSettings';
	const SETTINGS_DELETE_CSV_ACTION 	= 'deleteModuleCSVSettings';
	const FILENAME 						= 'csv.controller';
	const DEFAULT_FIRST_LINE_INDEX		= 2;

	public function __construct()
	{
		parent::__construct();

		$this->csv_titles = array(
			'country' 					=> $this->l('Country'),
			'region' 					=> $this->l('Region / State'),
			'zip' 						=> $this->l('Zip / Postal Code'),
			'weight_from' 				=> $this->l('Weight / Price (From)'),
			'weight_to' 				=> $this->l('Weight / Price (To)'),
			'shipping_price' 			=> $this->l('Shipping Price'),
			'shipping_price_percentage'	=> $this->l('Shipping Price Percentage'),
			'currency'					=> $this->l('Currency'),
			'method_id' 				=> $this->l('Method ID'),
			'cod_surcharge' 			=> $this->l('COD Surcharge'),
			'cod_surcharge_percentage' 	=> $this->l('COD Surcharge Percentage'),
			'cod_min_surcharge' 		=> $this->l('COD Min. Surcharge'),
		);

		$this->csv_available_formats = array(
			'text/csv',
			'text/plain',
			'application/csv',
			'text/comma-separated-values',
			'application/excel',
			'application/vnd.ms-excel',
			'application/vnd.msexcel',
			'text/anytext',
			'application/octet-stream',
			'application/txt',
			'application/force-download'
		);
	}

	public function getCSVPage()
	{
		$selected_pagination = Tools::getValue('pagination', '20');
		$page = Tools::getValue('current_page', '1');
		$start = ($selected_pagination * $page) - $selected_pagination;
		$limit = ' LIMIT '.(int)$start.', '.(int)$selected_pagination.' ';

		$selected_products_data = DpdGeopostCSV::getAllData($limit);
		$list_total = count(DpdGeopostCSV::getAllData());
		$pagination = array(20, 50, 100, 300);

		$total_pages = ceil($list_total / $selected_pagination);

		if (!$total_pages)
			$total_pages = 1;

		$this->context->smarty->assign(array(
			'saveAction' => $this->module_instance->module_url.'&menu=csv',
			'csv_data' => $selected_products_data,
			'page' => $page,
			'total_pages' => $total_pages,
			'pagination' => $pagination,
			'list_total' => $list_total,
			'selected_pagination' => $selected_pagination

		));
		return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_.'admin/csv.tpl');
	}

	public static function init()
	{
		$controller = new DpdGeopostCSVController;

		if (Tools::isSubmit(DpdGeopostCSVController::SETTINGS_SAVE_CSV_ACTION))
		{
			$csv_data = $controller->readCSVData();
			if ($csv_data === false)
			{
				DpdGeopost::addFlashError($controller->l('Wrong CSV file'));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=csv');
			}

			$message = $controller->validateCSVData($csv_data);
			if ($message !== true)
				return $controller->module_instance->outputHTML($controller->module_instance->displayErrors($message));

			if ($controller->saveCSVData($csv_data))
			{
				DpdGeopost::addFlashMessage($controller->l('CSV data was successfully saved'));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=csv');
			}
			else
			{
				DpdGeopost::addFlashError($controller->l('CSV data could not be saved'));
				Tools::redirectAdmin($controller->module_instance->module_url.'&menu=csv');
			}
		}

		if (Tools::isSubmit(DpdGeopostCSVController::SETTINGS_DOWNLOAD_CSV_ACTION))
			$controller->generateCSV();

		if (Tools::isSubmit(DpdGeopostCSVController::SETTINGS_DELETE_CSV_ACTION))
			$controller->deleteCSV();
	}

	private function deleteCSV()
	{
		if (DpdGeopostCSV::deleteAllData())
		{
			DpdGeopost::addFlashMessage($this->l('Price rules deleted successfully'));
			Tools::redirectAdmin($this->module_instance->module_url.'&menu=csv');
		}
		else
			DpdGeopost::addFlashError($this->l('Price rules could not be deleted'));
	}

	public function generateCSV()
	{
		$csv_data = array($this->csv_titles);
		$csv_data = array_merge($csv_data, DpdGeopostCSV::getCSVData());
		$this->arrayToCSV($csv_data, _DPDGEOPOST_CSV_FILENAME_.'.csv', _DPDGEOPOST_CSV_DELIMITER_);
	}

	private function arrayToCSV($array, $filename, $delimiter)
	{
		// open raw memory as file so no temp files needed, you might run out of memory though
		$f = fopen('php://memory', 'w');
		// loop over the input array
		foreach ($array as $line) {
			// generate csv lines from the inner arrays
			fputcsv($f, $line, $delimiter);
		}
		// rewrind the "file" with the csv lines
		fseek($f, 0);
		// tell the browser it's going to be a csv file
		header('Content-Type: application/csv; charset=utf-8');
		// tell the browser we want to save it instead of displaying it
		header('Content-Disposition: attachement; filename="'.$filename.'"');
		// make php send the generated csv lines to the browser
		fpassthru($f);
		exit;
	}

	private function validateCSVData($csv_data)
	{
		$errors = array();

		$csv_data_count = count($csv_data);

		if (!$this->validateCSVStructure($csv_data, $csv_data_count))
		{
			$errors[] = $this->l('Wrong CSV file structure or empty lines');
			return $errors;
		}

		$countries_validation = $this->validateCSVCountries($csv_data, $csv_data_count);
		if ($countries_validation !== true)
			$errors[] = sprintf($this->l('Country codes do not exist in your PrestaShop system - invalid lines: %s'), $countries_validation);

		$regions_validation = $this->validateCSVRegions($csv_data, $csv_data_count);
		if ($regions_validation !== true)
		{
			if ($regions_validation[0])
				$errors[] = sprintf($this->l('Regions / States can not be defined for country * so specific country must be provided - invalid lines: %s'), $regions_validation[0]);
			if ($regions_validation[1])
				$errors[] = sprintf($this->l('Regions / States do not belong to provided country - invalid lines: %s'), $regions_validation[1]);
		}

		$zips_validation = $this->validateCSVZips($csv_data, $csv_data_count);
		if ($zips_validation !== true)
		{
			if ($zips_validation[0])
				$errors[] = sprintf($this->l('ZIP / Postal codes does not belong to provided country - invalid lines: %s'), $zips_validation[0]);
			if ($zips_validation[1])
				$errors[] = sprintf($this->l('ZIP / Postal code can not be defined for country * so specific country must be provided - invalid lines: %s'), $zips_validation[1]);
		}

		$weight_from_validation = $this->validateCSVWeightFrom($csv_data, $csv_data_count);
		if ($weight_from_validation !== true)
			$errors[] = sprintf($this->l('Weight (From) must be real number >= 0 - invalid lines: %s'), $weight_from_validation);

		$weight_to_validation = $this->validateCSVWeightTo($csv_data, $csv_data_count);
		if ($weight_to_validation !== true)
			$errors[] = sprintf($this->l('Weight (To) must be real number > 0 than weight (From) - invalid lines: %s'), $weight_to_validation);

		$shipping_price_validation = $this->validateCSVShippingPrices($csv_data, $csv_data_count);
		if ($shipping_price_validation !== true)
			$errors[] = sprintf($this->l('Shipping price must be real number >= 0 - invalid lines: %s'), $shipping_price_validation);

		$shipping_percentage_validation = $this->validateCSVShippingPercentage($csv_data, $csv_data_count);
		if ($shipping_percentage_validation !== true)
		{
			if ($shipping_percentage_validation[0])
				$errors[] = sprintf($this->l('Shipping percentage must be real number >= 0 - invalid lines: %s'), $shipping_percentage_validation[0]);
			if ($shipping_percentage_validation[1])
				$errors[] = sprintf($this->l('One of shipping percentage or shipping price is allowed - invalid lines: %s'), $shipping_percentage_validation[1]);
		}

		$currencies_validation = $this->validateCSVCurrencies($csv_data, $csv_data_count);
		if ($currencies_validation !== true)
			$errors[] = sprintf($this->l('Currency does not exist in your system so please install it first - invalid lines: %s'), $currencies_validation);

		$method_validation = $this->validateCSVMethods($csv_data, $csv_data_count);
		if ($method_validation !== true)
			$methods = array(
					_DPDGEOPOST_CLASSIC_ID_,
					_DPDGEOPOST_LOCCO_ID_,
					_DPDGEOPOST_INTERNATIONAL_ID_,
					_DPDGEOPOST_REGIONAL_EXPRESS_ID_,
					_DPDGEOPOST_HUNGARY_ID_,
			);
			$errors[] = sprintf(
				$this->l('Method ID can be declared one value of: %1$d - invalid lines: %2$s'),
				implode(', ', $methods), $method_validation
			);

		$surcharge_validation = $this->validateCSVCODSurcharge($csv_data, $csv_data_count);
		if ($surcharge_validation !== true)
			$errors[] = sprintf($this->l('COD surcharge must be real number >= 0 - invalid lines: %s'), $surcharge_validation);

		$surcharge_percentage_validation = $this->validateCSVCODSurchargePercentage($csv_data, $csv_data_count);
		if ($surcharge_percentage_validation !== true)
		{
			if ($surcharge_percentage_validation[0])
				$errors[] = sprintf($this->l('COD surcharge must be real number >= 0 - invalid lines: %s'), $surcharge_percentage_validation[0]);
			if ($surcharge_percentage_validation[1])
				$errors[] = sprintf($this->l('One of COD surcharge or COD surcharge percentage is allowed - invalid lines: %s'), $surcharge_percentage_validation[1]);
		}

		$min_surcharge_validation = $this->validateCSVCODMinSurcharge($csv_data, $csv_data_count);
		if ($min_surcharge_validation !== true)
		{
			if ($min_surcharge_validation[0])
				$errors[] = sprintf($this->l('COD min. surcharge must be real number >= 0 - invalid lines: %s'), $min_surcharge_validation[0]);
			if ($min_surcharge_validation[1])
				$errors[] = sprintf($this->l('COD surcharge percentage must be defined in order to define COD min. surcharge - invalid lines: %s'), $min_surcharge_validation[1]);
		}

		if (!empty($errors))
			return $errors;
		return true;
	}

	private function validateCSVStructure($csv_data, $csv_data_count)
	{
		for ($i = 0; $i < $csv_data_count; $i++)
			if (!isset($csv_data[$i][DpdGeopostCSV::COLUMN_COD_MIN_SURCHARGE]))
				return false;
		return true;
	}

	private function validateCSVCountries($csv_data, $csv_data_count)
	{
		$wrong_countries = '';
		for ($i = 0; $i < $csv_data_count; $i++)
		{
			if (!$this->validateCSVCountry($csv_data[$i][DpdGeopostCSV::COLUMN_COUNTRY]))
				$wrong_countries .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';
		}

		$this->removeLastSymbolsFromString($wrong_countries);

		return empty($wrong_countries) ? true : $wrong_countries;
	}

	private function validateCSVCountry($iso_code)
	{
		return $iso_code === '*' || Validate::isLanguageIsoCode($iso_code) && Country::getByIso($iso_code);
	}

	private function validateCSVRegions($csv_data, $csv_data_count)
	{
		$wrong_regions = '';
		$wrong_regions_country = '';

		for ($i = 0; $i < $csv_data_count; $i++)
		{
			if ($this->validateCSVCountry($csv_data[$i][DpdGeopostCSV::COLUMN_COUNTRY]) && $csv_data[$i][DpdGeopostCSV::COLUMN_REGION] !== '*')
			{
				$id_state = (int)State::getIdByIso($csv_data[$i][DpdGeopostCSV::COLUMN_REGION]);

				if ($csv_data[$i][DpdGeopostCSV::COLUMN_COUNTRY] === '*')
					$wrong_regions .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';
				else
				{
					$id_country = (int)Country::getByIso($csv_data[$i][DpdGeopostCSV::COLUMN_COUNTRY]);
					$state = new State((int)$id_state);
					if ($state->id_country != $id_country)
						$wrong_regions_country .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';
				}
			}
		}

		$this->removeLastSymbolsFromString($wrong_regions);
		$this->removeLastSymbolsFromString($wrong_regions_country);

		return empty($wrong_regions) && empty($wrong_regions_country) ? true : array($wrong_regions, $wrong_regions_country);
	}

	private function validateCSVZips($csv_data, $csv_data_count)
	{
		$wrong_zips = '';
		$wrong_zips_detected = '';

		for ($i = 0; $i < $csv_data_count; $i++)
		{
			if ($this->validateCSVCountry($csv_data[$i][DpdGeopostCSV::COLUMN_COUNTRY]))
			{
				if ($csv_data[$i][DpdGeopostCSV::COLUMN_COUNTRY] === '*')
				{
					if ($csv_data[$i][DpdGeopostCSV::COLUMN_ZIP] !== '*')
						$wrong_zips_detected .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';
				}
				elseif ($csv_data[$i][DpdGeopostCSV::COLUMN_ZIP] !== '*')
				{
					$id_country = (int)Country::getByIso($csv_data[$i][DpdGeopostCSV::COLUMN_COUNTRY]);
					if (!$this->checkCSVZip($id_country, $csv_data[$i][DpdGeopostCSV::COLUMN_ZIP]))
						$wrong_zips .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';
				}
			}
		}

		$this->removeLastSymbolsFromString($wrong_zips);
		$this->removeLastSymbolsFromString($wrong_zips_detected);

		return empty($wrong_zips) && empty($wrong_zips_detected) ? true : array($wrong_zips, $wrong_zips_detected);
	}

	private function checkCSVZip($id_country, $iso_code)
	{
		$country = new Country($id_country);
		return $country->checkZipCode($iso_code);
	}

	private function validateCSVWeightFrom($csv_data, $csv_data_count)
	{
		$wrong_weights = '';
		for ($i = 0; $i < $csv_data_count; $i++)
			if (!Validate::isUnsignedFloat($csv_data[$i][DpdGeopostCSV::COLUMN_WEIGHT_FROM]))
				$wrong_weights .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';

		$this->removeLastSymbolsFromString($wrong_weights);

		return empty($wrong_weights) ? true : $wrong_weights;
	}

	private function validateCSVWeightTo($csv_data, $csv_data_count)
	{
		$wrong_weights = '';
		for ($i = 0; $i < $csv_data_count; $i++)
			if (!Validate::isUnsignedFloat($csv_data[$i][DpdGeopostCSV::COLUMN_WEIGHT_TO]) ||
				$csv_data[$i][DpdGeopostCSV::COLUMN_WEIGHT_TO] < $csv_data[$i][DpdGeopostCSV::COLUMN_WEIGHT_FROM]
			)
				$wrong_weights .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';

		$this->removeLastSymbolsFromString($wrong_weights);

		return empty($wrong_weights) ? true : $wrong_weights;
	}

	private function validateCSVShippingPrices($csv_data, $csv_data_count)
	{
		$wrong_prices = '';
		for ($i = 0; $i < $csv_data_count; $i++)
			if ($this->isNotNull($csv_data[$i][DpdGeopostCSV::COLUMN_SHIPPING_PRICE]) &&
				!Validate::isUnsignedFloat($csv_data[$i][DpdGeopostCSV::COLUMN_SHIPPING_PRICE])
			)
				$wrong_prices .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';

		$this->removeLastSymbolsFromString($wrong_prices);

		return empty($wrong_prices) ? true : $wrong_prices;
	}

	private function isNotNull($variable)
	{
		return (bool)($variable !== '');
	}

	private function validateCSVShippingPercentage($csv_data, $csv_data_count)
	{
		$wrong_percentages = '';
		$wrongly_defined_percentages = '';

		for ($i = 0; $i < $csv_data_count; $i++)
			if ($this->isNotNull($csv_data[$i][DpdGeopostCSV::COLUMN_SHIPPING_PRICE]) &&
				$this->isNotNull($csv_data[$i][DpdGeopostCSV::COLUMN_SHIPPING_PERCENTAGE]) ||
				!$this->isNotNull($csv_data[$i][DpdGeopostCSV::COLUMN_SHIPPING_PRICE]) &&
				!$this->isNotNull($csv_data[$i][DpdGeopostCSV::COLUMN_SHIPPING_PERCENTAGE])
			)
				$wrongly_defined_percentages .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';
			elseif ($this->isNotNull($csv_data[$i][DpdGeopostCSV::COLUMN_SHIPPING_PERCENTAGE]) &&
				!Validate::isUnsignedFloat($csv_data[$i][DpdGeopostCSV::COLUMN_SHIPPING_PERCENTAGE])
			)
				$wrong_percentages .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';

		$this->removeLastSymbolsFromString($wrong_percentages);
		$this->removeLastSymbolsFromString($wrongly_defined_percentages);

		return empty($wrong_percentages) && empty($wrongly_defined_percentages) ? true : array($wrong_percentages, $wrongly_defined_percentages);
	}

	private function validateCSVCurrencies($csv_data, $csv_data_count)
	{
		if (version_compare(_PS_VERSION_, '1.5', '<'))
			$currencies = Currency::getCurrencies();
		else
			$currencies = Currency::getCurrenciesByIdShop((int)$this->context->shop->id);

		$available_currencies = array();
		foreach ($currencies as $data)
			$available_currencies[] = $data['iso_code'];

		$wrong_currencies = '';
		for ($i = 0; $i < $csv_data_count; $i++)
			if (!in_array($csv_data[$i][DpdGeopostCSV::COLUMN_CURRENCY], $available_currencies))
				$wrong_currencies .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';

		$this->removeLastSymbolsFromString($wrong_currencies);

		return empty($wrong_currencies) ? true : $wrong_currencies;
	}

	private function validateCSVMethods($csv_data, $csv_data_count)
	{
		$available_methods = array(
				_DPDGEOPOST_CLASSIC_ID_,
				_DPDGEOPOST_LOCCO_ID_,
				_DPDGEOPOST_INTERNATIONAL_ID_,
				_DPDGEOPOST_REGIONAL_EXPRESS_ID_,
				_DPDGEOPOST_HUNGARY_ID_,
				'*'
		);
		$wrong_methods = '';
		for ($i = 0; $i < $csv_data_count; $i++)
			if (!in_array($csv_data[$i][DpdGeopostCSV::COLUMN_METHOD_ID], $available_methods))
				$wrong_methods .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';

		$this->removeLastSymbolsFromString($wrong_methods);

		return empty($wrong_methods) ? true : $wrong_methods;
	}

	private function validateCSVCODSurcharge($csv_data, $csv_data_count)
	{
		$wrong_surcharges = '';
		for ($i = 0; $i < $csv_data_count; $i++)
			if ($this->isNotNull($csv_data[$i][DpdGeopostCSV::COLUMN_COD_SURCHARGE]) &&
				!Validate::isUnsignedFloat($csv_data[$i][DpdGeopostCSV::COLUMN_COD_SURCHARGE])
			)
				$wrong_surcharges .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';

		$this->removeLastSymbolsFromString($wrong_surcharges);

		return empty($wrong_surcharges) ? true : $wrong_surcharges;
	}

	private function validateCSVCODSurchargePercentage($csv_data, $csv_data_count)
	{
		$wrong_percentages = '';
		$wrong_percentages_defined = '';

		for ($i = 0; $i < $csv_data_count; $i++)
			if ($this->isNotNull($csv_data[$i][DpdGeopostCSV::COLUMN_COD_SURCHARGE_PERCENTAGE]))
			{
				if (!Validate::isUnsignedFloat($csv_data[$i][DpdGeopostCSV::COLUMN_COD_SURCHARGE_PERCENTAGE]))
					$wrong_percentages .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';
				elseif ($this->isNotNull($csv_data[$i][DpdGeopostCSV::COLUMN_COD_SURCHARGE]))
					$wrong_percentages_defined .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';
			}

		$this->removeLastSymbolsFromString($wrong_percentages);
		$this->removeLastSymbolsFromString($wrong_percentages_defined);

		return empty($wrong_percentages) && empty($wrong_percentages_defined) ? true : array($wrong_percentages, $wrong_percentages_defined);
	}

	private function validateCSVCODMinSurcharge($csv_data, $csv_data_count)
	{
		$wrong_min_surcharges = '';
		$wrong_min_surcharges_defined = '';

		for ($i = 0; $i < $csv_data_count; $i++)
			if ($this->isNotNull($csv_data[$i][DpdGeopostCSV::COLUMN_COD_MIN_SURCHARGE]))
			{
				if (!Validate::isUnsignedFloat($csv_data[$i][DpdGeopostCSV::COLUMN_COD_MIN_SURCHARGE]))
					$wrong_min_surcharges .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';
				elseif (!$this->isNotNull($csv_data[$i][DpdGeopostCSV::COLUMN_COD_SURCHARGE_PERCENTAGE]))
					$wrong_min_surcharges_defined .= ($i + self::DEFAULT_FIRST_LINE_INDEX).', ';
			}

		$this->removeLastSymbolsFromString($wrong_min_surcharges);
		$this->removeLastSymbolsFromString($wrong_min_surcharges_defined);

		return empty($wrong_min_surcharges) && empty($wrong_min_surcharges_defined) ? true : array($wrong_min_surcharges, $wrong_min_surcharges_defined);
	}

	/* Usually used to remove last space and comma from a error message */
	private function removeLastSymbolsFromString(&$string, $symbols_count = 2)
	{
		if (!empty($string))
			$string = Tools::substr($string, 0, ((-1) * $symbols_count));
	}

	private function readCSVData()
	{
		if ($_FILES[DpdGeopostCSV::CSV_FILE]['error'])
			return false;

		if (!in_array($_FILES[DpdGeopostCSV::CSV_FILE]['type'], $this->csv_available_formats))
			return false;

		$csv_data = array();
		$row = 0;
		if (($handle = fopen($_FILES[DpdGeopostCSV::CSV_FILE]['tmp_name'], 'r')) !== FALSE)
		{
			while (($data = fgetcsv($handle, 1000, _DPDGEOPOST_CSV_DELIMITER_)) !== FALSE)
			{
				if (!$data) continue;
				$csv_data_line = array();
				$row++;
				if ($row == 1)
					continue;
				$num = count($data);
				$row++;
				for ($i = 0; $i < $num; $i++)
					$csv_data_line[] = $data[$i];
				$csv_data[] = $csv_data_line;
			}
			fclose($handle);
		}
		return $csv_data;
	}

	private function saveCSVData($csv_data)
	{
		if (!DpdGeopostCSV::deleteAllData())
			return false;

		$success = true;

		foreach ($csv_data as $data)
		{
			$csv = new DpdGeopostCSV();
			$csv->id_shop 					= (int)$this->context->shop->id;
			$csv->country 					= $data[DpdGeopostCSV::COLUMN_COUNTRY];
			$csv->region 					= $data[DpdGeopostCSV::COLUMN_REGION];
			$csv->zip 						= $data[DpdGeopostCSV::COLUMN_ZIP];
			$csv->weight_from 				= $data[DpdGeopostCSV::COLUMN_WEIGHT_FROM];
			$csv->weight_to 				= $data[DpdGeopostCSV::COLUMN_WEIGHT_TO];
			$csv->shipping_price 			= $data[DpdGeopostCSV::COLUMN_SHIPPING_PRICE];
			$csv->shipping_price_percentage = $data[DpdGeopostCSV::COLUMN_SHIPPING_PERCENTAGE];
			$csv->currency 					= $data[DpdGeopostCSV::COLUMN_CURRENCY];
			$csv->method_id 				= $data[DpdGeopostCSV::COLUMN_METHOD_ID];
			$csv->cod_surcharge 			= $data[DpdGeopostCSV::COLUMN_COD_SURCHARGE];
			$csv->cod_surcharge_percentage 	= $data[DpdGeopostCSV::COLUMN_COD_SURCHARGE_PERCENTAGE];
			$csv->cod_min_surcharge 		= $data[DpdGeopostCSV::COLUMN_COD_MIN_SURCHARGE];
			$success &= $csv->save();
		}

		return $success;
	}
}
