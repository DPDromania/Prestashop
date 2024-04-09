<div class="csv_info_container">
	<span>
		{l s='In order to import shipping rules successfully please follow guidelines:' mod='dpdgeopost'}
	</span>
	<span>
		{l s='1. You can define rules selection according total order weight or total order price. It depends what settings are defined in carrier area:' mod='dpdgeopost'}
	</span>
	<span>
		<span>
			{l s='a. According to total price' mod='dpdgeopost'}
		</span>
		<span>
			{l s='b. According to total weight' mod='dpdgeopost'}
		</span>
	</span>
	<span>
		{l s='2. The first matched rule is applied for a carrier so it\'s important to define most specific rules at the top of the file and most general rules at the bottom of the file' mod='dpdgeopost'}
	</span>
	<span>
		{l s='3. "*" symbol means all countries, all regions in selected country or all ZIP codes in selected country - depends in which column its defined' mod='dpdgeopost'}
	</span>
	<span>
		{l s='4. Country is defined by ISO code or "*" (all countries) character' mod='dpdgeopost'}
	</span>
	<span>
		{l s='5. Region / State can be defined by ISO code or "*" (all Regions / States) character. If country "*" then Region / State also must be only "*" - its impossible to detect Region / State if country is not defined.' mod='dpdgeopost'}
	</span>
	<span>
		{l s='6. ZIP / Postal code can be defined according format defined in PrestaShop country settings page or "*" symbol (all ZIP / Postal codes). If country "*" then ZIP / Postal code also must be only "*" - its impossible to detect ZIP / Postal code if country is not defined.' mod='dpdgeopost'}
	</span>
	<span>
		{l s='7. Weight / Price (From) can be real number => 0.' mod='dpdgeopost'}
	</span>
	<span>
		{l s='8. Weight / Price (From) can be real number => 0 and equal or higher then Weight / Price (From). If Weight / Price (From) is equal toWeight / Price (To) the price is defined for exact weight / price value.' mod='dpdgeopost'}
	</span>
	<span>
		{l s='9. Method ID' mod='dpdgeopost'}
	</span>
	<span>
		<span>
			{l s='a. 1 - DPD Classic' mod='dpdgeopost'}
		</span>
		<span>
			{l s='d. 109 - DPD Classic Locco' mod='dpdgeopost'}
		</span>
		<span>
			{l s='e. 40033 - DPD INTERNATIONAL' mod='dpdgeopost'}
		</span>
		<span>
			{l s='f. 40107 - DPD REGIONAL EXPRESS' mod='dpdgeopost'}
		</span>
		<span>
			{l s='f. 40171 - DPD HUNGARY' mod='dpdgeopost'}
		</span>
		<span>
			{l s='h. "*" - rule can be applied to any carrier' mod='dpdgeopost'}
		</span>
	</span>
	<span>
		{l s='10. Shipping price - price applied if rule is matched for a carrier' mod='dpdgeopost'}
	</span>
	<span>
		{l s='11. Shipping percentage price - percentage price applied if rule is matched. One of - shipping price or shipping price percentage must be defined.' mod='dpdgeopost'}
	</span>
	<span>
		{l s='12. COD Surcharge - if carrier is compatible with COD then this surcharge will be applied.' mod='dpdgeopost'}
	</span>
	<span>
		{l s='13. COD Surcharge Percentage - if carrier is compatible with COD then this percentage surcharge will be applied. Only one of COD Surcharge or COD surcharge percentage can be defined.' mod='dpdgeopost'}
	</span>
	<span>
		{l s='14. COD Min. Surcharge - if this value is defined then COD surcharge must be not defined and COD Surcharge Percentage must be defined.' mod='dpdgeopost'}
	</span>
	<span>
		{l s='15. If rule should be applied to COD carrier then COD should be defined e.g. can also be 0 if needed, but value must be defined and in other case this rule will not be applied for COD carriers.' mod='dpdgeopost'}
	</span>
	<span>
		{l s='16. Currency - currency ISO code. It should exist in your PrestaShop installation. ' mod='dpdgeopost'}
	</span>
</div>
