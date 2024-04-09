{if count($warnings)}
	<div class="warning warn">
		{if count($warnings) == 1}
			{$warnings[0]|escape:'htmlall':'UTF-8'}
		{else}
			{l s='%d warnings' mod='dpdgeopost' sprintf=$warnings|count}
			<br/>
			<ol>
				{foreach $warnings as $warning}
					<li>{$warning|escape:'htmlall':'UTF-8'}</li>
				{/foreach}
			</ol>
		{/if}
	</div>
{/if}