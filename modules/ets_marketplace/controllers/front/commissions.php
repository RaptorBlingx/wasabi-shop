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
 * Class Ets_MarketPlaceCommissionsModuleFrontController
 * @property \Ets_marketplace $module;
 * @property \Ets_mp_seller $seller;
 */
class Ets_MarketPlaceCommissionsModuleFrontController extends ModuleFrontController
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
            die($this->module->l('You do not have permission','commissions'));
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
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/commissions.tpl');      
        else        
            $this->setTemplate('commissions_16.tpl'); 
    }
    public function _initContent()
    {
        $commistion_status=array(
            array(
                'id' => '-1',
                'name' => $this->module->l('Pending','commissions'),
            ),
            array(
                'id' => '0',
                'name' => $this->module->l('Canceled','commissions')
            ),
            array(
                'id' => '1',
                'name' => $this->module->l('Approved','commissions')
            ),
            array(
                'id' =>'refunded' ,
                'name' => $this->module->l('Refunded','commissions')
            ),
            array(
                'id' =>'deducted' ,
                'name' => $this->module->l('Deducted','commissions')
            ),
        );
        $fields_list = array(
            'reference' => array(
                'title' => $this->module->l('Reference','commissions'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'commission' => array(
                'title' => $this->module->l('Commissions','commissions'),
                'type' => 'int',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
            ),
            'product_name' => array(
                'title' => $this->module->l('Product','commissions'),
                'type'=> 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag'=> false,
            ),
            'price' => array(
                'title' => $this->module->l('Price','commissions'),
                'type'=> 'int',
                'sort' => true,
                'filter' => true,
            ),
            'quantity' => array(
                'title' => $this->module->l('Quantity','commissions'),
                'type'=> 'int',
                'sort' => true,
                'filter' => true,
            ),
            'status' => array(
                'title' => $this->module->l('Status','commissions'),
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
                'title' => $this->module->l('Date','commissions'),
                'type' => 'date',
                'sort' => true,
                'filter' => true,
                'class' => 'text-center'
            ),
        );
        //Filter
        $show_resset = false;
        $filter = "";
        $having = "";
        if(($reference = Tools::getValue('reference')) || $reference!='' )
        {
            if(Validate::isReference($reference))
                $filter .= ' AND sc.reference like "%'.pSQL($reference).'%"';
            $show_resset = true;
        }
        if(($customer_name = Tools::getValue('customer_name')) && $customer_name!='')
        {
            if(Validate::isCleanHtml($customer_name))
                $filter .= ' AND CONCAT(c.firstname," ",c.lastname) like "%'.pSQL($customer_name).'%"';
            $show_resset = true;   
        }
        if(($id_order= Tools::getValue('id_order')) || $id_order!='')
        {
            if(Validate::isUnsignedId($id_order))
                $filter .=' AND sc.id_order ="'.(int)$id_order.'"';
            $show_resset = true;
        }
        if(($product_name = Tools::getValue('product_name')) || $product_name!='')
        {
            if(Validate::isCleanHtml($product_name))
                $filter .= ' AND sc.product_name LIKE "%'.pSQL($product_name).'%"';
            $show_resset = true;
        }
        if(($price_min =Tools::getValue('price_min')) || $price_min!='')
        {
            if(Validate::isFloat($price_min))
                $filter .=' AND sc.price_tax_incl >='.(float)$price_min.'';
            $show_resset = true;
        }
        if(($price_max = Tools::getValue('price_max')) || $price_max!='')
        {
            if(Validate::isFloat($price_max))
                $filter .=' AND sc.price_tax_incl <='.(float)$price_max.'';
            $show_resset = true;
        }
        if(($quantity_min = trim(Tools::getValue('quantity_min'))) || $quantity_min!='')
        {
            if(Validate::isFloat($quantity_min))
                $filter .=' AND sc.quantity >= '.(int)$quantity_min.'';
            $show_resset = true;
        }
        if(($quantity_max = trim(Tools::getValue('quantity_max'))) || $quantity_max!='')
        {
            if(Validate::isFloat($quantity_max))
                $filter .=' AND sc.quantity <= '.(int)$quantity_max.'';
            $show_resset = true;
        }
        if(($commission_min = trim(Tools::getValue('commission_min'))) || $commission_min!='')
        {
            if(Validate::isFloat($commission_min))
                $filter .= ' AND sc.commission >='.(float)$commission_min.'';
            $show_resset=true;
        }
        if(($commission_max = trim(Tools::getValue('commission_max'))) || $commission_max!='')
        {
            if(Validate::isFloat($commission_max))
                $filter .= ' AND sc.commission <='.(float)$commission_max.'';
            $show_resset=true;
        }
        if(($status = trim(Tools::getValue('status'))) || $status!='')
        {
            if($status =='refunded' || $status=='deducted')
            {
                $filter .= ' AND sc.type="usage" AND sc.status="'.($status =='refunded' ? 0 :1).'"';
            }
            elseif(Validate::isInt($status))
            {
                $filter .=' AND sc.type="commission" AND sc.status = "'.(int)$status.'"';
            }
            $show_resset = true;
        }
        if(($date_add_min = trim(Tools::getValue('date_add_min'))) || $date_add_min!='')
        {
            if(Validate::isDate($date_add_min))
                $filter .= ' AND sc.date_add >="'.pSQL($date_add_min).' 00:00:00"';
            $show_resset = true;
        }
        if(($date_add_max = trim(Tools::getValue('date_add_max'))) || $date_add_max!='')
        {
            if(Validate::isDate($date_add_max))
                $filter .=  ' AND sc.date_add <="'.pSQL($date_add_max).' 23:59:59"';
            $show_resset = true;
        }
        //Sort
        $sort = "";
        $sort_type = Tools::getValue('sort_type','desc');
        $sort_value = Tools::getValue('sort','date_add');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'reference':
                    $sort .='sc.reference';
                    break;
                case 'id_order':
                    $sort .='sc.id_order';
                    break;
                case 'customer_name':
                    $sort .='customer_name';
                    break;
                case 'product_name':
                    $sort .='sc.product_name';
                    break;
                case 'price':
                    $sort .='sc.price';
                    break;
                case 'quantity':
                    $sort .=' sc.quantity';
                    break;
                case 'commission':
                    $sort .=' sc.commission';
                    break;
                case 'status':
                    $sort .=' sc.status';
                    break;
                case 'date_add':
                    $sort .= 'sc.date_add';
                    break;
                
            }
            if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                $sort .= ' '.trim($sort_type);  
        }
        //Paggination
        $page = (int)Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $totalRecords = (int) $this->seller->getCommissions($filter,$having,0,0,'',true);
        $paggination = new Ets_mp_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getModuleLink($this->module->name,'commissions',array('list'=>true,'page'=>'_page_')).$this->module->getFilterParams($fields_list,'mp_commissions');
        $paggination->limit =  (int)Tools::getValue('paginator_commision_select_limit',10);
        $paggination->name ='commision';
        $paggination->num_links =5;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $commissions = $this->seller->getCommissions($filter,$having, $start,$paggination->limit,$sort,false);
        if($commissions)
        {
            foreach($commissions as &$commission)
            {
                if($commission['price'])
                    $commission['price'] = Tools::displayPrice($commission['price_tax_incl'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));  
                else
                    $commission['price']='';
                $commission['commission'] = Tools::displayPrice($commission['commission'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));  
                if($commission['type']=='usage')
                    $commission['commission'] = Module::getInstanceByName('ets_marketplace')->displayText('-'.$commission['commission'],'span','ets_mp_commision_usage');
                if($commission['note'])
                    $commission['commission'] .= Module::getInstanceByName('ets_marketplace')->displayText('','br').Module::getInstanceByName('ets_marketplace')->displayText($commission['note'],'i','');
                if($commission['type']=='usage')
                {
                    $commission['id'] = 'U-'.$commission['id'];
                    if($commission['status']==0)
                        $commission['status'] = Module::getInstanceByName('ets_marketplace')->displayText($this->module->l('Refunded','commissions'),'span','ets_mp_status refunded');
                    elseif($commission['status']==1)
                        $commission['status'] = Module::getInstanceByName('ets_marketplace')->displayText($this->module->l('Deducted','commissions'),'span','ets_mp_status deducted');
                }
                else
                {
                    $commission['id'] = 'C-'.$commission['id'];
                    if($commission['status']==-1)
                        $commission['status'] = Module::getInstanceByName('ets_marketplace')->displayText($this->module->l('Pending','commissions'),'span','ets_mp_status pending');
                    elseif($commission['status']==0)
                        $commission['status'] = Module::getInstanceByName('ets_marketplace')->displayText($this->module->l('Canceled','commissions'),'span','ets_mp_status canceled');
                    elseif($commission['status']==1)
                        $commission['status'] = Module::getInstanceByName('ets_marketplace')->displayText($this->module->l('Approved','commissions'),'span','ets_mp_status approved');
                }
                if($commission['id_product'] >0)
                    $commission['product_name'] = ($commission['product_id'] ? Module::getInstanceByName('ets_marketplace')->displayText($commission['product_name'],'a','ets_mp_commistion_productname_front','',$this->context->link->getProductLink($commission['id_product'],null,null,null,null,null,$commission['id_product_attribute']),'_blank'):'');
                if(!$commission['quantity'])
                    $commission['quantity']='';               
            }
        }
        $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','commissions');
        $paggination->style_links = $this->module->l('links','commissions');
        $paggination->style_results = $this->module->l('results','commissions');
        $listData = array(
            'name' => 'mp_commissions',
            'actions' => array(),
            'currentIndex' => $this->context->link->getModuleLink($this->module->name,'commissions',array('list'=>true)).($paggination->limit!=10 ? '&paginator_commision_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getModuleLink($this->module->name,'commissions',array('list'=>true)),
            'identifier' => 'id',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->module->l('Commissions','commissions'),
            'fields_list' => $fields_list,
            'field_values' => $commissions,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list,'mp_commissions'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'show_add_new'=> false,
            'sort_type' => $sort_type,
        );            
        return $this->module->renderList($listData);
    }
    
 }