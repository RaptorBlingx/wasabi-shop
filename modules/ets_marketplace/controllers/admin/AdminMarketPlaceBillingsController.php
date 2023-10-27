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
 * Class AdminMarketPlaceBillingsController
 * @property \Ets_marketplace $module
 */
class AdminMarketPlaceBillingsController extends ModuleAdminController
{
    public function init()
    {
       parent::init();
       $this->bootstrap = true;
       if(Tools::isSubmit('dowloadpdf') && ($id_billing = Tools::getValue('id_ets_mp_seller_billing')) && Validate::isUnsignedId($id_billing))
       {
            $billing = new Ets_mp_billing($id_billing);
            if(Validate::isLoadedObject($billing))
            {
                $pdf = new PDF($billing,'BillingPdf', Context::getContext()->smarty);
                $pdf->render(true);
            }
            else
                $this->module->_errors[] = $this->l('Billing is not valid');
       }
       if(Tools::isSubmit('saveBilling'))
       {
            $date_to = Tools::getValue('date_to');
            $date_from = Tools::getValue('date_from');
            if(!($id_seller =Tools::getValue('id_seller')))
            {
                $this->module->_errors[] = $this->l('Seller is required');
            }
            elseif(!Validate::isUnsignedId($id_seller) || !Validate::isLoadedObject(Ets_mp_seller::_getSellerByIdCustomer($id_seller)))
                $this->module->_errors[] = $this->l('Seller is not valid');
            if(!($amount = Tools::getValue('amount')))
                $this->module->_errors[] = $this->l('Amount is required');
            elseif(!Validate::isPrice($amount))
                $this->module->_errors[] = $this->l('Amount is not valid');
            if(($note=Tools::getValue('note')) && !Validate::isCleanHtml($note))
                $this->module->_errors[] = $this->l('Description is not valid');
            if($date_from && !Validate::isDate($date_from))
                $this->module->_errors[] = $this->l('"From" date is not valid');
            if($date_to && !Validate::isDate($date_to))
                $this->module->_errors[] = $this->l('"To" date is not valid');
            if($date_to && $date_from && Validate::isDate($date_to) && Validate::isDate($date_from) && strtotime($date_from) > strtotime($date_to))
                $this->module->_errors[] = $this->l('"From" date must be smaller than "To" date'); 
            $active = (int)Tools::getValue('active') ? 1:0;
            if(!$this->module->_errors)
            {
                $billing = new Ets_mp_billing();
                $billing->id_customer = (int)$id_seller;
                $billing->amount = (float)$amount;
                $billing->amount_tax = (float)$amount;
                $billing->note = $note;
                $billing->date_from = $date_from;
                $billing->date_to = $date_to;
                $billing->active= (int)$active;
                $billing->id_employee = $this->context->employee->id;
                $billing->used=1;
                if($billing->add())
                {
                    $this->context->cookie->success_message = $this->l('Added successfully');
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceBillings'));
                }
                else
                    $this->module->_errors[] = $this->l('An error occurred while saving the billing');
            }
       }
    }
    public function initContent()
    {
        parent::initContent();
        if(Tools::isSubmit('search_seller'))
        {
            if(($query = Tools::getValue('q')) && Validate::isCleanHtml($query))
            {

                $sellers = Ets_mp_seller::getSellerByQuery($query,false);
                if($sellers)
                {
                    foreach($sellers as $seller)
                    {
                        echo $seller['id_customer'].'|'.$seller['seller_name'].'|'.$seller['email']."\n";
                    }
                }
            }
            die();            
        }
    }
    public function renderList()
    {
        $this->module->getContent();
        if(Tools::isSubmit('addnewbillng'))
        {
            $this->context->smarty->assign(
                array(
                    'ets_mp_body_html'=> $this->renderFromBilling(),
                    'ets_link_search_seller' => $this->context->link->getAdminLink('AdminMarketPlaceBillings').'&search_seller=1',
                )
            );
        }
        else
        {
            $this->context->smarty->assign(
                array(
                    'ets_mp_body_html'=> $this->module->renderBilling(),
                )
            );
        }
        
        $html ='';
        if($this->context->cookie->success_message)
        {
            $html .= $this->module->displayConfirmation($this->context->cookie->success_message);
            $this->context->cookie->success_message ='';
        }
        if($this->module->_errors)
            $html .= $this->module->displayError($this->module->_errors);
        return $html.$this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'admin.tpl');
    }
    public function renderFromBilling()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Add membership'),
                    'icon' =>'icon-billing',
                ),
                'input' => array(
                    array(
                        'type'=>'hidden',
                        'name' => 'id_seller',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Seller name'),
                        'name' => 'seller_name',
                        'required' => true,
                        'suffix' => $this->module->displayText('','i','fa fa-search'),
                        'col'=>3,
                        'form_group_class' => 'form_search_seller',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Amount'),
                        'name' => 'amount',
                        'suffix' => $this->context->currency->iso_code,
                        'col'=>3,
                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Status'),
                        'name' => 'active',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option'=>0,
                                    'name'=>$this->l('Pending'),
                                ),
                                array(
                                    'id_option'=>1,
                                    'name'=>$this->l('Paid'),
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->l('Available from'),
                        'name' => 'date_from',
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->l('Available to'),
                        'name' => 'date_to',
                    ),
                    array(
                        'type' =>'textarea',
                        'label' => $this->l('Description'),
                        'name' => 'note',
                        'col'=>6
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'buttons' => array(
                    array(
                        'href' =>Tools::isSubmit('viewseller') ? $this->context->link->getAdminLink('AdminMarketPlaceSellers'): $this->context->link->getAdminLink('AdminMarketPlaceBillings', true),
                        'icon'=>'process-icon-cancel',
                        'title' => $this->l('Cancel'),
                    )
                ),
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'ets_mp_seller_billing';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this->module;
        $helper->identifier = 'id_ets_mp_seller_billing';
        $helper->submit_action = 'saveBilling';
        $helper->currentIndex = Tools::isSubmit('viewseller') ? $this->context->link->getAdminLink('AdminMarketPlaceSellers',false).'&addnewbillng=1': $this->context->link->getAdminLink('AdminMarketPlaceBillings', false).'&addnewbillng=1';
        $helper->token = Tools::isSubmit('viewseller') ? Tools::getAdminTokenLite('AdminMarketPlaceSellers'): Tools::getAdminTokenLite('AdminMarketPlaceBillings');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),

            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'fields_value' => $this->getBillingFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => '',
            'link' => $this->context->link,
            'cancel_url' => $this->context->link->getAdminLink('AdminMarketPlaceSellers', true),
        );
        return $helper->generateForm(array($fields_form));
    }
    public function getBillingFieldsValues()
    {
        if(($id_seller= (int)Tools::getValue('id_seller')) && Validate::isUnsignedId($id_seller))
            $seller = new Ets_mp_seller($id_seller);
        else
            $seller = new Ets_mp_seller();
        $fields = array(
            'id_seller' =>$seller->id,
            'seller_name' => $seller->seller_name,
            'amount'=> Tools::getValue('amount'),
            'active' => Tools::getValue('active'),
            'date_from'=> Tools::getValue('date_from'),
            'date_to' => Tools::getValue('date_to'),
            'note' => Tools::getValue('note'),
        );
        return $fields;
    }
}