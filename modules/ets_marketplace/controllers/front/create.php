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

if (!defined('_PS_VERSION_'))
	exit;

/**
 * Class Ets_MarketPlaceCreateModuleFrontController
 * @property \Ets_marketplace $module;
 *
 */
class Ets_MarketPlaceCreateModuleFrontController extends ModuleFrontController
{
    public $_success;
    public $_errors = array();
    public $_warning;
    public function __construct()
	{
		parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
	}
    public function postProcess()
    {
        parent::postProcess();
        if(Tools::isSubmit('i_have_just_sent_the_fee') && ($seller= $this->module->_getSeller()))
        {
            $seller->confirmedPayment();
        }
        if(!$this->context->customer->isLogged() || ((!($registration = Ets_mp_registration::_getRegistration()) || $registration->active!=1) && Configuration::get('ETS_MP_REQUIRE_REGISTRATION')))
            Tools::redirect($this->context->link->getPageLink('my-account'));
        if($this->module->_getSeller())
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(Tools::isSubmit('submitDeclinceManageShop') && ($id_manager = Ets_mp_manager::getIDMangerByEmail($this->context->customer->email)) && ($manager = new Ets_mp_manager($id_manager)) && Validate::isLoadedObject($manager) )
        {
            $manager->active=0;
            $manager->update();
            $this->_warning = $this->module->l('You have declined a shop management invitation. How about registering for your own shop?','create');
        }
        if(Tools::isSubmit('submitApproveManageShop') && ($id_manager = Ets_mp_manager::getIDMangerByEmail($this->context->customer->email)) && ($manager = new Ets_mp_manager($id_manager)) && Validate::isLoadedObject($manager))
        {
            $manager->active=1;
            $manager->update();
        }
        if(Tools::isSubmit('submitSaveSeller'))
        {
            $valueFieldPost = array();
            $this->module->submitSaveSeller(0,$this->_errors,false,$valueFieldPost);
            if($this->context->cookie->success_message)
            {
                $this->_success = $this->context->cookie->success_message;
                $this->context->cookie->success_message='';
            }
            $this->context->smarty->assign(
                array(
                    'valueFieldPost' => $valueFieldPost,
                )
            );
        }
    }
    public function initContent()
	{
		parent::initContent();
        $this->context->smarty->assign(
            array(
                'path' => $this->module->getBreadCrumb(),
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false, 
                'html_content' => $this->_initContent(),
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/create.tpl');      
        else        
            $this->setTemplate('create_16.tpl'); 
    }
    public function _initContent()
    {
        if(($id_manager = (int)Ets_mp_manager::getIDMangerByEmail($this->context->customer->email)) && ($manager = new Ets_mp_manager($id_manager)) && Validate::isLoadedObject($manager) && $manager->active!=0 )
        {
            $seller = Ets_mp_seller::_getSellerByIdCustomer($manager->id_customer,$this->context->language->id);
            $manager_shop = array(
                'firstname' => $seller->firstname,
                'lastname' => $seller->lastname,
                'shop_name' => $seller->shop_name,
                'active' => $manager->active,
            );
        }
        $address = new Address((int)Address::getFirstCustomerAddressId($this->context->customer->id));
        $this->context->smarty->assign(
            array(
                'seller' => Ets_mp_registration::_getRegistration(),
                '_errors' => $this->_errors ? $this->module->displayError($this->_errors):'',
                '_success' => $this->_success ? $this->module->displayConfirmation($this->_success):'',
                'shop_seller' => $this->module->_getSeller(),
                'create_customer' => $this->context->customer,
                'path' => $this->module->getBreadCrumb(),
                'link_base' => $this->module->getBaseLink(),
                'shop_name' => $this->context->shop->name,
                'languages' => Language::getLanguages(true),
                'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
                'manager_shop' => isset($manager_shop) ? $manager_shop :false,
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false, 
                'shop_categories' => Ets_mp_shop_category::getShopCategories(' AND c.active=1',0,false),
                'number_phone' => $address->phone ? : $address->phone_mobile,
                'vat_number' => $address->vat_number,
            )
        );
        return ($this->_warning ? $this->module->displayWarning($this->_warning):'').$this->module->displayTpl('create.tpl');
    }
}