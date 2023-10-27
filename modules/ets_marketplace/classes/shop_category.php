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
class Ets_mp_shop_category extends ObjectModel
{
    protected static $instance;
    public $active;
    public $name;
    public static $definition = array(
		'table' => 'ets_mp_shop_category',
		'primary' => 'id_ets_mp_shop_category',
		'multilang' => true,
		'fields' => array(
			'active' => array('type' => self::TYPE_INT),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_mp_shop_category();
        }
        return self::$instance;
    }
    public static function getShopCategories($filter='',$start=0,$limit=12,$order_by='',$total=false)
    {
        if($total)
            $sql = 'SELECT COUNT(c.id_ets_mp_shop_category)';
        else
            $sql ='SELECT c.*,cl.name';
        $sql .= ' FROM `'._DB_PREFIX_.'ets_mp_shop_category` c
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_shop_category_lang` cl ON (c.id_ets_mp_shop_category = cl.id_ets_mp_shop_category AND cl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE 1 '.($filter ? $filter:'');
        if(!$total)
        {
            $sql .=($order_by ? ' ORDER By '.pSQL($order_by) :'');
            if($limit)
                $sql .= ' LIMIT '.(int)$start.','.(int)$limit;
        }
        if($total)
            return Db::getInstance()->getValue($sql);
        else
        {
            return Db::getInstance()->executeS($sql);
        }
    }
}