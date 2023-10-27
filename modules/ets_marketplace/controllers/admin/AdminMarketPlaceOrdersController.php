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
 * Class AdminMarketPlaceOrdersController
 * @property \Ets_marketplace $module
 */
class AdminMarketPlaceOrdersController extends ModuleAdminController
{
    public function __construct()
    {
       parent::__construct();
       $this->context= Context::getContext();
       $this->bootstrap = true;
    }
    public function postProcess()
    {
        parent::postProcess();
        if(Tools::isSubmit('del') && ($id_order = Tools::getValue('id_order')) && Validate::isUnsignedId($id_order))
        {
            $order = new Order($id_order);
            if($order->delete())
            {
                $this->context->cookie->success_message = $this->l('Deleted order successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceOrders'));
            }
        }
    }
    public function renderList()
    {
        $this->module->getContent();
        $this->context->smarty->assign(
            array(
                'ets_mp_body_html'=> $this->_renderOrders(),
            )
        );
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
    public function _renderOrders()
    {
        $orderStates = OrderState::getOrderStates($this->context->language->id);
        $fields_list = array(
            'id_order' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'class'=>'text-center'
            ),
            'reference'=>array(
                'title' => $this->l('Order reference'),
                'type'=> 'text',
                'sort' => true,
                'filter' => true,
            ),
            'customer_name' => array(
                'title' => $this->l('Customer'),
                'type'=> 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag'=>false,
            ),
            'total_paid_tax_incl' => array(
                'title' => $this->l('Total price (tax incl)'),
                'type' => 'int',
                'sort' => true,
                'filter' => true,
                'class'=>'text-center'
            ),
            'seller_name' => array(
                'title' => $this->l('Seller name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag'=>false,
            ),
            'shop_name' => array(
                'title' => $this->l('Shop name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag'=>false
            ),
            'total_commission' => array(
                'title' => $this->l('Seller commissions'),
                'type' => 'text',
                'sort' => true,
            ),
            'admin_earned' => array(
                'title' => $this->l('Admin earned'),
                'type' => 'text',
                'sort' => true,
            ),
            'current_state' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'list' => $orderStates,
                    'id_option' => 'id_order_state',
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
        if(($id_order = Tools::getValue('id_order')) && !Tools::isSubmit('del'))
        {
            if(Validate::isUnsignedId($id_order))
                $filter .= ' AND o.id_order="'.(int)$id_order.'"';
            $show_resset = true;
        }
        if(($seller_name = Tools::getValue('seller_name')) || $seller_name!='')
        {
            if(Validate::isCleanHtml($seller_name))
                $filter .= ' AND CONCAT(customer.firstname," ",customer.lastname) like "%'.pSQL($seller_name).'%"';
            $show_resset =true;
        }
        if(($customer_name = Tools::getValue('customer_name')) || $customer_name!='' )
        {
            if(Validate::isCleanHtml($customer_name))
                $filter .= ' AND CONCAT(c.firstname," ",c.lastname) like "%'.pSQL($customer_name).'%"';
            $show_resset = true;   
        }
        if(($shop_name = Tools::getValue('shop_name')) || $shop_name)
        {
            if(Validate::isCleanHtml($shop_name))
                $filter .= ' AND sl.shop_name like "%'.pSQL($shop_name).'%"';
            $show_resset = true;
        }
        if(($total_commission=  Tools::getValue('total_commission')) || $total_commission!='')
        {
            if(Validate::isFloat($total_commission))
                $having .=' AND total_commission ="'.(float)$total_commission.'"';
            $show_resset = true;
        }
        if(($admin_earned = Tools::getValue('admin_earned')) || $admin_earned!='')
        {
            if(Validate::isFloat($admin_earned))
                $having .= ' AND admin_earned ="'.(float)$admin_earned.'"';
            $show_resset = true;
        }
        if(($reference = Tools::getValue('reference')) || $reference!='')
        {
            if(Validate::isCleanHtml($reference))
                $filter .=' AND o.reference LIKE "%'.pSQL($reference).'%"';
            $show_resset = true;
        }
        if(($payment = Tools::getValue('payment')) || $payment!='')
        {
            if(Validate::isCleanHtml($payment))
                $filter .=' AND o.payment LIKE "%'.pSQL($payment).'%"';
            $show_resset = true;
        }
        if(($date_add_min = trim(Tools::getValue('date_add_min'))) || $date_add_min!='')
        {
            if(Validate::isDate($date_add_min))
                $filter .=' AND o.date_add >= "'.pSQL($date_add_min).' 00:00:00"';
            $show_resset = true;
        }
        if(($date_add_max = trim(Tools::getValue('date_add_max'))) || $date_add_max!='')
        {
            if(Validate::isDate($date_add_max))
                $filter .= ' AND o.date_add <="'.pSQL($date_add_max).' 23:59:59"';
            $show_resset=true;
        }
        if(($total_paid_tax_incl_min = trim(Tools::getValue('total_paid_tax_incl_min'))) || $total_paid_tax_incl_min!='')
        {
            if(Validate::isFloat($total_paid_tax_incl_min))
                $filter .=' AND o.total_paid_tax_incl >= "'.(float)$total_paid_tax_incl_min.'"';
            $show_resset = true;
        }
        if(($total_paid_tax_incl_max = trim(Tools::getValue('total_paid_tax_incl_max'))) || $total_paid_tax_incl_max!='')
        {
            if(Validate::isFloat($total_paid_tax_incl_max))
                $filter .=' AND o.total_paid_tax_incl <= "'.(float)$total_paid_tax_incl_max.'"';
            $show_resset = true;
        }
        if(($current_state = trim(Tools::getValue('current_state'))) || $current_state!='')
        {
            if(Validate::isUnsignedId($current_state))
                $filter .=' AND o.current_state = "'.(int)$current_state.'"';
            $show_resset = true;
        }
        //Sort
        $sort = "";
        $sort_type=Tools::getValue('sort_type','desc');
        $sort_value = Tools::getValue('sort','id_order');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'id_order':
                    $sort .='o.id_order';
                    break;
                case 'seller_name':
                    $sort .='seller_name';
                    break;
                case 'reference':
                    $sort .='o.reference';
                    break;
                case 'customer_name':
                    $sort .='customer_name';
                    break;
                case 'shop_name':
                    $sort .='shop_name';
                    break;
                case 'date_add':
                    $sort .= 'o.date_add';
                    break;
                case 'total_paid_tax_incl':
                    $sort .= 'o.total_paid_tax_incl';
                    break;
                case 'total_commission':
                    $sort .='total_commission';
                    break;
                case 'admin_earned':
                    $sort .='admin_earned';
                    break;
                case 'payment':
                    $sort .= 'o.payment';
                    break;
                case 'current_state':
                    $sort .= 'o.current_state';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                $sort .= ' '.trim($sort_type);  
        }
        //Paggination
        $page = Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $totalRecords = (int)Ets_mp_commission::getOrders($filter,$having,0,0,'',true);
        $paggination = new Ets_mp_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getAdminLink('AdminMarketPlaceOrders').'&page=_page_'.$this->module->getFilterParams($fields_list,'ms_orders');
        $paggination->limit =  (int)Tools::getValue('paginator_order_select_limit',20);
        $paggination->name ='order';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $orders = Ets_mp_commission::getOrders($filter,$having, $start,$paggination->limit,$sort,false);
        if($orders)
        {
            foreach($orders as &$order)
            {
                $order['total_paid_tax_incl'] = Tools::displayPrice(Tools::convertPrice($order['total_paid_tax_incl'],$order['id_currency'],false), new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));
                $order['total_commission'] = Tools::displayPrice($order['total_commission'], new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));
                $order['admin_earned'] = Tools::displayPrice($order['admin_earned'], new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));
                $order['current_state'] = $this->module->displayOrderState($order['current_state']);  
                $order['child_view_url'] = $this->module->getLinkOrderAdmin($order['id_order']);   
                if($order['id_order_seller'])
                {
                    if(!$order['id_seller'])
                    {
                        $order['shop_name']= Module::getInstanceByName('ets_marketplace')->displayText($this->l('Shop deleted'),'span','deleted_shop row_deleted');
                    }
                    else
                    {
                        $order['shop_name'] = Module::getInstanceByName('ets_marketplace')->displayText($order['shop_name'],'a','ets_mp_shopname','',$this->module->getShopLink(array('id_seller'=>$order['id_seller'])),'_blank');

                    }
                    if($order['id_customer_seller'])
                    {
                        $order['seller_name'] = Module::getInstanceByName('ets_marketplace')->displayText($order['seller_name'],'a','','',$this->module->getLinkCustomerAdmin($order['id_customer_seller']));
                    }
                    else
                        $order['seller_name'] = Module::getInstanceByName('ets_marketplace')->displayText($this->l('Seller deleted'),'span','row_deleted');
                }
                else
                {
                    $order['seller_name'] ='--';
                    $order['shop_name'] ='--';
                }
                $order['customer_name'] = Module::getInstanceByName('ets_marketplace')->displayText($order['customer_name'],'a','','',$this->module->getLinkCustomerAdmin($order['id_customer']));

            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $listData = array(
            'name' => 'ms_orders',
            'actions' => array('view'),
            'icon' => 'icon-orders',
            'currentIndex' => $this->context->link->getAdminLink('AdminMarketPlaceOrders').($paggination->limit!=20 ? '&paginator_order_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getAdminLink('AdminMarketPlaceOrders'),
            'identifier' => 'id_order',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->l('Orders'),
            'fields_list' => $fields_list,
            'field_values' => $orders,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list,'ms_orders'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'show_add_new'=> false,
            'sort_type' => $sort_type,
        );            
        return $this->module->renderList($listData);
    }
}