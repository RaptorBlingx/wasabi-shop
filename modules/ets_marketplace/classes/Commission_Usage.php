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

class Ets_mp_commission_usage extends ObjectModel
{
    /**
     * @var float
     */
    public $amount;
    public $reference;
    /**
     * @var int
     */
    public $id_shop;
    /**
     * @var int
     */
    public $id_voucher;
    /**
     * @var int
     */
    public $id_order;
    /**
     * @var int
     */
    public $id_withdraw;
    /**
     * @var int
     */
    public $id_currency;
    /**
     * @var int
     */
    public $id_customer;
    public $status;
    /**
     * @var string
     */
    public $note;
    /**
     * @var int
     */
    public $deleted;
    public $date_add;
    public static $definition = array(
        'table' => 'ets_mp_commission_usage',
        'primary' => 'id_ets_mp_commission_usage',
        'fields' => array(
            'reference' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'amount' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat'
            ),
            'id_customer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_voucher' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isUnsignedInt'
            ),
            'id_withdraw' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isUnsignedInt'
            ),
            'id_order' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isUnsignedInt'
            ),
            'id_currency' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'status' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'note' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDateFormat',
                'allow_null' => true
            ),
            'deleted' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            )
        )
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public function add($auto_date=true,$null_value=true)
    {
        do {
            $reference = Ets_mp_commission_usage::generateReference();
        } while (Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_commission_usage` WHERE reference="'.pSQL($reference).'"'));
        $this->reference = $reference;
        if(parent::add($auto_date,$null_value))
        {
            if($this->id_voucher)
            {
                if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'cart_rule_shop` WHERE id_cart_rule="'.(int)$this->id_voucher.'" AND id_shop='.(int)Context::getContext()->shop->id))
                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_shop`(id_cart_rule,id_shop) VALUES("'.(int)$this->id_voucher.'","'.(int)Context::getContext()->shop->id.'")');
            }
            return true;
        }
        return false;
    }
    public function delete()
    {
        if(parent::delete())
        {
            $withdrawals = Db::getInstance()->executeS('SELECT w.id_ets_mp_withdrawal FROM `'._DB_PREFIX_.'ets_mp_withdrawal` w
            INNER JOIN `'._DB_PREFIX_.'ets_mp_commission_usage` cu ON (cu.id_withdraw = w.id_ets_mp_withdrawal)
            WHERE cu.id_ets_mp_commission_usage='.(int)$this->id);
            if($withdrawals)
            {
                foreach($withdrawals as $withdrawal)
                {
                    $withdrawal_class = new Ets_mp_withdraw($withdrawal['id_ets_mp_withdrawal']);
                    $withdrawal_class->delete();
                }
            }
            $cartRules = Db::getInstance()->executeS('SELECT cr.id_cart_rule FROM `'._DB_PREFIX_.'cart_rule` cr
            INNER JOIN `'._DB_PREFIX_.'ets_mp_commission_usage` cu ON (cu.id_voucher = cr.id_cart_rule)
            WHERE cu.id_ets_mp_commission_usage ='.(int)$this->id);
            if($cartRules)
            {
                foreach($cartRules as $cartRule)
                {
                    $cartRule_class = new CartRule($cartRule['id_cart_rule']);
                    $cartRule_class->delete();
                }
            }
            return true;
        }
    }
    public function update($null_value=true)
    {
        return parent::update($null_value);
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_marketplace', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public static function generateReference()
    {
        return Tools::strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
    }
    public static function getCommissionUsageBYIDCartRule($id_cart_rule)
    {
        if($id_ets_mp_commission_usage = Db::getInstance()->getValue('SELECT id_ets_mp_commission_usage FROM `'._DB_PREFIX_.'ets_mp_commission_usage` WHERE id_voucher='.(int)$id_cart_rule))
        {
            return new Ets_mp_commission_usage($id_ets_mp_commission_usage);
        }
        return false;
    }
    public static function getTotalCartRulesCommissionUsage($id_seller_customer)
    {
        return Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'cart_rule` cr
        INNER JOIN `'._DB_PREFIX_.'ets_mp_commission_usage` cu ON (cu.id_voucher= cr.id_cart_rule AND cu.id_customer="'.(int)$id_seller_customer.'")
        LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (crl.id_cart_rule=cr.id_cart_rule AND crl.id_lang="'.(int)Context::getContext()->language->id.'")');
    }
    public static function getCartRulesCommissionUsage($id_seller_customer,$start,$limit)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'cart_rule` cr
        INNER JOIN `'._DB_PREFIX_.'ets_mp_commission_usage` cu ON (cu.id_voucher= cr.id_cart_rule AND cu.id_customer="'.(int)$id_seller_customer.'")
        LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (crl.id_cart_rule=cr.id_cart_rule AND crl.id_lang="'.(int)Context::getContext()->language->id.'") ORDER BY cr.id_cart_rule DESC LIMIT '.(int)$start.','.(int)$limit);
    }
    public static function getCommissionUsageByIDWithDraw($id_withdraw)
    {
        $id_ets_mp_commission_usage = (int)Db::getInstance()->getValue('SELECT id_ets_mp_commission_usage FROM `'._DB_PREFIX_.'ets_mp_commission_usage` WHERE id_withdraw='.(int)$id_withdraw);
        return new Ets_mp_commission_usage($id_ets_mp_commission_usage);
    }
}