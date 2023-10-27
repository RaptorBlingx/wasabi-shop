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
class Ets_mp_report extends ObjectModel
{
    protected static $instance;
    public $id_customer;
    public $id_seller;
    public $id_product;
    public $title;
    public $content;
    public $date_add;
    public $date_upd;
    public static $definition = array(
		'table' => 'ets_mp_seller_report',
		'primary' => 'id_ets_mp_seller_report',
		'multilang' => false,
		'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT),
            'id_seller'  => array('type' => self::TYPE_INT),
            'id_product' => array('type'=> self::TYPE_INT),
            'title' => array('type'=> self::TYPE_STRING),
            'content' => array('type'=> self::TYPE_STRING),
            'date_add' => array('type'=>self::TYPE_DATE),
            'date_upd' => array('type'=>self::TYPE_DATE),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_mp_report();
        }
        return self::$instance;
    }
    public static function getReport($id_seller,$id_customer,$id_product)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_seller_report` where id_seller="'.(int)$id_seller.'" AND id_customer="'.(int)$id_customer.'" AND id_product="'.(int)$id_product.'"');
    }

    static public function _getReports($filter='',$sort='',$start=0,$limit=10,$total=false)
    {
        $context = Context::getContext();
        if($total)
        {
            $sql = 'SELECT COUNT(distinct id_ets_mp_seller_report) FROM `'._DB_PREFIX_.'ets_mp_seller_report` r
            INNER JOIN `'._DB_PREFIX_.'ets_mp_seller` s ON (s.id_seller = r.id_seller)
            LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_lang` sl ON (s.id_seller=sl.id_seller AND sl.id_lang="'.(int)$context->language->id.'")
            LEFT JOIN `'._DB_PREFIX_.'customer` reporter ON (reporter.id_customer=r.id_customer)
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product= r.id_product AND pl.id_lang="'.(int)$context->language->id.'")
            WHERE s.id_shop = "'.(int)$context->shop->id.'"'.(string)$filter;
            return Db::getInstance()->getValue($sql);
        }
        else
        {
            $sql = 'SELECT r.*,pl.name as product_name, CONCAT(reporter.firstname," ",reporter.lastname) as reporter_name,reporter.email,sl.shop_name FROM `'._DB_PREFIX_.'ets_mp_seller_report` r
            INNER JOIN `'._DB_PREFIX_.'ets_mp_seller` s ON (s.id_seller = r.id_seller)
            LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_lang` sl ON (s.id_seller=sl.id_seller AND sl.id_lang="'.(int)$context->language->id.'")
            LEFT JOIN `'._DB_PREFIX_.'customer` reporter ON (reporter.id_customer=r.id_customer)
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product= r.id_product AND pl.id_lang="'.(int)$context->language->id.'")
            WHERE s.id_shop = "'.(int)$context->shop->id.'"'.(string)$filter
            .' GROUP BY r.id_ets_mp_seller_report'
            .($sort ? ' ORDER BY '.pSQL($sort): ' ORDER BY r.id_ets_mp_seller_report DESC')
            .' LIMIT '.(int)$start.','.(int)$limit;
            return Db::getInstance()->executeS($sql);
        }
        
    }
 }