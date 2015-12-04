{*
* Configure page template
*
*  @author    Web In Color <contact@prestashop.com>
*  @copyright 2012-2015 Web In Color
*  @license   http://www.apache.org/licenses/  Apache License
*  International Registered Trademark & Property of Web In Color
*}

<div class="panel">
	<div class="row moduleconfig-header">
		<div class="col-xs-5 text-right">
			<img src="{$module_dir|escape:'html':'UTF-8'}views/img/logo.png" />
		</div>
		<div class="col-xs-7 text-left">
			<h2>{l s='Lorem' mod='tenbucks'}</h2>
			<h4>{l s='Lorem ipsum dolor' mod='tenbucks'}</h4>
		</div>
	</div>

	<hr />

	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-12">
				<p>
					<h4>{l s='Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor' mod='tenbucks'}</h4>
					<ul class="ul-spaced">
						<li><strong>{l s='Lorem ipsum dolor sit amet' mod='tenbucks'}</strong></li>
						<li>{l s='Lorem ipsum dolor sit amet' mod='tenbucks'}</li>
						<li>{l s='Lorem ipsum dolor sit amet' mod='tenbucks'}</li>
						<li>{l s='Lorem ipsum dolor sit amet' mod='tenbucks'}</li>
						<li>{l s='Lorem ipsum dolor sit amet' mod='tenbucks'}</li>
					</ul>
				</p>

				<br />

				<p class="text-center">
					<strong>
						<a href="{$ctrl_link|escape:'html':'UTF-8'}" target="_blank" title="Lorem ipsum dolor">
							{l s='Lorem ipsum dolor' mod='tenbucks' }
						</a>
					</strong>
				</p>
			</div>
		</div>
	</div>
</div>
