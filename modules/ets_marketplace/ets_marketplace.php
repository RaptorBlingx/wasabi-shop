<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if(!defined('_PS_VERSION_')) 
	exit;
require_once(dirname(__FILE__) . '/classes/Ets_mp_paggination_class.php');
require_once(dirname(__FILE__) . '/classes/seller.php');
require_once(dirname(__FILE__) . '/classes/billing.php');
require_once(dirname(__FILE__) . '/classes/registration.php');
require_once(dirname(__FILE__) . '/classes/commission.php');
require_once(dirname(__FILE__) . '/classes/Ets_mp_paymentmethod.php');
require_once(dirname(__FILE__) . '/classes/Ets_mp_paymentmethodfield.php');
require_once(dirname(__FILE__) . '/classes/Commission_Usage.php');
require_once(dirname(__FILE__) . '/classes/Ets_mp_withdraw.php');
require_once(dirname(__FILE__) . '/classes/Ets_mp_withdraw_field.php');
require_once(dirname(__FILE__) . '/classes/HTMLTemplateBillingPdf.php');
require_once(dirname(__FILE__) . '/classes/manager.php');
require_once(dirname(__FILE__) . '/classes/report.php');
require_once(dirname(__FILE__) . '/classes/group.php');
require_once(dirname(__FILE__) . '/classes/contact.php');
require_once(dirname(__FILE__) . '/classes/shop_category.php');
require_once(dirname(__FILE__) . '/classes/contact_message.php');
require_once(dirname(__FILE__) . '/classes/Ets_mp_email.php');
require_once(dirname(__FILE__) . '/classes/Ets_mp_product.php');
if(!class_exists('Ets_mp_defines'))
    require_once(dirname(__FILE__) . '/classes/Ets_mp_defines.php');
if (!defined('_PS_ETS_MARKETPLACE_UPLOAD_DIR_')) {
    define('_PS_ETS_MARKETPLACE_UPLOAD_DIR_', _PS_DOWNLOAD_DIR_.'ets_marketplace/');
}
if (!defined('_PS_ETS_MARKETPLACE_UPLOAD_')) {
    define('_PS_ETS_MARKETPLACE_UPLOAD_', __PS_BASE_URI__.'download/ets_marketplace/');
}
if (!defined('_PS_ETS_MARKETPLACE_LOG_DIR_')) {
    if (file_exists(_PS_ROOT_DIR_ . '/var/logs')) {
        define('_PS_ETS_MARKETPLACE_LOG_DIR_', _PS_ROOT_DIR_ . '/var/logs/');
    } else
        define('_PS_ETS_MARKETPLACE_LOG_DIR_', _PS_ROOT_DIR_ . '/log/');
}
class Ets_marketplace extends PaymentModule
{ 
    public $is17 = false;
    public $is15 = false;
    public $_errors = array();
    public $_path_module;
    public $_use_feature;
    public $_use_attribute;
    public $_hooks = array(
        'displayBackOfficeHeader',
        'displayHome',
        'displayCustomerAccount',
        'displayMyAccountBlock',
        'actionValidateOrder',
        'displayHeader',
        'displayFooter',
        'displayMPLeftContent',
        'actionOrderStatusUpdate',
        'paymentOptions',
        'payment',
        'paymentReturn',
        'displayCartExtraProductActions',
        'displayProductPriceBlock',
        'actionProductUpdate',
        'actionProductDelete',
        'displayETSMPFooterYourAccount',
        'displayProductAdditionalInfo',
        'displayRightColumnProduct',
        'displayFooterProduct',
        'actionObjectLanguageAddAfter',
        'actionObjectCustomerDeleteAfter',
        'moduleRoutes',
        'displayShoppingCartFooter',
        'actionObjectOrderDetailUpdateAfter',
        'actionObjectOrderDetailAddAfter',
        'actionObjectOrderDetailDeleteAfter',
        'displayProductListReviews',
        'displayPDFInvoice',
        'displayAdminProductsSeller',
        'displayOrderDetail',
        'displayAfterCarrier',
        'actionObjectProductUpdateBefore'
    );
    public $file_types = array('jpg', 'gif', 'jpeg', 'png','doc','docs','docx','pdf','zip','txt');
    public function __construct()
	{
        $this->name = 'ets_marketplace';
		$this->tab = 'market_place';
		$this->version = '3.6.2';
		$this->author = 'PrestaHero';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);
		$this->bootstrap = true;
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true; 
        if (version_compare(_PS_VERSION_, '1.6', '<'))
            $this->is15 = true;
        $this->module_key = 'eb5f1931437c485fa5ccdb6a0477081b';
        $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
		parent::__construct();
        $this->displayName =$this->l('Marketplace Builder');
        $this->description = $this->l('Turn PrestaShop into marketplace with simple set up steps, #1 PrestaShop Marketplace module (multi vendor) that allows sellers to list their products for sale and pay a percentage fee amount for each sale or a membership fee');
        $this->_path_module = $this->_path;
        if(Configuration::get('ETS_MP_SELLER_USER_GLOBAL_FEATURE') || Configuration::get('ETS_MP_SELLER_CREATE_FEATURE'))
            $this->_use_feature = true;
        else
            $this->_use_feature = false;
        if(Configuration::get('ETS_MP_SELLER_CREATE_PRODUCT_ATTRIBUTE') && ( Configuration::get('ETS_MP_SELLER_CREATE_ATTRIBUTE') || Configuration::get('ETS_MP_SELLER_USER_GLOBAL_ATTRIBUTE')))
            $this->_use_attribute = true;
        else
            $this->_use_attribute = false;
        $recaptcha = (string)Tools::getValue('g-recaptcha-response') ?: '';
        if(!Validate::isCleanHtml($recaptcha))
            $recaptcha='';
        
