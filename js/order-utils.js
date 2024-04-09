function convert_address_to_str_nr_bl() {

    var $ = jQuery;
    var check_adress_field_exist = '#delivery-address input[name="address1"]';
    if ($(check_adress_field_exist).length) {
        var check_adress_value_exist = $(check_adress_field_exist).val();
        var converted_address_html = '' +
            '<div class="converted-address-container">' +

            '<div class="form-group row">' +
            '<label class="col-md-2 form-control-label">Str.</label>' +
            '<div class="col-md-6"><input class="form-control js-converted-address-fields" type="text" value="" name="" id="converted-address-str" required/></div>' +
            '<div class="col-md-4 form-control-comment"></div>' +
            '</div>' +

            '<div class="form-group row">' +
            '<label class="col-md-2 form-control-label">Nr.</label>' +
            '<div class="col-md-6"><input class="form-control js-converted-address-fields" type="text" value="" name="" id="converted-address-nr" required/></div>' +
            '<div class="col-md-4 form-control-comment"></div>' +
            '</div>' +

            '<div class="form-group row">' +
            '<label class="col-md-2 form-control-label">Bl.</label>' +
            '<div class="col-md-6"><input class="form-control js-converted-address-fields" type="text" value="-" name="" id="converted-address-bl"/></div>' +
            '<div class="col-md-4 form-control-comment">Optional</div>' +
            '</div>' +

            '<div class="form-group row">' +
            '<label class="col-md-2 form-control-label">Ap.</label>' +
            '<div class="col-md-6"><input class="form-control js-converted-address-fields" type="text" value="-" name="" id="converted-address-ap"/></div>' +
            '<div class="col-md-4 form-control-comment">Optional</div>' +
            '</div>' +

            '</div>' +
            '';
        if (check_adress_value_exist && check_adress_value_exist !== '') {
            var parse_adress_value = check_adress_value_exist.split(',');
            if (parse_adress_value.length > 0) {

                var street_name = '';
                var street_no = '';

                var block_no = '';
                var app_no = '';

                if(parse_adress_value[0]) {
                    street_name = parse_adress_value[0];
                }

                if(parse_adress_value[1]) {
                    street_no = parse_adress_value[1].replace(/\D/g,'');
                }

                if(parse_adress_value[2]) {
                    block_no = parse_adress_value[2].replace(/\D/g,'');
                }

                if(parse_adress_value[3]) {
                    app_no = parse_adress_value[3].replace(/\D/g,'');
                }

                converted_address_html = '' +
                    '<div class="converted-address-container">' +

                    '<div class="form-group row">' +
                    '<label class="col-md-2 form-control-label">Str.</label>' +
                    '<div class="col-md-6"><input class="form-control js-converted-address-fields" type="text" value="' + street_name + '" name="" id="converted-address-str" required/></div>' +
                    '<div class="col-md-4 form-control-comment"></div>' +
                    '</div>' +

                    '<div class="form-group row">' +
                    '<label class="col-md-2 form-control-label">Nr.</label>' +
                    '<div class="col-md-6"><input class="form-control js-converted-address-fields" type="text" value="' + street_no + '" name="" id="converted-address-nr" required/></div>' +
                    '<div class="col-md-4 form-control-comment"></div>' +
                    '</div>' +

                    '<div class="form-group row">' +
                    '<label class="col-md-2 form-control-label">Bl.</label>' +
                    '<div class="col-md-6"><input class="form-control js-converted-address-fields" type="text" value="' + block_no + '" name="" id="converted-address-bl"/></div>' +
                    '<div class="col-md-4 form-control-comment">Optional</div>' +
                    '</div>' +

                    '<div class="form-group row">' +
                    '<label class="col-md-2 form-control-label">Ap.</label>' +
                    '<div class="col-md-6"><input class="form-control js-converted-address-fields" type="text" value="' + app_no + '" name="" id="converted-address-ap"/></div>' +
                    '<div class="col-md-4 form-control-comment">Optional</div>' +
                    '</div>' +

                    '</div>' +
                    '';
            }
        }
        $(check_adress_field_exist).hide().closest('.col-md-6').removeClass('col-md-6').addClass('col-md-9').append(converted_address_html);
    }

    $('#converted-address-bl').on('blur', function (e) {
        if ($(this).val() === '') {
            $(this).val('N/A');
        }
    });
    $('#converted-address-ap').on('blur', function (e) {
        if ($(this).val() === '') {
            $(this).val('N/A');
        }
    });
    $('.js-converted-address-fields').on('blur', function (e) {
        var converted_address_str = ($('#converted-address-str').val() && $('#converted-address-str').val() !== '') ? $('#converted-address-str').val() : '-';
        var converted_address_nr = ($('#converted-address-nr').val() && $('#converted-address-nr').val() !== '') ? $('#converted-address-nr').val() : '-';
        var converted_address_bl = ($('#converted-address-bl').val() && $('#converted-address-bl').val() !== '') ? $('#converted-address-bl').val() : '-';
        var converted_address_ap = ($('#converted-address-ap').val() && $('#converted-address-ap').val() !== '') ? $('#converted-address-ap').val() : '-';
        var new_converted_value = converted_address_str + ', nr. ' + converted_address_nr + ', bl. ' + converted_address_bl + ', ap.' + converted_address_ap;
        $(check_adress_field_exist).val(new_converted_value);
    });
    $(check_adress_field_exist).closest('form').submit(function () {
        var converted_address_str = ($('#converted-address-str').val() && $('#converted-address-str').val() !== '') ? $('#converted-address-str').val() : '-';
        var converted_address_nr = ($('#converted-address-nr').val() && $('#converted-address-nr').val() !== '') ? $('#converted-address-nr').val() : '-';
        var converted_address_bl = ($('#converted-address-bl').val() && $('#converted-address-bl').val() !== '') ? $('#converted-address-bl').val() : '-';
        var converted_address_ap = ($('#converted-address-ap').val() && $('#converted-address-ap').val() !== '') ? $('#converted-address-ap').val() : '-';
        var new_converted_value = converted_address_str + ', nr. ' + converted_address_nr + ', bl. ' + converted_address_bl + ', ap. ' + converted_address_ap;
        $(check_adress_field_exist).val(new_converted_value);
        return true;
    });
}

