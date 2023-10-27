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
 * Class Ets_MarketPlaceDashboardModuleFrontController
 * @property \Ets_mp_seller $seller
 * @property \Ets_marketplace $module
 */
class Ets_MarketPlaceDashboardModuleFrontController extends ModuleFrontController
{
    public $seller;
    public function __construct()
	{
		parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
        if($this->module->is17)
        {
            $smarty = $this->context->smarty;
            smartyRegisterFunction($smarty, 'function', 'displayAddressDetail', array('AddressFormat', 'generateAddressSmarty'));
            smartyRegisterFunction($smarty, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
        }
	}
    public function postProcess()
    {
        parent::postProcess();
        if(!$this->context->customer->isLogged() || !($this->seller = $this->module->_getSeller(true)) )
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->module->_checkPermissionPage($this->seller))
            die($this->module->l('You do not have permission','dashboard'));
        if(Tools::isSubmit('actionSubmitCommissionsChart') && $data_type = Tools::getValue('actionSubmitCommissionsChart'))
        {
            $chart_labels = array();
            $seller_commission_datas= array();
            $turn_over_datas = array();
            $labelStringx= $this->module->l('Date','dashboard');
            $no_data_char_commission = true;
            $total_number_of_product_sold =0;
            $total_turn_over = 0;
            $total_earning_commission=0;
            if($data_type=='this_year')
            {
                $months= array(1,2,3,4,5,6,7,8,9,10,11,12);
                foreach($months as $month)
                {
                    $chart_labels[]= $month;
                    $turn_over = (float)$this->_getTotalTurnOver(' AND month(o.date_upd) = "'.pSQL($month).'" AND YEAR(o.date_upd) ="'.pSQL(date('Y')).'"');
                    $turn_over_datas[] = $turn_over;
                    $seller_commission=(float)$this->_getTotalSellerCommission(' AND month(date_upd) = "'.pSQL($month).'" AND YEAR(date_upd) ="'.pSQL(date('Y')).'"');
                    $seller_commission_datas[] = $seller_commission;
                    if($seller_commission!=0 || $turn_over!=0)
                        $no_data_char_commission = false;
                }
                $labelStringx = $this->module->l('Month','dashboard');
                $total_number_of_product_sold = $this->_getTotalNumberOfProductSold(' AND YEAR(o.date_upd)="'.pSQL(date('Y')).'"');
                $total_turn_over = $this->_getTotalTurnOver(' AND YEAR(o.date_upd)="'.pSQL(date('Y')).'"');
                $total_earning_commission = $this->_getTotalSellerCommission(' AND YEAR(date_upd) ="'.pSQL(date('Y')).'"');
            }
            if($data_type=='_year')
            {
                $months= array(1,2,3,4,5,6,7,8,9,10,11,12);
                foreach($months as $month)
                {
                    $chart_labels[]= $month;
                    $turn_over = (float)$this->_getTotalTurnOver(' AND month(o.date_upd) = "'.pSQL($month).'" AND YEAR(o.date_upd) ="'.pSQL(date('Y')-1).'"');
                    $turn_over_datas[] = $turn_over;
                    $seller_commission=(float)$this->_getTotalSellerCommission(' AND month(date_upd) = "'.pSQL($month).'" AND YEAR(date_upd) ="'.pSQL(date('Y')-1).'"');
                    $seller_commission_datas[] = $seller_commission;
                    if($seller_commission!=0 || $turn_over!=0)
                        $no_data_char_commission = false;
                }
                $labelStringx = $this->module->l('Month','dashboard');
                $total_number_of_product_sold = $this->_getTotalNumberOfProductSold(' AND YEAR(o.date_upd)="'.pSQL(date('Y')-1).'"');
                $total_turn_over = $this->_getTotalTurnOver(' AND YEAR(o.date_upd)="'.pSQL(date('Y')-1).'"');
                $total_earning_commission = $this->_getTotalSellerCommission(' AND YEAR(date_upd) ="'.pSQL(date('Y')-1).'"');
            }
            if($data_type=='this_month')
            {
                $days = (int)date('t', mktime(0, 0, 0, (int)date('m'), 1, (int)date('Y')));
                if($days)
                {
                    for($day=1; $day<=$days;$day++)
                    {
                        $chart_labels[] = $day;
                        $turn_over = (float)$this->_getTotalTurnOver(' AND day(o.date_upd) = "'.pSQL($day).'" AND MONTH(o.date_upd)="'.pSQL(date('m')).'" AND YEAR(o.date_upd) ="'.pSQL(date('Y')).'"');
                        $seller_commission=(float)$this->_getTotalSellerCommission(' AND day(date_upd) = "'.pSQL($day).'" AND MONTH(date_upd)="'.pSQL(date('m')).'" AND YEAR(date_upd) ="'.pSQL(date('Y')).'"');
                        $turn_over_datas[] = $turn_over;
                        $seller_commission_datas[] = $seller_commission;
                        if($turn_over||$seller_commission)
                            $no_data_char_commission=false;
                    }
                }
                $labelStringx = $this->module->l('Day','dashboard');
                $total_number_of_product_sold = $this->_getTotalNumberOfProductSold(' AND MONTH(o.date_upd)="'.pSQL(date('m')).'" AND YEAR(o.date_upd)="'.pSQL(date('Y')).'"');
                $total_turn_over = $this->_getTotalTurnOver(' AND MONTH(o.date_upd)="'.pSQL(date('m')).'" AND YEAR(o.date_upd)="'.pSQL(date('Y')).'"');
                $total_earning_commission = $this->_getTotalSellerCommission(' AND MONTH(date_upd) ="'.pSQL(date('m')).'" AND YEAR(date_upd) ="'.pSQL(date('Y')).'"');
            }
            if($data_type=='_month')
            {
                $now_month = date('m');
                if($now_month=='01')
                {
                    $month = '12';
                    $year = date('Y')-1;
                }
                else
                {
                    $month = (int)$now_month-1;
                    if($month<10)
                        $month ='0'.$month;
                    $year = date('Y');
                }
                $days = (int)date('t', mktime(0, 0, 0, (int)$month, 1, (int)$year));
                if($days)
                {
                    for($day=1; $day<=$days;$day++)
                    {
                        $chart_labels[] = $day;
                        $turn_over = (float)$this->_getTotalTurnOver(' AND day(o.date_upd) = "'.pSQL($day).'" AND MONTH(o.date_upd)="'.pSQL($month).'" AND YEAR(o.date_upd) ="'.pSQL($year).'"');
                        $seller_commission=(float)$this->_getTotalSellerCommission(' AND day(date_upd) = "'.pSQL($day).'" AND MONTH(date_upd)="'.pSQL($month).'" AND YEAR(date_upd) ="'.pSQL($year).'"');
                        $turn_over_datas[] = $turn_over;
                        $seller_commission_datas[] = $seller_commission;
                        if($turn_over||$seller_commission)
                            $no_data_char_commission=false;
                    }
                }
                $labelStringx = $this->module->l('Day','dashboard');
                $total_number_of_product_sold = $this->_getTotalNumberOfProductSold(' AND MONTH(o.date_upd)="'.pSQL($month).'" AND YEAR(o.date_upd)="'.pSQL($year).'"');
                $total_turn_over = $this->_getTotalTurnOver(' AND MONTH(o.date_upd)="'.pSQL($month).'" AND YEAR(o.date_upd)="'.pSQL($year).'"');
                $total_earning_commission = $this->_getTotalSellerCommission(' AND MONTH(date_upd) ="'.pSQL($month).'" AND YEAR(date_upd) ="'.pSQL($year).'"');
            }
            if($data_type=='time_range' || $data_type=='all_time')
            {
                $date_from = Tools::getValue('date_from');
                $date_to = Tools::getValue('date_to');
                if($data_type=='time_range' && Validate::isDate($date_from) && Validate::isDate($date_to))
                {
                    $start_date = $date_from.' 00:00:00';
                    $end_date = $date_to.' 23:59:59';
                }
                else
                {
                    $min_order = Ets_mp_billing::getMinDateUpdOrder($this->seller->id_customer);
                    $max_order = Ets_mp_billing::getMaxDateUpdOrder($this->seller->id_customer);
                    $min_commission = Ets_mp_commission::getMinDateUpdCommission();
                    $max_commission = Ets_mp_commission::getMaxDateUpdCommission();
                    $start_date = $min_order;
                    $end_date = $max_order;
                    if((!$start_date || strtotime($start_date) > strtotime($min_commission)) && $min_commission )
                        $start_date = $min_commission;
                    if((!$end_date || strtotime($end_date) < strtotime($max_commission)) && $max_commission)
                        $end_date = $max_commission;
                    if($start_date && $end_date)
                    {
                        if(date('Y-m-d',strtotime($start_date))==date('Y-m-d',strtotime($end_date)))
                        {
                            $start_date = date('Y-m-d 00:00:00',strtotime($start_date)-86400);
                            $end_date = date('Y-m-d 23:59:59',strtotime($end_date)+86400);
                        }
                        else{
                            $start_date = date('Y-m-d 00:00:00',strtotime($start_date));
                            $end_date = date('Y-m-d 23:59:59',strtotime($end_date));
                        }
                    }
                }
                if(isset($start_date) && isset($end_date) && $start_date && $end_date)
                {
                    if (date('Y', strtotime($start_date)) != date('Y', strtotime($end_date)))
                    {
                        $years = $this->module->getYearRanger($start_date,$end_date,'Y');
                        if($years)
                        {
                            foreach($years as $year)
                            {
                                $chart_labels[] = $year;
                                $turn_over = (float)$this->_getTotalTurnOver(' AND o.date_upd<="'.pSQL($end_date).'" AND o.date_upd>="'.pSQL($start_date).'" AND YEAR(o.date_upd) ="'.pSQL($year).'"');
                                $seller_commission=(float)$this->_getTotalSellerCommission(' AND date_upd<="'.pSQL($end_date).'" AND date_upd>="'.pSQL($start_date).'" AND YEAR(date_upd) ="'.pSQL($year).'"');
                                $turn_over_datas[] = $turn_over;
                                $seller_commission_datas[] = $seller_commission;
                                if($turn_over!=0 || $seller_commission!=0)
                                    $no_data_char_commission=false;
                            }
                        }
                        $labelStringx = $this->module->l('Year','dashboard');
                        
                    }
                    elseif((int)date('m', strtotime($start_date)) != (int)date('m', strtotime($end_date))) 
                    {
                        $months = $this->module->getDateRanger($start_date,$end_date,'m',false,'month');
                        if($months)
                        {
                            $year = date('Y', strtotime($start_date));
                            foreach($months as $month)
                            {
                                $chart_labels[] = $month;
                                $turn_over = (float)$this->_getTotalTurnOver(' AND o.date_upd<="'.pSQL($end_date).'" AND o.date_upd>="'.pSQL($start_date).'" AND MONTH(o.date_upd)="'.pSQL($month).'" AND YEAR(o.date_upd) ="'.pSQL($year).'"');
                                $seller_commission=(float)$this->_getTotalSellerCommission(' AND date_upd<="'.pSQL($end_date).'" AND date_upd >="'.pSQL($start_date).'" AND MONTH(date_upd)="'.pSQL($month).'" AND YEAR(date_upd) ="'.pSQL($year).'"');
                                $turn_over_datas[] = $turn_over;
                                $seller_commission_datas[] = $seller_commission;
                                if($turn_over!=0 || $seller_commission!=0)
                                    $no_data_char_commission=false;
                            }
                        }
                        $labelStringx = $this->module->l('Month','dashboard');
                        
                    }
                    else
                    {
                        $days = $this->module->getDateRanger($start_date,$end_date,'d');
                        if($days)
                        {
                            $year = date('Y', strtotime($start_date));
                            $month = date('m', strtotime($start_date));
                            foreach($days as $day)
                            {
                                $chart_labels[] = $day;
                                $turn_over = (float)$this->_getTotalTurnOver(' AND DAY(o.date_upd)="'.pSQL($day).'" AND MONTH(o.date_upd)="'.pSQL($month).'" AND YEAR(o.date_upd) ="'.pSQL($year).'"');
                                $seller_commission=(float)$this->_getTotalSellerCommission(' AND DAY(date_upd)="'.pSQL($day).'" AND MONTH(date_upd)="'.pSQL($month).'" AND YEAR(date_upd) ="'.pSQL($year).'"');
                                $turn_over_datas[] = $turn_over;
                                $seller_commission_datas[] = $seller_commission;
                                if($turn_over_datas!=0 || $seller_commission!=0)
                                    $no_data_char_commission=false;
                            }
                        }
                        $labelStringx = $this->module->l('Day','dashboard');
                    }
                    $total_number_of_product_sold = $this->_getTotalNumberOfProductSold(' AND o.date_upd >="'.pSQL($start_date).'" AND o.date_upd <="'.pSQL($end_date).'"');
                    $total_turn_over = $this->_getTotalTurnOver(' AND o.date_upd >="'.pSQL($start_date).'" AND o.date_upd <="'.pSQL($end_date).'"');
                    $total_earning_commission = $this->_getTotalSellerCommission(' AND date_upd <="'.pSQL($end_date).'" AND date_upd >="'.pSQL($start_date).'"');
                }
                
            }

            unset($no_data_char_commission);
            $commissions_line_datasets = array($turn_over_datas,$seller_commission_datas);
            die(
                json_encode(
                    array(
                        'label_datas' => $chart_labels,
                        'commissions_line_datasets' => $commissions_line_datasets,
                        'labelStringx' => $labelStringx,
                        'total_earning_commission' => Tools::displayPrice(Tools::convertPrice((float)$total_earning_commission)),
                        'total_turn_over' => Tools::displayPrice((float)$total_turn_over),
                        'total_number_of_product_sold'=>(int)$total_number_of_product_sold,
                    )
                )
            );
        }
    }
    public function initContent()
	{
		parent::initContent();
        $day_before_expired = (int)Configuration::get('ETS_MP_MESSAGE_EXPIRE_BEFORE_DAY');
        $date_expired = date('Y-m-d H:i:s',strtotime("+ $day_before_expired days"));
        if($this->seller && $this->seller->date_to!='' && $this->seller->date_to!='0000-00-00 00:00:00' && strtotime($this->seller->date_to)< strtotime($date_expired))
        {
            $going_to_be_expired = true;
        }
        else
            $going_to_be_expired = false;
        $this->context->smarty->assign(
            array(
                'html_content' => $this->_initContentDemo(),
                'path' => $this->module->getBreadCrumb(),
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false,
                'seller' => $this->seller,
                'going_to_be_expired'=>$going_to_be_expired,
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/dashboard.tpl');      
        else        
            $this->setTemplate('dashboard_16.tpl'); 
    }
    public function _initContent()
    {
        $no_data_char_commission= true;
        $turn_over_datas= array();
        $seller_commission_datas = array();
        $chart_labels = array();
        $days = (int)date('t', mktime(0, 0, 0, (int)date('m'), 1, (int)date('Y')));
        if($days)
        {
            for($day=1; $day<=$days;$day++)
            {
                $chart_labels[] = $day;
                $turn_over = (float)$this->_getTotalTurnOver(' AND day(o.date_upd) = "'.pSQL($day).'" AND MONTH(o.date_upd)="'.pSQL(date('m')).'" AND YEAR(o.date_upd) ="'.pSQL(date('Y')).'"');
                $seller_commission=(float)$this->_getTotalSellerCommission(' AND day(date_upd) = "'.pSQL($day).'" AND MONTH(date_upd)="'.pSQL(date('m')).'" AND YEAR(date_upd) ="'.pSQL(date('Y')).'"');
                $turn_over_datas[] = $turn_over;
                $seller_commission_datas[] = $seller_commission;
                if($turn_over||$seller_commission)
                    $no_data_char_commission=false;
            }
        }
        $commissions_line_datasets = array(
            array(
                'label'=> $this->module->l('Turnover','dashboard'),
                'data' =>$turn_over_datas,
                'backgroundColor'=>'rgba(163,225,212,0.3)',
                'borderColor'=>'rgba(163,225,212,1)',
                'borderWidth'=>1,
                'pointRadius' => 2,
                'lineTension'=> 0
            ),
            array(
                'label'=> $this->module->l('Earning commission','dashboard'),
                'data' =>$seller_commission_datas,
                'backgroundColor'=>'rgba(253,193,7,0.3)',
                'borderColor'=>'rgba(253,193,7,1)',
                'borderWidth'=>1,
                'pointRadius' => 2,
                'lineTension'=> 0
            ),
        );
        $this->context->smarty->assign(
            array(
                'ets_mp_url_search_product' => $this->context->link->getModuleLink($this->module->name,'ajax',array('ajaxSearchProduct'=>1,'disableCombination'=>1,'active'=>true)),
                'no_data_char_commission' => $no_data_char_commission,
                'current_currency' => $this->context->currency,
                'commissions_line_datasets' => $commissions_line_datasets,
                'chart_labels' => $chart_labels,
                'total_turn_over_all' => $this->_getTotalTurnOver(),
                'total_turn_over' => $this->_getTotalTurnOver(' AND MONTH(o.date_upd)="'.pSQL(date('m')).'" AND YEAR(o.date_upd)="'.pSQL(date('Y')).'"'),
                'total_earning_commission' => Tools::convertPrice($this->_getTotalSellerCommission(' AND YEAR(date_upd) ="'.pSQL(date('Y')).'" AND MONTH(date_upd) ="'.pSQL(date('m')).'"')),
                'total_commission_balance' => Tools::convertPrice($this->seller->getTotalCommission(1)-$this->seller->getToTalUseCommission(1)),
                'total_withdrawls' => Tools::convertPrice($this->seller->getToTalUseCommission(1,false,false,true)),
                'total_commission_used' => Tools::convertPrice($this->seller->getToTalUseCommission(1)),
                'best_selling_products' => $this->_getBestSellingProducts(),
                'total_number_of_product_sold' => $this->_getTotalNumberOfProductSold(' AND YEAR(o.date_upd)="'.pSQL(date('Y')).'" AND MONTH(o.date_upd)="'.pSQL(date('m')).'"'),
            )
        );
        return $this->module->displayTpl('dashboard/front/dashboard.tpl');
    }
    public function _initContentDemo()
    {
        $no_data_char_commission= true;
        $months= array(1,2,3,4,5,6,7,8,9,10,11,12);
        $turn_over_datas= array();
        $seller_commission_datas = array();
        $chart_labels = array();
        foreach($months as $month)
        {
            $chart_labels[]= $month;
            $turn_over = (float)$this->_getTotalTurnOver(' AND month(o.date_upd) = "'.pSQL($month).'" AND YEAR(o.date_upd) ="'.pSQL(date('Y')).'"');
            $turn_over_datas[] = $turn_over;
            $seller_commission=(float)$this->_getTotalSellerCommission(' AND month(date_upd) = "'.pSQL($month).'" AND YEAR(date_upd) ="'.pSQL(date('Y')).'"');
            $seller_commission_datas[] = $seller_commission;
            if($seller_commission!=0 || $turn_over!=0)
                $no_data_char_commission = false;
        }
        $commissions_line_datasets = array(
            array(
                'label'=> $this->module->l('Turnover','dashboard'),
                'data' =>$turn_over_datas,
                'backgroundColor'=>'rgba(163,225,212,0.3)',
                'borderColor'=>'rgba(163,225,212,1)',
                'borderWidth'=>1,
                'pointRadius' => 2,
                'lineTension'=> 0
            ),
            array(
                'label'=> $this->module->l('Earning commission','dashboard'),
                'data' =>$seller_commission_datas,
                'backgroundColor'=>'rgba(253,193,7,0.3)',
                'borderColor'=>'rgba(253,193,7,1)',
                'borderWidth'=>1,
                'pointRadius' => 2,
                'lineTension'=> 0
            ),
        );
        $this->context->smarty->assign(
            array(
                'ets_mp_url_search_product' => $this->context->link->getModuleLink($this->module->name,'ajax',array('ajaxSearchProduct'=>1,'disableCombination'=>1,'active'=>true)),
                'no_data_char_commission' => $no_data_char_commission,
                'current_currency' => $this->context->currency,
                'commissions_line_datasets' => $commissions_line_datasets,
                'chart_labels' => $chart_labels,
                'total_turn_over_all' => $this->_getTotalTurnOver(),
                'total_turn_over' => $this->_getTotalTurnOver(' AND YEAR(o.date_upd)="'.pSQL(date('Y')).'"'),
                'total_earning_commission' => Tools::convertPrice($this->_getTotalSellerCommission(' AND YEAR(date_upd) ="'.pSQL(date('Y')).'"')),
                'total_commission_balance' => Tools::convertPrice($this->seller->getTotalCommission(1)-$this->seller->getToTalUseCommission(1)),
                'total_withdrawls' => Tools::convertPrice($this->seller->getToTalUseCommission(1,false,false,true)),
                'total_commission_used' => Tools::convertPrice($this->seller->getToTalUseCommission(1)),
                'best_selling_products' => $this->_getBestSellingProducts(),
                'total_number_of_product_sold' => $this->_getTotalNumberOfProductSold(' AND YEAR(o.date_upd)="'.pSQL(date('Y')).'"'),
            )
        );
        return $this->module->displayTpl('dashboard/front/dashboard.tpl');
    }
    public function _getTotalNumberOfProductSold($filter=false)
    {
        $id_product_chart = (int)Tools::getValue('id_product_chart');
        return $this->seller->_getTotalNumberOfProductSold($id_product_chart,$filter);
    }
    public function _getTotalTurnOver($filter=false)
    {
        if(Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'))
        {
            $id_product_chart = (int)Tools::getValue('id_product_chart');
            return $this->seller->getTotalTurnOver($id_product_chart,$filter);
        }
        return 0;
    }
    public function _getTotalSellerCommission($filter=false)
    {
        $id_product_chart = (int)Tools::getValue('id_product_chart');
        return $this->seller->getTotalCommission(1,$id_product_chart,$filter);
    }
    public function _getBestSellingProducts()
    {
        if(Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'))
            $status = explode(',',Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'));
        else
            return '';
        $products = Ets_mp_product::getBestSellingProducts($this->seller->id_customer);
        $products = Product::getProductsProperties($this->context->language->id,$products);
        if($products)
        {
            if($this->module->is17)
                $type_image= ImageType::getFormattedName('home');
            else
                $type_image= ImageType::getFormatedName('home');
            foreach($products as &$product)
            {
                $product['price'] = Tools::displayPrice($product['price']);
                if($product['id_image'])
                {
                    $product['image'] = Module::getInstanceByName('ets_marketplace')->displayText(Module::getInstanceByName('ets_marketplace')->displayText('','img','width_80','','','',$this->context->link->getImageLink($product['link_rewrite'],$product['id_image'],$type_image)),'a','','',$this->context->link->getProductLink($product['id_product']));
                }
                else
                    $product['image']='';
                $product['name'] = Module::getInstanceByName('ets_marketplace')->displayText($product['name'],'a','','',$this->context->link->getProductLink($product['id_product']));
                $product['commission'] = Tools::displayPrice(Tools::convertPrice($product['commission']));
                
            }
        }
        $this->context->smarty->assign(
            array(
                'products' => $products,
            )
        );
        return $this->module->displayTpl('dashboard/front/best_selling_products.tpl');
    }
 }