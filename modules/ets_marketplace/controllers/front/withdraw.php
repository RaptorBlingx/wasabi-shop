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
 * Class Ets_MarketPlaceWithdrawModuleFrontController
 * @property \Ets_mp_seller $seller;
 * @property \Ets_marketplace $module;
 */
class Ets_MarketPlaceWithdrawModuleFrontController extends ModuleFrontController
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
            die($this->module->l('You do not have permission','withdraw'));
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
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/withdraw.tpl');      
        else        
            $this->setTemplate('withdraw_16.tpl');
    }
    public function _initContent()
    {
        $currency_default = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $this->context->smarty->assign(
            array(
                'currency_default' => $currency_default
            )
        );
        $total_commission_can_user = (float)$this->seller->getTotalCommission(1)- $this->seller->getToTalUseCommission(1);
        if ($id_payment = (int)Tools::getValue('id_payment')) {
            $paymentMethod = new Ets_mp_paymentmethod($id_payment,$this->context->language->id);
            $payment_fields = $paymentMethod->getListFields();
            if($payment_fields)
            {
                foreach($payment_fields as &$payment_field)
                {
                    $paymentField = new Ets_mp_paymentmethodfield($payment_field['id_ets_mp_payment_method_field']);
                    $value = Validate::isLoadedObject($paymentField) ? $paymentField->getValueWithDrawByIDCustomerSeller($this->seller->id_customer):'';
                    $payment_field_value = Tools::getValue('payment_field_'.$payment_field['id_ets_mp_payment_method_field'],$value);
                    $payment_field['value'] = $payment_field_value;
                }
            }
            if(Configuration::get('ETS_MP_WITHDRAW_INVOICE_REQUIRED'))
            {
                $payment_fields[] = array(
                    'id_ets_mp_payment_method_field' => null,
                    'type'=>'file',
                    'title' => $this->module->l('Invoice','withdraw'),
                    'required' => 1,
                    'description' => '',
                );
            }
            $amount_withdraw_error = '';
            $amount_withdraw = (float)Tools::getValue('amount_withdraw');
            if(Tools::isSubmit('checkamountWithdraw') && $id_payment)
            {
                if(!(float)$amount_withdraw)
                {
                    $amount_withdraw_error = $this->module->l('Withdraw amount is required','withdraw');
                }
                elseif($amount_withdraw)
                {
                    if($paymentMethod=='NO_FEE')
                        $payment_fee = 0;
                    else
                        $payment_fee = $paymentMethod->fee_type=='FIXED' ? $paymentMethod->fee_fixed : ($paymentMethod->fee_percent*$amount_withdraw)/100;
                    if($amount_withdraw <= $payment_fee)
                        $amount_withdraw_error = $this->module->l('Amount to withdraw must be greater than','withdraw').' '.Tools::displayPrice($payment_fee,$currency_default);
                    elseif((float)Tools::ps_round($amount_withdraw,6) > (float)Tools::ps_round($total_commission_can_user,6))
                        $amount_withdraw_error = $this->module->l('Amount to withdraw is greater than total commission','withdraw');
                    elseif(Configuration::get('ETS_MP_MAX_WITHDRAW') && $amount_withdraw > Configuration::get('ETS_MP_MAX_WITHDRAW'))
                        $amount_withdraw_error = $this->module->l('Amount to withdraw must be less than','withdraw').' '.Tools::displayPrice(Configuration::get('ETS_MP_MAX_WITHDRAW'),$currency_default);
                    elseif(Configuration::get('ETS_MP_BALANCE_REQUIRED_FOR_WITHDRAW') && $amount_withdraw < Configuration::get('ETS_MP_BALANCE_REQUIRED_FOR_WITHDRAW'))
                        $amount_withdraw_error = $this->module->l('Amount to withdraw must be greater than','withdraw').' '.Tools::displayPrice(Configuration::get('ETS_MP_BALANCE_REQUIRED_FOR_WITHDRAW'),$currency_default);
                }
                if($amount_withdraw_error)
                {
                    die(
                        json_encode(
                            array(
                                'error' => $amount_withdraw_error,
                            )
                        )
                    );
                }
                else
                {
                    die(
                        json_encode(
                            array(
                                'amount_withdraw' => Tools::displayPrice($amount_withdraw-$payment_fee,$currency_default),
                            )
                        )
                    );
                }
            }
            if(Tools::isSubmit('ets_mp_withdraw_submit'))
            {
                if(!$amount_withdraw)
                {
                    $amount_withdraw_error = $this->module->l('Withdraw amount is required','withdraw');
                }
                elseif($amount_withdraw)
                {
                    if($paymentMethod=='NO_FEE')
                        $payment_fee = 0;
                    else
                        $payment_fee = $paymentMethod->fee_type=='FIXED' ? $paymentMethod->fee_fixed : ($paymentMethod->fee_percent*$amount_withdraw)/100;
                    if($amount_withdraw <= $payment_fee)
                        $amount_withdraw_error = $this->module->l('Amount to withdraw must be greater than','withdraw').' '.Tools::displayPrice($payment_fee,$currency_default);
                    elseif(Configuration::get('ETS_MP_MAX_WITHDRAW') && $amount_withdraw > Configuration::get('ETS_MP_MAX_WITHDRAW'))
                        $amount_withdraw_error = $this->module->l('Amount to withdraw must be less than','withdraw').' '.Tools::displayPrice(Configuration::get('ETS_MP_MAX_WITHDRAW'),$currency_default);
                    elseif(Configuration::get('ETS_MP_BALANCE_REQUIRED_FOR_WITHDRAW') && $amount_withdraw < Configuration::get('ETS_MP_BALANCE_REQUIRED_FOR_WITHDRAW'))
                            $amount_withdraw_error = $this->module->l('Amount to withdraw must be greater than','withdraw').' '.Tools::displayPrice(Configuration::get('ETS_MP_BALANCE_REQUIRED_FOR_WITHDRAW'),$currency_default);
                    elseif((float)Tools::ps_round($amount_withdraw,6) > (float)Tools::ps_round($total_commission_can_user,6))
                        $amount_withdraw_error = $this->module->l('Amount to withdraw is greater than total commission','withdraw');
                }
                $field_error = false;
                if($payment_fields)
                {
                    foreach($payment_fields as &$payment_field)
                    {
                        if($payment_field['type']=='file')
                        {
                            $file = isset($_FILES['payment_field_'.(int)$payment_field['id_ets_mp_payment_method_field']]) ? $_FILES['payment_field_'.(int)$payment_field['id_ets_mp_payment_method_field']]:null;
                            if($payment_field['required'] && (!isset($file['name']) || !$file['name']))
                            {
                                $payment_field['error'] = $this->module->l('This field is required.','withdraw');
                                $field_error = true;
                            }
                            elseif(isset($file['name']) && $file['name'] && isset($file['tmp_name']) && $file['tmp_name'])
                            {
                                $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
                                if ($max_file_size && $file['size'] >$max_file_size )
                				{
                				    $payment_field['error'] = $this->module->l('File is too large. Maximum size allowed: ','withdraw').(int)($max_file_size/1024).'Kb';
                                    $field_error = true;
                				}
                                else
                                {
                                    $type = Tools::strtolower(Tools::substr(strrchr($file['name'], '.'), 1));
                        			if (in_array($type, array('pdf')))
                        			{
 			                            if(!is_dir(_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_withdraw/'))
                                        {
                                            @mkdir(_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_withdraw/',0777,true);
                                            @copy(dirname(__FILE__).'/index.php', _PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_withdraw/index.php');
                                        }
 			                            $file_name = md5(time());
                                        if(file_exists(_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_withdraw/'.$file_name))
                                        {
                                            $file['name'] = time()-$file_name;
                                        }
                        				if (!move_uploaded_file($file['tmp_name'], _PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_withdraw/'.$file_name))
                       					{
                       					    $payment_field['error'] = $this->module->l('Cannot upload the file','withdraw');
                                            $field_error = true;
                       					}
                                        else
                                        {
                                            $payment_field['value'] = $file['name'];
                                            $payment_field['file_name'] = $file_name;
                                        }
                        			}
                                    else
                                    {
                                        $payment_field['error'] = $this->module->l('Only pdf file type is accepted','withdraw');
                                        $field_error = true;
                                    }
                                }
                            }
                        }
                        else
                        {
                            $payment_field_value = Tools::getValue('payment_field_'.$payment_field['id_ets_mp_payment_method_field']);
                            if($payment_field['required'] && !$payment_field_value)
                            {
                                $payment_field['error'] = $this->module->l('This field is required.','withdraw');
                                $field_error =  true;
                            }
                            elseif($payment_field_value && !Validate::isCleanHtml($payment_field_value))
                            {
                                $payment_field['error'] = $this->module->l('This field is not valid','withdraw');
                                $field_error = true;
                            }
                        }
                        
                    }
                }
                if(!$field_error && !$amount_withdraw_error)
                {
                    $withdraw = new Ets_mp_withdraw();
                    $withdraw->id_ets_mp_payment_method = $id_payment;
                    $withdraw->fee_type = $paymentMethod->fee_type;
                    $withdraw->fee = $payment_fee;
                    $withdraw->date_add = date('Y-m-d H:i:s');
                    $withdraw->processing_date = ($processed_date = $paymentMethod->estimated_processing_time) ? date('Y-m-d H:i:s',strtotime("+ $processed_date days")): $withdraw->date_add;
                    if($withdraw->add())
                    {
                        $commission_usage = new Ets_mp_commission_usage();
                        $commission_usage->amount = $amount_withdraw;
                        $commission_usage->id_customer= $this->seller->id_customer;
                        $commission_usage->status=1;
                        $commission_usage->id_withdraw = $withdraw->id;
                        $commission_usage->id_currency = $this->context->currency->id;
                        $commission_usage->note = 'Withdrawn ('.trim($paymentMethod->title).', ID withdrawal: '.$withdraw->id.')';
                        $commission_usage->date_add = date('Y-m-d H:i:s');
                        $commission_usage->deleted=0;
                        $commission_usage->id_shop = $this->context->shop->id;
                        if($commission_usage->add())
                        {
                            if($payment_fields)
                            {
                                foreach($payment_fields as $field)
                                {
                                    $wf = new Ets_mp_withdraw_field();
                                    if($field['type']=='file')
                                    {
                                        $wf->value = isset($field['value']) ? $field['value'] :'';
                                        $wf->file_name = isset($field['file_name']) ? $field['file_name']:'';
                                    }
                                    else
                                        $wf->value = Tools::getValue('payment_field_'.$field['id_ets_mp_payment_method_field']);
                                    $wf->id_ets_mp_withdrawal = $withdraw->id;
                                    $wf->id_ets_mp_payment_method_field = $field['id_ets_mp_payment_method_field'];
                                    $wf->save();
                                }
                            }
                            if(Configuration::get('ETS_MP_EMAIL_ADMIN_WITHDRAWAL_CREATED'))
                            {
                                $withdrawal = $withdraw->getWithdrawalDetail();
                                if($withdrawal)
                                {
                                    $data= array(
                                        '{withdrawal_ID}' => $withdraw->id,
                                        '{amount}' => Tools::displayPrice($withdrawal['amount'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))),
                                        '{seller_name}' => $withdrawal['seller_name'],
                                        '{payment_method}' => $withdrawal['payment_method'],
                                        '{created_date}' => Tools::displayDate($withdraw->date_add,null,true),
                                        '{processed_date}' => Tools::displayDate($withdraw->processing_date,null,true),
                                    );
                                    $subjects = array(
                                        'translation' => $this->module->l('New withdrawal has been created','withdraw'),
                                        'origin'=> 'New withdrawal has been created',
                                        'specific'=>'withdraw'
                                    );
                                    Ets_marketplace::sendMail('to_admin_withdrawal_created',$data,'',$subjects);
                                    
                                }
                                
                            }
                        }
                        $this->context->cookie->ets_mp_success_message = $this->module->l('You have successfully submitted your withdrawal request.', 'withdraw');
                        $this->context->cookie->write();
                        Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'withdraw'));
                    }
                }
            }
            if(Configuration::get('ETS_MP_WITHDRAW_ONE_ONLY'))
            {
                if($this->seller->getPendingWithdrawal())
                    $withdraw_one_only = true;
                else
                    $withdraw_one_only = false;
            }
            else
                $withdraw_one_only = false;
            $this->context->smarty->assign(
                array(
                    'total_commission' =>$total_commission_can_user,
                    'paymentMethod' => $paymentMethod,
                    'payment_fields' => $payment_fields,
                    'amount_withdraw' => $amount_withdraw,
                    'amount_withdraw_error' => $amount_withdraw_error,
                    'withdraw_one_only' => $withdraw_one_only,
                    'ETS_MP_BALANCE_REQUIRED_FOR_WITHDRAW' => (float)Configuration::get('ETS_MP_BALANCE_REQUIRED_FOR_WITHDRAW'),
                    'MIN_WITHDRAW' => (float)Configuration::get('ETS_MP_BALANCE_REQUIRED_FOR_WITHDRAW')!=0 ? Tools::displayPrice((float)Configuration::get('ETS_MP_BALANCE_REQUIRED_FOR_WITHDRAW'),$currency_default):false,
                    'MAX_WITHDRAW' => (float)Configuration::get('ETS_MP_MAX_WITHDRAW')!=0 ? Tools::displayPrice((float)Configuration::get('ETS_MP_MAX_WITHDRAW'),$currency_default):false, 
                    'fee_payment' => $paymentMethod->fee_type=='FIXED' ? Tools::displayPrice($paymentMethod->fee_fixed,$currency_default) : $paymentMethod->fee_percent.'%',
                )
            );
            return $this->module->displayTpl('payment/payment_withdraw.tpl');
        }
        else
        {
            $payments = Ets_mp_paymentmethod::getListPaymentMethods(true);
            if($payments)
            {
                foreach($payments as &$payment)
                {
                    $payment['link'] = $this->context->link->getModuleLink($this->module->name,'withdraw',array('id_payment'=>$payment['id_ets_mp_payment_method']));
                    $payment['fee_fixed'] = Tools::displayPrice($payment['fee_fixed'],$currency_default);
                }
            }
            $this->context->smarty->assign(
                array(
                    'payments' => $payments,
                    'withdraws' => $this->seller->getWithdraws(),
                    'ets_mp_success_message' => $this->context->cookie->ets_mp_success_message ? $this->context->cookie->ets_mp_success_message:'',
                    'total_commission' => Tools::displayPrice($total_commission_can_user,$currency_default),
                )
            );
            $this->context->cookie->ets_mp_success_message='';
            $this->context->cookie->write();
            return $this->module->displayTpl('payment/withdraw.tpl'); 
        }
    }
}