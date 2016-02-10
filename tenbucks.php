<?php
/**
* Main module class
*
*  @author    Web In Color <contact@webincolor.fr>
*  @copyright 2012-2015 Web In Color
*  @license   http://www.apache.org/licenses/  Apache License
*  International Registered Trademark & Property of Web In Color
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__).'/classes/WIC_Server.php';

class Tenbucks extends Module
{
    /**
    * @var string $output Configuration page HTML
    */
    protected $output;

    /**
     * @var array $informations List of informations messages
     */
    protected $informations = array();

    /**
    * @var array $ctrls module controllers list
    */
    protected $ctrls = array(
        'AdminTenbucksAccount',
        'AdminTenbucksApps',
        'AdminTenbucksParent',
    );

    public function __construct()
    {
        $this->name = 'tenbucks';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Web In Color';
        $this->need_instance = 0;
        $this->module_key = 'f379014b011869cc93e15c074b374294';

        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('tenbucks');
        $this->description = $this->l('Use tenbucks with your PrestaShop website.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update.
     */
    public function install()
    {
        Configuration::updateValue('TENBUCKS_WEBSERVICE_KEY_ID', 0);
        Configuration::updateValue('TENBUCKS_TAB_ID', 0);
        Configuration::updateValue('TENBUCKS_ACCOUNT_CREATED', false);
        return parent::install() &&
            $this->registerHook('actionProductSave') &&
            $this->registerHook('actionOrderStatusUpdate') &&
            $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        $keys = array(
            'TENBUCKS_WEBSERVICE_KEY_ID',
            'TENBUCKS_ACCOUNT_CREATED',
            'TENBUCKS_TAB_ID'
        );

        foreach ($keys as $key) {
            Configuration::deleteByName($key);
        }

        return parent::uninstall() &&
            $this->uninstallModuleTab();
    }

    /**
     * Generate a Tenbucks tab on admin
     *
     * @return boolean installation success
     */
    protected function installModuleTab()
    {
        if ((int)Configuration::get('TENBUCKS_TAB_ID')) {
            return true;
        }

        if (version_compare(_PS_VERSION_, '1.6.0.0', '<')) {
            // v1.5 icon in menu
            Tools::copy(_PS_MODULE_DIR_.$this->name.'/views/img/logo.gif',
             _PS_MODULE_DIR_.$this->name.'/AdminTenbucksParent.gif');
        }

        $languages = Language::getLanguages();
        $tab_names = array();
        $valid = true;

        foreach ($languages as $lang) {
            $tab_names[$lang['id_lang']] = $this->displayName;
        }
        $controllers = array(
            'Apps' => $this->l('My apps'),
            'Account' => $this->l('My account'),
        );

        $parent_tab = new Tab();
        $parent_tab->name = $tab_names;
        $parent_tab->class_name = 'AdminTenbucksParent';
        $parent_tab->module = $this->name;
        $parent_tab->id_parent = 0;

        if ($parent_tab->save()) {
            Configuration::updateValue('TENBUCKS_TAB_ID', $parent_tab->id);
            foreach ($controllers as $class_name => $name) {
                $tab_names = array();
                foreach ($languages as $lang) {
                    $tab_names[$lang['id_lang']] = $name;
                }
                $sub_tab = new Tab();
                $sub_tab->name = $tab_names;
                $sub_tab->class_name = 'AdminTenbucks'.$class_name;
                $sub_tab->module = $this->name;
                $sub_tab->id_parent = $parent_tab->id;
                if (!$sub_tab->save()) {
                    $valid = false;
                }
            }

            return $valid;
        } else {
            return false;
        }
    }

    /**
     * Uninstall tenbucks tab
     *
     * @return boolean
     */
    protected function uninstallModuleTab()
    {
        $valid = true;
        foreach ($this->ctrls as $ctrl) {
            $id_tab = (int) Tab::getIdFromClassName($ctrl);
            if (!$id_tab) {
                return true;
            }
            $tab = new Tab($id_tab);
            if (!(bool) $tab->delete()) {
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * Load the configuration form.
     *
     * @return string HTML content
     */
    public function getContent()
    {
        $this->output = '';
        /*
         * If values have been submitted in the form, process.
         */
        if (Tools::isSubmit('submitTenbucksModule')) {
            $this->output .= $this->postProcess();
        }

        $this->context->smarty->assign(array(
            'module_dir' => $this->_path,
            'ctrl_link' => $this->context->link->getAdminLink($this->ctrls[0], true),
        ));

        $header_ver = version_compare(_PS_VERSION_, '1.6.0.0', '<') ? '5' : '6';
        $header_tpl = $this->getAdminTemplatePath('header_1.'.$header_ver);

        $this->output .= $this->context->smarty->fetch($header_tpl);
        if ((bool)Configuration::get('TENBUCKS_ACCOUNT_CREATED')) {
            $standalone_url = WIC_Server::getUrl('dispatch', $this->getIframeQuery(), true);
            $this->context->smarty->assign('standaloneUrl', $standalone_url);
            $this->output .= $this->context->smarty->fetch(
                $this->getAdminTemplatePath('configure')
            );
        } else {
            $this->informations[] = $this->l('You have to create an account in order to use tenbucks.');
            $this->output .= $this->renderForm();
        }

        // Display informations
        foreach ($this->informations as $msg) {
            $this->adminDisplayInformation($msg);
        }

        return $this->output;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitTenbucksModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Your email:'),
                        'desc' => $this->l('Your password will be send to this email, so it must be valid. If your already have an account, this shop will be added to your existing sites.'),
                        'name' => 'email',
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Confirmation:'),
                        'empty_message' => $this->l('Please confirm your email.'),
                        'name' => 'email_confirmation',
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Your sponsor email:'),
                        'desc' => $this->l('Your sponsor email. Leave blank for none.'),
                        'name' => 'sponsor',
                        'required' => false,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     *
     * @return array
     */
    protected function getConfigFormValues()
    {
        $email = Configuration::get('PS_SHOP_EMAIL');
        return array(
            'email' => Tools::getValue('email', $email),
            'email_confirmation' => Tools::getValue('email_confirmation', $email),
            'sponsor' => Tools::getValue('sponsor', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $email = Tools::getValue('email');
        if (!Validate::isEmail($email)) {
            $msg = $this->l('Invalid email');
            return $this->displayError($msg);
        } elseif ($email !== Tools::getValue('email_confirmation') ) {
            $msg = $this->l('Email and confirmation are different.');
            return $this->displayError($msg);
        }

        if (!Configuration::get('PS_WEBSERVICE')) {
            Configuration::updateValue('PS_WEBSERVICE', 1);
            $this->informations[] = $this->l('Your Webservice has been activated for tenbucks use.');
        }

        if ($this->isCGI() && !Configuration::get('PS_WEBSERVICE_CGI_HOST')) {
            Configuration::updateValue('PS_WEBSERVICE_CGI_HOST', 1);
            $this->informations[] = $this->l('Your server is running as CGI, we actiated CGI mode for your Webservice.');
        }

        // Include vendor library
        include dirname(__FILE__).'/vendor/tenbucks_registration_client/lib/TenbucksRegistrationClient.php';
        $ws_key = $this->getWebServiceKey();
        $lang_infos = explode('-', $this->context->language->language_code);

        $opts = array(
            'email' => $email,
            'company' => $this->context->shop->name,
            'platform' => 'PrestaShop',
            'locale' => $lang_infos[0],
            'country' => Tools::strtoupper($lang_infos[1]),
            'url'         => $this->getShopUri(),
            'credentials' => array(
                'api_key'    => $ws_key->key, // key
            )
        );

        // Add sponsor if any
        $sponsor = Tools::getValue('sponsor');
        if (Validate::isEmail($sponsor)) {
            $opts['sponsor'] = Tools::strtolower($sponsor);
        }

        try {
            $client = new TenbucksRegistrationClient();
            $query = $client->send($opts);
            $success = array_key_exists('success', $query) && (bool)$query['success'];
            if ($success) {
                // success
                Configuration::updateValue('TENBUCKS_ACCOUNT_CREATED', 1);
                $this->installModuleTab();
                $new_user = (bool)$query['new_account'];
                if ($new_user) {
                    $msg =  $this->l('Account created. Please check your email to confirm your address');
                } else {
                    $msg = $this->l('Shop added to your account.');
                }
                $this->context->smarty->assign('newUser', $new_user);
                return $this->displayConfirmation($msg);
            } else {
                return $this->displayError($this->l('Creation failed, please try again.'));
            }
        } catch (Exception $e) {
            return $this->displayError($e->getMessage());
        }
    }

    /**
     * Get a template path
     *
     * @param string $template template name, without extension
     * @return string complete path
     */
    public function getAdminTemplatePath($template)
    {
        return sprintf($this->local_path.'views/templates/admin/%s.tpl', $template);
    }

    /**
     * Get a webservice key generation link
     */
    public function getGenerateLink()
    {
        $query_string = http_build_query(array(
            'configure' => $this->name,
            'tab_module' => $this->tab,
            'module_name' => $this->name,
            'generate_key' => true,
        ));

        return $this->context->link->getAdminLink('AdminModules', true).'&'.$query_string;
    }

    /**
    * Check if server is running as (Fast)CGI
    */
    public function isCGI()
    {
        return (bool)preg_match('/f?cgi/', php_sapi_name());
    }

    /**
     * Generate a webservice key with proper rights
     */
    protected function getWebServiceKey()
    {
        $id_key = (int)Configuration::get('TENBUCKS_WEBSERVICE_KEY_ID');

        if ($id_key) {
            $webservice_key = new WebserviceKey($id_key);

            if (Validate::isLoadedObject($webservice_key)) {
                if (!$webservice_key->active) {
                    // Regenerate key
                    $hash = Tools::encrypt(time());
                    $webservice_key->key = Tools::strtoupper($hash);
                    $webservice_key->active = true;
                    $webservice_key->save();
                }
                return $webservice_key;
            }
        }

        $crud_methods = array('GET', 'PUT', 'POST', 'DELETE');
        $hash = Tools::encrypt(time());
        $webservice_key = new WebserviceKey();
        $webservice_key->key = Tools::strtoupper($hash);
        $webservice_key->active = true;
        $webservice_key->description = $this->displayName;
        $webservice_key->save();
        $resources = array();
        foreach (WebserviceRequest::getResources() as $resource_name => $data) {
            $resources[$resource_name] = array();
            foreach ($crud_methods as $method) {
                if (array_key_exists('forbidden_method', $data)
                && in_array($method, $data['forbidden_method'])) {
                    continue;
                }
                $resources[$resource_name][$method] = true;
            }
        };
        WebserviceKey::setPermissionForAccount($webservice_key->id, $resources);
        Configuration::updateValue('TENBUCKS_WEBSERVICE_KEY_ID', $webservice_key->id);
        $format = $this->l('New Webservice key created: %s.');
        $this->informations[] = sprintf($format, $webservice_key->description);
        return $webservice_key;
    }

    public function hookActionOrderStatusUpdate($args)
    {
        /* Place your code here. */
        // d($args);
    }

    public function hookActionProductSave($args)
    {
        $data = array(
            'shop' => $this->getShopUri(),
            'external_id' => (int)$args['id_product']
        );
        WIC_Server::post('webhooks/products', $data);
    }

    public function hookActionValidateOrder($args)
    {
        /* Place your code here. */
        // d($args);
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if ($this->active && version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }

        if (Tools::getValue('module_name') == $this->name ||
            in_array(Tools::getValue('controller'), $this->ctrls)) {
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/controller.css');
            if (version_compare(_PS_VERSION_, '1.6.0.0', '<')) {
                $this->context->controller->addCSS($this->_path.'views/css/backward.css');
            }
        }
    }

    public function getShopUri()
    {
        $base_url = Tools::getShopDomainSsl(1);
        $shop_uri = $this->context->shop->getBaseUri();
        return $base_url.$shop_uri;
    }

    public function getIframeQuery()
    {
        return array(
            'url' => $this->getShopUri(),
            'timestamp' => (int) microtime(true),
            'platform' => 'PrestaShop',
            'ps_version' => _PS_VERSION_,
            'module_version' => $this->module->version,
        );
    }
}
