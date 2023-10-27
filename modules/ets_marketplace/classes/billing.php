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
class Ets_mp_billing extends ObjectModel
{
    protected static $instance;
    public $id_customer;
    public $reference;
    public $seller_confirm;
    public $amount;
    public $amount_tax;
    public $fee_type;
    public $id_shop;
    public $active;
    public $used;
    public $date_from;
    public $date_to;
    public $date_add;
    public $date_upd;
    public $id_employee;
    public $note;    
    public static $definition = array(
		'table' => 'ets_mp_seller_billing',
		'primary' => 'id_ets_mp_seller_billing',
		'multilang' => false,
		'fields' => array(
			'id_customer' => array('type' => self::TYPE_INT),
            'id_shop' => array('type' => self::TYPE_INT),
            'seller_confirm' => array('type'=>self::TYPE_INT),
            'amount' => array('type'=> self::TYPE_FLOAT),
            'amount_tax' => array('type'=> self::TYPE_FLOAT),
            'fee_type' => array('type'=>self::TYPE_STRING),
            'reference' => array('type'=>self::TYPE_STRING),
            'active' => array('type'=> self::TYPE_INT),
            'used' =>array('type'=> self::TYPE_INT),
            'note' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'id_employee' => array('type'=>self::TYPE_INT),
            'date_from' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_to' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_add' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_upd' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_mp_billing();
        }
        return self::$instance;
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_marketplace', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public static function getSellerBillings($filter='',$having="",$start=0,$limit=12,$order_by='',$total=false)
    {
        if($total)
            $sql = 'SELECT COUNT(DISTINCT b.id_ets_mp_seller_billing)';
        else
            $sql ='SELECT b.*,if(b.id_ets_mp_seller_billing = seller.id_billing AND b.active=0,seller.date_to,"") as date_due, CONCAT(customer.firstname," ",customer.lastname) as seller_name,customer.id_customer as id_customer_seller,seller.id_customer,seller.id_seller, seller_lang.shop_name,b.active as status';
        $sql .= ' FROM `'._DB_PREFIX_.'ets_mp_seller_billing` b
        LEFT JOIN `'._DB_PREFIX_.'customer` customer ON (customer.id_customer=b.id_customer)
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller` seller ON (customer.id_customer= seller.id_customer)
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_lang` seller_lang ON (seller.id_seller= seller_lang.id_seller AND seller_lang.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE b.id_shop="'.(int)Context::getContext()->shop->id.'"'.($filter ? (string)$filter:'');
        if(!$total)
        {
            $sql .=' GROUP BY b.id_ets_mp_seller_billing ';
            if($having)
                $sql .= ' HAVING 1 '.(string)$having;
            $sql .= ($order_by ? ' ORDER By '.pSQL($order_by) :'');
            $sql .= ' LIMIT '.(int)$start.','.(int)$limit;
        }
        if($total)
            return Db::getInstance()->getValue($sql);
        else
        {
            return Db::getInstance()->executeS($sql);
        }
    }
    public function add($auto_date=true,$null_values=false)
    {
        do {
            $reference = Order::generateReference();
        } while (Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_seller_billing` WHERE reference="'.pSQL($reference).'"'));
        $this->reference= $reference;
        $this->id_shop = Context::getContext()->shop->id;
        $res = parent::add($auto_date,$null_values);
        if($res && Configuration::get('ETS_MP_EMAIL_SELLER_BILLING_CREATED'))
        {
            $seller = Ets_mp_seller::_getSellerByIdCustomer($this->id_customer,Context::getContext()->language->id);
            $payment_information = Configuration::get('ETS_MP_SELLER_PAYMENT_INFORMATION',Context::getContext()->language->id);
            $str_search = array('[shop_id]','[shop_name]','[seller_name]','[seller_email]');
            $str_replace = array($seller->id,$seller->shop_name,$seller->seller_email,$seller->seller_email);
            $data = array(
                '{seller_name}' => $seller->seller_name,
                '{payment_information}' => str_replace($str_search,$str_replace,$payment_information),
            );
            $pdf = new PDF($this,'BillingPdf', Context::getContext()->smarty);
            $file_attachment = array();
            $file_attachment['content'] =$pdf->render(false);
            $file_attachment['name'] = $this->getBillingNumberInvoice(). '.pdf';
            $file_attachment['mime'] = 'application/pdf';
            $subjects = array(
                'translation' => $this->l('New billing created'),
                'origin'=> 'New billing created',
                'specific'=>'billing',
            );
            Ets_marketplace::sendMail('to_seller_billing_created',$data,$seller->seller_email,$subjects,$seller->seller_name,$file_attachment);
        }
        return $res;
    }
    public function getBillingNumberInvoice(){
        return $this->reference;
    }
    public static function getMinDateUpdBilling()
    {
        $sql  = 'SELECT MIN(s.date_upd) FROM `'._DB_PREFIX_.'ets_mp_seller_billing` b
                    INNER JOIN `'._DB_PREFIX_.'ets_mp_seller` s ON (b.id_customer=s.id_customer)
                    WHERE s.id_shop="'.(int)Context::getContext()->shop->id.'" AND b.active=1';
        return Db::getInstance()->getValue($sql);
    }
    public static function getMaxDateUpdBilling()
    {
        $sql  = 'SELECT MAX(s.date_upd) FROM `'._DB_PREFIX_.'ets_mp_seller_billing` b
                    INNER JOIN `'._DB_PREFIX_.'ets_mp_seller` s ON (b.id_customer=s.id_customer)
                    WHERE s.id_shop="'.(int)Context::getContext()->shop->id.'" AND b.active=1';
        return Db::getInstance()->getValue($sql);
    }
    public static function getMinDateUpdOrder($id_customer_seller=0)
    {
        if($status = Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'))
        {
            $status = explode(',',$status);
            $sql = 'SELECT MIN(o.date_upd) FROM `'._DB_PREFIX_.'orders` o
                            INNER JOIN `'._DB_PREFIX_.'currency` c ON (o.id_currency=c.id_currency)
                            INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_order` seller_order ON (seller_order.id_order=o.id_order '.($id_customer_seller ? ' AND seller_order.id_customer="'.(int)$id_customer_seller.'"':'').')
                            WHERE o.id_shop="'.(int)Context::getContext()->shop->id.'" AND o.current_state IN ('.implode(',',array_map('intval',$status)).')';
            return Db::getInstance()->getValue($sql);
        }
        return '';
    }
    public static function getMaxDateUpdOrder($id_customer_seller=0)
    {
        if($status = Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'))
        {
            $status = explode(',',$status);
            $sql = 'SELECT Max(o.date_upd) FROM `'._DB_PREFIX_.'orders` o
                            INNER JOIN `'._DB_PREFIX_.'currency` c ON (o.id_currency=c.id_currency)
                            INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_order` seller_order ON (seller_order.id_order=o.id_order '.($id_customer_seller ? ' AND seller_order.id_customer='.(int)$id_customer_seller:'').')
                            WHERE o.id_shop="'.(int)Context::getContext()->shop->id.'" AND o.current_state IN ('.implode(',',array_map('intval',$status)).')';
            return Db::getInstance()->getValue($sql);
        }
        return '';
    }
    public static function getTotalSellerFee($filter=false)
    {
        $sql  = 'SELECT SUM(b.amount) FROM `'._DB_PREFIX_.'ets_mp_seller_billing` b
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller` s ON (b.id_customer=s.id_customer)
        WHERE b.id_shop="'.(int)Context::getContext()->shop->id.'" AND b.active=1'.($filter ? (string)$filter:'');
        return Db::getInstance()->getInstance()->getValue($sql);
    }
}