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
 * Class AdminMarketPlaceSellersController
 * @property \Ets_marketplace $module
 */
class AdminMarketPlaceSellersController extends ModuleAdminController
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
        $id_seller= (int)Tools::getValue('id_seller');
        if(Tools::isSubmit('getmapseller') && $id_seller && Validate::isUnsignedId($id_seller))
        {
            $params = array(
                'all' => (int)Tools::getValue('all'),
                'radius' => (int)Tools::getValue('radius', 100),
                'latitude' =>(float)Tools::getValue('latitude'),
                'longitude' => (float)Tools::getValue('longitude'),
            );
            Ets_mp_seller::getMaps($id_seller,false,$params);
        }
        if(Tools::isSubmit('deletelogo') && $id_seller && Validate::isUnsignedId($id_seller))
        {      
            $seller = new Ets_mp_seller($id_seller);
            $shop_logo = $seller->shop_logo;
            $seller->shop_logo='';
            if($seller->update(true))
            {
                if($shop_logo && file_exists(_PS_IMG_DIR_.'mp_seller/'.$shop_logo))
                    @unlink(_PS_IMG_DIR_.'mp_seller/'.$shop_logo);
                $this->context->cookie->success_message = $this->module->l('Deleted logo successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceSellers').'&editets_seller=1&id_seller='.$seller->id);
            }
        }
        if(Tools::isSubmit('deletebanner') && $id_seller && Validate::isUnsignedId($id_seller))
        {
            $id_lang = (int)Tools::getValue('id_lang');
            if(Validate::isUnsignedId($id_lang) && Validate::isLoadedObject(new Language($id_lang)))
            {
                $seller = new Ets_mp_seller($id_seller);
                $shop_banner = isset($seller->shop_banner[$id_lang]) ? $seller->shop_banner[$id_lang] :'';
                $seller->shop_banner[$id_lang]='';
                if($seller->update(true))
                {
                    if($shop_banner && !in_array($shop_banner,$seller->shop_banner) && file_exists(_PS_IMG_DIR_.'mp_seller/'.$shop_banner))
                        @unlink(_PS_IMG_DIR_.'mp_seller/'.$shop_banner);
                    $this->context->cookie->success_message = $this->module->l('Deleted banner successfully');
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceSellers').'&editets_seller=1&id_seller='.$seller->id);
                }
            }
            
        }
        if(Tools::isSubmit('saveStatusSeller') && ($id_seller = (int)Tools::getValue('seller_id')) && Validate::isUnsignedId($id_seller))
        {
            $seller = new Ets_mp_seller($id_seller);
            $active_old = $seller->active;
            $error = '';
            $active_seller = (int)Tools::getValue('active_seller');
            $reason = Tools::getValue('reason');
            if($active_seller==0 || $active_seller==-3)
            {
                $seller->active = (int)$active_seller;
                $seller->reason = $reason;
                if($reason && !Validate::isCleanHtml($reason))
                    $error = $this->l('Reason is not valid');
            }    
            else{
                $date_from=Tools::getValue('date_from');
                $date_to = Tools::getValue('date_to');
                if($date_from && !Validate::isDate($date_from) && $date_to && !Validate::isDate($date_to))
                    $error = $this->l('"From" date and "To" date are not valid');
                elseif($date_from && !Validate::isDate($date_from))
                    $error = $this->l('"From" date is not valid');
                elseif($date_to && !Validate::isDate($date_to))
                    $error = $this->l('"To" date is not valid');
                elseif($date_to && $date_from && Validate::isDate($date_to) && Validate::isDate($date_to) && strtotime($date_from) >= strtotime($date_to))
                    $error = $this->l('"From" date must be smaller than "To" date');
                $seller->date_from = $date_from;
                $seller->date_to = $date_to;
                if((!$seller->date_from || strtotime($seller->date_from) <= strtotime(date('Y-m-d'))) && (!$seller->date_to || strtotime($seller->date_to) >= strtotime(date('Y-m-d'))))
                {
                    $seller->active =1;
                    $seller->mail_expired=0;
                    $seller->mail_going_to_be_expired=0;
                }
                else
                {
                    $seller->active =-2;
                    $seller->payment_verify=-1;
                }
            }
            $seller->date_upd = date('Y-m-d H:i:s');
            if($error)
            {
                if(Tools::isSubmit('ajax'))
                {
                    die(
                        json_encode(
                            array(
                                'errors' => $error
                            )
                        )
                    );
                }
            }
            else
            {
                if($seller->update(true))
                {
                    if($seller->active!=$active_old && $seller->active==-2)
                    {
                        $fee_type = $seller->getFeeType();
                        if($fee_type!='no_fee')
                        {
                            $billing = new Ets_mp_billing();
                            $billing->id_customer = $seller->id_customer;
                            $billing->amount = (float)$seller->getFeeAmount();
                            $billing->amount_tax = $this->module->getFeeIncludeTax($billing->amount,$seller);
                            $billing->active = 0;
                            $billing->date_from = $seller->date_to;
                            if($fee_type=='monthly_fee')
                                $billing->date_to = date("Y-m-d H:i:s", strtotime($seller->date_to."+1 month"));
                            elseif($fee_type=='quarterly_fee')
                                $billing->date_to = date("Y-m-d H:i:s", strtotime($seller->date_to."+3 month"));
                            elseif($fee_type=='yearly_fee')
                                $billing->date_to = date("Y-m-d H:i:s", strtotime($seller->date_to."+1 year"));
                            else
                                $billing->date_to ='';
                            $billing->fee_type = $fee_type;
                            if($billing->add(true,true))
                            {
                                $seller->id_billing = $billing->id;
                                $seller->update();
                            }
                        }
                    }
                    if($seller->active==1)
                        $status = $this->module->displayText($this->l('Active'),'span','ets_mp_status actived');
                    elseif($seller->active==-2)
                        $status = $this->module->displayText($this->l('Expired'),'span','ets_mp_status expired');
                    elseif($seller->active==0)
                        $status = $this->module->displayText($this->l('Disabled'),'span','ets_mp_status disabled');
                    elseif($seller->active==-3)
                        $status = $this->module->displayText($this->l('Declined payment'),'span','ets_mp_status declined');
                    else
                        $status='';

                    if(isset($billing) && $billing->id)
                        $payment_verify = $this->module->displayText($this->l('Pending'),'span','ets_mp_status awaiting_payment');
                    else
                        $payment_verify ='';       
                    if($seller->date_from || $seller->date_to)
                        $date_approved = ($seller->date_from && $seller->date_from!='0000-00-00' ? $this->l('from'). ' '.Tools::displayDate($seller->date_from,null,false):'').' '.($seller->date_to && $seller->date_to!='0000-00-00' ? $this->l('to'). ' '.Tools::displayDate($seller->date_to):'');
                    else
                        $date_approved = $this->l('unlimited');
                    if(Tools::isSubmit('ajax'))
                    {
                        die(
                            json_encode(
                                array(
                                    'success' => $this->l('Updated successfully'),
                                    'status' => $status,
                                    'active' => $seller->active,
                                    'id_seller' => $seller->id,
                                    'date_approved' => $date_approved,
                                    'payment_verify' =>$payment_verify,
                                )
                            )
                        );
                    }
                }
                else
                {
                    if(Tools::isSubmit('ajax'))
                    {
                        die(
                            json_encode(
                                array(
                                    'errors' => $this->l('Update failed'),
                                )
                            )
                        );
                    }
                }
            }
        }
    }
    public function renderList()
    {
        $this->module->getContent();
        $this->context->smarty->assign(
            array(
                'ets_mp_body_html'=> $this->renderSellers(),
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
    public function renderSellers($filter = '', $title = '')
    {
        if (!$title)
            $title = $this->l('Shops');
        $id_seller = (int)Tools::getValue('id_seller');
        if (Tools::isSubmit('viewseller') && $id_seller) {
            return $this->renderInfoSeller($id_seller);
        }
        if (Tools::isSubmit('editets_seller') && $id_seller) {
            return $this->_renderFormSeller();
        }
        if (Tools::isSubmit('del') && $id_seller) {
            $seller = new Ets_mp_seller($id_seller);
            if (Validate::isLoadedObject($seller) && $seller->delete()) {
                $this->context->cookie->success_message = $this->l('Deleted shop successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceSellers') . '&list=true');
            }
        }
        if (Tools::isSubmit('delproducts') && $id_seller) {
            $seller = new Ets_mp_seller($id_seller);
            if (Validate::isLoadedObject($seller) && $seller->deleteProducts()) {
                $this->context->cookie->success_message = $this->l('Deleted products successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceSellers') . '&list=true');
            }
        }
        if (Tools::isSubmit('delcommissions') && $id_seller) {
            $seller = new Ets_mp_seller($id_seller);
            if (Validate::isLoadedObject($seller) && $seller->deleteCommissions()) {
                $this->context->cookie->success_message = $this->l('Deleted commissions successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceSellers') . '&list=true');
            }
        }
        if (Tools::isSubmit('delall') && $id_seller) {
            $seller = new Ets_mp_seller($id_seller);
            if (Validate::isLoadedObject($seller) && $seller->delete(true)) {
                $this->context->cookie->success_message = $this->l('Deleted shop and all data successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceSellers') . '&list=true');
            }
        }
        $fields_list = array(
            'id_seller' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'shop_name' => array(
                'title' => $this->l('Shop name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
            ),
            'seller_name' => array(
                'title' => $this->l('Seller name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
            ),
            'seller_email' => array(
                'title' => $this->l('Seller email'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'reference' => array(
                'title' => $this->l('Invoice ref'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'payment_status' => array(
                'title' => $this->l('Payment status'),
                'type' => 'select',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'active',
                    'value' => 'title',
                    'list' => array(
                        array(
                            'active' => -1,
                            'title' => $this->l('Canceled')
                        ),
                        array(
                            'active' => 0,
                            'title' => $this->l('Pending')
                        ),
                        array(
                            'active' => 1,
                            'title' => $this->l('Paid')
                        ),
                    )
                )
            ),
            'date_from' => array(
                'title' => $this->l('Available from'),
                'type' => 'date',
                'sort' => true,
                'filter' => true
            ),
            'date_to' => array(
                'title' => $this->l('Available to'),
                'type' => 'date',
                'sort' => true,
                'filter' => true
            ),
            'total_reported' => array(
                'title' => $this->l('Reported'),
                'type' => 'int',
                'sort' => true,
                'class' => 'text-center',
                'filter' => true
            ),
            'group_name' => array(
                'title' => $this->l('Shop group'),
                'type' => 'text',
                'sort' => true,
                'filter' => true, // seller_group_lang
            ),
            'category_name' => array(
                'title' => $this->l('Shop category'),
                'type' => 'select',
                'sort' => true,
                'filter' => true,
                'filter_list' => array(
                    'id_option' => 'id_ets_mp_shop_category',
                    'value' => 'name',
                    'list' => Ets_mp_shop_category::getShopCategories(' AND c.active=1', 0, false),
                )
            ),
            'active' => array(
                'title' => $this->l('Shop status'),
                'type' => 'select',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'active',
                    'value' => 'title',
                    'list' => array(
                        array(
                            'active' => -4,
                            'title' => $this->l('Vacation')
                        ),
                        array(
                            'active' => -3,
                            'title' => $this->l('Declined payment')
                        ),
                        array(
                            'active' => -2,
                            'title' => $this->l('Expired')
                        ),
                        array(
                            'active' => -1,
                            'title' => $this->l('Pending')
                        ),
                        array(
                            'active' => 1,
                            'title' => $this->l('Active')
                        ),
                        array(
                            'active' => 0,
                            'title' => $this->l('Disabled')
                        ),
                    )
                )
            ),
        );
        //Filter
        $show_resset = false;
        if ($id_seller && !Tools::isSubmit('del') && !Tools::isSubmit('delproducts') && !Tools::isSubmit('delcommissions') && !Tools::isSubmit('delall')) {
            if (Validate::isUnsignedId($id_seller))
                $filter .= ' AND s.id_seller="' . (int)$id_seller . '"';
            $show_resset = true;
        }
        if (($seller_name = trim(Tools::getValue('seller_name'))) || $seller_name != '') {
            if (Validate::isCleanHtml($seller_name))
                $filter .= ' AND CONCAT(customer.firstname," ", customer.lastname) LIKE "%' . pSQL($seller_name) . '%"';
            $show_resset = true;
        }
        if (($seller_email = trim(Tools::getValue('seller_email'))) || $seller_email != '') {
            if (Validate::isCleanHtml($seller_email))
                $filter .= ' AND customer.email LIKE "%' . pSQL($seller_email) . '%"';
            $show_resset = true;
        }
        if (($shop_name = trim(Tools::getValue('shop_name'))) || $shop_name != '') {
            if (Validate::isCleanHtml($shop_name))
                $filter .= ' AND sl.shop_name LIKE "%' . pSQL($shop_name) . '%"';
            $show_resset = true;
        }
        if (($shop_description = trim(Tools::getValue('shop_description'))) || $shop_description != '') {
            if (Validate::isCleanHtml($shop_description))
                $filter .= ' AND sl.shop_description = "%' . pSQL($shop_description) . '%"';
            $show_resset = true;
        }
        if (($active = trim(Tools::getValue('active'))) || $active !== '') {
            if (Validate::isInt($active)) {
                if ($active == -4)
                    $filter .= ' AND s.vacation_mode=1';
                else
                    $filter .= ' AND s.active="' . (int)$active . '"';
            }
            $show_resset = true;
        }
        if (($payment_status = trim(Tools::getValue('payment_status'))) || $payment_status !== '') {
            if (Validate::isInt($payment_status))
                $filter .= ' AND b.active="' . (int)$payment_status . '"';
            $show_resset = true;
        }
        if (($reference = trim(Tools::getValue('reference'))) || $reference !== '') {
            if (Validate::isCleanHtml($reference))
                $filter .= ' AND b.reference like "%' . pSQL($reference) . '%"';
            $show_resset = true;
        }
        if (($date_from_min = trim(Tools::getValue('date_from_min'))) || $date_from_min != '') {
            if (Validate::isDate($date_from_min))
                $filter .= ' AND s.date_from >= "' . pSQL($date_from_min) . ' 00:00:00"';
            $show_resset = true;
        }
        if (($date_from_max = trim(Tools::getValue('date_from_max'))) || $date_from_max != '') {
            if (Validate::isDate($date_from_max))
                $filter .= ' AND s.date_from <= "' . pSQL($date_from_max) . ' 23:59:59"';
            $show_resset = true;
        }
        if (($date_to_min = trim(Tools::getValue('date_to_min'))) || $date_to_min != '') {
            if (Validate::isDate($date_to_min))
                $filter .= ' AND s.date_to >="' . pSQL($date_to_min) . ' 00:00:00"';
            $show_resset = true;
        }
        if (($date_to_max = trim(Tools::getValue('date_to_max'))) || $date_to_max != '') {
            if (Validate::isDate($date_to_max))
                $filter .= ' AND s.date_to <="' . pSQL($date_to_max) . ' 23:59:59"';
            $show_resset = true;
        }
        if (($total_reported_min = trim(Tools::getValue('total_reported_min'))) || $total_reported_min != '') {
            if (Validate::isInt($total_reported_min))
                $filter .= ' AND seller_report.total_reported >= ' . (int)$total_reported_min;
            $show_resset = true;
        }
        if (($total_reported_max = trim(Tools::getValue('total_reported_max'))) || $total_reported_max != '') {
            if (Validate::isInt($total_reported_max))
                $filter .= ' AND seller_report.total_reported <= ' . (int)$total_reported_max;
            $show_resset = true;
        }
        if (($group_name = trim(Tools::getValue('group_name'))) || $group_name != '') {
            if (Validate::isCleanHtml($group_name))
                $filter .= ' AND seller_group_lang.name like "' . pSQL($group_name) . '"';
            $show_resset = true;
        }
        if (($category_name = trim(Tools::getValue('category_name'))) || $category_name != '') {
            if (Validate::isInt($category_name))
                $filter .= ' AND s.id_shop_category ="' . (int)$category_name . '"';
            $show_resset = true;
        }
        //Sort
        $sort = "";
        $sort_type = Tools::getValue('sort_type');
        $sort_value = Tools::getValue('sort');
        if ($sort_value) {
            switch ($sort_value) {
                case 'id_seller':
                    $sort .= ' s.id_seller';
                    break;
                case 'customer_name':
                    $sort .= ' customer_name';
                    break;
                case 'seller_name':
                    $sort .= ' seller_name';
                    break;
                case 'seller_email':
                    $sort .= ' seller_email';
                    break;
                case 'reference':
                    $sort .= 'b.reference';
                    break;
                case 'payment_status':
                    $sort .= 'b.active';
                    break;
                case 'shop_name':
                    $sort .= 'sl.shop_name';
                    break;
                case 'shop_description':
                    $sort .= 'sl.shop_description';
                    break;
                case 'active':
                    $sort .= 's.active';
                    break;
                case 'date_from':
                    $sort .= 's.date_from';
                    break;
                case 'date_to':
                    $sort .= 's.date_to';
                    break;
                case 'total_reported':
                    $sort .= 'seller_report.total_reported';
                    break;
                case 'group_name':
                    $sort .= 'seller_group_lang.name';
                    break;
                case 'category_name':
                    $sort .= 'scl.name';
                    break;
            }
            if ($sort && $sort_type && in_array($sort_type, array('asc', 'desc')))
                $sort .= ' ' . $sort_type;
        }
        //Paggination
        $page = (int)Tools::getValue('page');
        if ($page <= 0)
            $page = 1;
        $totalRecords = (int)Ets_mp_seller::_getSellers($filter, $sort, 0, 0, true);
        $paggination = new Ets_mp_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = Context::getContext()->link->getAdminLink('AdminMarketPlaceSellers') . '&page=_page_' . $this->module->getFilterParams($fields_list, 'ets_seller');
        $paggination->limit = (int)Tools::getValue('paginator_seller_select_limit', 20);
        $paggination->name = 'seller';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if ($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if ($start < 0)
            $start = 0;
        $sellers = Ets_mp_seller::_getSellers($filter, $sort, $start, $paggination->limit, false);
        if ($sellers) {
            foreach ($sellers as &$seller) {
                $seller['status_val'] = $seller['active'];
                $seller['child_view_url'] = Context::getContext()->link->getAdminLink('AdminMarketPlaceSellers') . '&viewseller=1&id_seller=' . (int)$seller['id_seller'];
                if ($seller['vacation_mode'] == 1) {
                    $seller['active'] = $this->module->displayText($this->l('Vacation'), 'span', 'ets_mp_status vacation');
                } else {
                    if ($seller['active'] == -1)
                        $seller['active'] = $this->module->displayText($this->l('Pending'), 'span', 'ets_mp_status pending');
                    elseif ($seller['active'] == 0)
                        $seller['active'] = $this->module->displayText($this->l('Disabled'), 'span', 'ets_mp_status disabled');
                    elseif ($seller['active'] == 1)
                        $seller['active'] = $this->module->displayText($this->l('Active'), 'span', 'ets_mp_status actived');
                    elseif ($seller['active'] == -2)
                        $seller['active'] = $this->module->displayText($this->l('Expired'), 'span', 'ets_mp_status expired');
                    elseif ($seller['active'] == -3)
                        $seller['active'] = $this->module->displayText($this->l('Declined payment'), 'span', 'ets_mp_status declined');
                }
                if ($seller['id_billing']) {
                    if ($seller['payment_status'] == -1)
                        $seller['payment_status'] = $this->module->displayText($this->l('Canceled'), 'span', 'ets_mp_status canceled');
                    elseif ($seller['payment_status'] == 0)
                        $seller['payment_status'] = $this->module->displayText($this->l('Pending') . ($seller['seller_confirm'] ? ' (' . $this->l('Seller confirmed') . ')' : ''), 'span', 'ets_mp_status pending');
                    elseif ($seller['payment_status'] == 1)
                        $seller['payment_status'] = $this->module->displayText($this->l('Paid'), 'span', 'ets_mp_status purchased');
                } else
                    $seller['payment_status'] = '--';
                $seller['seller_name'] = $this->module->displayText($seller['seller_name'], 'a', '', '', $this->module->getLinkCustomerAdmin($seller['id_customer']));

            }
        }
        $paggination->text = $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $listData = array(
            'name' => 'ets_seller',
            'actions' => array('view', 'edit', 'delete_shop', 'delete_product', 'delete_commission', 'delete_all'),
            'icon' => 'icon-sellers',
            'currentIndex' => Context::getContext()->link->getAdminLink('AdminMarketPlaceSellers') . ($paggination->limit != 20 ? '&paginator_seller_select_limit=' . $paggination->limit : ''),
            'postIndex' => Context::getContext()->link->getAdminLink('AdminMarketPlaceSellers'),
            'identifier' => 'id_seller',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $title,
            'fields_list' => $fields_list,
            'field_values' => $sellers,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list, 'ets_seller'),
            'show_reset' => $show_resset,
            'totalRecords' => $totalRecords,
            'sort' => $sort_value,
            'sort_type' => $sort_type,
        );
        return $this->module->renderList($listData);
    }
    public function renderInfoSeller($id_seller)
    {
        if (!Validate::isLoadedObject(new Ets_mp_seller($id_seller)))
            Tools::redirect($this->context->link->getAdminLink('AdminMarketPlaceSellers'));
        $errors = array();
        $amount = (float)Tools::getValue('amount', false);
        $action = Tools::getValue('action', false);
        $reason = Tools::getValue('reason', false);
        $active_seller = (int)Tools::getValue('active_seller');
        if (Tools::isSubmit('saveStatusSeller') && ($id_seller = Tools::getValue('id_seller')) && Validate::isUnsignedId($id_seller)) {
            $seller = new Ets_mp_seller($id_seller);
            $date_from = Tools::getValue('date_from');
            $date_to = Tools::getValue('date_to');
            if ($date_from && !Validate::isDate($date_from))
                $errors[] = $this->l('"From" date is not valid');
            if ($date_to && !Validate::isDate($date_to))
                $errors[] = $this->l('"To" date is not valid');
            if ($date_from && $date_to && Validate::isDate($date_from) && Validate::isDate($date_to) && strtotime($date_from) >= strtotime($date_to))
                $errors[] = $this->l('"From" date must be smaller than "To" date');
            if ($reason && !Validate::isCleanHtml($reason))
                $errors[] = $this->l('Reason is not valid');
            if (!$errors) {
                $seller->date_from = $date_from;
                $seller->date_to = $date_to;
                $active_old = $seller->active;
                if (!$active_seller) {
                    if ($seller->active == -1)
                        $seller->active = -3;
                    else
                        $seller->active = 0;
                } else {
                    if ((!$seller->date_from || strtotime($seller->date_from) <= strtotime(date('Y-m-d'))) && (!$seller->date_to || strtotime($seller->date_to) >= strtotime(date('Y-m-d'))))
                        $seller->active = 1;
                    else {
                        $seller->active = -2;
                        if ($seller->getFeeType() != 'no_fee')
                            $seller->payment_verify = -1;
                        else
                            $seller->payment_verify = 0;
                    }
                }
                $seller->reason = $reason;
                if ($seller->update(true)) {
                    if ($seller->active != $active_old && $seller->active == -2) {
                        $fee_type = $seller->getFeeType();
                        if ($fee_type != 'no_fee') {
                            $billing = new Ets_mp_billing();
                            $billing->id_seller = $seller->id;
                            $billing->amount = (float)$seller->getFeeAmount();
                            $billing->amount_tax = $this->module->getFeeIncludeTax($billing->amount, $seller);
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
                                if ($fee_type != 'no_fee') {
                                    $seller->id_billing = $billing->id;
                                    $seller->update();
                                }

                            }

                        }
                    }
                    if (Tools::isSubmit('ajax')) {
                        die(
                        json_encode(
                            array(
                                'success' => $this->l('Updated seller successfully'),
                                'seller_active' => $seller->active,
                            )
                        )
                        );
                    } else {
                        $this->context->cookie->success_message = $this->l('Updated seller successfully');
                    }
                } else
                    $errors[] = $this->l('Update failed');

            }
            if ($errors) {
                if (Tools::isSubmit('ajax')) {
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
        $seller = new Ets_mp_seller($id_seller, $this->context->language->id);
        $customer_seller = new Customer($seller->id_customer);
        if (Tools::isSubmit('add_commission_by_admin') || Tools::isSubmit('deduct_commission_by_admin')) {
            if (!$amount) {
                $errors[] = $this->l('Amount is required');
            } elseif (!Validate::isPrice($amount)) {
                if ($action == 'deduct')
                    $errors[] = $this->l('The commission is not enough to deduct');
                else
                    $errors[] = $this->l('Amount must be a decimal');
            }
            if (!$errors) {
                if ($action == 'deduct') {
                    $totalCommistionCanUse = $seller->getTotalCommission(1) - $seller->getToTalUseCommission(1);
                    if ($totalCommistionCanUse < $amount)
                        $errors[] = $this->l('Remaining commission is not enough to deduct.');
                    else {
                        $commission_usage = new Ets_mp_commission_usage();
                        $commission_usage->amount = $amount;
                        $commission_usage->status = 1;
                        $commission_usage->id_customer = $seller->id_customer;
                        $commission_usage->date_add = date('Y-m-d H:i:s');
                        $commission_usage->deleted = 0;
                        $commission_usage->note = $reason;
                        $commission_usage->id_currency = $this->context->currency->id;
                        $commission_usage->id_shop = $this->context->shop->id;
                        if ($commission_usage->add(true, true))
                            $this->context->cookie->success_message = $this->l('Deducted successfully');
                    }
                } else {
                    $commisstion = new Ets_mp_commission();
                    $commisstion->commission = $amount;
                    $commisstion->id_shop = $this->context->shop->id;
                    $commisstion->id_customer = $seller->id_customer;
                    $commisstion->status = 1;
                    $commisstion->note = $reason;
                    $commisstion->add();
                    $this->context->cookie->success_message = $this->l('Added successfully');
                }
            }
        }
        if ($seller->latitude != 0 && $seller->longitude != 0) {
            $default_country = new Country((int)Tools::getCountry());
            if (($map_key = Configuration::get('ETS_MP_GOOGLE_MAP_API')))
                $key = 'key=' . $map_key . '&';
            else
                $key = '';
            $link_map_google = 'http' . ((Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 's' : '') . '://maps.googleapis.com/maps/api/js?' . $key . 'region=' . Tools::substr($default_country->iso_code, 0, 2);
            $this->context->smarty->assign(
                array(
                    'link_map_google' => $link_map_google,
                )
            );
        }
        $this->context->smarty->assign(
            array(
                'seller' => $seller,
                'seller_billing' => $seller->id_billing ? new Ets_mp_billing($seller->id_billing) : false,
                'customer' => $customer_seller,
                'link_customer' => $this->module->getLinkCustomerAdmin($customer_seller->id),
                'link' => $this->context->link,
                'currency' => $this->context->currency,
                'amount' => $errors ? $amount : '',
                'action' => $errors ? $action : '',
                'reason' => $errors ? $reason : '',
                'history_billings' => $this->module->renderBilling($seller->id_customer),
                'history_commissions' => $this->module->renderCommission($seller->id_customer),
                'base_link' => $this->module->getBaseLink(),
                'shop_category' => ($seller->id_shop_category && ($shop_category = new Ets_mp_shop_category($seller->id_shop_category, $this->context->language->id)) && Validate::isLoadedObject($shop_category)) ? $shop_category : false,
            )
        );
        $html = '';
        if ($errors)
            $html .= $this->module->displayError($errors);
        return $html . $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'ets_marketplace/views/templates/hook/seller_info.tpl');
    }

    public function _renderFormSeller()
    {
        $html = '';
        $id_seller = (int)Tools::getValue('id_seller');
        if (Tools::isSubmit('saveSeller') && $id_seller && Validate::isUnsignedId($id_seller) && Validate::isLoadedObject(new Ets_mp_seller($id_seller)) ) {
            $errors = array();
            $valueFieldPost = array();
            $this->module->submitSaveSeller($id_seller, $errors, true, $valueFieldPost);
            if ($errors)
                $html .= $this->module->displayError($errors);
        }
        if ($id_seller && Validate::isUnsignedId($id_seller))
            $seller = new Ets_mp_seller($id_seller);
        else
            $seller = new Ets_mp_seller();
        if ($seller->active == -1) {
            $status = array(
                'query' => array(
                    array(
                        'id_option' => -1,
                        'name' => $this->l('Pending'),
                    ),
                    array(
                        'id_option' => 1,
                        'name' => $this->l('Active'),
                    ),
                    array(
                        'id_option' => 0,
                        'name' => $this->l('Disabled'),
                    ),
                    array(
                        'id_option' => -3,
                        'name' => $this->l('Declined'),
                    ),
                ),
                'id' => 'id_option',
                'name' => 'name'
            );
        } else
            $status = array(
                'query' => array(
                    array(
                        'id_option' => '1',
                        'name' => $this->l('Activate'),
                    ),
                    array(
                        'id_option' => 0,
                        'name' => $this->l('Disabled'),
                    ),
                ),
                'id' => 'id_option',
                'name' => 'name'
            );
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Edit shop'),
                    'icon' => 'icon-sellers',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Seller name'),
                        'name' => 'seller_name',
                        'required' => true,
                        'disabled' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Seller email'),
                        'name' => 'seller_email',
                        'disabled' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'shop_phone',
                        'label' => $this->l('Shop phone'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'shop_name',
                        'label' => $this->l('Shop name'),
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Shop description'),
                        'name' => 'shop_description',
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Shop address'),
                        'name' => 'shop_address',
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Latitude'),
                        'name' => 'latitude',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Longitude'),
                        'name' => 'longitude',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('VAT number'),
                        'name' => 'vat_number',
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Live Chat embed code'),
                        'name' => 'code_chat',
                        'desc' => $this->l('Enter here embed code of live chat service such as intercom.com, zendesk.com, etc. '),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Shop group'),
                        'name' => 'id_group',
                        'options' => array(
                            'query' => array_merge(array(array('id_group' => 0, 'name' => '--')), Ets_mp_seller_group::_getSellerGroups()),
                            'id' => 'id_group',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Commission rate'),
                        'name' => 'commission_rate',
                        'suffix' => '%',
                        'col' => 3,
                        'desc' => $this->l('If you leave this field blank, the default value') . ' ' . Tools::ps_round($seller->getCommissionRate(true), 2) . $this->l('% will be applied')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable commission by category'),
                        'name' => 'enable_commission_by_category',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'name' => 'category_commission_rate',
                        'label' => $this->l('Commission rate by categories'),
                        'type' => 'tre_categories',
                        'form_group_class' => 'category_commission_rate',
                        'tree' => $this->module->displayFormCategoryCommissionRate($id_seller ?: -1, 0),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Auto approve products submitted by this seller'),
                        'name' => 'auto_enabled_product',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 'default',
                                    'name' => $this->l('Default (base on general config)'),
                                ),
                                array(
                                    'id_option' => 'yes',
                                    'name' => $this->l('Auto approve'),
                                ),
                                array(
                                    'id_option' => 'no',
                                    'name' => $this->l('Manually approve by admin'),
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),

                    array(
                        'type' => 'file',
                        'label' => $this->l('Logo'),
                        'name' => 'shop_logo',
                        'required' => true,
                        'image' => $seller->shop_logo ? $this->module->displayText('', 'img', 'ets_mp_shoplogo', '', '', '', $this->module->getBaseLink() . '/img/mp_seller/' . $seller->shop_logo) : false,
                        'desc' => sprintf($this->l('Recommended size: 250x250 px. Accepted formats: jpg, png, gif. Limit %sMb'), Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')),
                    ),
                    array(
                        'type' => 'file_lang',
                        'label' => $this->l('Shop banner'),
                        'name' => 'shop_banner',
                        'imageType' => 'banner',
                        'desc' => sprintf($this->l('Recommended size: 1170x170 px. Accepted formats: jpg, png, gif. Limit %sMb'), Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Banner URL'),
                        'name' => 'banner_url',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Facebook link'),
                        'name' => 'link_facebook',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Google link'),
                        'name' => 'link_google',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Instagram link'),
                        'name' => 'link_instagram',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Twitter link'),
                        'name' => 'link_twitter',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Shop category'),
                        'name' => 'id_shop_category',
                        'options' => array(
                            'query' => array_merge(array(array('id_ets_mp_shop_category' => 0, 'name' => '--')), Ets_mp_shop_category::getShopCategories(' AND c.active=1', 0, false)),
                            'id' => 'id_ets_mp_shop_category',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Status'),
                        'name' => 'active',
                        'options' => $status,
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->l('Available from'),
                        'name' => 'date_from',
                        'form_group_class' => 'seller_date',
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->l('Available to'),
                        'name' => 'date_to',
                        'form_group_class' => 'seller_date',
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Reason'),
                        'name' => 'reason',
                        'form_group_class' => 'seller_reason',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'buttons' => array(
                    array(
                        'href' => $this->context->link->getAdminLink('AdminMarketPlaceSellers', true),
                        'icon' => 'process-icon-cancel',
                        'title' => $this->l('Cancel'),
                    )
                ),
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this->module;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'saveSeller';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminMarketPlaceSellers', false) . '&editets_seller=1&id_seller=' . (int)$id_seller;
        $helper->token = Tools::getAdminTokenLite('AdminMarketPlaceSellers');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),

            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'fields_value' => $this->getSellerFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => _PS_IMG_ . 'mp_seller/',
            'link' => $this->context->link,
            'cancel_url' => $this->context->link->getAdminLink('AdminMarketPlaceSellers', true),
            'banner_del_link' => $this->context->link->getAdminLink('AdminMarketPlaceSellers') . '&editets_seller=1&id_seller=' . (int)$id_seller . '&deletebanner=1',
        );
        $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_seller');
        return $html . $helper->generateForm(array($fields_form));
    }

    public function getSellerFieldsValues()
    {
        $fields = array();
        $id_seller = (int)Tools::getValue('id_seller');
        $languages = Language::getLanguages(false);
        $seller = new Ets_mp_seller($id_seller);
        $fields['seller_name'] = Tools::getValue('seller_name', $seller->seller_name);
        $fields['seller_email'] = Tools::getValue('seller_email', $seller->seller_email);
        $fields['vat_number'] = Tools::getValue('seller_email', $seller->vat_number);
        $fields['shop_phone'] = Tools::getValue('shop_phone', $seller->shop_phone);
        $fields['active'] = Tools::getValue('active', $seller->active);
        $fields['id_seller'] = $id_seller;
        $fields['reason'] = Tools::getValue('reason', $seller->reason);
        $fields['commission_rate'] = Tools::getValue('commission_rate', $seller->commission_rate);
        $fields['code_chat'] = Tools::getValue('code_chat', $seller->code_chat);
        $fields['date_from'] = Tools::getValue('date_from', $seller->date_from);
        $fields['date_to'] = Tools::getValue('date_to', $seller->date_to);
        $fields['auto_enabled_product'] = Tools::getValue('auto_enabled_product', $seller->auto_enabled_product);
        $fields['link_facebook'] = Tools::getValue('link_facebook', $seller->link_facebook);
        $fields['link_google'] = Tools::getValue('link_google', $seller->link_google);
        $fields['link_instagram'] = Tools::getValue('link_instagram', $seller->link_instagram);
        $fields['link_twitter'] = Tools::getValue('link_twitter', $seller->link_twitter);
        $fields['id_shop_category'] = Tools::getValue('id_shop_category', $seller->id_shop_category);
        $latitude = Tools::getValue('latitude', $seller->latitude);
        $fields['latitude'] = $latitude != 0 ? $latitude : '';
        $longitude = Tools::getValue('longitude', $seller->longitude);
        $fields['longitude'] = $longitude != 0 ? $longitude : '';
        $fields['id_group'] = Tools::getValue('id_group', $seller->id_group);
        $fields['enable_commission_by_category'] = Tools::getValue('enable_commission_by_category', $seller->enable_commission_by_category);
        if ($languages) {
            foreach ($languages as $language) {
                $fields['shop_name'][$language['id_lang']] = Tools::getValue('shop_name_' . $language['id_lang'], $seller->shop_name[$language['id_lang']]);
                $fields['shop_description'][$language['id_lang']] = Tools::getValue('shop_description_' . $language['id_lang'], $seller->shop_description[$language['id_lang']]);
                $fields['shop_address'][$language['id_lang']] = Tools::getValue('shop_address_' . $language['id_lang'], $seller->shop_address[$language['id_lang']]);
                $fields['shop_banner'][$language['id_lang']] = $seller->shop_banner[$language['id_lang']];
                $fields['banner_url'][$language['id_lang']] = Tools::getValue('banner_url_' . $language['id_lang'], $seller->banner_url[$language['id_lang']]);
            }
        }
        return $fields;
    }
}