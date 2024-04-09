
	<form id="defaultForm form-horizontal" class="defaultForm" action="{$saveAction|escape:'htmlall':'UTF-8'}&menu=configuration"
		method="post" enctype="multipart/form-data">

		<div class="panel" id="fieldset_0">

			<div class="panel-heading">
				<i class="icon-cogs"></i>{l s='Web Services' mod='dpdgeopost'}
			</div>

			<div class="form-wrapper">
				<div class="form-group">

						<input id="production_ws_url" type="hidden"
							   name="{DpdGeopostConfiguration::PRODUCTION_URL|escape:'htmlall':'UTF-8'}"
							   value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::PRODUCTION_URL|escape:'htmlall':'UTF-8', $settings->ws_production_url|escape:'htmlall':'UTF-8')}" />
						<label>
							{l s='Web Service Username:' mod='dpdgeopost'}
							<sup>{l s='*' mod='dpdgeopost'}</sup>
						</label>


						<input type="text" name="{DpdGeopostConfiguration::USERNAME|escape:'htmlall':'UTF-8'}" class="form-control"
							   value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::USERNAME|escape:'htmlall':'UTF-8', $settings->ws_username|escape:'htmlall':'UTF-8')}" />

						<small  class="form-text text-muted">
							{l s='Enter your web services username. If you forgot your username please contact DPD GeoPost.'
							mod='dpdgeopost'}
						</small >


				</div>

				<div class="form-group">
					<label>
						{l s='Web Service Password:' mod='dpdgeopost'}
						<sup>{l s='*' mod='dpdgeopost'}</sup>
					</label>

					<input type="text" name="{DpdGeopostConfiguration::PASSWORD|escape:'htmlall':'UTF-8'}" class="form-control"
					   value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::PASSWORD|escape:'htmlall':'UTF-8', $settings->ws_password|escape:'htmlall':'UTF-8')}" />

					<small  class="form-text text-muted">
							{l s='Enter WebServices password. If you forgot your password please contact DPD GeoPost.'
							mod='dpdgeopost'}
					</small >

				</div>

				<div class="form-group">
					<label>
						{l s='Web Service Connection Timeout:' mod='dpdgeopost'}
					</label>

				<input type="text" name="{DpdGeopostConfiguration::TIMEOUT|escape:'htmlall':'UTF-8'}" class="form-control"
					   value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::TIMEOUT|escape:'htmlall':'UTF-8', $settings->ws_timeout|escape:'htmlall':'UTF-8')}" />
					<small  class="form-text text-muted">
					{l s='Set a timeout for connecting to the DPD web service in seconds. Default is 10s. 0 - no limitl'
					mod='dpdgeopost'}
					</small >
				</div>
			</div>

			<div class="separation"></div>

			<div class="margin-form">
				<p class="connection_message conf">
					{l s='Connected successfuly!' mod='dpdgeopost'}
				</p>
				<p class="connection_message error">
					{l s='Could not connect to a web service server. Error:' mod='dpdgeopost'} <span
							class="error_message"></span>
				</p>
			</div>

			<div class="margin-form">
				<input type="submit" class="btn btn-primary"
					   name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
					   value="{l s='Save' mod='dpdgeopost'}" />
				<input id="test_connection" type="button" class="btn btn-primary"
					   value="{l s='Test Connection' mod='dpdgeopost'}" />
			</div>

			<div class="small">
				<sup>{l s='*' mod='dpdgeopost'}</sup> {l s='Required field' mod='dpdgeopost'}
			</div>
		</div>

		<br />

		<a name="cod_selection_warning"></a>
		<div class="panel">
			<div class="panel-heading">
				{l s='Active Services' mod='dpdgeopost'}
			</div>
			<div class="panel" id="fieldset_0">
				<div class="form-wrapper">
					<div class="form-group">
						{foreach from=$availableServices key=serviceId item=serviceDetails}
							{if $serviceId !== 2432 && $serviceId !== 2323 &&  $serviceId !== 2412}
								<div class="form-check form-check-inline">
									<input id="active_services_classic" type="checkbox" name="{$serviceDetails['htmlName']}"
									   {if $serviceDetails['isChecked']}checked="checked"{/if} value="{$serviceId}" />

									<label>
										{$serviceDetails['nameEn']}
									</label>
									<small  class="form-text text-muted">
											({l s='Enable'} {$serviceDetails['nameEn']})
									</small >
								</div>
							{/if}
						{/foreach}
					</div>
				</div>
			</div>




			<div class="margin-form">
				<input type="submit" class="btn btn-primary"
					name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
					value="{l s='Save' mod='dpdgeopost'}" />
			</div>
			<div class="alert alert-primary" role="alert">
				{l s='Please note that after installation carriers will be created for each service. You can manage
				these carriers using standar PrestaShop configuration tools.' mod='dpdgeopost'}
				<span style="color:red">{l s='If you are using the WebService price calculation method, make sure the VAT is zero because the API prices inlcude it already.' mod='dpdgeopost'}</span>
			</div>
		</div>


		<div class="panel">
			<div class="panel-heading">
				{l s='Additional Services' mod='dpdgeopost'}
			</div>
			<div class="panel" id="fieldset_0">
				<div class="form-wrapper">
					<div class="form-group">
						<label>{l s='Print format' mod='dpdgeopost'}</label>
						<select id="print_format" name="{DpdGeopostConfiguration::DPD_PRINT_FORMAT|escape:'htmlall':'UTF-8'}" class="form-control" >
							<option value="">{l s='-' mod='dpdgeopost'}</option>

							<option value="{DpdGeopostConfiguration::DPD_PRINT_FORMAT_PDF}"
									{if DPDGeopost::getInputValue(DpdGeopostConfiguration::DPD_PRINT_FORMAT, $settings->print_format) == DpdGeopostConfiguration::DPD_PRINT_FORMAT_PDF} selected {/if} >
									{l s='pdf' mod='dpdgeopost'}
							</option>

							<option value="{DpdGeopostConfiguration::DPD_PRINT_FORMAT_ZPL}"
							{if DPDGeopost::getInputValue(DpdGeopostConfiguration::DPD_PRINT_FORMAT, $settings->print_format) == DpdGeopostConfiguration::DPD_PRINT_FORMAT_ZPL} selected {/if}>
								{l s='zpl' mod='dpdgeopost'}
							</option>


							<option value="{DpdGeopostConfiguration::DPD_PRINT_FORMAT_HTML}"
							{if DPDGeopost::getInputValue(DpdGeopostConfiguration::DPD_PRINT_FORMAT, $settings->print_format) ==  DpdGeopostConfiguration::DPD_PRINT_FORMAT_HTML} selected {/if}>
							{l s='html' mod='dpdgeopost'}
							</option>

						</select>

					</div>

					<div class="form-group">
						<label>{l s='Print paper size' mod='dpdgeopost'}</label>

						<select id="paper_size" name="{DpdGeopostConfiguration::DPD_PAPER_SIZE|escape:'htmlall':'UTF-8'}" class="form-control" >
							<option value="">{l s='-' mod='dpdgeopost'}</option>

							<option value="{DpdGeopostConfiguration::DPD_PAPER_SIZE_A6}"
							{if DPDGeopost::getInputValue(DpdGeopostConfiguration::DPD_PAPER_SIZE, $settings->paper_size) == DpdGeopostConfiguration::DPD_PAPER_SIZE_A6} selected {/if} >
							{l s='a6' mod='dpdgeopost'}
							</option>

							<option value="{DpdGeopostConfiguration::DPD_PAPER_SIZE_A4}"
							{if DPDGeopost::getInputValue(DpdGeopostConfiguration::DPD_PAPER_SIZE, $settings->paper_size) == DpdGeopostConfiguration::DPD_PAPER_SIZE_A4} selected {/if} >
							{l s='a4' mod='dpdgeopost'}
							</option>

							<option value="{DpdGeopostConfiguration::DPD_PAPER_SIZE_A4x4Ag}"
							{if DPDGeopost::getInputValue(DpdGeopostConfiguration::DPD_PAPER_SIZE, $settings->paper_size) == DpdGeopostConfiguration::DPD_PAPER_SIZE_A4x4Ag} selected {/if} >
							{l s='a4(4xa6)' mod='dpdgeopost'}
							</option>

						</select>

					</div>

					<div class="form-group">
						<label>{l s='Defines the option to be used' mod='dpdgeopost'}</label>

						<select id="payment_options" name="{DpdGeopostConfiguration::DPD_PAYMENT_OPTIONS|escape:'htmlall':'UTF-8'}" class="form-control" >
							<option value="">{l s='-' mod='dpdgeopost'}</option>

							<option value="{DpdGeopostConfiguration::DPD_PAYMENT_OPTIONS_OPEN}"
							{if DPDGeopost::getInputValue(DpdGeopostConfiguration::DPD_PAYMENT_OPTIONS, $settings->payment_options) == DpdGeopostConfiguration::DPD_PAYMENT_OPTIONS_OPEN} selected {/if} >
							{l s='Open parcels before payment.' mod='dpdgeopost'}
							</option>

							<option value="{DpdGeopostConfiguration::DPD_PAYMENT_OPTIONS_TEST}"
							{if DPDGeopost::getInputValue(DpdGeopostConfiguration::DPD_PAYMENT_OPTIONS, $settings->payment_options) == DpdGeopostConfiguration::DPD_PAYMENT_OPTIONS_TEST} selected {/if} >
							{l s='Test parcels before payment.' mod='dpdgeopost'}
							</option>

						</select>

					</div>

					<div class="form-group">
						<label>{l s='Defines who pays the return shipment.' mod='dpdgeopost'}</label>

						<select id="return_pay" name="{DpdGeopostConfiguration::DPD_RETURN_PAY|escape:'htmlall':'UTF-8'}" class="form-control" >
							<option value="">{l s='-' mod='dpdgeopost'}</option>

							<option value="{DpdGeopostConfiguration::DPD_RETURN_PAY_SENDER}"
							{if DPDGeopost::getInputValue(DpdGeopostConfiguration::DPD_RETURN_PAY, $settings->return_pay) == DpdGeopostConfiguration::DPD_RETURN_PAY_SENDER} selected {/if} >
							{l s='Sender' mod='dpdgeopost'}
							</option>

							<option value="{DpdGeopostConfiguration::DPD_RETURN_PAY_RECIPIENT}"
							{if DPDGeopost::getInputValue(DpdGeopostConfiguration::DPD_RETURN_PAY,  $settings->return_pay) == DpdGeopostConfiguration::DPD_RETURN_PAY_RECIPIENT} selected {/if} >
							{l s='Recipient' mod='dpdgeopost'}
							</option>

						</select>

					</div>

				</div>
				<div class="margin-form">
					<input type="submit" class="btn btn-primary"
						   name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
						   value="{l s='Save' mod='dpdgeopost'}" />
				</div>

			</div>





		</div>

		<div class="panel">
			<div class="panel-heading">
				{l s='Sender & Payer' mod='dpdgeopost'}
			</div>
			<div class="margin-form">
				<div class="form-wrapper">
					<div class="form-group">
						<label for="sender_address_id">
							{l s='Sender Address Id:' mod='dpdgeopost'}
							<sup>{l s='*' mod='dpdgeopost'}</sup>
						</label>
						<select id="sender_address_id" name="{DpdGeopostConfiguration::SENDER_ID|escape:'htmlall':'UTF-8'}" class="form-control" >
							<option value="">{l s='-' mod='dpdgeopost'}</option>
							{foreach from=$contractoptions item=contract}
								<option value="{$contract.clientId|escape:'htmlall':'UTF-8'}"
									{if DPDGeopost::getInputValue(DpdGeopostConfiguration::SENDER_ID|escape:'htmlall':'UTF-8', $settings->sender_id|escape:'htmlall':'UTF-8') == $contract.clientId} selected {/if}
								 >
									{$contract.objectName} {$contract.address.fullAddressString|escape:'htmlall':'UTF-8'}
								</option>
							{/foreach}
						</select>

						<small  class="form-text text-muted">
							{l s='You should find your sender address id in contract with DPD GeoPost.' mod='dpdgeopost'}
						</small>
					</div>
					<div class="form-group">
						<label for="{DpdGeopostConfiguration::SENDER_DROPOFF_OFFICE}">
							{l s='OOH DPD RO Network:' mod='dpdgeopost'}
						</label>
						<select id="{DpdGeopostConfiguration::SENDER_DROPOFF_OFFICE}" name="{DpdGeopostConfiguration::SENDER_DROPOFF_OFFICE}"  class="form-control">
							<option value="">{l s='-' mod='dpdgeopost'}</option>
							{foreach from=$dropoffoptions item=office}
								<option value="{$office.id|escape:'htmlall':'UTF-8'}"
									{if $selectedDropOff==$office.id } selected {/if}
								>
										{$office.name|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						</select>

					</div>

					<div class="form-group">
						<label>
							{l s='Send insurance value:' mod='dpdgeopost'}
						</label>

						<input id="sende_insurance_value_yes" type="radio"
							   name="{DpdGeopostConfiguration::SEND_INSURANCE_VALUE}" {if
							   DPDGeopost::getInputValue(DpdGeopostConfiguration::SEND_INSURANCE_VALUE,
							   $settings->send_insurance_value) == 1}checked="checked"{/if} value="1" />
						<label class="t" for="sende_insurance_value_yes">
							{l s='Yes' mod='dpdgeopost'}
						</label>
						<input id="sende_insurance_value_no" type="radio" name="{DpdGeopostConfiguration::SEND_INSURANCE_VALUE}"
							   {if DPDGeopost::getInputValue(DpdGeopostConfiguration::SEND_INSURANCE_VALUE,
							   $settings->send_insurance_value) == 0}checked="checked"{/if} value="0" />
						<label class="t" for="sende_insurance_value_no">
							{l s='No' mod='dpdgeopost'}
						</label>
						<p class="preference_description">
							{l s='Select "Yes" if you want to send the insurance value when creating the shipment.'
							mod='dpdgeopost'}
						</p>
					</div>


					<div class="form-group">
						<label>
							{l s='Defines who pays in shipment'}
						</label>
						<div class="margin-form">
							<select id="{DpdGeopostConfiguration::COURIER_SERVICE_PAYER}"  name="{DpdGeopostConfiguration::COURIER_SERVICE_PAYER}">
								{foreach from=DpdGeopostConfiguration::getCourierServicePayerList() item=payer }
									<option value="{$payer.id}"  {if DpdGeopostConfiguration::getSettingStatic(DpdGeopostConfiguration::COURIER_SERVICE_PAYER, DpdGeopostConfiguration::COURIER_SERVICE_PAYER_SENDER ) ==$payer.id } selected {/if} >{$payer.value}</option>
								{/foreach}
							</select>
							<input  value="{DpdGeopostConfiguration::getSettingStatic(DpdGeopostConfiguration::COURIER_SERVICE_PAYER_THIRD_PARTY_ID, '' )}"   type="text" id="{DpdGeopostConfiguration::COURIER_SERVICE_PAYER_THIRD_PARTY_ID}" name="{DpdGeopostConfiguration::COURIER_SERVICE_PAYER_THIRD_PARTY_ID}" >
						</div>
					</div>
				</div>
			</div>

			<div class="margin-form">
				<input type="submit" class="btn btn-primary"
					   name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
					   value="{l s='Save' mod='dpdgeopost'}" />
			</div>
		</div>
		<br />

		<div class="panel">
			<div class="panel-heading">
				{l s='Price calculation' mod='dpdgeopost'}
			</div>
			<div class="margin-form">
				<div class="form-wrapper">
					<div class="margin-form">

						<label>
							{l s='Shipping price calculation method:' mod='dpdgeopost'}
						</label>
					</div>
					<div class="form-group">
						<input id="price_calculation_webservices" type="radio"
							   name="{DpdGeopostConfiguration::PRICE_CALCULATION}" {if
							   DPDGeopost::getInputValue(DpdGeopostConfiguration::PRICE_CALCULATION,
							   $settings->price_calculation_method) == DpdGeopostConfiguration::WEB_SERVICES}checked="checked"{/if}
							   value="{DpdGeopostConfiguration::WEB_SERVICES}" />


						<label class="t" for="price_calculation_webservices">
							{l s='Web Services' mod='dpdgeopost'}
						</label>

						<input id="price_calculation_prestashop" type="radio"
							   name="{DpdGeopostConfiguration::PRICE_CALCULATION}" {if
							   DPDGeopost::getInputValue(DpdGeopostConfiguration::PRICE_CALCULATION,
							   $settings->price_calculation_method) == DpdGeopostConfiguration::PRESTASHOP}checked="checked"{/if}
							   value="{DpdGeopostConfiguration::PRESTASHOP}" />

						<label class="t" for="price_calculation_prestashop">
							{l s='Prestashop Rules' mod='dpdgeopost'}
						</label>

						<div class="alert alert-primary" role="alert">
							<span style="color:red">
								{l s="WebService price calculation method inlcudes VAT" mod='dpdgeopost'}
							</span>
						</div>

					</div>

					<div class="margin-form">

						<label>
							{l s='Packaging Method:' mod='dpdgeopost'}
						</label>
					</div>
					<div class="form-group">
						<input id="packaging_method_all_products" type="radio"
							   name="{DpdGeopostConfiguration::PACKING_METHOD}" {if
							   DPDGeopost::getInputValue(DpdGeopostConfiguration::PACKING_METHOD,
							   $settings->packaging_method) == DpdGeopostConfiguration::ONE_PRODUCT}checked="checked"{/if}
							   value="{DpdGeopostConfiguration::ONE_PRODUCT}" />


						<label class="t" for="packaging_method_all_products">
							{l s='One parcel for one products' mod='dpdgeopost'}
						</label>

						<input id="packaging_method_one_product" type="radio"
							   name="{DpdGeopostConfiguration::PACKING_METHOD}" {if
							   DPDGeopost::getInputValue(DpdGeopostConfiguration::PACKING_METHOD,
							   $settings->packaging_method) == DpdGeopostConfiguration::ALL_PRODUCTS}checked="checked"{/if}
							   value="{DpdGeopostConfiguration::ALL_PRODUCTS}" />

						<label class="t" for="packaging_method_one_product">
							{l s='One parcel for all products' mod='dpdgeopost'}
						</label>
					</div>
				</div>
			</div>
			<div class="clear"></div>
				<input type="hidden" name="{DpdGeopostConfiguration::COUNTRY|escape:'htmlall':'UTF-8'}" value="{DpdGeopostConfiguration::OTHER}">
				<input id="price_calculation_webservices" type="hidden"  name="{DpdGeopostConfiguration::PRICE_CALCULATION_INCLUDE_SHIPPING}" value="yes" />
				<input id="price_calculation_prestashop" type="hidden"  name="{DpdGeopostConfiguration::DPD_SHOW_NORMALIZATION_FORM}" value="no" />
				<!--<input id="packaging_method_one_product" type="hidden" name="{DpdGeopostConfiguration::PACKING_METHOD}" value="{DpdGeopostConfiguration::ALL_PRODUCTS}" />-->
			<div class="clear"></div>
			<div class="margin-form">
				<input type="submit" class="btn btn-primary"
					   name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
					   value="{l s='Save' mod='dpdgeopost'}" />
			</div>
		</div>

		<br /><br />

		<div class="panel">
			<div class="panel-heading">
				{l s='Weight measurement units conversion' mod='dpdgeopost'}
			</div>

			<div class="form-wrapper">
				<div class="form-group">
					<label>
						{l s='System default weight units:' mod='dpdgeopost'} {Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'}
					</label>
				</div>
				<div class="form-group">
					<label>
						{l s='DPD weight units:' mod='dpdgeopost'} {$smarty.const._DPDGEOPOST_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}
					</label>
				</div>
				<div class="form-group">
					<label>
						{l s='Conversion rate:' mod='dpdgeopost'}
					</label>
					<div class="margin-form">
						<input id="weight_conversion_input" type="text"
							name="{DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8'}"
							value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8', $settings->weight_conversation_rate|escape:'htmlall':'UTF-8')}" />
						<sup>{l s='*' mod='dpdgeopost'}</sup>
						1 {Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'} = <span
							id="dpd_weight_unit">{DPDGeopost::getInputValue(DpdGeopostConfiguration::WEIGHT_CONVERSATION_RATE|escape:'htmlall':'UTF-8',
							$settings->weight_conversation_rate|escape:'htmlall':'UTF-8')}</span>
						{$smarty.const._DPDGEOPOST_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}
						<p class="preference_description">
							{l s='Conversion rate from system to DPD weight units. If your system uses the same weight units as
							DPD then leave this field blank.' mod='dpdgeopost'}
						</p>
					</div>
				</div>
				<div class="form-group">
					<label>
						{l s='Default weight:' mod='dpdgeopost'}
					</label>
					<div class="margin-form">
						<input id="default_weight" type="number"
							   name="{DpdGeopostConfiguration::WEIGHT_DEFAULT_VALUE|escape:'htmlall':'UTF-8'}"  class="form-control"
							   value="{DPDGeopost::getInputValue(DpdGeopostConfiguration::WEIGHT_DEFAULT_VALUE|escape:'htmlall':'UTF-8', $settings->weight_default_value|escape:'htmlall':'UTF-8')}" />
					</div>
				</div>
				<div class="margin-form">
					<input type="submit" class="btn btn-primary"
						name="{DpdGeopostConfigurationController::SETTINGS_SAVE_ACTION|escape:'htmlall':'UTF-8'}"
						value="{l s='Save' mod='dpdgeopost'}" />
				</div>

				<div class="small">
					<sup>{l s='*' mod='dpdgeopost'}</sup> {l s='Required field' mod='dpdgeopost'}
				</div>
			</div>
		</div>


	</form>