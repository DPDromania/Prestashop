$(document).ready(function() {
    var AJAX_URL = window._DPDGEOPOST_AJAX_URI_;
    var DPD_TOKEN = window._DPD_TOKEN_;


    function filterPickupOffice() {

        var currentSelection = $('select[name="dpd_office"]').val();
        var selectedCity = $('input[name="city"]').val();
        var selectedCountry = $('select[name="id_country"]').val();

        if (selectedCity == undefined || selectedCountry == undefined) {
            return;
        }

        var url = AJAX_URL + '?filterCity=' + selectedCity + '&filterCountry=' + selectedCountry;

        $('select[name="dpd_office"]').attr('disabled', 'disabled');
        $('select[name="dpd_office"]').html('...');



        $.getJSON(url, function (response) {
            if (response.empty) {
                $('select[name="dpd_office"]').attr('disabled', 'disabled');
                $('select[name="dpd_office"]').html(response.html);
            } else {
                $('select[name="dpd_office"]').html(response.html);
                $('select[name="dpd_office"]').removeAttr('disabled');
                setTimeout(function () {
                    $('select[name="dpd_office"]').val(currentSelection);
                }, 250);

                //$('select[name="dpd_office"]').parent().append('<iframe id="frameOfficeLocator" name="frameOfficeLocator" src="https://services.dpd.ro/office_locator_widget_v2/office_locator.php?lang=en&showAddressForm=0&showOfficesList=0&selectOfficeButtonCaption=Select this office"  width="800px" height="500px"></iframe>')
            }

        });
    }

    $('select[name="dpd_office"]').on('change', function() {
        siteId = ($('select[name="dpd_office"]').val());
        source = "https://services.dpd.ro/office_locator_widget_v2/office_locator.php?lang=en&showAddressForm=0&showOfficesList=0&siteID=' + siteId + ¡&selectOfficeButtonCaption=Select this office";
        //$('#frameOfficeLocator').attr('src', source);

    });

    $(document).on('blur', 'input[name="city"], input[name="country"]', function (e) {
        filterPickupOffice();
    });

    $(document).on ('click', "input[type='radio']", function(e) {
        console.log(e);
    })

    $(document).on('blur', 'input[name="postcode"]', function(){

        let zip_code = $('input[name="postcode"]').val();
        let country_id = $('select[name="id_country"]').val();

        if (zip_code == '') {
            console.log('empty zip code');
            return;
        }

        if (country_id == '') {
            console.log('empty country');
            return;
        }

        $.ajax({
            type: "POST",
            async: true,
            url: AJAX_URL,
            dataType: "json",
            global: false,
            data: "ajax=true&token=" + encodeURIComponent(DPD_TOKEN) +
                "&action=validateZipCode" +
                "&country_id=" + country_id +
                "&zip_code=" + encodeURIComponent(zip_code),
            success: function(resp)
            {
                if (resp.error)
                    $('#dpdgeopost_shipment_creation .message_container').hide().html('<p class="error">'+resp.error+'</p>').slideDown();
                else {
                    console.log(resp);
                }

            },
            error: function(resp)
            {
               console.log(resp);
            }
        });
    });

    filterPickupOffice();

    function getCountryId(code)
    {
        console.log(code);
        switch (code) {
            case 'RO':
                return 642;
            case 'BG':
                return 100;
            case 'GR':
                return 300;
            case 'HU':
                return 348;
            case 'PL':
                return 616;
            case 'SL':
                return 703;
            case 'SK':
                return 705;
            case 'CZ':
                return 203;
            case 'HR':
                return 191;
            case 'AT':
                return 40;
            case 'IT':
                return 380;
            case 'DE':
                return 276;
            case 'ES':
                return 724;
            case 'FR':
                return 250;
            case 'NL':
                return 528;
            case 'BE':
                return 56;
            case 'EE':
                return 233;
            case 'DK':
                return 208;
            case 'LU':
                return 442;
            case 'LV':
                return 428;
            case 'LT':
                return 440;
            case 'FI':
                return 246;
            case 'PT':
                return 620;
            case 'SE':
                return 752;
            default:
                return 642;
        }
    }

});