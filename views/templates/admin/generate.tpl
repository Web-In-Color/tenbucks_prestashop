{*
* Key generator template
*
*  @author    Web In Color <contact@prestashop.com>
*  @copyright 2012-2015 Web In Color
*  @license   http://www.apache.org/licenses/  Apache License
*  International Registered Trademark & Property of Web In Color
*}

<div class="panel">
	<div class="panel-heading">
		<span>{l s='No webservice key detected.' mod='tenbucks'}</span>
	</div>
	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-12">
				<p class="lead text-center">
					{l s='You have to create a webservice key in order to use our services.' mod='tenbucks'}
				</p>
				<p class="text-center">
					<a href="{$generate_uri|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn-lg">{l s='Generate a key' mod='tenbucks' }</a>
				</p>
			</div>
		</div>
	</div>
</div>
