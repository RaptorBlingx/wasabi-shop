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
 * Class AdminMarketPlaceWithdrawalsController
 * @property \Ets_marketplace $module
 */
class AdminMarketPlaceWithdrawalsController extends ModuleAdminController
{
    public function __construct()
    {
       parent::__construct();
       $this->context= Context::getContext();
       $this->bootstrap = true;
    }
    public function initContent()
    {
        parent::initContent();
        if(Tools::isSubmit('downloadField') && ($id_field = (int)Tools::getValue('id_ets_mp_withdrawal_field')))
        {
            $withdrawField = new Ets_mp_withdraw_field(($id_field));
            $withdrawField->download();
        }
    }
    public function renderList()
    {
        $this->module->getContent();
        $this->context->smarty->assign(
            array(
                'ets_mp_body_html'=> $this->renderWithdraw(),
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
    public function renderWithdraw()
    {
        if((Tools::isSubmit('approveets_withdraw') || Tools::isSubmit('returnets_withdraw')|| Tools::isSubmit('deductets_withdraw') || Tools::isSubmit('del')) && ($id_ets_mp_withdrawal= (int)Tools::getValue('id_ets_mp_withdrawal')) && Validate::isUnsignedId($id_ets_mp_withdrawal) )
        {
            $submit= true;
            $withdraw_class = new Ets_mp_withdraw($id_ets_mp_withdrawal);
            $commissison_usage = Ets_mp_commission_usage::getCommissionUsageByIDWithDraw($id_ets_mp_withdrawal);
            if(Tools::isSubmit('del'))
            {
                if($withdraw_class->delete())
                {
                    $this->context->cookie->success_message = $this->l('Deleted successfully');
                    $commissison_usage->delete();
                }
            }
            else
            {
                if(Tools::isSubmit('approveets_withdraw'))
                {
                    $withdraw_class->status= 1;
                    $this->context->cookie->success_message = $this->l('Approved successfully');
                }
                elseif(Tools::isSubmit('returnets_withdraw'))
                {
                    $withdraw_class->status=-1;
                    $commissison_usage->status = 0;
                    $commissison_usage->note .= ($commissison_usage->note ? ' - ':'').$this->l('Returned commission');
                    $this->context->cookie->success_message = $this->l('Returned successfully');
                }
                elseif(Tools::isSubmit('deductets_withdraw'))
                {
                    $withdraw_class->status=-1;
                    $commissison_usage->status = 1;
                    $commissison_usage->note .= ($commissison_usage->note ? ' - ':'').$this->l('Deducted commission');
                    $this->context->cookie->success_message = $this->l('Deducted successfully');
                }
                if($withdraw_class->update())
                {
                    if($commissison_usage->id)
                    {
                        $commissison_usage->update();
                    }
                }

            }
            if(!Tools::isSubmit('viewwithdraw'))
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceWithdrawals'));
        }
        if(Tools::isSubmit('submitSaveNoteWithdrawal') && ($id_ets_mp_commission_usage =(int)Tools::getValue('id_ets_mp_commission_usage')) && Validate::isUnsignedId($id_ets_mp_commission_usage))
        {
            $note = Tools::getValue('note');
            if(!Validate::isCleanHtml($note))
                $this->module->_errors[] = $this->l('Note is not valid');
            else
            {
                $commission_usage = new Ets_mp_commission_usage($id_ets_mp_commission_usage);
                $commission_usage->note = $note;
                if($commission_usage->update())
                    $this->context->cookie->success_message= $this->l('Updated note successfully');
            }
        }
        if(Tools::isSubmit('viewwithdraw') && ($id_ets_mp_withdrawal = (int)Tools::getValue('id_ets_mp_withdrawal')) && Validate::isUnsignedId($id_ets_mp_withdrawal))
        {
            if(($withdraw_class = new Ets_mp_withdraw($id_ets_mp_withdrawal)) && Validate::isLoadedObject($withdraw_class))
            {
                $withdraw_fields = $withdraw_class->getListFields();
                if($withdraw_detail = $withdraw_class->getWithdrawalDetail())
                {
                    $this->context->smarty->assign(
                        array(
                            'withdraw_detail' => $withdraw_detail,
                            'link' => $this->context->link,
                            'seller' => new Ets_mp_seller($withdraw_detail['id_seller'],$this->context->language->id),
                            'withdraw_fields' => $withdraw_fields,
                        )
                    );
                    return  $this->context->smarty->fetch(_PS_MODULE_DIR_.'ets_marketplace/views/templates/hook/detail_withdraw.tpl');
                }
                else
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceWithdrawals'));
            }
            else
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceWithdrawals'));
        }
        $withdraw_status=array(
            array(
                'id' => '-1',
                'name' => $this->l('Declined')
            ),
            array(
                'id' => '0',
                'name' => $this->l('Pending')
            ),
            array(
                'id' => '1',
                'name' => $this->l('Approved')
            )
        );
        $fields_list = array(
            'id_ets_mp_withdrawal' => array(
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
                'strip_tag' =>false,
            ),
            'title' => array(
                'title' => $this->l('Payment name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'amount' => array(
                'title' => $this->l('Amount'),
                'type' => 'int',
                'sort' => true,
                'filter' => true
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'list' => $withdraw_status,
                    'id_option' => 'id',
                    'value' => 'name',
                ),
            ),
            'note' => array(
                'title'=> $this->l('Description'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
        );
        //Filter
        $show_resset = false;
        $filter = "";
        if(($id_ets_mp_withdrawal = (int)Tools::getValue('id_ets_mp_withdrawal')) && !$submit)
        {
            if(Validate::isUnsignedId($id_ets_mp_withdrawal))
                $filter .= ' AND w.id_ets_mp_withdrawal="'.(int)$id_ets_mp_withdrawal.'"';
            $show_resset = true;
        }
        if(($seller_name = Tools::getValue('seller_name')) || $seller_name!='' )
        {
            if(Validate::isCleanHtml($seller_name))
                $filter .=' AND CONCAT(customer.firstname," ",customer.lastname) LIKE "%'.pSQL($seller_name).'%"';
            $show_resset = true;
        }
        if(($title = trim(Tools::getValue('title'))) || $title!='')
        {
            if(Validate::isCleanHtml($title))
                $filter .= ' AND pml.title LIKE "%'.pSQL($title).'%"';
            $show_resset = true;
        }
        if(($amount_min = trim(Tools::getValue('amount_min'))) || $amount_min!='')
        {
            if(Validate::isFloat($amount_min))
                $filter .= ' AND cu.amount >= "'.(float)$amount_min.'"';
            $show_resset = true;
        }
        if(($amount_max = trim(Tools::getValue('amount_max'))) || $amount_max!='')
        {
            if(Validate::isFloat($amount_max))
                $filter .= ' AND cu.amount <= "'.(float)$amount_max.'"';
            $show_resset = true;
        }
        if(($status = trim(Tools::getValue('status'))) || $status!=='')
        {
            if(Validate::isInt($status))
                $filter .= ' AND w.status = "'.(int)$status.'"';
            $show_resset = true;
        }
        if(($note = trim(Tools::getValue('note'))) || $note!='')
        {
            if(Validate::isCleanHtml($note))
                $filter .=' AND cu.note LIKE "%'.pSQL($note).'%"';
            $show_resset = true;
        }

        //Sort
        $sort = "";
        $sort_type = Tools::getValue('sort_type','desc');
        $sort_value = Tools::getValue('sort','id_ets_mp_withdrawal');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'id_ets_mp_withdrawal':
                    $sort .=' w.id_ets_mp_withdrawal';
                    break;
                case 'seller_name':
                    $sort .= ' seller_name';
                    break;
                case 'title':
                    $sort .= ' pml.title';
                    break;
                case 'amount':
                    $sort .= ' cu.amount';
                    break;
                case 'status':
                    $sort .= ' w.status';
                    break;
                case 'note':
                    $sort.= ' cu.note';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('acs','desc')))
                $sort .= ' '.$sort_type;
        }
        //Paggination
        $page = (int)Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $totalRecords = (int) Ets_mp_withdraw::_getWithdrawals($filter,$sort,0,0,true);
        $paggination = new Ets_mp_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getAdminLink('AdminMarketPlaceWithdrawals').'&page=_page_'.$this->module->getFilterParams($fields_list,'ets_withdraw');
        $paggination->limit =  (int)Tools::getValue('paginator_withdraw_select_limit',20);
        $paggination->name ='withdraw';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $withdraws= Ets_mp_withdraw::_getWithdrawals($filter,$sort,$start,$paggination->limit,false);
        if($withdraws)
        {
            foreach($withdraws as &$withdraw)
            {
                $withdraw['child_view_url'] = $this->context->link->getAdminLink('AdminMarketPlaceWithdrawals').'&viewwithdraw&id_ets_mp_withdrawal='.(int)$withdraw['id_ets_mp_withdrawal'];
                $withdraw['amount'] = Tools::displayPrice($withdraw['amount'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));
                if($withdraw['status']==0)
                    $withdraw['change_status'] = true;
                else
                    $withdraw['change_status'] = false;
                if($withdraw['status']==-1)
                    $withdraw['status'] = $this->module->displayText($this->l('Declined','commissions'),'span','ets_mp_status declined');
                elseif($withdraw['status']==0)
                    $withdraw['status'] = $this->module->displayText($this->l('Pending','commissions'),'span','ets_mp_status pending');
                elseif($withdraw['status']==1)
                    $withdraw['status'] = $this->module->displayText($this->l('Approved','commissions'),'span','ets_mp_status approved');
                if($withdraw['id_customer_seller'])
                {
                    $withdraw['seller_name'] = $this->module->displayText($withdraw['seller_name'],'a','ets_mp_wdr_seller','',$this->module->getLinkCustomerAdmin($withdraw['id_customer_seller']));
                }
                else
                    $withdraw['seller_name'] = $this->module->displayText($this->l('Seller deleted'),'span','row_deleted');
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $listData = array(
            'name' => 'ets_withdraw',
            'actions' => array('view','approve','declined','delete'),
            'icon' => 'icon-withdraw',
            'currentIndex' => $this->context->link->getAdminLink('AdminMarketPlaceWithdrawals').($paggination->limit!=20 ? '&paginator_withdraw_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getAdminLink('AdminMarketPlaceWithdrawals'),
            'identifier' => 'id_ets_mp_withdrawal',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->l('Withdrawals'),
            'fields_list' => $fields_list,
            'field_values' => $withdraws,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list,'ets_withdraw'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'sort_type' => $sort_type,
        );
        return  $this->module->renderList($listData);
    }
}