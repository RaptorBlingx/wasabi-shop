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
 * @param Ets_marketplace $module
 * @return bool
 * @throws PrestaShopDatabaseException
 * @throws PrestaShopException
 */
function upgrade_module_3_2_0($module)
{
    if(!Tab::getIdFromClassName('AdminMarketPlaceCategory'))
    {
        $tab = new Tab();
        $tab->class_name = 'AdminMarketPlaceCategory';
        $tab->module = $module->name;
        $tab->id_parent = Tab::getIdFromClassName('AdminMarketPlaceShopSellers'); 
        $tab->icon= 'icon icon-shop-category'; 
        $languages = Language::getLanguages(false);          
        foreach($languages as $lang){
            $tab->name[$lang['id_lang']] = $module->getTextLang('Shop categories',$lang,'install-3.2.0')?: $module->l('Shop categories','install-3.2.0');
        }
        $tab->save();
    }
    $sqls = array();
    $sqls[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_shop_category` ( 
        `id_ets_mp_shop_category` INT(11) NOT NULL AUTO_INCREMENT , 
        `active` TINYINT(1) NOT NULL , 
        PRIMARY KEY (`id_ets_mp_shop_category`), INDEX (`active`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
    $sqls[] ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_shop_category_lang` ( 
        `id_ets_mp_shop_category` INT(11) NOT NULL , 
        `id_lang` INT(11) NOT NULL , 
        `name` VARCHAR(1000) NOT NULL , 
        PRIMARY KEY (`id_ets_mp_shop_category`, `id_lang`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
    if(!$module->checkCreatedColumn('ets_mp_seller','id_shop_category'))
    {
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller` ADD `id_shop_category` INT(11) NULL';
    }
    if(!$module->checkCreatedColumn('ets_mp_registration','id_shop_category'))
    {
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_registration` ADD `id_shop_category` INT(11) NULL';
    }
    if($sqls)
    {
        foreach($sqls as $sql)
        {
            Db::getInstance()->execute($sql);
        }
    }
    return true;
}