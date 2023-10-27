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

class Ets_mp_paymentmethodfield extends ObjectModel
{
    /**
     * @var int
     */
    public $id_ets_mp_payment_method;
    /**
     * @var int
     */
    public $sort;
    /**
     * @var string
     */
    public $type;
   
    public $required;
    public $enable;
    public $deleted;
    public $title;
    public $description;
    public static $definition = array(
        'table' => 'ets_mp_payment_method_field',
        'primary' => 'id_ets_mp_payment_method_field',
        'multilang' => true,
        'fields' => array(
            'id_ets_mp_payment_method' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'required' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'enable' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'sort' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'deleted' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString'
            ),
            'description' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString'
            ),
            
        )
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
	public function getValueWithDrawByIDCustomerSeller($id_customer_seller)
    {
        $sql ='SELECT wf.value FROM `'._DB_PREFIX_.'ets_mp_withdrawal_field` wf
                        INNER JOIN `'._DB_PREFIX_.'ets_mp_commission_usage` cu ON (wf.id_ets_mp_withdrawal = cu.id_withdraw)
                        WHERE wf.id_ets_mp_payment_method_field='.(int)$this->id.' AND cu.id_customer="'.(int)$id_customer_seller.'"  ORDER BY wf.id_ets_mp_withdrawal DESC';
        return Db::getInstance()->getValue($sql);
    }
}
