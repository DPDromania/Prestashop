$(document).ready(function(){


    var updateDpdShippingPriceHandler = function(){

        $('#ajax_running').slideDown();

        var method_id = $('#dpd_shipping_method').val();

        $.ajax({
            type: "POST",
            async: true,
            url: dpd_geopost_ajax_uri,
            dataType: "json",
            global: false,
            data: "ajax=true&token=" + encodeURIComponent(dpd_geopost_token) +
            "&id_shop=" + encodeURIComponent(dpd_geopost_id_shop) +
            "&id_lang=" + encodeURIComponent(dpd_geopost_id_lang) +
            "&calculatePrice=true" +
            "&method_id=" + encodeURIComponent(method_id) +
            "&id_address=" + encodeURIComponent($('#dpdgeopost_id_address').val()) +
            "&id_order=" + encodeURIComponent(id_order),
            success: function(resp)
            {

                if (resp.error || resp.notice)
                {
                    if (resp.notice)
                    {
                        $('#dpdgeopost_notice_container').hide().html('<p class="warn">'+resp.notice+'</p>').slideDown();
                        changeShipmentCreationButtonAccessibility(true);
                    }
                    else
                    {
                        $('#dpdgeopost_notice_container').hide().html('<p class="error">'+resp.error+'</p>').slideDown();
                        changeShipmentCreationButtonAccessibility(false);
                    }
                }
                else
                {
                    $('#dpdgeopost_notice_container').slideUp().html('');
                    changeShipmentCreationButtonAccessibility(true);
                }

                if (resp.price == '---')
                    changeShipmentCreationButtonAccessibility(false);

                if ($('#dpdgeopost_service_price').text() != resp.price)
                {
                    $('#dpdgeopost_service_price').fadeOut('slow', function(){
                        $('#dpdgeopost_service_price').text(resp.price);
                        $(this).fadeIn('slow', function(){});
                    });
                }

                if (stringToNumber($('#dpdgeopost_paid_price').text()) < stringToNumber(resp.price))
                    $('#dpdgeopost_paid_price, #dpdgeopost_service_price').css('color', 'red');
                else
                    $('#dpdgeopost_paid_price, #dpdgeopost_service_price').css('color', 'inherit');

                $('#ajax_running').slideUp();
            },
            error: function(resp)
            {

                changeShipmentCreationButtonAccessibility(false);
                $('#ajax_running').slideUp();
            }
        });
    };

    // $('#dpd_shipping_method, #dpdgeopost_id_address').live('change', updateDpdShippingPriceHandler);
    $(document).on('change', '#dpd_shipping_method, #dpdgeopost_id_address', updateDpdShippingPriceHandler)
    updateDpdShippingPriceHandler();

	$(document).on('click', '#dpdgeopost_shipment_creation_save', function(){
	//$('#dpdgeopost_shipment_creation_save').live('click', function(){
		$('#ajax_running').slideDown();

		var method_id = $('#dpd_shipping_method').val();
		var parcels = collectParcels();

		var swapEnabled = $('#dpd_swap_enabled').is(':checked') ? '1': '0';
		var reusableEnable = $('#dpd_reusable_enabled').is(':checked') ? '1' : '0';
		var rodEnabled = $('#dpd_rod_enabled').is(':checked') ? '1': '0';
		var voucherEnabled = $('#dpd_voucher_enabled').is(':checked') ? '1' : '0';
		var shipmentNote = $.trim($('#dpd_shipment_note').val()) ;
		var shipmentReference = $.trim($('#dpd_shipment_reference').val()) ;

		$.ajax({
			type: "POST",
			async: true,
			url: dpd_geopost_ajax_uri,
			dataType: "json",
			global: false,
			data: "ajax=true&token=" + encodeURIComponent(dpd_geopost_token) +
				  "&id_shop=" + encodeURIComponent(dpd_geopost_id_shop) +
				  "&id_lang=" + encodeURIComponent(dpd_geopost_id_lang) +
				  "&saveShipment=true" +
				  "&method_id=" + encodeURIComponent(method_id) +
				  "&id_address=" + encodeURIComponent($('#dpdgeopost_id_address').val()) +
				  "&id_order=" + encodeURIComponent(id_order) +
				  "&swap_enabled=" + encodeURIComponent(swapEnabled) +
				  "&rod_enabled=" + encodeURIComponent(rodEnabled) +
				  "&voucher_enabled=" + encodeURIComponent(voucherEnabled) +
				  "&shipment_note=" + encodeURIComponent(shipmentNote) +
                  "&shipment_reference=" + encodeURIComponent(shipmentReference) +
				  "&reusable_enabled=" + encodeURIComponent(reusableEnable) +
				  parcels,
			success: function(resp)
			{
				if (resp.error)
					$('#dpdgeopost_shipment_creation .message_container').hide().html('<p class="error">'+resp.error+'</p>').slideDown();
				else {
					window.location.reload();
				}

				$('#ajax_running').slideUp();
			},
			error: function()
			{
				$('#ajax_running').slideUp();
			}
		});
	});

	$(document).on('click', '.parcel_selection', function(){
	// $('.parcel_selection').live('change', function(){
		updatePercalDescriptions();
		updatePercalTotals();
	});

	$(document).on('click', '#dpdgeopost_create_shipment', function(){
		if ($('#dpd_shipping_method').val() == '2505' || $('#dpd_shipping_method').val() == '25052') {
			$('#dpd_reusable_enabled_container').show();
		} else {
			$('#dpd_reusable_enabled_container').hide();
		}

	//$('#dpdgeopost_create_shipment').live('click', function(){
		$('#dpdgeopost_shipment_creation').bPopup();
	});

	$(document).on('click', '#dpdgeopost_shipment_creation_cancel, #dpdgeopost_shipment_creation_close', function (){
	//$('#dpdgeopost_shipment_creation_cancel, #dpdgeopost_shipment_creation_close').live('click', function(){
		$('div.message_container p.error').css('display', 'none');
		$('#dpdgeopost_shipment_creation').bPopup().close();
	});

	$(document).on('click', '#dpdgeopost_edit_shipment', function(){
	//$('#dpdgeopost_edit_shipment').live('click', function(){
		$("#dpdgeopost_shipment_creation :input[value!='']").removeAttr("disabled");
		$('.buttons_container').show();
		$('#dpdgeopost_shipment_creation_close').hide();
		$('#dpdgeopost_shipment_creation').bPopup();
	});

	$(document).on('click', '#dpdgeopost_preview_shipment', function (){
	//$('#dpdgeopost_preview_shipment').live('click', function(){
		$('#dpdgeopost_shipment_creation .message_container').slideUp().html('');
		$('.buttons_container').hide();
		$("#dpdgeopost_shipment_creation :input").attr("disabled", true);
		$('#dpdgeopost_shipment_creation_close').removeAttr('disabled').show();
		$('#dpdgeopost_shipment_creation').bPopup();
	});

	$(document).on('change keyup paste', '#parcel_selection_table .parcel_weight', function (){
	//$('#parcel_selection_table .parcel_weight').live("change keyup paste", function(){
		updatePercalTotals();
	});
});

