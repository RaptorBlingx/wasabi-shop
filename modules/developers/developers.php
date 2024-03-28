<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Developers extends Module
{
    const STATUS_APPROVED = 'approved';
    const STATUS_PENDING = 'pending';
    const STATUS_REFUSED = 'refused';

    const AVAILABLE_STATUSES = [
        self::STATUS_APPROVED, 
        self::STATUS_REFUSED, 
        self::STATUS_REFUSED
    ];
    
    public function __construct()
    {
        $this->name = 'developers';
        $this->tab = 'front_office_features';
        $this->version = '0.0.1';
        $this->author = 'Wasabi';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '8.0',
            'max' => _PS_VERSION_,
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Developers', [], 'Modules.Developers.Admin');
        $this->description = $this->trans('Add developers management', [], 'Modules.Developers.Admin');
    }

    public function install()
    {
        include __DIR__ . '/sql/install.php';
        return parent::install() &&
            $this->registerHook([
                'displayCustomerAccount'
            ]);
        ;
    }

    public function hookDisplayCustomerAccount()
    {
        $this->context->smarty->assign([
            'link' => $this->context->link->getModuleLink($this->name, 'dashboard')
        ]);
        return $this->display(__FILE__, 'my-account.tpl');
    }

    public function isDeveloperApproved(Customer $customer)
    {
        return $customer->developer_status === self::STATUS_APPROVED;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb['links'] = [
            [
                'title' => $this->getTranslator()->trans('Home', [], 'Shop.Theme.Global'),
                'url' => $this->context->link->getPageLink('index', true),
            ], 
            [
                'title' => $this->trans('Your account', [], 'Shop.Theme.Customeraccount'),
                'url' => $this->context->link->getPageLink('my-account', true),
            ], 
            [
                'title' => $this->l('Developer dashboard'),
                'url' => $this->context->link->getModuleLink($this->name, 'dashboard'),
            ]
        ];
        return $breadcrumb;
    }
}