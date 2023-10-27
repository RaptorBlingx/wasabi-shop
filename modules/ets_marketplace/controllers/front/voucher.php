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
 * Class Ets_MarketPlaceVoucherModuleFrontController
 * @property \Ets_mp_seller $seller;
 * @property \Ets_marketplace $module;
 */
class Ets_MarketPlaceVoucherModuleFrontController extends ModuleFrontController
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
        if(!$this->context->customer->isLogged() || !($this->seller = $this->module->_getSeller(true)) || !Configuration::get('ETS_MP_ALLOW_CONVERT_TO_VOUCHER'))
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->module->_checkPermissionPage($this->seller))
            die($this->module->l('You do not have permission','voucher'));
        if(Tools::isSubmit('ets_mp-submit-voucher'))
        {
            $total_commission_can_user = (float)$this->seller->getTotalCommission(1)- $this->seller->getToTalUseCommission(1);
            $error_amount = '';
            $amount = Tools::getValue('ets_mp_VOUCHER_AMOUNT');
            $currency_default = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
            if(!$amount)
                $error_amount = $this->module->l('The amount field is required.', 'voucher');
            elseif(!Validate::isPrice($amount))
                $error_amount = $this->module->l('The amount field must be a number.','voucher');
            elseif(Tools::ps_round($amount,6) > Tools::ps_round($total_commission_can_user,6))
                $error_amount = $this->module->l('Your balance is not enough for convert.','voucher');
            elseif(($max_convert = Configuration::get('ETS_MP_MAX_BALANCE_REQUIRED_FOR_VOUCHER')) && $amount > $max_convert)
                $error_amount = $this->module->l('Amount to convert into voucher must be less than','voucher').' '.Tools::displayPrice($max_convert,$currency_default);
            elseif(($min_convert = Configuration::get('ETS_MP_MIN_BALANCE_REQUIRED_FOR_VOUCHER')) && $amount < $min_convert)
                $error_amount = $this->module->l('Amount to convert into voucher must be greater than','voucher').' '.Tools::displayPrice($min_convert,$currency_default);
            if($error_amount)
            {
                $this->context->smarty->assign(
                    array(
                        'error_amount' => $error_amount,
                        'ets_mp_VOUCHER_AMOUNT' =>$amount
                    )
                );
            }
            else
            {
                $cart_rule = new CartRule();
                $cart_rule->id_customer = $this->seller->id_customer;
                $cart_rule->date_from = date('Y-m-d H:i:s');
                $cart_rule->date_to = date('Y-m-d H:i:s', strtotime("+30 days"));
                $cart_rule->quantity = 1;
                $cart_rule->highlight=1;
                $languages = Language::getLanguages(false);
                if ($languages)
                {
                    $rule_name = array();
                    foreach ($languages as $lang){
                        $rule_name[(int)$lang['id_lang']] = Configuration::get('ETS_MP_DEFAULT_VOUCHER_NAME',$lang['id_lang']) ? : $this->module->l('Converted from commission balance', 'voucher');
                    }
                    $cart_rule->name = $rule_name;
                }
                $code = $this->module->generatePromoCode(null);
                $cart_rule->code = $code;
                $cart_rule->reduction_amount = (float)$amount;
                $cart_rule->reduction_tax = 1;
                if($cart_rule->add())
                {

                    $commission_usage = new Ets_mp_commission_usage();
                    $commission_usage->amount = $cart_rule->reduction_amount;
                    $commission_usage->id_customer= $this->seller->id_customer;
                    $commission_usage->status=1;
                    $commission_usage->id_shop = (int)$this->context->shop->id;
                    $commission_usage->id_voucher =$cart_rule->id;
                    $commission_usage->id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
                    $commission_usage->note = 'Converted into vourcher #'.$cart_rule->code;
                    $commission_usage->date_add = date('Y-m-d H:i:s');
                    $commission_usage->deleted=0;
                    $commission_usage->add();
                    $this->context->smarty->assign(
                        array(
                            'cart_rule'=>$cart_rule,
                        )
                    );
                }
            }
        }
        if (Tools::isSubmit('addVoucherTocart')) {
            $response = array();
            $id_cart_rule = (int)Tools::getValue('id_voucher');
            $cart_rule = new CartRule((int) $id_cart_rule);
            $cart = $this->context->cart;
            if (! count($cart->getProducts())) {
                $response['success'] = false;
                $response['message'] = $this->module->l('Your cart is empty.', 'voucher');
                die(json_encode($response));
            }
            if (! Validate::isUnsignedId($id_cart_rule) || !$cart_rule->id) {
                $response['success'] = false;
                $response['message'] = $this->module->l('Could not find your voucher', 'voucher');
                die(json_encode($response));
            }
            if ((int)$cart_rule->quantity <= 0 || $cart_rule->active = 0) {
                $response['success'] = false;
                $response['message'] = $this->module->l('Your voucher has been used.', 'voucher');
                die(json_encode($response));
            }
            if (!(($commission_usage = Ets_mp_commission_usage::getCommissionUsageBYIDCartRule($cart_rule->id)) && $commission_usage->id_customer == $this->seller->id_customer)) {
                $response['success'] = false;
                $response['message'] = $this->module->l('Your voucher code is not available for your account.', 'voucher');
                die(json_encode($response));
            }
            if (! $cart_rule->checkValidity($this->context)) {
                die(json_encode(array(
                    'success' => false,
                    'message' => $this->module->l('The voucher is already used or not available.', 'voucher')
                )));
            }
            if ($cart->addCartRule((int)$cart_rule->id)) {
                die(json_encode(array(
                    'success' => true,
                    'message' => $this->module->l('The voucher code applied to cart successfully.', 'voucher')
                )));
            }
            die(json_encode(array(
                'success' => false,
                'message' => $this->module->l('There was problem while trying to apply voucher code', 'voucher')
            )));
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
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/voucher.tpl');      
        else        
            $this->setTemplate('voucher_16.tpl'); 
    }
    public function _initContent()
    {
        $currency_default = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $total_commission_can_user = (float)$this->seller->getTotalCommission(1)- $this->seller->getToTalUseCommission(1);
        $total_cart_rules = Ets_mp_commission_usage::getTotalCartRulesCommissionUsage($this->seller->id_customer);
        $page = (int)Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $paggination = new Ets_mp_paggination_class();            
        $paggination->total = $total_cart_rules;
        $paggination->url = $this->context->link->getModuleLink($this->module->name,'voucher',array('page'=>'_page_'));
        $paggination->limit =  (int)Tools::getValue('paginator_voucher_select_limit',20);
        $paggination->num_links =5;
        $totalPages = ceil($total_cart_rules / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        if($cart_rules = Ets_mp_commission_usage::getCartRulesCommissionUsage($this->seller->id_customer,$start,$paggination->limit))
        {
            foreach($cart_rules as &$cart_rule)
            {
                $cart_rule['voucher_date'] = Tools::displayDate($cart_rule['date_to'], null, false); 
                $cart_rule['voucher_minimal'] = ($cart_rule['minimum_amount'] > 0) ? Tools::displayPrice($cart_rule['minimum_amount'], $currency_default) : $this->module->l('None', 'voucher');
                $now = new DateTime();
                $from = new DateTime($cart_rule['date_from']);
                $to = new DateTime($cart_rule['date_to']);
                if ($cart_rule['quantity'] <= 0 || $cart_rule['quantity_per_user'] <= 0) {
                    $cart_rule['status'] = 1; 
                } elseif ($now < $from || $now > $to) {
                    $cart_rule['status'] = -1;
                } else {
                    $cart_rule['status'] = 0;
                }
            }
        }
        $this->context->smarty->assign(
            array(
                'currency_default'=>$currency_default,
                'total_commission_can_user' => $total_commission_can_user,
                'cart_rules' => $cart_rules,
                'paggination' => $paggination->render(),
                'MAX_VOUCHER'=> (float)Configuration::get('ETS_MP_MAX_BALANCE_REQUIRED_FOR_VOUCHER') ? Tools::displayPrice((float)Configuration::get('ETS_MP_MAX_BALANCE_REQUIRED_FOR_VOUCHER'),$currency_default):false,
                'MIN_VOUCHER' => (float)Configuration::get('ETS_MP_MIN_BALANCE_REQUIRED_FOR_VOUCHER') ? Tools::displayPrice((float)Configuration::get('ETS_MP_MIN_BALANCE_REQUIRED_FOR_VOUCHER'),$currency_default):false,
            )
        );
        return $this->module->displayTpl('voucher.tpl');
    }
}