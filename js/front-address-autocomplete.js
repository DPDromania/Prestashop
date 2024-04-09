var PostcodeAutocompleter = {};

$(document).ready(function () {
    var addressForm = $('#add_address');
    if(addressForm){
        var postcodeField = $('#postcode', addressForm);
        if(postcodeField){
            var fields = ['address1', 'address2', 'city', 'id_country', 'id_state'];
            var extraParams = {};
            extraParams['token'] = dpd_token;
            extraParams['action'] = 'postcode-recommendation';
            $(fields).each(function(index, item){
                extraParams[item] = function( ) {
                    return $('#' + item).val();
                }
            });
            $( postcodeField ).autocomplete(window.baseUri + "modules/dpdgeopost/dpdgeopost.ajax.php", {
                extraParams: extraParams,
                delay: 100,
                selectFirst: false,
                max: 10,
                minChars: 0,
                highlight: function(item, value) {
                    return item;
                },
                formatItem: function(data, position, max, value, term) {
                    return "<span class='address'>" + value + "</span>";
                },
                parse: function(data) {
                    var parsed = [];
                    var rows = $.parseJSON(data);
                    for (var i=0; i < rows.length; i++) {
                        parsed[parsed.length] = {
                            data: rows[i]['postcode'],
                            value: rows[i]['label'],
                            result: rows[i]['postcode']
                        };
                    }
                    $(postcodeField).focus();
                    PostcodeAutocompleter.started = false;
                    return parsed;
                }
            });
            $(fields).each(function(index, item) {
                $('#' + item).on('change', function(event) {
                    var all_completed = true;
                    $(fields).each(function(index, item) {
                        var field = $('#' + item);
                        if((field.hasClass('is_required ') || item === 'id_state') && field.val() === '' ) {
                            all_completed = false;
                        }
                    });
                    if(all_completed && PostcodeAutocompleter.started !== true) {
                        PostcodeAutocompleter.value = $(postcodeField).val();
                        PostcodeAutocompleter.started = true;
                        $( postcodeField ).flushCache();
                        //simulate key up to trigger autocomplete
                        var e = jQuery.Event("keydown.autocomplete");
                        e.keyCode = 38;
                        $(postcodeField).focus().trigger(e);
                    }
                });
            });
        }
    }
});
