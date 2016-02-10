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
		<i class="icon-user"></i> {l s='Account created !' mod='tenbucks'}
	</h3>
	<div class="panel-body">
		<h4>{l s='You have now 6 hours to confirm your email address. Once it\'s done, you can use tenbucks.© the way you like:' mod='tenbucks'}</h4>
		<ul class="ul-spaced">
			<li>
				{l s='From your back-office:' mod='tenbucks'} (<em>{l s='We added a tab for you' mod='tenbucks'}</em>)
				<ul>
					<li><a href="{Link::getAdminLink('AdminTenbucksApps')}">{l s='Browse app list' mod='tenbucks'}</a></li>
					<li><a href="{Link::getAdminLink('AdminTenbucksAccount')}">{l s='See account details' mod='tenbucks'}</a></li>
				</ul>
			</li>
			<li><a href="{$standaloneUrl|escape:'htmlall':'UTF-8'}">{l s='In "stand-alone" mode (new window)' mod='tenbucks'}</a></li>
			<li>{l s='Via Firefox add-on' mod='tenbucks'} <em class="text-muted">{l s='Coming soon...' mod='tenbucks'}</em></li>
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
