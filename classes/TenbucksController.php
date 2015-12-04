<?php
/**
* Controller used to display iframe.
*
*  @author    Web In Color <contact@prestashop.com>
*  @copyright 2012-2015 Web In Color
*  @license   http://www.apache.org/licenses/  Apache License
*  International Registered Trademark & Property of Web In Color
*/

 /**
  * Tenbucks controllers abstract class.
  */
 abstract class TenbucksController extends ModuleAdminController
 {
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->multishop_context = Shop::CONTEXT_ALL;

        parent::__construct();
        $this->override_folder = 'tenbucks/';
    }

    protected function getServerUri($redirect = null, $standalone = false)
    {
        $query = $this->getQuery();
        if ($this->redirect_to) {
            $query['redirect'] = $this->redirect_to;
        }

        return WIC_Server::getUrl('dispatch', $query, $standalone);
    }

    public function renderModulesList() {
        $this->page_header_toolbar_btn['standalone'] = array(
            'href' => $this->getServerUri(null, true),
            'desc' => $this->l('Standalone mode', null, null, false),
            'icon' => 'process-icon-preview',
        );

        $show_help = (bool) Configuration::get('TENBUCKS_DISPLAY_HELP');

        if ($show_help) {
            $id_key = (int)Configuration::get('TENBUCKS_WEBSERVICE_KEY_ID');
            $wsk = new WebserviceKey($id_key);
            if (Validate::isLoadedObject($wsk)) {
                $key = $wsk->key;
            } else {
                $key = false;
                $generate_uri = $this->module->getGenerateLink();
            }
        }

        $this->context->smarty->assign(array(
            'show_help' => $show_help,
            'key' => $key,
            'generate_uri' => $key ? null : $generate_uri,
            'iframe_uri' => $this->getServerUri()
        ));
    }

    public function ajaxProcessHideHelp() {
        Configuration::updateValue('TENBUCKS_DISPLAY_HELP', false);
        $conf = $this->l('Settings updated.');
        return $this->jsonConfirmation($conf);
    }

    protected function getQuery()
    {
        $base_url = Tools::getShopDomainSsl(1);
        $shop_uri = $this->context->shop->getBaseUri();
        $lang_infos = explode('-', $this->context->language->language_code);

        return array(
            'url' => $base_url.$shop_uri,
            'timestamp' => (int) microtime(true),
            'platform' => 'PrestaShop',
            'ps_version' => _PS_VERSION_,
            'module_version' => $this->module->version,
            'email' => $this->context->employee->email,
            'username' => $this->context->shop->name,
            'locale' => $lang_infos[0],
            'country' => Tools::strtoupper($lang_infos[1]),
        );
    }
 }
