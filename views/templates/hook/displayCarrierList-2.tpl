{if isset($dpd_should_normalize)}
<div>
    <style>
        * html .ui-autocomplete {
            height: 100px;
        }
        .carrier-extra-content label {
            text-align: left;
            padding-right: 0;
        }
        .carrier-extra-content .form-group {
            margin-bottom: 10px;
        }
        .carrier-extra-content .form-group > div {
            padding-left: 0;
            display: -ms-flexbox;
            display: flex;
            display: -webkit-flex;
            -ms-flex-align: center;
            align-items: center;
            -ms-flex-pack: center;
            justify-content: center;
        }
        .carrier-extra-content .form-group > div:first-child {
            padding-left: 15px;
        }
        .carrier-extra-content .form-group:last-child {
            margin-bottom: 0;
        }
        .carrier-extra-content .form-group span {
            display: inline-block;
            float: left;
            width: 40px;
            margin-right: 10px;
            font-size: 14px;
        }
        .carrier-extra-content .form-group .form-control {
            padding: .5rem 0.5rem;
        }
        .carrier-extra-content .form-group .form-control.custom {
            max-width: calc(100% - 30px);
        }
    </style>
    <h1 class="step-title h3">Please confirm your delivery address</h1>
    <br>
    <hr>
    <br>
    <div class="form-group row ">
        <label class="col-md-2 form-control-label text-left">Country</label>
        <div class="col-md-10">
            <input class="form-control dpd_confirm_country" type="text" value="{$address->country}">
        </div>
    </div>

    <div class="form-group row ">
        <label class="col-md-2 form-control-label text-left">City</label>
        <div class="col-md-10">
            <input class="form-control dpd_confirm_city" type="text" value="{$address->city}">
        </div>
        <label class="col-md-2 form-control-label text-left"></label>
        <div class="col-md-10 text-left js-municipality" style="text-align: left; display: inline-block;">
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


    {if isset($city_error)}
    <div class="form-group row" id="dpd_city_error">
        <div class="col-md-2"></div>
        <div class="col-md-10">
        {$city_error}
        </div>
    </div>
    {/if}

    <div>
        <div>
            <h2>Livrare la adresa</h2>
        </div>
        <div>
            <h2>Livrare la automat</h2>
        </div>
        <div>
            <h2>Livrare la oficiu</h2>
        </div>
    </div>


    <div id="delivery-address">
        <div class="form-group row ">
            <label class="col-md-2 form-control-label text-left">Address Str.</label>
            <div class="col-md-10">
                <div class="form-group row ">
                    <div class="col-md-6">
                        <input class="form-control dpd_confirm_street" placeholder="the name of the street" type="text" value="{$converted_address_str}">
                    </div>
                    <div class="col-md-2">
                        <span>Nr.</span>
                        <input class="form-control custom dpd_confirm_street_no" placeholder="street no" type="text" value="{$converted_address_nr}">
                    </div>
                    <div class="col-md-2">
                        <span>Bl.</span>
                        <input class="form-control custom dpd_confirm_block_no" placeholder="block no" type="text" value="{$converted_address_bl}">
                    </div>
                    <div class="col-md-2">
                        <span>Ap.</span>
                        <input class="form-control custom dpd_confirm_app_no" placeholder="ap no" type="text" value="{$converted_address_ap}">
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
        <div class="form-group row ">
            <label class="col-md-2 form-control-label text-left">Postcode</label>
            <div class="col-md-10">
                <input class="form-control dpd_confirm_postcode" type="text" value="{$address->postcode}">
            </div>
        </div>
        <input type="hidden" value="{$address->id}" class="address_id">

        {if isset($city_id)}
            <input type="hidden" value="{$city_id}" id="dpd_city_id">
        {/if}

        {if isset($street_id)}
            <input type="hidden" value="{$street_id}" id="dpd_street_id">
        {/if}

        {if isset($country_id)}
            <input type="hidden" value="{$country_id}" id="dpd_country_id">
        {/if}

        {if isset($street_is_required)}
            <input type="hidden" value="1" id="dpd_street_is_required">
        {else}
            <input type="hidden" value="0" id="dpd_street_is_required">
        {/if}

        {if isset($state_name) }
            <input type="hidden" value="{$state_name}" id="dpd_state_name">
        {/if}
    </div>

    <div id="delivery-auto">
        <div>
            <label>{l s='Your current delivery address is'}</label>
            <br />
            <select id="dropoffice_id">
                <option value="">{l s='Current delivery address' mod='dpdgeopost'}</option>
                {foreach from=$offices item=office}
                    <option value="{$office.id|escape:'htmlall':'UTF-8'}" {if $office.id == $address->dpd_office}selected disabled{/if}>
                        {$office.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>

        </div>
    </div>

    <div id="delivery-office">
        <div>

            <label>{l s='Your current delivery address is'}</label>
            <br />
            <select id="dropoffice_id">
                <option value="">{l s='Current delivery address' mod='dpdgeopost'}</option>
                {foreach from=$offices item=office}
                    <option value="{$office.id|escape:'htmlall':'UTF-8'}" {if $office.id == $address->dpd_office}selected disabled{/if}>
                            {$office.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>

        </div>
    </div>

</div>


{/if}