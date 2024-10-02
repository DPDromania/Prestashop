<script>
    var _DPD_GLOBAL_AJAX_ = '{$dpd_geopost_ajax_uri|escape:'htmlall':'UTF-8'}';
    var _DPD_TOKEN_ = '{$dpd_geopost_token|escape:'htmlall':'UTF-8'}';
    var dpd_geopost_ajax_uri = '{$dpd_geopost_ajax_uri|escape:'htmlall':'UTF-8'}';
    var dpd_geopost_token = '{$dpd_geopost_token|escape:'htmlall':'UTF-8'}';
    var dpd_geopost_id_shop = '{$dpd_geopost_id_shop|escape:'htmlall':'UTF-8'}';
    var dpd_geopost_id_lang = '{$dpd_geopost_id_lang|escape:'htmlall':'UTF-8'}';
    var dpd_geopost_weight_unit = '{$smarty.const._DPDGEOPOST_DEFAULT_WEIGHT_UNIT_|escape:'htmlall':'UTF-8'}';
    var id_order = '{Tools::getValue('id_order')|escape:'htmlall':'UTF-8'}';

    {if $streetLengthErrors}
    $(document).ready(function () {
        var shippingAddressHolder = $('#addressShipping');
        $('form', shippingAddressHolder).after('<div class="" style="color: red; padding:15px 0; ">{l s='DPD shipping carrier requirements: The length of address provided by your customer is too long.Please provide a maximum 70 characters length address(street field).Click Edit button.' mod='dpdgeopost'}</div>');
    });
    {/if}

    {if $dpdAddress->dpd_postcode_id && !$dpdAddress->relevance }
    $(document).ready(function () {
        var shippingAddressHolder = $('#addressShipping');
        $('form', shippingAddressHolder).after('<div class="" style="color: red; padding:15px 0; ">{l s='DPD shipping carrier detected an other postcode for current address: % s.You can validate this postcode before delivery by clicking the edit button.' mod='dpdgeopost'}</div>');
    });
    {/if}
