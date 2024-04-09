<?php
class CustomerAddressFormatter extends CustomerAddressFormatterCore
{
    private $country;

    private $translator;

    private $availableCountries;

    private $definition;

    public function __construct( Country $country, \Symfony\Component\Translation\TranslatorInterface $translator, array $availableCountries ) {
		$this->country = $country;
		$this->translator = $translator;
		$this->availableCountries = $availableCountries;
		$this->definition = Address::$definition['fields'];
		parent::__construct( $country, $translator, $availableCountries );
	}

    public function getFormat()
	{
		$fields = AddressFormat::getOrderedAddressFields(
			$this->country->id,
			true,
			true
		);
		$required = array_flip(AddressFormat::getFieldsRequired());
		$format = [
			'id_address' => (new FormField())
				->setName('id_address')
				->setType('hidden'),
			'id_customer' => (new FormField())
				->setName('id_customer')
				->setType('hidden'),
			'back' => (new FormField())
				->setName('back')
				->setType('hidden'),
			'token' => (new FormField())
				->setName('token')
				->setType('hidden'),
			'alias' => (new FormField())
				->setName('alias')
				->setLabel(
					$this->getFieldLabel('alias')
				),
		];
		require_once(_PS_MODULE_DIR_ . '/dpdgeopost/models/Pudo.php');
		$dpdGeoPostPudo = new DpdGeopostPudo();



		foreach ($fields as $field) {
			$formField = new FormField();
			$formField->setName($field);
			$fieldParts = explode(':', $field, 2);
			if (count($fieldParts) === 1) {
				if ($field === 'postcode') {
					if ($this->country->need_zip_code) {
						$formField->setRequired(true);
					}
				} elseif ($field === 'phone') {
					$formField->setType('tel');
					$formField->setRequired(true);
				}
				switch($field) {
					case 'dpd_country':

						break;
					case 'dpd_state':

						break;
					case 'dpd_site':

						break;
					case 'dpd_street':

						break;
					case 'dpd_complex':

						break;
					case 'dpd_block':


						break;
					case 'dpd_office':
						$officesOptions = $dpdGeoPostPudo->listOfficesDropDownOptions(false, $this->country->id);
						$formField->setType('select');
						$formField->setLabel('Pickup Office');
						$formField->setAvailableValues($officesOptions);
						break;
					case 'dpd_postcode':


						break;
				}
			} elseif (count($fieldParts) === 2) {
				list($entity, $entityField) = $fieldParts;
				$formField->setType('select');
				$formField->setName('id_' . strtolower($entity));
				if ($entity === 'Country') {
					$formField->setType('countrySelect');
					$formField->setValue($this->country->id);
					foreach ($this->availableCountries as $country) {
						$formField->addAvailableValue(
							$country['id_country'],
							$country[$entityField]
						);
					}
				} elseif ($entity === 'State') {
					if ($this->country->contains_states) {
						$states = State::getStatesByIdCountry($this->country->id, true);
						foreach ($states as $state) {
							$formField->addAvailableValue(
								$state['id_state'],
								$state[$entityField]
							);
						}
						$formField->setRequired(true);
					}
				}
			}
			$formField->setLabel($this->getFieldLabel($field));
			if (!$formField->isRequired()) {
				$formField->setRequired(
					array_key_exists($field, $required)
				);
			}
			$format[$formField->getName()] = $formField;
		}
		return $this->addConstraints(
			$this->addMaxLength(
				$format
			)
		);
	}

    public function setCountry(Country $country)
	{
		$this->country = $country;
		return $this;
	}

    public function getCountry()
	{
		return $this->country;
	}

    private function addConstraints(array $format)
	{
		foreach ($format as $field) {
			if (!empty($this->definition[$field->getName()]['validate'])) {
				$field->addConstraint(
					$this->definition[$field->getName()]['validate']
				);
			}
		}
		return $format;
	}

    private function addMaxLength(array $format)
	{
		foreach ($format as $field) {
			if (!empty($this->definition[$field->getName()]['size'])) {
				$field->setMaxLength(
					$this->definition[$field->getName()]['size']
				);
			}
		}
		return $format;
	}

    private function getFieldLabel($field)
	{
		$field = explode(':', $field)[0];
		switch ($field) {
			case 'alias':
				return $this->translator->trans('Alias', [], 'Shop.Forms.Labels');
			case 'firstname':
				return $this->translator->trans('First name', [], 'Shop.Forms.Labels');
			case 'lastname':
				return $this->translator->trans('Last name', [], 'Shop.Forms.Labels');
			case 'address1':
				return $this->translator->trans('Address', [], 'Shop.Forms.Labels');
			case 'address2':
				return $this->translator->trans('Address Complement', [], 'Shop.Forms.Labels');
			case 'postcode':
				return $this->translator->trans('Zip/Postal Code', [], 'Shop.Forms.Labels');
			case 'city':
				return $this->translator->trans('City', [], 'Shop.Forms.Labels');
			case 'Country':
				return $this->translator->trans('Country', [], 'Shop.Forms.Labels');
			case 'State':
				return $this->translator->trans('State', [], 'Shop.Forms.Labels');
			case 'phone':
				return $this->translator->trans('Phone', [], 'Shop.Forms.Labels');
			case 'phone_mobile':
				return $this->translator->trans('Mobile phone', [], 'Shop.Forms.Labels');
			case 'company':
				return $this->translator->trans('Company', [], 'Shop.Forms.Labels');
			case 'vat_number':
				return $this->translator->trans('VAT number', [], 'Shop.Forms.Labels');
			case 'dni':
				return $this->translator->trans('Identification number', [], 'Shop.Forms.Labels');
			case 'other':
				return $this->translator->trans('Other', [], 'Shop.Forms.Labels');

			case 'dpd_office':
				return $this->translator->trans('DPD PickUp Office', [], 'Shop.Forms.Labels');
			case 'dpd_postcode':
				return $this->translator->trans('Postcode', [], 'Shop.Forms.Labels');
			case 'dpd_block':
				return $this->translator->trans('Block', [], 'Shop.Forms.Labels');
			case 'dpd_complex':
				return $this->translator->trans('Complex', [], 'Shop.Forms.Labels');
			case 'dpd_street':
				return $this->translator->trans('Street', [], 'Shop.Forms.Labels');
			case 'dpd_site':
				return $this->translator->trans('City', [], 'Shop.Forms.Labels');
			case 'dpd_state':
				return $this->translator->trans('State', [], 'Shop.Forms.Labels');
			case 'dpd_country':
				return $this->translator->trans('Country', [], 'Shop.Forms.Labels');
			default:
				return $field;
		}
	}
}