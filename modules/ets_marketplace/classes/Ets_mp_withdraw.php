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

if (!defined('_PS_VERSION_')) {
    exit();
}

class Ets_mp_withdraw extends ObjectModel
{
    public $id_ets_mp_payment_method;
    public $status;
    public $fee;
    public $fee_type;
    public $date_add;
    public $processing_date;
    public static $definition = array(
        'table' => 'ets_mp_withdrawal',
        'primary' => 'id_ets_mp_withdrawal',
        'fields' => array(
            'id_ets_mp_payment_method' => array(
                'type' => self::TYPE_INT,
            ),
            'status' => array(
                'type' => self::TYPE_INT,
            ),
            'fee' => array(
                'type' => self::TYPE_FLOAT,
            ),
            'fee_type' => array(
                'type' => self::TYPE_STRING,
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE
            ),
            'processing_date'=>array(
                'type' => self::TYPE_DATE
            ),
        )
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public static function _getWithdrawals($filter='',$sort='',$start=0,$limit=10,$total=false)
    {
        $context = Context::getContext();
        if($total)
        {
            $sql = 'SELECT COUNT(DISTINCT w.id_ets_mp_withdrawal) FROM ';
        }
        else
        {
            $sql = 'SELECT w.id_ets_mp_withdrawal,w.status,CONCAT(customer.firstname," ",customer.lastname) as seller_name,customer.id_customer as id_customer_seller,seller.id_seller,pml.title,cu.amount,cu.note,cu.id_customer as seller_id FROM ';
        }
        $sql .= _DB_PREFIX_.'ets_mp_withdrawal w
        INNER JOIN `'._DB_PREFIX_.'ets_mp_commission_usage` cu ON (w.id_ets_mp_withdrawal = cu.id_withdraw)
        lEFT JOIN `'._DB_PREFIX_.'customer` customer ON (customer.id_customer=cu.id_customer)
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller` seller ON (seller.id_customer=customer.id_customer)
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_payment_method_lang` pml ON (w.id_ets_mp_payment_method=pml.id_ets_mp_payment_method AND pml.id_lang="'.(int)$context->language->id.'")
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_lang` seller_lang ON (seller.id_seller=seller_lang.id_seller AND seller_lang.id_lang="'.(int)$context->language->id.'")
        WHERE cu.id_shop="'.(int)$context->shop->id.'"'.(string)$filter;
        if(!$total)
        {
            $sql .=' GROUP BY w.id_ets_mp_withdrawal '.($sort ? ' ORDER By '.pSQL($sort) :'');
            $sql .= ' LIMIT '.(int)$start.','.(int)$limit;
            return Db::getInstance()->executeS($sql);
        }
        else
            return Db::getInstance()->getValue($sql);
    }

    public function add($auto_date=true,$null_values=false)
    {
        $res = parent::add($auto_date,$null_values);
        
        return $res;
    }
    public function delete()
    {
        if(parent::delete())
        {
            $fields = Db::getInstance()->executeS('SELECT id_ets_mp_withdrawal_field FROM `'._DB_PREFIX_.'ets_mp_withdrawal_field` where id_ets_mp_withdrawal='.(int)$this->id);
            if($fields)
            {
                foreach($fields as $filed)
                {
                    $Withdrawfield = new Ets_mp_withdraw_field(($filed['id_ets_mp_withdrawal_field']));
                    $Withdrawfield->delete();
                }
            }
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_mp_withdrawal_field` WHERE id_ets_mp_withdrawal='.(int)$this->id);
            return true;
        }
    }
    public function update($null_values=false)
    {
        $status_old = Db::getInstance()->getValue('SELECT status FROM `'._DB_PREFIX_.'ets_mp_withdrawal` WHERE id_ets_mp_withdrawal='.(int)$this->id);
        if($this->status==1 && $status_old!=1)
        {
            $this->processing_date = date('Y-m-d H:i:s');
        }
        $res = parent::update($null_values);
        if($status_old != $this->status && $res && Configuration::get('ETS_MP_EMAIL_SELLER_WITHDRAWAL_APPROVED'))
        {
            $withdrawal = $this->getWithdrawalDetail();
            if($withdrawal)
            {
                $data = array(
                    '{seller_name}' => $withdrawal['seller_name']?:$withdrawal['name'],
                    '{withdrawal_ID}' => $this->id,
                    '{amount}' => Tools::displayPrice($withdrawal['amount'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))),
                    '{payment_method}'=>$withdrawal['payment_method'],
                    '{approved_date}' => date('Y-m-d H:i:s'),
                    '{declined_date}' => date('Y-m-d H:i:s'),
                    '{reason}' => ''
                );
                $email = $withdrawal['seller_email'] ?:$withdrawal['email'];
                if($this->status==1)
                {
                    $subjects = array(
                        'translation' => $this->l('Your withdrawal has been approved'),
                        'origin'=> 'Your withdrawal has been approved',
                        'specific'=>'ets_mp_withdraw'
                    );
                    Ets_marketplace::sendMail('to_seller_withdrawal_approved',$data,$email,$subjects,$withdrawal['seller_name']?:$withdrawal['name']);
                }
                else
                {
                    $subjects = array(
                        'translation' => $this->l('Your withdrawal has been declined'),
                        'origin'=> 'Your withdrawal has been declined',
                        'specific'=>'ets_mp_withdraw',
                    );
                    Ets_marketplace::sendMail('to_seller_withdrawal_declined',$data,$email,$subjects,$withdrawal['seller_name']?:$withdrawal['name']);
                }    
            }
        }
        return $res;
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_marketplace', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public function getWithdrawalDetail()
    {
        $sql = 'SELECT w.id_ets_mp_withdrawal, cu.id_withdraw,cu.amount,s.id_seller,CONCAT(c.firstname," ",c.lastname) as seller_name,s.id_customer,c.email as seller_email,CONCAT(c.firstname," ",c.lastname) as name, w.date_add,w.fee,w.fee_type,w.status,pml.title as payment_method,pm.estimated_processing_time,cu.id_ets_mp_commission_usage,cu.note, cu.amount,(cu.amount-w.fee) as pay_amount ,pml.title as payment_name,w.fee_type,w.status,w.date_add
        FROM `'._DB_PREFIX_.'ets_mp_withdrawal` w
        INNER JOIN `'._DB_PREFIX_.'ets_mp_commission_usage` cu ON (cu.id_withdraw= w.id_ets_mp_withdrawal)
        INNER JOIN `'._DB_PREFIX_.'ets_mp_seller` s ON (cu.id_customer=s.id_customer)
        INNER JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer=s.id_customer)
        INNER JOIN `'._DB_PREFIX_.'ets_mp_payment_method` pm ON (pm.id_ets_mp_payment_method=w.id_ets_mp_payment_method)
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_payment_method_lang` pml ON (pml.id_ets_mp_payment_method=pm.id_ets_mp_payment_method AND pml.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE cu.id_shop = "'.(int)Context::getContext()->shop->id.'" AND w.id_ets_mp_withdrawal = "'.(int)$this->id.'"';
        return Db::getInstance()->getRow($sql);
    }
    public function getListFields()
    {
        return Db::getInstance()->executeS('SELECT wf.value,wf.id_ets_mp_withdrawal,pmfl.title,wf.id_ets_mp_payment_method_field,wf.id_ets_mp_withdrawal_field FROM `'._DB_PREFIX_.'ets_mp_withdrawal_field` wf
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_payment_method_field_lang` pmfl ON (wf.id_ets_mp_payment_method_field=pmfl.id_ets_mp_payment_method_field AND pmfl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE wf.id_ets_mp_withdrawal='.(int)$this->id);
    }
 }