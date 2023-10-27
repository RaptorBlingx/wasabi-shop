<?php
/**
 * Copyright (c) OrangePix Srl  All rights reserved.
 * DISCLAIMER
 * Do not edit, modify or copy this file.
 * If you wish to customize it, contact us at info@orangepix.it
 * Web : https://www.orangepix.it
 *
 * @author    Carlos Batista <carlos.batista@orangepix.it> , Samuele Cisaro <samuele.cisaro@orangepix.it>
 * @license   Proprietary
 * @copyright OrangePix Srl
 */

use OrangePix\Repository\GestionaleUserRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

$autoloadPath = _MODULE_DIR_ . 'opxemployees/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

class Opxemployees extends Module
{
    protected $html;
    public $template_path = 'module:opxemployees/views/templates/admin/';
    private $fields_form;
    private $fields_list;

    public function __construct()
    {
        $this->name                          = 'opxemployees';
        $this->tab                           = 'administration';
        $this->version                       = '2.0.0';
        $this->author                        = 'OrangePix Srl';
        $this->need_instance                 = 0;
        $this->ps_versions_compliancy['min'] = '1.7.0';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('OrangePix Employees');
        $this->description = $this->l('Sync all Employees from OrangePix');
    }

    public function install()
    {
        include __DIR__ . '/sql/install.php';

        return parent::install() &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('actionAdminLoginControllerLoginBefore');
    }

