$(document).ready(function(){
    function init_normalization_form() {
        if($('body#checkout').length > 0) {
            jQuery.fn.slideDown = jQuery.fn.show;
        }

        var AJAX_URL = window._DPDGEOPOST_AJAX_URI_;
        if(!AJAX_URL && window._DPD_GLOBAL_AJAX_) {
            AJAX_URL = window._DPD_GLOBAL_AJAX_;
        }

        function filterPickupOffice() {
            var currentSelection = $('select[name="dpd_office"]').val();
            var selectedCity = $('input[name="city"]').val();
            var selectedCountry = $('select[name="id_country"]').val();
            var url = AJAX_URL + '?filterCity=' + selectedCity + '&filterCountry=' + selectedCountry;
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

        $(document).on('blur', 'input[name="city"], input[name="country"]', function(e){
            filterPickupOffice();
        });


        filterPickupOffice();

        $("#js-delivery input[type='radio']").change(function(e){
            var currentSelectedCarrierId = $(this).val();
            if(currentSelectedCarrierId[currentSelectedCarrierId.length-1] == ',') {
                currentSelectedCarrierId = currentSelectedCarrierId.substring(0, currentSelectedCarrierId.length-1);
            }

            $('.carrier-extra-content').hide();

            var relatedExtraContent = $(this).parents('.delivery-option').next();
            if(relatedExtraContent) {
                relatedExtraContent.show();
            }

            if($('.dpd-normalizer:visible').length > 0) {
                if (_DPD_CARRIERS.includes(currentSelectedCarrierId)) {
                    hideContinueButton();
                } else {
                    showContinueButton();
                }
            } else {
                showContinueButton();
            }
        });

        $(".dpd_confirm_country").select2({
            ajax: {
                url: function(params) {
                    var term = params.term;
                    var url = AJAX_URL + '?autocomplete=true&type=country';

                    if(term) {
                        url += '&value=' + term;
                    }

                    return url;
                },
                dataType: 'json',
                minimumInputLength: 3,
                delay: 250
            }
        }).on("select2:select", function (e) {
            if(typeof window._DPD_NORMALIZER_ON_CHECKOUT_ !== 'undefined') {
                saveCurrentAddress(function (response) {
                    location.reload();
                }, false);
            }
            cleanDataBelow('country');
        });
        

        $(".dpd_confirm_region").select2({});

        $(".dpd_confirm_city").select2({
            ajax: {
                url: function(params) {
                    var term = params.term;
                    var currentAddress = getCurrentAddress();
                    var url = AJAX_URL + '?autocomplete=true&type=city';
                    if(term) {
                        url += '&value=' + term;
                    }

                    if(currentAddress['country_id']) {
                        url += '&countryid='+currentAddress['country_id'];
                    }

                    if( $.trim($(".dpd_confirm_region").val()) !== '') {
                        url += '&state='+$.trim($(".dpd_confirm_region").val());
                    }

                    return url;
                },
                dataType: 'json',
                minimumInputLength: 3,
                delay: 250
            }
        }).on("select2:select", function (e) {

            if(typeof window._DPD_NORMALIZER_ON_CHECKOUT_ !== 'undefined') {
                saveCurrentAddress(function (response) {
                    location.reload();
                }, false);
            }

            cleanDataBelow('city');

            var postcode = e.params.data.postcode

            var data = {
                id: postcode,
                text: postcode
            }

            var $confirm_postcode = $('.dpd_confirm_postcode');
            if ($confirm_postcode.find("option[value='" + data.id + "']").length) {
                $confirm_postcode.val(data.id).trigger('change');
            } else {
                // Create a DOM Option and pre-select by default
                var newOption = new Option(data.text, data.id, true, true);
                // Append it to the select
                $confirm_postcode.append(newOption).trigger('change');
            }

            $confirm_postcode.val(postcode);
            $confirm_postcode.trigger('change');
        });

        $(".dpd_confirm_postcode").select2({
            ajax: {
                url: function(params) {
                    var term = params.term;
                    var currentAddress = getCurrentAddress();
                    var url = AJAX_URL + '?autocomplete=true&type=postcode';

                    if(term) {
                        url += '&value=' + term;
                    }

                    if(currentAddress['city_id']) {
                        url += '&siteid=' + currentAddress['city_id'];
                    }


                    return url;
                },
                dataType: 'json',
                minimumInputLength: 2,
                delay: 250
            }
        }).on("select2:select", function (e) {
            cleanDataBelow('postcode');
        });

        $(".dpd_confirm_street").select2({
            tags: true,
            ajax: {
                url: function(params) {
                    var term = params.term;
                    var currentAddress = getCurrentAddress();
                    var url = AJAX_URL + '?autocomplete=true&type=street';

                    if(term) {
                        url += '&value=' + term;
                    }

                    if(currentAddress['city_id']) {
                        url += '&siteid=' + currentAddress['city_id'];
                    }

                    return url;
                },
                dataType: 'json',
                minimumInputLength: 2,
                delay: 250
            }
        }).on("select2:select", function (e) {
            cleanDataBelow('street');
        });

        $(".dpd_confirm_office").select2({
            ajax: {
                url: function(params) {
                    var term = params.term;
                    var currentAddress = getCurrentAddress();
                    var url = AJAX_URL + '?autocomplete=true&type=office';

                    if(term) {
                        url += '&value=' + term;
                    }

                    if(currentAddress['city_id']) {
                        url += '&siteid=' + currentAddress['city_id'];
                    }

                    if(currentAddress['country_id']) {
                        url += '&countryid=' + currentAddress['country_id'];
                    }

                    return url;
                },
                dataType: 'json',
                minimumInputLength: 2,
                delay: 250
            }
        }).on("select2:select", function (e) {
            if(typeof window._DPD_NORMALIZER_ON_CHECKOUT_ !== 'undefined') {
                saveCurrentAddress(function(response){
                    location.reload();
                }, false);
            }

            cleanDataBelow('office');
        });

        $('.dpd_confirm_street_no, .dpd_confirm_block_no, .dpd_confirm_app_no').on('blur', function () {
            cleanDataBelow('extra');
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
                    $('.dpd-normalizer:visible .current_delivery_method').val($(this).attr('data-method'));
                    $('.dpd-normalizer .save_and_validate').click();
                }
                return false;
            });
        }

        function getIdFromSelect2Element(elementSelector) {
            if (
                $(elementSelector)
                &&  $(elementSelector).select2('data')
                &&  $(elementSelector).select2('data')[0]
                &&  $(elementSelector).select2('data')[0]['id']
            ) return $(elementSelector).select2('data')[0]['id'];

            return undefined;
        }

        function getTextFromSelect2Element(elementSelector) {
            if (
                $(elementSelector)
                &&  $(elementSelector).select2('data')
                &&  $(elementSelector).select2('data')[0]
                &&  $(elementSelector).select2('data')[0]['text']
            ) return $(elementSelector).select2('data')[0]['text'];

            return undefined;
        }

        function getCurrentAddress() {
            var block = [
                $('.dpd-normalizer:visible .dpd_confirm_street_no').val(),
                $('.dpd-normalizer:visible .dpd_confirm_block_no').val(),
                $('.dpd-normalizer:visible .dpd_confirm_app_no').val()
            ];

            return {
                'country_id': getIdFromSelect2Element('.dpd_confirm_country:visible'),
                'city_id': getIdFromSelect2Element('.dpd_confirm_city:visible'),
                'postcode': getIdFromSelect2Element('.dpd_confirm_postcode:visible'),
                'street_id': getIdFromSelect2Element('.dpd_confirm_street:visible'),
                'street_name': getTextFromSelect2Element('.dpd_confirm_street:visible'),
                'office_id':  getIdFromSelect2Element('.dpd_confirm_office:visible'),
                'shipment_type': $('.dpd-normalizer:visible .current_delivery_method').val(),
                'order_id': $('.dpd-normalizer:visible .dpd_id_order').val(),
                'address_id': $('.dpd-normalizer:visible .dpdgeopost_id_address').val(),
                'block': block.join(':')
            };
        }

        function cleanDataBelow(startCleaningFrom) {
            switch (startCleaningFrom) {
                case 'country':
                    $('.dpd-normalizer:visible .dpd_confirm_city').val(null).trigger('change');
                case 'city':
                    $('.dpd-normalizer:visible .dpd_confirm_postcode').val(null).trigger('change');
                case 'postcode':
                    $('.dpd-normalizer:visible .dpd_confirm_street').val(null).trigger('change');
                case 'street':
                    $('.dpd-normalizer:visible .dpd_confirm_street_no').val(null).trigger('change');
                    $('.dpd-normalizer:visible .dpd_confirm_block_no').val(null).trigger('change');
                    $('.dpd-normalizer:visible .dpd_confirm_app_no').val(null).trigger('change');
            }

        }

        if(  $("#show_error_message").length == 0 ) {
            $('.dpd-normalizer .dpd-confirmation-error').hide();
        }


        $('.dpd-normalizer .dpd-confirmation-confirmed').hide();

        $('.dpd-normalizer .save_and_validate').click(function(e){
            e.preventDefault();
            $('.dpd-normalizer:visible .dpd-confirmation-error').hide();
            $('.dpd-normalizer:visible .dpd-confirmation-confirmed').hide();
            $('.dpd-normalizer:visible .dpd-confirmation-confirmed .js-ambiguu-street').hide();
            $('#dpd_street_error').hide();

            saveCurrentAddress(function(response){
                if(response.error) {
                    $('.dpd-normalizer:visible .dpd-confirmation-error').html(response.error.message);
                    $('.dpd-normalizer:visible .dpd-confirmation-error').show();
                    if(typeof changeShipmentCreationButtonAccessibility === "function") {
                        //shipment always enabled
                        changeShipmentCreationButtonAccessibility(true);
                    }
                    hideContinueButton();
                }

                if(response.valid) {
                    $('.dpd-normalizer:visible .dpd-confirmation-confirmed').show();
                    if(typeof changeShipmentCreationButtonAccessibility === "function") {
                        changeShipmentCreationButtonAccessibility(true);
                    }

                    if( $('#dpd-is-admin').val() != '1') {
                        updateDeliveryPrice();
                    }

                    showContinueButton();
                }

            }, true);

            return false;
        });

        function saveCurrentAddress(withResponseDo, hideLoaderWhenDone) {
            var data = getCurrentAddress();
            data['action'] = 'updateAddress';
            data['token'] = _DPD_TOKEN_;
            showLoader();
            $.post(AJAX_URL, data, function(data) {

                if(typeof withResponseDo === 'function') {
                    withResponseDo(data);

                }
                if(hideLoaderWhenDone) {
                    hideLoader();
                }

            }, 'json');

            // on call it should check if valid
            // if not valid do not save
        }

        function validateCurrentAddress(withResponseDo, hideLoaderWhenDone) {
            var data = getCurrentAddress();
            data['action'] = 'validateAddress';
            data['token'] = _DPD_TOKEN_;
            showLoader();
            $.post(AJAX_URL, data, function (data) {
                if (typeof withResponseDo === 'function') {
                    withResponseDo(data);
                }
                if (hideLoaderWhenDone) {
                    hideLoader();
                }

            }, 'json');
        }

        function hideContinueButton() {
            $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
            $('button[name="confirmDeliveryOption"]').hide();
        }

        function showContinueButton() {
            $('button[name="confirmDeliveryOption"]').removeAttr('disabled');
            $('button[name="confirmDeliveryOption"]').show();
        }

        function setInitialStateOfContinueButton() {
            var currentSelectedCarrierId = $("#js-delivery input[type='radio']:checked").val();
            if(currentSelectedCarrierId && currentSelectedCarrierId[currentSelectedCarrierId.length-1] == ',') {
                currentSelectedCarrierId = currentSelectedCarrierId.substring(0, currentSelectedCarrierId.length-1);
            }


            if(! (typeof _DPD_CARRIERS === 'undefined' || _DPD_CARRIERS === null ) ) {
                if($('.dpd-normalizer:visible').length > 0) {
                    if( _DPD_CARRIERS.includes(currentSelectedCarrierId)) {
                        hideContinueButton();

                        validateCurrentAddress(function(response){
                           if(response.valid) {
                               showContinueButton();
                           }
                        }, true);
                    } else {
                        showContinueButton();
                    }
                }
            }

        }

        function showLoader() {
            var html = "<div id='normalization_form_loader'><div class='loader'>Loading...</div></div>"
            $('body').append(html);
        }
        window.showLoader = showLoader;

        function hideLoader() {
            $('#normalization_form_loader').remove();
        }
        window.hideLoader = hideLoader;

        function updateDeliveryPrice() {
            showLoader();
            location.reload();
        }

        dpd_SelectedTabAddress();
        setInitialStateOfContinueButton();
    }

    init_normalization_form();
    window.init_normalization_form = init_normalization_form;
});