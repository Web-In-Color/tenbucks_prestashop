<?php
/**
* Main module class
*
*  @author    Web In Color <contact@prestashop.com>
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
    protected $output;
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
        $this->output = '';

        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('tenbucks');
        $this->description = $this->l('In in condimentum velit; nec massa nunc.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update.
     */
    public function install()
    {
        Configuration::updateValue('TENBUCKS_WEBSERVICE_KEY_ID', false);
        Configuration::updateValue('TENBUCKS_DISPLAY_HELP', true);

        return parent::install() &&
            $this->installModuleTab() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        $keys = array(
            'TENBUCKS_WEBSERVICE_KEY_ID',
            'TENBUCKS_DISPLAY_HELP',
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
        /*
         * If values have been submitted in the form, process.
         */
        if (Tools::isSubmit('submitTenbucksModule')) {
            $this->postProcess();
        }
        if (Tools::isSubmit('generate_key')) {
            $this->generateKey();
        }
        if (!Configuration::get('PS_WEBSERVICE')) {
            $error = $this->l('Your Webservice is deactivated, please active it in order to use our services.');
            $this->output .= $this->displayError($error);
        }

        $query = new DbQuery();
        $query->select('COUNT(`id_webservice_account`)')
            ->from(WebserviceKey::$definition['table'])
            ->where('`active` =  1');
        $count = (int) Db::getInstance()->getValue($query);

        if ($count) {
            $this->output .= $this->renderForm();
        } else {
            $generate_uri = $this->getGenerateLink();
            $this->context->smarty->assign('generate_uri', $generate_uri);

            $this->output .= $this->context->smarty->fetch($this->getAdminTemplatePath('generate'));
        }

        $this->context->smarty->assign(array(
            'module_dir' => $this->_path,
            'ctrl_link' => $this->context->link->getAdminLink($this->controllerName, true),
        ));

        $this->output .= $this->context->smarty->fetch($this->getAdminTemplatePath('configure'));

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
        $query = new DbQuery();
        $query->select('`description`, `id_webservice_account`')
            ->from(WebserviceKey::$definition['table'])
            ->where('`active` =  1');
        $results = Db::getInstance()->executeS($query);

        $webservice_keys = array_map(function ($ws) {
            return array(
                'id' => $ws['id_webservice_account'],
                'name' => $ws['description'],
                    );
        }, $results);

        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('WebserviceKey:'),
                        'desc' => $this->l('Which key do use for webservice access.'),
                        'name' => 'TENBUCKS_WEBSERVICE_KEY_ID',
                        'required' => true,
                        'options' => array(
                            'query' => $webservice_keys,
                            'id' => 'id',
                            'name' => 'name',
                        ),
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
     */
    protected function getConfigFormValues()
    {
        return array(
            'TENBUCKS_WEBSERVICE_KEY_ID' => Configuration::get('TENBUCKS_WEBSERVICE_KEY_ID'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }

        $this->output .= $this->displayConfirmation($this->l('Settings updated.'));
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
     * Generate a webservice key with proper rights
     */
    protected function generateKey()
    {
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
        $conf = sprintf($format, $webservice_key->description);
        $this->output .= $this->displayConfirmation($conf);
        unset($crud_methods, $hash, $webservice_key, $resources, $format, $conf);
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
}
