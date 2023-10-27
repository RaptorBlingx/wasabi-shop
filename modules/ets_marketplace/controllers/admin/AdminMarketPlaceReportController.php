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
 * Class AdminMarketPlaceReportController
 * @property \Ets_marketplace $module
 */
class AdminMarketPlaceReportController extends ModuleAdminController
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
        $controller = Tools::getValue('controller');
        if(!Validate::isControllerName($controller))
            $controller = '';
        if(Tools::isSubmit('del') && ($id_report = Tools::getValue('id_ets_mp_seller_report')) && Validate::isUnsignedId($id_report))
        {
            $report = new Ets_mp_report($id_report);
            if($report->delete())
            {
                $this->context->cookie->success_message = $this->l('Deleted successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink($controller).'&list=true');
            }
        }
    }
    public function renderList()
    {
        $this->module->getContent();
        $this->context->smarty->assign(
            array(
                'ets_mp_body_html'=> $this->renderReports(),
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
    public function renderReports()
    {
        $fields_list = array(
            'id_ets_mp_seller_report' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'reporter_name' => array(
                'title'=> $this->l('Reporter'),
                'type'=> 'text',
                'sort'=>true,
                'filter'=>true,
            ),
            'email' => array(
                'title' => $this->l('Email'),
                'type'=> 'text',
                'sort'=>true,
                'filter'=>true,
            ),
            'shop_name' => array(
                'title'=> $this->l('Shop'),
                'type'=> 'text',
                'sort'=>true,
                'filter'=>true,
                'strip_tag' => false,
            ),
            'product_name' => array(
                'title'=> $this->l('Product'),
                'type'=> 'text',
                'sort'=>true,
                'filter'=>true,
                'strip_tag' => false,
            ),
            'title' => array(
                'title' => $this->l('Title'),
                'type'=> 'text',
                'sort'=>true,
                'filter'=>true,
            ),
            'content' => array(
                'title'=> $this->l('Report content'),
                'type'=> 'text',
                'sort'=>true,
                'filter'=>true,
                'strip_tag' => false,
            )
        );
        $show_resset = false;
        $filter ='';
        if(($id_ets_mp_seller_report = Tools::getValue('id_ets_mp_seller_report')) && !Tools::isSubmit('del'))
        {
            if(Validate::isUnsignedId($id_ets_mp_seller_report))
                $filter .= ' AND r.id_ets_mp_seller_report="'.(int)$id_ets_mp_seller_report.'"';
            $show_resset = true;
        }
        if(($reporter_name = trim(Tools::getValue('reporter_name'))) || $reporter_name!='')
        {
            if(Validate::isCleanHtml($reporter_name))
                $filter .= ' AND CONCAT(reporter.firstname," ",reporter.lastname) LIKE "%'.pSQL($reporter_name).'%"';
            $show_resset = true;
        }
        if(($shop_name = trim(Tools::getValue('shop_name'))) || $shop_name!='')
        {
            if(Validate::isCleanHtml($shop_name))
                $filter .= ' AND sl.shop_name LIKE "%'.pSQL($shop_name).'%"';
            $show_resset=true;
        }
        if(($product_name =trim(Tools::getValue('product_name'))) || $product_name!='')
        {
            if(Validate::isCleanHtml($product_name))
                $filter .= ' AND pl.name LIKE "%'.pSQL($product_name).'%"';
            $show_resset=true;
        }
        if(($content = trim(Tools::getValue('content'))) || $content!='')
        {
            if(Validate::isCleanHtml($content))
                $filter .= ' AND r.content LIKE "%'.pSQL($content).'%"';
            $show_resset=true;
        }
        if(($email = trim(Tools::getValue('email'))) || $email!='')
        {
            if(Validate::isCleanHtml($email))
                $filter .= ' AND reporter.email LIKE "%'.pSQL($email).'%"';
            $show_resset=true;
        }
        if(($title = trim(Tools::getValue('title'))) || $title!='')
        {
            if(Validate::isCleanHtml($title))
                $filter .= ' AND r.title LIKE "%'.pSQL($title).'%"';
            $show_resset=true;
        }
        $sort = "";
        $sort_type=Tools::getValue('sort_type');
        $sort_value = Tools::getValue('sort','id_ets_mp_seller_report');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'id_ets_mp_seller_report':
                    $sort .=' r.id_ets_mp_seller_report';
                    break;
                case 'reporter_name':
                    $sort .=' reporter_name';
                    break;
                case 'product_name':
                    $sort .= ' pl.name';
                    break;
                case 'content':
                    $sort .= ' r.content';
                    break;
                case 'shop_name':
                    $sort .= 'sl.shop_name';
                    break;
                case 'email':
                    $sort .= 'reporter.email';
                    break;
                case 'title':
                    $sort .= 'r.title';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('acs','desc')))
                $sort .= ' '.$sort_type;
        }
        $page = (int)Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $totalRecords = (int) Ets_mp_report::_getReports($filter,$sort,0,0,true);
        $paggination = new Ets_mp_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getAdminLink('AdminMarketPlaceReport').'&page=_page_'.$this->module->getFilterParams($fields_list,'ets_report');
        $paggination->limit =  (int)Tools::getValue('paginator_report_select_limit',20);
        $paggination->name ='report';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $reports = Ets_mp_report::_getReports($filter,$sort,$start,$paggination->limit,false);
        if($reports)
        {
            foreach($reports as &$report)
            {
                $report['child_view_url'] = $this->context->link->getAdminLink('AdminMarketPlaceReport').'&viewreport=1&id_report='.(int)$report['id_ets_mp_seller_report'];
                if($report['id_product'])
                    $report['product_name'] = $this->module->displayText($report['product_name'],'a','','',$this->context->link->getProductLink($report['id_product']),'_blank');
                $report['shop_name'] = $this->module->displayText($report['shop_name'],'a','','',$this->module->getShopLink(array('id_seller'=>$report['id_seller'])),'_blank');
                $report['content'] = Tools::nl2br($report['content']);
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = 'links';
        $paggination->style_results = 'results';
        $listData = array(
            'name' => 'ets_report',
            'actions' => array('delete'),
            'icon' => 'icon-report',
            'currentIndex' => $this->context->link->getAdminLink('AdminMarketPlaceReport').($paggination->limit!=20 ? '&paginator_report_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getAdminLink('AdminMarketPlaceReport'),
            'identifier' => 'id_ets_mp_seller_report',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->l('Reports'),
            'fields_list' => $fields_list,
            'field_values' => $reports,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list,'ets_report'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'sort_type' => $sort_type,
        );
        return  $this->module->renderList($listData);
    }
}