</script>
{if isset($ps14) && $ps14}
    {assign var="total_shipping_tax_incl" value=Tools::convertPrice($order->total_paid, $order->id_currency, false)}
{else}
    {assign var="total_shipping_tax_incl" value=Tools::convertPrice($order->total_shipping_tax_incl,
    $order->id_currency, false)}
{/if}
<br/>
{$assets}
<a name="dpdgeopost_fieldset_identifier"></a>
<fieldset id="dpdgeopost">
    <legend>
        <img src="{$smarty.const._DPDGEOPOST_MODULE_URI_}logo.gif" width="16"
             height="16"> {l s='DPD GeoPost shipping information ' mod='dpdgeopost'}
    </legend>
    {if $settings->checkRequiredFields()}
        <style>

            .dpd-confirmation-confirmed {
                display: inline-block;
                color: #3c3d3b;
            }

            .dpd-confirmation-confirmed.waiting {
                color: #e22a27;
                font-weight: bold;
            }

            .dpd-confirmation-confirmed.waiting p {
                display: inline-block !important;
            }

            .dpd-confirmation-confirmed i {
                font-size: 18px;
            }

            .dpd-confirmation-confirmed p {
                display: inline-block;
                margin: 0;
            }

            .dpd-confirmation-confirmed p b {
                color: #e22a27;
            }

            .dpd-confirmation-confirmed button {
                color: #fff;
                background-color: #3c3d3b;
                border-color: #3c3d3b;
                background-image: none;
                border-radius: 0;
                margin: 0 0 0 10px;
                border: none;
                height: 22px;
                line-height: 22px;
                padding: 0 10px;
            }

            .dpd-confirmation-confirmed button:hover,
            .dpd-confirmation-confirmed button:focus {
                color: #fff;
                background-color: #232323;
                border-color: #232323;
                background-image: none;
                border-radius: 4px;
                margin: 0 0 0 10px;
                border: none;
                height: 22px;
                line-height: 22px;
                padding: 0 10px;
            }

            .dpd-confirmation-confirmed button,
            .dpd-confirmation-confirmed p span {
                display: none;
            }

            input:checked + .dpd-confirmation-confirmed button,
            input:checked + .dpd-confirmation-confirmed p span {
                display: inline-block;
            }

            body.dpd_loader_active {
                max-height: 100vh !important;
                overflow: hidden !important;
            }

            .dpd_loader {
                display: -webkit-box;
                display: -moz-box;
                display: -webkit-flex;
                display: -ms-flexbox;
                display: flex;
                -webkit-justify-content: center;
                justify-content: center;
                -webkit-align-items: center;
                align-items: center;
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                position: fixed;
                z-index: 99999999;
                top: 0;
                left: 0;
                background-color: rgba(0, 0, 0, 0.5);
            }

            .dpd_loader i {
                font-size: 50px;
                color: #fff;
                background-color: #e22a27;
                border-radius: 50%;
                padding: 15px;
            }

            .dpd-confirmation-address {
                width: 100%;
                height: auto;
                background-color: #f5f5f5;
                border: 1px solid #dddddd;
                padding: 0;
                margin: 10px 0;
                -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
                box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
                border-radius: 4px;
                overflow: hidden;
                font-family: 'Open Sans', sans-serif;
            }

            input:checked + .dpd-confirmation-address,
            input:checked + .dpd-confirmation-confirmed + .dpd-confirmation-address {
                display: inline-block;
            }

            .dpd-confirmation-address .dpd-confirmation-address-heading {
                display: inline-block;
                width: 100%;
                height: auto;
                margin: 0;
                padding: 0;
            }

            .dpd-confirmation-address .dpd-confirmation-address-heading .dpd-confirmation-address-nav {
                display: inline-block;
                width: 100%;
                height: auto;
                margin: 0;
                padding: 0;
                list-style: none;
                border-bottom: 1px solid #ddd;
            }

            .dpd-confirmation-address .dpd-confirmation-address-heading .dpd-confirmation-address-nav li {
                position: relative;
                display: inline-block;
                width: calc(50% - 45px);
                height: 40px;
                margin-bottom: -1px;
                padding: 0;
                text-align: center;
                float: left;
            }

            .dpd-confirmation-address .dpd-confirmation-address-heading .dpd-confirmation-address-nav li:first-child {
                width: 90px;
                border-right: 1px solid #ddd;
            }

            .dpd-confirmation-address .dpd-confirmation-address-heading .dpd-confirmation-address-nav li img {
                padding: 7.5px 15px;
                height: 39px;
            }

            .dpd-confirmation-address .dpd-confirmation-address-heading .dpd-confirmation-address-nav li button {
                display: inline-block;
                width: 100%;
                height: 40px;
                line-height: 40px;
                margin: 0;
                padding: 0 15px;
                color: #232323;
                font-weight: 600;
                font-size: 12px;
                background-color: transparent;
                border: none;
                box-shadow: none;
                outline: none;
            }

            .dpd-confirmation-address .dpd-confirmation-address-heading .dpd-confirmation-address-nav li button:hover {
                color: #fff;
                background-color: #232323;
                border-color: #232323;
            }

            .dpd-confirmation-address .dpd-confirmation-address-heading .dpd-confirmation-address-nav li.active button {
                color: #fff;
                background-color: #3c3d3b;
                border-color: #3c3d3b;
            }

            .dpd-confirmation-address .dpd-confirmation-address-heading .dpd-confirmation-address-nav li.active button:hover {} .dpd-confirmation-address .dpd-confirmation-address-body {
                display: inline-block;
                width: 100%;
                margin: 0;
                padding: 15px;
                border-radius: 0;
            }

            .dpd-confirmation-address .dpd-confirmation-address-body .dpd-confirmation-address-content {
                display: inline-block;
                width: 100%;
                height: auto;
                margin: 0;
                padding: 0;
            }

            .dpd-confirmation-address .dpd-confirmation-address-body .dpd-confirmation-address-content .dpd-confirmation-address-tab {
                display: none;
                width: 100%;
                height: auto;
                margin: 0;
                padding: 0;
            }

            .dpd-confirmation-address .dpd-confirmation-address-body .dpd-confirmation-address-content .dpd-confirmation-address-tab.active {
                display: inline-block;
            }

            .dpd-confirmation-address .dpd-confirmation-address-body .dpd-confirmation-address-content .dpd-confirmation-address-tab h4 {
                font-weight: 700;
                color: #666;
                font-size: 12px;
                line-height: 20px;
                margin: 0 0 5px;
            }

            .dpd-confirmation-address .dpd-confirmation-address-body .dpd-confirmation-address-content .dpd-confirmation-address-tab h4 span {
                display: inline-block;
                color: #e22a27;
                text-transform: capitalize;
            }

            .dpd-confirmation-address .dpd-confirmation-address-body .dpd-confirmation-address-content .dpd-confirmation-address-tab hr {
                display: inline-block;
                width: 100%;
                margin: 10px 0 0;
                background: transparent;
                border: none;
                float: left;
            }

            .dpd-confirmation-address .dpd-confirmation-address-body .dpd-confirmation-address-content .dpd-confirmation-address-tab .dpd-confirmation-address-select {
                display: block;
                width: 100%;
                height: 35px;
                padding: 0 15px;
                font-size: 12px;
                line-height: 33px;
                color: #555;
                background-color: #fff;
                background-image: none;
                border: 1px solid #ccc;
                border-radius: 4px;
                -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
                -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
                -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
                transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
            }

            .dpd-confirmation-address .dpd-confirmation-address-body .dpd-confirmation-address-content .dpd-confirmation-address-tab .dpd-confirmation-address-select.error {
                outline: none;
                border: 1px solid #e22a27;
            }

            .dpd-confirmation-address .dpd-confirmation-address-body .dpd-confirmation-address-content .dpd-confirmation-address-tab button {
                color: #fff;
                background-color: #e22a27;
                border-color: #e22a27;
                background-image: none;
                border-radius: 4px;
                margin: 0 !important;
                float: left;
                border: none;
                outline: none;
                font-weight: 600;
                font-size: 12px;
            }

            .dpd-confirmation-address .dpd-confirmation-address-body .dpd-confirmation-address-content .dpd-confirmation-address-tab button:hover {
                background-color: #a90034;
                border-color: #a90034;
            }

            .dpd-confirmation-address .dpd-confirmation-address-body .dpd-confirmation-address-content .dpd-confirmation-address-tab select {
                margin-top: 5px;
            }

            .dpd-disabled {
                display: none !important;
            }

            .dpd-confirmation-error {
                display: inline-block;
                color: #e22a27;
                font-weight: 600;
            }

            .dropdown-menu.typeahead {
                padding: 10px;
                -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
                box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
                border-radius: 4px;
                background-color: #f5f5f5;
                border: 1px solid #dddddd;
                font-family: 'Open Sans', sans-serif;
            }

            .dropdown-menu.typeahead li {
                display: inline-block;
                width: 100%;
                height: 30px;
                margin: 0 0 2px 0;
                padding: 0;
            }

            .dropdown-menu.typeahead li a {
                display: inline-block;
                width: 100%;
                height: 30px;
                line-height: 30px;
                margin: 0;
                padding: 0 10px;
                min-width: 150px;
                white-space: nowrap;
                color: #232323;
                background-color: #fff;
                background-image: none;
                border-radius: 0;
                margin: 0 !important;
                outline: none;
                font-weight: 600;
                font-size: 12px;
                border: 1px solid #dddddd;
            }

            .dropdown-menu.typeahead li a:hover {
                color: #fff;
                background-color: #232323;
            }

            .fragment-container {
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .fragment-container span {
                margin-right: 5px;
            }

            .fragment-container input {
                float: none;
                width: auto;
                max-width: 50px
            }

            .select2-container .select2-selection--single {
                width: 500px;
            }

        </style>
        <b>{l s='Entered Shipping address' mod='dpdgeopost'}</b>
        <br/>



                {$deliveryAddress->alias|escape:'htmlall':'UTF-8'} -
                {$deliveryAddress->address1|escape:'htmlall':'UTF-8'} {$deliveryAddress->address2|escape:'htmlall':'UTF-8'} {$deliveryAddress->postcode|escape:'htmlall':'UTF-8'}
                {$deliveryAddress->city|escape:'htmlall':'UTF-8'}{if !empty($deliveryAddress->state)}
                {$deliveryAddress->state|escape:'htmlall':'UTF-8'}{/if}
                , {$deliveryAddress->country|escape:'htmlall':'UTF-8'} <br />

                {if $selectedOffice}
                    Selected office: {$selectedOffice|escape:'htmlall':'UTF-8'}
                {/if}


        <div class="dpd-normalizer">
            <div class="dpd-confirmation-address">
                <input type="hidden" value="{$order->id}" class="dpd_id_order" />
                <input type="hidden" value="{$deliveryAddress->id}" class="dpdgeopost_id_address"  id="dpdgeopost_id_address" />

                <input type="hidden" value="{$deliveryAddress->dpd_shipment_type}" name="current_delivery_method" class="current_delivery_method" />
                <div class="dpd-confirmation-address-heading">
                    <ul class="dpd-confirmation-address-nav">
                        <li><img src="{$smarty.const._DPDGEOPOST_MODULE_URI_}logo.gif" alt="DPD"/></li>


                        <li class="{if $deliveryAddress->dpd_shipment_type == 'delivery'}active{/if}">
                            <button type="button" data-method="delivery" class="js-selected-delivery-tab-address">
                                Delivery to address
                            </button>
                        </li>

                        <li class="{if $deliveryAddress->dpd_shipment_type == 'pickup'}active{/if}">
                            <button type="button" data-method="pickup" class="js-selected-delivery-tab-address">
                                Delivery to Office/Automat
                            </button>
                        </li>

                    </ul>
                </div>

                <div class="dpd-confirmation-address-body">
                    <div class="dpd-confirmation-address-content">
                        <div data-method="delivery"
                             class="dpd-confirmation-address-tab  js-selected-delivery-content-address {if $deliveryAddress->dpd_shipment_type == 'delivery'}active{/if}">
                            <h4>Delivery to Address</h4>
                        </div>

                        <div data-method="pickup"
                             class="dpd-confirmation-address-tab js-selected-delivery-content-address {if $deliveryAddress->dpd_shipment_type == 'pickup'}active{/if}">
                            <h4>Delivery to office/automat</h4>
                        </div>

                        <div class="form-group row ">
                            <label class="col-md-2 form-control-label text-left">Country</label>
                            <div class="col-md-10">
                                <select class="form-control dpd_confirm_country" type="text" style="width: 500px">
                                    {if $deliveryAddress->dpd_country}
                                        <option value="{$deliveryAddress->dpd_country}" selected="selected">{$deliveryAddress->country}</option>
                                    {/if}
                                </select>
                            </div>
                        </div>

                        <div class="form-group row ">
                            <label class="col-md-2 form-control-label text-left">Region</label>
                            <div class="col-md-10">
                                <select class="form-control dpd_confirm_region" type="text" style="width: 500px">
                                    <option value="" selected="selected"></option>
                                    {foreach $states as $state }
                                        {if $deliveryAddress->city == $state}
                                            <option value="{$state}" selected="selected">{$state}</option>
                                        {else}
                                            <option value="{$state}">{$state}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </div>
                        </div>

                        <div class="form-group row ">
                            <label class="col-md-2 form-control-label text-left">City</label>

                            <div class="col-md-10">
                                <select class="form-control dpd_confirm_city" type="text"  style="width: 500px">
                                    {if $deliveryAddress->dpd_site}
                                        <option value="{$deliveryAddress->dpd_site}" selected="selected">{$deliveryAddress->city}</option>
                                    {/if}
                                </select>
                            </div>
                            <label class="col-md-2 form-control-label text-left"></label>
                            <div class="col-md-10 text-left js-municipality"
                                 style="text-align: left; display: inline-block;">
                                {if isset($ws_city)}
                                    {if isset($ws_city['municipality']) }
                                        {$ws_city['municipality']}
                                    {/if}
                                    ,
                                    {if isset($ws_city['region'])}
                                        {$ws_city['region']}
                                    {/if}
                                {/if}
                            </div>
                        </div>


                        <div data-method="delivery"
                             class="dpd-confirmation-address-tab  js-selected-delivery-content-address {if $deliveryAddress->dpd_shipment_type == 'delivery'}active{/if}">
                            <div class="form-group row ">
                                <label class="col-md-2 form-control-label text-left">Postcode</label>
                                <div class="col-md-10">

                                    <select class="form-control dpd_confirm_postcode" type="text"  style="width: 500px">
                                        {if $deliveryAddress->dpd_postcode}
                                            <option value="{$deliveryAddress->dpd_postcode}" selected="selected">{$deliveryAddress->dpd_postcode}</option>
                                        {/if}
                                    </select>

                                </div>
                            </div>

                            <div class="form-group row ">
                                <label class="col-md-2 form-control-label text-left">Address Str.</label>
                                <div class="col-md-10">
                                    <div class="form-group row ">
                                        <div class="col-md-6">
                                            <select class="form-control dpd_confirm_street"  style="width: 500px">
                                                {if count($foundStreets) == 1}
                                                    {foreach from=$foundStreets item=street }
                                                        <option value="{$street.id}" selected="selected"> {$street.typeEn} {$street.name} </option>
                                                    {/foreach}
                                                {/if}
                                            </select>

                                            {if count($foundStreets) > 1 }
                                                <div class="js-ambiguu-street">
                                                We found two similar streets. Please select the proper one in the field above. <br />
                                                {foreach from=$foundStreets item=street }
                                                    <b>{$street.typeEn} {$street.name}</b><br />
                                                {/foreach}
                                                </div>
                                            {/if}


                                        </div>
                                        <div class="col-md-2 fragment-container">
                                            <span>Nr.</span>
                                            <input class="form-control custom dpd_confirm_street_no"
                                                   placeholder="street no"
                                                   type="text" value="{if $streetNr}{$streetNr}{elseif isset($converted_address_nr)}{$converted_address_nr}{/if}">
                                        </div>
                                        <div class="col-md-2 fragment-container">
                                            <span>Bl.</span>
                                            <input class="form-control custom dpd_confirm_block_no"
                                                   placeholder="block no"
                                                   type="text" value="{if $streetBl}{$streetBl}{elseif isset($converted_address_b)}{$converted_address_bl}{/if}">
                                        </div>
                                        <div class="col-md-2 fragment-container">
                                            <span>Ap.</span>
                                            <input class="form-control custom dpd_confirm_app_no" placeholder="ap no"
                                                   type="text" value="{if $streetAp}{$streetAp}{elseif isset($converted_address_ap)}{$converted_address_ap}{/if}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {if isset($street_error)}
                                <div class="form-group row" id="dpd_street_error">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        {$street_error}
                                    </div>
                                </div>
                            {/if}

                        </div>

                        <div data-method="pickup"
                             class="dpd-confirmation-address-tab js-selected-delivery-content-address {if $deliveryAddress->dpd_shipment_type == 'pickup'}active{/if}">
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label text-left"> Office/Pickup Point</label>
                                <div class="col-md-10">
                                    <select class="dpd_confirm_office form-control" style="width: 100%">
                                        {if $selectedOfficeId}
                                            <option value="{$selectedOfficeId}">{$selectedOffice|escape:'htmlall':'UTF-8'}</option>
                                        {/if}
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="form-group row ">
                            <label class="col-md-10 form-control-label text-left">
                                {if ! ( $selectedOfficeId ||  $deliveryAddress->dpd_street )  }
                                    <input type="hidden" id="show_error_message" value="1" />
                                {/if}
                                <span class="dpd-confirmation-error" style="color: red; display: none">Adresa nu este normalizata</span>
                                <span class="dpd-confirmation-confirmed" style="color: green;display: none">Address is valid</span>
                            </label>
                            <div class="col-md-2">
                                <button type="button" class="save_and_validate btn btn-primary pull-right button">Save & Validate Addresss</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div id="dpdgeopost_notice_container" {if isset($ps14) && $ps14} class="order_ps14" {/if}> {if isset($errors) &&
            $errors} {include file=$smarty.const._PS_MODULE_DIR_|cat:'dpdgeopost/views/templates/admin/errors.tpl'}
            {elseif !$shipment->id_shipment && !$selected_shipping_method_id}
                <p class="warn">{l s='Client did not selected DPD shipment, but you can use this shipment method.'
                    mod='dpdgeopost'}</p>
            {elseif $ws_shippingPrice_noCurrency > 0 && $ws_shippingPrice_noCurrency > $total_shipping_tax_incl}
                {$ws_shippingPrice} > {$total_shipping_tax_incl}
                <p class="warn">{l s='Shipping costs more than client paid.' mod='dpdgeopost'}</p>
            {elseif $error_message}
                <p class="error">{$error_message|escape:'htmlall':'UTF-8'}</p>
            {/if}
        </div>
        <br/>
        <br/>
        <b>{l s='Parcels information' mod='dpdgeopost'}</b>
        <br/>
        <br/>
        <table width="100%" cellspacing="0" cellpadding="0" class="table{if isset($ps14) && $ps14} order_ps14{/if}">
            <colgroup>
                <col width="20%">
                <col width="20%">
                <col width="20%">
                <col width="">
            </colgroup>
            <thead>
            <tr>
                <th>{l s='Weight' mod='dpdgeopost'}</th>
                <th>{l s='Parcels' mod='dpdgeopost'}</th>
                <th>{l s='Paid for Shipping' mod='dpdgeopost'}</th>
                <th>{l s='DPD Shipping Price' mod='dpdgeopost'}</th>
                <th>{l s='Shipping Method' mod='dpdgeopost'}</th>
            </tr>
            </thead>
            <tbody>
            <tr>


                <td>{$total_weight|string_format:"%.3f"} {$smarty.const._DPDGEOPOST_DEFAULT_WEIGHT_UNIT_}</td>
                <td>{if $shipment->parcels}{$shipment->parcels|count|escape:'htmlall':'UTF-8'}{elseif
                    $settings->packaging_method ==
                    DpdGeopostConfiguration::ONE_PRODUCT}{$products|count}{else}1{/if}</td>
                <td><span id="dpdgeopost_paid_price" {if $ws_shippingPrice> 0 && $ws_shippingPrice >
                    $total_shipping_tax_incl} style="color:red"{/if}>{displayPrice
                        price=$total_shipping_tax_incl}</span></td>
                <td><span id="dpdgeopost_service_price" {if $ws_shippingPrice> 0 && $ws_shippingPrice >
                    $total_shipping_tax_incl}
                        style="color:red"{/if}>{$ws_shippingPrice|escape:'htmlall':'UTF-8'}</span></td>
                <td>
                    <select id="dpd_shipping_method" autocomplete="off" {if $shipment->id_shipment &&
                    $shipment->id_manifest} disabled="disabled"{/if}>
                        <option value="">-</option>
                        {if $settings->active_services_locker }
                            <option value="{$smarty.const._DPDGEOPOST_LOCKER_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_LOCKER_ID_ || !empty($selectedOfficeId)} selected="selected" {/if}>
                                DPD Standard Locker
                            </option>
                        {/if}
                        {if $settings->active_services_classic}
                            <option value="{$smarty.const._DPDGEOPOST_CLASSIC_ID_}" {if $shipment->mainServiceCode==1 ||
                            $selected_shipping_method_id==1} selected="selected"{/if}>{l s='DPD Classic'
                                mod='dpdgeopost'}</option>
                        {/if}

                        {if $settings->active_services_classic}
                            <option value="{$smarty.const._DPDGEOPOST_CLASSIC_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_CLASSIC_ID_ } selected="selected" {/if}>
                                DPD Classic
                            </option>
                        {/if}
                        {if $settings->active_services_classic_1_parcel}
                            <option value="{$smarty.const._DPDGEOPOST_CLASSIC_1_PARCEL_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_CLASSIC_1_PARCEL_ID_ } selected="selected" {/if}>
                                DPD Classic 1 Parcel
                            </option>
                        {/if}
                        {if $settings->active_services_locco}
                            <option value="{$smarty.const._DPDGEOPOST_LOCCO_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_LOCCO_ID_ } selected="selected" {/if}>
                                DPD Locco
                            </option>
                        {/if}
                        {if $settings->active_services_locco_1_parcel}
                            <option value="{$smarty.const._DPDGEOPOST_LOCCO_1_PARCEL_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_LOCCO_1_PARCEL_ID_ } selected="selected" {/if}>
                                DPD Locco 1 Parcel
                            </option>
                        {/if}
                        {if $settings->active_services_classic_balkan}
                            <option value="{$smarty.const._DPDGEOPOST_CLASSIC_BALKAN_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_CLASSIC_BALKAN_ID_ } selected="selected" {/if}>
                                DPD Regional CEE
                            </option>
                        {/if}
                        {if $settings->active_services_classic_international}
                            <option value="{$smarty.const._DPDGEOPOST_CLASSIC_INTERNATIONAL_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_CLASSIC_INTERNATIONAL_ID_ } selected="selected" {/if}>
                                DPD Classic International
                            </option>
                        {/if}
                        {if $settings->active_services_classic_pallet_one_romania}
                            <option value="{$smarty.const._DPDGEOPOST_CLASSIC_PALLET_ONE_ROMANIA_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_CLASSIC_PALLET_ONE_ROMANIA_ID_ } selected="selected" {/if}>
                                DPD Classic Pallet One Romania
                            </option>
                        {/if}
                        {if $settings->active_services_classic_poland}
                            <option value="{$smarty.const._DPDGEOPOST_CLASSIC_POLAND_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_CLASSIC_POLAND_ID_ } selected="selected" {/if}>
                                DPD Classic Poland
                            </option>
                        {/if}
                        {if $settings->active_services_standard_24 }
                            <option value="{$smarty.const._DPDGEOPOST_STANDARD_24_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_STANDARD_24_ID_ && empty($selectedOfficeId)} selected="selected" {/if}>
                                DPD Standard 24
                            </option>
                        {/if}
                        {if $settings->active_services_fastius_express}
                            <option value="{$smarty.const._DPDGEOPOST_FASTIUS_EXPRESS_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_FASTIUS_EXPRESS_ID_ } selected="selected" {/if}>
                                DPD Fastius Express
                            </option>
                        {/if}
                        {if $settings->active_services_fastius_express_2h}
                            <option value="{$smarty.const._DPDGEOPOST_FASTIUS_EXPRESS_2H_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_FASTIUS_EXPRESS_2H_ID_ } selected="selected" {/if}>
                                DPD Fastius Express 2h
                            </option>
                        {/if}
                        {if $settings->active_services_pallet_one_romania}
                            <option value="{$smarty.const._DPDGEOPOST_PALLET_ONE_ROMANIA_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_PALLET_ONE_ROMANIA_ID_ } selected="selected" {/if}>
                                DPD Pallet One Romania
                            </option>
                        {/if}
                        {if $settings->active_services_tires }
                            <option value="{$smarty.const._DPDGEOPOST_TIRES_ID_}" {if $selected_shipping_method_id == $smarty.const._DPDGEOPOST_TIRES_ID_} selected="selected" {/if}>
                                DPD TIRES
                            </option>
                        {/if}

                    </select>
                </td>
            </tr>
            </tbody>
        </table>
        <br/>
        {if $shipment->id_shipment}
            <div class="dpdgeopost_shipment_action_buttons" class="container-sm">
                <a href="{$smarty.const._DPDGEOPOST_PDF_URI_|escape:'htmlall':'UTF-8'}?printLabels=true&token={$dpd_geopost_token|escape:'htmlall':'UTF-8'}&id_shop={$dpd_geopost_id_shop|escape:'htmlall':'UTF-8'}&id_lang={$dpd_geopost_id_lang|escape:'htmlall':'UTF-8'}&id_order={$order->id|escape:'htmlall':'UTF-8'}"
                   id="dpdgeopost_print_labels" class="btn btn-secondary">
                   {l s='Print labels' mod='dpdgeopost'}
                </a>
                {if $hasVouchers}
                    <a href="{$smarty.const._DPDGEOPOST_PDF_URI_|escape:'htmlall':'UTF-8'}?printVouchers=true&token={$dpd_geopost_token|escape:'htmlall':'UTF-8'}&id_shop={$dpd_geopost_id_shop|escape:'htmlall':'UTF-8'}&id_lang={$dpd_geopost_id_lang|escape:'htmlall':'UTF-8'}&id_order={$order->id|escape:'htmlall':'UTF-8'}"
                       id="dpdgeopost_print_vouchers" class="button">
                         {l s='Print vouchers' mod='dpdgeopost'}
                    </a>
                {/if}
                {if !$shipment->id_manifest}
                    <input type="button" id="dpdgeopost_delete_shipment"
                           onclick="if (confirm('{l s='Are You sure?' mod='dpdgeopost'}')) deleteShipment();"
                           class="btn btn-secondary"
                           value="{l s='Delete' mod='dpdgeopost'}"/>
                    <input type="button" id="dpdgeopost_edit_shipment" class="btn btn-secondary"
                           value="{l s='Edit' mod='dpdgeopost'}"/>
                {/if}
                <input type="button" id="dpdgeopost_preview_shipment" class="btn btn-secondary"
                       value="{l s='Preview' mod='dpdgeopost'}"/>

            </div>
        {else}
            <p id="dpdgeopost_create_shipment_message"
               style="{if !$selected_shipping_method_id }{else}display: none;{/if}text-align: right;color: #ff5450;">{l s='Something is wrong. Please check address fields or the selected service.' mod='dpdgeopost'}</p>
            <input type="button"
                   class="btn btn-primary pull-right button" {if !$selected_shipping_method_id || $ws_shippingPrice=='---' }
                disabled="disabled" {/if} id="dpdgeopost_create_shipment"
                   value="{l s='Create shipment' mod='dpdgeopost'}"
                   autocomplete="off" style="margin-bottom: 10px;"/>
        {/if}
        <div class="clear"></div>
        <br/>
        {if $shipment->id_shipment}
            <div class="separation"></div>
            {*		<b>{l s='Status' mod='dpdgeopost'}</b><br /><br />*}
            <table id="dpdgeopost_shipment_actions" width="100%" cellspacing="0" cellpadding="0" class="table">
                <colgroup>
                    <col width="">
                    <col width="20%">
                </colgroup>
                <thead>
                <tr>
                    <th>{l s='Action' mod='dpdgeopost'}</th>
                    <th>{l s='Status' mod='dpdgeopost'}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{l s='DPD pickup arranged' mod='dpdgeopost'}</td>
                    <td>{if $shipment->isPickupArranged()}{l s='Yes' mod='dpdgeopost'}{else}{l s='No'
                        mod='dpdgeopost'}
                            <a href="{$module_link|escape:'htmlall':'UTF-8'}&menu=shipment_list" class="btn btn-secondary"
                               target="_blank">{l s='Request Courier' mod='dpdgeopost'}</a>
                        {/if}</td>
                </tr>
                {*
                <tr>
                    <td>{l s='Manifest closed' mod='dpdgeopost'}</td>
                    <td>{if $shipment->id_manifest}{l s='Yes' mod='dpdgeopost'}{else}{l s='No' mod='dpdgeopost'}{/if}
                    </td>
                </tr>
                *}
                </tbody>
            </table>
        {/if}
        <div id="dpdgeopost_shipment_creation" class="modal-dialog modal-dialog-centered modal-dialog-scrollable fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{l s='Shipment creation' mod='dpdgeopost'}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="message_container">
                            <b>{l s='Group the products in your shipment into parcels' mod='dpdgeopost'}</b><br/>
                            {l s='This module lets you organize your products into parcels using the table below. Select parcel number.'
                            mod='dpdgeopost'}
                        </div>
                        <table width="100%" cellspacing="0" cellpadding="0" class="table" id="parcel_selection_table">
                            <colgroup>
                                <col width="10%">
                                <col width="">
                                <col width="20%">
                                <col width="20%">
                                <col width="5%">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>{l s='ID' mod='dpdgeopost'}</th>
                                <th>{l s='Product' mod='dpdgeopost'}</th>
                                <th>{l s='Weight' mod='dpdgeopost'}</th>
                                <th>{l s='Total selected parcel weight' mod='dpdgeopost'}</th>
                                <th>{l s='Parcel' mod='dpdgeopost'}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$products item=product name=products}
                                {if isset($product.parcel_weight)}
                                    {assign var="parcel_total_weight" value=$product.parcel_weight}
                                {else}
                                    {assign var="parcel_total_weight" value=$product.product_weight}
                                {/if}
                                <tr>
                                    <td class="product_id">
                                        {$product.id_product|escape:'htmlall':'UTF-8'}_{$product.id_product_attribute|escape:'htmlall':'UTF-8'}
                                    </td>
                                    <td>{$product.product_name|escape:'htmlall':'UTF-8'}</td>
                                    <td class="parcel_weight" rel="{$product.product_weight|string_format:" %.3f"}">
                                        <input type="text" value="{$product.product_weight|string_format:" %.3f"}"/>
                                        {$smarty.const._DPDGEOPOST_DEFAULT_WEIGHT_UNIT_}
                                    </td>
                                    <td class="parcel_total_weight" rel="{$parcel_total_weight|escape:'htmlall':'UTF-8'}">
                                        <input type="text" value="{$parcel_total_weight|string_format:" %.3f"}" />
                                        {$smarty.const._DPDGEOPOST_DEFAULT_WEIGHT_UNIT_}
                                    </td>
                                    <td>
                                        <select class="parcel_selection" autocomplete="off">
                                            {if array_key_exists('parcelReferenceNumber', $product)}
                                                {foreach from=$shipment->parcels key=parcel_no item=parcel}
                                                    {if isset($parcel.parcelReferenceNumber) && $parcel.parcelReferenceNumber == $product.parcelReferenceNumber}
                                                        {assign var="selected_parcel" value=$parcel_no+1}
                                                    {/if}
                                                {/foreach}
                                            {/if}

                                            {section start=1 loop=$products|count+1 name=parcel}
                                                {if !isset($selected_parcel)}
                                                    {if $settings->packaging_method == DpdGeopostConfiguration::ONE_PRODUCT}
                                                        {assign var="parcelNo" value=$smarty.foreach.products.index+1}
                                                    {else}
                                                        {assign var="parcelNo" value=1}
                                                    {/if}
                                                    <option value="{$smarty.section.parcel.index|escape:'htmlall':'UTF-8'}" {if
                                                            $smarty.section.parcel.index==$parcelNo} selected="selected"
                                                            {/if}>{$smarty.section.parcel.index|escape:'htmlall':'UTF-8'} </option>
                                                {else}
                                                    <option value="{$smarty.section.parcel.index|escape:'htmlall':'UTF-8'}" {if
                                                            $smarty.section.parcel.index==$selected_parcel} selected="selected"
                                                            {/if}>{$smarty.section.parcel.index|escape:'htmlall':'UTF-8'} </option>
                                                {/if}
                                            {/section} </select></td>
                                </tr>
                            {/foreach} </tbody>
                        </table>
                        <br/>

                        <b>{l s='Enter description for each parcel' mod='dpdgeopost'}</b><br/>
                        {l s='You can enter description of each parcel, for communication with courier services,
                                            in the fields below.' mod='dpdgeopost'}
                         <br/><br/>
                        <table width="100%" cellspacing="0" cellpadding="0" class="table"
                               id="parcel_descriptions_table">
                            <colgroup>
                                <col width="10%">
                                <col width="">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>{l s='Parcel' mod='dpdgeopost'}</th>
                                <th>{l s='Description' mod='dpdgeopost'}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$products item=product name=products}
                                <tr>
                                    <td class="parcel_id_{$smarty.foreach.products.iteration|escape:'htmlall':'UTF-8'}">
                                        {$smarty.foreach.products.iteration|escape:'htmlall':'UTF-8'}
                                    </td>
                                    <td class="parcel_description">


                                        {if $shipment->parcels && isset($shipment->parcels[$smarty.foreach.products.iteration-1]) && isset($shipment->parcels[$smarty.foreach.products.iteration-1].description)}
                                            {assign var="description" value=$shipment->parcels[$smarty.foreach.products.iteration-1].description}
                                        {elseif $shipment->parcels}
                                            {assign var="description" value=""}
                                        {elseif $settings->packaging_method == DpdGeopostConfiguration::ONE_PRODUCT}
                                            {assign var="description" value=$product.description}
                                        {elseif $settings->packaging_method == DpdGeopostConfiguration::ALL_PRODUCTS && $smarty.foreach.products.iteration == 1}
                                            {assign var="description" value=$product.description}
                                        {else}
                                            {assign var="description" value=$product.description}
                                        {/if}
                                        <input type="hidden" value="{$description|escape:'htmlall':'UTF-8'}"
                                               autocomplete="off"/>
                                        <input type="text" value="{$description|escape:'htmlall':'UTF-8'}" {if
                                        !$description} disabled="disabled" {/if} autocomplete="off"  class="form-control"/>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                        <br/>

                        <table  class="table">
                            <tr>
                                <td><input type="checkbox" id="dpd_swap_enabled"> This is a SWAP shipment</td>
                            </tr>

                            <tr id="dpd_reusable_enabled_container" style="display: none;">
                                <td><input type="checkbox" id="dpd_reusable_enabled" class="form-check-input"> This is a Reusable Return shipment</td>
                            </tr>

                            <tr>
                                <td><input type="checkbox" id="dpd_rod_enabled" class="form-check-input"> This is a ROD shipment</td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" id="dpd_voucher_enabled" class="form-check-input"> This shipment has a VOUCHER</td>
                            </tr>
                            <tr>
                                <td><label> Shipment reference </label><br/>
                                    <input type="text" id="dpd_shipment_reference" class="form-check">
                                </td>
                            </tr>
                            <tr>
                                <td><label> Shipment note </label><br/>
                                    <textarea id="dpd_shipment_note" class="form-check">{$deliveryAddress->other|escape:'htmlall':'utf-8'}</textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footerr">
                        <input type="button" class="btn btn-primary" id="dpdgeopost_shipment_creation_save" value="{l s='Save' mod='dpdgeopost'}"/>
                        <input type="button" class="btn btn-secondary" id="dpdgeopost_shipment_creation_cancel" value="{l s='Cancel' mod='dpdgeopost'}"/>
                    </div>
                    <input type="button" style="display:none; float:right" class="button" id="dpdgeopost_shipment_creation_close" value="{l s='Close' mod='dpdgeopost'}"/>
                </div>
             </div>
        </div>
    {else}
        <p class="warn">{l s='Please provide required information in module settings page' mod='dpdgeopost'} <a
                    href="{$module_link|escape:'htmlall':'UTF-8'}&menu=configuration">{l s='here' mod='dpdgeopost'}</a>
        </p>
    {/if}


    {if isset($order->shipping_number) && isset($carrier_url)}
        <div class="separation"></div>
        <a target="_blank" href="{$carrier_url|replace:'@':$order->shipping_number}">
            <button id="track_shipment_button" class="button">
                {l s='Track Shipment' mod='dpdgeopost'}
            </button>
        </a>
    {/if}

</fieldset>