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

});