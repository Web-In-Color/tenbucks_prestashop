{*
* Configure page template
*
*  @author    Web In Color <contact@prestashop.com>
*  @copyright 2012-2015 Web In Color
*  @license   http://www.apache.org/licenses/  Apache License
*  International Registered Trademark & Property of Web In Color
*}

<div class="panel">
	<h3 class="panel-heading">
		<i class="icon-user"></i>
		{if isset($newUser)}
			{if $newUser}
				{l s='Account created!' mod='tenbucks'}
			{else}
				{l s='Shop added to your account!' mod='tenbucks'}
			{/if}
		{else}
			{l s='Use tenbucks.©' mod='tenbucks'}
		{/if}
	</h3>
	<div class="panel-body">
		<h4>
			{if isset($newUser)}
				{if $newUser}
					{l s='You have now 6 hours to confirm your email address. Once it\'s done, you can use tenbucks.© the way you like:' mod='tenbucks'}
				{else}
					{l s='This shop has been added to your account, you can now use tenbucks.© the way you like:' mod='tenbucks'}
				{/if}
			{else}
				{l s='Use tenbucks.© the way you like:' mod='tenbucks'}
			{/if}
		</h4>
		<ul class="ul-spaced">
			<li>
				{l s='From your back-office:' mod='tenbucks'} (<em>{l s='We added a tab for you' mod='tenbucks'}</em>)
				<ul>
					<li><a href="{Link::getAdminLink('AdminTenbucksApps')}">{l s='Browse app list' mod='tenbucks'}</a></li>
					<li><a href="{Link::getAdminLink('AdminTenbucksAccount')}">{l s='See account details' mod='tenbucks'}</a></li>
				</ul>
			</li>
			<li><a href="{$standaloneUrl|escape:'htmlall':'UTF-8'}">{l s='In "stand-alone" mode (new window)' mod='tenbucks'}</a></li>
		</ul>
		<p class="text-center">
			<strong>
			  <a href="https://www.tenbucks.io" target="_blank" title="tenbucks." class="btn btn-primary">
				  <i class="icon-globe"></i> {l s='Visit tenbucks.© website' mod='tenbucks' }
			  </a>
		  </strong>
		</p>
	</div>
</div>