jQuery(document).ready(function ($) {

    console.log('in order-utils');
//    convert_address_to_str_nr_bl();

    var selectedCity = $('input[name="city"]').val();

    filterPickupOffice(selectedCity);

    // $('input[name="city"], input[name="country"]').on('blur', function (e) {
    //     filterPickupOffice();
    // });

    $(document).on('blur', 'input[name="city"], input[name="country"]', function(e){
        filterPickupOffice();
    });

    $("#delivery-address").bind("DOMSubtreeModified", function() {
        console.log('new thing');
    });

    function filterPickupOffice() {
        var currentSelection = $('select[name="dpd_office"]').val();
        var selectedCity = $('input[name="city"]').val();
        var selectedCountry = $('select[name="id_country"]').val();
        var url = window._DPDGEOPOST_AJAX_URI_ + '?filterCity=' + selectedCity + '&filterCountry=' + selectedCountry;
        $('select[name="dpd_office"]').attr('disabled', 'disabled');
        $('select[name="dpd_office"]').html('...');

        $.getJSON(url, function (response) {
            if(response.empty) {
                $('select[name="dpd_office"]').attr('disabled', 'disabled');
                $('select[name="dpd_office"]').html(response.html);
            } else {
                $('select[name="dpd_office"]').html(response.html);
                $('select[name="dpd_office"]').removeAttr('disabled');
                setTimeout(function () {
                    $('select[name="dpd_office"]').val(currentSelection);
                }, 250);
            }

        });
    }

    function updateConfirmedAddress(field, value) {
        var address = $('.address_id').val();
        var url = window._DPDGEOPOST_AJAX_URI_ + '?autoselect=true&field=' + field + '&value=' + value + '&address=' + address;
        $.getJSON(url, function (response) {
            updateAddressText();
           // disableOrEnableConfirmDeliveryOption();
        });
    }

    DPD_CURRENT_COUNTRY_ID = 0;
    DPD_CURRENT_CITY_ID = 0;
    DPD_CURRENT_STREET_ID = 0;
    DPD_CURRENT_POSTCODE = 0;

    DPD_CURRENT_CITY_TEXT = '';
    DPD_CURRENT_STREET_TEXT = '';
    DPD_CURRENT_STREET_TYPE = '';

    DPD_CURRENT_STATE_NAME = '';

    if($('#dpd_country_id')) {
        DPD_CURRENT_COUNTRY_ID = $('.dpd-normalizer:visible .dpd_country_id').val();
    }

    if($('#dpd_city_id').val()) {
        DPD_CURRENT_CITY_ID = $('.dpd-normalizer:visible .dpd_city_id').val();
    };

    if($('#dpd_street_id').val()) {
        DPD_CURRENT_STREET_ID = $('.dpd-normalizer:visible .dpd_street_id').val();
    };

    if($('#dpd_city_text').val()) {
        DPD_CURRENT_CITY_TEXT = $('.dpd-normalizer:visible .dpd_city_text').val();
    }

    if($('#dpd_street_text').val() ) {
        DPD_CURRENT_STREET_TEXT = $('.dpd-normalizer:visible .dpd_street_text').val();
    }

    if($('#dpd_state_name').val()) {
        DPD_CURRENT_STATE_NAME = $('.dpd-normalizer:visible .dpd_state_name').val();
    }

    function filterPickupOfficeInNormalization() {
        var currentSelection = $('.dpd-normalizer:visible select.dropoffice_id').val();
        var selectedCity = $('.dpd-normalizer:visible input.dpd_confirm_city').val();
        var selectedCountry = $('.dpd-normalizer:visible input.dpd_confirm_country').val();
        var url = window._DPDGEOPOST_AJAX_URI_ + '?filterCity=' + selectedCity + '&filterCountryNormal=' + selectedCountry;
        $.getJSON(url, function (response) {
            checkIfCurrentAddressIsValid();
            if(response.empty) {

                $('.dpd-normalizer:visible select.dropoffice_id').attr('disabled', 'disabled');
                $('.dpd-normalizer:visible select.dropoffice_id').html(response.html);
            } else {
                $('.dpd-normalizer:visible select.dropoffice_id').html(response.html);
                $('.dpd-normalizer:visible select.dropoffice_id').removeAttr('disabled');
                setTimeout(function () {
                    $('.dpd-normalizer:visible select.dropoffice_id').val(currentSelection);
                }, 250);
            }
        });
    }


    // $(".dpd_confirm_city").autocomplete({
    //     source: function (request, response) {
    //         var term = request.term;
    //
    //         var url = window._DPDGEOPOST_AJAX_URI_ + '?autocomplete=true&type=city&value=' + term + '&countryid='+DPD_CURRENT_COUNTRY_ID;
    //         if(DPD_CURRENT_STATE_NAME) {
    //             url += '&state=' + DPD_CURRENT_STATE_NAME;
    //         }
    //         $.getJSON(url, request, function (data, status, xhr) {
    //
    //             response(data);
    //         });
    //     },
    //     minLength: 2,
    //     select: function (event, ui) {
    //         if(ui.item.id != DPD_CURRENT_CITY_ID) {
    //             cleanDataBelow('city');
    //         }
    //
    //         DPD_CURRENT_CITY_ID = ui.item.id;
    //         DPD_CURRENT_CITY_TEXT = ui.item.value;
    //         $('#dpd_city_error').hide();
    //         $('.dpd-normalizer:visible .js-municipality').html(ui.item.municipality);
    //
    //         filterPickupOfficeInNormalization();
    //
    //         updateConfirmedAddress('city', ui.item.id);
    //         checkIfCurrentAddressIsValid();
    //     }
    // });

    $(".dpd_confirm_street").autocomplete({
        source: function (request, response) {
            var term = request.term;
            var $city_id = $('.dpd-normalizer:visible .dpd_city_id').val();
            var url = window._DPDGEOPOST_AJAX_URI_ + '?autocomplete=true&type=street&value=' + term + '&siteid=' + $city_id;
            $.getJSON(url, request, function (data, status, xhr) {
                response(data);
            });
        },
        minLength: 2,
        select: function (event, ui) {
            if(ui.item.id != DPD_CURRENT_STREET_ID) {
                cleanDataBelow('street');
            }
            DPD_CURRENT_STREET_ID = ui.item.id;
            DPD_CURRENT_STREET_TEXT = ui.item.value;
            DPD_CURRENT_STREET_TYPE = ui.item.type;
            $('#dpd_street_error').hide();
            updateConfirmedAddress('street', ui.item.id);
            checkIfCurrentAddressIsValid();
        }
    });

    $(".dpd_confirm_country").autocomplete({
        source: function(request, response) {
            var term = request.term;

            var url = window._DPDGEOPOST_AJAX_URI_ + '?autocomplete=true&type=country&value=' + term;
            $.getJSON(url, request, function(data, status, xhr){
                response(data);
            });
        },
        minLength: 2,
        select: function(event, ui) {
            if(ui.item.id != DPD_CURRENT_COUNTRY_ID) {
                cleanDataBelow('country');
            }

            DPD_CURRENT_COUNTRY_ID = ui.item.id;
            $('#dpd_country_error').hide();

            $('.dpd-normalizer:visible .dpd_country_id').val(ui.item.id);


            updateConfirmedAddress('country', ui.item.id);
            checkIfCurrentAddressIsValid();
        }
    });

    function cleanDataBelow(startCleaningFrom) {
        switch (startCleaningFrom) {
            case 'country':
                $('.dpd-normalizer:visible .dpd_confirm_city').val('');
                DPD_CURRENT_CITY_TEXT = '';
                DPD_CURRENT_CITY_ID = 0;
            case 'city':
                DPD_CURRENT_POSTCODE = '';
                $('.dpd-normalizer:visible .dpd_confirm_postcode').val('');
            case 'postcode':
                $('.dpd-normalizer:visible .dpd_confirm_street').val('');
                $('.dpd-normalizer:visible .dpd_street_error').hide();
                DPD_CURRENT_STREET_TYPE = '';
                DPD_CURRENT_STREET_TEXT = '';
                DPD_CURRENT_STREET_ID = 0;
            case 'street':
                $('.dpd-normalizer:visible .dpd_confirm_street_no').val('');
                $('.dpd-normalizer:visible .dpd_confirm_block_no').val('');
                $('.dpd-normalizer:visible .dpd_confirm_app_no').val('');
        }
    }

    $(".dpd_confirm_postcode").autocomplete({
        source: function (request, response) {
            var term = request.term;

            var $city_id = $('.dpd-normalizer:visible .dpd_city_id').val();
            var url = window._DPDGEOPOST_AJAX_URI_ + '?autocomplete=true&type=postcode&value=' + term + '&siteid=' + $city_id;
            $.getJSON(url, request, function (data, status, xhr) {
                response(data);
            });
        },
        minLength: 3,
        select: function (event, ui) {
            if(ui.item.id != DPD_CURRENT_POSTCODE) {
                cleanDataBelow('postcode');
            }
            DPD_CURRENT_POSTCODE = ui.item.id;
            updateConfirmedAddress('postcode', ui.item.id);
            checkIfCurrentAddressIsValid();
        }
    });

    $('.dpd_confirm_street_no, .dpd_confirm_block_no, .dpd_confirm_app_no').on('blur', function () {

        var extra_data = $('.dpd_confirm_street_no:visible').val() + ':' + $('.dpd_confirm_block_no:visible').val() + ':' + $('.dpd_confirm_app_no:visible').val();

        updateConfirmedAddress('extra', extra_data);
        checkIfCurrentAddressIsValid();

    });

    function updateDpdAddress(callback) {

        var extra_data = $('.dpd_confirm_street_no:visible').val() + ':' + $('.dpd_confirm_block_no:visible').val() + ':' + $('.dpd_confirm_app_no:visible').val();

        var data = {
            'dpd_site': DPD_CURRENT_CITY_ID,
            'dpd_street': DPD_CURRENT_STREET_ID,
            'dpd_extra': extra_data
        };

        var address = $('.address_id').val();
        var url = window._DPDGEOPOST_AJAX_URI_ + '?updateDpdAddress=true&address='+ address;

        $.post(url, data, function(response){
            console.log(response);
            checkIfCurrentAddressIsValid();
            if(callback) callback();
        }, 'json');

    }


    function disableOrEnableConfirmDeliveryOption() {

        if($('.dpd_confirm_street:visible').length <= 0) {
            return;
        }

        var shouldEnable = DPD_CURRENT_CITY_ID && DPD_CURRENT_STREET_ID;
        if(shouldEnable) {
            updateDpdAddress(function(){
                $('button[name="confirmDeliveryOption"]').removeAttr('disabled');
                $('button[name="confirmDeliveryOption"]').show();
            });


        } else {
            $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
            $('button[name="confirmDeliveryOption"]').hide();
        }

    }

    function updateAddressText() {

        var address = $('.address_id').val();
        var streetText = $('.dpd_confirm_street:visible').val() ;

        if( $('.dpd_confirm_street_no:visible').val() ) {
            streetText += ", nr. " + $('.dpd_confirm_street_no:visible').val();
        }

        if( $('.dpd_confirm_block_no:visible').val() ) {
            streetText += ", bl. " + $('.dpd_confirm_block_no:visible').val();
        }

        if( $('.dpd_confirm_app_no:visible').val() ) {
            streetText += ", ap. " + $('.dpd_confirm_app_no:visible').val();
        }

        var cityText = $('.dpd_confirm_city:visible').val();

        var url = window._DPDGEOPOST_AJAX_URI_ + '?updateAddress=true&streetText=' + streetText + '&cityText=' + cityText + '&address='+ address;

        $.getJSON(url, function (response) {
            checkIfCurrentAddressIsValid();
        });
    }

    $('button[name="confirmDeliveryOption"]').click(function(e){

        if($('.dpd_confirm_street:visible').length <= 0) {
            return;
        }

        if($('#dpd_street_is_required').val() != '1') {
            return;
        }

        var shouldEnable = DPD_CURRENT_CITY_ID && DPD_CURRENT_STREET_ID;

        if(!shouldEnable) {
            e.preventDefault();

            var oldText = $(this).html();
            $(this).html('Invalid address!');
            var self = this;
            setTimeout(function(){
                $(self).html(oldText);
            }, 2000);

        }
    });

    function dpd_SelectedTabAddress() {
        var selected = '.js-selected-delivery-tab-address';
        $(document).on('click', selected, function(e) {
            e.preventDefault();
            if (!$(this).closest('li').hasClass('active')) {
                $(selected).closest('li').removeClass('active');
                $('.js-selected-delivery-tab-address[data-method="' + $(this).attr('data-method') + '"]').closest('li').addClass('active');
                $('.js-selected-delivery-content-address').removeClass('active');
                $('.js-selected-delivery-content-address[data-method="' + $(this).attr('data-method') + '"]').addClass('active');
            }
            return false;
        });
    }

    dpd_SelectedTabAddress();


    function checkIfCurrentAddressIsValid() {
        // if(window.updateDpdShippingPriceHandler) {
        //     window.updateDpdShippingPriceHandler();
        // }
    }

    // $('.ps-shown-by-js').change(function(e) {
    //
    //     var module_name = $(this).attr('data-module-name');
    //     var inputName= $(this).attr('name');
    //     var dpd_cart_id = $('#dpd_cart_id').val();
    //     var url = false;
    //
    //     if(module_name.indexOf('DPD') === 0 && inputName == 'payment-option') {
    //         url = window._DPDGEOPOST_AJAX_URI_ + '?codChosen=true&cart='+dpd_cart_id;
    //     } else {
    //         url = window._DPDGEOPOST_AJAX_URI_ + '?codRemoved=true&cart='+dpd_cart_id;
    //     }
    //
    //     if(url !== false) {
    //         $.getJSON(url, function (response) {
    //             // prestashop.emit('updateCart', {'reason':{
    //             //         'linkAction': 'add-to-cart'
    //             //     }});
    //         });
    //     }
    //
    // });

});