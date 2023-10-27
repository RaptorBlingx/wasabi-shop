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
class Ets_MarketPlaceVacationModuleFrontController extends ModuleFrontController
{
    public $_errors= array();
    public $_success ='';
    public $seller;
    public function __construct()
	{
		parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
	}
    public function postProcess()
    {
        if(!Configuration::get('ETS_MP_VACATION_MODE_FOR_SELLER'))
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->context->customer->isLogged() || !($this->seller = $this->module->_getSeller(true)) )
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->module->_checkPermissionPage($this->seller))
            die($this->module->l('You do not have permission to access this page','vacation'));
        $valueFieldPost = array();
        $languages = Language::getLanguages(false);
        if(Tools::isSubmit('submitSaveVacationSeller'))
        {
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            $vacation_mode = (int)Tools::getValue('vacation_mode');
            $vacation_type = Tools::getValue('vacation_type');
            $date_vacation_start = trim(Tools::getValue('date_vacation_start'));
            $date_vacation_end = trim(Tools::getValue('date_vacation_end'));
            if(!in_array($vacation_type,array('show_notifications','disable_product','disable_product_and_show_notifications','disable_shopping','disable_shopping_and_show_notifications')))
                $this->_errors[] = $this->module->l('Vacation mode type is not valid','vacation');
            $valueFieldPost['vacation_type'] = $vacation_type;
            $valueFieldPost['vacation_mode'] = $vacation_mode;
            if($vacation_mode)
            {
                $vacation_notifications_default = Tools::getValue('vacation_notifications_'.$id_lang_default);
                $vacation_notifications = array();
                if(Tools::strpos($vacation_type,'show_notifications')!==false && !$vacation_notifications_default)
                    $this->_errors[] = $this->module->l('Notification is required','vacation');
                foreach($languages as $language)
                {
                    if(($vacation_notifications[$language['id_lang']] = Tools::getValue('vacation_notifications_'.$language['id_lang'],$this->seller->vacation_notifications[$language['id_lang']])) && Tools::strpos($vacation_type,'show_notifications')!==false && !Validate::isCleanHtml($vacation_notifications[$language['id_lang']]))
                        $this->_errors[] =sprintf($this->module->l('Notification is not valid in %s','vacation'),$language['iso_code']);
                }
            }
            if($date_vacation_start && !Validate::isDate($date_vacation_start))
            {
                $this->_errors[] = $this->module->l('Vacation start date is not valid','vacation');
            }
            if($date_vacation_end && !Validate::isDate($date_vacation_end))
            {
                $this->_errors[] = $this->module->l('Vacation end date is not valid','vacation');
            }
            if($date_vacation_end && $date_vacation_start && strtotime($date_vacation_start) >= strtotime($date_vacation_end))
            {
                $this->_errors[] = $this->module->l('The vacation end date must be greater than the vacation start date','vacation');
            }
            foreach($languages as $language)
            {
                $valueFieldPost['vacation_notifications'][$language['id_lang']] = Tools::getValue('vacation_notifications_'.$language['id_lang'],$this->seller->vacation_notifications[$language['id_lang']]);
            }
            if(!$this->_errors)
            {
                $this->seller->vacation_mode = (int)$vacation_mode;
                if($this->seller->vacation_mode)
                {
                    $this->seller->vacation_type = $vacation_type;
                    $this->seller->date_vacation_start = $date_vacation_start;
                    $this->seller->date_vacation_end = $date_vacation_end;
                    if(Tools::strpos($vacation_type,'show_notifications')!==false)
                    {
                        foreach($languages as $language)
                        {
                            $this->seller->vacation_notifications[$language['id_lang']] = $vacation_notifications[$language['id_lang']] ? : $vacation_notifications_default;
                        }
                    }
                }
                if($this->seller->update())
                    $this->_success = $this->module->l('Updated successfully','vaccation');
                else
                    $this->_errors[] = $this->module->l('An error occurred while saving'); 
            }
            
        }
        else
        {
            $valueFieldPost['vacation_type'] = $this->seller->vacation_type;
            $valueFieldPost['vacation_mode'] = $this->seller->vacation_mode;
            foreach($languages as $language)
            {
                $valueFieldPost['vacation_notifications'][$language['id_lang']] = $this->seller->vacation_notifications[$language['id_lang']] ? :$this->module->l('The seller is currently on vacation. This shop will come back later','vacation');
            }
        }
        
        $this->context->smarty->assign(
            array(
                'valueFieldPost' => $valueFieldPost,
            )
        );
    }
    public function initContent()
	{
		parent::initContent();
        if(isset($this->context->cookie->_success) && $this->context->cookie->_success )
        {
            $this->_success = $this->context->cookie->_success;
            $this->context->cookie->_success='';
        }    
        $this->context->smarty->assign(
            array(
                'path' => $this->module->getBreadCrumb(),
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false, 
                'html_content' => $this->_initContent(),
                '_errors' => $this->_errors ? $this->module->displayError($this->_errors):'',
                '_success' => $this->_success ? $this->module->displayConfirmation($this->_success):'',
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/vacation.tpl');      
        else        
            $this->setTemplate('vacation_16.tpl'); 
    }
    public function _initContent()
    {
        $this->context->smarty->assign(
            array(
                'seller' => $this->seller,
                'languages' => Language::getLanguages(false),
                'id_lang_default' => (int)Configuration::get('PS_LANG_DEFAULT')
            )
        );
        return $this->module->displayTpl('shop/vacation.tpl');
    }
 }