function stringToNumber(string) {
	if (typeof(string) == "number")
		return string;

	return Number(string.replace(/[,]/g, '.').replace(/[^0-9.]/g,''));
}

function updatePercalDescriptions()
{
	$('#parcel_descriptions_table td.parcel_description input[type="text"]').attr('value', '').removeAttr('disabled');
	$('#parcel_descriptions_table td.parcel_description input[type="hidden"]').attr('value', '');

	$('#parcel_selection_table .product_id').each(function(){
		var product_id = $(this).text();
		var parcel_id = $(this).siblings().find('select').val();
		var description = '';
		var $parcel_description_field = $('#parcel_descriptions_table td.parcel_id_'+parcel_id).siblings().find('input[type="text"]');
		var $parcel_description_safe = $parcel_description_field.siblings('input[type="hidden"]:first');

		if ($parcel_description_safe.attr('value') == '')
			description = product_id;
		else
			description =  $.trim($parcel_description_safe.attr('value'))   + ',' + product_id;

		$parcel_description_field.attr('value', description);
		$parcel_description_safe.attr('value', description);
	});

	$('#parcel_descriptions_table td.parcel_description input[type="text"][value=""]').attr('disabled', 'disabled');
}

function updatePercalTotals()
{
	$('.parcel_total_weight').each(function(){
		var parcel_number = $(this).siblings().find('select').val();
		//var total_parcel_weight = Number($(this).siblings('td.parcel_weight').attr('rel'));
		var total_parcel_weight = Number($(this).siblings('td.parcel_weight').find('input').val());

		$(this).parents('tr:first').siblings('tr').find('select option[value="'+parcel_number+'"]:selected').each(function(){
			//total_parcel_weight += Number($(this).parents('td:first').siblings('td.parcel_weight').attr('rel'));
			total_parcel_weight += Number($(this).parents('td:first').siblings('td.parcel_weight').find('input').val());
		});
		if (total_parcel_weight.toFixed(3) == '' || total_parcel_weight.toFixed(3) == 'NaN')
			total_parcel_weight = 0;

		$(this).attr('rel', total_parcel_weight.toFixed(3)).find('input').val(total_parcel_weight.toFixed(3));
	});
}

function collectParcels()
{
	var parcels = '';

	$('#parcel_descriptions_table td.parcel_description input[type="text"]:enabled').each(function(){
		var parcel_number = $.trim($(this).parent().siblings('td:first').text()) ;
		parcels +=  '&parcels['+parcel_number+'][products]='+   encodeURIComponent($.trim($(this).siblings('input[type="hidden"]:first').val()))+
					'&parcels['+parcel_number+'][description]='+encodeURIComponent( $.trim($(this).val()) )+
					'&parcels['+parcel_number+'][weight]='+	 encodeURIComponent( $.trim($('#parcel_selection_table tbody tr:nth-child('+parcel_number+') td.parcel_total_weight input').val()) );
	});
	console.log(parcels);
	return parcels;
}

function changeShipmentCreationButtonAccessibility(enabled)
{
	if (enabled) {
		$('#dpdgeopost_create_shipment').removeAttr('disabled');
		$('#dpdgeopost_create_shipment_message').hide();
	} else {
		$('#dpdgeopost_create_shipment_message').show();
		$('#dpdgeopost_create_shipment').attr('disabled', 'disabled');
	}
}

function deleteShipment()
{
	$('#ajax_running').slideDown();

	$.ajax({
		type: "POST",
		async: true,
		url: dpd_geopost_ajax_uri,
		dataType: "json",
		global: false,
		data: "ajax=true&token=" + encodeURIComponent(dpd_geopost_token) +
			  "&id_shop=" + encodeURIComponent(dpd_geopost_id_shop) +
			  "&id_lang=" + encodeURIComponent(dpd_geopost_id_lang) +
			  "&id_order=" + encodeURIComponent(id_order) +
			  "&deleteShipment=true",
		success: function(resp)
		{
			if (resp.error)
				$('#dpdgeopost_notice_container').hide().html('<p class="error">'+resp.error+'</p>').slideDown();
			else
				window.location.reload();
		}
	});

	$('#ajax_running').slideUp();
}