        $secret = Configuration::get('ETS_MP_ENABLE_CAPTCHA_TYPE')=='google_v2' ? Configuration::get('ETS_MP_ENABLE_CAPTCHA_SECRET_KEY2') : Configuration::get('ETS_MP_ENABLE_CAPTCHA_SECRET_KEY3');
        $this->link_capcha="https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $recaptcha . "&remoteip=" . Tools::getRemoteAddr();
    }
    public function _registerHooks()
    {
        if($this->_hooks)
        {
            foreach($this->_hooks as $hook)
            {
                $this->registerHook($hook);
            }
        }
        return true;
    }
    public function changOrverideBeforInstall()
    {
        if(!$this->is17)
        {
            $overide_cart = Tools::file_get_contents(dirname(__FILE__).'/override/classes/Cart.php');
            if(Tools::strpos($overide_cart,'bool $keepOrderPrices=false'))
            {
                $overide_cart = str_replace('bool $keepOrderPrices=false','$keepOrderPrices=false',$overide_cart);
                file_put_contents(dirname(__FILE__).'/override/classes/Cart.php',$overide_cart);
            }
        }
        return true;
    }
    public function install()
	{
        $this->changOrverideBeforInstall();
        Ets_mp_defines::getInstance()->_installDb();
	    return parent::install()
        && $this->_installDb() 
        && $this->_registerHooks()
        && $this->_installDbDefault() 
        && $this->_installTabs() 
        && $this->createTemplateMail()
        && $this->installLinkDefault() && Ets_mp_defines::createIndexDataBase()&&$this->_installOverried();
    }
    public function createTemplateMail()
    {
        $languages= Language::getLanguages(false);
        foreach($languages as $language)
        {
            $this->copy_directory(dirname(__FILE__).'/mails/en', dirname(__FILE__).'/mails/'.$language['iso_code']);
        }
        return true;
    }
    public function copy_directory($src, $dst)
    {
        if(!is_dir($src))
            return '';
        $dir = opendir($src);
        if(!file_exists($dst))
            @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copy_directory($src . '/' . $file, $dst . '/' . $file);
                } elseif(!file_exists($dst . '/' . $file)) {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    public function delete_template_overried($directory)
    {
        $dir = opendir($directory);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($directory . '/' . $file)) {
                    $this->delete_template_overried($directory . '/' . $file);
                } else {
                    if (file_exists($directory . '/' . $file) && $file != 'index.php' && ($content = Tools::file_get_contents($directory . '/' . $file)) && Tools::strpos($content, 'overried by chung_ets_marketplace') !== false) {
                        @unlink($directory . '/' . $file);
                        if (file_exists($directory . '/backup_' . $file))
                            copy($directory . '/backup_' . $file, $directory . '/' . $file);
                    }

                }
            }
        }
        closedir($dir);
    }
    public function _installOverried()
    {
        $this->copy_directory(dirname(__FILE__) . '/views/templates/admin/_configure/templates', _PS_OVERRIDE_DIR_ . 'controllers/admin/templates');
        if(!$this->is17)
        {
            $overide_cart = Tools::file_get_contents(dirname(__FILE__).'/override/classes/Cart.php');
            if(Tools::strpos($overide_cart,'$keepOrderPrices=false'))
            {
                $overide_cart = str_replace('$keepOrderPrices=false','bool $keepOrderPrices=false',$overide_cart);
                file_put_contents(dirname(__FILE__).'/override/classes/Cart.php',$overide_cart);
            }
        }
        return true;
    }
    public function _unInstallOverried()
    {
        $this->delete_template_overried(_PS_OVERRIDE_DIR_ . 'controllers/admin/templates');
        if(!$this->is17)
        {
            $overide_cart = Tools::file_get_contents(dirname(__FILE__).'/override/classes/Cart.php');
            if(Tools::strpos($overide_cart,'$keepOrderPrices=false'))
            {
                $overide_cart = str_replace('$keepOrderPrices=false','bool $keepOrderPrices=false',$overide_cart);
                file_put_contents(dirname(__FILE__).'/override/classes/Cart.php',$overide_cart);
            }
        }
        return true;
    }
    public function _installTabs()
    {
        $languages = Language::getLanguages(false);
        if(!Tab::getIdFromClassName('AdminMarketPlace'))
        {
            $tab = new Tab();
            $tab->class_name = 'AdminMarketPlace';
            $tab->module = $this->name;
            $tab->id_parent = 0;            
            foreach($languages as $lang){
                $tab->name[$lang['id_lang']] = $this->getTextLang('Market place',$lang) ? : $this->l('Market place');
            }
            $tab->save();
        }
        $tabId = Tab::getIdFromClassName('AdminMarketPlace');
        if($tabId)
        {
            if(!Tab::getIdFromClassName('AdminMarketPlaceAjax'))
            {
                $tab = new Tab();
                $tab->class_name = 'AdminMarketPlaceAjax';
                $tab->module = $this->name;
                $tab->id_parent = $tabId;    
                $tab->active=0;        
                foreach($languages as $lang){
                    $tab->name[$lang['id_lang']] = $this->getTextLang('Market place ajax',$lang) ? : $this->l('Market place ajax');
                }
                $tab->save();
            }
            $subTabs = array(
                array(
                    'class_name' =>'AdminMarketPlaceDashboard',
                    'tab_name' => $this->l('Dashboard'),
                    'tabname' => 'Dashboard',
                    'icon'=>'icon icon-dashboard',
                ),
                array(
                    'class_name' => 'AdminMarketPlaceOrders',
                    'tab_name' => $this->l('Orders'),
                    'tabname' => 'Orders',
                    'icon'=>'icon icon-orders',
                ),
                array(
                    'class_name' => 'AdminMarketPlaceProducts',
                    'tab_name' => $this->l('Products'),
                    'tabname' => 'Products',
                    'icon'=>'icon icon-products',
                ),
                array(
                    'class_name' => 'AdminMarketPlaceRatings',
                    'tab_name' => $this->l('Ratings'),
                    'tabname' => 'Ratings',
                    'icon'=>'icon icon-ratings',
                ),
                array(
                    'class_name' => 'AdminMarketPlaceCommissions',
                    'tab_name' => $this->l('Commissions'),
                    'tabname' => 'Commissions',
                    'icon'=>'icon icon-commission',
                ),
                array(
                    'class_name' => 'AdminMarketPlaceBillings',
                    'tab_name' => $this->l('Membership'),
                    'tabname' => 'Membership',
                    'icon'=>'icon icon-billing', 
                ),
                array(
                    'class_name' => 'AdminMarketPlaceWithdrawals',
                    'tab_name' => $this->l('Withdrawals'),
                    'tabname' => 'Withdrawals',
                    'icon'=>'icon icon-withdraw',
                ),
                array(
                    'class_name' => 'AdminMarketPlaceRegistrations',
                    'tab_name' => $this->l('Applications'),
                    'tabname' => 'Applications',
                    'icon'=>'icon icon-sellers_registration',
                ),
                array(
                    'class_name' => 'AdminMarketPlaceShopSellers',
                    'tab_name' => $this->l('Shops'),
                    'tabname' => 'Shops',
                    'icon'=>'icon icon-sellers',
                    'subs' => array(
                        'AdminMarketPlaceSellers' => array(
                            'tab_name' => $this->l('Shops'),
                            'tabname' => 'Shops',
                            'class_name'=> 'AdminMarketPlaceSellers',
                            'icon' => 'icon icon-sellers', 
                        ),
                        'AdminMarketPlaceShopGroups' => array(
                            'tab_name' => $this->l('Shop groups'),
                            'tabname' => 'Shop groups',
                            'class_name'=> 'AdminMarketPlaceShopGroups',
                            'icon' => 'icon icon-group',
                        ),
                        'AdminMarketPlaceReport' => array(
                            'tab_name' => $this->l('Reports'),
                            'tabname' => 'Reports',
                            'class_name' => 'AdminMarketPlaceReport',
                            'icon' => 'icon icon-report',
                        ),
                        'AdminMarketPlaceCategory' => array(
                            'tab_name' => $this->l('Shop categories'),
                            'tabname' => 'Shop categories',
                            'class_name' => 'AdminMarketPlaceCategory',
                            'icon' => 'icon icon-shop-category',
                        )
                        
                    ),
                ),
                array(
                    'class_name' => 'AdminMarketPlaceSettings',
                    'tab_name' => $this->l('Settings'),
                    'tabname' => 'Settings',
                    'icon'=>'icon icon-settings',
                    'subs' => array(
                         array(
                            'class_name' => 'AdminMarketPlaceSettingsGeneral',
                            'tab_name' => $this->l('General'),
                            'tabname' => 'General',
                            'icon'=>'icon icon-settings',
                        ),   
                        array(
                            'class_name' => 'AdminMarketPlaceCommissionsUsage',
                            'tab_name' => $this->l('Commissions'),
                            'tabname' => 'Commissions',
                            'icon'=>'icon icon-commissions-usage',
                        ),
                        array(
                            'class_name' => 'AdminMarketPlaceCronJob',
                            'tab_name' => $this->l('Cronjob'),
                            'tabname' => 'Cronjob',
                            'icon'=>'icon icon-Cronjob',
                        )
                    )
                ),
            );
            foreach($subTabs as $tabArg)
            {
                if(!Tab::getIdFromClassName($tabArg['class_name']))
                {
                    $tab = new Tab();
                    $tab->class_name = $tabArg['class_name'];
                    $tab->module = $this->name;
                    $tab->id_parent = $tabId; 
                    $tab->icon=$tabArg['icon'];           
                    foreach($languages as $lang){
                        $tab->name[$lang['id_lang']] = $this->getTextLang($tabArg['tabname'],$lang)?: $tabArg['tab_name'];
                    }
                    $tab->save();
                    if(isset($tabArg['subs']) && $tabArg['subs'])
                    {
                        foreach($tabArg['subs'] as $sub)
                        {
                            $subtab = new Tab();
                            $subtab->class_name = $sub['class_name'];
                            $subtab->module = $this->name;
                            $subtab->id_parent = $tab->id; 
                            $subtab->icon=$sub['icon'];           
                            foreach($languages as $lang){
                                $subtab->name[$lang['id_lang']] = $this->getTextLang($sub['tabname'],$lang)?: $sub['tab_name'];
                            }
                            $subtab->save();
                        }
                    }
                }elseif($tab_id = Tab::getIdFromClassName($tabArg['class_name']) && isset($tabArg['subs']) && $tabArg['subs'])
                {
                    foreach($tabArg['subs'] as $sub)
                    {
                        if(!Tab::getIdFromClassName($sub['class_name']))
                        {
                            $subtab = new Tab();
                            $subtab->class_name = $sub['class_name'];
                            $subtab->module = $this->name;
                            $subtab->id_parent = $tab_id; 
                            $subtab->icon=$sub['icon'];           
                            foreach($languages as $lang){
                                $subtab->name[$lang['id_lang']] = $this->getTextLang($sub['tabname'],$lang)?:$sub['tab_name'];
                            }
                            $subtab->save();
                        }
                        
                    }
                }
            }                
        }            
        return true;
    }
    public function setMetas()
    {
        $meta = array();
        $module = Tools::getValue('module');
        if(!Validate::isModuleName($module))
            $module!='';
        $controller = Tools::getValue('controller');
        if(!Validate::isControllerName($controller))
            $controller='';
        if(trim($module)== $this->name && $controller=='shop')
        {
            if(($id_seller=Tools::getValue('id_seller')) && Validate::isUnsignedId($id_seller))
            {
                $seller = new Ets_mp_seller((int)$id_seller,$this->context->language->id);
                if(Validate::isLoadedObject($seller))
                {
                    $meta['meta_title'] = $seller->shop_name ?: $seller->seller_name;
                    $meta['description'] = Tools::strlen(strip_tags($seller->shop_description)) <=256 ? strip_tags($seller->shop_description) : Tools::substr(strip_tags($seller->shop_description),0,Tools::strpos(strip_tags($seller->shop_description)," ",255));
                }
                else
                {
                    $meta['meta_title'] = '';
                    $meta['description'] = '';
                }
                
            }
            else
            {
                $meta['meta_title'] = Configuration::get('ETS_MP_SHOP_META_TITLE',$this->context->language->id) ? : $this->l('Shops');
                $meta_description = Configuration::get('ETS_MP_SHOP_META_DESCRIPTION',$this->context->language->id);
                $meta['description'] = Tools::strlen(strip_tags($meta_description)) <=256 ? strip_tags($meta_description) : Tools::substr(strip_tags($meta_description),0,Tools::strpos(strip_tags($meta_description)," ",255));
            }
            if($this->is17)
            {
                $body_classes = array(
                    'lang-'.$this->context->language->iso_code => true,
                    'lang-rtl' => (bool) $this->context->language->is_rtl,
                    'country-'.$this->context->country->iso_code => true,                              
                );
                $page = array(
                    'title' => '',
                    'canonical' => '',
                    'meta' => array(
                        'title' => isset($meta['meta_title'])? $meta['meta_title'] :'',
                        'description' => isset($meta['description']) ? $meta['description'] :'',
                        'keywords' => isset($meta['keywords']) ? $meta['keywords'] :'',
                        'robots' => 'index',
                    ),
                    'page_name' => '',
                    'body_classes' => $body_classes,
                    'admin_notifications' => array(),
                ); 
                $this->context->smarty->assign(array('page' => $page)); 
            }    
            else
            {
                $this->context->smarty->assign($meta);
            }   
        }        
    }
    public function installLinkDefault()
    {
        $metas= array(
            array(
                'controller' => 'dashboard',
                'title' => $this->l('Dashboard'),
                'tabname' => 'Dashboard',
                'url_rewrite' => 'seller-dashboard',
                'url_rewrite_lang' =>$this->l('seller-dashboard'),
            ),
            array(
                'controller' => 'orders',
                'title' => $this->l('Orders'),
                'tabname' => 'Orders',
                'url_rewrite' => 'seller-orders',
                'url_rewrite_lang' =>$this->l('seller-orders'),
            ),
            array(
                'controller' => 'products',
                'title' => $this->l('Products'),
                'tabname' => 'Products',
                'url_rewrite' => 'seller-products',
                'url_rewrite_lang' =>$this->l('seller-products'),
            ),
            array(
                'controller' => 'ratings',
                'title' => $this->l('Ratings'),
                'tabname' => 'Ratings',
                'url_rewrite' => 'seller-product-ratings',
                'url_rewrite_lang' =>$this->l('seller-product-ratings'),
            ),
            array(
                'controller' => 'commissions',
                'title' => $this->l('Commissions'),
                'tabname' => 'Commissions',
                'url_rewrite' => 'seller-commissions',
                'url_rewrite_lang' =>$this->l('seller-commissions'),
            ),
            array(
                'controller' => 'billing',
                'title' => $this->l('Membership'),
                'tabname' => 'Membership',
                'url_rewrite' => 'seller-membership-invoices',
                'url_rewrite_lang' =>$this->l('seller-membership-invoices'),
            ),
            array(
                'controller' => 'withdraw',
                'title' => $this->l('Withdrawals'),
                'tabname' => 'Withdrawals',
                'url_rewrite' => 'seller-withdrawals',
                'url_rewrite_lang' =>$this->l('seller-withdrawals'),
            ),
            array(
                'controller' => 'voucher',
                'title' => $this->l('My vouchers'),
                'tabname' => 'My vouchers',
                'url_rewrite' => 'seller-vouchers',
                'url_rewrite_lang' =>$this->l('seller-vouchers'),
            ),
            array(
                'controller' => 'attributes',
                'title' => $this->l('Attributes'),
                'tabname' => 'Attributes',
                'url_rewrite' => 'seller-attributes',
                'url_rewrite_lang' =>$this->l('seller-attributes'),
            ),
            array(
                'controller' => 'features',
                'title' => $this->l('Features'),
                'tabname' => 'Features',
                'url_rewrite' => 'seller-features',
                'url_rewrite_lang' =>$this->l('seller-features'),
            ),
            array(
                'controller' => 'discount',
                'title' => $this->l('Discounts'),
                'tabname' => 'Discounts',
                'url_rewrite' => 'seller-discounts',
                'url_rewrite_lang' =>$this->l('seller-discounts'),
            ),
            array(
                'controller' => 'messages',
                'title' => $this->l('Messages'),
                'tabname' => 'Messages',
                'url_rewrite' => 'seller-messages',
                'url_rewrite_lang' =>$this->l('seller-messages'),
            ),
            array(
                'controller' => 'profile',
                'title' => $this->l('Profile'),
                'tabname' => 'Profile',
                'url_rewrite' => 'seller-profile',
                'url_rewrite_lang' =>$this->l('seller-profile'),
            ),
            array(
                'controller'=>'vacation',
                'title' => $this->l('Vacation mode'),
                'tabname' => 'Vacation mode',
                'url_rewrite' => 'vacation-mode',
                'url_rewrite_lang' => $this->l('vacation-mode'),
            ),
            array(
                'controller' => 'create',
                'title' => $this->l('Create'),
                'tabname' => 'Create',
                'url_rewrite' => 'seller-create-shop',
                'url_rewrite_lang' =>$this->l('seller-create-shop'),
            ),
            array(
                'controller' => 'registration',
                'title' => $this->l('Application'),
                'tabname' => 'Application',
                'url_rewrite' => 'seller-application',
                'url_rewrite_lang' =>$this->l('seller-application'),
            ),
            array(
                'controller' => 'myseller',
                'title' => $this->l('Seller account'),
                'tabname' => 'Seller account',
                'url_rewrite' => 'seller-account',
                'url_rewrite_lang' =>$this->l('seller-account'),
            ),
            array(
                'controller' => 'brands',
                'title' => $this->l('Brands'),
                'tabname' => 'Brands',
                'url_rewrite' => 'seller-brands',
                'url_rewrite_lang' =>$this->l('seller-brands'),
            ),
            array(
                'controller' => 'suppliers',
                'title' => $this->l('Suppliers'),
                'tabname' => 'Suppliers',
                'url_rewrite' => 'seller-suppliers',
                'url_rewrite_lang' =>$this->l('seller-suppliers'),
            ),
            array(
                'controller' => 'import',
                'title' => $this->l('Import products'),
                'tabname' => 'Import products',
                'url_rewrite' => 'seller-import-products',
                'url_rewrite_lang' =>$this->l('seller-import-products'),
            ),
            array(
                'controller' => 'contactseller',
                'title' => $this->l('Contact shop'),
                'tabname' => 'Contact shop',
                'url_rewrite' => 'seller-contact',
                'url_rewrite_lang' =>$this->l('seller-contact'),
            ),
            array(
                'controller' => 'carrier',
                'title' => $this->l('Carriers'),
                'tabname' => 'Carriers',
                'url_rewrite' => 'seller-carrier',
                'url_rewrite_lang' =>$this->l('seller-carrier'),
            ),
            array(
                'controller' => 'manager',
                'title' => $this->l('Shop managers'),
                'tabname' => 'Shop managers',
                'url_rewrite' => 'seller-manager',
                'url_rewrite_lang' =>$this->l('seller-manager'),
            ),
            array(
                'controller' => 'map',
                'title' => $this->l('Store locations'),
                'tabname' => 'Store locations',
                'url_rewrite' => 'store-locations',
                'url_rewrite_lang' =>$this->l('store-locations'),
            ),
            array(
                'controller' => 'stock',
                'title' => $this->l('Stock'),
                'tabname' => 'Stock',
                'url_rewrite' => 'seller-product-stock',
                'url_rewrite_lang' =>$this->l('seller-product-stock'),
            )
        );
        $languages = Language::getLanguages(false);
        foreach($metas as $meta)
        {
            if(!Ets_mp_defines::checkUrlMeta($meta['url_rewrite'],$meta['controller']))
            {
                $meta_class = new Meta();
                $meta_class->page = 'module-'.$this->name.'-'.$meta['controller'];
                $meta_class->configurable=1;
                foreach($languages as $language)
                {
                    $meta_class->title[$language['id_lang']] = $this->getTextLang($meta['tabname'],$language) ?: $meta['title'];
                    $meta_class->url_rewrite[$language['id_lang']] = ($link_rewrite = $this->getTextLang($meta['url_rewrite_lang'],$language)) ? Tools::link_rewrite($link_rewrite) :  $meta['url_rewrite'];
                }
                $meta_class->add();
            }
        }
        return true;
    }
    public function unInstallLinkDefault()
    {
        $metas= array(
            array(
                'controller' => 'dashboard',
                'title' => $this->l('Dashboard'),
                'url_rewrite' => 'seller-dashboard'
            ),
            array(
                'controller' => 'orders',
                'title' => $this->l('Orders'),
                'url_rewrite' => 'seller-orders'
            ),
            array(
                'controller' => 'products',
                'title' => $this->l('Products'),
                'url_rewrite' => 'seller-products'
            ),
            array(
                'controller' => 'commissions',
                'title' => $this->l('Commissions'),
                'url_rewrite' => 'seller-commissions'
            ),
            array(
                'controller' => 'billing',
                'title' => $this->l('Membership'),
                'url_rewrite' => 'seller-membership-invoices'
            ),
            array(
                'controller' => 'withdraw',
                'title' => $this->l('Withdrawals'),
                'url_rewrite' => 'seller-withdrawals'
            ),
            array(
                'controller' => 'voucher',
                'title' => $this->l('My vouchers'),
                'url_rewrite' => 'seller-vouchers'
            ),
            array(
                'controller' => 'attributes',
                'title' => $this->l('Attributes and Features'),
                'url_rewrite' => 'seller-attributes'
            ),
            array(
                'controller' => 'features',
                'title' => $this->l('Attributes and Features'),
                'url_rewrite' => 'seller-features'
            ),
            array(
                'controller' => 'discount',
                'title' => $this->l('Discounts'),
                'url_rewrite' => 'seller-discounts'
            ),
            array(
                'controller' => 'messages',
                'title' => $this->l('Messages'),
                'url_rewrite' => 'seller-messages'
            ),
            array(
                'controller' => 'profile',
                'title' => $this->l('Profile'),
                'url_rewrite' => 'seller-profile'
            ),
            array(
                'controller'=>'vacation',
                'title' => $this->l('Vacation mode'),
                'url_rewrite' => 'vacation-mode',
            ),            
            array(
                'controller' => 'create',
                'title' => $this->l('Create'),
                'url_rewrite' => 'seller-create-shop'
            ),
            array(
                'controller' => 'registration',
                'title' => $this->l('Application'),
                'url_rewrite' => 'seller-application'
            ),
            array(
                'controller' => 'myseller',
                'title' => $this->l('Seller account'),
                'url_rewrite' => 'seller-account'
            ),
            array(
                'controller' => 'brands',
                'title' => $this->l('Brands'),
                'url_rewrite' => 'seller-brands'
            ),
            array(
                'controller' => 'import',
                'title' => $this->l('Import products'),
                'url_rewrite' => 'seller-import-products'
            ),
            array(
                'controller' => 'contactseller',
                'title' => $this->l('Seller contact'),
                'url_rewrite' => 'seller-contact'
            ),
            array(
                'controller' => 'carrier',
                'title' => $this->l('Carriers'),
                'url_rewrite' => 'seller-carrier'
            ),
            array(
                'controller' => 'manager',
                'title' => $this->l('Shop managers'),
                'url_rewrite' => 'seller-manager'
            ),
            array(
                'controller' => 'map',
                'title' => $this->l('Store locations'),
                'url_rewrite' => 'store-locations'
            ),
            array(
                'controller' => 'stock',
                'title' => $this->l('Stock'),
                'url_rewrite' => 'seller-product-stock',
            )
        );
        foreach($metas as $meta)
        {
            if($id_meta = Ets_mp_defines::getIDMeta($meta['controller']))
            {
                $meta_class = new Meta($id_meta);
                $meta_class->delete();
            }
        }
        return true;
    }
    public function _installDb(){
        $files = glob(dirname(__FILE__).'/views/import/*'); 
        if($files)
        {
           foreach($files as $file){ 
                if(file_exists($file) && $file!=dirname(__FILE__).'/views/import/index.php')
                    @unlink($file); 
            } 
        }
        return true;
    }
    public function _installFieldConfigDefault()
    {
        $languages = Language::getLanguages(false);
        if($settings = Ets_mp_defines::getInstance()->getFieldConfig('settings'))
        {
            foreach($settings as $setting)
            {
                if(!Configuration::hasKey($setting['name']))
                {
                    if(($setting['type']=='categories' || $setting['type']=='tre_categories') && isset($setting['default']) && $setting['default'])
                        Configuration::updateGlobalValue($setting['name'],implode(',',$setting['default']));
                    elseif(isset($setting['default']))
                    {
                        if(isset($setting['lang']) && $setting['lang'])
                        {
                            $values = array();
                            foreach($languages as $language)
                            {
                                $values[$language['id_lang']] = $setting['default'];
                            }
                            Configuration::updateGlobalValue($setting['name'],$values,true);
                        }
                        else
                            Configuration::updateGlobalValue($setting['name'],$setting['default'],true);
                    }
                }
            }
        }
        Configuration::updateGlobalValue('ETS_MP_REGISTRATION_FIELDS_VALIDATE','shop_phone,message_to_administrator');
        Configuration::updateGlobalValue('ETS_MP_CONTACT_FIELDS_VALIDATE','title,message');
        $commission_usage_settings = Ets_mp_defines::getInstance()->getFieldConfig('commission_usage_settings');
        if($commission_usage_settings)
        {
            foreach($commission_usage_settings as $setting)
            {
                if(isset($setting['default']) && !Configuration::hasKey($setting['name']))
                    Configuration::updateGlobalValue($setting['name'],$setting['default']);
            }
        }
        $commission_rate_settings = Ets_mp_defines::getInstance()->getFieldConfig('commission_rate_settings');
        if($commission_rate_settings)
        {
            foreach($commission_rate_settings as $setting)
            {
                if(isset($setting['default']) && !Configuration::hasKey($setting['name']))
                    Configuration::updateGlobalValue($setting['name'],$setting['default']);
            }
        }
        return true;
    }
    public function _installDbDefault(){
        $languages = Language::getLanguages(false);
        $this->_installFieldConfigDefault();
        $pm_params = array();
        $pmf_params = array(
            array(
                'type' => 'text',
                'required' => 1,
                'enable' => 1,
                'sort' => 1,
            ),
            array(
                'type' => 'text',
                'required' => 1,
                'enable' => 1,
                'sort' => 2,
            ),
            array(
                'type' => 'text',
                'required' => 1,
                'enable' => 1,
                'sort' => 3,
            ),
            array(
                'type' => 'text',
                'required' => 1,
                'enable' => 1,
                'sort' => 4,
            ),
        );
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $pm_params['title'][$lang['id_lang']] = $this->getTextLang('PayPal',$lang) ?:  $this->l('PayPal');
            $pm_params['desc'][$lang['id_lang']] = $this->getTextLang('The fastest method to withdraw funds, directly to your local bank account!',$lang) ?: $this->l('The fastest method to withdraw funds, directly to your local bank account!');
            $pm_params['note'][$lang['id_lang']] = null;
            foreach ($pmf_params as &$p) {
                if($p['sort'] == 1){
                    $p['title'][$lang['id_lang']] = $this->getTextLang('First name',$lang) ?: $this->l('First name');
                    $p['desc'][$lang['id_lang']] = $this->getTextLang('Type your first name',$lang) ?: $this->l('Type your first name');
                }
                elseif($p['sort'] == 2){
                    $p['title'][$lang['id_lang']] = $this->getTextLang('Last name',$lang) ?: $this->l('Last name');
                    $p['desc'][$lang['id_lang']] =  $this->getTextLang('Type your last name',$lang) ? : $this->l('Type your last name');
                }
                elseif($p['sort'] == 3){
                    $p['title'][$lang['id_lang']] = $this->getTextLang('PayPal email',$lang)?: $this->l('PayPal email');
                    $p['desc'][$lang['id_lang']] = $this->getTextLang('Type your PayPal email to receive money',$lang) ?: $this->l('Type your PayPal email to receive money');
                }
                elseif($p['sort'] == 4){
                    $p['title'][$lang['id_lang']] = $this->getTextLang('Phone',$lang) ?: $this->l('Phone');
                    $p['desc'][$lang['id_lang']] =  $this->getTextLang('Type your phone number',$lang) ?: $this->l('Type your phone number');
                }
            }
        }
        $pm_params['fee_fixed'] = 1;
        $pm_params['fee_type'] = 'NO_FEE';
        $pm_params['fee_percent'] = null;
        $pm_params['estimate_processing_time'] = 30;
        $pm = new Ets_mp_paymentmethod();
        $pm->title = $pm_params['title'];
        $pm->description = $pm_params['desc'];
        $pm->fee_fixed = $pm_params['fee_fixed'];
        $pm->fee_type = $pm_params['fee_type'];
        $pm->enable=1;
        $pm->id_shop= $this->context->shop->id;
        $pm->sort=1;
        $pm->estimated_processing_time = $pm_params['estimate_processing_time'];
        $pm->logo = 'paypal.png';
        $id_pm = $pm->add();
        if($id_pm){
            if(!is_dir(_PS_IMG_DIR_.'mp_payment/'))
            {
                @mkdir(_PS_IMG_DIR_.'mp_payment/',0777,true);
                @copy(dirname(__FILE__).'/index.php', _PS_IMG_DIR_.'mp_payment/index.php');
            }
            Tools::copy(_PS_MODULE_DIR_.$this->name.'/views/img/paypal.png',_PS_IMG_DIR_.'mp_payment/paypal.png');
            foreach ($pmf_params as $pmf_param) {
                $pmf = new Ets_mp_paymentmethodfield();
                $pmf->id_ets_mp_payment_method = $id_pm;
                $pmf->title = $pmf_param['title'];
                $pmf->description = $pmf_param['desc'];
                $pmf->type = $pmf_param['type'];
                $pmf->required = $pmf_param['required'];
                $pmf->sort = $pmf_param['sort'];
                $pmf->enable=1;
                $pmf->add();
            }
        }
        return true;
    }
    public function _unRegisterHooks()
    {
        if($this->_hooks)
        {
            foreach($this->_hooks as $hook)
                $this->unregisterHook($hook);
        }
        return true;
    }
    public function uninstall()
	{
        $this->changOrverideBeforInstall();
        return parent::uninstall()
        && $this->_unRegisterHooks()
        && $this->_uninstallDbDefault() && $this->_uninstallDb()&& $this->_uninstallTabs() && $this->unInstallLinkDefault()&& $this->_unInstallOverried();
    }
    public function _uninstallTabs()
    {
        $tabs = array('AdminMarketPlaceDashboard','AdminMarketPlaceOrders','AdminMarketPlaceProducts','AdminMarketPlaceCommissions','AdminMarketPlaceCommissionsUsage','AdminMarketPlaceBillings','AdminMarketPlaceWithdrawals','AdminMarketPlaceRegistrations','AdminMarketPlaceShopSellers','AdminMarketPlaceSellers','AdminMarketPlaceSettings','AdminMarketPlaceRatings');
        if($tabs)
        {
            foreach($tabs as $classname)
            {
                if($tabId = Tab::getIdFromClassName($classname))
                {
                    if($classname=='AdminMarketPlaceSettings')
                    {
                        $subs = array('AdminMarketPlaceSettingsGeneral','AdminMarketPlacePayments','AdminMarketPlaceCronJob');
                        foreach($subs as $sub)
                        {
                            if($idTab = Tab::getIdFromClassName($sub))
                            {
                                $tab = new Tab($idTab);
                                if($tab)
                                    $tab->delete();
                            }
                        }
                    }
                    if($classname=='AdminMarketPlaceShopSellers')
                    {
                        $subs = array('AdminMarketPlaceSellers','AdminMarketPlaceReport','AdminMarketPlaceShopGroups');
                        foreach($subs as $sub)
                        {
                            if($idTab = Tab::getIdFromClassName($sub))
                            {
                                $tab = new Tab($idTab);
                                if($tab)
                                    $tab->delete();
                            }
                        }
                    }
                    $tab = new Tab($tabId);
                    if($tab)
                        $tab->delete();
                }               
            }
            if($tabId = Tab::getIdFromClassName('AdminMarketPlace'))
            {
                $tab = new Tab($tabId);
                if($tab)
                    $tab->delete();
            }
        }
        return true;
    }
    public function _uninstallDbDefault()
    {
        if($settings = Ets_mp_defines::getInstance()->getFieldConfig('settings'))
        {
            foreach($settings as $setting)
            {
                Configuration::deleteByName($setting['name']);
            }
        }
        Configuration::deleteByName('ETS_MP_REGISTRATION_FIELDS_VALIDATE');
        Configuration::deleteByName('ETS_MP_CONTACT_FIELDS_VALIDATE');
        Configuration::deleteByName('ETS_MP_TIME_LOG_CRONJOB');
        $commission_usage_settings = Ets_mp_defines::getInstance()->getFieldConfig('commission_usage_settings');
        if($commission_usage_settings)
        {
            foreach($commission_usage_settings as $setting)
            {
                Configuration::deleteByName($setting['name']);
            }
        }
        $commission_rate_settings = Ets_mp_defines::getInstance()->getFieldConfig('commission_rate_settings');
        if($commission_rate_settings)
        {
            foreach($commission_rate_settings as $setting)
            {
                Configuration::deleteByName($setting['name']);
            }
        }
        return true;
    }
    public function rrmdir($dir)
    {
        $dir = rtrim($dir, '/');
        if ($dir && is_dir($dir)) {
            if ($objects = scandir($dir)) {
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (is_dir($dir . "/" . $object) && !is_link($dir . "/" . $object))
                            $this->rrmdir($dir . "/" . $object);
                        else
                            @unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
    public function _uninstallDb()
    {
        Ets_mp_defines::unInstallDb();
        $this->rrmdir(_PS_ETS_MARKETPLACE_UPLOAD_DIR_);
        $this->rrmdir(_PS_IMG_DIR_.'mp_seller/');
        $this->rrmdir(_PS_IMG_DIR_.'mp_payment/');
        $this->rrmdir(_PS_IMG_DIR_.'mp_group/');
        $files = glob(dirname(__FILE__).'/views/import/*'); 
        if($files)
        {
           foreach($files as $file){ 
                if(file_exists($file) && $file!=dirname(__FILE__).'/views/import/index.php')
                    @unlink($file); 
            } 
        }

        if(file_exists(_PS_ETS_MARKETPLACE_LOG_DIR_.'ets_merketplace.log'))
            @unlink(_PS_ETS_MARKETPLACE_LOG_DIR_.'ets_merketplace.log');
        return true;
    }
    public function getSellerInfoById($id_seller)
    {
        $seller = new Ets_mp_seller($id_seller,$this->context->language->id);
        $this->context->smarty->assign(
            array(
                'seller' => $seller,
                'link'=> $this->context->link,
            )
        );
        return  $this->display(__FILE__,'seller_order_product.tpl');
    }
    public function getRequestContainer()
    {
        $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer','getInstance'));

        if (null !== $sfContainer && null !== $sfContainer->get('request_stack')->getCurrentRequest()) {
            $request = $sfContainer->get('request_stack')->getCurrentRequest();
            return $request;
        }
        return null;
    }
    public function getLinkAdminController($entiny,$params=array())
    {
        $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer','getInstance'));
    	if (null !== $sfContainer) {
    		$sfRouter = $sfContainer->get('router');
    		return $sfRouter->generate(
    			$entiny,
    			$params
    		);
    	}
        
    }
    public function getLinkOrderAdmin($id_order)
    {
        if(version_compare(_PS_VERSION_, '1.7.7.0', '>='))
        {
            $link_order = $this->getLinkAdminController('admin_orders_view',array('orderId' => $id_order));
        }
        else
            $link_order = $this->context->link->getAdminLink('AdminOrders').'&id_order='.(int)$id_order.'&vieworder';
        return $link_order;
    }
    public function hookDisplayBackOfficeHeader($params)
    {
        $tabs = array('AdminMarketPlaceDashboard','AdminMarketPlaceOrders','AdminMarketPlaceProducts','AdminMarketPlaceCommissions','AdminMarketPlaceCommissionsUsage','AdminMarketPlaceBillings','AdminMarketPlaceWithdrawals','AdminMarketPlaceRegistrations','AdminMarketPlaceSellers','AdminMarketPlacePayments','AdminMarketPlaceCronJob','AdminMarketPlaceSettings','AdminMarketPlaceSettingsGeneral','AdminMarketPlaceReport','AdminMarketPlaceShopGroups','AdminMarketPlaceCategory','AdminMarketPlaceRatings');
        $html ='';
        $controller = Tools::getValue('controller');
        if(!Validate::isControllerName($controller))
            $controller='';
        $configure = Tools::getValue('configure');
        if(!Validate::isModuleName($configure))
            $configure!='';
        if($controller=='AdminMarketPlaceDashboard')
        {
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path.'views/js/Chart.min.js');
            $this->context->controller->addCSS($this->_path.'views/css/daterangepicker.css'); 
        }
        if($controller=='AdminMarketPlaceProducts')
        {
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path.'views/js/product_bulk.js');
        }
        if(($controller=='AdminModules' && $configure==$this->name) || in_array($controller,$tabs) )
        {
            $this->context->controller->addCSS($this->_path.'views/css/admin.css');
        }
        if($controller=='AdminOrders' || $controller=='AdminProducts')
        {
            if($controller=='AdminOrders' && ($id_order=Tools::getValue('id_order')) && Validate::isUnsignedId($id_order))
            {
                $id_seller = Ets_mp_seller::getIDByIDOrder($id_order);
            }
            elseif($controller=='AdminProducts')
            {
                if($this->is17)
                {
                    $request = $this->getRequestContainer();
                    if($request)
                        $id_product= $request->get('id');
                    else
                        $id_product = Tools::getValue('id_product');
                }
                else
                    $id_product= Tools::getValue('id_product');
                if($id_product && Validate::isUnsignedId($id_product))
                {
                    $id_seller = Ets_mp_product::getSellerByIdProduct($id_product);
                }    
            }
            if(isset($id_seller) && $id_seller)
            {
                $html .= $this->getSellerInfoById($id_seller);
            }

        }
        $this->context->controller->addCSS($this->_path.'views/css/admin_all.css');
        if(!$this->is17){
            $this->context->controller->addCSS($this->_path.'views/css/admin_16.css');
        }
        $this->context->smarty->assign(
            array(
                'total_registrations' => Ets_mp_registration::getTotalRegistrations(),
                'total_seller_wait_approve' => Ets_mp_seller::getTotalSellerWaitApprove(),
            )
        );
        $this->context->smarty->assign(
            array(
                'ets_mp_module_dir' => $this->_path,
            )
        );
        $html .=$this->display(__FILE__,'admin_header.tpl');
        return $html;
    }
    public function getSfContainer()
    {
        if(!class_exists('\PrestaShop\PrestaShop\Adapter\SymfonyContainer'))
        {
            $kernel = null;
            try{
                $kernel = new AppKernel('prod', false);
                $kernel->boot();
                return $kernel->getContainer();
            }
            catch (Exception $ex){
                return null;
            }
        }
        $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer', 'getInstance'));
        return $sfContainer;
    }
    public function hookActionProductDelete($params)
    {
        if(isset($params['id_product']) && $params['id_product'])
        {
            Ets_mp_product::deleteMpProduct($params['id_product']);
        }
    }
    public function hookActionProductUpdate($params)
    {
        if(isset($params['id_product']) && ($id_product = $params['id_product']) && ($product_seller = Ets_mp_product::getProductSellerByIDProduct($id_product)))
        {
            if(isset($this->context->employee) && isset($this->context->employee->id) && $this->context->employee->id)
            {
               $admin= true; 
            }
            else
                $admin = false;
            $product = new Product($id_product);
            Ets_mp_product::updateStatus($product->id,$product->active,$admin);
            if($product->active && $admin && $product_seller['id_customer'] && Configuration::get('ETS_MP_EMAIL_SELLER_PRODUCT_APPROVED_OR_DECLINED') && $product_seller['approved']!=$product->active)
            {
                $seller = Ets_mp_seller::_getSellerByIdCustomer($product_seller['id_customer']);
                $data = array(
                    '{seller_name}' => $seller->seller_name,
                    '{product_link}' => $this->context->link->getProductLink($product),
                    '{product_name}' => $product->name[$this->context->language->id],
                    '{product_ID}' => $product->id,
                );
                $subjects = array(
                    'translation' => $this->l('Your product is approved'),
                    'origin'=> 'Your product is approved',
                    'specific'=>false
                );
                Ets_marketplace::sendMail('to_seller_product_approved',$data,$seller->seller_email,$subjects,$seller->seller_name);
            }
        }
        
    }
    public function hookDisplayAdminProductsSeller($params)
    {
        if(isset($params['id_product']) && ($id_product = $params['id_product']))
        {
            if(!Ets_mp_product::getProductSellerByIDProduct($id_product,0,true))
            {
                if(($id_customer = Ets_mp_product::getProductSellerByIDProduct($id_product,1,true)))
                {
                    $seller = Ets_mp_seller::_getSellerByIdCustomer($id_customer,$this->context->language->id);
                }
                else
                    $seller = false;
                $this->context->smarty->assign(
                    array(
                        'seller_product' => $seller,
                        'id_product' => $id_product,
                        'is_ps16' => $this->is17 ? false:true,
                        'link_search_seller' => $this->context->link->getAdminLink('AdminMarketPlaceAjax'),
                    )
                );
                return $this->display(__FILE__,'form_add_seller_to_product.tpl');
            }
        }
    }
    public function getContent()
	{
	   $this->context->controller->addJqueryUI('ui.sortable');
       $this->context->controller->addJqueryPlugin('autocomplete');
       $control = (string)Tools::getValue('control');
       if($control && !in_array($control,array('dashboard','commission','commission_usage','billing','withdraw','sellers_registration','sellers','payments','cronjob','products','orders')))
            $control = 'dashboard';
       $controller = Tools::getValue('controller');
       if(!Validate::isControllerName($controller))
            $controller!='';
       $html = '';
       if(Tools::isSubmit('delImage') && ($fieldDel = Tools::getValue('delImage')) && Validate::isCleanHtml($fieldDel))
       {
            if(($image = Configuration::get($fieldDel)) && file_exists(dirname(__FILE__).'/views/img/'.$image))
            {
                @unlink(dirname(__FILE__).'/views/img/'.$image);
                Configuration::deleteByName($fieldDel);
                $this->context->cookie->success_message = $this->l('Deleted successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceSettingsGeneral').'&current_tab=map_settings');
            }
       }
       if(Tools::isSubmit('saveConfig'))
       {
            if($this->_checkFormBeforeSubmit())
            {
                $html .= $this->displayConfirmation($this->l('Save successfully'));
                $this->_saveFromSettings();
            }
       }
	   $this->context->smarty->assign(array(
            'ets_mp_sidebar' => $this->renderSidebar($control),
            'control' => $control,
            'ets_mp_module_dir' => $this->_path,
        ));
        if($control)
        {
            if($this->context->cookie->success_message)
            {
                $html .= $this->displayConfirmation($this->context->cookie->success_message);
                $this->context->cookie->success_message ='';
            }
            $this->context->smarty->assign(
                array(
                    'ets_mp_body_html'=> $this->renderAdminBodyHtml($control),
                )
            );
            if($this->_errors)
                $html .= $this->displayError($this->_errors);
            $html .=$this->display(__FILE__,'admin.tpl');
            return $html;  
        }
        elseif($controller=='AdminModules')
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceDashboard'));
    }
    public function renderSidebar($control)
    {
        $controller = Tools::getValue('controller');
        if(!Validate::isControllerName($controller))
            $controller= '';
        $this->context->smarty->assign(
            array(
                'sidebars' => Ets_mp_defines::getInstance()->getFieldConfig('sidebars'),
                'control' => $control,
                'link'=>$this->context->link,
                'controller'=>$controller,
                'mp_link_module' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name,
            )
        );
        return $this->display(__FILE__,'sidebar.tpl');
    }
    public function renderAdminBodyHtml($control)
    {
        switch ($control) {
            case 'dashboard':
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceDashboard'));
            case 'commission':
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceCommissions'));
            case 'commission_usage':
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceCommissionUsage'));
            case 'billing':
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceBillings'));
            case 'withdraw':
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceWithdrawals'));
            case 'sellers_registration':
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceRegistrations'));
            case 'sellers':
            {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceSellers')); 
            }
            case 'payments':
            {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlacePayments')); 
            }
            case 'cronjob':
                return $this->_renderCronjob();
            case 'products':
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceProducts'));
            case 'orders':
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceOrders'));
            default:
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceDashboard'));
        } 
    }
    public function _renderCronjob()
    {
        if(Tools::isSubmit('etsmpSubmitUpdateToken'))
        {
            $error = '';
            $ETS_MP_CRONJOB_TOKEN = Tools::getValue('ETS_MP_CRONJOB_TOKEN');
            if(!$ETS_MP_CRONJOB_TOKEN)
                $error =$this->l('Token is required');
            elseif(!Validate::isCleanHtml($ETS_MP_CRONJOB_TOKEN))
                $error =$this->l('Token is not valid');
            if(!$error)
            {
                Configuration::updateGlobalValue('ETS_MP_CRONJOB_TOKEN',$ETS_MP_CRONJOB_TOKEN);
                die(
                    json_encode(
                        array(
                            'success' => $this->l('Updated successfully'),
                        )
                    )
                );
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' => $error,
                        )
                    )
                );
            }   
        }
        if(!Configuration::getGlobalValue('ETS_MP_CRONJOB_TOKEN'))
            Configuration::updateGlobalValue('ETS_MP_CRONJOB_TOKEN',Tools::passwdGen(12));
        $this->context->smarty->assign(
            array(
                'dir_cronjob' => dirname(__FILE__).'/cronjob.php',
                'php_path' => (defined('PHP_BINDIR') && PHP_BINDIR && is_string(PHP_BINDIR) ? PHP_BINDIR.'/' : '').'php ',
                'link_conjob' => $this->getBaseLink().'/modules/'.$this->name.'/cronjob.php',
                'ETS_MP_CRONJOB_TOKEN' => Tools::getValue('ETS_MP_CRONJOB_TOKEN',Configuration::getGlobalValue('ETS_MP_CRONJOB_TOKEN')),
            )
        );
        return $this->display(__FILE__,'cronjob.tpl');
    }
    public function _renderSettings()
    {
        $languages = Language::getLanguages(false);
        $fields_form = array(
    		'form' => array(
    			'legend' => array(
    				'title' => $this->l('General'),
    				'icon' => 'icon-settings'
    			),
    			'input' => array(),
                'submit' => array(
    				'title' => $this->l('Save'),
    			)
            ),
    	);
        $configs = Ets_mp_defines::getInstance()->getFieldConfig('settings');
        $fields = array();
        foreach($configs as $config)
        {
            $fields_form['form']['input'][] = $config;
            if($config['type']!='checkbox' && $config['type']!='categories' && $config['type']!='tre_categories')
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    foreach($languages as $language)
                    {
                        $fields[$config['name']][$language['id_lang']] = Tools::getValue($config['name'].'_'.$language['id_lang'],Configuration::get($config['name'],$language['id_lang']));
                    }
                    
                }
                else
                    $fields[$config['name']] = Tools::getValue($config['name'],Configuration::get($config['name']));
            }
            else
                $fields[$config['name']] = Tools::isSubmit('saveConfig') ?  Tools::getValue($config['name']) : explode(',',Configuration::get($config['name']));
            $fields['ETS_MP_REGISTRATION_FIELDS_VALIDATE'] = Tools::isSubmit('saveConfig') ? Tools::getValue('ETS_MP_REGISTRATION_FIELDS_VALIDATE') : explode(',',Configuration::get('ETS_MP_REGISTRATION_FIELDS_VALIDATE'));
            $fields['ETS_MP_CONTACT_FIELDS_VALIDATE'] = Tools::isSubmit('saveConfig') ? Tools::getValue('ETS_MP_CONTACT_FIELDS_VALIDATE') : explode(',',Configuration::get('ETS_MP_CONTACT_FIELDS_VALIDATE'));
        }
        $fields_form['form']['input'][]= array(
            'name' =>'current_tab',
            'type' => 'hidden',
        );
        $current_tab = (string)Tools::getValue('current_tab','conditions');
        if(!in_array($current_tab,array('conditions','application','memberships','seller_settings','map_settings','commission_status','email_settings','message','contact_form','home_page','seller_seo','product_page')))
            $current_tab='conditions';
        $fields['current_tab'] = $current_tab;
        $helper = new HelperForm();
    	$helper->show_toolbar = false;
    	$helper->table = $this->table;
    	$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
    	$helper->default_form_language = $lang->id;
    	$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
    	$this->fields_form = array();
    	$helper->module = $this;
    	$helper->identifier = $this->identifier;
    	$helper->submit_action = 'saveConfig';
    	$helper->currentIndex = $this->context->link->getAdminLink('AdminMarketPlaceSettingsGeneral', false);
    	$helper->token = Tools::getAdminTokenLite('AdminMarketPlaceSettingsGeneral');
    	$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));            
        $helper->tpl_vars = array(
    		'base_url' => $this->context->shop->getBaseURL(),
    		'language' => array(
    			'id_lang' => $language->id,
    			'iso_code' => $language->iso_code
    		),
    		'fields_value' => $fields,
    		'languages' => $this->context->controller->getLanguages(),
            'configTabs' => Ets_mp_defines::getInstance()->getFieldConfig('configTabs'),
    		'id_language' => $this->context->language->id,
            'isConfigForm' => true,
            'link_base' => $this->getBaseLink(),
            'current_tab' => $current_tab,
            'image_baseurl' => $this->_path.'views/img/',
        );
        return $helper->generateForm(array($fields_form));	
    }
    public function _checkFormBeforeSubmit()
    {
        $languages = Language::getLanguages(false);
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $ETS_MP_SELLER_GROUP = Tools::getValue('ETS_MP_SELLER_GROUPS');
        if(!$ETS_MP_SELLER_GROUP)
            $this->_errors[] = $this->l('Applicable customer group is required');
        elseif(!Ets_marketplace::validateArray($ETS_MP_SELLER_GROUP,'isInt'))
            $this->_errors[] = $this->l('Applicable customer group is not valid');
        $ETS_MP_SELLER_FEE_TYPE = Tools::getValue('ETS_MP_SELLER_FEE_TYPE');
        if(!in_array($ETS_MP_SELLER_FEE_TYPE,array('no_fee','pay_once','monthly_fee','quarterly_fee','yearly_fee')))
            $this->l('Fee amount is not valid');
        elseif($ETS_MP_SELLER_FEE_TYPE!='no_fee')
        {
            $ETS_MP_SELLER_FEE_AMOUNT = Tools::getValue('ETS_MP_SELLER_FEE_AMOUNT');
            if(trim($ETS_MP_SELLER_FEE_AMOUNT)=='')
                $this->_errors[] = $this->l('Fee amount is required');
            elseif(!Validate::isUnsignedFloat($ETS_MP_SELLER_FEE_AMOUNT))
                $this->_errors[] = $this->l('Fee amount is not valid');
        }
        $ETS_MP_SELLER_PAYMENT_INFORMATION_default = Tools::getValue('ETS_MP_SELLER_PAYMENT_INFORMATION_'.$id_lang_default); 
        if(!$ETS_MP_SELLER_PAYMENT_INFORMATION_default)
            $this->_errors[] = $this->l('Payment information of the marketplace manager is required');
        $ETS_MP_SELLER_ALLOWED_INFORMATION_SUBMISSION = Tools::getValue('ETS_MP_SELLER_ALLOWED_INFORMATION_SUBMISSION');
        if(!$ETS_MP_SELLER_ALLOWED_INFORMATION_SUBMISSION)
            $this->_errors[] = $this->l('Allow seller to submit these information is required');
        elseif(!Ets_marketplace::validateArray($ETS_MP_SELLER_ALLOWED_INFORMATION_SUBMISSION))
            $this->_errors[] = $this->l('Allow seller to submit these information is not valid');
        $ETS_MP_SELLER_CAN_CHANGE_ORDER_STATUS = (int)Tools::getValue('ETS_MP_SELLER_CAN_CHANGE_ORDER_STATUS');
        $ETS_MP_SELLER_ALLOWED_STATUSES = Tools::getValue('ETS_MP_SELLER_ALLOWED_STATUSES');
        if($ETS_MP_SELLER_CAN_CHANGE_ORDER_STATUS)
        {
            if(!$ETS_MP_SELLER_ALLOWED_STATUSES)
                $this->_errors[] = $this->l('Select order status which seller can update is required');
            elseif(!Ets_marketplace::validateArray($ETS_MP_SELLER_ALLOWED_STATUSES))
                $this->_errors[] = $this->l('Select order status which seller can update is not valid');
        }
        $ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT = Tools::getValue('ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT');
        if(!$ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT)
            $this->_errors[] = $this->l('The type of product is required.');
        elseif(!Ets_marketplace::validateArray($ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT))
            $this->_errors[] = $this->l('The type of product is not valid.');
        $ETS_MP_ENABLE_CAPTCHA = (int)Tools::getValue('ETS_MP_ENABLE_CAPTCHA');
        if($ETS_MP_ENABLE_CAPTCHA)
        {
            $ETS_MP_ENABLE_CAPTCHA_FOR = Tools::getValue('ETS_MP_ENABLE_CAPTCHA_FOR');
            if(!$ETS_MP_ENABLE_CAPTCHA_FOR)
                $this->_errors[] = $this->l('Enable captcha for is required');
            elseif(!Ets_marketplace::validateArray($ETS_MP_ENABLE_CAPTCHA_FOR))
                $this->_errors[] = $this->l('Enable captcha for is not valid');
            $ETS_MP_ENABLE_CAPTCHA_TYPE = (string)Tools::getValue('ETS_MP_ENABLE_CAPTCHA_TYPE');
            if($ETS_MP_ENABLE_CAPTCHA_TYPE=='google_v2')
            {
                $ETS_MP_ENABLE_CAPTCHA_SITE_KEY2 = Tools::getValue('ETS_MP_ENABLE_CAPTCHA_SITE_KEY2');
                if(!$ETS_MP_ENABLE_CAPTCHA_SITE_KEY2)
                    $this->_errors[] = $this->l('Site key is required');
                elseif(!Validate::isCleanHtml($ETS_MP_ENABLE_CAPTCHA_SITE_KEY2))
                    $this->_errors[] = $this->l('Site key is not valid');
                $ETS_MP_ENABLE_CAPTCHA_SECRET_KEY2 = Tools::getValue('ETS_MP_ENABLE_CAPTCHA_SECRET_KEY2');
                if(!$ETS_MP_ENABLE_CAPTCHA_SECRET_KEY2)
                    $this->_errors[] = $this->l('Secret key is required');
                elseif(!Validate::isCleanHtml($ETS_MP_ENABLE_CAPTCHA_SECRET_KEY2))
                    $this->_errors[] = $this->l('Secret key is not valid');
            }
            elseif($ETS_MP_ENABLE_CAPTCHA_TYPE=='google_v3')
            {
                $ETS_MP_ENABLE_CAPTCHA_SITE_KEY3 = Tools::getValue('ETS_MP_ENABLE_CAPTCHA_SITE_KEY3');
                if(!$ETS_MP_ENABLE_CAPTCHA_SITE_KEY3)
                    $this->_errors[] = $this->l('Site key is required');
                elseif(!Validate::isCleanHtml($ETS_MP_ENABLE_CAPTCHA_SITE_KEY3))
                    $this->_errors[] = $this->l('Site key is not valid');
                $ETS_MP_ENABLE_CAPTCHA_SECRET_KEY3 = Tools::getValue('ETS_MP_ENABLE_CAPTCHA_SECRET_KEY3');    
                if(!$ETS_MP_ENABLE_CAPTCHA_SECRET_KEY3)
                    $this->_errors[] = $this->l('Secret key is required');
                elseif(!Validate::isCleanHtml($ETS_MP_ENABLE_CAPTCHA_SECRET_KEY3))
                    $this->_errors[] = $this->l('Secret key is not valid');
            }
            else
                $this->_errors[] = $this->l('Captcha is not valid');
        }
        $ETS_MP_ENABLE_MAP = (int)Tools::getValue('ETS_MP_ENABLE_MAP');
        $ETS_MP_SEARCH_ADDRESS_BY_GOOGLE = (int)Tools::getValue('ETS_MP_SEARCH_ADDRESS_BY_GOOGLE');
        $ETS_MP_GOOGLE_MAP_API = Tools::getValue('ETS_MP_GOOGLE_MAP_API');
        if($ETS_MP_ENABLE_MAP && $ETS_MP_SEARCH_ADDRESS_BY_GOOGLE)
        {
            if(!$ETS_MP_GOOGLE_MAP_API)
                $this->_errors[] = $this->l('Google map api is required');
            elseif(!Validate::isCleanHtml($ETS_MP_GOOGLE_MAP_API))
                $this->_errors[] = $this->l('Google map api is not valid');
        }
        $ETS_MP_EDIT_PRODUCT_APPROVE_REQUIRED = (int)Tools::getValue('ETS_MP_EDIT_PRODUCT_APPROVE_REQUIRED');
        $ETS_MP_SELLER_PRODUCT_APPROVE_REQUIRED = (int)Tools::getValue('ETS_MP_SELLER_PRODUCT_APPROVE_REQUIRED');
        $ETS_MP_FIELD_PRODUCT_APPROVE_REQUIRED = Tools::getValue('ETS_MP_FIELD_PRODUCT_APPROVE_REQUIRED',array());
        if($ETS_MP_FIELD_PRODUCT_APPROVE_REQUIRED && !Ets_marketplace::validateArray($ETS_MP_FIELD_PRODUCT_APPROVE_REQUIRED))
            $this->_errors[] = $this->l('"Seller needs to have approval from the administrator to edit these product information fields" are invalid');
        if($ETS_MP_EDIT_PRODUCT_APPROVE_REQUIRED && $ETS_MP_SELLER_PRODUCT_APPROVE_REQUIRED && !$ETS_MP_FIELD_PRODUCT_APPROVE_REQUIRED)
            $this->_errors[] = $this->l('"Seller needs to have approval from the administrator to edit these product information fields" are required');
        if($settings = Ets_mp_defines::getInstance()->getFieldConfig('settings'))
        {
            foreach($settings as $config)
            {
                $name = $config['name'];
                if(isset($config['lang']) && $config['lang'])
                { 
                    if((isset($config['validate']) && $config['validate'] && method_exists('Validate',$config['validate'])))
                    {
                        $validate = $config['validate'];
                        foreach($languages as $lang)
                        {
                            if(($value = trim(Tools::getValue($name.'_'.$lang['id_lang']))) && !Validate::$validate($value))
                                $this->_errors[] =  $config['label'].' '.$this->l('is not valid in ').$lang['iso_code'];
                        }
                        unset($validate);
                    }
                }
                else
                {
                    if((isset($config['validate']) && $config['validate'] && method_exists('Validate',$config['validate'])))
                    {
                        $validate = $config['validate'];
                        $value = trim(Tools::getValue($name)); 
                        if($value && !Validate::$validate($value))
                             $this->_errors[] = $config['label'].' '. $this->l('is not valid');
                        unset($validate);
                    } 
                }
                    
            }
        }
        $ETS_MP_APPLICABLE_CATEGORIES = (string)Tools::getValue('ETS_MP_APPLICABLE_CATEGORIES');
        $ETS_MP_SELLER_CATEGORIES = Tools::getValue('ETS_MP_SELLER_CATEGORIES');
        
        if($ETS_MP_APPLICABLE_CATEGORIES=='specific_product_categories' && (!$ETS_MP_SELLER_CATEGORIES || !is_array($ETS_MP_SELLER_CATEGORIES) || !Ets_marketplace::validateArray($ETS_MP_SELLER_CATEGORIES,'isInt') ))
            $this->_errors[] = $this->l('Categories are required');
        if($ETS_MP_ENABLE_MAP && isset($_FILES['ETS_MP_GOOGLE_MAP_LOGO']['name']) && $_FILES['ETS_MP_GOOGLE_MAP_LOGO']['name'])
        {
            $this->validateFile($_FILES['ETS_MP_GOOGLE_MAP_LOGO']['name'],$_FILES['ETS_MP_GOOGLE_MAP_LOGO']['size'],$this->_errors,array('jpeg','jpg','png','gif'));
        }
        $ETS_MP_DISPLAY_FOLLOWED_SHOP = (int)Tools::getValue('ETS_MP_DISPLAY_FOLLOWED_SHOP');
        $ETS_MP_DISPLAY_NUMBER_SHOP = (int)Tools::getValue('ETS_MP_DISPLAY_NUMBER_SHOP');
        if($ETS_MP_DISPLAY_FOLLOWED_SHOP && !$ETS_MP_DISPLAY_NUMBER_SHOP)
            $this->_errors[] = $this->l('Number of shops to display is required');
        $ETS_MP_DISPLAY_PRODUCT_FOLLOWED_SHOP = (int)Tools::getValue('ETS_MP_DISPLAY_PRODUCT_FOLLOWED_SHOP');
        $ETS_MP_DISPLAY_NUMBER_PRODUCT_FOLLOWED_SHOP = (int)Tools::getValue('ETS_MP_DISPLAY_NUMBER_PRODUCT_FOLLOWED_SHOP');
        if($ETS_MP_DISPLAY_PRODUCT_FOLLOWED_SHOP && !$ETS_MP_DISPLAY_NUMBER_PRODUCT_FOLLOWED_SHOP)
            $this->_errors[] = $this->l('Number of followed products to display on homepage is required');
        $ETS_MP_DISPLAY_PRODUCT_TRENDING_SHOP = (int)Tools::getValue('ETS_MP_DISPLAY_PRODUCT_TRENDING_SHOP');
        if($ETS_MP_DISPLAY_PRODUCT_TRENDING_SHOP)
        {
            $ETS_MP_TRENDING_PERIOD_SHOP = (int)Tools::getValue('ETS_MP_TRENDING_PERIOD_SHOP');
            if(!$ETS_MP_TRENDING_PERIOD_SHOP)
                $this->_errors[] = $this->l('Trending period is required');
            $ETS_MP_DISPLAY_NUMBER_PRODUCT_TRENDING_SHOP = (int)Tools::getValue('ETS_MP_DISPLAY_NUMBER_PRODUCT_TRENDING_SHOP');
            if(!$ETS_MP_DISPLAY_NUMBER_PRODUCT_TRENDING_SHOP)
                $this->_errors[] = $this->l('Number of trending products to display is required');
        }
        $ETS_MP_DISPLAY_TOP_SHOP = (int)Tools::getValue('ETS_MP_DISPLAY_TOP_SHOP');
        $emails = Tools::getValue('ETS_MP_EMAIL_ADMIN_NOTIFICATION');
        if($emails)
        {
            $emails = array_map('trim',explode(',',$emails));
            if($emails)
            {
                foreach($emails as $email)
                {
                    if(!Validate::isEmail($email))
                    {
                        $this->_errors[] = $this->l('Email addresses to receive notifications is not valid');
                        break;
                    }
                }
            }
        }
        $ETS_MP_SELLER_MAXIMUM_UPLOAD = trim(Tools::getValue('ETS_MP_SELLER_MAXIMUM_UPLOAD'));
        if($ETS_MP_SELLER_MAXIMUM_UPLOAD!='' && (int)$ETS_MP_SELLER_MAXIMUM_UPLOAD==0)
            $this->_errors[] = $this->l('Maximum number of uploadable products is not valid');
        if($ETS_MP_DISPLAY_TOP_SHOP && !$this->_errors)
        {
            $ETS_MP_DISPLAY_NUMBER_TOP_SHOP = (int)Tools::getValue('ETS_MP_DISPLAY_NUMBER_TOP_SHOP');
            if(!$ETS_MP_DISPLAY_NUMBER_TOP_SHOP)
                $this->_errors[] = $this->l('Number of shops to display is required');
        }
        $ETS_MP_DISPLAY_OTHER_PRODUCT = (int)Tools::getValue('ETS_MP_DISPLAY_OTHER_PRODUCT');
        if($ETS_MP_DISPLAY_OTHER_PRODUCT && !$this->_errors)
        {
            $ETS_MP_DISPLAY_NUMBER_OTHER_PRODUCT = (int)Tools::getValue('ETS_MP_DISPLAY_NUMBER_OTHER_PRODUCT');
            if(!$ETS_MP_DISPLAY_NUMBER_OTHER_PRODUCT)
                $this->_errors[] = $this->l('Number of other products to display is required');
        }
        if($this->_errors)
            return false;
        else
            return true;
    }
    public function _saveFromSettings()
    {
        $languages = Language::getLanguages(false);
        $id_language_default = Configuration::get('PS_LANG_DEFAULT');
        if($settings = Ets_mp_defines::getInstance()->getFieldConfig('settings'))
        {
            foreach($settings as $config)
            {
                $config_value = Tools::getValue($config['name']);
                if($config['type']=='checkbox' || $config['type']=='categories'|| $config['type']=='tre_categories')
                {
                    if(!is_array($config_value))
                        $config_value = array();
                    Configuration::updateValue($config['name'],$config_value ? implode(',',$config_value) :'' );
                }
                else
                {
                    if(!Validate::isCleanHtml($config_value))
                        $config_value='';
                    if(isset($config['lang']) && $config['lang'])
                    {
                        $values = array();
                        $config_value_lang_default = Tools::getValue($config['name'].'_'.$id_language_default);
                        if(!Validate::isCleanHtml($config_value_lang_default))
                            $config_value_lang_default='';
                        foreach($languages as $language)
                        {
                            $config_value_lang = Tools::getValue($config['name'].'_'.$language['id_lang']);
                            if(!Validate::isCleanHtml($config_value_lang))
                                $config_value_lang='';
                            $values[$language['id_lang']] = $config_value_lang ? $config_value_lang : $config_value_lang_default;
                        }
                        Configuration::updateValue($config['name'],$values,true);
                    }
                    elseif($config['type']=='file')
                    {
                        if(isset($_FILES[$config['name']]['name']) && isset($_FILES[$config['name']]['name']) && isset($_FILES[$config['name']]['tmp_name']) && $_FILES[$config['name']]['tmp_name'])
                        {
                            $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$config['name']]['name'], '.'), 1));
                            $file_name = Tools::passwdGen(12).'.'.$type;
                            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                            if (!$temp_name || !move_uploaded_file($_FILES[$config['name']]['tmp_name'], $temp_name))
            					$this->_errors[] = $this->l('Cannot upload the file');
            				elseif (!ImageManager::resize($temp_name,dirname(__FILE__).'/views/img/'.$file_name, 30,30, $type))
            					$this->_errors[] = $this->l('An error occurred during the image upload process.');
                            else
                            {
                                $file_old = Configuration::get($config['name']);
                                Configuration::updateValue($config['name'],$file_name);
                                if($file_old && file_exists(dirname(__FILE__).'/views/img/'.$file_old))
                                    @unlink(dirname(__FILE__).'/views/img/'.$file_old);
                            }
                        }
                    }    
                    else
                    {
                        Configuration::updateValue($config['name'],$config_value,true);
                    }
                }
                
            }
            $ETS_MP_CONTACT_FIELDS_VALIDATE = Tools::getValue('ETS_MP_CONTACT_FIELDS_VALIDATE',array());
            if(!is_array($ETS_MP_CONTACT_FIELDS_VALIDATE) || !Ets_marketplace::validateArray($ETS_MP_CONTACT_FIELDS_VALIDATE))
                $ETS_MP_CONTACT_FIELDS_VALIDATE=array();
            $ETS_MP_REGISTRATION_FIELDS_VALIDATE = Tools::getValue('ETS_MP_REGISTRATION_FIELDS_VALIDATE',array());
            if(!is_array($ETS_MP_REGISTRATION_FIELDS_VALIDATE) || !Ets_marketplace::validateArray($ETS_MP_REGISTRATION_FIELDS_VALIDATE))
                $ETS_MP_REGISTRATION_FIELDS_VALIDATE = array();
            Configuration::updateValue('ETS_MP_REGISTRATION_FIELDS_VALIDATE',implode(',',array_map('pSQL',$ETS_MP_REGISTRATION_FIELDS_VALIDATE)));
            Configuration::updateValue('ETS_MP_CONTACT_FIELDS_VALIDATE',implode(',',array_map('pSQL',$ETS_MP_CONTACT_FIELDS_VALIDATE)));
            if(!$this->_errors)
                $this->context->cookie->success_message = $this->l('Updated successfully');
        }
    }
    public function _checkPermissionPage($seller=false,$controller='')
    {
        if(!$seller) 
            $seller = $this->_getSeller(true);
        if(!$controller)
            $controller = Tools::getValue('controller');
        if(!Validate::isControllerName($controller))
            $controller='';
        if($seller->id_customer == $this->context->customer->id || $controller=='shop')
        {
            return true;
        }
        else
        {
            $permissions = Ets_mp_seller::getPermistionSellerManager();
            if($permissions)
            {
                $permissions = explode(',',$permissions);
                if(in_array($controller,$permissions) || (in_array('all',$permissions) && !in_array($controller,array('manager','shop','voucher','withdraw'))))
                    return true;
            }
        }
        return false;
    }
    public function _getSeller($active=false)
    {
        return Ets_mp_seller::getCurrentSeller($active);
    }
    public function renderList($listData)
    { 
        if(isset($listData['fields_list']) && $listData['fields_list'])
        {
            foreach($listData['fields_list'] as $key => &$val)
            {
                $value_key = (string)Tools::getValue($key);
                $value_key_max = (string)Tools::getValue($key.'_max');
                $value_key_min = (string)Tools::getValue($key.'_min');
                if(isset($val['filter']) && $val['filter'] && ($val['type']=='int' || $val['type']=='date'))
                {
                    if(Tools::isSubmit('ets_mp_submit_'.$listData['name']))
                    {
                        $val['active']['max'] =  trim($value_key_max);   
                        $val['active']['min'] =  trim($value_key_min); 
                    }
                    else
                    {
                        $val['active']['max']='';
                        $val['active']['min']='';
                    }  
                }  
                elseif(!Tools::isSubmit('del') && Tools::isSubmit('ets_mp_submit_'.$listData['name']))               
                    $val['active'] = trim($value_key);
                else
                    $val['active']='';
            }
        }  
        if(!isset($listData['class']))
            $listData['class']='';  
        $this->context->smarty->assign($listData);
        return $this->display(__FILE__, 'list_helper.tpl');
    }
    public function getFilterParams($field_list,$table='')
    {
        $params = '';        
        if($field_list)
        {
            if(Tools::isSubmit('ets_mp_submit_'.$table))
                $params .='&ets_mp_submit_'.$table.='=1';
            foreach($field_list as $key => $val)
            {
                $value_key = (string)Tools::getValue($key);
                $value_key_max = (string)Tools::getValue($key.'_max');
                $value_key_min = (string)Tools::getValue($key.'_min');
                if($value_key!='')
                {
                    $params .= '&'.$key.'='.urlencode($value_key);
                }
                if($value_key_max!='')
                {
                    $params .= '&'.$key.'_max='.urlencode($value_key_max);
                }
                if($value_key_min!='')
                {
                    $params .= '&'.$key.'_min='.urlencode($value_key_min);
                } 
            }
            unset($val);
        }
        return $params;
    }
    public function validateFile($file_name,$file_size,&$errors,$file_types=array(),$max_file_size= false)
    {
        if($file_name)
        {
            if(!Validate::isFileName(str_replace(array(' ','(',')','!','@','#','+'),'_',$file_name)))
            {
                $errors[] = sprintf($this->l('The file name "%s" is invalid'),$file_name);
            }
            else
            {
                $type = Tools::strtolower(Tools::substr(strrchr($file_name, '.'), 1));
                if(!$file_types)
                    $file_types = $this->file_types;
                if(!in_array($type,$file_types))
                    $errors[] = sprintf($this->l('The file name "%s" is not in the correct format, accepted formats: %s'),$file_name,'.'.trim(implode(', .',$file_types),', .'));
                $max_file_size = $max_file_size ? : Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
                if($file_size > $max_file_size)
                    $errors[] = sprintf($this->l('The file name "%s" is too large. Limit: %s'),$file_name,Tools::ps_round($max_file_size/1048576,2).'Mb');
            }
        }
        
    }
    public function uploadFile($name,&$errors)
    {
        if(!is_dir(_PS_IMG_DIR_.'mp_seller/'))
        {
            @mkdir(_PS_IMG_DIR_.'mp_seller/',0777,true);
            @copy(dirname(__FILE__).'/index.php', _PS_IMG_DIR_.'mp_seller/index.php');
        }
        if(isset($_FILES[$name]['tmp_name']) && isset($_FILES[$name]['name']) && $_FILES[$name]['name'])
        {
            if(!Validate::isFileName(str_replace(array(' ','(',')','!','@','#','+'),'_',$_FILES[$name]['name'])))
                $errors[] = '"'.$_FILES[$name]['name'].'" '.$this->l('file name is not valid');
            else
            {
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$name]['name'], '.'), 1));
                $_FILES[$name]['name'] = Tools::strtolower(Tools::passwdGen(12,'NO_NUMERIC')).'.'.$type;
    			$imagesize = @getimagesize($_FILES[$name]['tmp_name']);
    			if (isset($_FILES[$name]) &&				
    				!empty($_FILES[$name]['tmp_name']) &&
    				!empty($imagesize) &&
    				in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
    			)
    			{
    				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    
                    $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;				
    				if ($_FILES[$name]['size'] > $max_file_size)
    					$errors[] = sprintf($this->l('Image is too large (%s Mb). Maximum allowed: %s Mb'),Tools::ps_round((float)$_FILES[$name]['size']/1048576,2), Tools::ps_round(Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),2));
    				elseif (!$temp_name || !move_uploaded_file($_FILES[$name]['tmp_name'], $temp_name))
    					$errors[] = $this->l('Cannot upload the file');
    				elseif (!ImageManager::resize($temp_name, _PS_IMG_DIR_.'mp_seller/'.$_FILES[$name]['name'], $name=='shop_logo' ? 250 :null, $name=='shop_logo' ? 250 :null, $type))
    					$errors[] = $this->l('An error occurred during the image upload process.');
    				if (isset($temp_name) && file_exists($temp_name))
    					@unlink($temp_name);
                    if(!$errors)
                        return $_FILES[$name]['name'];		
    			}
                else
                {
                    if($name=='shop_logo')
                        $errors[] = $this->l('Logo is not valid');
                    else
                        $errors[] = $this->l('Banner is not valid');
                }
            }
                
        }
        return '';
    }
    public function getBreadCrumb()
    {
        $nodes = array();
        $controller = Tools::getValue('controller');
        if(!Validate::isControllerName($controller))
            $controller='';
        $nodes[] = array(
            'title' => $this->l('Home'),
            'url' => $this->context->link->getPageLink('index', true),
        );
        if($controller=='map')
        {
            $nodes[] = array(
                'title' => $this->l('Store locations'),
                'url' => $this->context->link->getModuleLink($this->name,'map'),
                'last' => true,
            );
        }
        elseif($controller!='shop')
        {
            $nodes[] = array(
                'title' => $this->l('My account'),
                'url' => $this->context->link->getPageLink('my-account'),
            );
            if($controller=='contactseller')
                $nodes[] = array(
                    'title' => $this->l('Contact shop'),
                    'url' => $this->context->link->getModuleLink($this->name,'contactseller'),
                    'last' => $controller=='contactseller' ? true : false,
                );
            else
                $nodes[] = array(
                    'title' => $this->l('My seller account'),
                    'url' => $this->context->link->getModuleLink($this->name,'myseller'),
                    'last' => $controller=='myseller' ? true : false,
                );
        }
        else
        {
            $id_seller= Tools::getValue('id_seller');
            if(!Validate::isUnsignedId($id_seller))
                $id_seller=0;
            $nodes[] = array(
                'title' => $this->l('Shops'),
                'url' => $this->getShopLink(),
                'last' => $id_seller ? false:true,
            );
            if($id_seller)
            {
                $seller = new Ets_mp_seller($id_seller,$this->context->language->id);
                $nodes[] = array(
                    'title' => $seller->shop_name,
                    'url' => $this->getShopLink(array('id_seller'=>$id_seller)),
                    'last' => true,
                );
            }
        }
        if($controller=='dashboard')
        {
            $nodes[] = array(
                'title' => $this->l('Dashboard'),
                'url' => $this->context->link->getModuleLink($this->name,'dashboard'),
                'last' => true,
            );
        }
        if($controller=='orders')
        {
            $nodes[] = array(
                'title' => $this->l('Orders'),
                'url' => $this->context->link->getModuleLink($this->name,'orders'),
                'last' => true,
            );
        }
        if($controller=='carrier')
        {
            $id_carrier = Tools::getValue('id_carrier');
            if(!Validate::isUnsignedId($id_carrier))
                $id_carrier=0;
            $nodes[] = array(
                'title' => $this->l('Carriers'),
                'url' => $this->context->link->getModuleLink($this->name,'carrier'),
                'last' =>  $id_carrier? false :true,
            );
            if($id_carrier)
            {
                $carrier = new Carrier($id_carrier);
                $nodes[] = array(
                    'title' => $carrier->name ? : $this->context->shop->name,
                    'url' => $this->context->link->getModuleLink($this->name,'carrier',array('editmp_carrier'=>1,'id_carrier'=>$id_carrier)),
                    'last' => true,
                );
            }
        }
        if($controller=='products')
        {
            $nodes[] = array(
                'title' => $this->l('Products'),
                'url' => $this->context->link->getModuleLink($this->name,'products',array('list'=>1)),
                'last' => true,
            );
        }
        if($controller=='ratings')
        {
            $nodes[] = array(
                'title' => $this->l('Ratings'),
                'url' => $this->context->link->getModuleLink($this->name,'ratings',array('list'=>1)),
                'last' => true,
            );
        }
        if($controller=='commissions')
        {
            $nodes[] = array(
                'title' => $this->l('Commissions'),
                'url' => $this->context->link->getModuleLink($this->name,'commissions'),
                'last' => true,
            );
        }
        if($controller=='billing')
        {
            $nodes[] = array(
                'title' => $this->l('Membership'),
                'url' => $this->context->link->getModuleLink($this->name,'billing'),
                'last' => true,
            );
        }
        if($controller=='withdraw')
        {
            $nodes[] = array(
                'title' => $this->l('Withdrawals'),
                'url' => $this->context->link->getModuleLink($this->name,'withdraw'),
                'last' => true,
            );
        }
        if($controller=='voucher')
        {
            $nodes[] = array(
                'title' => $this->l('My vouchers'),
                'url' => $this->context->link->getModuleLink($this->name,'voucher'),
                'last' => true,
            );
        }
        if($controller=='attributes')
        {
            $id_attribute_group = Tools::getValue('id_attribute_group');
            if(!Validate::isUnsignedId($id_attribute_group))
                $id_attribute_group=0;
            $nodes[] = array(
                'title' => $this->l('Attributes'),
                'url' => $this->context->link->getModuleLink($this->name,'attributes'),
                'last' => $id_attribute_group ? true:false,
            );
            if($id_attribute_group)
            {
                $attributeGroup = new AttributeGroup($id_attribute_group,$this->context->language->id);
                $nodes[] = array(
                    'title' => $attributeGroup->name,
                    'url' =>Tools::isSubmit('viewGroup') ? $this->context->link->getModuleLink($this->name,'attributes',array('viewGroup'=>1,'id_attribute_group'=>$id_attribute_group)) : $this->context->link->getModuleLink($this->name,'attributes',array('editmp_attribute_group'=>1,'id_attribute_group'=>$id_attribute_group)),
                    'last' => true,
                );
            }
        }
        if($controller=='features')
        {
            $id_feature = Tools::getValue('id_feature');
            if(!Validate::isUnsignedId($id_feature))
                $id_feature =0;
            $nodes[] = array(
                'title' => $this->l('Features'),
                'url' => $this->context->link->getModuleLink($this->name,'features'),
                'last' => $id_feature ? true:false,
            );
            if($id_feature)
            {
                $feature = new Feature($id_feature,$this->context->language->id);
                $nodes[] = array(
                    'title' => $feature->name,
                    'url' =>Tools::isSubmit('viewFeature') ? $this->context->link->getModuleLink($this->name,'features',array('viewFeature'=>1,'id_feature'=>$id_feature)) : $this->context->link->getModuleLink($this->name,'features',array('editmp_feature'=>1,'id_feature'=>$id_feature)),
                    'last' => true,
                );
            }
        }
        if($controller=='discount')
        {
            $id_cart_rule= Tools::getValue('id_cart_rule');
            if(!Validate::isUnsignedId($id_cart_rule))
                $id_cart_rule=0;
            $nodes[] = array(
                'title' => $this->l('Discounts'),
                'url' => $this->context->link->getModuleLink($this->name,'discount'),
                'last' => $id_cart_rule? true:false,
            );
            if($id_cart_rule)
            {
                $cartRule = new CartRule($id_cart_rule,$this->context->language->id);
                $nodes[] = array(
                    'title' => $cartRule->name,
                    'url' => $this->context->link->getModuleLink($this->name,'discount',array('editmp_discount'=>1,'id_cart_rule'=>$cartRule->id)),
                    'last' => true,
                );
            }
        }
        if($controller=='messages')
        {
            $nodes[] = array(
                'title' => $this->l('Messages'),
                'url' => $this->context->link->getModuleLink($this->name,'messages'),
                'last' => true,
            );
        }
        if($controller=='profile')
        {
            $nodes[] = array(
                'title' => $this->l('Profile'),
                'url' => $this->context->link->getModuleLink($this->name,'profile'),
                'last' => true,
            );
        }
        if($controller=='brands')
        {
            $id_manufacturer = Tools::getValue('id_manufacturer');
            if(!Validate::isUnsignedId($id_manufacturer))
                $id_manufacturer=0;
            $nodes[] = array(
                'title' => $this->l('Brands'),
                'url' => $this->context->link->getModuleLink($this->name,'brands',array('list'=>1)),
                'last' => $id_manufacturer ? true:false,
            );
            if($id_manufacturer)
            {
                $manufacturer = new Manufacturer($id_manufacturer);
                $nodes[] = array(
                    'title' => $manufacturer->name,
                    'url' => $this->context->link->getModuleLink($this->name,'brands',array('view'=>1,'id_manufacturer'=>$manufacturer->id)),
                    'last' => true,
                );
            }
        }
        if($controller=='suppliers')
        {
            $nodes[] = array(
                'title' => $this->l('Suppliers'),
                'url' => $this->context->link->getModuleLink($this->name,'suppliers',array('list'=>1)),
                'last' =>  true,
            );
        }
        if($controller=='import')
        {
            $nodes[] = array(
                'title' => $this->l('Products'),
                'url' => $this->context->link->getModuleLink($this->name,'products',array('list'=>1)),
            );
            $nodes[] = array(
                'title' => $this->l('Import products'),
                'url' => $this->context->link->getModuleLink($this->name,'import'),
                'last' => true,
            );
        }
        if($controller=='manager')
        {
            $nodes[] = array(
                'title' => $this->l('Shop managers'),
                'url' => $this->context->link->getModuleLink($this->name,'manager',array('list'=>1)),
                'last' =>  true,
            );
        }
        if($controller=='stock')
        {
            $nodes[] = array(
                'title' => $this->l('Stock'),
                'url' => $this->context->link->getModuleLink($this->name,'stock',array('list'=>1)),
                'last' =>  true,
            );
        }
        if($controller=='vacation')
        {
            $nodes[] = array(
                'title' => $this->l('Vacation mode'),
                'url' => $this->context->link->getModuleLink($this->name,'vacation'),
                'last' =>  true,
            );
        }
        if($this->is17)
            return array('links' => $nodes,'count' => count($nodes));
        return $this->displayBreadcrumb($nodes);
    }
    public function displayBreadcrumb($nodes)
    {
        $this->context->smarty->assign(array('nodes' => $nodes));
        return  $this->display(__FILE__, 'nodes.tpl');
    }
    public static function productsForTemplate($products, Context $context = null)
    {
        if (!$products || !is_array($products))
            return array();
        if (!$context)
            $context = Context::getContext();
        $assembler = new ProductAssembler($context);
        $presenterFactory = new ProductPresenterFactory($context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new PrestaShop\PrestaShop\Core\Product\ProductListingPresenter(
            new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever(
                $context->link
            ),
            $context->link,
            new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
            new PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
            $context->getTranslator()
        );

        $products_for_template = array();

        foreach ($products as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $context->language
            );
        }
        return $products_for_template;
    }
    public function hookActionOrderStatusUpdate($params)
    {
        $newOrderStatus = $params['newOrderStatus'];
        $id_order = $params['id_order'];
        if($commissions = Ets_mp_commission::getCommistionBYIDOrder($id_order))
        {
            if(Configuration::get('ETS_MP_COMMISSION_PENDING_WHEN') && ($status_pedding = explode(',',Configuration::get('ETS_MP_COMMISSION_PENDING_WHEN'))) && in_array($newOrderStatus->id,$status_pedding))
            {
                $status=-1;
            }
            elseif(Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN') && ($status_approved = explode(',',Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'))) && in_array($newOrderStatus->id,$status_approved))
            {
                
                if(!$days = (int)Configuration::get('ETS_MP_VALIATE_COMMISSION_IN_DAYS'))
                    $status=1;
                else
                {
                    $status=-1;
                    $expired_date = date('Y-m-d H:i:s',strtotime("+ $days days"));
                }    
                
            }
            elseif(Configuration::get('ETS_MP_COMMISSION_CANCELED_WHEN') && ($status_canceled = explode(',',Configuration::get('ETS_MP_COMMISSION_CANCELED_WHEN'))) && in_array($newOrderStatus->id,$status_canceled))
            {
                $status=0;
            }
            else
                $status=-1;   
            foreach($commissions as $commission)
            {
                $ets_commission = new Ets_mp_commission($commission['id_seller_commission']);
                $ets_commission->status = $status;
                if(isset($expired_date))
                    $ets_commission->expired_date = $expired_date;
                $ets_commission->update();
            }
        }
    }
    public function hookDisplayShoppingCartFooter($params)
    {
        if(Configuration::get('ETS_MP_ALLOW_VOUCHER_IN_CART'))
        {
            if(($seller= $this->_getSeller(true)) && $seller->id_customer == $this->context->customer->id)
            {
                $currency_default = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
                $commission_total_balance = $seller->getTotalCommission(1) - $seller->getToTalUseCommission(1);
                if($commission_total_balance >0 && (!Configuration::get('ETS_MP_MIN_BALANCE_REQUIRED_FOR_VOUCHER') || $commission_total_balance > Configuration::get('ETS_MP_MIN_BALANCE_REQUIRED_FOR_VOUCHER')) && (!Configuration::get('ETS_MP_MAX_BALANCE_REQUIRED_FOR_VOUCHER') || $commission_total_balance < Configuration::get('ETS_MP_MAX_BALANCE_REQUIRED_FOR_VOUCHER') ) )
                {
                    $this->context->smarty->assign(
                        array(
                            'commission_total_balance' => Tools::displayPrice($commission_total_balance,$currency_default),
                        )
                    );
                    return $this->display(__FILE__, 'cart-message.tpl');
                }
                
            }
        }
    }
    public function hookActionObjectOrderDetailDeleteAfter($params)
    {
        $context = Context::getContext();
        if(Configuration::get('ETS_MP_RECALCULATE_COMMISSION') && isset($context->employee->id) && $context->employee->id)
        {
            $orderDetail = $params['object'];
            if(!Validate::isLoadedObject(new OrderDetail($orderDetail->id)))
                Ets_mp_commission::deleteCommistion($orderDetail->id_order,$orderDetail->product_id,$orderDetail->product_attribute_id);
            else
                Ets_mp_commission::changeCommissionWhenUpdateOrder($orderDetail);
        }
        
    }
    public function hookActionObjectOrderDetailAddAfter($params)
    {
        $context = Context::getContext();
        if(Configuration::get('ETS_MP_RECALCULATE_COMMISSION') && isset($context->employee->id) && $context->employee->id)
        {
            Ets_mp_commission::changeCommissionWhenUpdateOrder($params['object']);
        }
    }
    public function hookActionObjectOrderDetailUpdateAfter($params)
    {
        $context = Context::getContext();
        if(Configuration::get('ETS_MP_RECALCULATE_COMMISSION') && isset($context->employee->id) && $context->employee->id)
        {
            Ets_mp_commission::changeCommissionWhenUpdateOrder($params['object']);
        }
    }
    public function getEmailProductPurchasedTemplateContent($template, $products)
    {
        $content ='';
        if($products)
        {
            foreach($products as $product)
            {
                $product_link = $this->context->link->getProductLink($product['product_id']);
                if($template=='txt')
                    $content .= $product['product_name'].': '.$product_link."\n";
                else
                    $content .= Module::getInstanceByName('ets_marketplace')->displayText(Module::getInstanceByName('ets_marketplace')->displayText($product['product_name'],'a','','',$product_link,'_blank'),'p','');
            }
        }
        return $content;
    }
    public function hookActionValidateOrder($params)
    {
        if (!Configuration::get('ETS_MP_ENABLED') ||  !(isset($params['cart'])) || !(isset($params['order'])) || !$params['cart'] || !($order = $params['order']))
            return;
        if($order->module == $this->name && ($seller_pay = $this->_getSeller()) && $seller_pay->id_customer == $this->context->customer->id)
        {
            $commission_usage = new Ets_mp_commission_usage();
            $commission_usage->amount = Tools::convertPrice($order->total_paid,null,false);
            $commission_usage->id_shop = $this->context->shop->id;
            $commission_usage->id_customer = $seller_pay->id_customer;
            $commission_usage->id_order = $order->id;
            $commission_usage->status = 1;
            $commission_usage->id_currency = $this->context->currency->id;
            $commission_usage->date_add = date('Y-m-d H:i:s');
            $commission_usage->note = $this->l('Paid for order #').$order->id;
            $commission_usage->add();
        }
        $products = $order->getProductsDetail();
        $sellers= array();
        if($products)
        {
            foreach($products as $product)
            {
                if(($id_customer = Ets_mp_seller::getIDCustomerSellerByIdProduct($product['product_id'])))
                {
                    $seller = Ets_mp_seller::_getSellerByIdCustomer($id_customer);
                    if($seller)
                    {
                        if(!in_array($id_customer,$sellers))
                        {
                            Ets_mp_seller::addOrderToSeller($id_customer,$order->id);
                            $sellers[] = $id_customer;
                            if(Configuration::get('ETS_MP_EMAIL_SELLER_PRODUCT_PURCHASED'))
                            {
                                $data = array(
                                    '{seller_name}' => $seller->seller_name,
                                    '{seller_shop}' => $seller->shop_name[$this->context->language->id],
                                    '{product_name}' => $product['product_name'],
                                    '{customer_name}' => $this->context->customer->firstname.' '.$this->context->customer->lastname,
                                    '{order_reference}' => $order->reference,
                                    '{product_detail_txt}' => $this->getEmailProductPurchasedTemplateContent('txt',$products),
                                    '{product_detail_tpl}' => $this->getEmailProductPurchasedTemplateContent('tpl',$products),
                                    '{total_payment}' => Tools::displayPrice(Tools::convertPrice($order->total_products_wt,null,false),new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))),
                                    '{purchased_date}' => $order->date_add,
                                    '{order_status}' => $params['orderStatus']->name,
                                );
                                $subjects = array(
                                    'translation' => $this->l('Your product has been purchased'),
                                    'origin'=> 'Your product has been purchased',
                                    'specific'=>false
                                );
                                Ets_marketplace::sendMail('to_seller_product_purchased',$data,$seller->seller_email,$subjects,$seller->seller_name);
                            }
                        }
                        $commission = new Ets_mp_commission(); 
                        $commission->id_product = (int)$product['product_id'];
                        $commission->id_customer= $id_customer;
                        $commission->id_order = (int)$order->id;
                        $commission->id_product_attribute = (int)$product['product_attribute_id'];
                        $commission->product_name = $product['product_name'];
                        $commission->quantity = (int)$product['product_quantity'];
                        $commission->price = (float)Tools::ps_round(Tools::convertPrice($product['unit_price_tax_excl'],null,false),6);  
                        $commission->price_tax_incl = (float)Tools::ps_round(Tools::convertPrice($product['unit_price_tax_incl'],null,false),6);
                        $commission->total_price = (float)Tools::ps_round(Tools::convertPrice($product['total_price_tax_excl'],null,false),6);
                        $commission->total_price_tax_incl=(float)Tools::ps_round(Tools::convertPrice($product['total_price_tax_incl'],null,false),6);
                        $commission->id_shop = $order->id_shop;
                        $commission->date_add = date('Y-m-d H:i:s'); 
                        $commission->date_upd = date('Y-m-d H:i:s'); 
                        $commistion_rate = (float)$seller->getCommissionRate(false,$commission->id_product);
                        $total_price_tax_excl = $product['total_price_tax_excl'];
                        $total_price_tax_incl = $product['total_price_tax_incl'];
                        if(($rule = Ets_mp_seller::getOrderCartRule($id_customer,$order->id,$product['product_id'])))
                        {
                            $total_price_tax_excl -=$rule['value'];
                            $total_price_tax_incl -=$rule['value'];
                        }
                        if(Configuration::get('ETS_MP_COMMISSION_EXCLUDE_TAX'))
                        {
                            $commission->commission = (float)Tools::ps_round(Tools::convertPrice($total_price_tax_excl,null,false) * $commistion_rate/100,6);
                            $commission->use_tax=0;
                        }
                        else
                        {
                            $commission->commission = (float)Tools::ps_round(Tools::convertPrice($total_price_tax_incl,null,false) * $commistion_rate/100,6);
                            $commission->use_tax=1;
                        }
                        if(Configuration::get('ETS_MP_COMMISSION_PENDING_WHEN') && ($status_pedding = explode(',',Configuration::get('ETS_MP_COMMISSION_PENDING_WHEN'))) && in_array($params['orderStatus']->id,$status_pedding))
                        {
                            $commission->status=-1;
                        }
                        elseif(Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN') && ($status_approved = explode(',',Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'))) && in_array($params['orderStatus']->id,$status_approved))
                        {
                            if(!$days = (int)Configuration::get('ETS_MP_VALIATE_COMMISSION_IN_DAYS'))
                                $commission->status =1;
                            else
                            {
                                $commission->status=-1;
                                $commission->expired_date = date('Y-m-d H:i:s',strtotime("+ $days days"));
                            }    
                        }
                        elseif(Configuration::get('ETS_MP_COMMISSION_CANCELED_WHEN') && ($status_canceled = explode(',',Configuration::get('ETS_MP_COMMISSION_CANCELED_WHEN'))) && in_array($params['orderStatus']->id,$status_canceled))
                        {
                            $commission->status =0;
                        }
                        else
                            $commission->status=-1;
                        $commission->add();
                    }
                }
            }
        }
        if(Configuration::get('ETS_MP_RETURN_SHIPPING') && $order->total_shipping_tax_excl)
        {
            $id_customer_seller = Ets_mp_seller::getIDCustomerSellerByIDOrder($order->id);
            if($id_customer_seller && ($seller = Ets_mp_seller::_getSellerByIdCustomer($id_customer_seller)))
            {
                $commission = new Ets_mp_commission(); 
                $commission->id_product = 0;
                $commission->id_customer= $id_customer_seller;
                $commission->id_order = (int)$order->id;
                $commission->id_product_attribute = 0;
                $commission->product_name = $this->l('Return shipping fee from order #').$order->id;
                $commission->quantity = 1;
                $commission->price = (float)Tools::ps_round(Tools::convertPrice($order->total_shipping_tax_excl,null,false),6);  
                $commission->price_tax_incl = (float)Tools::ps_round(Tools::convertPrice($order->total_shipping_tax_incl,null,false),6);
                $commission->total_price = (float)Tools::ps_round(Tools::convertPrice($order->total_shipping_tax_excl,null,false),6);
                $commission->total_price_tax_incl=(float)Tools::ps_round(Tools::convertPrice($order->total_shipping_tax_incl,null,false),6);
                $commission->id_shop = $order->id_shop;
                $commission->date_add = date('Y-m-d H:i:s'); 
                $commission->date_upd = date('Y-m-d H:i:s'); 
                $commission->commission = (float)Tools::ps_round(Tools::convertPrice($order->total_shipping_tax_incl,null,false),6);
                    $commission->use_tax=1;
                if(Configuration::get('ETS_MP_COMMISSION_PENDING_WHEN') && ($status_pedding = explode(',',Configuration::get('ETS_MP_COMMISSION_PENDING_WHEN'))) && in_array($params['orderStatus']->id,$status_pedding))
                {
                    $commission->status=-1;
                }
                elseif(Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN') && ($status_approved = explode(',',Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'))) && in_array($params['orderStatus']->id,$status_approved))
                {
                    if(!$days = (int)Configuration::get('ETS_MP_VALIATE_COMMISSION_IN_DAYS'))
                        $commission->status =1;
                    else
                    {
                        $commission->status=-1;
                        $commission->expired_date = date('Y-m-d H:i:s',strtotime("+ $days days"));
                    }    
                }
                elseif(Configuration::get('ETS_MP_COMMISSION_CANCELED_WHEN') && ($status_canceled = explode(',',Configuration::get('ETS_MP_COMMISSION_CANCELED_WHEN'))) && in_array($params['orderStatus']->id,$status_canceled))
                {
                    $commission->status =0;
                }
                else
                    $commission->status=-1;
                $commission->add();
            }
        }
    }
    public function hookModuleRoutes()
    {
        $subfix = (int)Configuration::get('ETS_MP_URL_SUBFIX') ? '.html' : '';
        $shopAlias = Configuration::get('ETS_MP_SHOP_ALIAS',$this->context->language->id)?:'shops';
        if(!$shopAlias)
            return array();
        Configuration::deleteByName('PS_ROUTE_etsmpshops');
        Configuration::deleteByName('PS_ROUTE_etsmpshopsseller');
        $routes = array(
            'etsmpshops' => array(
                'controller' => 'shop',
                'rule' => $shopAlias,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ),
            'etsmpshopsseller' => array(
                'controller' => 'shop',
                'rule' => $shopAlias.'/{id_seller}-{url_alias}'.$subfix,
                'keywords' => array(
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*','param' => 'url_alias'),
                    'id_seller' =>    array('regexp' => '[0-9]+', 'param' => 'id_seller'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ),
        );
        return $routes;
    }
    public function getLangLinkFriendly($id_lang = null, Context $context = null, $id_shop = null)
	{
		if (!$context)
			$context = Context::getContext();

		if ((!Configuration::get('PS_REWRITING_SETTINGS') && in_array($id_shop, array($context->shop->id,  null))) || !Language::isMultiLanguageActivated($id_shop) || !(int)Configuration::get('PS_REWRITING_SETTINGS', null, null, $id_shop))
			return '';

		if (!$id_lang)
			$id_lang = $context->language->id;

		return Language::getIsoById($id_lang).'/';
	}
	
	public function getBaseLinkFriendly($id_shop = null, $ssl = null)
	{
		static $force_ssl = null;
		
		if ($ssl === null)
		{
			if ($force_ssl === null)
				$force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
			$ssl = $force_ssl;
		}

		if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null)
			$shop = new Shop($id_shop);
		else
			$shop = Context::getContext()->shop;

		$base = ($ssl ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);

		return $base.$shop->getBaseURI();
	}
    public function getShopLink($params = array())
    {
        $context = Context::getContext();      
        $id_lang =  $context->language->id;
        $subfix = (int)Configuration::get('ETS_MP_URL_SUBFIX') ? '.html' : '';
        $alias = Configuration::get('ETS_MP_SHOP_ALIAS',$this->context->language->id) ?:'shops';
        $friendly = Configuration::get('PS_REWRITING_SETTINGS');        
        if($friendly && $alias)
        {    
            $url = $this->getBaseLinkFriendly(null, null).$this->getLangLinkFriendly($id_lang, null, null).$alias; 
            if(isset($params['id_seller']) && $params['id_seller'])
            {
                
                $seller = new Ets_mp_seller($params['id_seller'],$id_lang);

                $url .= '/'.$seller->id.'-'.Tools::link_rewrite($seller->shop_name).$subfix;
                unset($params['id_seller']);
            }
            if($params)
            {
                $extra='';
                foreach($params as $key=> $param)
                    $extra .='&'.$key.'='.$param;
                $url .= '?'.ltrim($extra,'&');
            }
            return $url;       
        }
        else
            return $this->context->link->getModuleLink($this->name,'shop',$params);
    }
    public function submitReportShop()
    {
        $errors = array();
        if(!$report_title = Tools::getValue('report_title'))
            $errors[] = $this->l('Title is required');
        elseif(!Validate::isCleanHtml($report_title))
            $errors[] = $this->l('Title is not valid');
        elseif(Tools::strlen($report_title) >100)
            $errors[] = $this->l('Title can not be longer than 100 characters');
        if(!$report_content = Tools::getValue('report_content'))
            $errors[] = $this->l('Content is required');
        elseif($report_content && !Validate::isCleanHtml($report_content))
            $errors[] = $this->l('Content is not valid');
        elseif(Tools::strlen($report_content) >300)
            $errors[] = $this->l('Content can not be longer than 300 characters');
        if(!$id_seller_report = (int)Tools::getValue('id_seller_report'))
        {
            $errors[] = $this->l('Shop report is null');
        }
        elseif(($seller = new Ets_mp_seller($id_seller_report)) &&  !Validate::isLoadedObject($seller))
        {
            $errors[] = $this->l('Shop report is not valid');
        }
        elseif(($id_product = Tools::getValue('id_product_report')) && (!Validate::isUnsignedId($id_product) || !Validate::isLoadedObject(new Product($id_product)) || !$seller->checkHasProduct($id_product)))
            $errors[] = $this->l('Product report is not valid');
        elseif(Ets_mp_report::getReport($id_seller_report,Context::getContext()->customer->id,$id_product))
            $errors[] = $this->l('Shop reported','report');
        if(Configuration::get('ETS_MP_ENABLE_CAPTCHA') && Tools::isSubmit('g-recaptcha-response'))
        {
            $g_recaptcha_response = Tools::getValue('g-recaptcha-response');
            if(!$g_recaptcha_response)
            {
                $errors[] = $this->l('reCAPTCHA is invalid');
            }
            else
            {
                $recaptcha = $g_recaptcha_response ? : false;
                if ($recaptcha && Validate::isCleanHtml($recaptcha)) {
                    $response = json_decode(Tools::file_get_contents($this->link_capcha), true);
                    if ($response['success'] == false) {
                        $errors[] = $this->l('reCAPTCHA is invalid');
                    }
                }
                elseif(!Validate::isCleanHtml($recaptcha))
                    $errors[] = $this->l('reCAPTCHA is invalid');
            }

        }
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->displayError($errors),
                    )
                )
            );
        }
        else
        {
            $report = new Ets_mp_report();
            $report->id_seller = (int)$id_seller_report;
            $report->id_customer = Context::getContext()->customer->id;
            $report->id_product = (int)$id_product;
            $report->title = $report_title;
            $report->content = $report_content;
            if($report->add())
            {
                if(Configuration::get('ETS_MP_EMAIL_SELLER_REPORT'))
                {
                    $report_seller = new Ets_mp_seller($report->id_seller);
                    $template_vars = array(
                        '{seller_name}' => $report_seller->seller_name,
                        '{reporter}' => Context::getContext()->customer->firstname.' '.Context::getContext()->customer->lastname,
                        '{shop_seller}' => $report_seller->shop_name[$report_seller->id_language],
                        '{product_name}' => $report->id_product ? (new Product($report->id_product,false,$report_seller->id_language))->name: '',
                        '{link_report}' => $report->id_product ? Context::getContext()->link->getProductLink($report->id_product) : $report_seller->getLink(),
                        '{title}' => $report->title,
                        '{content}' => Tools::nl2br($report->content),
                    );
                    $subjects = array(
                        'translation' =>$this->l('Seller shop was reported as abused') ,
                        'origin'=>'Seller shop was reported as abused',
                        'specific'=>'report'
                    );
                    Ets_marketplace::sendMail($report->id_product ? 'to_seller_when_report_product' : 'to_seller_when_report_shop',$template_vars,$report_seller->seller_email,$subjects);
                }
                if(Configuration::get('ETS_MP_EMAIL_ADMIN_REPORT'))
                {
                    $report_seller = new Ets_mp_seller($report->id_seller,Context::getContext()->language->id);
                    $template_vars = array(
                        '{seller_name}' => $report_seller->seller_name,
                        '{reporter}' => Context::getContext()->customer->firstname.' '.Context::getContext()->customer->lastname,
                        '{shop_seller}' => $report_seller->shop_name,
                        '{product_name}' => $report->id_product ? (new Product($report->id_product,false,Context::getContext()->language->id))->name: '',
                        '{link_report}' => $report->id_product ? Context::getContext()->link->getProductLink($report->id_product) : $report_seller->getLink(),
                        '{title}' => $report->title,
                        '{content}' => Tools::nl2br($report->content),
                    );
                    $subjects = array(
                        'translation' =>$this->l('Seller shop was reported as abused') ,
                        'origin'=>'Seller shop was reported as abused',
                        'specific'=>'report'
                    );
                    Ets_marketplace::sendMail($report->id_product ? 'to_admin_when_report_product' : 'to_admin_when_report_shop',$template_vars,'',$subjects);

                }
                die(
                    json_encode(
                        array(
                            'success' => $this->l('Reported successfully'),
                        )
                    )
                );
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->displayError($this->l('Report failed.')),
                        )
                    )
                );
            }
        }
    }
    public function hookDisplayHeader()
    {
        $controller = Tools::getValue('controller');
        if(!Validate::isControllerName($controller))
            $controller ='';
        $module = Tools::getValue('module');
        if(!Validate::isModuleName($module))
            $module ='';
        if(Tools::isSubmit('submitReportShopSeller'))
        {
            $this->submitReportShop();
        }
        if(Tools::isSubmit('i_have_just_sent_the_fee') && ($seller= $this->_getSeller()))
        {
            $seller->confirmedPayment();
        }
        if($controller=='orderdetail')
            $this->context->controller->addJS($this->_path.'views/js/orderdetail.js'); 
        if($controller=='order')
        {
            $this->context->controller->addJS($this->_path.'views/js/shipping.js');
            $this->context->controller->addCSS($this->_path.'views/css/shipping.css'); 
        }
        if($module==$this->name || $controller=='myaccount')
        {
            $this->context->smarty->assign(
                array(
                    'is17' => $this->is17,
                )
            );
            $this->context->controller->addJqueryPlugin('growl');
            $this->context->controller->addJqueryUI('ui.tooltip');
            $this->context->controller->addJqueryUI('ui.effect');
            $this->context->controller->addJqueryUI('ui.datepicker');
            $this->context->controller->addCSS($this->_path.'views/css/front.css'); 
            if($controller=='carrier')
            {
                $id_carrier = (int)Tools::getValue('id_carrier');
                if(Tools::isSubmit('addnew') || (Tools::isSubmit('editmp_carrier') && Validate::isUnsignedInt($id_carrier) && $id_carrier))
                {
                    $this->context->controller->addJqueryPlugin('smartWizard');
                    $this->context->controller->addJqueryPlugin('typewatch');
                    if($this->is17)
                    {
                        $this->context->controller->registerJavascript('modules-ets_marketplace-carrier','modules/'.$this->name.'/views/js/carrier.js', ['position' => 'bottom', 'priority' => 160]);
                    }
                    else
                       $this->context->controller->addJS($this->_path.'views/js/carrier.js'); 
                }
            }    
            if(!$this->is17)
                $this->context->controller->addCSS($this->_path.'views/css/front16.css');
            $this->context->controller->addCSS($this->_path.'views/css/autosearch.css');
            if($controller=='products')
            {
                $this->context->controller->addJqueryPlugin('fancybox');
                $this->context->controller->addJqueryUI('ui.sortable');
                $this->context->controller->addJqueryUI('ui.widget');
                $this->context->controller->addJqueryPlugin('tagify');                
            }
            if($controller=='dashboard')
            {
                $this->context->controller->addCSS($this->_path.'views/css/daterangepicker.css');
                if($this->is17)
                {
                    $this->context->controller->registerJavascript('modules-ets_marketplace-chart','modules/'.$this->name.'/views/js/Chart.min.js', ['position' => 'bottom', 'priority' => 153]);
                    $this->context->controller->registerJavascript('modules-ets_marketplace-moment','modules/'.$this->name.'/views/js/moment.min.js', ['position' => 'bottom', 'priority' => 154]);
                    $this->context->controller->registerJavascript('modules-ets_marketplace-date','modules/'.$this->name.'/views/js/daterangepicker.js', ['position' => 'bottom', 'priority' => 154]);
                    $this->context->controller->registerJavascript('modules-ets_marketplace-dashboard','modules/'.$this->name.'/views/js/front_dashboard.js', ['position' => 'bottom', 'priority' => 155]);
                }
                else
                {
                    $this->context->controller->addJS($this->_path.'views/js/Chart.min.js');
                    $this->context->controller->addJS($this->_path.'views/js/moment.min.js');
                    $this->context->controller->addJS($this->_path.'views/js/daterangepicker.js');
                    $this->context->controller->addJS($this->_path.'views/js/front_dashboard.js');
                }
                
            }
            if(!$this->is17 && $controller=='shop')
            {
                $this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css', 'all');
            }
            if($controller=='contactseller')
            {
                if($this->is17)
                {
                    $this->context->controller->registerJavascript('modules-ets_marketplace-contact','modules/'.$this->name.'/views/js/contact.js', ['position' => 'bottom', 'priority' => 153]);
                }
                else
                    $this->context->controller->addJS($this->_path.'views/js/contact.js');
            }

            if($this->is17)
            {
                $this->context->controller->registerJavascript('modules-ets_marketplace-auto','modules/'.$this->name.'/views/js/autosearch.js', ['position' => 'bottom', 'priority' => 150]);
                $this->context->controller->registerJavascript('modules-ets_marketplace','modules/'.$this->name.'/views/js/front.js', ['position' => 'bottom', 'priority' => 151]);
                $this->context->controller->registerJavascript('modules-ets_marketplace-multi-upload','modules/'.$this->name.'/views/js/multi_upload.js', ['position' => 'bottom', 'priority' => 151]);
                $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');
            }    
            else
            {
                
                $this->context->controller->addJS($this->_path.'views/js/autosearch.js');
                $this->context->controller->addJS($this->_path.'views/js/front.js');
                $this->context->controller->addJS($this->_path.'views/js/front16.js');
                $this->context->controller->addJS($this->_path.'views/js/multi_upload.js');
                $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');
            }
            $this->context->controller->addCSS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css');
            if($controller=='products')
            {
                if($this->is17)
                    $this->context->controller->registerJavascript('modules-ets_marketplace-product-bulk','modules/'.$this->name.'/views/js/product_bulk.js', ['position' => 'bottom', 'priority' => 160]);
                else
                    $this->context->controller->addJS($this->_path.'views/js/product_bulk.js');
            }
            if($controller=='stock')
            {
                if($this->is17)
                    $this->context->controller->registerJavascript('modules-ets_marketplace-product-stock','modules/'.$this->name.'/views/js/stock.js', ['position' => 'bottom', 'priority' => 160]);
                else
                    $this->context->controller->addJS($this->_path.'views/js/stock.js');
            }  
        }
        $this->context->controller->addCSS($this->_path.'views/css/home.css');
        if($controller=='order')
            $this->context->controller->addCSS($this->_path.'views/css/payment.css');
        if($controller=='index' || $controller=='product' )
        {

            if(!$this->is17)
                $this->context->controller->addCSS(_PS_THEME_DIR_.$this->context->shop->theme_name.'/css/product_list.css','all');
            if($this->is17)
            {
                $this->context->controller->registerJavascript('modules-ets_marketplace-stick','modules/'.$this->name.'/views/js/slick.min.js', ['position' => 'bottom', 'priority' => 150]);
                $this->context->controller->registerJavascript('modules-ets_marketplace-follow','modules/'.$this->name.'/views/js/product_follow.js', ['position' => 'bottom', 'priority' => 150]);
            } 
            else
            {
                $this->context->controller->addJS($this->_path.'views/js/slick.min.js');
                $this->context->controller->addJS($this->_path.'views/js/product_follow.js');
            }
            $this->context->controller->addCSS($this->_path.'views/css/slick.css');
        }
        $this->context->controller->addJqueryPlugin('growl');
        if($this->is17)
            $this->context->controller->registerJavascript('modules-ets_marketplace-product-detail','modules/'.$this->name.'/views/js/report.js', ['position' => 'bottom', 'priority' => 154]);
        else
            $this->context->controller->addJS($this->_path.'views/js/report.js');
        $this->context->controller->addCSS($this->_path.'views/css/report.css');
        if($controller=='cart' || $controller=='order' || $controller=='orderconfirmation')
        {
            if($this->is17)
                $this->context->controller->registerJavascript('modules-ets_marketplace-cart','modules/'.$this->name.'/views/js/cart.js', ['position' => 'bottom', 'priority' => 153]);
            else
                $this->context->controller->addJS($this->_path.'views/js/cart.js');
            
        }
        $this->context->smarty->assign(
            array(
                'colorImageFolder' => $this->getBaseLink().'/img/admin/',
            )
        );
        if($settings = Ets_mp_defines::getInstance()->getFieldConfig('settings'))
        {
            foreach($settings as $setting)
            {
                if(isset($setting['lang']))
                {
                    $text = Configuration::get($setting['name'],$this->context->language->id) ? : (isset($setting['default']) ? $setting['default']:'');
                    $this->context->smarty->assign(
                        array(
                            $setting['name'] => $this->_replaceTag($text),
                        )
                    );
                }
                else
                {
                    if($setting['type']=='switch')
                    {
                        $this->context->smarty->assign(
                            array(
                                $setting['name'] => (int)Configuration::get($setting['name']),
                            )
                        );
                    }
                    else
                    $this->context->smarty->assign(
                        array(
                            $setting['name'] => Configuration::get($setting['name']) ? : (isset($setting['default']) ? $setting['default']:'' ),
                        )
                    );
                }    
            }
        }
        if(Configuration::get('ETS_MP_ENABLE_MAP') && Validate::isModuleName($module) && $module==$this->name && ($controller=='shop' || $controller=='map'))
        {
            $default_country = new Country((int)Tools::getCountry());
            if(Configuration::get('ETS_MP_SEARCH_ADDRESS_BY_GOOGLE') && ($map_key = Configuration::get('ETS_MP_GOOGLE_MAP_API')))
                $key ='key='.$map_key.'&';
            else
                $key='';
            $link_map_google = 'http'.((Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 's' : '').'://maps.googleapis.com/maps/api/js?'.$key.'region='.Tools::substr($default_country->iso_code, 0, 2);
            if($this->is17)
            {
                if($controller=='shop')
                    $this->context->controller->registerJavascript('modules-ets_marketplace-google-map',$link_map_google, ['position' => 'bottom', 'priority' => 150,'server' =>'remote','inline' => true]);
                $this->context->controller->registerJavascript('modules-ets_marketplace-map','modules/'.$this->name.'/views/js/map.js', ['position' => 'bottom', 'priority' => 153]);
            }
            else
            {
                if($controller=='shop')
                {
                    $this->context->controller->addJS($link_map_google);
                    $this->context->controller->addJS($this->_path.'views/js/map.js');
                }              
            }
            $this->context->controller->addCSS($this->_path.'views/css/map.css');
        }
        if($controller=='shop' && ($id_seller = (int)Tools::getValue('id_seller')))
        {
            
            $this->context->smarty->assign(
                array(
                    'ets_logo_shop'=> $this->getBaseLink().'/img/mp_seller/'.(new Ets_mp_seller($id_seller))->shop_logo,
                )
            );
        }
        return $this->display(__FILE__,'header.tpl');
    }
    public function hookDisplayHome()
    {
        if(!Configuration::get('ETS_MP_ENABLED'))
            return '';
        $html= '';
        if(Configuration::get('ETS_MP_DISPLAY_PRODUCT_TRENDING_SHOP') && ($nbProducts = (int)Configuration::get('ETS_MP_DISPLAY_NUMBER_PRODUCT_TRENDING_SHOP')) && ($days = (int)Configuration::get('ETS_MP_TRENDING_PERIOD_SHOP') ))
        {
            $trending_products = $this->getTrendingProducts($nbProducts,$days);
            if(!$trending_products)
                $trending_products = $this->getTrendingProducts($nbProducts);
            $this->context->smarty->assign(
                array(
                    'products' => $trending_products,
                    'position'=>''
                )
            );
            if($this->is17)
                $html .= $this->display(__FILE__,'trending_product.tpl');
            else
                $html .= $this->display(__FILE__,'trending_product16.tpl');
        }
        if($this->context->customer->isLogged())
        {
            if($sellers = Ets_mp_seller::getSellersfollow())
            {
                
                $id_sellers = array();
                foreach($sellers as $seller)
                {
                    $id_sellers[] = $seller['id_customer'];
                }
                if(Configuration::get('ETS_MP_DISPLAY_PRODUCT_FOLLOWED_SHOP') && ($number_product = (int)Configuration::get('ETS_MP_DISPLAY_NUMBER_PRODUCT_FOLLOWED_SHOP')))
                {

                    if($products = Ets_mp_seller::getProductsByIdSellers($id_sellers,$number_product))
                    {
                        $products = Product::getProductsProperties(Context::getContext()->language->id, $products);
                        if(version_compare(_PS_VERSION_, '1.7', '>=')) {
                            $products = Ets_marketplace::productsForTemplate($products);
                        }
                        $this->context->smarty->assign(
                            array(
                                'products' => $products,
                                'position'=>''
                            )
                        );
                        if($this->is17)
                            $html .= $this->display(__FILE__,'product_seller_follow.tpl');
                        else
                            $html .= $this->display(__FILE__,'product_seller_follow16.tpl');
                    }
                }
                if(Configuration::get('ETS_MP_DISPLAY_FOLLOWED_SHOP') && ($number_shop = (int)Configuration::get('ETS_MP_DISPLAY_NUMBER_SHOP')))
                {
                    if($sellers = Ets_mp_seller::getShopsByIDs($id_sellers,$number_shop))
                    {
                        foreach($sellers as &$seller)
                        {
                            $seller['link'] = $this->getShopLink(array('id_seller'=>$seller['id_seller']));
                        }
                    }
                    $this->context->smarty->assign(
                        array(
                            'sellers'=> $sellers,
                            'link_base' => $this->getBaseLink(),
                        )
                    );
                    $html .= $this->display(__FILE__,'seller_follow.tpl');
                }
            }
        }
        // top _shop
        if(Configuration::get('ETS_MP_DISPLAY_TOP_SHOP') && ($numberShop = (int)Configuration::get('ETS_MP_DISPLAY_NUMBER_TOP_SHOP')) && Validate::isInt($numberShop))
        {
            $sellers = Ets_mp_seller::_getSellers(' AND seller_sale.total_sale >0','seller_sale.total_sale DESC',0,$numberShop);
            if($sellers)
            {
                foreach($sellers as &$seller)
                {
                    $seller['link'] = $this->getShopLink(array('id_seller'=>$seller['id_seller']));
                }
            }
            $this->context->smarty->assign(
                array(
                    'sellers'=> $sellers,
                    'link_base' => $this->getBaseLink(),
                )
            );
            $html .= $this->display(__FILE__,'home_top_seller.tpl');
        }
        
        return $html;
    }
    public function hookDisplayOrderDetail($params)
    {        
        if(isset($params['order']) && ($order = $params['order']) && Validate::isLoadedObject($order))
        {

            if(($id_customer = Ets_mp_seller::getIDCustomerSellerByIDOrder($order->id)) && ($seller = Ets_mp_seller::_getSellerByIdCustomer($id_customer,$this->context->language->id)))
            {
                $this->context->smarty->assign(
                    array(
                        'seller' => $seller,
                        'is17' => $this->is17
                    )
                );
                return $this->display(__FILE__,'seller_order_detail.tpl');
            }
        }
        return '';
    }
    public function getTrendingProducts($nbProducts,$day=0)
    {
        $id_ets_css_sub_category = (int)Tools::getValue('id_ets_css_sub_category');
        if (!$products = Ets_mp_product::getTrendingProducts($nbProducts,$day,$id_ets_css_sub_category)) {
            return array();
        }
        if($this->is17)
            return Ets_marketplace::productsForTemplate($products);
        else
            return Product::getProductsProperties($this->context->language->id, $products);
    }
    public function hookDisplayETSMPFooterYourAccount()
    {
        $controller = (string)Tools::getValue('controller');
        $this->context->smarty->assign(
            array(
                'is17' => $this->is17,
                'seller_account' => $controller!='myseller' && Validate::isControllerName($controller) && $controller!='contactseller' && $controller!='registration' ?  $this->context->link->getModuleLink($this->name,'myseller'):'', 
            )
        );
        return $this->display(__FILE__,'footer_my_account.tpl');
    }
    public function hookDisplayFooter()
    {
        if(!Configuration::get('ETS_MP_ENABLED'))
            return '';
        $controller = Tools::getValue('controller');
        if(!Validate::isControllerName($controller))
            $controller='';
        if($controller=='map' && !$this->is17 && Configuration::get('ETS_MP_ENABLE_MAP'))
        {
            $default_country = new Country((int)Tools::getCountry());
            if(($map_key = Configuration::get('ETS_MP_GOOGLE_MAP_API')))
                $key ='key='.$map_key.'&';
            else
                $key='';
            $link_map_google = 'http'.((Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 's' : '').'://maps.googleapis.com/maps/api/js?'.$key.'region='.Tools::substr($default_country->iso_code, 0, 2);
            $this->context->smarty->assign(
                array(
                    'link_map_google' => $link_map_google,
                    'link_map_js' =>$this->_path.'views/js/map.js',
                    'ETS_MP_GOOGLE_MAP_API' => $map_key,
                )
            );
            return $this->display(__FILE__,'footer_map_js.tpl');
        }
        if(Configuration::get('ETS_MP_SELLER_ALLOWED_EMBED_CHAT'))
        {
            if( $controller=='product' && ($id_product = Tools::getValue('id_product')))
            {
                if($id_customer = Ets_mp_seller::getIDCustomerSellerByIdProduct($id_product))
                {
                    $seller = Ets_mp_seller::_getSellerByIdCustomer($id_customer,$this->context->language->id);
                }
            }
            $fc = (string)Tools::getValue('fc');
            $module = (string)Tools::getValue('module');
            if($fc =='module' && Validate::isModuleName($module) && $module== $this->name && $controller=='shop' && ($id_seller = (int)Tools::getValue('id_seller')) && Validate::isUnsignedId($id_seller))
                $seller = new Ets_mp_seller($id_seller);
            if(isset($seller) && $seller &&  $seller->active==1 && $seller->code_chat)
            {
                $this->context->smarty->assign(
                    array(
                        'code_chat' => $seller->code_chat,
                    )
                );
                return $this->display(__FILE__,'footer.tpl');
            }
        }
        if($controller && ($products = $this->context->cart->getProducts()) && ($controller=='cart' || $controller=='order'))
        {
            $sellers = array();
            foreach($products as $product)
            {
                if(!isset($sellers[$product['id_product']]))
                {
                    if(($id_customer = Ets_mp_seller::getIDCustomerSellerByIdProduct($product['id_product'])) && ($seller= Ets_mp_seller::_getSellerByIdCustomer($id_customer,$this->context->language->id)) && $seller->active!=0 )
                    {
                        $this->context->smarty->assign(
                            array(
                                'link_shop_seller' => $this->getShopLink(array('id_seller'=>$seller->id)),
                                'shop_name' => $seller->shop_name,
                                'link_contact_form' => $this->context->link->getModuleLink($this->name,'contactseller',array('id_product'=>$product['id_product'])),
                            )
                        );
                        $sellers[$product['id_product']] = $this->displayTpl('product/cart_detail.tpl');
                        
                    }
                }
            }
            if($sellers)
            {
                $this->context->smarty->assign(
                    array(
                        'sellers' => $sellers,
                    )
                );
                return $this->display(__FILE__,'sellers_cart.tpl');
            }
        }
        return '';
    }
    public function hookDisplayPDFInvoice($params)
    {
        if(($object = $params['object']) && isset($object->id_order) && $object->id_order)
        {
            if($id_customer = Ets_mp_seller::getIDCustomerSellerByIDOrder($object->id_order))
            {
                if($seller = Ets_mp_seller::_getSellerByIdCustomer($id_customer,$this->context->language->id))
                {
                    $this->context->smarty->assign(
                        array(
                            'order_seller' => $seller,
                        )
                    );
                    return $this->display(__FILE__,'seller_order_invoice.tpl');                
                }
            }
        }
    }
    public function hookDisplayMyAccountBlock()
    {
        if(!Configuration::get('ETS_MP_ENABLED'))
            return '';
        $seller = $this->_getSeller();
        if(!$seller && !Ets_mp_seller::checkGroupCustomer() && $this->context->customer->isLogged())
            return '';
        $this->context->smarty->assign(
            array(
                'is17' => $this->is17,
                'seller' => $seller,
                'registration' => Ets_mp_registration::_getRegistration(),
                'link' => $this->context->link,
                'require_registration' => (int)Configuration::get('ETS_MP_REQUIRE_REGISTRATION'),
            )
        );
        return $this->display(__FILE__,'my_account.tpl');
    }
    public function hookDisplayCustomerAccount()
    {
        if(!Configuration::get('ETS_MP_ENABLED'))
            return '';
        $seller = $this->_getSeller();
        if(!$seller && !Ets_mp_seller::checkGroupCustomer())
            return '';
        $this->context->smarty->assign(
            array(
                'is17' => $this->is17,
                'seller' => $seller,
                'registration' => Ets_mp_registration::_getRegistration(),
                'link' => $this->context->link,
                'require_registration' => (int)Configuration::get('ETS_MP_REQUIRE_REGISTRATION'),
            )
        );
        return $this->display(__FILE__,'customer_account.tpl');
    }
    public function hookPaymentOptions($params)
    {
        if(!Configuration::get('ETS_MP_ENABLED') || !Configuration::get('ETS_MP_ALLOW_BALANCE_TO_PAY'))
            return '';
        if(($seller= $this->_getSeller(true)) && $seller->id_customer == $this->context->customer->id)
        {
            $commission_total_balance = $seller->getTotalCommission(1) - $seller->getToTalUseCommission(1);
            $min_order_pay = (float)Configuration::get('ETS_MP_MIN_BALANCE_REQUIRED_FOR_ORDER');
            $max_order_pay = (float)Configuration::get('ETS_MP_MAX_BALANCE_REQUIRED_FOR_ORDER');
            $cart = $params['cart'];
            $cart_total = $cart->getOrderTotal(true, Cart::BOTH);
            $cart_total = Tools::convertPrice($cart_total, null, false);
            if($commission_total_balance >0 && $cart_total >0 && $cart_total <= $commission_total_balance && (!$min_order_pay || $min_order_pay <= $cart_total) && (!$max_order_pay || $max_order_pay >=$cart_total))
            {
                $this->context->smarty->assign(
                    array(
                        'commission_total_balance' => Tools::displayPrice(Tools::convertPrice($commission_total_balance)),
                    )
                );
                $newOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
                $newOption->setModuleName($this->name)
                    ->setCallToActionText($this->l('Pay by Commission'))
                    ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
                    ->setAdditionalInformation($this->fetch('module:ets_marketplace/views/templates/hook/payment_info.tpl'));
                $payment_options = array(
                    $newOption,
                );
                return $payment_options;

            }
        }
    }
    public function hookPayment($params)
	{
		if (!$this->active && $this->is17)
			return;
        if(!Configuration::get('ETS_MP_ENABLED') || !Configuration::get('ETS_MP_ALLOW_BALANCE_TO_PAY'))
            return '';
		if(($seller= $this->_getSeller(true)) && $seller->id_customer == $this->context->customer->id)
        {
            $commission_total_balance = $seller->getTotalCommission(1) - $seller->getToTalUseCommission(1);
            $min_order_pay = (float)Configuration::get('ETS_MP_MIN_BALANCE_REQUIRED_FOR_ORDER');
            $max_order_pay = (float)Configuration::get('ETS_MP_MAX_BALANCE_REQUIRED_FOR_ORDER');
            $cart = $params['cart'];
            $cart_total = $cart->getOrderTotal(true, Cart::BOTH);
            $cart_total = Tools::convertPrice($cart_total, null, false);
            if($commission_total_balance >0 && $cart_total >0 && $cart_total <= $commission_total_balance && (!$min_order_pay || $min_order_pay <= $cart_total) && (!$max_order_pay || $max_order_pay >=$cart_total))
            {
                
                $this->context->smarty->assign(
                    array(
                        'commission_total_balance' => Tools::displayPrice(Tools::convertPrice($commission_total_balance)),
                    )
                );
                return $this->display(__FILE__, 'payment.tpl');
            }
        }
		
	}
    public function hookPaymentReturn($params)
	{
		if (!$this->active || $this->is17)
			return;
		return $this->display(__FILE__, 'payment_return.tpl');
	}
    public function hookDisplayMPLeftContent()
    {
        if(!Configuration::get('ETS_MP_ENABLED'))
            Tools::redirect($this->context->link->getPageLink('my-account'));
        if(Configuration::get('ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT'))
            $product_types = explode(',',Configuration::get('ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT'));
        else
            $product_types = array();
        $tabs = array(
            'dashboard' => array(
                'page' => 'dashboard',
                'name' => $this->l('Dashboard')
            ),
            'orders' => array(
                'page' => 'orders',
                'name' => $this->l('Orders'),
                'link' => $this->context->link->getModuleLink($this->name,'orders',array('list'=>1))
            ),
            'products' => array(
                'page' => 'products',
                'name' => $this->l('Products'),
                'link' => $this->context->link->getModuleLink($this->name,'products',array('list'=>true)),
            ),
            'stock' => array(
                'page' => 'stock',
                'name' => $this->l('Stock'),
                'link' => $this->context->link->getModuleLink($this->name,'stock',array('list'=>true)),
            ),
            'ratings' => array(
                'page' => 'ratings',
                'name' => $this->l('Ratings'),
            ),
            'messages'=>array(
                'page' => 'messages',
                'name' => $this->l('Messages')
            ),
            'commissions' => array(
                'page' => 'commissions',
                'name' => $this->l('Commissions'),
                'link' => $this->context->link->getModuleLink($this->name,'commissions',array('list'=>true)),
            ),
            'attributes'=>array(
                'page' => 'attributes',
                'name' => in_array('standard_product',$product_types) && $this->_use_attribute && $this->_use_feature ?  $this->l('Attributes and features') : ($this->_use_feature ? $this->l('Features') : $this->l('Attributes') ),
                'link' => in_array('standard_product',$product_types) && $this->_use_attribute ? $this->context->link->getModuleLink($this->name,'attributes') : $this->context->link->getModuleLink($this->name,'features'),
            ),
            'discount'=>array(
                'page' => 'discount',
                'name' => $this->l('Discounts'),
                'link' => $this->context->link->getModuleLink($this->name,'discount',array('list'=>true)),
            ),
            'carrier'=>array(
                'page' => 'carrier',
                'name' => $this->l('Carriers'),
                'link' => $this->context->link->getModuleLink($this->name,'carrier',array('list'=>true)),
            ),
            'brands' =>array(
                'page'=> 'brands',
                'name' => $this->l('Brands','myseller'),
                'link' => $this->context->link->getModuleLink($this->name,'brands',array('list'=>true))
            ),
            'suppliers' =>array(
                'page'=> 'suppliers',
                'name' => $this->l('Suppliers'),
                'link' => $this->context->link->getModuleLink($this->name,'suppliers',array('list'=>true))
            ),
            'billing' =>array(
                'page' => 'billing',
                'name' => $this->l('Membership'),
                'link' => $this->context->link->getModuleLink($this->name,'billing',array('list'=>true)),
            ),
            'withdraw'=>array(
                'page' => 'withdraw',
                'name' => $this->l('Withdrawals'),
                'link' => $this->context->link->getModuleLink($this->name,'withdraw'),
            ),
            'voucher'=>array(
                'page' => 'voucher',
                'name' => $this->l('My vouchers'),
                'link' => $this->context->link->getModuleLink($this->name,'voucher'),
            ),
            'profile' => array(
                'page' => 'profile',
                'name' => $this->l('Profile')
            ),
            'vacation' => array(
                'page' => 'vacation',
                'name' => $this->l('Vacation mode')
            ),
            'manager' => array(
                'page' =>'manager',
                'name'=> $this->l('Shop managers')
            ),
        );
        if(!Configuration::get('ETS_MP_SELLER_CAN_CREATE_VOUCHER'))
            unset($tabs['discount']);
        if(!Configuration::get('ETS_MP_SELLER_ALLOWED_IMPORT_EXPORT_PRODUCTS'))
            unset($tabs['import']);
        if(!(in_array('standard_product',$product_types) && $this->_use_attribute) && !$this->_use_feature)
            unset($tabs['attributes']);
        if(!Configuration::get('ETS_MP_SELLER_CREATE_BRAND') && !Configuration::get('ETS_MP_SELLER_USER_GLOBAL_BRAND'))
            unset($tabs['brands']);
        if(!Configuration::get('ETS_MP_SELLER_CREATE_SUPPLIER') && !Configuration::get('ETS_MP_SELLER_USER_GLOBAL_SUPPLIER'))
        {
            unset($tabs['suppliers']);
        }
        if(!Configuration::get('ETS_MP_SELLER_CREATE_SHIPPING') && !Configuration::get('ETS_MP_SELLER_USER_GLOBAL_SHIPPING'))
            unset($tabs['carrier']);
        if(!Configuration::get('ETS_MP_ALLOW_CONVERT_TO_VOUCHER'))
            unset($tabs['voucher']);
        if(!Configuration::get('ETS_MP_ALLOW_WITHDRAW'))
            unset($tabs['withdraw']);
        if(!Module::isEnabled('productcomments') && !Module::isEnabled('ets_reviews'))
            unset($tabs['ratings']);
        if(!Configuration::get('ETS_MP_ENABLE_CONTACT_SHOP'))
            unset($tabs['messages']);
        if(!Configuration::get('ETS_MP_VACATION_MODE_FOR_SELLER'))
            unset($tabs['vacation']);
        if($seller = $this->_getSeller())
        {
            $tabs['shop'] = array(
                'page' => 'shop',
                'name' => $this->l('My shop','myseller'),
                'link'=> $this->getShopLink(array('id_seller'=>$seller->id)),
                'new_tab' => true,
            );
            $this->context->smarty->assign(
                array(
                    'total_message' => $this->_getOrderMessages(' AND (`read`!=1 OR `read` is NULL) AND id_employee=0 AND id_seller=0',false,false,false,true),
                )
            );
            $day_before_expired = (int)Configuration::get('ETS_MP_MESSAGE_EXPIRE_BEFORE_DAY');
            $date_expired = date('Y-m-d H:i:s',strtotime("+ $day_before_expired days"));
            if($seller && $seller->date_to!='' && $seller->date_to!='0000-00-00 00:00:00' && strtotime($seller->date_to)< strtotime($date_expired))
            {
                $going_to_be_expired = true;
            }
            else
                $going_to_be_expired = false;
            $this->context->smarty->assign(
                array(
                    'going_to_be_expired' =>$going_to_be_expired,
                    'seller' => $seller,
                    'isManager' => $this->context->customer->id!= $seller->id_customer,
                    'seller_billing' => $seller->id_billing ? (new Ets_mp_billing($seller->id_billing)) : false, 
                )
            );
            
        }
        if($tabs)
        {
            foreach($tabs as $key=> $tab)
            {
                if(!$this->_checkPermissionPage($seller,$tab['page']))
                    unset($tabs[$key]);
            }
        }
        $this->context->smarty->assign(
            array(
                'tabs' => $tabs,
                'controller'=> ($controller = Tools::getValue('controller')) && Validate::isControllerName($controller) &&  $controller!='features' ? $controller : 'attributes',
            )
        );
        return $this->display(__FILE__,'left_content.tpl');
    }
    public function displayProductCategoryTre($blockCategoryTree,$selected_categories=array(),$name='',$disabled_categories=array(),$id_category_default=0,$backend=false,$displayInput=true)
    {
        $this->context->smarty->assign(
            array(
                'blockCategoryTree'=> $blockCategoryTree,
                'branche_tpl_path_input'=> _PS_MODULE_DIR_.$this->name.'/views/templates/hook/category-tree.tpl',
                'selected_categories'=>$selected_categories,
                'disabled_categories' => $disabled_categories,
                'id_category_default' => $id_category_default,
                'name'=>$name ? $name :'id_categories',
                'backend' => $backend,
                'displayInput' => $displayInput,
            )
        );
        return $this->display(__FILE__, 'categories.tpl');
    }
    public function displayFormCategoryCommissionRate($id_seller=0,$id_group=0)
    {
        $controller = Tools::getValue('controller');
        $rate_categories = Tools::getValue('rate_category');
        $blockCategoryTree = Ets_mp_defines::getRateCategoriesTree(0,$id_seller,$id_group,self::validateArray($rate_categories) ? $rate_categories : array());
        $rate_categories = Configuration::get('ETS_MP_APPLICABLE_CATEGORIES')=='specific_product_categories' && Configuration::get('ETS_MP_SELLER_CATEGORIES') ? explode(',',Configuration::get('ETS_MP_SELLER_CATEGORIES')):array();
        if($controller=='AdminMarketPlaceShopGroups')
        {
            if($id_group)
                $group = new Ets_mp_seller_group($id_group);
            else
                $group = new Ets_mp_seller_group();
            if($group->applicable_product_categories=='default')
                $rate_group_categories = $rate_categories;
            elseif($group->applicable_product_categories=='all_product_categories')
                $rate_group_categories = array();
            elseif($group->id)
                $rate_group_categories = explode(',',$group->list_categories);
            else
                $rate_group_categories = $rate_categories;
        }
        if($controller=='AdminMarketPlaceSellers')
        {
            if($id_seller)
                $seller = new Ets_mp_seller($id_seller);
            else
                $seller = new Ets_mp_seller();
            if($seller->id_group)
                $group = new Ets_mp_seller_group($id_group);
            else
                $group = new Ets_mp_seller_group();
            if($group->applicable_product_categories=='default')
                $rate_group_categories = $rate_categories;
            elseif($group->applicable_product_categories=='all_product_categories')
                $rate_group_categories = array();
            elseif($group->id)
                $rate_group_categories = explode(',',$group->list_categories);
            else
                $rate_group_categories = $rate_categories;
            $rate_seller_groups=array();
            $rate_seller_groups[0] = $rate_categories ?  implode(',',$rate_categories):false;
            $groups = Ets_mp_seller_group::getGroups();
            if($groups)
            {
                foreach($groups as $seller_group)
                {
                    if($seller_group['applicable_product_categories']=='default')
                        $rate_seller_groups[$seller_group['id_group']] = $rate_categories ? implode(',',$rate_categories):false;
                    elseif($group->applicable_product_categories=='all_product_categories')
                        $rate_seller_groups[$seller_group['id_group']] = false;
                    else
                        $rate_seller_groups[$seller_group['id_group']] = $seller_group['list_categories'];
                }
            }
        }
        $this->context->smarty->assign(
            array(
                'blockCategoryTree'=> $blockCategoryTree,
                'branche_tpl_path_input'=> _PS_MODULE_DIR_.$this->name.'/views/templates/hook/rate-category-tree.tpl',
                'rate_categories' =>isset($rate_group_categories) ? $rate_group_categories:  $rate_categories,
                'default_rate_categories' => $rate_categories ? implode(',',$rate_categories) : false,
                'rate_seller_groups' => isset($rate_seller_groups) ? $rate_seller_groups : array(),
            )
        );
        return $this->display(__FILE__, 'rate_categories.tpl');
    }
    public function displayProductFeatures($id_product)
    {
        $seller = $this->_getSeller();
        if($id_product)
        {
            $product_features = Ets_mp_product::getFeatureProducts($id_product);
        }
        else
            $product_features = array();
        $features= $seller->getFeatures('',false,false);
        $features_values = $seller->getFeatureValues('',false,false);
        $this->context->smarty->assign(
            array(
                'product_features' => $product_features,
                'features' =>$features ,
                'features_values' => $features_values,
            )
        );
        if($features || $product_features)
            return $this->display(__FILE__,'product/features.tpl');
        else
            return false;
        
    }
    public function getBaseLink()
    {
        $url =(Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$this->context->shop->domain.$this->context->shop->getBaseURI();
        return trim($url,'/');
    }
    public function getFeeIncludeTax($fee,$seller=null)
    {
        if(!$seller)
        {
            $id_customer = $this->context->customer->id;
            $seller = new Ets_mp_seller();
        }
        else
            $id_customer = $seller->id_customer;
        if($id_tax_group = (int)$seller->getFeeTax())
        {
            if($id_address = Address::getFirstCustomerAddressId($id_customer))
                $address = new Address($id_address);
            else
                $address = new Address();
            $address = Address::initialize($address->id,true);
            $tax_manager = TaxManagerFactory::getManager($address, $id_tax_group);
            $product_tax_calculator = $tax_manager->getTaxCalculator();
            $feeTax = $product_tax_calculator->addTaxes($fee);
            return $feeTax;
        }
        return $fee;
    }
    public function getTaxValue($id_tax_group)
    {
        if($id_tax_group)
        {
            $price = 10;
            $context = $this->context;
            if (is_object($context->cart) && $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
                $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                $address = new Address($id_address);
            } else {
                $address = new Address();
            }
            $address = Address::initialize($address->id,true);
            $tax_manager = TaxManagerFactory::getManager($address, $id_tax_group);
            $product_tax_calculator = $tax_manager->getTaxCalculator();
            $priceTax = $product_tax_calculator->addTaxes($price);
            if($priceTax >  $price)
                return ($priceTax-$price)/$price;
            else
                return 0;
        }
        return 0;
    }
    public function displayOrderState($id_order_state)
    {
        if($id_order_state && ($orderState = new OrderState($id_order_state,$this->context->language->id)) && Validate::isLoadedObject($orderState))
        {
            $this->context->smarty->assign(
                array(
                    'orderState' => $orderState,
                )
            );
            return $this->display(__FILE__,'order_state.tpl');
        }
        return '--';
    }
    public function checkListProductSeller($productList)
    {
        if($productList)
        {
            foreach($productList as $product)
            {
                if(Ets_mp_seller::getIDCustomerSellerByIdProduct($product['id_product']))
                    return true;
            }
        }
        return false;
    }
    public function getListProductSeller($productList)
    {
        $sellerProducts = array();
        if($productList)
        {
            foreach($productList as $product)
            {
                $id_customer = Ets_mp_seller::getIDCustomerSellerByIdProduct($product['id_product']);
                if(!isset($sellerProducts[$id_customer]))
                    $sellerProducts[$id_customer] = array();
                $sellerProducts[$id_customer][]=$product;
            }
        }
        return $sellerProducts;
    }
    public function generatePromoCode($prefix = null)
    {
        if ($prefix) {
            $code = $prefix . Tools::passwdGen(5);
            if (CartRule::getCartsRuleByCode($code, $this->context->language->id)) {
                $code = self::generatePromoCode($prefix);
            }
        } else {
            $code = Tools::passwdGen(8);
            if (CartRule::getCartsRuleByCode($code, $this->context->language->id)) {
                $code = self::generatePromoCode(null);
            }
        }
        return Tools::strtoupper($code);
    }
    public static function sendMail($template,$template_vars,$emails='',$title='',$name=null,$file_attachment=null,$id_lang=null){
        if(!$emails)
        {
            $isAdmin= true;
            $emails= Configuration::get('ETS_MP_EMAIL_ADMIN_NOTIFICATION')?:Configuration::get('PS_SHOP_EMAIL');
            if($emails && Tools::strpos($emails,',')!==false)
                $emails= array_map('trim',explode(',',$emails));
        }
        else
            $isAdmin= false;
        if(is_array($emails))
        {
            foreach($emails as $key=>$email)
            {
                if(!Validate::isEmail($email))
                    unset($emails[$key]);
            }
        }elseif(!Validate::isEmail($emails))
            return '';
        if($emails)
        {
            if(!is_array($emails))
            {
                if($isAdmin)
                    $to = array('employee'=>$emails);
                else
                    $to = array('customer' => $emails);
                return Ets_mp_email::Send(
        			$id_lang,
        			$template,
        			$title,
        			$template_vars,
        			$to,
        			$name,
        			null,
        			null,
        			$file_attachment,
        			null,
        			$template=='order_merchant_comment' ? _PS_MAIL_DIR_ : dirname(__FILE__).'/mails/',
        			null,
        			Context::getContext()->shop->id
        		);
            }
            else
            {
                foreach($emails as $email)
                {
                    if($isAdmin)
                        $to = array('employee'=>$email);
                    else
                        $to = array('customer' => $email);
                    Ets_mp_email::Send(
            			$id_lang,
            			$template,
            			$title,
            			$template_vars,
            			$to,
            			$name,
            			null,
            			null,
            			$file_attachment,
            			null,
            			$template=='order_merchant_comment' ? _PS_MAIL_DIR_ : dirname(__FILE__).'/mails/',
            			null,
            			Context::getContext()->shop->id
            		);
                }
                return true;
            }
            
        }
        return false;
    }
    public function _replaceTag($text){
        $search = array(
                '[fee_amount]',
                '[payment_information_manager]',
                '[seller_email]',
                '[shop_phone]',
                '[remaining_day]',
                '[disabled_day]',
                '[shop_id]',
                '[shop_name]',
                '[seller_name]',
                '[shop_declined_reason]',
                '[store_email]',
                '[manager_email]',
                '[manager_phone]'
        );
        if($seller= $this->_getSeller())
        {
            $replace = array(
                Tools::displayPrice($this->getFeeIncludeTax((float)$seller->getFeeAmount(),$seller),new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))).' ('.$this->l('Tax incl').')',
                Configuration::get('ETS_MP_SELLER_PAYMENT_INFORMATION',$this->context->language->id),
                $seller->seller_email,
                $seller->shop_phone,
                Ceil((strtotime($seller->date_to.' 23:59:59')-strtotime(date('Y-m-d H:i:s')))/86400),
                date('Y-m-d'),
                $seller->id,
                $seller->shop_name[$this->context->language->id],
                $seller->seller_name,
                $seller->reason,
                Configuration::get('ETS_MP_EMAIL_ADMIN_NOTIFICATION')?:Configuration::get('PS_SHOP_EMAIL'),
                Configuration::get('ETS_MP_EMAIL_ADMIN_NOTIFICATION')?:Configuration::get('PS_SHOP_EMAIL'),
                Configuration::get('PS_SHOP_PHONE'),
            );
            return Tools::nl2br(str_replace($search,$replace,$text));
        }elseif($seller=Ets_mp_registration::_getRegistration())
        {
           $replace = array(
                Tools::displayPrice($this->getFeeIncludeTax((float)Configuration::get('ETS_MP_SELLER_FEE_AMOUNT')),new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))).' ('.$this->l('Tax incl').')',
                Configuration::get('ETS_MP_SELLER_PAYMENT_INFORMATION',$this->context->language->id),
                $seller->seller_email,
                $seller->shop_phone,
                date('Y-m-d'),
                date('Y-m-d'),
                $seller->id,
                $seller->shop_name,
                $seller->seller_name,
                $seller->reason,
                Configuration::get('ETS_MP_EMAIL_ADMIN_NOTIFICATION')?:Configuration::get('PS_SHOP_EMAIL'),
                Configuration::get('ETS_MP_EMAIL_ADMIN_NOTIFICATION')?:Configuration::get('PS_SHOP_EMAIL'),
                Configuration::get('PS_SHOP_PHONE'),
            );
            return Tools::nl2br(str_replace($search,$replace,$text)); 
        }
        return Tools::nl2br($text) ;
    }
    public function _postPDFProcess()
    {
        $seller = $this->_getSeller();
        $secure_key= Tools::getValue('secure_key');
        if($secure_key && !Validate::isMd5($secure_key))
            $secure_key='';
        if (!$this->context->customer->isLogged() && !$secure_key) {
            Tools::redirect('index.php?controller=authentication&back=pdf-invoice');
        }
        if (!(int) Configuration::get('PS_INVOICE')) {
            die($this->l('Membership is disabled in this shop.'));
        }
        $id_order = (int) Tools::getValue('id_order');
        if (Validate::isUnsignedId($id_order)) {
            $order = new Order((int) $id_order);
        }
        if (!isset($order) || !Validate::isLoadedObject($order)) {
            die($this->l('The invoice was not found.'));
        }
        if ((isset($this->context->customer->id) && $order->id_customer != $this->context->customer->id) || (Tools::isSubmit('secure_key') && $order->secure_key != $secure_key)) {
            if(!$seller || Ets_mp_seller::getIDByIDOrder($order->id)!= $seller->id)
                die($this->l('The invoice was not found.'));
        }
        if (!OrderState::invoiceAvailable($order->getCurrentState()) && !$order->invoice_number) {
            die($this->l('No invoice is available.'));
        }
        return $order;
    }
    public function _runCronJob()
    {
        $commissions_expired = Ets_mp_commission::getCommistionexpired();
        $ok=false;
        if($commissions_expired)
        {
            foreach($commissions_expired as $commission)
            {
                $ets_commission = new Ets_mp_commission($commission['id_seller_commission']);
                $ets_commission->status=1;
                $ets_commission->expired_date='0000-00-00 00:00:00';
                $ets_commission->update();
            }
            $ok= true;
            if(Configuration::getGlobalValue('ETS_MP_SAVE_CRONJOB_LOG'))
                file_put_contents(_PS_ETS_MARKETPLACE_LOG_DIR_.'ets_merketplace.log',Tools::displayDate(date('Y-m-d H:i:s'),Configuration::get('PS_LANG_DEFAULT'),true).': '.$this->l('Approved').' '.Count($commissions_expired).' '.$this->l('commission')."\n",FILE_APPEND);
        }
        $sellers_going_to_be_expired = Ets_mp_seller::getSellersGoingToBeExpired();
        if($sellers_going_to_be_expired)
        {
            foreach($sellers_going_to_be_expired as $seller)
            {
                $seller_class = new Ets_mp_seller($seller['id_seller'],$this->context->language->id);
                $seller_class->mail_going_to_be_expired=1;
                $seller_class->mail_payed=0;
                $seller_class->mail_wait_pay=0;
                if($seller_class->getFeeType()!='no_fee')
                {
                    $seller_class->payment_verify=-1;
                }
                else
                    $seller_class->payment_verify=0;
                if($seller_class->update(true))
                {
                    if(Configuration::get('ETS_MP_EMAIL_SELLER_GOING_TOBE_EXPIRED'))
                    {
                        $payment_information = Configuration::get('ETS_MP_SELLER_PAYMENT_INFORMATION',$this->context->language->id);
                        $str_search = array('[shop_id]','[shop_name]','[seller_name]','[seller_email]');
                        $str_replace = array($seller_class->id,$seller_class->shop_name,$seller_class->seller_email,$seller_class->seller_email);
                        $data= array(
                            '{seller_name}' => $seller_class->seller_name,
                            '{reason}' => $seller_class->reason,
                            '{date_expired}' => $seller_class->date_to,
                            '{fee_amount}' => (float)$seller_class->getFeeAmount().'('.(new Currency(Configuration::get('PS_CURRENCY_DEFAULT')))->iso_code.')',
                            '{payment_information}' => str_replace($str_search,$str_replace,$payment_information),
                            '{store_email}' => Configuration::get('ETS_MP_EMAIL_ADMIN_NOTIFICATION')?:Configuration::get('PS_SHOP_EMAIL'),
                        );
                        $subjects = array(
                            'translation' => $this->l('Your account is going to be expired'),
                            'origin'=> 'Your account is going to be expired',
                            'specific'=>false
                        );
                        Ets_marketplace::sendMail('to_seller_account_going_to_be_expired',$data,$seller_class->seller_email,$subjects,$seller_class->seller_name);
                    }
                    $fee_type= $seller_class->getFeeType();
                    if($fee_type!='no_fee')
                    {
                        $billing = new Ets_mp_billing();
                        $billing->id_customer = $seller_class->id_customer;
                        $billing->amount = (float)$seller_class->getFeeAmount();
                        $billing->amount_tax = (float)$this->getFeeIncludeTax($billing->amount,$seller_class);
                        $billing->active = 0;
                        $billing->date_from = $seller_class->date_to;
                        if($fee_type=='monthly_fee')
                            $billing->date_to = date("Y-m-d", strtotime($seller_class->date_to."+1 month"));
                        elseif($fee_type=='quarterly_fee')
                            $billing->date_to = date("Y-m-d", strtotime($seller_class->date_to."+3 month"));
                        elseif($fee_type=='yearly_fee')
                            $billing->date_to = date("Y-m-d", strtotime($seller_class->date_to."+1 year"));
                        else
                            $billing->date_to ='';
                        $billing->fee_type = $fee_type;
                        if($billing->add(true,true))
                        {
                            $seller_class->id_billing = $billing->id;
                            $seller_class->update();
                        }
                    }
                    
                }
            }
            $ok= true;
            if(Configuration::getGlobalValue('ETS_MP_SAVE_CRONJOB_LOG'))
                file_put_contents(_PS_ETS_MARKETPLACE_LOG_DIR_.'ets_merketplace.log',Tools::displayDate(date('Y-m-d H:i:s'),Configuration::get('PS_LANG_DEFAULT'),true).': '.$this->l('Sent').' '.Count($sellers_going_to_be_expired).' '.$this->l('email is going to be expired')."\n",FILE_APPEND);
            unset($seller);
        }
        $sellers_expired = Ets_mp_seller::getSellersExpired();
        if($sellers_expired)
        {
            foreach($sellers_expired as $seller)
            {
                $seller_class = new Ets_mp_seller($seller['id_seller']);
                $seller_class->mail_expired=1;
                $seller_class->active = -2;
                $seller_class->mail_payed=0;
                $seller_class->mail_wait_pay=0;
                $seller_class->payment_verify=-1;
                $seller_class->update(true);
            }
            $ok= true;
            if(Configuration::getGlobalValue('ETS_MP_SAVE_CRONJOB_LOG'))
                file_put_contents(_PS_ETS_MARKETPLACE_LOG_DIR_.'ets_merketplace.log',Tools::displayDate(date('Y-m-d H:i:s'),Configuration::get('PS_LANG_DEFAULT'),true).': '.$this->l('Expired').' '.Count($sellers_expired).' '.$this->l('seller')."\n",FILE_APPEND);
            unset($seller);
        }
        $sellers_wait_approve = Ets_mp_seller::getSellersWaitApprove();
        if($sellers_wait_approve)
        {
            foreach($sellers_wait_approve as $seller)
            {
                $seller_class = new Ets_mp_seller($seller['id_seller']);
                $seller_class->active = 1;
                $seller_class->update(true);
            }
            $ok= true;
            if(Configuration::getGlobalValue('ETS_MP_SAVE_CRONJOB_LOG'))
                file_put_contents(_PS_ETS_MARKETPLACE_LOG_DIR_.'ets_merketplace.log',Tools::displayDate(date('Y-m-d H:i:s'),Configuration::get('PS_LANG_DEFAULT'),true).': '.$this->l('Approved').' '.Count($sellers_expired).' '.$this->l('seller')."\n",FILE_APPEND);
            unset($seller);
        }
        $sellers_wait_pay = Ets_mp_seller::getSellersWaitPay();
        if($sellers_wait_pay)
        {
            foreach($sellers_wait_pay as &$seller)
            {
                $seller_class = new Ets_mp_seller($seller['id_seller']);
                $seller_class->mail_wait_pay = 1;
                $seller_class->update(true);
                $seller['seller_name'] = $seller_class->seller_name;
                $seller['seller_email'] = $seller_class->seller_email;
            }
        }
        // send mail
        if($sellers_wait_pay)
        {
            $header = array(
                $this->l('ID'),
                $this->l('Invoice ID'),
                $this->l('Seller name'),
                $this->l('Seller mail'),
                $this->l('Amount'),
            );
            $data= array();
            foreach($sellers_wait_pay as $seller)
            {
                $data[]=array(
                    'id_seller' =>$seller['id_seller'],
                    'id_billing' => $seller['id_ets_mp_seller_billing'],
                    'seller_name' => $seller['seller_name'],
                    'seller_email' => $seller['seller_email'],
                    'amount' => $seller['amount'],
                );
            }
            $filename ='list_seller';
            $file_attachment = array();
            $file_attachment['content'] = $this->exportCSV($filename,$header,$data,false);
            $file_attachment['name'] = $filename . date('d_m_Y') . '.csv';
            $file_attachment['mime'] = 'application/csv';
        }
        if($total = Ets_mp_seller::autoUpdateGroup()){
            $ok = true;
            file_put_contents(_PS_ETS_MARKETPLACE_LOG_DIR_.'ets_merketplace.log',Tools::displayDate(date('Y-m-d H:i:s'),Configuration::get('PS_LANG_DEFAULT'),true).': '.$total .' '. $this->l(' shop(s) has been upgraded')."\n",FILE_APPEND);
        }
        if(!$ok && Configuration::getGlobalValue('ETS_MP_SAVE_CRONJOB_LOG'))
            file_put_contents(_PS_ETS_MARKETPLACE_LOG_DIR_.'ets_merketplace.log',Tools::displayDate(date('Y-m-d H:i:s'),Configuration::get('PS_LANG_DEFAULT'),true).': '.$this->l('Cronjob run but nothing to do')."\n",FILE_APPEND);
        Configuration::updateGlobalValue('ETS_MP_TIME_LOG_CRONJOB',date('Y-m-d H:i:s'));
        if(Tools::isSubmit('ajax'))
            die(
                json_encode(
                    array(
                        'success' => $this->l('Cronjob done'),
                        'cronjob_log' => file_exists(_PS_ETS_MARKETPLACE_LOG_DIR_.'ets_merketplace.log') ?  Tools::file_get_contents(_PS_ETS_MARKETPLACE_LOG_DIR_.'ets_merketplace.log'):'',
                    )
                )
            );
    }
    public function exportCSV($file_name,$header=array(),$data= array(),$display=false)
    {
        $filename = $file_name . date('d_m_Y') . ".csv";
        if ($display) {
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-type: application/x-msdownload");
        }
        $flag = false;
        $csv = '';
        if ($data) {
            foreach ($data as $row) {
                if (!$flag) {
                    $csv .= join("\t", $header)."\r\n";
                    $flag = true;
                }
                if($row)
                {
                    foreach($row as &$val)
                        $val = str_replace(array("\r\n","\r","\n"),"",$val);
                }
                $csv .= join("\t", array_values($row))."\r\n";
            }
        } else {
            $csv .= join("\t", $header)."\r\n";
        }
        $csv = chr(255).chr(254).mb_convert_encoding($csv, "UTF-16LE", "UTF-8");
        if ($display) {
            echo $csv;
            exit();
        } else {
            return $csv;
        }
    }
    public function getDateRanger($start, $end, $format = 'Y-m-d', $list_data_by_date = false, $type = 'date')
    {

        $array = array();
        $interval = new DateInterval('P1D');
        if ($type == 'month') {
            $interval = DateInterval::createFromDateString('1 month');
        }

        $period = new DatePeriod(
            new DateTime($start),
            $interval,
            new DateTime($end));

        foreach ($period as $date) {
            if ($list_data_by_date) {
                $array[$date->format($format)] = 0;
            } else {
                $array[] = $date->format($format);
            }
        }
        return $array;
    }

    public function getYearRanger($start, $end, $format = 'Y', $list_data_by_date = false)
    {

        $array = array();

        $getRangeYear = range(gmdate('Y', strtotime($start)), gmdate('Y', strtotime($end)));
        foreach ($getRangeYear as $year) {
            if ($list_data_by_date) {
                $array[date($format, strtotime($year . '-01-01 00:00:00'))] = 0;
            } else {
                $array[] = date($format, strtotime($year . '-01-01 00:00:00'));
            }
        }
        return $array;
    }
    public function hookDisplayFooterProduct($params)
    {
        if(!Configuration::get('ETS_MP_ENABLED') || (!Configuration::get('ETS_MP_DISPLAY_OTHER_PRODUCT') && !Configuration::get('ETS_MP_DISPLAY_OTHER_SELLER_PRODUCT')) )
            return '';
        if(isset($params['product']) && $product= $params['product'])
        {
            if(Validate::isLoadedObject($product))
                $id_product = $product->id;
            elseif(is_array($product) && isset($product['id_product']))
                $id_product = $product['id_product'];
            else
                return '';
            if($id_customer = (int)Ets_mp_seller::getIDCustomerSellerByIdProduct($id_product))
            {
                $seller= Ets_mp_seller::_getSellerByIdCustomer($id_customer,$this->context->language->id);
                $productObj = new Product($id_product,true,$this->context->language->id);
                $html = '';
                if(Configuration::get('ETS_MP_DISPLAY_OTHER_PRODUCT'))
                {
                    $products= $seller->getProductOther($productObj);
                    if($products)
                    {
                        $this->context->smarty->assign(
                            array(
                                'seller' => $seller,
                                'products' => $products,
                                'position' => '',
                                'link_seller' => $this->getShopLink(array('id_seller'=>$seller->id))
                            )
                        );
                        if($this->is17)
                            $html .= $this->display(__FILE__,'product/products_other.tpl');
                        else
                            $html .= $this->display(__FILE__,'product/products_other16.tpl');
                    }
                }
                if(Configuration::get('ETS_MP_DISPLAY_OTHER_SELLER_PRODUCT'))
                {
                    $other_seller_products= $seller->getProductSellerOther($productObj);
                    if($other_seller_products)
                    {
                        $this->context->smarty->assign(
                            array(
                                'seller' => $seller,
                                'products' => $other_seller_products,
                                'position' => '',
                                'link_seller' => $this->getShopLink(array('id_seller'=>$seller->id))
                            )
                        );
                        if($this->is17)
                            $html .= $this->display(__FILE__,'product/products_seller_other.tpl');
                        else
                            $html .= $this->display(__FILE__,'product/products_seller_other16.tpl');
                    }
                }
                return $html;
            }
        }
        return '';
    }
    public function displaySellerInProductPage($id_product)
    {
        if(($id_customer = Ets_mp_seller::getIDCustomerSellerByIdProduct($id_product)) && ($seller= Ets_mp_seller::_getSellerByIdCustomer($id_customer,$this->context->language->id)) && $seller->active!=0)
        {
            $reviews = $seller->getAVGReviewProduct();
            $total_reviews = isset($reviews['avg_grade']) ? $reviews['avg_grade']:0;
            $count_reviews = isset($reviews['count_grade']) ? $reviews['count_grade']:0;
            $total_messages = $this->_getOrderMessages('',null,null,null,true,$seller->id);
            if($total_messages)
            {
                $total_messages_reply = $seller->getTotalMessagesReply();
                $response_rate = Tools::ps_round($total_messages_reply*100/$total_messages,2);
            }
            if(Configuration::get('ETS_MP_ENABLE_CAPTCHA') && Configuration::get('ETS_MP_ENABLE_CAPTCHA_FOR') && $this->context->customer->isLogged())
            {
                $captcha_for = explode(',',Configuration::get('ETS_MP_ENABLE_CAPTCHA_FOR'));
                if(in_array('shop_report',$captcha_for) &&  !Configuration::get('ETS_MP_NO_CAPTCHA_IS_LOGIN'))
                    $is_captcha = true;
            }
            $action = (string)Tools::getValue('action');
            $this->context->smarty->assign(
                array(
                    'link_shop_seller' => $this->getShopLink(array('id_seller'=>$seller->id)),
                    'shop_name' => $seller->shop_name,
                    'logo_seller' => $seller->shop_logo,
                    'total_reviews' => Tools::ps_round($total_reviews,1),
                    'total_reviews_int' => (int)$total_reviews,
                    'count_reviews' => $count_reviews,
                    'total_product_sold' => Configuration::get('ETS_MP_DISPLAY_PRODUCT_SOLD') ? $seller->_getTotalNumberOfProductSold($id_product):false,
                    'total_products' => $seller->getProducts(false,false,false,false,true,true,false),
                    'link_contact_form' => $this->context->link->getModuleLink($this->name,'contactseller',array('id_product'=>$id_product)),
                    'total_follow' => $seller->getTotalFollow(),
                    'response_rate' => isset($response_rate) ? $response_rate :false,
                    'seller_date_add' => $seller->date_add,
                    'customer_logged' => $this->context->customer->isLogged(),
                    'report_customer' => $this->context->customer,
                    'link_proudct' => $this->context->link->getProductLink($id_product),
                    'id_product_report' => $id_product,
                    'id_seller_report' => $seller->id,
                    'quick_view' => $action=='quickview' ? true : false,
                    'reported' => $this->context->customer->isLogged() ? $seller->CheckReported($this->context->customer->id,$id_product) :false,
                    'is_captcha' => isset($is_captcha) ? $is_captcha:false,
                    'ETS_MP_ENABLE_CAPTCHA_TYPE' => Configuration::get('ETS_MP_ENABLE_CAPTCHA_TYPE'),
                    'ETS_MP_ENABLE_CAPTCHA_SITE_KEY2' => Configuration::get('ETS_MP_ENABLE_CAPTCHA_SITE_KEY2'),
                    'ETS_MP_ENABLE_CAPTCHA_SECRET_KEY2' => Configuration::get('ETS_MP_ENABLE_CAPTCHA_SECRET_KEY2'),
                    'ETS_MP_ENABLE_CAPTCHA_SITE_KEY3' => Configuration::get('ETS_MP_ENABLE_CAPTCHA_SITE_KEY3'),
                    'ETS_MP_ENABLE_CAPTCHA_SECRET_KEY3' => Configuration::get('ETS_MP_ENABLE_CAPTCHA_SECRET_KEY3'),
                    'vacation_notifications' => $seller->checkVacation() && Tools::strpos($seller->vacation_type,'show_notifications')!==false ? $seller->vacation_notifications:'',
                    
                )
            );
            return $this->display(__FILE__,'product/product_detail.tpl');
        }
    }
    public function hookDisplayProductAdditionalInfo($params)
    {
        if(!Configuration::get('ETS_MP_ENABLED'))
            return '';
        if(isset($params['product']) && $product= $params['product'])
        {
            return $this->displaySellerInProductPage(Validate::isLoadedObject($product) ?  $product->id :(is_array($product) && isset($product['id_product']) ? $product['id_product']:0));
        }
    }
    public function hookDisplayRightColumnProduct()
    {
        if(!Configuration::get('ETS_MP_ENABLED'))
            return '';
        if(($id_product = Tools::getValue('id_product')) && Validate::isUnsignedId($id_product))
        {
            return $this->displaySellerInProductPage($id_product);
        }
    }
    public function hookDisplayCartExtraProductActions($params)
    {
        $controller = Tools::getValue('controller');
        
        if(!Validate::isControllerName($controller))
            $controller ='';
        if(!Configuration::get('ETS_MP_ENABLED') || ($controller!='order' && $controller!='orderconfirmation') )
            return '';
        if(isset($params['product']) && $product= $params['product'])
        {
            if(($id_customer = (int)Ets_mp_seller::getIDCustomerSellerByIdProduct($product['id_product'])) && ($seller = Ets_mp_seller::_getSellerByIdCustomer($id_customer,$this->context->language->id)) && $seller->active!=0)
            {
                $this->context->smarty->assign(
                    array(
                        'link_shop_seller' => $this->getShopLink(array('id_seller'=>$seller->id)),
                        'shop_name' => $seller->shop_name,
                        'controller' => $controller,
                        'link_contact_form' => $this->context->link->getModuleLink($this->name,'contactseller',array('id_product'=>$product['id_product'])),
                    )
                );
                return $this->display(__FILE__,'product/cart_detail.tpl');
            }
        }
        return '';
    }
    public function hookDisplayProductPriceBlock($params)
    {
        return $this->hookDisplayCartExtraProductActions($params);
    }
    public function hookActionObjectLanguageAddAfter()
    {
       Ets_mp_defines::duplicateRowsFromDefaultShopLang(_DB_PREFIX_.'ets_mp_seller_lang',$this->context->shop->id,'id_seller');
       $this->createTemplateMail();
    }
    public function hookActionObjectCustomerDeleteAfter($params)
    {
        if($params['object']->id)
        {
            $seller = Ets_mp_seller::_getSellerByIdCustomer($params['object']->id);
            if($seller)
                $seller->delete();
            $registration = Ets_mp_registration::_getRegistration($params['object']->id);
            if ($registration && Validate::isLoadedObject($registration))
                $registration->delete();
        }
    }
    public function getLinkCustomerAdmin($id_customer)
    {
        if(version_compare(_PS_VERSION_, '1.7.6', '>='))
        {
            $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer','getInstance'));
        	if (null !== $sfContainer) {
        		$sfRouter = $sfContainer->get('router');
        		$link_customer= $sfRouter->generate(
        			'admin_customers_view',
        			array('customerId' => $id_customer)
        		);
        	}
        }
        else
            $link_customer = $this->context->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$id_customer.'&viewcustomer';
        return $link_customer;
    }
    public function updateContext(Customer $customer)
	{
	    if ($this->is17)
	        return false;
        $this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
        $this->context->cookie->id_customer = (int)($customer->id);
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->logged = 1;
        $customer->logged = 1;
        $this->context->cookie->is_guest = $customer->isGuest();
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->email = $customer->email;
        // Add customer to the context
        $this->context->customer = $customer;
        if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id)) {
            $this->context->cart = new Cart($id_cart);
        } else {
            $this->context->cart->id_carrier = 0;
            $this->context->cart->setDeliveryOption(null);
            $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
            $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)($customer->id));
        }
        $this->context->cart->id_customer = (int)$customer->id;
        $this->context->cart->secure_key = $customer->secure_key;
        $this->context->cart->save();
        $this->context->cookie->id_cart = (int)$this->context->cart->id;
        $this->context->cookie->write();
        $this->context->cart->autosetProductAddress();
        Hook::exec('actionAuthentication', array('customer' => $this->context->customer));
        // Login information have changed, so we check if the cart rules still apply
        CartRule::autoRemoveFromCart($this->context);
        CartRule::autoAddToCart($this->context);
	}
    public function _getOrderMessages($filter='',$start=0,$limit=12,$order_by='',$total=false,$id_seller=0)
    {
        if(!$id_seller)
            $seller = $this->_getSeller();
        else
            $seller = new Ets_mp_seller($id_seller);
        if($seller)
        {
            return $seller->getOrderMessages($filter,$start,$limit,$order_by,$total);
        }
        return false;
    }
    public function checkMultiSellerProductList($products)
    {
        $sellers = array();
        if($products)
        {
            foreach($products as $product)
            {
                $id_customer = Ets_mp_seller::getIDCustomerSellerByIdProduct($product['id_product']);
                if(!isset($sellers[$id_customer]))
                    $sellers[$id_customer] = array();
                $sellers[$id_customer][]= $product;
            }
        }
        if(count($sellers)>=2)
            return $sellers;
        else
            return false;
    }
   
    public function checkCreatedColumn($table,$column)
    {
        $fieldsCustomers = Ets_mp_defines::getColumns($table);
        $check_add=false;
        foreach($fieldsCustomers as $field)
        {
            if($field['Field']==$column)
            {
                $check_add=true;
                break;
            }    
        }
        return $check_add;
    }
    public static function isLink($inputLink)
    {
        if(Tools::strpos($inputLink,'http')===0)
        {
            $link_validation = '/(http|https)\:\/\/[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/';
            if(preg_match("$link_validation", $inputLink)){
                return  true;
            }
        }
        return false;
    }
    public function hookDisplayProductListReviews($params)
    {
        
        if(($controller=Tools::getValue('controller')) && Validate::isControllerName($controller) && $controller !='shop')
        {
            if(isset($params['product']))
            {
                $product = $params['product'];
                if(($id_customer = Ets_mp_seller::getIDCustomerSellerByIdProduct($product['id_product'])) && ($seller= Ets_mp_seller::_getSellerByIdCustomer($id_customer,$this->context->language->id)) && $seller->active!=0)
                {
                    $this->context->smarty->assign(
                        array(
                            'link_shop_seller' => $this->getShopLink(array('id_seller'=>$seller->id)),
                            'shop_name' => $seller->shop_name,
                            'logo_seller' => $seller->shop_logo,
                            'link_contact_form' => $this->context->link->getModuleLink($this->name,'contactseller',array('id_product'=>$product['id_product'])),
                        )
                    );
                    return $this->display(__FILE__,'product/product_list_detail.tpl');
                }
            }
        }
        
    }
    public function getTextLang($text, $lang,$file_name='')
    {
        if(is_array($lang))
            $iso_code = $lang['iso_code'];
        elseif(is_object($lang))
            $iso_code = $lang->iso_code;
        else
        {
            $language = new Language($lang);
            $iso_code = $language->iso_code;
        }
		$modulePath = rtrim(_PS_MODULE_DIR_, '/').'/'.$this->name;
        $fileTransDir = $modulePath.'/translations/'.$iso_code.'.'.'php';
        if(!@file_exists($fileTransDir)){
            return $text;
        }
        $fileContent = Tools::file_get_contents($fileTransDir);
        $text_tras = preg_replace("/\\\*'/", "\'", $text);
        $strMd5 = md5($text_tras);
        $keyMd5 = '<{' . $this->name . '}prestashop>' . ($file_name ? : $this->name) . '_' . $strMd5;
        preg_match('/(\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]\s*=\s*\')(.*)(\';)/', $fileContent, $matches);
        if($matches && isset($matches[2])){
           return  $matches[2];
        }
        return $text;
    }
    public static function validateArray($array,$validate='isCleanHtml')
    {
        if(!is_array($array))
            return false;
        if(method_exists('Validate',$validate))
        {
            if($array && is_array($array))
            {
                $ok= true;
                foreach($array as $val)
                {
                    if(!is_array($val))
                    {
                        if($val && !Validate::$validate($val))
                        {
                            $ok= false;
                            break;
                        }
                    }
                    else
                        $ok = self::validateArray($val,$validate);
                }
                return $ok;
            }
        }
        return true;
    }
    public static function createCombinations($list)
    {
        if (count($list) <= 1) {
            return count($list) ? array_map(function ($v) { return array($v); }, $list[0]) : $list;
        }
        $res = array();
        $first = array_pop($list);
        foreach ($first as $attribute) {
            $tab = self::createCombinations($list);
            foreach ($tab as $to_add) {
                $res[] = is_array($to_add) && Ets_marketplace::validateArray($to_add) ? array_merge($to_add, array($attribute)) : array($to_add, $attribute);
            }
        }
        return $res;
    }
    public function changeDeliveryOptionList($delivery_option_list)
    {
        if($delivery_option_list)
        {
            if(version_compare(_PS_VERSION_, '1.7', '>='))
            {
                foreach($delivery_option_list as $id_address => $option_list)
                {
                    foreach($option_list as $key => $option)
                    {

                       if($option['carrier_list']) 
                       {
                            foreach($option['carrier_list'] as $id_carrier=> $carrier)
                            {
                                $list_carriers = explode(',',trim($key,','));
                                if(count($list_carriers)>=2)
                                {
                                    $name = '';
                                    $delay = array();
                                    $languages = Language::getLanguages(true);
                                    foreach($list_carriers as $carrier_id)
                                    {
                                        $objCarrier = new Carrier($carrier_id);
                                        $name .= $objCarrier->name.' ('.Tools::displayPrice($this->context->cart->getPackageShippingCost($carrier_id,false,null,$option['carrier_list'][$carrier_id]['product_list'])).'), ';
                                        foreach($languages as $language)
                                        {
                                            if(!isset($delay[$language['id_lang']]))
                                                $delay[$language['id_lang']] = '';
                                            if($objCarrier->delay[$language['id_lang']])
                                                $delay[$language['id_lang']] .= $objCarrier->name.': '.$objCarrier->delay[$language['id_lang']].', ';
                                        }
                                        
                                    }
                                    foreach($languages as $language)
                                    {
                                        $delay[$language['id_lang']] = trim($delay[$language['id_lang']],', ');
                                    }
                                    $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance'] = clone $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance'];
                                    $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance']->name = trim($name,', ');
                                    $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance']->delay = $delay;
                                }
                                
                                unset($carrier);
                            } 
                       }
                       unset($option);
                    }
                   unset($option_list); 
                }
                
            }
            else
            {
                foreach($delivery_option_list as &$option_list)
                {
                    foreach($option_list as $key => &$option)
                    {
                       if($option['carrier_list']) 
                       {
                            foreach($option['carrier_list'] as &$carrier)
                            {
                                if($carrier['product_list'])
                                {
                                    foreach($carrier['product_list'] as &$product)
                                    {
                                        $carrier_list = array();
                                        if($product['carrier_list'])
                                        {
                                            foreach($product['carrier_list'] as $id)
                                                $carrier_list[] = $id;
                                        }
                                        $product['carrier_list'] = $carrier_list;
                                    }
                                }
                            }
                       }
                    }
                }
            }
            
        }
        return $delivery_option_list;
    }
    public function getDeliveryOptionList(Country $default_country = null, $flush = false)
    {
        $delivery_option_list = array();
        $carriers_price = array();
        $carrier_collection = array();
        $package_list = $this->context->cart->getPackageList($flush);
        // Foreach addresses   
                             
        foreach ($package_list as $id_address => $packages) {
            // Initialize vars
            $delivery_option_list[$id_address] = array();
            $carriers_price[$id_address] = array();
            $common_carriers = null;
            $best_price_carriers = array();
            $best_grade_carriers = array();
            $carriers_instance = array();

            // Get country
            if ($id_address) {
                $address = new Address($id_address);
                $country = new Country($address->id_country);
            } else {
                $country = $default_country;
            }
        
            // Foreach packages, get the carriers with best price, best position and best grade 
            $extra_carriers = array();
            foreach ($packages as $id_package => $package) {
                // No carriers available
                if (count($packages) == 1 && count($package['carrier_list']) == 1 && current($package['carrier_list']) == 0) {
                    return array();
                }
                $extra_carriers[$id_package] = $package['carrier_list'];
                $carriers_price[$id_address][$id_package] = array();

                // Get all common carriers for each packages to the same address
                if (null === $common_carriers) {
                    $common_carriers = $package['carrier_list'];
                } else {
                    $common_carriers = array_intersect($common_carriers, $package['carrier_list']);
                }
                $best_price = null;
                $best_price_carrier = null;
                $best_grade = null;
                $best_grade_carrier = null;
                foreach ($package['carrier_list'] as $id_carrier) {
                    if (!isset($carriers_instance[$id_carrier])) {
                        $carriers_instance[$id_carrier] = new Carrier($id_carrier);
                    }

                    $price_with_tax = $this->context->cart->getPackageShippingCost((int) $id_carrier, true, $country, $package['product_list']);
                    $price_without_tax = $this->context->cart->getPackageShippingCost((int) $id_carrier, false, $country, $package['product_list']);
                    if (null === $best_price || $price_with_tax < $best_price) {
                        $best_price = $price_with_tax;
                        $best_price_carrier = $id_carrier;
                    }
                    $carriers_price[$id_address][$id_package][$id_carrier] = array(
                        'without_tax' => $price_without_tax,
                        'with_tax' => $price_with_tax,
                    );

                    $grade = $carriers_instance[$id_carrier]->grade;
                    if (null === $best_grade || $grade > $best_grade) {
                        $best_grade = $grade;
                        $best_grade_carrier = $id_carrier;
                    }
                }

                $best_price_carriers[$id_package] = $best_price_carrier;
                $best_grade_carriers[$id_package] = $best_grade_carrier;
            }

            // Reset $best_price_carrier, it's now an array
            $best_price_carrier = array();
            $key = '';
            // Get the delivery option with the lower price
            foreach ($best_price_carriers as $id_package => $id_carrier) {
                $key .= $id_carrier . ',';
                if (!isset($best_price_carrier[$id_carrier])) {
                    $best_price_carrier[$id_carrier] = array(
                        'price_with_tax' => 0,
                        'price_without_tax' => 0,
                        'package_list' => array(),
                        'product_list' => array(),
                    );
                }
                $best_price_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                $best_price_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                $best_price_carrier[$id_carrier]['package_list'][] = $id_package;
                $best_price_carrier[$id_carrier]['product_list'] = array_merge($best_price_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);
                $best_price_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];
                $real_best_price = !isset($real_best_price) || $real_best_price > $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'] ?
                    $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'] : $real_best_price;
                $real_best_price_wt = !isset($real_best_price_wt) || $real_best_price_wt > $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'] ?
                    $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'] : $real_best_price_wt;
            }

            // Add the delivery option with best price as best price
            $delivery_option_list[$id_address][$key] = array(
                'carrier_list' => $best_price_carrier,
                'is_best_price' => true,
                'is_best_grade' => false,
                'unique_carrier' => (count($best_price_carrier) <= 1),
            );
            
            // Reset $best_grade_carrier, it's now an array
            $best_grade_carrier = array();
            $key = '';

            // Get the delivery option with the best grade
            foreach ($best_grade_carriers as $id_package => $id_carrier) {
                $key .= $id_carrier . ',';
                if (!isset($best_grade_carrier[$id_carrier])) {
                    $best_grade_carrier[$id_carrier] = array(
                        'price_with_tax' => 0,
                        'price_without_tax' => 0,
                        'package_list' => array(),
                        'product_list' => array(),
                    );
                }
                $best_grade_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                $best_grade_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                $best_grade_carrier[$id_carrier]['package_list'][] = $id_package;
                $best_grade_carrier[$id_carrier]['product_list'] = array_merge($best_grade_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);
                $best_grade_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];
            }
            // Add the delivery option with best grade as best grade
            if (!isset($delivery_option_list[$id_address][$key])) {
                $delivery_option_list[$id_address][$key] = array(
                    'carrier_list' => $best_grade_carrier,
                    'is_best_price' => false,
                    'unique_carrier' => (count($best_grade_carrier) <= 1),
                );
            }
            $delivery_option_list[$id_address][$key]['is_best_grade'] = true;

            // Get all delivery options with a unique carrier
            foreach ($common_carriers as $id_carrier) {
                $key = '';
                $package_list = array();
                $product_list = array();
                $price_with_tax = 0;
                $price_without_tax = 0;
                
                foreach ($packages as $id_package => $package) {
                    $key .= $id_carrier . ',';
                    $price_with_tax += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                    $price_without_tax += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                    $package_list[] = $id_package;
                    $product_list = array_merge($product_list, $package['product_list']);
                }

                if (!isset($delivery_option_list[$id_address][$key])) {
                    $delivery_option_list[$id_address][$key] = array(
                        'is_best_price' => false,
                        'is_best_grade' => false,
                        'unique_carrier' => true,
                        'carrier_list' => array(
                            $id_carrier => array(
                                'price_with_tax' => $price_with_tax,
                                'price_without_tax' => $price_without_tax,
                                'instance' => $carriers_instance[$id_carrier],
                                'package_list' => $package_list,
                                'product_list' => $product_list,
                            ),
                        ),
                    );
                } else {
                    $delivery_option_list[$id_address][$key]['unique_carrier'] = (count($delivery_option_list[$id_address][$key]['carrier_list']) <= 1);
                }
            }
            if($extra_carriers)
            {
                $extra_carriers = Ets_marketplace::createCombinations($extra_carriers);
                foreach($extra_carriers as $extra_carrier)
                {
                    $best_grade_carrier = array();
                    $key = '';
                    // Get the delivery option with the best grade
                    foreach ($extra_carrier as $id_package => $id_carrier) {
                        $key .= $id_carrier . ',';
                        if (!isset($best_grade_carrier[$id_carrier])) {
                            $best_grade_carrier[$id_carrier] = array(
                                'price_with_tax' => 0,
                                'price_without_tax' => 0,
                                'package_list' => array(),
                                'product_list' => array(),
                            );
                        }
                        $best_grade_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                        $best_grade_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                        $best_grade_carrier[$id_carrier]['package_list'][] = $id_package;
                        $best_grade_carrier[$id_carrier]['product_list'] = array_merge($best_grade_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);
                        $best_grade_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];
                    }
                    // Add the delivery option with best grade as best grade
                    if (!isset($delivery_option_list[$id_address][$key])) {
                        $delivery_option_list[$id_address][$key] = array(
                            'carrier_list' => $best_grade_carrier,
                            'is_best_price' => false,
                            'is_best_grade' => false,
                            'unique_carrier' => false,
                        );
                    }
                }
            }
        }

        $cart_rules = CartRule::getCustomerCartRules(Context::getContext()->cookie->id_lang, Context::getContext()->cookie->id_customer, true, true, false, $this->context->cart, true);

        $result = false;
        if ($this->context->cart->id) {
            $result = $this->context->cart->getCartRules(CartRule::FILTER_ACTION_ALL,false);
        }

        $cart_rules_in_cart = array();

        if (is_array($result)) {
            foreach ($result as $row) {
                $cart_rules_in_cart[] = $row['id_cart_rule'];
            }
        }

        $total_products_wt = $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $total_products = $this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        $free_carriers_rules = array();

        $context = Context::getContext();
        foreach ($cart_rules as $cart_rule) {
            $total_price = $cart_rule['minimum_amount_tax'] ? $total_products_wt : $total_products;
            $total_price += $cart_rule['minimum_amount_tax'] && $cart_rule['minimum_amount_shipping'] ? $real_best_price : 0;
            $total_price += !$cart_rule['minimum_amount_tax'] && $cart_rule['minimum_amount_shipping'] ? $real_best_price_wt : 0;
            if ($cart_rule['free_shipping'] && $cart_rule['carrier_restriction']
                && in_array($cart_rule['id_cart_rule'], $cart_rules_in_cart)
                && $cart_rule['minimum_amount'] <= $total_price) {
                $cr = new CartRule((int) $cart_rule['id_cart_rule']);
                if (Validate::isLoadedObject($cr) &&
                    $cr->checkValidity($context, in_array((int) $cart_rule['id_cart_rule'], $cart_rules_in_cart), false, false)) {
                    $carriers = $cr->getAssociatedRestrictions('carrier', true, false);
                    if (is_array($carriers) && count($carriers) && isset($carriers['selected'])) {
                        foreach ($carriers['selected'] as $carrier) {
                            if (isset($carrier['id_carrier']) && $carrier['id_carrier']) {
                                $free_carriers_rules[] = (int) $carrier['id_carrier'];
                            }
                        }
                    }
                }
            }
        }

        // For each delivery options :
        //    - Set the carrier list
        //    - Calculate the price
        //    - Calculate the average position
        foreach ($delivery_option_list as $id_address => $delivery_option) {
            foreach ($delivery_option as $key => $value) {
                $total_price_with_tax = 0;
                $total_price_without_tax = 0;
                $position = 0;
                foreach ($value['carrier_list'] as $id_carrier => $data) {
                    $total_price_with_tax += $data['price_with_tax'];
                    $total_price_without_tax += $data['price_without_tax'];
                    $total_price_without_tax_with_rules = (in_array($id_carrier, $free_carriers_rules)) ? 0 : $total_price_without_tax;

                    if (!isset($carrier_collection[$id_carrier])) {
                        $carrier_collection[$id_carrier] = new Carrier($id_carrier);
                    }
                    $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance'] = $carrier_collection[$id_carrier];

                    if (file_exists(_PS_SHIP_IMG_DIR_ . $id_carrier . '.jpg')) {
                        $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = _THEME_SHIP_DIR_ . $id_carrier . '.jpg';
                    } else {
                        $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = false;
                    }

                    $position += $carrier_collection[$id_carrier]->position;
                }
                $delivery_option_list[$id_address][$key]['total_price_with_tax'] = $total_price_with_tax;
                $delivery_option_list[$id_address][$key]['total_price_without_tax'] = $total_price_without_tax;
                $delivery_option_list[$id_address][$key]['is_free'] = !$total_price_without_tax_with_rules ? true : false;
                $delivery_option_list[$id_address][$key]['position'] = $position / count($value['carrier_list']);
            }
        }

        // Sort delivery option list
        foreach ($delivery_option_list as &$array) {
            uasort($array, array('Cart', 'sortDeliveryOptionList'));
        }
        return $delivery_option_list;
    }
    public function hookDisplayAfterCarrier()
    {
        $products = $this->context->cart->getProducts(false, false, null, true);
        if(!$this->checkMultiSellerProductList($products) || !Configuration::get('ETS_MP_ENABLE_MULTI_SHIPPING'))
            return '';
        $delivery_option_list = array();
        $package_list = $this->context->cart->getPackageList(false);
        $delivery_option_selecteds = $this->context->cart->delivery_option ? json_decode($this->context->cart->delivery_option,true):$this->context->cart->getDeliveryOption();
        $sellers = array();
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $type_image= ImageType::getFormattedName('small');
        else
            $type_image= ImageType::getFormatedName('small');
        foreach ($package_list as $id_address => $packages) {
            $carriers_instance = array();
            if(isset($delivery_option_selecteds[$id_address]) && $delivery_option_selecteds[$id_address])
                $delivery_option_selected = explode(',',trim($delivery_option_selecteds[$id_address],','));
            else
                $delivery_option_selected = array();
            // Get country
            if ($id_address) {
                $address = new Address($id_address);
                $country = new Country($address->id_country);
            } else {
                $country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
            }
            foreach ($packages as $id_package => $package) {
                if (count($packages) == 1 && count($package['carrier_list']) == 1 && current($package['carrier_list']) == 0) {
                    return '';
                }
                if(!isset($delivery_option_list[$id_address]))
                    $delivery_option_list[$id_address] = array();
                $carrier_list = array();
                foreach ($package['carrier_list'] as $id_carrier) {
                    if (!isset($carriers_instance[$id_carrier])) {
                        $carriers_instance[$id_carrier] = new Carrier($id_carrier);
                    }

                    $price_with_tax = $this->context->cart->getPackageShippingCost((int) $id_carrier, true, $country, $package['product_list']);
                    $price_without_tax = $this->context->cart->getPackageShippingCost((int) $id_carrier, false, $country, $package['product_list']);
                    $carrier_list[] = array(
                        'id_carrier' => $id_carrier,
                        'name' => $carriers_instance[$id_carrier]->name,
                        'delay' => $carriers_instance[$id_carrier]->delay[$this->context->language->id],
                        'price_with_tax' => $price_with_tax,
                        'price_without_tax' => $price_without_tax,
                        'selected' =>in_array($id_carrier,$delivery_option_selected) ? true :false,
                    );
                }
                $shop_names = array();
                if($package['product_list'])
                {
                    foreach($package['product_list'] as &$product)
                    {
                        if($product['id_image'])
                        {
                            $ids = explode('-',$product['id_image']);
                            if(isset($ids[1]))
                                $id_image = (int)$ids[1];
                            else
                                $id_image = (int)$ids[0];
                        }
                        if(isset($id_image) && $id_image)
                            $product['image'] =  $this->context->link->getImageLink($product['link_rewrite'],$id_image,$type_image);
                        else
                            $product['image']='';
                        if($id_seller = Ets_mp_product::getSellerByIdProduct($product['id_product']))
                        {
                            if(!isset($sellers[$id_seller]))
                                $sellers[$id_seller] = new Ets_mp_seller($id_seller,$this->context->language->id);
                            $product['shop_name'] = $sellers[$id_seller]->shop_name;
                            if(!isset($shop_names[$id_seller]))
                                $shop_names[$id_seller] = $this->displayText($sellers[$id_seller]->shop_name,'a',null,null,$sellers[$id_seller]->getLink());
                        }else
                            if(!isset($shop_names[0]))
                                $shop_names[0] = $this->displayText($this->context->shop->name,'a',null,null,$this->context->link->getPageLink('index'));
                    }
                }
                $option = array(
                    'product_list' => $package['product_list'],
                    'carrier_list' =>$carrier_list,
                    'shop_names' => $shop_names,
                );
                $delivery_option_list[$id_address][$id_package] = $option;
            }

        }
        $smarty = $this->context->smarty;
        smartyRegisterFunction($smarty, 'function', 'displayAddressDetail', array('AddressFormat', 'generateAddressSmarty'));
        smartyRegisterFunction($smarty, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
        $this->context->smarty->assign(
            array(
                'delivery_option_list' => $delivery_option_list,
            )
        );  
        return  $this->display(__FILE__,'shippings.tpl');
    }
    public function displayText($content=null,$tag=null,$class=null,$id=null,$href=null,$blank=false,$src = null,$name = null,$value = null,$type = null,$data_id_product = null,$rel = null,$attr_datas=null)
    {
        $this->smarty->assign(
            array(
                'ets_mp_content' =>$content,
                'ets_mp_tag' => $tag,
                'ets_mp_class'=> $class,
                'ets_mp_id' => $id,
                'ets_mp_href' => $href,
                'ets_mp_blank' => $blank,
                'ets_mp_src' => $src,
                'ets_mp_name' => $name,
                'ets_mp_value' => $value,
                'ets_mp_type' => $type,
                'ets_mp_data_id_product' => $data_id_product,
                'ets_mp_attr_datas' => $attr_datas,
                'ets_mp_rel' => $rel,
            )
        );
        return $this->display(__FILE__,'html.tpl');
    }
    public function displayTpl($tpl)
    {
        return $this->display(__FILE__,$tpl);
    }
    public function hookActionObjectProductUpdateBefore($params)
    {
        if(isset($params['object']) && ($product = $params['object']) && Validate::isLoadedObject($product))
        {
            $context = $this->context;
            if($product->active && isset($context->employee->id) && $context->employee->id && ($id_seller = Ets_mp_product::getSellerByIdProduct($product->id)) && ($seller = new Ets_mp_seller($id_seller)) && Validate::isLoadedObject($seller) && $seller->checkVacation() && Tools::strpos($seller->vacation_type,'disable_product')!==false)
            {
                throw new PrestaShopException($this->l('You do not have permission to enable this product'));
            } 
            if($product->available_for_order && isset($context->employee->id) && $context->employee->id && ($id_seller = Ets_mp_product::getSellerByIdProduct($product->id)) && ($seller = new Ets_mp_seller($id_seller)) && Validate::isLoadedObject($seller) && $seller->checkVacation() && Tools::strpos($seller->vacation_type,'disable_shopping')!==false)
            {
                throw new PrestaShopException($this->l('You do not have permission to set this product as Available for order'));
            }  
        }
    }
    public function displayPaggination($limit,$name)
    {
        $this->context->smarty->assign(
            array(
                'limit' => $limit,
                'pageName' => $name,
            )
        );
        return $this->display(__FILE__,'limit.tpl');
    }
    public function getCustomerMessagesOrder($id_customer, $id_order)
    {
        return Ets_mp_contact_message::getCustomerMessagesOrder($id_customer, $id_order);
    }
    public function submitSaveSeller($id_seller, &$errors, $admin, &$valueFieldPost)
    {
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages(false);
        $seller_fields = array(
            'seller_name' => $this->l('Seller name'),
            'seller_email' => $this->l('Seller email'),
            'shop_name' => $this->l('Shop name'),
            'shop_description' => $this->l('Shop description'),
            'shop_address' => $this->l('Shop address'),
            'vat_number' => $this->l('VAT number'),
            'shop_phone' => $this->l('Shop phone number'),
            'shop_logo' => $this->l('Shop logo'),
            'shop_banner' => $this->l('Shop banner'),
            'banner_url' => $this->l('Banner URL'),
            'link_facebook' => $this->l('Facebook link'),
            'link_google' => $this->l('Google link'),
            'link_instagram' => $this->l('Instagram link'),
            'link_twitter' => $this->l('Twitter link'),
            'latitude' => $this->l('Latitude'),
            'longitude' => $this->l('Longitude'),
            'id_shop_category' => $this->l('Shop category'),
        );
        $shop_name_default = Tools::getValue('shop_name_' . $id_lang_default);
        if (!$shop_name_default)
            $errors[] = $this->l('Shop name is required');
        $shop_description_default = Tools::getValue('shop_description_' . $id_lang_default);
        if (!$shop_description_default)
            $errors[] = $this->l('Shop description is required');
        $shop_address_default = Tools::getValue('shop_address_' . $id_lang_default);
        if (!$shop_address_default)
            $errors[] = $this->l('Shop address is required');
        if (!($shop_phone = Tools::getValue('shop_phone')))
            $errors[] = $this->l('Shop phone number is required');
        elseif ($shop_phone && !Validate::isPhoneNumber($shop_phone))
            $errors[] = $this->l('Shop phone number is not valid');
        $longitude = Tools::getValue('longitude');
        $latitude = Tools::getValue('latitude');
        if ($longitude || $latitude) {
            if (!$longitude)
                $errors[] = $this->l('Longitude is required');
            elseif (!Validate::isFloat($longitude))
                $errors[] = $this->l('Longitude is not valid');
            if (!$latitude)
                $errors[] = $this->l('Latitude is required');
            elseif (!Validate::isFloat($latitude))
                $errors[] = $this->l('Latitude is not valid');
        }
        if ($admin) {
            $active = (int)Tools::getValue('active');
            if ($active == 0 || $active == -3) {
                $reason = Tools::getValue('reason');
                if (!Validate::isCleanHtml($reason))
                    $errors[] = $this->l('Reason is not valid');
            }
            $commission_rate = Tools::getValue('commission_rate');
            if ($commission_rate) {
                if (!Validate::isPrice($commission_rate))
                    $errors[] = $this->l('Commission rate is invalid');
                elseif ($commission_rate <= 0 || $commission_rate > 100)
                    $errors[] = $this->l('Commission rate must be between 0% and 100%');
            }
            $enable_commission_by_category = (int)Tools::getValue('enable_commission_by_category');
            $rate_categories = Tools::getValue('rate_category');
            $categories = array();
            if ($enable_commission_by_category && $rate_categories) {
                foreach ($rate_categories as $id_category => $rate) {
                    if (trim($rate) != '') {
                        if (!Validate::isFloat($rate)) {
                            if (!isset($categories[$id_category])) {
                                $categories[$id_category] = new Category($id_category, $this->context->language->id);
                                $errors[] = sprintf($this->l('Commission rate by category %s is not valid'), $categories[$id_category]->name);
                            }
                        } elseif (Validate::isFloat($rate) && ($rate > 100 || $rate <= 0)) {
                            if (!isset($categories[$id_category]))
                                $categories[$id_category] = new Category($id_category, $this->context->language->id);
                            $errors[] = sprintf($this->l('Commission rate by category %s must be between 0%s and 100%s'), $categories[$id_category]->name, '%', '%');
                        }
                    }
                }
            }
            $auto_enabled_product = Tools::getValue('auto_enabled_product');
            if (!in_array($auto_enabled_product, array('default', 'yes', 'no')))
                $errors[] = $this->l('Auto approve products submitted by this seller is not valid');
            if (($vat_number = Tools::getValue('vat_number')) && !Validate::isGenericName($vat_number))
                $errors[] = $this->l('VAT number is invalid');
            $date_from = Tools::getValue('date_from');
            $date_to = Tools::getValue('date_to');
            if ($date_from && !Validate::isDate($date_from))
                $errors[] = $this->l('Available from is not valid');
            if ($date_to && !Validate::isDate($date_to))
                $errors[] = $this->l('Available to is not valid');
            if ($date_to && $date_from && Validate::isDate($date_to) && Validate::isDate($date_from) && strtotime($date_from) >= strtotime($date_to))
                $errors[] = $this->l('"From" date must be smaller than "To" date');
        }
        foreach ($seller_fields as $key => $seller_field) {
            $value = Tools::getValue($key);
            if (in_array($key, array('shop_name', 'shop_description', 'shop_address', 'banner_url'))) {
                foreach ($languages as $language) {
                    if (($value = Tools::getValue($key . '_' . $language['id_lang'])) && ($key == 'banner_url' ? !Ets_marketplace::isLink($value) : !Validate::isCleanHtml($value)))
                        $errors[] = sprintf($this->l('%s is not valid in %s'), $seller_field, $language['iso_code']);
                    $valueFieldPost[$key][$language['id_lang']] = Tools::getValue($key . '_' . $language['id_lang']);
                }
            } else {
                if (in_array($key, array('link_facebook', 'link_google', 'link_instagram', 'link_twitter'))) {
                    if ($value && !Ets_marketplace::isLink($value))
                        $errors[] = sprintf($this->l('%s is not valid'), $seller_field);
                } elseif (in_array($key, array('longitude', 'latitude'))) {
                    if ($value && !Validate::isCoordinate($value))
                        $errors[] = sprintf($this->l('%s is not valid'), $seller_field);
                } elseif ($key != 'shop_logo' && $key != 'shop_banner' && $key != 'seller_email' && $key != 'vat_number' && $value && !Validate::isCleanHtml($value))
                    $errors[] = sprintf($this->l('%s is not valid'), $seller_field);
                elseif ($key == 'seller_email' && $value && !Validate::isEmail($value))
                    $errors[] = sprintf($this->l('%s is not valid'), $seller_field);
                elseif ($key == 'vat_number' && $value && !Validate::isGenericName($value))
                    $errors[] = $this->l('VAT number is not valid');
                $valueFieldPost[$key] = $value;
            }
        }
        if (!$errors) {
            if ($id_seller) {
                $seller = new Ets_mp_seller($id_seller);
            } else {
                $seller = new Ets_mp_seller();
                $seller->date_add = date('Y-m-d H:i:s');
                $seller->id_customer = $this->context->customer->id;
                $seller->id_shop = $this->context->shop->id;
                $seller->date_add = date('Y-m-d H:i:s');
                $seller->id_group = (int)Configuration::get('ETS_MP_SELLER_GROUP_DEFAULT');
                $seller->auto_enabled_product = 'default';
                if ($seller->getFeeType() != 'no_fee') {
                    $seller->active = -1;
                    $seller->payment_verify = -1;
                } else {
                    $seller->payment_verify = 0;
                    if (Configuration::get('ETS_MP_ENABLED_IF_NO_FEE'))
                        $seller->active = 1;
                    else
                        $seller->active = -1;
                }
                $registration = Ets_mp_registration::_getRegistration($this->context->customer->id);
                $seller->shop_logo =$registration->shop_logo;
                foreach ($languages as $language)
                    $seller->shop_banner[$language['id_lang']] = $registration->shop_banner;
            }
            $seller->date_upd = date('Y-m-d H:i:s');
            foreach (array_keys($seller_fields) as $field) {

                if (in_array($field, array('shop_name', 'shop_description', 'shop_address', 'banner_url'))) {
                    $field_value_default = Tools::getValue($field . '_' . $id_lang_default);
                    foreach ($languages as $language) {
                        $field_value = Tools::getValue($field . '_' . $language['id_lang']);
                        $seller->{$field}[$language['id_lang']] = $field_value ?: $field_value_default;
                    }
                } else {
                    $field_value = Tools::getValue($field);
                    if ($field != 'shop_logo' && $field != 'shop_banner') {
                        if (Tools::isSubmit($field))
                            $seller->{$field} = $field_value;
                    } else {
                        if ($field == 'shop_logo') {
                            if (isset($_FILES['shop_logo']['name']) && $_FILES['shop_logo']['name']) {
                                $logo = $this->uploadFile('shop_logo', $errors);
                                if ($logo) {
                                    $logo_old = $seller->shop_logo;
                                    $seller->shop_logo = $logo;
                                }
                            } elseif (!$seller->shop_logo)
                                $errors[] = $this->l('Shop logo is required');
                        }
                        if ($field == 'shop_banner') {
                            $shop_banner_news = array();
                            $shop_banner_olds = array();
                            foreach ($languages as $language) {
                                if (isset($_FILES['shop_banner_' . $language['id_lang']]['name']) && $_FILES['shop_banner_' . $language['id_lang']]['name']) {
                                    $shop_banner_news[$language['id_lang']] = $this->uploadFile('shop_banner_' . $language['id_lang'], $errors);
                                    if ($shop_banner_news[$language['id_lang']]) {
                                        $shop_banner_olds[$language['id_lang']] = $seller->shop_banner[$language['id_lang']];
                                        $seller->shop_banner[$language['id_lang']] = $shop_banner_news[$language['id_lang']];
                                    }
                                }
                            }
                            foreach ($languages as $language) {
                                if (!$seller->shop_banner[$language['id_lang']])
                                    $seller->shop_banner[$language['id_lang']] = $seller->shop_banner[$id_lang_default];
                            }
                        }

                    }
                }
            }
            if ($admin) {
                $seller->date_from = $date_from;
                $seller->date_to = $date_to;
                $code_chat = Tools::getValue('code_chat');
                $seller->code_chat = $code_chat;
                $active_old = (int)$seller->active;
                $id_group = (int)Tools::getValue('id_group');
                $seller->id_group = (int)$id_group;
                $seller->commission_rate = $commission_rate ? (float)$commission_rate : null;
                $seller->enable_commission_by_category = (int)$enable_commission_by_category;
                $seller->auto_enabled_product = $auto_enabled_product;
                if ($active == 0 || $active == -3) {
                    $seller->active = $active;
                    $seller->reason = $reason;
                } elseif ($active == -1)
                    $seller->active = $active;
                else {
                    if ((!$seller->date_from || strtotime($seller->date_from) <= strtotime(date('Y-m-d'))) && (!$seller->date_to || strtotime($seller->date_to) >= strtotime(date('Y-m-d')))) {
                        $seller->active = 1;
                        $seller->mail_expired = 0;
                        $seller->mail_going_to_be_expired = 0;
                    } else {
                        $seller->active = -2;
                        $seller->payment_verify = -1;
                    }
                }
            }
            if (!$errors) {
                if ($seller->id) {
                    if ($seller->update(true)) {
                        if ($admin) {
                            $seller->updateCommission($rate_categories);
                            if ($seller->active != $active_old && $seller->active == -2) {
                                $fee_type = $seller->getFeeType();
                                if ($fee_type != 'no_fee') {
                                    $billing = new Ets_mp_billing();
                                    $billing->id_customer = $seller->id_customer;
                                    $billing->amount = (float)$seller->getFeeAmount();
                                    $billing->amount_tax = $this->getFeeIncludeTax($billing->amount, $seller);
                                    $billing->active = 0;
                                    $billing->date_from = $seller->date_to;
                                    if ($fee_type == 'monthly_fee')
                                        $billing->date_to = date("Y-m-d H:i:s", strtotime($seller->date_to . "+1 month"));
                                    elseif ($fee_type == 'quarterly_fee')
                                        $billing->date_to = date("Y-m-d H:i:s", strtotime($seller->date_to . "+3 month"));
                                    elseif ($fee_type == 'yearly_fee')
                                        $billing->date_to = date("Y-m-d H:i:s", strtotime($seller->date_to . "+1 year"));
                                    else
                                        $billing->date_to = '';
                                    $billing->fee_type = $fee_type;
                                    if ($billing->add(true, true)) {
                                        $seller->id_billing = $billing->id;
                                        $seller->update();
                                    }
                                }
                            }
                        }
                        $registration = Ets_mp_registration::_getRegistration($seller->id_customer);
                        if (isset($logo_old) && $logo_old && file_exists(_PS_IMG_DIR_ . 'mp_seller/' . $logo_old) && (!$registration || $registration->shop_logo != $logo_old))
                            @unlink(_PS_IMG_DIR_ . 'mp_seller/' . $logo_old);
                        if (isset($shop_banner_olds) && $shop_banner_olds) {
                            foreach ($shop_banner_olds as $shop_banner_old) {
                                if (!in_array($shop_banner_old, $seller->shop_banner) && (!$registration || $registration->shop_banner != $shop_banner_old) && file_exists(_PS_IMG_DIR_ . 'mp_seller/' . $shop_banner_old))
                                    @unlink(_PS_IMG_DIR_ . 'mp_seller/' . $shop_banner_old);
                            }
                        }
                        $this->context->cookie->success_message = $this->l('Updated shop successfully');
                    } else {
                        $this->_errors[] = $this->l('An error occurred while saving the shop');
                        if (isset($logo) && $logo && file_exists(_PS_IMG_DIR_ . 'mp_seller/' . $logo))
                            @unlink(_PS_IMG_DIR_ . 'mp_seller/' . $logo);
                        if (isset($shop_banner_news) && $shop_banner_news) {
                            foreach ($shop_banner_news as $shop_banner) {
                                if (file_exists(_PS_IMG_DIR_ . 'mp_seller/' . $shop_banner))
                                    @unlink(_PS_IMG_DIR_ . 'mp_seller/' . $shop_banner);
                            }
                        }
                    }
                } else {
                    if ($seller->add(true, true)) {
                        $registration = Ets_mp_registration::_getRegistration($seller->id_customer);
                        if (isset($logo_old) && $logo_old && file_exists(_PS_IMG_DIR_ . 'mp_seller/' . $logo_old) && $registration->shop_logo != $logo_old) {
                            @unlink(_PS_IMG_DIR_ . 'mp_seller/' . $logo_old);
                        }
                        if (isset($shop_banner_olds) && $shop_banner_olds) {
                            foreach ($shop_banner_olds as $idLang => $shop_banner_old) {
                                if (!in_array($shop_banner_old, $seller->shop_banner) && $seller->shop_banner[$idLang] != $shop_banner_old && file_exists(_PS_IMG_DIR_ . 'mp_seller/' . $shop_banner_old))
                                    @unlink(_PS_IMG_DIR_ . 'mp_seller/' . $shop_banner_old);
                            }
                        }
                        $fee_type = $seller->getFeeType();
                        if ($fee_type != 'no_fee') {
                            $billing = new Ets_mp_billing();
                            $billing->id_customer = $seller->id_customer;
                            $billing->amount = (float)$seller->getFeeAmount();
                            $billing->amount_tax = $this->getFeeIncludeTax($billing->amount, $seller);
                            $billing->active = 0;
                            $billing->date_from = date('Y-m-d');
                            if ($fee_type == 'monthly_fee')
                                $billing->date_to = date("Y-m-d", strtotime(date('Y-m-d') . "+1 month"));
                            elseif ($fee_type == 'quarterly_fee')
                                $billing->date_to = date("Y-m-d", strtotime(date('Y-m-d') . "+3 month"));
                            elseif ($fee_type == 'yearly_fee')
                                $billing->date_to = date("Y-m-d", strtotime(date('Y-m-d') . "+1 year"));
                            else
                                $billing->date_to = '';
                            $billing->fee_type = $fee_type;
                            if ($billing->add(true, true)) {
                                $seller->id_billing = $billing->id;
                                $seller->update();
                            }
                            $message = Configuration::get('ETS_MP_MESSAGE_CREATED_SHOP_FEE_REQUIRED', $this->context->language->id) ?: $this->l('Thanks for creating your shop. Please send the fee [fee_amount] right now to activate your shop and click on the button "I have just sent the fee" after making payment. [payment_information_manager]');
                            $str_search = array(
                                '[fee_amount]',
                                '[manager_email]',
                                '[payment_information_manager]',
                                '[manager_phone]',
                            );
                            $str_replace = array(
                                Tools::displayPrice($billing->amount_tax, new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))) . ' (' . $this->l('Tax incl') . ')',
                                Configuration::get('ETS_MP_EMAIL _ADMIN_NOTIFICATION') ?: Configuration::get('PS_SHOP_EMAIL'),
                                $this->_replaceTag(Configuration::get('ETS_MP_SELLER_PAYMENT_INFORMATION', $this->context->language->id)),
                                Configuration::get('PS_SHOP_PHONE'),
                            );
                            $message = str_replace($str_search, $str_replace, $message);
                        } else {
                            if ($seller->active == 1)
                                $message = Configuration::get('ETS_MP_MESSAGE_SHOP_ACTIVED', $this->context->language->id) ?: $this->l('Congratulations! Your shop has been activated. You can upload products and start selling them');
                            else
                                $message = Configuration::get('ETS_MP_MESSAGE_CREATED_SHOP_NO_FEE', $this->context->language->id) ?: $this->l('Thanks for creating your shop. Our team are reviewing it. We will get back to you soon');

                        }
                        $this->context->cookie->success_message = str_replace("\n", Module::getInstanceByName('ets_marketplace')->displayText('', 'br'), $message);
                        if (Configuration::get('ETS_MP_EMAIL_ADMIN_SHOP_CREATED')) {
                            $data = array(
                                '{seller_name}' => $this->context->customer->firstname . ' ' . $this->context->customer->lastname,
                                '{shop_seller_name}' => $seller->shop_name[$this->context->language->id],
                                '{shop_description}' => $seller->shop_description[$this->context->language->id],
                                '{shop_address}' => $seller->shop_address[$this->context->language->id],
                                '{shop_phone}' => $seller->shop_phone,
                            );
                            $subjects = array(
                                'translation' => $this->l('New shop has been created'),
                                'origin' => 'New shop has been created',
                                'specific' => 'create'
                            );
                            Ets_marketplace::sendMail('to_admin_shop_created', $data, '', $subjects);

                        }
                    } else {
                        $errors[] = $this->l('An error occurred while creating the shop');
                        if (isset($logo) && $logo && file_exists(_PS_IMG_DIR_ . 'mp_seller/' . $logo))
                            @unlink(_PS_IMG_DIR_ . 'mp_seller/' . $logo);
                        if (isset($shop_banner_news) && $shop_banner_news) {
                            foreach ($shop_banner_news as $shop_banner) {
                                if (file_exists(_PS_IMG_DIR_ . 'mp_seller/' . $shop_banner))
                                    @unlink(_PS_IMG_DIR_ . 'mp_seller/' . $shop_banner);
                            }
                        }
                    }
                }
            } else {
                if (isset($logo) && $logo && file_exists(_PS_IMG_DIR_ . 'mp_seller/' . $logo))
                    @unlink(_PS_IMG_DIR_ . 'mp_seller/' . $logo);
                if (isset($shop_banner_news) && $shop_banner_news) {
                    foreach ($shop_banner_news as $shop_banner) {
                        if (file_exists(_PS_IMG_DIR_ . 'mp_seller/' . $shop_banner))
                            @unlink(_PS_IMG_DIR_ . 'mp_seller/' . $shop_banner);
                    }
                }
            }
        }
    }
    public function renderBilling($id_customer=0)
    {
        $id_ets_mp_seller_billing =(int)Tools::getValue('id_ets_mp_seller_billing');
        $controller = Tools::getValue('controller');
        if(!Validate::isControllerName($controller))
            $controller='';
        $id_seller = (int)Tools::getValue('id_seller');
        if((Tools::isSubmit('cancelms_billings') || Tools::isSubmit('purchasems_billings') || Tools::isSubmit('del')) && $id_ets_mp_seller_billing && Validate::isUnsignedId($id_ets_mp_seller_billing))
        {
            $billing_class = new Ets_mp_billing($id_ets_mp_seller_billing);
            if(Tools::isSubmit('del'))
            {
                if(Validate::isLoadedObject($billing_class) && $billing_class->delete())
                    $this->context->cookie->success_message = $this->l('Deleted successfully');
                else
                    $this->_errors[] = $this->l('An error occurred while deleting the memberships');
            }
            if(Tools::isSubmit('cancelms_billings'))
            {
                $billing_class->active=-1;
                if(Validate::isLoadedObject($billing_class) && $billing_class->update(true))
                    $this->context->cookie->success_message = $this->l('Canceled successfully');
                else
                    $this->_errors[] = $this->l('An error occurred while saving the memberships');
            }
            if(Tools::isSubmit('purchasems_billings'))
            {
                $billing_class->active=1;
                $used = $billing_class->used;
                $billing_class->used=1;
                if(Validate::isLoadedObject($billing_class) && $billing_class->update(true))
                {
                    $this->context->cookie->success_message = $this->l('Set as paid successfully');
                    $seller = Ets_mp_seller::_getSellerByIdCustomer($billing_class->id_customer);
                    if(!$used)
                    {
                        $seller->payment_verify =0;
                        $seller->update();
                    }
                    if(!$used && Configuration::get('ETS_MP_APPROVE_AUTO_BY_BILLING'))
                    {
                        if($seller->active!=0)
                        {
                            if($seller->date_to || $seller->active==-1)
                            {
                                $seller->date_from = $seller->date_to && strtotime($seller->date_to) < strtotime(date('Y-m-d H:i:s')) ? $seller->date_to : date('Y-m-d H:i:s');
                                if($seller->active==-1 || ($seller->date_to && strtotime($seller->date_to) < strtotime(date('Y-m-d H:i:s'))))
                                {
                                    $date_add = date('Y-m-d H:i:s');
                                }
                                else
                                {
                                    $date_add = $seller->date_to;
                                }
                                if($billing_class->fee_type=='monthly_fee')
                                    $seller->date_to = date("Y-m-d H:i:s", strtotime($date_add."+1 month"));
                                elseif($billing_class->fee_type=='quarterly_fee')
                                    $seller->date_to = date("Y-m-d H:i:s", strtotime($date_add."+3 month"));
                                elseif($billing_class->fee_type=='yearly_fee')
                                    $seller->date_to = date("Y-m-d H:i:s", strtotime($date_add."+1 year"));
                                else
                                    $seller->date_to =null;
                                if((!$seller->date_from || strtotime($seller->date_from) <= strtotime(date('Y-m-d H:i:s'))) && (!$seller->date_to || strtotime($seller->date_to) >= strtotime(date('Y-m-d H:i:s'))))
                                    $seller->active=1;
                            }
                        }
                        $seller->update(true);
                    }

                }
                else
                    $this->_errors[] = $this->l('An error occurred while saving the memberships');
            }
        }
        $fields_list = array(
            'id_ets_mp_seller_billing' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'reference' => array(
                'title' => $this->l('Reference'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'seller_name' => array(
                'title' => $this->l('Seller name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
            ),
            'shop_name' => array(
                'title' => $this->l('Shop name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
            ),
            'amount_tax' => array(
                'title' => $this->l('Amount (Tax incl.)'),
                'type' => 'int',
                'sort' => true,
                'filter' => true,
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'sort' => true,
                'filter' => true,
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>-1,
                            'value' => $this->l('Canceled'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->l('Pending'),
                        ),
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Paid'),
                        )
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),
            ),
            'note' => array(
                'title' => $this->l('Description'),
                'type'=>'text',
                'sort'=>false,
                'filter'=>false,
                'strip_tag'=>false,
            ),
            'by_admin' => array(
                'title' => $this->l('Invoice type'),
                'type'=>'select',
                'sort'=>true,
                'filter'=>true,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>0,
                            'value' => $this->l('Auto'),
                        ),
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Manually'),
                        )
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),
            ),
            'date_add' => array(
                'title' => $this->l('Date of invoice'),
                'type' => 'date',
                'sort' => true,
                'filter' => true
            ),
            'date_due' => array(
                'title' => $this->l('Due date'),
                'type' => 'date',
                'sort' => true,
                'filter' => true
            ),
            'pdf' => array(
                'title' => $this->l('PDF','billing'),
                'type' => 'text',
                'sort' => false,
                'filter' => false,
                'strip_tag' => false,
            ),
        );
        //Filter
        $show_resset = false;
        $filter = "";
        $having = "";
        if($id_customer)
        {
            $filter .=' AND b.id_customer='.(int)$id_customer;
            unset($fields_list['seller_name']);
            unset($fields_list['shop_name']);
        }
        if(Tools::isSubmit('ets_mp_submit_ms_billings'))
        {
            if($id_ets_mp_seller_billing)
            {
                if(Validate::isUnsignedId($id_ets_mp_seller_billing))
                    $filter .=' AND b.id_ets_mp_seller_billing ="'.(int)$id_ets_mp_seller_billing.'"';
                $show_resset=true;
            }
            if(($seller_name = Tools::getValue('seller_name')) || $seller_name!='')
            {
                if(Validate::isCleanHtml($seller_name))
                    $filter .= ' AND CONCAT(customer.firstname," ",customer.lastname) like "%'.pSQL($seller_name).'%"';
                $show_resset =true;
            }
            if(($shop_name = Tools::getValue('shop_name')) || $shop_name!='')
            {
                if(Validate::isCleanHtml($shop_name))
                    $filter .= ' AND seller_lang.shop_name like "%'.pSQL($shop_name).'%"';
                $show_resset = true;
            }
            if(($amount_min = trim(Tools::getValue('amount_min'))) || $amount_min!='')
            {
                if(Validate::isFloat($amount_min))
                    $filter .= ' AND b.amount >= "'.(float)$amount_min.'"';
                $show_resset = true;
            }
            if(($amount_max = trim(Tools::getValue('amount_max'))) || $amount_max!='')
            {
                if(Validate::isFloat($amount_max))
                    $filter .= ' AND b.amount <="'.(float)$amount_max.'"';
                $show_resset = true;
            }
            if(($active = trim(Tools::getValue('active'))) || $active!=='')
            {
                if(Validate::isInt($active))
                    $filter .= ' AND b.active="'.(int)$active.'"';
                $show_resset = true;
            }
            if(($date_add_min = trim(Tools::getValue('date_add_min'))) || $date_add_min!='')
            {
                if(Validate::isDate($date_add_min))
                    $filter .= ' AND b.date_add >="'.pSQL($date_add_min).' 00:00:00"';
                $show_resset = true;
            }
            if(($date_add_max = trim(Tools::getValue('date_add_max'))) || $date_add_max!='')
            {
                if(Validate::isDate($date_add_max))
                    $filter .= ' AND b.date_add <="'.pSQL($date_add_max).' 23:59:59"';
                $show_resset = true;
            }
            if(($date_due_min =trim(Tools::getValue('date_due_min'))) || $date_due_min!='')
            {
                if(Validate::isDate($date_due_min))
                    $having .= ' AND date_due!="" AND date_due >="'.pSQL($date_due_min).' 00:00:00"';
                $show_resset = true;
            }
            if(($date_due_max = trim(Tools::getValue('date_due_max'))) || $date_due_max!='')
            {
                if(Validate::isDate($date_due_max))
                    $having .= ' AND date_due!="" AND date_due <="'.pSQL($date_due_max).' 23:59:59"';
                $show_resset = true;
            }
            if(($note = trim(Tools::getValue('note'))) || $note!='')
            {
                if(Validate::isCleanHtml($note))
                    $filter .= ' AND b.note LIKE "%'.pSQL($note).'%"';
                $show_resset=true;
            }
            if(($by_admin = trim(Tools::getValue('by_admin'))) || $by_admin!='')
            {
                $show_resset=true;
                if($by_admin)
                    $filter .=' AND b.id_employee!=0';
                else
                    $filter .=' AND b.id_employee=0';
            }
            if(($reference = trim(Tools::getValue('reference'))) || $reference!='')
            {
                if(Validate::isCleanHtml($reference))
                    $filter .=' AND b.reference LIKE "'.pSQL($reference).'%"';
                $show_resset = true;
            }
        }
        //Sort
        $sort = "";
        $sort_type=Tools::getValue('sort_type','desc');
        $sort_value = Tools::getValue('sort','id_ets_mp_seller_billing');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'id_ets_mp_seller_billing':
                    $sort .='b.id_ets_mp_seller_billing';
                    break;
                case 'seller_name':
                    $sort .='seller_name';
                    break;
                case 'shop_name':
                    $sort .='seller_lang.shop_name';
                    break;
                case 'amount_tax':
                    $sort .='b.amount';
                    break;
                case 'active':
                    $sort .='b.active';
                    break;
                case 'date_add':
                    $sort .='b.date_add';
                    break;
                case 'date_due':
                    $sort .='date_due';
                    break;
                case 'by_admin':
                    $sort .='b.id_employee';
                    break;
                case 'note':
                    $sort .='b.note';
                    break;
                case 'reference':
                    $sort .='b.reference';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                $sort .= ' '.trim($sort_type);
        }
        //Paggination
        $page = (int)Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $totalRecords = (int)Ets_mp_billing::getSellerBillings($filter,$having,0,0,'',true);
        $paggination = new Ets_mp_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getAdminLink($controller).'&page=_page_'.(Tools::isSubmit('viewseller') && $id_seller ? '&viewseller=1&id_seller='.(int)$id_seller:'').$this->getFilterParams($fields_list,'ms_billings');
        $paggination->limit =  (int)Tools::getValue('paginator_billing_select_limit',20);
        $paggination->name ='billing';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $billings = Ets_mp_billing::getSellerBillings($filter,$having, $start,$paggination->limit,$sort,false);
        if($billings)
        {
            foreach($billings as &$billing)
            {
                $billing['amount'] = Tools::displayPrice($billing['amount'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));
                $billing['amount_tax'] = Tools::displayPrice($billing['amount_tax'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));
                if($billing['active']==0)
                {
                    $billing['active'] = $this->displayText($this->l('Pending'),'span','ets_mp_status pending');
                }
                elseif($billing['active']==1)
                {
                    $billing['active'] = $this->displayText($this->l('Paid'),'span','ets_mp_status purchased');
                }
                elseif($billing['active']==-1)
                {
                    $billing['active'] = $this->displayText($this->l('Canceled'),'span','ets_mp_status deducted');
                }
                if($billing['id_seller'])
                {
                    $billing['shop_name'] = $this->displayText($billing['shop_name'],'a','','',$this->getShopLink(array('id_seller'=>$billing['id_seller'])));
                }
                else
                {
                    $billing['shop_name']= $this->displayText($this->l('Shop deleted'),'span','deleted_shop row_deleted');
                }
                if($billing['id_customer_seller'])
                {
                    $billing['seller_name'] = $this->displayText($billing['seller_name'],'a','','',$this->getLinkCustomerAdmin($billing['id_customer_seller']));
                }
                else
                    $billing['seller_name'] = $this->displayText($this->l('Seller deleted'),'span','row_deleted');
                if($billing['id_employee'])
                    $billing['by_admin'] = $this->l('Manually');
                else
                    $billing['by_admin'] = $this->l('Auto');
                $billing['billing_number'] ='#BL';
                while(Tools::strlen($billing['billing_number'].$billing['id_ets_mp_seller_billing'])<8)
                    $billing['billing_number'] .='0';
                $billing['billing_number'] .= $billing['id_ets_mp_seller_billing'];
                if(!$billing['id_employee'])
                {
                    if($billing['fee_type']=='pay_once')
                        $billing['note'] = $this->l('Pay once');
                    if($billing['fee_type']=='monthly_fee')
                        $billing['note'] = $this->l('Monthly fee:').$this->displayText('','br').$this->l('From').' '.Tools::displayDate($billing['date_from']).' '.$this->l('To'). ' '.Tools::displayDate($billing['date_to']);
                    if($billing['fee_type']=='quarterly_fee')
                        $billing['note'] = $this->l('Quarterly fee:').$this->displayText('','br').$this->l('From').' '.Tools::displayDate($billing['date_from']).' '.$this->l('To'). ' '.Tools::displayDate($billing['date_to']);
                    if($billing['fee_type']=='yearly_fee')
                        $billing['note'] = $this->l('Yearly fee:').$this->displayText('','br').$this->l('From').' '.Tools::displayDate($billing['date_from']).' '.$this->l('To'). ' '.Tools::displayDate($billing['date_to']);
                }
                else
                    $billing['note'] .= (trim($billing['note']) ? $this->displayText('','br'):'').($billing['date_from'] && $billing['date_from']!='0000-00-00' ? $this->l('From').' '.Tools::displayDate($billing['date_from']).' ' :'' ). ($billing['date_to'] && $billing['date_to']!='0000-00-00' ? $this->l('To').' '.Tools::displayDate($billing['date_to']) :'' );
                $billing['pdf'] = $this->displayText($this->displayText('','i','icon-pdf icon icon-pdf fa fa-file-pdf-o'),'a','ets_mp_downloadpdf','',$this->context->link->getAdminLink('AdminMarketPlaceBillings').'&id_ets_mp_seller_billing='.(int)$billing['id_ets_mp_seller_billing'].'&dowloadpdf=yes"');
                if(!$billing['date_due'])
                    $billing['date_due'] ='--';
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $listData = array(
            'name' => 'ms_billings',
            'icon' => 'fa fa-bank',
            'actions' => array('purchased','delete'),
            'currentIndex' => $this->context->link->getAdminLink($controller).(Tools::isSubmit('viewseller') && $id_seller ? '&viewseller=1&id_seller='.(int)$id_seller:'').($paggination->limit!=20 ? '&paginator_billing_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getAdminLink($controller).(Tools::isSubmit('viewseller') && $id_seller ? '&viewseller=1&id_seller='.(int)$id_seller:''),
            'identifier' => 'id_ets_mp_seller_billing',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->l('Membership'),
            'fields_list' => $fields_list,
            'field_values' => $billings,
            'paggination' => $paggination->render(),
            'filter_params' => $this->getFilterParams($fields_list,'ms_billings'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'show_add_new'=> true,
            'link_new' => $this->context->link->getAdminLink('AdminMarketPlaceBillings').'&addnewbillng=1',
            'sort_type' => $sort_type,
        );
        return $this->renderList($listData);
    }
    public function renderCommission($id_customer = false)
    {
        $id_commission = (int)Tools::getValue('id_commission');
        $controller = Tools::getValue('controller');
        if (!Validate::isControllerName($controller))
            $controller = '';
        $id_seller = (int)Tools::getValue('id_seller');
        $type = Tools::getValue('type');
        if ($type && !in_array($type, array('commission', 'usage')))
            $type = 'commission';
        if ((Tools::isSubmit('cancelms_commissions') || Tools::isSubmit('approvems_commissions') || (Tools::isSubmit('del') && $type == 'commission')) && $id_seller_commission = (int)$id_commission) {
            $commission = new Ets_mp_commission($id_seller_commission);
            if (Tools::isSubmit('del')) {
                if (Validate::isLoadedObject($commission) &&  $commission->delete()) {
                    $this->context->cookie->success_message = $this->l('Deleted successfully');
                }
                else
                    $this->_errors[] = $this->l('An error occurred while deleting the commision');
            }
            if (Tools::isSubmit('cancelms_commissions')) {
                $commission->status = 0;
                if (Validate::isLoadedObject($commission) && $commission->update()) {
                    $this->context->cookie->success_message = $this->l('Canceled successfully');
                    $this->context->cookie->write();
                }
                else
                    $this->_errors[] = $this->l('An error occurred while saving the commision');
            }
            if (Tools::isSubmit('approvems_commissions')) {
                $commission->status = 1;
                if (Validate::isLoadedObject($commission) && $commission->update()) {
                    $this->context->cookie->success_message = $this->l('Approved successfully');
                    $this->context->cookie->write();

                }
                else
                    $this->_errors[] = $this->l('An error occurred while saving the commision');
            }
            Tools::redirectAdmin($this->context->link->getAdminLink($controller) . (Tools::isSubmit('viewseller') ? '&viewseller=1&id_seller=' . (int)$id_seller : ''));
        }
        if ((Tools::isSubmit('returnms_commissions') || Tools::isSubmit('deductms_commissions') || (Tools::isSubmit('del') && $type == 'usage')) && $id_ets_mp_commission_usage = (int)$id_commission) {
            $commission_ugage = new Ets_mp_commission_usage($id_ets_mp_commission_usage);
            if (Tools::isSubmit('del')) {
                if (Validate::isLoadedObject($commission_ugage) && $commission_ugage->delete())
                    $this->context->cookie->success_message = $this->l('Deleted successfully');
                else
                    $this->_errors[] = $this->l('An error occurred while deleting the commision ugage');
            }
            if (Tools::isSubmit('returnms_commissions')) {
                $commission_ugage->status = 0;
                if (Validate::isLoadedObject($commission_ugage) && $commission_ugage->update())
                    $this->context->cookie->success_message = $this->l('Returned successfully');
                else
                    $this->_errors[] = $this->l('An error occurred while saving the commision ugage');
            }
            if (Tools::isSubmit('deductms_commissions')) {
                $commission_ugage->status = 1;
                if (Validate::isLoadedObject($commission_ugage) && $commission_ugage->update())
                    $this->context->cookie->success_message = $this->l('Deducted successfully');
                else
                    $this->_errors[] = $this->l('An error occurred while saving the commision ugage');
            }
            Tools::redirectAdmin($this->context->link->getAdminLink($controller) . (Tools::isSubmit('viewseller') ? '&viewseller=1&id_seller=' . (int)$id_seller : ''));
        }
        $commistion_status = array(
            array(
                'id' => '-1',
                'name' => $this->l('Pending')
            ),
            array(
                'id' => '0',
                'name' => $this->l('Canceled')
            ),
            array(
                'id' => '1',
                'name' => $this->l('Approved')
            ),
            array(
                'id' => 'refunded',
                'name' => $this->l('Refunded')
            ),
            array(
                'id' => 'deducted',
                'name' => $this->l('Deducted')
            ),
        );
        $fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'seller_name' => array(
                'title' => $this->l('Seller name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
            ),
            'id_order' => array(
                'title' => $this->l('Order ID'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'product_name' => array(
                'title' => $this->l('Product name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
            ),
            'price' => array(
                'title' => $this->l('Product price'),
                'type' => 'int',
                'sort' => true,
                'filter' => true,
            ),
            'quantity' => array(
                'title' => $this->l('Product quantity'),
                'type' => 'int',
                'sort' => true,
                'filter' => true,
            ),
            'commission' => array(
                'title' => $this->l('Commissions'),
                'type' => 'int',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
            ),
            'admin_earning' => array(
                'title' => $this->l('Admin earning'),
                'type' => 'int',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'list' => $commistion_status,
                    'id_option' => 'id',
                    'value' => 'name',
                ),
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'type' => 'date',
                'sort' => true,
                'filter' => true
            ),
        );
        //Filter
        $show_resset = false;
        $filter = "";
        $having = "";
        if ($id_customer) {
            $filter .= ' AND sc.id_customer=' . (int)$id_customer;
            unset($fields_list['seller_name']);
        }
        if (Tools::isSubmit('ets_mp_submit_ms_commissions')) {
            if ($id = Tools::getValue('id')) {
                if (Validate::isUnsignedId($id))
                    $filter .= ' AND sc.id="' . (int)$id . '"';
                $show_resset = true;
            }
            if ($id_order = (int)Tools::getValue('id_order')) {
                if (Validate::isUnsignedId($id_order))
                    $filter .= ' AND sc.id_order="' . (int)$id_order . '"';
                $show_resset = true;
            }
            if (($seller_name = Tools::getValue('seller_name')) || $seller_name != '') {
                if (Validate::isCleanHtml($seller_name))
                    $filter .= ' AND CONCAT(customer.firstname," ",customer.lastname) like "%' . pSQL($seller_name) . '%"';
                $show_resset = true;
            }
            if (($shop_name = Tools::getValue('shop_name')) || $shop_name != '') {
                if (Validate::isCleanHtml($shop_name))
                    $filter .= ' AND seller_lang.shop_name like "%' . pSQL($shop_name) . '%"';
                $show_resset = true;
            }
            if (($product_name = trim(Tools::getValue('product_name'))) || $product_name != '') {
                if (Validate::isCleanHtml($product_name))
                    $filter .= ' AND sc.product_name like "%' . pSQL($product_name) . '%"';
                $show_resset = true;
            }
            if (($price_min = trim(Tools::getValue('price_min'))) || $price_min != '') {
                if (Validate::isFloat($price_min))
                    $filter .= ' AND sc.price >= "' . (float)$price_min . '"';
                $show_resset = true;
            }
            if (($price_max = trim(Tools::getValue('price_max'))) || $price_max != '') {
                if (Validate::isFloat($price_max))
                    $filter .= ' AND sc.price <= "' . (float)$price_max . '"';
                $show_resset = true;
            }
            if (($quantity_min = trim(Tools::getValue('quantity_min'))) || $quantity_min != '') {
                if (Validate::isInt($quantity_min))
                    $filter .= ' AND sc.quantity <="' . (int)$quantity_min . '"';
                $show_resset = true;
            }
            if (($quantity_max = trim(Tools::getValue('quantity_max'))) || $quantity_max != '') {
                if (Validate::isInt($quantity_max))
                    $filter .= ' AND sc.quantity >="' . (int)$quantity_max . '"';
                $show_resset = true;
            }
            if (($commission_min = trim(Tools::getValue('commission_min'))) || $commission_min != '') {
                if (Validate::isFloat($commission_min))
                    $filter .= ' AND sc.commission >= "' . (float)$commission_min . '"';
                $show_resset = true;
            }
            if (($commission_max = trim(Tools::getValue('commission_max'))) || $commission_max != '') {
                if (Validate::isFloat($commission_max))
                    $filter .= ' AND sc.commission <="' . (float)$commission_max . '"';
                $show_resset = true;
            }
            if (($admin_earning_min = trim(Tools::getValue('admin_earning_min'))) || $admin_earning_min != '') {
                if (Validate::isFloat($admin_earning_min))
                    $having .= ' AND admin_earning_min >= "' . (float)$admin_earning_min . '"';
                $show_resset = true;
            }
            if (($admin_earning_max = trim(Tools::getValue('admin_earning_max'))) || $admin_earning_max != '') {
                if (Validate::isFloat($admin_earning_max))
                    $having .= ' AND admin_earning <="' . (float)$admin_earning_max . '"';
                $show_resset = true;
            }
            if (($status = trim(Tools::getValue('status'))) || $status !== '') {
                if ($status == 'refunded' || $status == 'deducted') {
                    $filter .= ' AND sc.type="usage" AND sc.status="' . ($status == 'refunded' ? 0 : 1) . '"';
                } else {
                    if (Validate::isInt($status))
                        $filter .= ' AND sc.type="commission" AND sc.status = "' . (int)$status . '"';
                }

                $show_resset = true;
            }
            if (($date_add_min = trim(Tools::getValue('date_add_min'))) || $date_add_min != '') {
                if (Validate::isDate($date_add_min))
                    $filter .= ' AND sc.date_add >="' . pSQL($date_add_min) . ' 00:00:00"';
                $show_resset = true;
            }
            if (($date_add_max = trim(Tools::getValue('date_add_max'))) || $date_add_max != '') {
                if (Validate::isDate($date_add_max))
                    $filter .= ' AND sc.date_add <="' . pSQL($date_add_max) . ' 23:59:59"';
                $show_resset = true;
            }
        }
        //Sort
        $sort = "";
        $sort_type = Tools::getValue('sort_type', 'desc');
        $sort_value = Tools::getValue('sort', 'date_add');
        if ($sort_value) {
            switch ($sort_value) {
                case 'id':
                    $sort .= 'sc.id';
                    break;
                case 'id_order':
                    $sort .= 'sc.id_order';
                    break;
                case 'seller_name':
                    $sort .= 'seller_name';
                    break;
                case 'shop_name':
                    $sort .= 'seller_lang.shop_name';
                    break;
                case 'product_name':
                    $sort .= 'sc.product_name';
                    break;
                case 'price':
                    $sort .= 'sc.price';
                    break;
                case 'quantity':
                    $sort .= 'sc.quantity';
                    break;
                case 'commission':
                    $sort .= 'sc.commission';
                    break;
                case 'admin_earning':
                    $sort .= 'admin_earning';
                    break;
                case 'date_add':
                    $sort .= 'sc.date_add';
                    break;
                case 'status':
                    $sort .= ' sc.status';
                    break;
            }
            if ($sort && $sort_type && in_array($sort_type, array('asc', 'desc')))
                $sort .= ' ' . trim($sort_type);
        }
        //Paggination
        $page = (int)Tools::getValue('page');
        if ($page <= 0)
            $page = 1;
        $totalRecords = (int)Ets_mp_commission::getSellerCommissions($filter, $having, 0, 0, '', true);
        $paggination = new Ets_mp_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getAdminLink($controller) . '&page=_page_' . (Tools::isSubmit('viewseller') && $id_seller ? '&viewseller=1&id_seller=' . (int)$id_seller : '') . $this->getFilterParams($fields_list, 'ms_commissions');
        $paggination->limit = (int)Tools::getValue('paginator_commission_select_limit', 20);
        $paggination->name = 'commission';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if ($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if ($start < 0)
            $start = 0;
        $commissions = Ets_mp_commission::getSellerCommissions($filter, $having, $start, $paggination->limit, $sort, false);
        if ($commissions) {
            foreach ($commissions as &$commission) {
                $commission['price'] = $commission['price_tax_incl'] != 0 ? Tools::displayPrice($commission['price_tax_incl']) : '';

                $commission['commission'] = Tools::displayPrice($commission['commission']);
                if ($commission['id_product'] && $commission['admin_earning'])
                    $commission['admin_earning'] = Tools::displayPrice($commission['admin_earning']);
                else
                    $commission['admin_earning'] = '';
                if ($commission['type'] == 'usage')
                    $commission['commission'] = $this->displayText('-' . $commission['commission'], 'span', 'ets_mp_commision_usage');
                if ($commission['note'])
                    $commission['commission'] .= $this->displayText('', 'br') . $this->displayText($commission['note'], 'i', '');
                $commission['status_val'] = $commission['status'];
                $commission['id_commission'] = $commission['id'];
                if ($commission['type'] == 'usage') {
                    $commission['id'] = 'U-' . $commission['id'];
                    if ($commission['status'] == 0)
                        $commission['status'] = $this->displayText($this->l('Refunded', 'commissions'), 'span', 'ets_mp_status refunded');
                    elseif ($commission['status'] == 1)
                        $commission['status'] = $this->displayText($this->l('Deducted', 'commissions'), 'span', 'ets_mp_status deducted');
                } else {
                    $commission['id'] = 'C-' . $commission['id'];
                    if ($commission['status'] == -1)
                        $commission['status'] = $this->displayText($this->l('Pending', 'commissions'), 'span', 'ets_mp_status pending');
                    elseif ($commission['status'] == 0)
                        $commission['status'] = $this->displayText($this->l('Canceled', 'commissions'), 'span', 'ets_mp_status canceled');
                    elseif ($commission['status'] == 1)
                        $commission['status'] = $this->displayText($this->l('Approved', 'commissions'), 'span', 'ets_mp_status approved');
                }
                if ($commission['id_product']) {
                    $commission['product_name'] = $commission['product_id'] ? $this->displayText($commission['product_name'], 'a', '', '', $this->context->link->getAdminLink('AdminProducts', true, array('id_product' => $commission['id_product']))) : $this->displayText($commission['product_name'], '', '');
                }
                if ($commission['id_customer_seller']) {
                    $commission['seller_name'] = $this->displayText($commission['seller_name'], 'a', '', '', $this->getLinkCustomerAdmin($commission['id_customer_seller']));
                } else
                    $commission['seller_name'] = $this->displayText($this->l('Seller deleted'), 'span', 'row_deleted');

            }
        }
        $paggination->text = $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $listData = array(
            'name' => 'ms_commissions',
            'actions' => array('approved', 'delete'),
            'icon' => 'fa fa-dollar',
            'currentIndex' => $this->context->link->getAdminLink($controller) . (Tools::isSubmit('viewseller') && $id_seller ? '&viewseller=1&id_seller=' . (int)$id_seller : '') . ($paggination->limit != 20 ? '&paginator_commission_select_limit=' . $paggination->limit : ''),
            'postIndex' => $this->context->link->getAdminLink($controller) . (Tools::isSubmit('viewseller') && $id_seller ? '&viewseller=1&id_seller=' . (int)$id_seller : ''),
            'identifier' => 'id_commission',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->l('Commissions'),
            'fields_list' => $fields_list,
            'field_values' => $commissions,
            'paggination' => $paggination->render(),
            'filter_params' => $this->getFilterParams($fields_list, 'ms_commissions'),
            'show_reset' => $show_resset,
            'totalRecords' => $totalRecords,
            'sort' => $sort_value,
            'show_add_new' => false,
            'sort_type' => $sort_type,
        );
        return $this->renderList($listData);
    }
    public function enable($force_all = false)
    {
        require_once(dirname(__FILE__) . '/classes/OverrideUtil.php');
        Ets_mp_overrideUtil::resolveConflict($this);
        return parent::enable($force_all);
    }
    public function disable($force_all = false)
    {
        require_once(dirname(__FILE__) . '/classes/OverrideUtil.php');
        $parentResult = parent::disable($force_all);
        Ets_mp_overrideUtil::restoreReplacedMethod($this);
        return $parentResult;
    }
}