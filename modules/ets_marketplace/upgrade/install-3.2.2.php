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
 */
function upgrade_module_3_2_2($module)
{
    Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_category_commission` ( 
    `id_category` INT(11) NOT NULL , 
    `commission_rate` FLOAT(10,2) NOT NULL , 
    PRIMARY KEY (`id_category`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
    Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_category_commission_seller` ( 
    `id_category` INT(11) NOT NULL , 
    `id_seller` INT(11) NOT NULL,
    `commission_rate` FLOAT(10,2) NOT NULL , 
    PRIMARY KEY (`id_category`,`id_seller`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
    Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_category_commission_group` ( 
    `id_category` INT(11) NOT NULL , 
    `id_group` INT(11) NOT NULL,
    `commission_rate` FLOAT(10,2) NOT NULL , 
    PRIMARY KEY (`id_category`,`id_group`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
    $commission_rate_settings = Ets_mp_defines::getInstance()->getFieldConfig('commission_rate_settings');
    if($commission_rate_settings)
    {
        foreach($commission_rate_settings as $setting)
        {
            if(isset($setting['default']) && !Configuration::hasKey($setting['name']))
                Configuration::updateValue($setting['name'],$setting['default']);
        }
    }
    if(!$module->checkCreatedColumn('ets_mp_seller','enable_commission_by_category'))
    {
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller` ADD `enable_commission_by_category` INT(1) NULL');
    }
    if(!$module->checkCreatedColumn('ets_mp_seller_group','enable_commission_by_category'))
    {
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_group` ADD `enable_commission_by_category` INT(1) NULL');
    }
    return true;
}