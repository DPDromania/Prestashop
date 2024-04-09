{if isset($dpd_should_normalize)}
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

    </style>

<div class="dpd-normalizer">
    <input type="hidden" value="{$cart_id}" class="cart_id" />
    <input type="hidden" value="{$address->id}" class="address_id dpdgeopost_id_address">
    {if isset($city_id)}
        <input type="hidden" value="{$city_id}" class="dpd_city_id">
    {/if}

    {if isset($street_id)}
        <input type="hidden" value="{$street_id}" class="dpd_street_id">
    {/if}

    {if isset($country_id)}
        <input type="hidden" value="{$country_id}" class="dpd_country_id">
    {/if}

    {if isset($street_is_required)}
        <input type="hidden" value="1" class="dpd_street_is_required">
    {else}
        <input type="hidden" value="0" class="dpd_street_is_required">
    {/if}

    {if isset($state_name) }
        <input type="hidden" value="{$state_name}" class="dpd_state_name">
    {/if}

    {if $address->dpd_shipment_type}
        {assign var="current_delivery_method" value=$address->dpd_shipment_type }
    {else}
        {if $address->dpd_office}
            {assign var="current_delivery_method" value="pickup" }
        {else}
            {assign var="current_delivery_method" value="delivery" }
        {/if}
    {/if}

    <input type="hidden" value="{$current_delivery_method}" name="current_delivery_method" class="current_delivery_method" />

    <div class="dpd-confirmation-address">

        <div class="dpd-confirmation-address-heading">
            <ul class="dpd-confirmation-address-nav">
                <li><img src=" logo --" alt="DPD"/></li>


                <li class="{if $current_delivery_method == 'delivery'}active{/if}">
                    <button type="button" data-method="delivery" class="js-selected-delivery-tab-address">
                        Delivery to adress
                    </button>
                </li>

                <li class="{if $current_delivery_method == 'pickup' }active{/if}">
                    <button type="button" data-method="pickup" class="js-selected-delivery-tab-address">
                        Delivery to Office/Automat
                    </button>
                </li>

            </ul>
        </div>

        <div class="dpd-confirmation-address-body">11111111111111
            <div class="dpd-confirmation-address-content">
                <div data-method="delivery"
                     class="dpd-confirmation-address-tab  js-selected-delivery-content-address {if $current_delivery_method == 'delivery'}active{/if}">
                    <h4>Delivery to Address</h4>
                </div>

                <div data-method="pickup"
                     class="dpd-confirmation-address-tab js-selected-delivery-content-address {if $current_delivery_method == 'pickup'}active{/if}">
                    <h4>Delivery to office/automat</h4>
                </div>

                <div class="form-group row ">
                    <label class="col-md-2 form-control-label text-left">Country</label>
                    <div class="col-md-10">
                        <select class="form-control dpd_confirm_country" type="text" style="width: 100%">
                            {if $country_ws_id}
                                <option value="{$country_ws_id}" selected="selected">{$country_name}</option>
                            {/if}
                        </select>
                    </div>
                </div>

                <div class="form-group row ">
                    <label class="col-md-2 form-control-label text-left">City</label>

                    <div class="col-md-10">
                        <select class="form-control dpd_confirm_city" type="text"  style="width: 100%">
                            {if $address->dpd_site}
                                <option value="{$address->dpd_site}" selected="selected">{$address->city}</option>
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
                     class="dpd-confirmation-address-tab  js-selected-delivery-content-address {if $current_delivery_method == 'delivery'}active{/if}">
                    <div class="form-group row ">
                        <label class="col-md-2 form-control-label text-left">Postcode</label>
                        <div class="col-md-10">

                            <select disabled="disabled" class="form-control dpd_confirm_postcode" type="text"  style="width: 100%">
                                {if $address->dpd_postcode}
                                    <option value="{$address->dpd_postcode}" selected="selected">{$address->dpd_postcode}</option>
                                {/if}
                            </select>

                        </div>
                    </div>

                    <div class="form-group row ">
                        <label class="col-md-2 form-control-label text-left">Address Str.</label>
                        <div class="col-md-10">
                            <div class="form-group row ">
                                <div class="col-md-6">
                                    <select class="form-control dpd_confirm_street"  style="width: 100%">
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
                                           type="text" value="{$converted_address_nr}">
                                </div>
                                <div class="col-md-2 fragment-container">
                                    <span>Bl.</span>
                                    <input class="form-control custom dpd_confirm_block_no"
                                           placeholder="block no"
                                           type="text" value="{$converted_address_bl}">
                                </div>
                                <div class="col-md-2 fragment-container">
                                    <span>Ap.</span>
                                    <input class="form-control custom dpd_confirm_app_no" placeholder="ap no"
                                           type="text" value="{$converted_address_ap}">
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
                     class="dpd-confirmation-address-tab js-selected-delivery-content-address {if $current_delivery_method == 'pickup'}active{/if}">
                    <div class="form-group row">
                        <label class="col-md-2 form-control-label text-left"> Office/Pickup Point</label>
                        <div class="col-md-10">
                            <select class="dpd_confirm_office form-control" style="width: 100%">
                                {if $selectedPuDo}
                                    <option value="{$selectedPuDo.id}">{$selectedPuDo.name|escape:'htmlall':'UTF-8'}</option>
                                {/if}
                            </select>
                        </div>
                    </div>
                </div>


                <div class="form-group row ">
                    <label class="col-md-4 form-control-label text-left">
                        <span class="dpd-confirmation-error" style="color: red;"></span>
                        <span class="dpd-confirmation-confirmed" style="color: green;">Address is valid</span>
                    </label>
                    <div class="col-md-2">
                        <button type="button" class="save_and_validate btn btn-primary pull-right button">Save & Validate Addresss</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

    <script>
        if(typeof init_normalization_form == 'function') {
            init_normalization_form();
        }
    </script>
{/if}