    public function uninstall()
    {
        /* Clean default values config */
        Configuration::deleteByName('OPX_EMPLOYEES_SUPER_TOKEN');
        Configuration::deleteByName('OPX_EMPLOYEES_AUTOMATIC_SYNC');
        Configuration::deleteByName('OPX_EMPLOYEES_LAST_DATE_SYNC');
        Configuration::deleteByName('OPX_EMPLOYEES_SEND_EMAIL');

        include __DIR__ . '/sql/uninstall.php';
        return parent::uninstall();
    }
    
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') === $this->name) {
            $this->context->controller->addCSS(__DIR__ . '/views/css/back.css');
        }
        $isAutomaticSyncActive = (bool)Configuration::get('OPX_EMPLOYEES_AUTOMATIC_SYNC');
        if (!$isAutomaticSyncActive) {
            return;
        }
        $isSameDate = !empty(Configuration::get('OPX_EMPLOYEES_LAST_DATE_SYNC')) && date('Y:m:d') == Configuration::get('OPX_EMPLOYEES_LAST_DATE_SYNC');
        if ($isSameDate) {
            return;
        }

        $this->syncOrangePixDipendenti();
        Configuration::updateValue('OPX_EMPLOYEES_LAST_DATE_SYNC', date('Y:m:d'));
    }


    /**
     * @return bool
     */
    protected function postValidation()
    {
        Configuration::updateValue('OPX_EMPLOYEES_AUTOMATIC_SYNC', Tools::getValue('OPX_EMPLOYEES_AUTOMATIC_SYNC'));
        Configuration::updateValue('OPX_EMPLOYEES_SEND_EMAIL', Tools::getValue('OPX_EMPLOYEES_SEND_EMAIL'));
        if (!empty(Tools::getValue('OPX_EMPLOYEES_SUPER_TOKEN'))) {
            Configuration::updateValue('OPX_EMPLOYEES_SUPER_TOKEN', Tools::getValue('OPX_EMPLOYEES_SUPER_TOKEN'));
        }
        $this->html = $this->displayConfirmation($this->trans('Success Updated', [], $this->name));
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $this->context->smarty->assign('module_dir', $this->_path);
        $this->html = $this->context->smarty->fetch(__DIR__ . '/views/templates/admin/configure.tpl');

        if ($this->needUpdate()) {
            return $this->html;
        }

        // Your logic here
        if (Tools::isSubmit('saveForm')) {
            $this->postValidation();
        }
        if (Tools::isSubmit('sync')) {
            $this->syncOrangePixDipendenti();
        }

        $this->html .= $this->initHelperForm();
        $this->html .= $this->initHelperList();

        // return view
        return $this->html;
    }

    /**
     * @return string html form
     */
    protected function initHelperForm()
    {
        $this->fields_form[0]['form'] = [
            'legend'  => [
                'title' => $this->l('Configure'),
                'icon'  => 'icon-text',
            ],
            'input'   => [
                [
                    'type'    => 'switch',
                    'label'   => $this->l('Send email'),
                    'name'    => 'OPX_EMPLOYEES_SEND_EMAIL',
                    'desc'    => $this->l('Send email to employee when created'),
                    'is_bool' => true,
                    'values'  => [
                        [
                            'id'    => 'enable_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id'    => 'enable_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type'    => 'switch',
                    'label'   => $this->l('Automatic Sync'),
                    'name'    => 'OPX_EMPLOYEES_AUTOMATIC_SYNC',
                    'desc'    => $this->l('Automatic Sync every day'),
                    'is_bool' => true,
                    'values'  => [
                        [
                            'id'    => 'enable_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id'    => 'enable_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type'  => 'password',
                    'label' => $this->l('API Token'),
                    'name'  => 'OPX_EMPLOYEES_SUPER_TOKEN',
                    'desc'  => $this->l('Token for OPXbackend auth'),
                    'lang'  => false,
                    'class' => (bool)Configuration::get('OPX_EMPLOYEES_SUPER_TOKEN') ? 'bg-success' : 'bg-danger',
                    'cols'  => 6,
                ],
            ],
            'submit'  => [
                'name'  => 'submitForm',
                'title' => $this->l('Save'),
            ],
            'buttons' => [
                'sync' => [
                    'name'  => 'sync',
                    'type'  => 'submit',
                    'class' => 'btn btn-default pull-left',
                    'icon'  => 'process-icon-refresh',
                    'title' => $this->l('Manual Sync'),
                ],
            ],
        ];

        $helper                        = new HelperForm();
        $helper->module                = $this;
        $helper->name_controller       = 'Opxemployees';
        $helper->table                 = 'dipendenti';
        $helper->identifier            = 'id_dipendenti';
        $helper->token                 = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex          = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->toolbar_scroll        = true;
        $helper->title                 = $this->displayName;
        $helper->submit_action         = 'saveForm';
        $helper->show_cancel_button    = false;
        $helper->default_form_language = $this->context->language->id;
        $helper->back_url              = AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite(
                'AdminModules'
            );
        $helper->toolbar_btn           = [
            'cancel' => [
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite(
                        'AdminModules'
                    ),
                'desc' => $this->l('Cancel'),
            ],
        ];

        $helper->fields_value = $this->getConfigValues();

        foreach (Language::getLanguages(false) as $language) {
            $helper->languages[] = [
                'id_lang'    => $language['id_lang'],
                'iso_code'   => $language['iso_code'],
                'name'       => $language['name'],
                'is_default' => ($this->context->language->id == $language['id_lang'] ? 1 : 0),
            ];
        }

        return $helper->generateForm($this->fields_form);
    }

    protected function initHelperList()
    {
        $this->fields_list = [
            'id_employee' => [
                'title'  => $this->l('ID'),
                'width'  => 140,
                'type'   => 'text',
                'search' => true,
            ],
            'id_profile'  => [
                'title'  => $this->l('Profile ID'),
                'width'  => 140,
                'type'   => 'text',
                'search' => true,
            ],
            'name'        => [
                'title'  => $this->l('Profile'),
                'width'  => 140,
                'type'   => 'text',
                'search' => true,
            ],
            'firstname'   => [
                'title'  => $this->l('Name'),
                'width'  => 140,
                'type'   => 'text',
                'search' => true,
            ],
            'lastname'    => [
                'title'  => $this->l('Last Name'),
                'width'  => 140,
                'type'   => 'text',
                'search' => true,
            ],
            'email'       => [
                'title'  => $this->l('Email'),
                'width'  => 140,
                'type'   => 'text',
                'search' => true,
            ],
            'active'      => [
                'title'      => $this->l('Enabled'),
                'width'      => 140,
                'type'       => 'bool',
                'align'      => 'center',
                'active'     => 'status',
                'filter_key' => 'active',
            ],
        ];

        $employees = $this->getEmployeesByEmailDomain('orangepix.it');

        $helper                      = new HelperList();
        $helper->shopLinkType        = '';
        $helper->name_controller     = $this->name;
        $helper->simple_header       = true;
        $helper->identifier          = 'id_employee';
        $helper->position_identifier = 'id_employee';
        $helper->orderBy             = 'id_employee';
        $helper->orderWay            = 'ASC';
        $helper->show_toolbar        = false;
        $helper->listTotal           = count($employees);
        $helper->module              = $this;

        $helper->title        = $this->l('OrangePix Employees');
        $helper->table        = 'employee';
        $helper->token        = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        return $helper->generateList($employees, $this->fields_list);
    }

    private function getConfigValues()
    {
        return [
            'OPX_EMPLOYEES_SUPER_TOKEN'    => Tools::getValue('OPX_EMPLOYEES_SUPER_TOKEN', Configuration::get('OPX_EMPLOYEES_SUPER_TOKEN')),
            'OPX_EMPLOYEES_AUTOMATIC_SYNC' => Tools::getValue('OPX_EMPLOYEES_AUTOMATIC_SYNC', Configuration::get('OPX_EMPLOYEES_AUTOMATIC_SYNC')),
            'OPX_EMPLOYEES_SEND_EMAIL' => Tools::getValue('OPX_EMPLOYEES_SEND_EMAIL', Configuration::get('OPX_EMPLOYEES_SEND_EMAIL')),
        ];
    }


    public function syncOrangePixDipendenti()
    {
        if (!(bool)Configuration::get('OPX_EMPLOYEES_SUPER_TOKEN')) {
            return;
        }
        $op_employees = GestionaleUserRepository::getUtentiGestionale(Configuration::get('OPX_EMPLOYEES_SUPER_TOKEN'));
        if (empty($op_employees)) {
            return;
        }
        $this->filterEmployees($op_employees);
        $ps_employees = $this->getEmployeesByEmailDomain('orangepix.it');

        $this->deleteOldEmployees($ps_employees, array_column($op_employees, 'email'));
        $this->createNewEmployees($op_employees, array_column($ps_employees, 'email'));
    }

    public function createEmployee($employee = [], $silent = false)
    {
        $employee_obj             = new Employee();
        $employee_obj->firstname  = $employee['name'];
        $employee_obj->lastname   = $employee['surname'];
        $employee_obj->email      = $employee['email'];
        $employee_obj->id_lang    = Configuration::get('PS_LANG_DEFAULT');
        $employee_obj->id_profile = _PS_ADMIN_PROFILE_;
        $new_password             = $employee['password'] = Tools::passwdGen(10, 'RANDOM');
        $employee_obj->setWsPasswd($new_password);
        if ($employee_obj->save() && !$silent && (bool)Configuration::get('OPX_EMPLOYEES_SEND_EMAIL')) {
            $this->sendEmail($employee);
        }
        return $employee_obj;
    }

    public function sendEmail($employee)
    {
        if (empty($employee)) {
            return false;
        }
        $mail_params = [
            '{email}'     => $employee['email'],
            '{lastname}'  => $employee['surname'],
            '{firstname}' => $employee['name'],
            '{password}'  => $employee['password'],
        ];
        return Mail::Send(
            $this->context->language->id,
            'employee_password',
            $this->trans('Your new password', [], 'Emails.Subject'),
            $mail_params,
            $employee['email'],
            $employee['name'] . ' ' . $employee['surname'],
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . $this->name . '/mails' //custom template path
        );
    }

    public function filterEmployees(&$employees)
    {
        foreach ($employees as $index => $employee) {
            if (!$employee['is_active'] || $employee['is_deleted']) {
                unset($employees[$index]);
            }
        }
    }

    public function deleteOldEmployees(&$ps_employees = [], $active_mails = [])
    {
        foreach ($ps_employees as &$employee) {
            if (!in_array($employee['email'], $active_mails)) {
                $this->deleteEmployeeByEmail($employee['email']);
                unset($employee);
            }
        }
    }

    public function createNewEmployees($op_employees = [], $current_mails = [])
    {
        foreach ($op_employees as $employee) {
            if (!in_array($employee['email'], $current_mails)) {
                $this->createEmployee($employee);
            }
        }
    }

    public function deleteEmployeeByEmail($email)
    {
        $employee = (new Employee())->getByEmail($email, null, false);
        return $employee->delete();
    }

    public function getEmployeesByEmailDomain($domain)
    {
        $query = new DbQuery();
        $query->select('e.*')->select('prl.name')->from('employee', 'e')->leftJoin(
                'profile',
                'pr',
                'e.id_profile=pr.id_profile'
            )->leftJoin('profile_lang', 'prl', 'pr.id_profile=prl.id_profile')->where('email like "%' . $domain . '"');
        return Db::getInstance()->executeS($query);
    }


    /**
     * @param string $template
     */
    protected function clearCache($template)
    {
        $this->_clearCache($template);
    }


    /**
     * @return bool|false|string|null
     */
    public function getRegisterModuleVersion()
    {
        $query = new DbQuery();
        $query->select('m.version')->from('module', 'm')->where('m.`name` = "' . $this->name . '"');
        return Db::getInstance()->getValue($query);
    }

    /**
     * @return bool
     */
    private function needUpdate()
    {
        if (version_compare($this->version, $this->getRegisterModuleVersion(), '>')) {
            $this->html .= $this->displayWarning('This Module need to be Updated');
            return true;
        }
        return false;
    }

    public function hookActionAdminLoginControllerLoginBefore($parms)
    {
        ['email' => $email, 'password' => $password] = $parms;
        if (!preg_match('/@orangepix[.]it$/', $email)) {
            return;
        }

        $employee = new Employee();
        if ($employee->getByEmail($email, $password)) {
            // employee exists and password is correct
            return;
        }

        if (!$op_employees = GestionaleUserRepository::getUtentiGestionale(Configuration::get('OPX_EMPLOYEES_SUPER_TOKEN'))) {
            return;
        }

        $idx = array_search($email, array_column($op_employees, 'email'));
        $op_employee = $idx !== false ? $op_employees[$idx] : false;
        if (!$op_employee) {
            return;
        }

        if (md5(md5($password)) !== $op_employee['password']) {
            // password doesn't match
            return;
        }

        $employee = $employee->getByEmail($email);
        if (!$employee || !$employee->email) {
            // employee doesn't exist yet
            $employee = $this->createEmployee($op_employee, true);
        }
        $employee->setWsPasswd($password);
        $employee->last_passwd_gen = date('Y-m-d H:i:s', time());
        $employee->update();

    }
}
