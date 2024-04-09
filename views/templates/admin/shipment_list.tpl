<script>
	var dpdgeopost_error_no_shipment_selected = '{l s='Select at least one shipment' mod='dpdgeopost' js=1}';
	var dpdgeopost_error_puckup_not_available = '{l s='To arrange pickup, manifest or label must be printed' mod='dpdgeopost' js=1}';
	var dpd_geopost_id_lang = '{$dpd_geopost_id_lang|escape:'htmlall':'UTF-8'}';
	var ps14 = {if isset($ps14) && $ps14}1{else}0{/if};

	$(document).ready(function(){
		if (!ps14){
			$('#dpdgeopost_pickup_fromtime, #dpdgeopost_pickup_totime').datetimepicker({
				currentText: '{l s='Now' mod='dpdgeopost'}',
				closeText: '{l s='Done' mod='dpdgeopost'}',
				timeOnly: true,
				ampm: false,
				timeFormat: 'hh:mm:ss',
				timeSuffix: '',
				timeOnlyTitle: '{l s='Choose Time' mod='dpdgeopost'}',
				timeText: '{l s='Time' mod='dpdgeopost'}',
				hourText: '{l s='Hour' mod='dpdgeopost'}',
				minuteText: '{l s='Minute' mod='dpdgeopost'}',
			});
		}

		$("#dpdgeopost_pickup_datetime").datepicker({
			dateFormat:"yy-mm-dd"
		});

		$("table.Shipments .datepicker").datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd'
		});

		$('table#shipment_list .filter').keypress(function(event){
			formSubmit(event, 'submitFilterButtonShipments');
		})
	});
</script>

