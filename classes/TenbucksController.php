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

    protected function getServerUri($standalone = false)
    {
        $query = $this->getQuery();
        if ($this->redirect_to) {
            $query['redirect'] = $this->redirect_to;
        }

        return WIC_Server::getUrl('dispatch', $query, $standalone);
    }

    public function renderModulesList()
    {
        $this->page_header_toolbar_btn['standalone'] = array(
            'href' => $this->getServerUri(true),
            'desc' => $this->l('Standalone mode', null, null, false),
            'icon' => 'process-icon-preview',
        );

        $show_help = (bool) Configuration::get('TENBUCKS_DISPLAY_HELP');
        $key = false;
        $generate_uri = null;

        if ($show_help) {
            $id_key = (int) Configuration::get('TENBUCKS_WEBSERVICE_KEY_ID');
            $wsk = new WebserviceKey($id_key);
            if (Validate::isLoadedObject($wsk)) {
                $key = $wsk->key;
            } else {
                $generate_uri = $this->module->getGenerateLink();
            }
        }

        $this->context->smarty->assign(array(
            'show_help' => $show_help,
            'key' => $key,
            'generate_uri' => $generate_uri,
            'iframe_uri' => $this->getServerUri(),
        ));
    }

    public function ajaxProcessHideHelp()
    {
        Configuration::updateValue('TENBUCKS_DISPLAY_HELP', false);
        $conf = $this->l('Settings updated.');

        return $this->jsonConfirmation($conf);
    }
}
