<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class ps_edition_basic extends Module
{
    private const AVAILABLE_HOOKS = [
        'actionAdminLoginControllerSetMedia',
        'displayBackOfficeHeader',
    ];

    public function __construct()
    {
        $this->name = 'ps_edition_basic';
        $this->version = '0.2.1';
        $this->tab = 'administration';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.4', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->module_key = '5530785cbb44445d52d2a98f8ff6d309';

        parent::__construct();

        $this->displayName = $this->l('PrestaShop Edition Basic');
        $this->description = $this->l('PrestaShop Edition Basic.');
    }

    public function install(): bool
    {
        return parent::install() && $this->registerHook(self::AVAILABLE_HOOKS);
    }

    public function hookActionAdminLoginControllerSetMedia(): void
    {
        $this->addAdminThemeMedia();
    }

    public function hookDisplayBackOfficeHeader(): void
    {
        $this->addAdminThemeMedia();
    }

    private function addAdminThemeMedia(): void
    {
        // $this->context->controller->addCSS($this->getPathUri() . 'views/css/back.min.css');
        $this->context->controller->addCSS('https://unpkg.com/@prestashopcorp/edition-reskin@latest/dist/back.min.css');
        $this->context->controller->addJS($this->getPathUri() . 'views/js/favicon.js');
    }
}