<form class="form" action="{$full_url|escape:'htmlall':'UTF-8'}" method="post">
	<input type="hidden" value="0" name="submitFilterShipments" id="submitFilterShipments">
	<table id="shipment_list" name="list_table" class="table table-striped"">
		<tbody>
			<tr>
				<td style="vertical-align: bottom;">
					<span style="float: left;">
						{if $page > 1}
							<input type="image" src="../img/admin/list-prev2.gif" onclick="getE('submitFilterShipments').value=1"/>&nbsp;
							<input type="image" src="../img/admin/list-prev.gif" onclick="getE('submitFilterShipments').value={$page|escape:'htmlall':'UTF-8' - 1}"/>
						{/if}
						{l s='Page' mod='dpdgeopost'} <b>{$page|escape:'htmlall':'UTF-8'}</b> / {$total_pages|escape:'htmlall':'UTF-8'}
						{if $page < $total_pages}
							<input type="image" src="../img/admin/list-next.gif" onclick="getE('submitFilterShipments').value={$page|escape:'htmlall':'UTF-8' + 1}"/>&nbsp;
							<input type="image" src="../img/admin/list-next2.gif" onclick="getE('submitFilterShipments').value={$total_pages|escape:'htmlall':'UTF-8'}"/>
						{/if}
						| {l s='Display' mod='dpdgeopost'}
						<select name="pagination" onchange="submit()">
							{foreach from=$pagination item=value}
								<option value="{$value|intval|escape:'htmlall':'UTF-8'}"{if $selected_pagination == $value} selected="selected" {elseif $selected_pagination == NULL && $value == $pagination[1]} selected="selected2"{/if}>{$value|intval|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						</select>
						/ {$list_total|escape:'htmlall':'UTF-8'} {l s='result(s)' mod='dpdgeopost'}
					</span>
					<span style="float: right;">
						<input type="submit" class="btn btn-secondary" value="{l s='Filter' mod='dpdgeopost'}" name="submitFilterButtonShipments" id="submitFilterButtonShipments">
						<input type="submit" class="btn btn-secondary" value="{l s='Reset' mod='dpdgeopost'}" name="submitResetShipments">
					</span>
					<span class="clear"></span>
				</td>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom:10px;" class="table Shipments">
						<colgroup>
							<col width="10px">
							<col width="100px">
							<col width="160px">
							<col width="100px">
							<col width="160px">
							<col width="150px">
							<col>
							<col width="70px">
							<col width="140px">
							<col width="140px">
							<col width="160px">
							<col width="30px">
						</colgroup>
						<thead>
							<tr style="height: 40px" class="nodrag nodrop">
								<th class="center">
									<input type="checkbox" onclick="checkDelBoxes(this.form, 'ShipmentsBox[]', this.checked)" class="noborder" name="checkme">
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Shipment ID' mod='dpdgeopost'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=id_shipment&ShipmentOrderWay=desc">
										{if $order_by == 'id_shipment' && $order_way == 'desc'}
											<img border="0" src="../img/admin/arrow_down.png">
										{else}
											<img border="0" src="../img/admin/arrow_down.png">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=id_shipment&ShipmentOrderWay=asc">
										{if $order_by == 'id_shipment' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_up.png">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Date shipped' mod='dpdgeopost'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_shipped&ShipmentOrderWay=desc">
										{if $order_by == 'date_shipped' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_down.png">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_shipped&ShipmentOrderWay=asc">
										{if $order_by == 'date_shipped' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_up.png">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Order' mod='dpdgeopost'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=id_order&ShipmentOrderWay=desc">
										{if $order_by == 'id_order' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_down.png">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=id_order&ShipmentOrderWay=asc">
										{if $order_by == 'id_order' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_up.png">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Order Date' mod='dpdgeopost'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_add&ShipmentOrderWay=desc">
										{if $order_by == 'date_add' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_down.png">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_add&ShipmentOrderWay=asc">
										{if $order_by == 'date_add' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_up.png">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Carrier' mod='dpdgeopost'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=carrier&ShipmentOrderWay=desc">
										{if $order_by == 'carrier' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_down.png">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=carrier&ShipmentOrderWay=asc">
										{if $order_by == 'carrier' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_up.png">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Customer' mod='dpdgeopost'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=customer&ShipmentOrderWay=desc">
										{if $order_by == 'customer' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_down.png">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=customer&ShipmentOrderWay=asc">
										{if $order_by == 'customer' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_up.png">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Total Qty' mod='dpdgeopost'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=quantity&ShipmentOrderWay=desc">
										{if $order_by == 'quantity' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_down.png">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=quantity&ShipmentOrderWay=asc">
										{if $order_by == 'quantity' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_up.png">
										{/if}
									</a>
								</th>
								{*
								<th class="center">
									<span class="title_box">
										{l s='Manifest Closed' mod='dpdgeopost'}<br>&nbsp;
									</span>
									<br>
								</th> *}
								<th class="center">
									<span class="title_box">
										{l s='Label Printed' mod='dpdgeopost'}<br>&nbsp;
									</span>
									<br>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='DPD pickup' mod='dpdgeopost'}
									</span>
									<br>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_pickup&ShipmentOrderWay=desc">
										{if $order_by == 'date_pickup' && $order_way == 'desc'}
											<img border="0" src="../img/admin/down_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_down.png">
										{/if}
									</a>
									<a href="{$full_url|escape:'htmlall':'UTF-8'}&ShipmentOrderBy=date_pickup&ShipmentOrderWay=asc">
										{if $order_by == 'date_pickup' && $order_way == 'asc'}
											<img border="0" src="../img/admin/up_d.gif">
										{else}
											<img border="0" src="../img/admin/arrow_up.png">
										{/if}
									</a>
								</th>
								<th class="center">
									<span class="title_box">
										{l s='Actions' mod='dpdgeopost'}<br>&nbsp;
									</span>
									<br>
								</th>
							</tr>
							<tr style="height: 35px;" class="nodrag nodrop filter row_hover">
								<td class="center">
									--
								</td>
								<td class="center">
									<input placeholder="Shipment ID" type="text" style="width:95%" value="{if Context::getContext()->cookie->ShipmentsFilter_id_shipment}{Context::getContext()->cookie->ShipmentsFilter_id_shipment}{/if}" name="ShipmentsFilter_id_shipment" class="filter">
								</td>
								<td class="right">

										<input placeholder="{l s='From' mod='dpdgeopost'}" type="text" style="width:70px" value="" name="ShipmentsFilter_date_shipped[0]" id="ShipmentsFilter_date_shipped_0" class="filter datepicker">


										<input placeholder="{l s='To' mod='dpdgeopost'}" type="text" style="width:70px" value="" name="ShipmentsFilter_date_shipped[1]" id="ShipmentsFilter_date_shipped_1" class="filter datepicker">

								</td>
								<td class="center">
									<input placeholder="Order ID" type="text" style="width:95%" value="{if Context::getContext()->cookie->ShipmentsFilter_id_order}{Context::getContext()->cookie->ShipmentsFilter_id_order}{/if}" name="ShipmentsFilter_id_order" class="filter">
								</td>
								<td class="right">
									 <input placeholder="{l s='From' mod='dpdgeopost'}" type="text" style="width:70px" value="" name="ShipmentsFilter_date_add[0]" id="ShipmentsFilter_date_add_0" class="filter datepicker">

									<input placeholder="{l s='To' mod='dpdgeopost'}" type="text" style="width:70px" value="" name="ShipmentsFilter_date_add[1]" id="ShipmentsFilter_date_add_1" class="filter datepicker">
								</td>
								<td class="right">
									<input placeholder="Service" type="text" style="width:95%" value="{if Context::getContext()->cookie->__isset('ShipmentsFilter_carrier') && Context::getContext()->cookie->ShipmentsFilter_carrier}{Context::getContext()->cookie->ShipmentsFilter_carrier}{/if}" name="ShipmentsFilter_carrier" class="filter">
								</td>
								<td class="right">
									<input placeholder="Customer" type="text" style="width:95%" value="{if Context::getContext()->cookie->__isset('ShipmentsFilter_customer') && Context::getContext()->cookie->ShipmentsFilter_customer}{Context::getContext()->cookie->ShipmentsFilter_customer}{/if}" name="ShipmentsFilter_customer" class="filter">
								</td>
								<td class="right">
									<input type="text" style="width:95%" value="{if Context::getContext()->cookie->__isset('ShipmentsFilter_quantity') && Context::getContext()->cookie->ShipmentsFilter_quantity}{Context::getContext()->cookie->ShipmentsFilter_quantity}{/if}" name="ShipmentsFilter_quantity" class="filter">
								</td>
								<td class="center">
									<select name="ShipmentsFilter_label" onchange="$('input#submitFilterButtonShipments').click();">
										<option value="">--</option>
										<option {if Context::getContext()->cookie->__isset('ShipmentsFilter_label') && Context::getContext()->cookie->ShipmentsFilter_label == '1'}selected="selected" {/if}value="1">{l s='Yes' mod='dpdgeopost'}</option>
										<option {if Context::getContext()->cookie->__isset('ShipmentsFilter_label') && Context::getContext()->cookie->ShipmentsFilter_label == '0'}selected="selected" {/if}value="0">{l s='No' mod='dpdgeopost'}</option>
									</select>
								</td>
								<td class="right">
									<input placeholder="{l s='From' mod='dpdgeopost'}" type="text" style="width:70px" value="" name="ShipmentsFilter_date_pickup[0]" id="ShipmentsFilter_date_pickup_0" class="filter datepicker">

									<input placeholder="{l s='To' mod='dpdgeopost'}" type="text" style="width:70px" value="" name="ShipmentsFilter_date_pickup[1]" id="ShipmentsFilter_date_pickup_1" class="filter datepicker">
								</td>
								<td class="center">
									--
								</td>
							</tr>
						</thead>
						<tbody>
							{if isset($shipments) && $shipments}
								{section name=ii loop=$shipments}
									<tr class="row_hover" id="tr_{$smarty.section.ii.index|escape:'htmlall':'UTF-8' + 1}_{$shipments[ii].id_shipment|escape:'htmlall':'UTF-8'}_0">
										<td class="center">
											<input type="checkbox" class="noborder" value="{$shipments[ii].id_shipment|escape:'htmlall':'UTF-8'}" name="ShipmentsBox[]"{if isset($smarty.post.ShipmentsBox) && in_array($shipments[ii].id_shipment, $smarty.post.ShipmentsBox)} checked="checked"{/if}>
											<input type="hidden" name="pickup_available" value="{if $shipments[ii].manifest && $shipments[ii].manifest != '0000-00-00 00:00:00' || $shipments[ii].label}1{else}0{/if}" />
										</td>
										<td class="center">
											{if $shipments[ii].id_shipment}
												{$shipments[ii].id_shipment|escape:'htmlall':'UTF-8'}
											{else}
												--
											{/if}
										</td>
										<td class="center">
											{if $shipments[ii].date_shipped && $shipments[ii].date_shipped != '0000-00-00 00:00:00'}
												{$shipments[ii].date_shipped|escape:'htmlall':'UTF-8'}
											{else}
												--
											{/if}
										</td>
										<td class="center">
											{if $shipments[ii].id_order}
												{$shipments[ii].id_order|escape:'htmlall':'UTF-8'}
											{else}
												--
											{/if}
										</td>
										<td class="center">
											{if $shipments[ii].date_add && $shipments[ii].date_add != '0000-00-00 00:00:00'}
												{$shipments[ii].date_add|escape:'htmlall':'UTF-8'}
											{else}
												--
											{/if}
										</td>
										<td class="center">
											{if $shipments[ii].carrier}
												{$shipments[ii].carrier|escape:'htmlall':'UTF-8'}
											{else}
												--
											{/if}
										</td>
										<td class="center">
											{if $shipments[ii].customer}
												{$shipments[ii].customer|escape:'htmlall':'UTF-8'}
											{else}
												--
											{/if}
										</td>
										<td class="center">
											{if $shipments[ii].quantity}
												{$shipments[ii].quantity|escape:'htmlall':'UTF-8'}
											{else}
												--
											{/if}
										</td>
										{*
										<td class="center">
											{if $shipments[ii].manifest && $shipments[ii].manifest != '0000-00-00 00:00:00'}
												<img alt="{l s='Yes' mod='dpdgeopost'}" src="../img/admin/enabled.gif">
											{else}
												<img alt="{l s='No' mod='dpdgeopost'}" src="../img/admin/disabled.gif">
											{/if}
										</td> *}
										<td class="center">
											{if $shipments[ii].label}
												<img alt="{l s='Yes' mod='dpdgeopost'}" src="../img/admin/enabled.gif">
											{else}
												<img alt="{l s='No' mod='dpdgeopost'}" src="../img/admin/disabled.gif">
											{/if}
										</td>
										<td class="center">
											{if $shipments[ii].date_pickup && $shipments[ii].date_pickup != '0000-00-00 00:00:00'}
												{$shipments[ii].date_pickup|escape:'htmlall':'UTF-8'}
											{else}
												--
											{/if}
										</td>
										<td style="white-space: nowrap;" class="center">
											<a title="{l s='View' mod='dpdgeopost'}" href="{$order_link|escape:'htmlall':'UTF-8'}&id_order={$shipments[ii].id_order|escape:'htmlall':'UTF-8'}">{*TODO : #a link to fieldset in order page*}
												<img alt="{l s='View' mod='dpdgeopost'}" src="../img/admin/details.gif">
											</a>
											{if $shipments[ii].shipping_number && isset($shipments[ii].carrier_url)}
												&nbsp;
												<a target="_blank" title="{l s='Track Shipment' mod='dpdgeopost'}" href="{$shipments[ii].carrier_url|replace:'@':$shipments[ii].shipping_number}">
													<img alt="{l s='Track Shipment' mod='dpdgeopost'}" src="../img/admin/external_link.png">
												</a>
											{/if}
										</td>
									</tr>
								{/section}
							{else}
								<tr>
									<td colspan="11" class="center">
										{l s='No shipments' mod='dpdgeopost'}
									</td>
								</tr>
							{/if}
						</tbody>
					</table>
					<p>
						{* <input class="button" type="submit" onclick="return confirm('{l s='Close selected manifest(s)?' mod='dpdgeopost'}');" value="{l s='Close manifest(s)' mod='dpdgeopost'}" name="printManifest" /> *}
						<input class="btn btn-secondary" type="submit" value="{l s='Print label(s)' mod='dpdgeopost'}" name="printLabels" />
						<input class="btn btn-secondary" type="submit" value="{l s='Change order status to shipped' mod='dpdgeopost'}" name="changeOrderStatus" />
						<input class="btn btn-secondary" type="button" value="{l s='Request DPD Courier' mod='dpdgeopost'}" id="displayPickupDialog" />
					</p>
				</td>
			</tr>
		</tbody>
	</table>
</form>

<div id="dpdgeopost_pickup_dialog">
	<h2>{l s='Arrange DPD Geopost pickup' mod='dpdgeopost'}</h2>
	<div id="dpdgeopost_pickup_dialog_mssg"></div>

	<label>
		<sup>*</sup>{l s='Contact name' mod='dpdgeopost'}
	</label>
	<div class="margin-form">
		<input type="text" value="{$employee->firstname|escape:'htmlall':'UTF-8'}" name="dpdgeopost_pickup_data[contactName]" />
		<p>{l s='Sender name' mod='dpdgeopost'}</p>
	</div>
	<div class="clear"></div>

	<label>
		{l s='Contact e-mail' mod='dpdgeopost'}
	</label>
	<div class="margin-form">
		<input type="text" value="{$employee->email|escape:'htmlall':'UTF-8'}" name="dpdgeopost_pickup_data[contactEmail]" />
		<p>{l s='Sender email' mod='dpdgeopost'}</p>
	</div>
	<div class="clear"></div>

	<label>
		{l s='Contact phone no.' mod='dpdgeopost'}
	</label>
	<div class="margin-form">
		<input type="text" name="dpdgeopost_pickup_data[contactPhone]" />
		<p>{l s='Sender phone number' mod='dpdgeopost'}</p>
	</div>
	<div class="clear"></div>


	<label class="required"><sup>*</sup> {l s='Required fields' mod='dpdgeopost'}</label>
	<div class="margin-form">
		<input type="button" class="button" value="{l s='Submit' mod='dpdgeopost'}" id="submit_dpdgeopost_pickup_dialog" />
		<input type="button" class="button" value="{l s='Cancel' mod='dpdgeopost'}" id="close_dpdgeopost_pickup_dialog" />
	</div>
	<div class="clear"></div>

</div>
