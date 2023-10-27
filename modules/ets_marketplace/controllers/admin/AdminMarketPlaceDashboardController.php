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
 * Class AdminMarketPlaceDashboardController
 * @property \Ets_marketplace $module
 */
class AdminMarketPlaceDashboardController extends ModuleAdminController
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
        $date_from = Tools::getValue('date_from');
        if(!Validate::isDate($date_from))
            $date_from = date('Y-m-d');
        $date_to = Tools::getValue('date_to');
        if(!Validate::isDate($date_to))
            $date_to = date('Y-m-d');
        if(Tools::isSubmit('actionSubmitCommissionsChart') && $data_type= Tools::getValue('actionSubmitCommissionsChart'))
        {
            $chart_labels = array();
            $earnings_datas= array();
            $seller_fee_datas = array();
            $seller_revenve_datas= array();
            $labelStringx= $this->l('Date');
            $no_data = true;
            if($data_type=='this_year')
            {
                $months= array(1,2,3,4,5,6,7,8,9,10,11,12);
                foreach($months as $month)
                {
                    $chart_labels[] = $month;
                    $total_fee = (float)Ets_mp_billing::getTotalSellerFee(' AND month(b.date_upd) = "'.pSQL($month).'" AND YEAR(b.date_upd) ="'.pSQL(date('Y')).'"');
                    $total_revenve = (float)Ets_mp_commission::getTotalSellerRevenve(' AND month(sc.date_upd) = "'.pSQL($month).'" AND YEAR(sc.date_upd) ="'.pSQL(date('Y')).'"');
                    $earnings_datas[]= $total_fee+$total_revenve;
                    $seller_fee_datas[] = $total_fee;
                    $seller_revenve_datas[] = $total_revenve;
                    if($total_fee!=0 || $total_revenve!=0)
                        $no_data=false;
                }
                $labelStringx = $this->l('Month');
            }
            if($data_type=='_year')
            {
                $months= array(1,2,3,4,5,6,7,8,9,10,11,12);
                foreach($months as $month)
                {
                    $chart_labels[] = $month;
                    $total_fee = (float)Ets_mp_billing::getTotalSellerFee(' AND month(b.date_upd) = "'.pSQL($month).'" AND YEAR(b.date_upd) ="'.pSQL(date('Y')-1).'"');
                    $total_revenve = (float)Ets_mp_commission::getTotalSellerRevenve(' AND month(sc.date_upd) = "'.pSQL($month).'" AND YEAR(sc.date_upd) ="'.pSQL(date('Y')-1).'"');
                    $earnings_datas[]= $total_fee+$total_revenve;
                    $seller_fee_datas[] = $total_fee;
                    $seller_revenve_datas[] = $total_revenve;
                    if($total_fee!=0 || $total_revenve!=0)
                        $no_data=false;
                }
                $labelStringx = $this->l('Month');
            }
            if($data_type=='this_month')
            {
                $days = (int)date('t', mktime(0, 0, 0, (int)date('m'), 1, (int)date('Y')));
                if($days)
                {
                    for($day=1; $day<=$days;$day++)
                    {
                        $chart_labels[] = $day;
                        $total_fee = (float)Ets_mp_billing::getTotalSellerFee(' AND day(b.date_upd) = "'.pSQL($day).'" AND MONTH(b.date_upd)="'.pSQL(date('m')).'" AND YEAR(b.date_upd) ="'.pSQL(date('Y')).'"');
                        $total_revenve = (float)Ets_mp_commission::getTotalSellerRevenve(' AND day(sc.date_upd) = "'.pSQL($day).'" AND MONTH(sc.date_upd)="'.pSQL(date('m')).'" AND YEAR(sc.date_upd) ="'.pSQL(date('Y')).'"');
                        $earnings_datas[]= $total_fee+$total_revenve;
                        $seller_fee_datas[] = $total_fee;
                        $seller_revenve_datas[] = $total_revenve;
                        if($total_fee!=0 || $total_revenve!=0)
                            $no_data=false;
                    }
                }
                $labelStringx = $this->l('Day');
            }
            if($data_type=='_month')
            {
                $month = date('m',strtotime("-1 months"));
                $year = date('Y',strtotime("-1 months"));
                $days = (int)date('t', mktime(0, 0, 0, (int)$month, 1, (int)$year));
                if($days)
                {
                    for($day=1; $day<=$days;$day++)
                    {
                        $chart_labels[] = $day;
                        $total_fee = (float)Ets_mp_billing::getTotalSellerFee(' AND day(b.date_upd) = "'.pSQL($day).'" AND MONTH(b.date_upd)="'.pSQL($month).'" AND YEAR(b.date_upd) ="'.pSQL($year).'"');
                        $total_revenve = (float)Ets_mp_commission::getTotalSellerRevenve(' AND day(sc.date_upd) = "'.pSQL($day).'" AND MONTH(sc.date_upd)="'.pSQL($month).'" AND YEAR(sc.date_upd) ="'.pSQL($year).'"');
                        $earnings_datas[]= $total_fee+$total_revenve;
                        $seller_fee_datas[] = $total_fee;
                        $seller_revenve_datas[] = $total_revenve;
                        if($total_fee!=0 || $total_revenve!=0)
                            $no_data=false;
                    }
                }
                $labelStringx = $this->l('Day');
            }
            if($data_type=='time_range' || $data_type=='all_time')
            {
                if($data_type=='time_range')
                {
                    $start_date = $date_from.' 00:00:00';
                    $end_date = $date_to.' 23:59:59';
                }
                else
                {
                    $min_billing=  Ets_mp_billing::getMinDateUpdBilling();
                    $max_billing=  Ets_mp_billing::getMaxDateUpdBilling();
                    $min_commission = Ets_mp_commission::getMinDateUpdCommission();
                    $max_commission = Ets_mp_commission::getMaxDateUpdCommission();
                    $start_date = $min_billing;
                    $end_date = $max_billing;
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
                                $total_fee = (float)Ets_mp_billing::getTotalSellerFee(' AND b.date_upd<="'.pSQL($end_date).'" AND b.date_upd>="'.pSQL($start_date).'" AND YEAR(b.date_upd) ="'.pSQL($year).'"');
                                $total_revenve = (float)Ets_mp_commission::getTotalSellerRevenve(' AND sc.date_upd<="'.pSQL($end_date).'" AND sc.date_upd>="'.pSQL($start_date).'" AND YEAR(sc.date_upd) ="'.pSQL($year).'"');
                                $earnings_datas[]= $total_fee+$total_revenve;
                                $seller_fee_datas[] = $total_fee;
                                $seller_revenve_datas[] = $total_revenve;
                                if($total_fee!=0 || $total_revenve!=0)
                                    $no_data=false;
                            }
                        }
                        $labelStringx = $this->l('Year');
                        
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
                                $total_fee = (float)Ets_mp_billing::getTotalSellerFee(' AND b.date_upd<="'.pSQL($end_date).'" AND b.date_upd>="'.pSQL($start_date).'" AND MONTH(b.date_upd)="'.pSQL($month).'" AND YEAR(b.date_upd) ="'.pSQL($year).'"');
                                $total_revenve = (float)Ets_mp_commission::getTotalSellerRevenve(' AND sc.date_upd<="'.pSQL($end_date).'" AND sc.date_upd >="'.pSQL($start_date).'" AND MONTH(sc.date_upd)="'.pSQL($month).'" AND YEAR(sc.date_upd) ="'.pSQL($year).'"');
                                $earnings_datas[]= $total_fee+$total_revenve;
                                $seller_fee_datas[] = $total_fee;
                                $seller_revenve_datas[] = $total_revenve; 
                                if($total_fee!=0 || $total_revenve!=0)
                                    $no_data=false;
                            }
                        }
                        $labelStringx = $this->l('Month');
                        
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
                                $total_fee = (float)Ets_mp_billing::getTotalSellerFee(' AND DAY(b.date_upd)="'.pSQL($day).'" AND MONTH(b.date_upd)="'.pSQL($month).'" AND YEAR(b.date_upd) ="'.pSQL($year).'"');
                                $total_revenve = (float)Ets_mp_commission::getTotalSellerRevenve(' AND DAY(sc.date_upd)="'.pSQL($day).'" AND MONTH(sc.date_upd)="'.pSQL($month).'" AND YEAR(sc.date_upd) ="'.pSQL($year).'"');
                                $earnings_datas[]= $total_fee+$total_revenve;
                                $seller_fee_datas[] = $total_fee;
                                $seller_revenve_datas[] = $total_revenve;
                                if($total_fee!=0 || $total_revenve!=0)
                                    $no_data=false;
                            }
                        }
                        $labelStringx = $this->l('Day');
                    }
                }
                
            }
            if($no_data)
            {
                die(
                    json_encode(
                        array(
                            'no_data' => $no_data,
                        )
                    )
                );
            }
            $commissions_line_datasets = array($earnings_datas,$seller_fee_datas,$seller_revenve_datas);
            die(
                json_encode(
                    array(
                        'label_datas' => $chart_labels,
                        'commissions_line_datasets' => $commissions_line_datasets,
                        'labelStringx' => $labelStringx,
                    )
                )
            );
        }
        if(Tools::isSubmit('actionSubmitTurnOVerChart') && $data_type= Tools::getValue('actionSubmitTurnOVerChart'))
        {
            $chart_labels = array();
            $seller_commission_datas = array();
            $turn_over_datas = array();
            $labelStringx= $this->l('Date');
            $no_data= true;
            if($data_type=='this_year')
            {
                $months= array(1,2,3,4,5,6,7,8,9,10,11,12);
                foreach($months as $month)
                {
                    $chart_labels[] = $month;
                    $turn_over = (float)Ets_mp_commission::getTotalTurnOver(' AND month(o.date_upd) = "'.pSQL($month).'" AND YEAR(o.date_upd) ="'.pSQL(date('Y')).'"');
                    $turn_over_datas[] = $turn_over;
                    $seller_commission=(float)Ets_mp_commission::getTotalSellerCommission(' AND month(date_upd) = "'.pSQL($month).'" AND YEAR(date_upd) ="'.pSQL(date('Y')).'"');
                    $seller_commission_datas[]= $seller_commission;
                    if($turn_over!=0 || $seller_commission!=0)
                        $no_data=false;
                }
                $labelStringx = $this->l('Month');
            }
            if($data_type=='_year')
            {
                $months= array(1,2,3,4,5,6,7,8,9,10,11,12);
                foreach($months as $month)
                {
                    $chart_labels[] = $month;
                    $turn_over = (float)Ets_mp_commission::getTotalTurnOver(' AND month(o.date_upd) = "'.pSQL($month).'" AND YEAR(o.date_upd) ="'.pSQL(date('Y')-1).'"');
                    $turn_over_datas[] = $turn_over;
                    $seller_commission=(float)Ets_mp_commission::getTotalSellerCommission(' AND month(date_upd) = "'.pSQL($month).'" AND YEAR(date_upd) ="'.pSQL(date('Y')-1).'"');
                    $seller_commission_datas[]= $seller_commission;
                    if($turn_over!=0 || $seller_commission!=0)
                        $no_data=false;
                }
                $labelStringx = $this->l('Month');
            }
            if($data_type=='this_month')
            {
                $days = (int)date('t', mktime(0, 0, 0, (int)date('m'), 1, (int)date('Y')));
                if($days)
                {
                    for($day=1; $day<=$days;$day++)
                    {
                        $chart_labels[] = $day;
                        $turn_over = (float)Ets_mp_commission::getTotalTurnOver(' AND day(o.date_upd) = "'.pSQL($day).'" AND MONTH(o.date_upd)="'.pSQL(date('m')).'" AND YEAR(o.date_upd) ="'.pSQL(date('Y')).'"');;
                        $seller_commission=(float)Ets_mp_commission::getTotalSellerCommission(' AND day(date_upd) = "'.pSQL($day).'" AND MONTH(date_upd)="'.pSQL(date('m')).'" AND YEAR(date_upd) ="'.pSQL(date('Y')).'"');
                        $turn_over_datas[] = $turn_over;
                        $seller_commission_datas[] = $seller_commission;
                        if($turn_over!=0 || $seller_commission!=0)
                            $no_data = false;
                    }
                }
                $labelStringx = $this->l('Day');
            }
            if($data_type=='_month')
            {
                $month = date('m',strtotime("-1 months"));
                $year = date('Y',strtotime("-1 months"));
                $days = (int)date('t', mktime(0, 0, 0, (int)$month, 1, (int)$year));
                if($days)
                {
                    for($day=1; $day<=$days;$day++)
                    {
                        $chart_labels[] = $day;
                        $turn_over = (float)Ets_mp_commission::getTotalTurnOver(' AND day(o.date_upd) = "'.pSQL($day).'" AND MONTH(o.date_upd)="'.pSQL($month).'" AND YEAR(o.date_upd) ="'.pSQL($year).'"');;
                        $seller_commission=(float)Ets_mp_commission::getTotalSellerCommission(' AND day(date_upd) = "'.pSQL($day).'" AND MONTH(date_upd)="'.pSQL($month).'" AND YEAR(date_upd) ="'.pSQL($year).'"');
                        $turn_over_datas[] = $turn_over;
                        $seller_commission_datas[] = $seller_commission;
                        if($turn_over!=0 || $seller_commission!=0)
                            $no_data = false;
                    }
                }
                $labelStringx = $this->l('Day');
            }
            if($data_type=='time_range' || $data_type=='all_time')
            {
                if($data_type=='time_range')
                {
                    $start_date = $date_from.' 00:00:00';
                    $end_date = $date_to.' 23:59:59';
                }
                else
                {
                    $min_order = Ets_mp_billing::getMinDateUpdOrder();
                    $max_order = Ets_mp_billing::getMaxDateUpdOrder();
                    $min_commission = Ets_mp_commission::getMinDateUpdCommission();
                    $max_commission = Ets_mp_commission::getMaxDateUpdCommission();
                    $start_date = $min_order;
                    $end_date = $max_order;
                    if((!$start_date || strtotime($start_date) > strtotime($min_commission)) && $min_commission)
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
                                $turn_over = (float)Ets_mp_commission::getTotalTurnOver(' AND o.date_upd<="'.pSQL($end_date).'" AND o.date_upd>="'.pSQL($start_date).'" AND YEAR(o.date_upd) ="'.pSQL($year).'"');
                                $seller_commission=(float)Ets_mp_commission::getTotalSellerCommission(' AND date_upd<="'.pSQL($end_date).'" AND date_upd>="'.pSQL($start_date).'" AND YEAR(date_upd) ="'.pSQL($year).'"');
                                $turn_over_datas[]= $turn_over;
                                $seller_commission_datas[] = $seller_commission;
                                if($turn_over!=0 || $seller_commission!=0)
                                    $no_data=false;
                            }
                        }
                        $labelStringx = $this->l('Year');
                        
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
                                $turn_over = (float)Ets_mp_commission::getTotalTurnOver(' AND o.date_upd<="'.pSQL($end_date).'" AND o.date_upd>="'.pSQL($start_date).'" AND MONTH(o.date_upd)="'.pSQL($month).'" AND YEAR(o.date_upd) ="'.pSQL($year).'"');
                                $seller_commission=(float)Ets_mp_commission::getTotalSellerCommission(' AND date_upd<="'.pSQL($end_date).'" AND date_upd >="'.pSQL($start_date).'" AND MONTH(date_upd)="'.pSQL($month).'" AND YEAR(date_upd) ="'.pSQL($year).'"');
                                $turn_over_datas[] = $turn_over;
                                $seller_commission_datas[] = $seller_commission;
                                if($turn_over!=0 || $seller_commission!=0)
                                    $no_data = false; 
                            }
                        }
                        $labelStringx = $this->l('Month');
                        
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
                                $turn_over = (float)Ets_mp_commission::getTotalTurnOver(' AND DAY(o.date_upd)="'.pSQL($day).'" AND MONTH(o.date_upd)="'.pSQL($month).'" AND YEAR(o.date_upd) ="'.pSQL($year).'"');
                                $seller_commission=(float)Ets_mp_commission::getTotalSellerCommission(' AND DAY(date_upd)="'.pSQL($day).'" AND MONTH(date_upd)="'.pSQL($month).'" AND YEAR(date_upd) ="'.pSQL($year).'"');
                                $turn_over_datas[] = $turn_over;
                                $seller_commission_datas[] = $seller_commission;
                                if($turn_over!=0 || $seller_commission!=0)
                                    $no_data=false;
                            }
                        }
                        $labelStringx = $this->l('Day');
                    }
                }
                
            }
            if($no_data)
            {
                die(
                    json_encode(
                       array(
                            'no_data' => true,
                       )
                    )
                );
            }
            $turn_over_bar_datasets= array($seller_commission_datas,$turn_over_datas);
            die(
                json_encode(
                    array(
                        'label_datas' => $chart_labels,
                        'turn_over_bar_datasets' => $turn_over_bar_datasets,
                        'labelStringx' => $labelStringx,
                    )
                )
            );
        }
    }
    public function renderList()
    {
        $this->module->getContent();
        $this->context->smarty->assign(
            array(
                'ets_mp_body_html'=> $this->_renderDashboard(),
            )
        );
        return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'admin.tpl');
    }
    public function _renderDashboard()
    {
        $going_tobe_expired_sellers = Ets_mp_seller::getSellersGoingToBeExpired(false,true);
        $chart_labels = array();
        $earnings_datas= array();
        $seller_fee_datas = array();
        $seller_revenve_datas= array();
        $seller_commission_datas = array();
        $turn_over_datas = array();
        $months= array(1,2,3,4,5,6,7,8,9,10,11,12);
        $no_data_char_commision = true;
        $no_data_char_turn_over= true;
        foreach($months as $month)
        {
            $chart_labels[] = $month;
            $total_fee = (float)Ets_mp_billing::getTotalSellerFee(' AND month(b.date_upd) = "'.pSQL($month).'" AND YEAR(b.date_upd) ="'.pSQL(date('Y')).'"');
            $total_revenve = (float)Ets_mp_commission::getTotalSellerRevenve(' AND month(sc.date_upd) = "'.pSQL($month).'" AND YEAR(sc.date_upd) ="'.pSQL(date('Y')).'"');
            $earnings_datas[]= $total_fee+$total_revenve;
            $seller_fee_datas[] = $total_fee;
            $seller_revenve_datas[] = $total_revenve;
            $turn_over = (float)Ets_mp_commission::getTotalTurnOver(' AND month(o.date_upd) = "'.pSQL($month).'" AND YEAR(o.date_upd) ="'.pSQL(date('Y')).'"');
            $turn_over_datas[] = $turn_over;
            $seller_commission=(float)Ets_mp_commission::getTotalSellerCommission(' AND month(date_upd) = "'.pSQL($month).'" AND YEAR(date_upd) ="'.pSQL(date('Y')).'"');
            $seller_commission_datas[] = $seller_commission;
            if($total_fee!=0 || $total_revenve!=0 )
                $no_data_char_commision=false;
            if($seller_commission!=0 || $turn_over!=0)
                $no_data_char_turn_over = false;
        }
        $commissions_line_datasets = array(
            array(
                'label'=> $this->l('Earning'),
                'data' =>$earnings_datas,
                'backgroundColor'=>'rgba(163,225,212,0.3)',
                'borderColor'=>'rgba(163,225,212,1)',
                'borderWidth'=>1,
                'pointRadius' => 2,
                'lineTension'=> 0
            ),
            array(
                'label'=> $this->l('Seller fee'),
                'data' =>$seller_fee_datas,
                'backgroundColor'=>'rgba(253,193,7,0.3)',
                'borderColor'=>'rgba(253,193,7,1)',
                'borderWidth'=>1,
                'pointRadius' => 2,
                'lineTension'=> 0
            ),
            array(
                'label'=> $this->l('Revenue'),
                'data' =>$seller_revenve_datas,
                'backgroundColor'=>'rgba(139,195,72,0.3)',
                'borderColor'=>'rgba(139,195,72,1)',
                'borderWidth'=>1,
                'pointRadius' => 2,
                'lineTension'=> 0
            )
        );
        $turn_over_bar_datasets = array(
            array(
                'label'=> $this->l('Seller commissions'),
                'data' =>$seller_commission_datas,
                'backgroundColor'=>'rgba(163,225,212,0.3)',
                'borderColor'=>'rgba(163,225,212,1)',
                'borderWidth'=>1,
                'pointRadius' => 2,
                'lineTension'=> 0
            ),
            array(
                'label'=> $this->l('Turnover'),
                'data' =>$turn_over_datas,
                'backgroundColor'=>'rgba(253,193,7,0.3)',
                'borderColor'=>'rgba(253,193,7,1)',
                'borderWidth'=>1,
                'pointRadius' => 2,
                'lineTension'=> 0
            ),
        );
        $this->context->smarty->assign(
            array(
                'module' => $this->module,
                'last_withdraws'=> Ets_mp_withdraw::_getWithdrawals(false,' w.id_ets_mp_withdrawal DESC',0,5,false),
                'last_payment_billings' => Ets_mp_billing::getSellerBillings(false,false,0,10,'b.id_ets_mp_seller_billing DESC'),
                'going_tobe_expired_sellers' => $going_tobe_expired_sellers,
                'latest_orders' => $this->_getLatestOrders(),
                'latest_seller_commissions'=> $this->_getLatestSellerCommissions(),
                'latest_products' => $this->_getLatestProducts(),
                'best_selling_products' => $this->_getBestSellingProducts(),
                'top_sellers' => $this->_getTopSellers(),
                'top_seller_commissions' => $this->_getTopSellerCommissions(),
                'totalTurnOver' => Ets_mp_commission::getTotalTurnOver(),
                'totalSellerProduct' => Ets_mp_product::getSellerProducts(' AND sp.id_product is NOT NULL AND p.active=1',0,0,0,true),
                'totalSellerRevenve'=> Ets_mp_commission::getTotalSellerRevenve(),
                'totalSellerFee' => Ets_mp_billing::getTotalSellerFee(),
                'commissions_line_datasets' => $commissions_line_datasets,
                'chart_labels' => $chart_labels,
                'turn_over_bar_datasets' => $turn_over_bar_datasets,
                'totalSellerCommission' => Ets_mp_commission::getTotalSellerCommission(),
                'default_currency' => $this->context->currency,
                'no_data_char_commission' => $no_data_char_commision,
                'no_data_char_turn_over' => $no_data_char_turn_over,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/dashboard/admin_dashboard.tpl');
    }
    public function _getLatestOrders()
    {
        $latest_orders = Ets_mp_commission::getOrders(' AND so.id_order is NOT NULL',false,0,10,'o.id_order DESC');
        if($latest_orders)
        {
            foreach ($latest_orders as &$order)
            {
                $order['current_state'] = $this->module->displayOrderState($order['current_state']);
                $order['customer_name'] = Module::getInstanceByName('ets_marketplace')->displayText($order['customer_name'],'a','','',$this->module->getLinkCustomerAdmin($order['id_customer']));
                $order['link_view'] = $this->module->getLinkOrderAdmin($order['id_order']);
            }
        }
        $this->context->smarty->assign(
            array(
                'latest_orders' => $latest_orders,
                'module'=> $this->module,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/dashboard/latest_orders.tpl');
    }
    public function _getLatestSellerCommissions()
    {
        $latest_seller_commissions = Ets_mp_commission::getLatestSellerCommissions();
        if($latest_seller_commissions)
        {
            foreach($latest_seller_commissions as &$commission)
            {
                if($commission['id_product'])
                {
                    $commission['product_name'] = Module::getInstanceByName('ets_marketplace')->displayText($commission['product_name'],'a','','',$this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$commission['id_product'])));
                }
            }
        }
        $this->context->smarty->assign(
            array(
                'latest_seller_commissions' => $latest_seller_commissions,
                'module'=>$this->module,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/dashboard/latest_seller_commissions.tpl');
    }
    public function _getLatestProducts()
    {
        $products = Ets_mp_product::getSellerProducts(' AND sp.id_product is NOT NULL',1,10);
        if($products)
        {
            if($this->module->is17)
                $type_image= ImageType::getFormattedName('home');
            else
                $type_image= ImageType::getFormatedName('home');
            foreach($products as &$product)
            {
                $product['price'] = Tools::displayPrice($product['price']);
                $product['link'] = $this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$product['id_product']));
                if($product['id_image'])
                {
                    $product['image'] = Module::getInstanceByName('ets_marketplace')->displayText(Module::getInstanceByName('ets_marketplace')->displayText('','img','width_80','','','',$this->context->link->getImageLink($product['link_rewrite'],$product['id_image'],$type_image)),'a','','',$product['link']);
                }
                else
                    $product['image']='';
                $product['name'] = Module::getInstanceByName('ets_marketplace')->displayText($product['name'],'a','','',$product['link']);
            }
        }
        $this->context->smarty->assign(
            array(
                'lastest_products' => $products,
                'module'=>$this->module,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/dashboard/lastest_products.tpl');
    }
    public function _getBestSellingProducts()
    {
        $products = Ets_mp_product::getBestSellingProducts();
        if($products)
        {
            if($this->module->is17)
                $type_image= ImageType::getFormattedName('home');
            else
                $type_image= ImageType::getFormatedName('home');
            foreach($products as &$product)
            {
                $product['link'] = $this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$product['id_product']));
                $product['price'] = Tools::displayPrice($product['price']);
                if(!$product['id_image'])
                    $product['id_image'] = Ets_mp_product::getImageByIDProduct($product['id_product']);
                if($product['id_image'])
                {
                    $product['image'] = Module::getInstanceByName('ets_marketplace')->displayText(Module::getInstanceByName('ets_marketplace')->displayText('','img','width_80','','','',$this->context->link->getImageLink($product['link_rewrite'],$product['id_image'],$type_image)),'a','','',$product['link']);
                }
                else
                    $product['image']='';
                $product['name'] = Module::getInstanceByName('ets_marketplace')->displayText($product['name'],'a','','',$product['link']);

            }
        }
        $this->context->smarty->assign(
            array(
                'products' => $products,
                'module' => $this->module,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/dashboard/best_selling_products.tpl');
    }
    public function _getTopSellers()
    {
        $this->context->smarty->assign(
            array(
                'sellers'=> Ets_mp_seller::getTopSellers(),
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_. $this->module->name.'/views/templates/hook/dashboard/top_sellers.tpl');
    }
    public function _getTopSellerCommissions()
    {
        $this->context->smarty->assign(
            array(
                'sellers' => Ets_mp_commission::getTopSellerCommissions(),
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/dashboard/top_seller_commissions.tpl');
    }
    
}