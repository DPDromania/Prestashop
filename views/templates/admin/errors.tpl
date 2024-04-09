
{if count($errors)}
	<div class="error">
		{if count($errors) == 1}
			{$errors[0]|escape:'htmlall':'UTF-8'}
		{else}
			{l s='%d errors' mod='dpdgeopost' sprintf=$errors|count}
			<br/>
			<ol>
				{foreach $errors as $error}
					<li>{$error|escape:'htmlall':'UTF-8'}</li>
				{/foreach}
			</ol>
		{/if}
	</div>
{/if}