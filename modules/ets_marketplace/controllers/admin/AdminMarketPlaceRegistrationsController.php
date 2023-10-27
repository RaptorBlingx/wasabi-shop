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
 * Class AdminMarketPlaceRegistrationsController
 * @property \Ets_marketplace $module
 */
class AdminMarketPlaceRegistrationsController extends ModuleAdminController
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
        if(Tools::isSubmit('ajax'))
            $this->renderList();
    }
    public function renderList()
    {
        $this->module->getContent();
        $this->context->smarty->assign(
            array(
                'ets_mp_body_html'=> $this->renderSellersRegistration(),
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
    public function renderSellersRegistration()
    {
        $id_registration = (int)Tools::getValue('id_registration');
        if(Tools::isSubmit('del') && $id_registration && Validate::isUnsignedId($id_registration))
        {
            $registration = new Ets_mp_registration($id_registration);
            if(Validate::isLoadedObject($registration) && $registration->delete())
            {
                $this->context->cookie->success_message = $this->l('Deleted successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceRegistrations').'&list=true');
            }
            else
                $this->module->_errors[] = $this->l('An error occurred while deleting the Application');
        }
        if(!Tools::isSubmit('post_filter') &&  Tools::isSubmit('saveStatusRegistration') && $id_registration && Validate::isUnsignedId($id_registration))
        {
            $registration = new Ets_mp_registration($id_registration);
            $seller = Ets_mp_seller::_getSellerByIdCustomer($registration->id_customer);
            $active_old = $registration->active;
            if($seller)
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->l('Seller created'),
                        )
                    )
                );
            }
            $registration->active = (int)Tools::getValue('active_registration');
            if((!$reason = Tools::getValue('reason')) || Validate::isCleanHtml($reason))
                $registration->reason= $reason;
            if((!$comment = Tools::getValue('comment')) || Validate::isCleanHtml($comment))
                $registration->comment= $comment;
            if(Validate::isLoadedObject($registration) &&  $registration->update())
            {
                if($registration->active!=$active_old)
                {
                    if(Configuration::get('ETS_MP_EMAIL_SELLER_APPLICATION_APPROVED_OR_DECLINED'))
                    {
                        $data =array(
                            '{seller_name}' => $registration->seller_name,
                            '{application_declined_reason}' => $reason,
                        );
                        if($registration->active==1)
                        {
                            $subjects = array(
                                'translation' => $this->l('Application has been approved'),
                                'origin'=> 'Application has been approved',
                                'specific'=>'registration'
                            );
                            Ets_marketplace::sendMail('to_seller_application_approved',$data,$registration->seller_email,$subjects,$registration->seller_name);
                        }
                        else
                        {
                            $subjects = array(
                                'translation' => $this->l('Application has been declined'),
                                'origin'=> 'Application has been declined',
                                'specific'=>'registration'
                            );
                            Ets_marketplace::sendMail('to_seller_application_declined',$data,$registration->seller_email,$subjects,$registration->seller_name);
                        }
                    }
                }
                if(Tools::isSubmit('ajax'))
                {
                    die(
                        json_encode(
                            array(
                                'success' => $this->l('Updated status successfully'),
                                'status' => $registration->active ? $this->module->displayText($this->l('Approved'),'span','ets_mp_status approved') : $this->module->displayText($this->l('Declined'),'span','ets_mp_status declined'),
                                'id_seller' => $registration->id,
                                'seller' => $seller ? true: false,
                            )
                        )
                    );
                }
                $this->context->cookie->success_message = $this->l('Updated status successfully');
            }
            else
                $this->module->_errors[] = $this->l('An error occurred while saving the application');
        }
        if(Tools::isSubmit('viewets_registration') && $id_registration && Validate::isUnsignedId($id_registration) && Validate::isLoadedObject(new Ets_mp_registration($id_registration)) )
        {
            return $this->renderFormSellersRegistration();
        }
        $fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'seller_name' => array(
                'title' => $this->l('Customer name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag'=>false,
            ),
            'seller_email' => array(
                'title' => $this->l('Customer email'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'message_to_administrator' => array(
                'title' => $this->l('Introduction'),
                'type'=>'text',
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'active',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'active' => 1,
                            'title' => $this->l('Approved')
                        ),
                        1 => array(
                            'active' => 0,
                            'title' => $this->l('Declined')
                        ),
                        2 => array(
                            'active' => -1,
                            'title' => $this->l('Pending'),
                        )
                    )
                )
            ),
        );
        //Filter
        $show_resset = false;
        $filter = "";
        if(($id=Tools::getValue('id')) && !Tools::isSubmit('saveStatusRegistration') && !Tools::isSubmit('del'))
        {
            if(Validate::isUnsignedId($id))
                $filter .= ' AND r.id_registration="'.(int)$id.'"';
            $show_resset = true;
        }
        if(($seller_name = trim(Tools::getValue('seller_name'))) || $seller_name!='')
        {
            if(Validate::isCleanHtml($seller_name))
                $filter .=' AND CONCAT(customer.firstname," ",customer.lastname) LIKE "%'.pSQL($seller_name).'%"';
            $show_resset = true;
        }
        if(($category_name = Tools::getValue('category_name')) || $category_name!='')
        {
            if(Validate::isGenericName($category_name))
                $filter .=' AND scl.name LIKE "%'.pSQL($category_name).'%"';
            $show_resset = false;
        }
        if(($seller_email = trim(Tools::getValue('seller_email'))) || $seller_email!='')
        {
            if(Validate::isCleanHtml($seller_email))
                $filter .=' AND customer.email LIKE "%'.pSQL($seller_email).'%"';
            $show_resset = true;
        }
        if(($shop_name = trim(Tools::getValue('shop_name'))) || $shop_name!='')
        {
            if(Validate::isCleanHtml($shop_name))
                $filter .= ' AND r.shop_name LIKE "%'.pSQL($shop_name).'%"';
            $show_resset = true;
        }
        if(($shop_description = trim(Tools::getValue('shop_description'))) || $shop_description!='')
        {
            if(Validate::isCleanHtml($shop_description))
                $filter .= ' AND r.shop_description = "%'.pSQL($shop_description).'%"';
            $show_resset = true;
        }
        if(($active = trim(Tools::getValue('active'))) || $active!='')
        {
            if(Validate::isInt($active))
                $filter .= ' AND r.active="'.(int)$active.'"';
            $show_resset=true;
        }
        //Sort
        $sort = "";
        $sort_type=Tools::getValue('sort_type');
        $sort_value = Tools::getValue('sort','id_registration');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'id':
                    $sort .=' r.id_registration';
                    break;
                case 'seller_name':
                    $sort .= ' seller_name';
                    break;
                case 'seller_email':
                    $sort .= ' seller_email';
                    break;
                case 'shop_name':
                    $sort .= 'r.shop_name';
                    break;
                case 'shop_description':
                    $sort .= 'r.shop_description';
                    break;
                case 'category_name':
                    $sort .='scl.name';
                    break;
                case 'active':
                    $sort .='r.active';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('acs','desc')))
                $sort .= ' '.$sort_type;
        }
        //Paggination
        $page = (int)Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $totalRecords = (int) Ets_mp_registration::_getRegistrations($filter,$sort,0,0,true);;
        $paggination = new Ets_mp_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getAdminLink('AdminMarketPlaceRegistrations').'&page=_page_'.$this->module->getFilterParams($fields_list,'ets_registration');
        $paggination->limit =  (int)Tools::getValue('paginator_registration_select_limit',20);
        $paggination->name ='registration';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $sellers_registration = Ets_mp_registration::_getRegistrations($filter,$sort,$start,$paggination->limit,false);
        if($sellers_registration)
        {
            foreach($sellers_registration as &$seller)
            {
                $seller['child_view_url'] = $this->context->link->getAdminLink('AdminMarketPlaceRegistrations').'&viewets_registration=1&id_registration='.$seller['id_registration'];
                $seller['status']= $seller['active'];
                if($seller['active']==-1)
                    $seller['active'] = $this->module->displayText($this->l('Pending'),'span','ets_mp_status pending');
                elseif($seller['active']==0)
                    $seller['active'] = $this->module->displayText($this->l('Declined'),'span','ets_mp_status declined');
                elseif($seller['active']==1)
                {
                    $seller['active'] = $this->module->displayText($this->l('Approved'),'span','ets_mp_status approved');
                }
                $seller['seller_name'] = $this->module->displayText($seller['seller_name'],'a','','',$this->module->getLinkCustomerAdmin($seller['id_customer']));
                $seller['has_seller'] = Ets_mp_seller::_getSellerByIdCustomer($seller['id_customer']) ? true : false;
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $listData = array(
            'name' => 'ets_registration',
            'actions' => array('approve_registration','decline_registration'),
            'icon' => 'icon-sellers_registration',
            'currentIndex' => $this->context->link->getAdminLink('AdminMarketPlaceRegistrations').($paggination->limit!=20 ? '&paginator_registration_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getAdminLink('AdminMarketPlaceRegistrations'),
            'identifier' => 'id_registration',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->l('Applications'),
            'fields_list' => $fields_list,
            'field_values' => $sellers_registration,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list,'ets_registration'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'sort_type' => $sort_type,
        );
        return  $this->module->renderList($listData);
    }
    public function renderFormSellersRegistration()
    {
        $id_registration = (int)Tools::getValue('id_registration');
        $registration = new Ets_mp_registration($id_registration);
        if(Validate::isLoadedObject($registration))
        {
            $this->context->smarty->assign(
                array(
                    'registration' => $registration,
                    'customer'=> new Customer($registration->id_customer),
                    'link'=> $this->context->link,
                    'has_seller' => Ets_mp_seller::_getSellerByIdCustomer($registration->id_customer) ? true :false,
                    'link_customer' =>$this->module->getLinkCustomerAdmin($registration->id_customer),
                    'shop_category' => ($registration->id_shop_category && ($shop_category = new Ets_mp_shop_category($registration->id_shop_category,$this->context->language->id)) && Validate::isLoadedObject($shop_category)) ? $shop_category : false,
                )
            );
            return $this->context->smarty->fetch(_PS_MODULE_DIR_.'ets_marketplace/views/templates/hook/shop/registration_detail.tpl');
        }
        return '';
    }
}