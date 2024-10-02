
<div id="dpdgeopost_locker" {if empty($dpd_office_name)}style="display: none"{/if}>
    {if !empty($dpd_office_name)}
        <div class="alert alert-success">Ati selectat livrare la {$dpd_office_name} </div>
    {/if}
</div>
<script>
    var cart_id = '{$dpd_id_cart|escape:'htmlall':'UTF-8'}';
    var delivery_address_id = '{$dpd_id_delivery_address|escape:'htmlall':'UTF-8'}';
    {literal}

    var AJAX_URL = window._DPDGEOPOST_AJAX_URI_;
    var DPD_TOKEN = window._DPD_TOKEN_;

    window.addEventListener("DOMContentLoaded", () => {


    });

    window.addEventListener("load", () => {
        // Fully loaded!
    });

    window.addEventListener('message',
        function (e) {
            let returnedOfficeJsonObject = e.data;
            if(returnedOfficeJsonObject['id'] == undefined) {
                return;
            }



            $.ajax({
                type: "POST",

                url: AJAX_URL,
                dataType: "json",
                global: false,
                data: "ajax=true&token=" + encodeURIComponent( window._DPD_TOKEN_) +
                    "&action=update_locker" +
                    "&cart_id=" + cart_id +
                    "&delivery_address_id=" + delivery_address_id +
                    "&dpd_office_id=" + returnedOfficeJsonObject['id'] +
                    "&dpd_office_type=" + returnedOfficeJsonObject['type'] +
                    "&dpd_office_name=" + encodeURIComponent(returnedOfficeJsonObject['nameEn']),
                success: function(response)
                {

                    if (response.error)
                        $('#dpdgeopost_locker').hide().html('<div class="alert alert-danger">'+response.error+'</div>').slideDown();
                    else {
                        $('#dpdgeopost_locker').hide().html('<div class="alert alert-success">Ati selectat livrare la '+ returnedOfficeJsonObject['address']['fullAddressString'] +'!</div>').slideDown();
                        if (typeof prestashop !== 'undefined') {
                             prestashop.emit('updateCart', {
                                 reason: {},
                                 resp: response
                             });
                        }
                    }
                },
                error: function(resp)
                {
                    console.log(resp);
                }
            });
        },
        false);


    {/literal}
</script>
{if $dpd_site_id > 0}
    <iframe id="frameOfficeLocator" name="frameOfficeLocator" src="https://services.dpd.ro/office_locator_widget_v3/office_locator.php?lang=en&showAddressForm=0&siteID={$dpd_site_id}&showOfficesList=0&selectOfficeButtonCaption=Select this office&countryId={$dpd_country_id}"  width="665px" height="500px"></iframe>
{else}
    <iframe id="frameOfficeLocator" name="frameOfficeLocator" src="https://services.dpd.ro/office_locator_widget_v3/office_locator.php?lang=en&showAddressForm=0&showOfficesList=0&selectOfficeButtonCaption=Select this office&countryId={$dpd_country_id}"  width="665px" height="500px"></iframe>
{/if}
