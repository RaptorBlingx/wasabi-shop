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
class Ets_mp_seller_group extends ObjectModel
{
    public $id_shop;
	public $name;
    public $description;
    public $use_fee_global=1;
    public $use_commission_global=1;
    public $enable_commission_by_category;
	public $fee_type;
    public $badge_image;
    public $level_name;
    public $fee_amount;
    public $fee_tax;
    public $commission_rate;
    public $auto_upgrade;
    public $date_add;
    public $date_upd;
    public $number_product_upload;
    public $list_categories;
    public $applicable_product_categories;
    public static $definition = array(
		'table' => 'ets_mp_seller_group',
		'primary' => 'id_ets_mp_seller_group',
		'multilang' => true,
		'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'use_fee_global' => array('type' => self::TYPE_INT),
            'use_commission_global'  => array('type' => self::TYPE_INT), 
            'enable_commission_by_category'  => array('type' => self::TYPE_INT), 
            'number_product_upload' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'fee_type'  => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'), 
            'badge_image'  => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'), 
            'fee_amount'  => array('type' => self::TYPE_FLOAT), 
            'fee_tax'  => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),  
            'commission_rate' => array('type'=> self::TYPE_FLOAT), 
            'auto_upgrade' => array('type'=> self::TYPE_FLOAT),
            'date_add' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_upd' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),  
            'list_categories' => array('type' => self::TYPE_STRING),
            'applicable_product_categories' => array('type'=>self::TYPE_STRING),
            'name' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml','lang'=>true), 
            'level_name' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml','lang'=>true),          
            'description' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml','lang'=>true),  
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
    }
    static public function _getSellerGroups($filter='',$sort='',$start=0,$limit=0,$total=false)
    {
        $module = Module::getInstanceByName('ets_marketplace');
        if($module->checkCreatedColumn('ets_mp_seller','id_group'))
        {
            if($total)
            {
                $sql = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.'ets_mp_seller_group` g
                LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_group_lang` gl ON (g.id_ets_mp_seller_group = gl.id_ets_mp_seller_group AND gl.id_lang="'.(int)Context::getContext()->language->id.'")
                WHERE g.id_shop="'.(int)Context::getContext()->shop->id.'" '.(string)$filter;
                return Db::getInstance()->getValue($sql);
            }   
            else
            {
                $fee_type = Configuration::get('ETS_MP_SELLER_FEE_TYPE');
                $fee_amount = Configuration::get('ETS_MP_SELLER_FEE_AMOUNT');
                $commission_rate = Configuration::get('ETS_MP_COMMISSION_RATE');
                $sql = 'SELECT g.id_ets_mp_seller_group as id_group,gl.name,gl.description,if(use_fee_global,"'.pSQL($fee_type).'",fee_type) as fee_type,if(use_fee_global,"'.($fee_type=='no_fee'? 0:(float)$fee_amount).'",IF(fee_type!="no_fee",fee_amount,"0")) as fee_amount,IF(use_commission_global,"'.(float)$commission_rate.'",commission_rate) as commission_rate,g.auto_upgrade FROM `'._DB_PREFIX_.'ets_mp_seller_group` g
                LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_group_lang` gl ON (g.id_ets_mp_seller_group = gl.id_ets_mp_seller_group AND gl.id_lang="'.(int)Context::getContext()->language->id.'")
                WHERE g.id_shop="'.(int)Context::getContext()->shop->id.'" '.(string)$filter
                .($sort ? ' ORDER BY '.pSQL($sort): ' ORDER BY g.id_ets_mp_seller_group DESC')
                .($limit ? ' LIMIT '.(int)$start.','.(int)$limit:'');
                return Db::getInstance()->executeS($sql);
            }
        }
        else
            return $total ? 0: array();
    }
    public function delete()
    {
        if(parent::delete())
        {
            if(Configuration::get('ETS_MP_SELLER_GROUP_DEFAULT')== $this->id)
                Configuration::updateValue('ETS_MP_SELLER_GROUP_DEFAULT',0);
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_mp_seller` SET id_group=0 WHERE id_group='.(int)$this->id);
            if($this->badge_image && file_exists(_PS_IMG_DIR_.'mp_group/'.$this->badge_image))
                @unlink(_PS_IMG_DIR_.'mp_group/'.$this->badge_image);
            return true;
        }
        return false;
    }
    public static function getGroups()
    {
        return Db::getInstance()->executeS('SELECT id_ets_mp_seller_group as id_group,id_ets_mp_seller_group,applicable_product_categories,list_categories FROM '._DB_PREFIX_.'ets_mp_seller_group');
    }
    public static function getGroupByAutoUpgrade($auto_upgrade)
    {
        return Db::getInstance()->getValue('SELECT id_ets_mp_seller_group FROM `'._DB_PREFIX_.'ets_mp_seller_group` WHERE auto_upgrade="'.(float)$auto_upgrade.'"');
    }
    public function updateRateByCategory($id_category,$rate)
    {
        if($rate)
        {
            if(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_category_commission_group` WHERE id_category='.(int)$id_category.' AND id_group='.(int)$this->id))
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_mp_category_commission_group` SET commission_rate="'.(float)$rate.'" WHERE id_category='.(int)$id_category.' AND id_group='.(int)$this->id);
            else
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_category_commission_group`(id_category,id_group,commission_rate) VALUES('.(int)$id_category.','.(int)$this->id.','.(float)$rate.')');
        }
        else
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_mp_category_commission_group` WHERE id_category='.(int)$id_category.' AND id_group='.(int)$this->id);
    }
}