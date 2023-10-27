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
 * Class Ets_MarketPlaceBillingModuleFrontController
 * @property \Ets_mp_seller $seller
 * @property \Ets_marketplace $module
 */
class Ets_MarketPlaceBillingModuleFrontController extends ModuleFrontController
{
    public $seller;
    public function __construct()
	{
		parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
	}
    public function postProcess()
    {
        parent::postProcess();
        
        if(!$this->context->customer->isLogged() || !($this->seller = $this->module->_getSeller(true)) )
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->module->_checkPermissionPage($this->seller))
            die($this->module->l('You do not have permission to access this page','billing'));
        if(Tools::isSubmit('dowloadpdf') && ($id_billing = (int)Tools::getValue('id_ets_mp_seller_billing')) && Validate::isUnsignedId($id_billing))
        {
            $billing = new Ets_mp_billing($id_billing);
            if($billing->id_customer == $this->seller->id_customer)
            {
                $pdf = new PDF($billing,'BillingPdf', Context::getContext()->smarty);
                $pdf->render(true);
            }
            else
                die($this->module->l('You do not have permission to download this invoice','billing'));
        }
        
        if(Tools::isSubmit('submitContactMarketplace') && ($id_billing = Tools::getValue('id_billing_contact')) && Validate::isUnsignedId($id_billing))
        {
            $errors = array();
            if(!($billing_contact_subject = Tools::getValue('biling_contact_subject')))
                $errors[] = $this->module->l('Subject is required','billing');
            elseif( $billing_contact_subject&& !Validate::isCleanHtml($billing_contact_subject))
                $errors[] = $this->module->l('Subject is not valid','billing');
            if(!($billing_contact_message = Tools::getValue('biling_contact_message')))
                $errors[] = $this->module->l('Message is required','billing');
            elseif($billing_contact_message && !Validate::isCleanHtml($billing_contact_message))
                $errors[] = $this->module->l('Message is not valid','billing');
            $billing = new Ets_mp_billing($id_billing);
            if(!Validate::isLoadedObject($billing) || $billing->id_customer!= $this->seller->id_customer)
                $errors[] = $this->module->l('Membership is not valid','billing');
            if(!$errors)
            {
                $biling_contact_paid_invoice = (int)Tools::getValue('biling_contact_paid_invoice');
                $billing->seller_confirm = $biling_contact_paid_invoice;
                if($billing->update())
                {
                    $template_vars = array(
                        '{seller_name}' => $this->seller->seller_name,
                        '{invoice_ref}' => $billing->reference,
                        '{content}' => $billing_contact_message,
                        '{subject}' => $billing_contact_subject,
                    );
                    $subjects = array(
                        'translation' => $this->module->l('Seller confirmed payment','billing'),
                        'origin'=> 'Seller confirmed payment',
                        'specific' =>'billing',
                    );
                    Ets_marketplace::sendMail('contact_marketplace',$template_vars,'',$subjects);
                    die(
                        json_encode(
                            array(
                                'success' => $this->module->l('Sent successfully','billing'),
                                'id_billing' => $id_billing,
                                'seller_confirm' => $billing->seller_confirm,
                                'seller_confirm_text' => $this->module->l('Seller confirmed','billing'),
                            )
                        )
                    );
                }
                else
                {
                    die(
                        json_encode(
                            array(
                                'errors' => $this->module->displayError($this->module->l('Billing update failed','billing')),
                            )
                        )
                    );
                }
                
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($errors),
                        )
                    )
                );
            }
            
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
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/billing.tpl');      
        else        
            $this->setTemplate('billing_16.tpl'); 
    }
    public function _initContent()
    {
        $fields_list = array(
            'reference' => array(
                'title' => $this->module->l('Reference','billing'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'amount' => array(
                'title' => $this->module->l('Amount','billing'),
                'type' => 'int',
                'sort' => true,
                'filter' => true,
            ),
            'active' => array(
                'title' => $this->module->l('Status','billing'),
                'type' => 'select',
                'sort' => true,
                'filter' => true,
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>-1,
                            'value' => $this->module->l('Canceled','billing'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->module->l('Pending','billing'),
                        ),
                        array(
                            'id_option'=>1,
                            'value' => $this->module->l('Paid','billing'),
                        )
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),
            ),
            'note' => array(
                'title' => $this->module->l('Description','billing'),
                'type' => 'text',
                'sort' => false,
                'filter' => false,
                'class' => 'text-center',
                'strip_tag'=>false,
            ),
            'date_add' => array(
                'title' => $this->module->l('Date of invoice','billing'),
                'type' => 'date',
                'sort' => true,
                'filter' => true
            ),
            'date_due' => array(
                'title' => $this->module->l('Due date','billing'),
                'type' => 'date',
                'sort' => true,
                'filter' => true
            ),
            'pdf' => array(
                'title' => $this->module->l('PDF','billing'),
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
        $filter .=' AND b.id_customer='.(int)$this->seller->id_customer;
        if(($reference = Tools::getValue('reference')) || $reference!='')
        {
            if(Validate::isCleanHtml($reference))
                $filter .=' AND b.reference like "'.pSQL($reference).'%"';
            $show_resset=true;
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
        if(($active = trim(Tools::getValue('active'))) || $active!='')
        {
            if(Validate::isInt($active))
                $filter .= ' AND b.active="'.(int)$active.'"';
            $show_resset = true;
        }
        if(($date_add_min=trim(Tools::getValue('date_add_min'))) || $date_add_min!='' )
        {
            if(Validate::isDate($date_add_min))
                $filter .= ' AND b.date_add >="'.pSQL($date_add_min).' 00:00:00"';
            $show_resset = true;
        }
        if(($date_add_max = trim(Tools::getValue('date_add_max'))) || $date_add_max)
        {
            if(Validate::isDate($date_add_max))
                $filter .= ' AND b.date_add <="'.pSQL($date_add_max).' 23:59:59"';
            $show_resset = true;
        }
        if(($date_due_min = trim(Tools::getValue('date_due_min'))) || $date_due_min!='' )
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
        //Sort
        $sort = "";
        $sort_type=Tools::getValue('sort_type','desc');
        $sort_value = Tools::getValue('sort','date_add');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'amount':
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
            }
            if($sort && $sort_type && in_array($sort_type,array('acs','desc')))
                $sort .= ' '.trim($sort_type);  
        }
        //Paggination
        $page = (int)Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $totalRecords = (int)Ets_mp_billing::getSellerBillings($filter,$having,0,0,'',true);
        $paggination = new Ets_mp_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getModuleLink($this->module->name,'billing',array('list'=>true,'page'=>'_page_')).$this->module->getFilterParams($fields_list,'front_ms_billings');
        $paggination->limit =  (int)Tools::getValue('paginator_billing_select_limit',10);
        $paggination->num_links =5;
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
                $billing['status'] = $billing['active'];
                if($billing['active']==0)
                {
                    $billing['active'] = Module::getInstanceByName('ets_marketplace')->displayText($this->module->l('Pending','billing').($billing['seller_confirm'] ? ' ('.$this->module->l('Seller confirmed','billing').')':''),'span','ets_mp_status pending');
                }
                elseif($billing['active']==1)
                {
                    $billing['active'] = Module::getInstanceByName('ets_marketplace')->displayText($this->module->l('Paid','billing'),'span','ets_mp_status purchased');
                }
                elseif($billing['active']==-1)
                {
                    $billing['active'] = Module::getInstanceByName('ets_marketplace')->displayText($this->module->l('Canceled','billing'),'span','ets_mp_status disabled');
                }
                if(!$billing['id_employee'])
                {
                    if($billing['fee_type']=='pay_once')
                        $billing['note'] = $this->module->l('Pay once','billing');
                    if($billing['fee_type']=='monthly_fee')
                        $billing['note'] = $this->module->l('Monthly fee:','billing').Module::getInstanceByName('ets_marketplace')->displayText('','br').$this->module->l('From','billing').' '.Tools::displayDate($billing['date_from']).' '.$this->module->l('To','billing'). ' '.Tools::displayDate($billing['date_to']);
                    if($billing['fee_type']=='quarterly_fee')
                        $billing['note'] = $this->module->l('Quarterly fee: from','billing').Module::getInstanceByName('ets_marketplace')->displayText('','br').$this->module->l('From','billing').' '.Tools::displayDate($billing['date_from']).' '.$this->module->l('To','billing'). ' '.Tools::displayDate($billing['date_to']);
                    if($billing['fee_type']=='yearly_fee')
                        $billing['note'] = $this->module->l('Yearly fee: from','billing').Module::getInstanceByName('ets_marketplace')->displayText('','br').$this->module->l('From','billing').' '.Tools::displayDate($billing['date_from']).' '.$this->module->l('To','billing'). ' '.Tools::displayDate($billing['date_to']);
                } 
                else
                    $billing['note'] .= (trim($billing['note']) ? Module::getInstanceByName('ets_marketplace')->displayText(' ','br'):'').($billing['date_from'] && $billing['date_from']!='0000-00-00' ? $this->module->l('From','billing').' '.Tools::displayDate($billing['date_from']).' ' :'' ). ($billing['date_to'] && $billing['date_to']!='0000-00-00' ? $this->module->l('To','billing').' '.Tools::displayDate($billing['date_to']) :'' );
                $billing['pdf'] = Module::getInstanceByName('ets_marketplace')->displayText(Module::getInstanceByName('ets_marketplace')->displayText('','i','icon-pdf icon icon-pdf fa fa-file-pdf-o'),'a','ets_mp_downloadpdf','',$this->context->link->getModuleLink($this->module->name,'billing',array('id_ets_mp_seller_billing'=>$billing['id_ets_mp_seller_billing'],'dowloadpdf'=>'yes')));
                if($billing['status']==0 && $billing['seller_confirm']==0)
                    $billing['pdf'] .= Module::getInstanceByName('ets_marketplace')->displayText(Module::getInstanceByName('ets_marketplace')->displayText('','i','icon-envelope icon icon-envelope fa fa-envelope-o'),'a','ets_mp_contact_marketplace','','#sendmail',false,null,null,Null,Null,null,Null,array(array('name'=>'data-id-billing','value'=>$billing['id_ets_mp_seller_billing'])));
                if(!$billing['date_due'])
                    $billing['date_due']= '--';
            }
        }
        $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','billing');
        $paggination->style_links = $this->module->l('links','billing');
        $paggination->style_results = $this->module->l('results','billing');
        $listData = array(
            'name' => 'front_ms_billings',
            'actions' => array(),
            'currentIndex' => $this->context->link->getModuleLink($this->module->name,'billing',array('list'=>true)).($paggination->limit!=10 ? '&paginator_billing_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getModuleLink($this->module->name,'billing',array('list'=>true)),
            'identifier' => 'id_ets_mp_seller_billing',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->module->l('Membership','billing'),
            'fields_list' => $fields_list,
            'field_values' => $billings,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list,'front_ms_billings'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'show_add_new'=> false,
            'sort_type' => $sort_type,
        );           
        return $this->module->renderList($listData);
    }
 }