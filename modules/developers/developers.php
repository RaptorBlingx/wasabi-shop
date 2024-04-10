<?php

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShopBundle\Entity\Repository\TabRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
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
        $this->version = '0.0.2';
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

        $tabNames = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tabNames[$lang['locale']] = $this->trans('Developers', [], 'Modules.Developers.Admin', $lang['locale']);
        }
        $this->tabs = [
            [
                'route_name' => 'developers_index',
                'class_name' => 'AdminDevelopers',
                'visible' => true,
                'name' => $tabNames,
                'icon' => 'free_breakfast',
                'parent_class_name' => $this->getParentTabClassName()
            ],
        ];
    }

    public function install()
    {
        include __DIR__ . '/sql/install.php';
        // $this->installTabs();
        return parent::install() &&
            $this->registerHook([
                'displayCustomerAccount'
            ]);
        ;
    }

    public function getContent()
    {
        $route = $this->get('router')->generate('developers_index');
        Tools::redirectAdmin($route);
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

    protected function getParentTabClassName()
    {
        /** @var TabRepository */
        $tabRepository = SymfonyContainer::getInstance()?->get('prestashop.core.admin.tab.repository');
        if ($tabRepository?->findOneIdByClassName('AdminMarketPlace')) {
            return 'AdminMarketPlace';
        }
        return 'SELL';
    }
}