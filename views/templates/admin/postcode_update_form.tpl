<h2>{l s='Update postcode database - for DPD Carrier' mod='dpdgeopost'}</h2>
<form id="csv_upload" class="defaultForm" action="{$saveAction|escape:'htmlall':'UTF-8'}&menu=postcodeUpdate_upload"
      method="post" enctype="multipart/form-data">
    <fieldset id="general">
        <legend>
            <img src="{$smarty.const._DPDGEOPOST_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png"
                 alt="{l s='Upload & Import' mod='dpdgeopost'}"/>
            {l s='Upload & import' mod='dpdgeopost'}
        </legend>

        <label>
            {l s='CSV file received from DPD' mod='dpdgeopost'}
        </label>

        <div class="margin-form">
            <input id="csv" type="file" name="csv"/>

            <p class="preference_description">
                {l s='Choose a CSV file to be uploaded. This file will be used for postcode database update.' mod='dpdgeopost'}
            </p>
        </div>
        <div class="clear"></div>

        <div class="margin-form">
            <input type="submit" class="button" name="" value="{l s='Upload & Import' mod='dpdgeopost'}"/>
        </div>
    </fieldset>

    <br/>

</form>


<form id="csv_import" class="defaultForm" action="{$saveAction|escape:'htmlall':'UTF-8'}&menu=postcodeUpdate_import"
      method="post">
    <fieldset id="general">
        <legend>
            <img src="{$smarty.const._DPDGEOPOST_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png"
                 alt="{l s='Import' mod='dpdgeopost'}"/>
            {l s='Import an existing file' mod='dpdgeopost'}
        </legend>

        <label>
            {l s='File name of the CSV found in upload/dpd/postcode_update/' mod='dpdgeopost'}
        </label>

        <div class="margin-form">
            <input id="file_path" type="text" name="file_path"/>

            <p class="preference_description">
                {l s='Enter the file name available on server and run database update with it.' mod='dpdgeopost'}
            </p>
        </div>
        <div class="clear"></div>

        <div class="margin-form">
            <input type="submit" class="button" name="" value="{l s='Import' mod='dpdgeopost'}"/>
        </div>
    </fieldset>

    <br/>

</form>


<fieldset id="general">
    <legend>
        <img src="{$smarty.const._DPDGEOPOST_IMG_URI_|escape:'htmlall':'UTF-8'}settings.png"
             alt="{l s='Import' mod='dpdgeopost'}"/>
        {l s='Available update files on server' mod='dpdgeopost'}
    </legend>
    <label>
        {l s='List of file uploaded' mod='dpdgeopost'}
    </label>
    <div class="margin-form">
        {if count($uploadedFiles) }
            <table cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom:10px;" class="table document">
                <thead>
                <tr style="height: 40px" class="nodrag nodrop">
                    <th class="center">
                        <span class="title_box">{l s='Filename' mod='dpdgeopost'}</span>
                    </th>
                    <th class="center">
                        <span class="title_box">{l s='Modified' mod='dpdgeopost'}</span>
                    </th>
                </tr>
                </thead>

                <tbody>
                {foreach from=$uploadedFiles item=modified key=file}
                    <tr>
                        <td class="center">
                            {$file}
                        </td>
                        <td class="center">
                            {date('d-m-Y H:i',$modified)}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {/if}
    </div>
</fieldset>
