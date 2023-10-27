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
function upgrade_module_3_1_7()
{
    Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_product` ( 
            `id_ets_mp_product` INT(11) NOT NULL AUTO_INCREMENT , 
            `id_product` INT(11) NOT NULL , 
            `price` DECIMAL(20,6) NOT NULL , 
            `id_tax_rules_group` INT(11),
            `wholesale_price` DECIMAL(20,6) NOT NULL , 
            `unity` VARCHAR(255) NOT NULL , 
            `unit_price_ratio` DECIMAL(20,6) NOT NULL , 
            `additional_shipping_cost` DECIMAL(20,6) NOT NULL , 
            `reference` VARCHAR(64) NOT NULL , 
            `isbn` VARCHAR(64) NOT NULL , 
            `ean13` VARCHAR(64) NOT NULL , 
            `upc` VARCHAR(64) NOT NULL , 
            `location` VARCHAR(64) NOT NULL , 
            `width` DECIMAL(20,6) NOT NULL , 
            `height` DECIMAL(20,6) NOT NULL , 
            `depth` DECIMAL(20,6) NOT NULL , 
            `weight` DECIMAL(20,6) NOT NULL , 
            `is_virtual` INT(1) NOT NULL , 
            `status` INT(1), 
            `decline` text , 
            `date_add` datetime,
            `date_upd` datetime,
            `filed_change` TEXT NOT NULL , PRIMARY KEY (`id_ets_mp_product`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
    Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_product_lang` ( 
        `id_ets_mp_product` INT(11) NOT NULL , 
        `id_lang` INT(11) NOT NULL , 
        `description` TEXT NOT NULL , 
        `description_short` TEXT NOT NULL , 
        `link_rewrite` VARCHAR(128) NOT NULL , 
        `meta_description` VARCHAR(512) NOT NULL , 
        `meta_keywords` VARCHAR(255) NOT NULL , 
        `meta_title` VARCHAR(128) NOT NULL , 
        `name` VARCHAR(128) NOT NULL , 
        `available_now` VARCHAR(255) NOT NULL , 
        `available_later` VARCHAR(255) NOT NULL , 
        `delivery_in_stock` VARCHAR(255) NOT NULL , 
        `delivery_out_stock` VARCHAR(255) NOT NULL , PRIMARY KEY (`id_ets_mp_product`, `id_lang`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
    Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_product_image` ( 
        `id_product` INT(11) NOT NULL , 
        `id_image` INT(11) NOT NULL , 
        PRIMARY KEY (`id_product`, `id_image`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
    return true;
}