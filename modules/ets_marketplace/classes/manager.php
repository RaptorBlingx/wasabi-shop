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
class Ets_mp_manager extends ObjectModel
{
    public $id_user;
    public $email;
    public $permission;
    public $delete_product;
    public $id_customer;
    public $active;
    public static $definition = array(
		'table' => 'ets_mp_seller_manager',
		'primary' => 'id_ets_mp_seller_manager',
		'multilang' => false,
		'fields' => array(
            'id_user' => array('type' => self::TYPE_INT),
            'email'  => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'), 
            'permission'  => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'), 
            'delete_product' => array('type'=> self::TYPE_INT),
            'id_customer' => array('type'=> self::TYPE_INT),
            'active' => array('type'=> self::TYPE_INT),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
	public static function checkExistSellerMangerByEmail($email,$id_ets_mp_seller_manager)
    {
        return Db::getInstance()->getValue('SELECT id_ets_mp_seller_manager FROM `'._DB_PREFIX_.'ets_mp_seller_manager` WHERE email="'.pSQl($email).'" AND id_ets_mp_seller_manager!="'.(int)$id_ets_mp_seller_manager.'"');
    }
    public static function checkExistSellerMangerByIDCustomer($id_customer,$id_ets_mp_seller_manager)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_seller_manager` WHERE id_customer="'.(int)$id_customer.'" AND id_ets_mp_seller_manager='.(int)$id_ets_mp_seller_manager);
    }
    public static function getManagerByEmail($email)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_seller_manager` WHERE email="'.pSQL($email).'" AND active !=0');
    }
    public static function getIDMangerByEmail($email)
    {
        return Db::getInstance()->getValue('SELECT id_ets_mp_seller_manager FROM `'._DB_PREFIX_.'ets_mp_seller_manager` WHERE email="'.pSQL($email).'"');
    }
 }