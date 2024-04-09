$(document).ready(function () {
    var addressForm = $('#address_form');
    if(false && addressForm){
        var postcodeField = $('#postcode');
        if(postcodeField){
            postcodeField.before('<a id="refresh-postcode" href="#refresh-postcode">'+dpd_search_postcode_test+'</a>');
            $('#refresh-postcode').click(function(e){
                $( postcodeField ).autocomplete('search');
            });
            $( postcodeField ).autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "/modules/dpdgeopost/dpdgeopost.ajax.php",
                        data: addressForm.serialize()+'&action=postcode-recommendation'+'&token='+dpd_token,
                        success: function( data ) {
                            data = jQuery.parseJSON(data);
                            if (data.length == 0 ){
                                alert(dpd_search_postcode_empty_result_alert);
                            }

                            response( data );
                        }
                    });
                },
                minLength: 0,
                select: function( event, ui ) {
                    $( postcodeField ).val(ui.item.postcode);
                    return false;
                },
                focus: function( event, ui ) {
                    return false;
                }
            }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                return $( "<li>" )
                    .append( "<a>" + item.label + "<br>" + item.postcode + "</a>" )
                    .appendTo( ul );
            };

        }
    }

    if($('#address_form').length > 0) {
        DPD_ADMIN_CITY_ID = false;
        DPD_ADMIN_STREET_ID = false;


        var beforeButton = "<button value=\"1\" id=\"address_form_check_btn\" name=\"submitAddaddress\" class=\"btn btn-default pull-right\">\n" +
            "\t\t\t\t\t\t\t<i class=\"process-icon-save\"></i> Check with DPD\n" +
            "\t\t\t\t\t\t</button>";
        $('#address_form_submit_btn').before(beforeButton);

        $('#address_form_check_btn').click(function(e){
            e.preventDefault();

            var country_db_id = $('#id_country').val();
            var state_db_id = $('#id_state').val();
            var city_text = $('#city').val();
            var street_text = $('#address1').val();
            var address_id = $('#id_address').val();


            var data = {
                'address_id': address_id,
                'country_id': country_db_id,
                'state_id': state_db_id,
                'city': city_text,
                'city_id_ws': $('.js_city_ws:checked').val(),
                'street': street_text,
                'street_id_ws': $('.js_street_ws:checked').val(),
                'action': 'admin_change_address',
                'token': dpd_token
            };

            var url = "/modules/dpdgeopost/dpdgeopost.ajax.php";
            if (typeof baseDir !== "undefined") { 
                if(baseDir && baseDir.length > 0) {
                    var suburl = baseDir.replace(/\\/g,"/");
                    url = suburl + url;
                }
            } else {
                var suburl = baseDirNew.replace(/\\/g,"/");
                url = suburl + url;
            }
            url += "?token="+dpd_token+"&action=admin_change_address";

            $.ajax({
               type: "POST",
                url: url,
                data: data,
                success: function (response) {
                       if(response['message']) {
                           alert(response['message']);
                       }

                       if(response['options']['city']) {
                           $('#dpd_city_options').remove();
                            var cityHtml = '<div id="dpd_city_options">';
                            cityHtml +=  '<span class="important" style="color: darkred;">' + response['message'] + '</span><br />';
                            for(var i = 0; i < response['options']['city'].length; i++) {
                                cityHtml += '<label><input type="radio" name="city_ws" class="js_city_ws" value="'+response['options']['city'][i]['id']+'"  data-text="'+response['options']['city'][i]['name']+'" /> '+response['options']['city'][i]['name']+' </label><br />';
                            }
                            cityHtml += '</div>';
                            $('#city').after(cityHtml);

                           $('.js_city_ws').change(function(e){
                               $('#city').val($(this).attr('data-text'));
                           });
                       }

                       if(response['options']['street']) {
                           $('#dpd_street_options').remove();
                           var streetHtml = '<div id="dpd_street_options">';
                           streetHtml +=  '<span class="important" style="color: darkred;">' + response['message'] + '</span><br >';
                           for(var i = 0; i < response['options']['street'].length; i++) {
                               streetHtml += '<label><input type="radio" name="street_ws" class="js_street_ws" value="'+response['options']['street'][i]['id']+'" data-text="'+response['options']['street'][i]['name']+'" /> '+response['options']['street'][i]['type']  +response['options']['street'][i]['name']+' </label><br />';
                           }
                           streetHtml += '</div>';
                           $('#address1').after(streetHtml);

                           $('.js_street_ws').change(function(e){
                               $('#address1').val($(this).attr('data-text'));
                           });
                       }

                       if(response['finish']) {
                           history.go(-1);
                       }

                },
                dataType: 'json'
            });

        });
    }


    $('#address_form').submit(function( event ) {
        if (dpdCheckAddressLength()===false){
            event.preventDefault();
            dpdAlertErrorProblems();
            $("body").scrollTop($("#address2").offset().top-150);
        }
    });

    $( "#address1,#address2" ).blur(function() {
        dpdAlertErrorProblems();
    });
    $( "#address1,#address2" ).keyup(function() {
        keyPressUpdate();
    });




});

function keyPressUpdate(){
    if (dpdCheckAddressLength()===true){
        $("#dpdAlertErrorProblems").remove();
    }
}

function dpdAlertErrorProblems(){
    $("#dpdAlertErrorProblems").remove();
    if (dpdCheckAddressLength()===false){
        $('#address2').after('<div style="color: red; padding: 10px;" id="dpdAlertErrorProblems">'+dpd_address_validation_length+'</div>');
        $('#dpdAlertErrorProblems').fadeOut( "slow");
        $('#dpdAlertErrorProblems').fadeIn( "slow");
    }
}


function dpdCheckAddressLength(){
    var $addressLength = $("#address1").val().length + $("#address2").val().length;
    if($addressLength < 70 ){
        return true;
    }
    return false;
}

function log( message ) {
    alert(message);
}
