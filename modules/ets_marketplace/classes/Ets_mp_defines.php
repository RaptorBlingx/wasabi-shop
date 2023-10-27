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
class Ets_mp_defines
{ 
    protected static $instance;
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_marketplace', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_mp_defines();
        }
        return self::$instance;
    }
    public static function unInstallDb()
    {
        $tables = array(
            'ets_mp_registration',
            'ets_mp_seller',
            'ets_mp_seller_lang',
            'ets_mp_seller_product',
            'ets_mp_cart_rule_seller',
            'ets_mp_seller_order',
            'ets_mp_seller_commission',
            'ets_mp_commission_usage',
            'ets_mp_payment_method',
            'ets_mp_payment_method_lang',
            'ets_mp_payment_method_field',
            'ets_mp_payment_method_field_lang',
            'ets_mp_attribute_group_seller',
            'ets_mp_attribute_seller',
            'ets_mp_withdrawal',
            'ets_mp_withdrawal_field',
            'ets_mp_seller_billing',
            'ets_mp_manufacturer_seller',
            'ets_mp_seller_customer_message',
            'ets_mp_seller_customer_follow',
            'ets_mp_feature_seller',
            'ets_mp_seller_contact',
            'ets_mp_seller_contact_message',
            'ets_mp_carrier_seller',
            'ets_mp_seller_manager',
            'ets_mp_seller_report',
            'ets_mp_seller_group',
            'ets_mp_seller_group_lang',
            'ets_mp_product',
            'ets_mp_product_image',
            'ets_mp_product_lang',
            'ets_mp_shop_category',
            'ets_mp_category_commission',
        );
        if($tables)
        {
            foreach($tables as $table)
                Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . pSQL($table).'`');
        }
        return true;
    }
    public function _installDb(){
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_registration` (
        `id_registration` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_customer` INT(11) NOT NULL , 
        `id_shop` INT(11) NOT NULL , 
        `id_shop_category` INT(11) , 
        `shop_name` TEXT NOT NULL , 
        `shop_description` TEXT NOT NULL , 
        `shop_address` TEXT NOT NULL , 
        `shop_phone` VARCHAR(222) NOT NULL , 
        `vat_number` VARCHAR(222) NULL ,
        `shop_logo` VARCHAR(222) NOT NULL , 
        `shop_banner` VARCHAR(222) NOT NULL , 
        `banner_url` TEXT , 
        `link_facebook` VARCHAR(1000) NULL , 
        `link_instagram` VARCHAR(1000) NULL , 
        `link_google` VARCHAR(1000) NULL , 
        `link_twitter` VARCHAR(1000) NULL , 
        `message_to_administrator` TEXT NOT NULL ,
        `reason` text null, 
        `comment` text null,
        `date_add` DATETIME ,
        `date_upd` DATETIME ,
        `active` INT(1) NOT NULL , 
        `latitude` decimal(13,8),
        `longitude` decimal(13,8),
        PRIMARY KEY (`id_registration`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller` (
        `id_seller` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_customer` INT(11) NOT NULL , 
        `id_shop` INT(11) NOT NULL ,
        `id_shop_category` INT(11) , 
        `id_group` INT(11) NOT NULL ,  
        `id_billing` INT(11) NOT NULL ,
        `shop_phone` VARCHAR(222) NULL , 
        `vat_number` VARCHAR(222) NULL ,
        `shop_logo` VARCHAR(222) NULL , 
        `link_facebook` VARCHAR(1000) NULL , 
        `link_instagram` VARCHAR(1000) NULL , 
        `link_google` VARCHAR(1000) NULL , 
        `link_twitter` VARCHAR(1000) NULL ,
        `message_to_administrator` TEXT NULL , 
        `reason` text null, 
        `code_chat` TEXT NULL, 
        `commission_rate` FLOAT(10,2) NULL,
        `enable_commission_by_category` INT(1),
        `auto_enabled_product` varchar(22) NULL,
        `payment_verify` INT(1) NULL,
        `user_shipping` INT(1) NULL,
        `user_brand` INT(11) NULL,
        `user_supplier` INT(11) NULL,
        `user_attribute` INT(1) NULL,
        `user_feature` INT(11) NULL,
        `date_add` DATETIME ,
        `date_upd` DATETIME ,
        `date_from` DATE NULL,
        `date_to` DATE NULL, 
        `active` INT(1) NULL , 
        `mail_expired` INT(1) NULL ,
        `mail_going_to_be_expired` INT(1) NULL ,
        `mail_payed` INT(1) NULL ,
        `mail_wait_pay` INT(1) NULL,
        `latitude` decimal(13,8),
        `longitude` decimal(13,8),
        `vacation_mode` INT(1),
        `vacation_type` VARCHAR(100),
        `date_vacation_start` DATETIME ,
        `date_vacation_end` DATETIME ,
        PRIMARY KEY (`id_seller`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller_lang` ( 
        `id_seller` INT(11) NOT NULL , 
        `id_lang` INT NOT NULL , 
        `shop_name` VARCHAR(222) NULL , 
        `shop_banner` VARCHAR(222) NULL ,
        `banner_url` TEXT , 
        `shop_description` TEXT NULL , 
        `shop_address` TEXT NULL , 
        `vacation_notifications` TEXT NULL , 
        PRIMARY KEY (`id_seller`, `id_lang`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller_product` ( 
        `id_seller_product` INT(11) NOT NULL AUTO_INCREMENT ,
        `id_customer` INT(11) NOT NULL , 
        `id_product` INT(11) NOT NULL ,
        `approved` INT(11) NOT NULL , 
        `active` INT(11) NOT NULL , 
        `is_admin` INT(1) NOT NULL , 
        `reason` text,
        PRIMARY KEY (`id_seller_product`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_cart_rule_seller` ( 
        `id_cart_rule` INT(11) NOT NULL , 
        `id_customer` INT(11) NOT NULL, 
        PRIMARY KEY (`id_cart_rule`, `id_customer`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller_order`( 
        `id_customer` INT(11) NOT NULL , 
        `id_order` INT(11) NOT NULL ,
         PRIMARY KEY (`id_customer`, `id_order`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller_customer_message`( 
        `id_customer` INT(11) NOT NULL , 
        `id_manager` INT(11) NOT NULL , 
        `id_customer_message` INT(11) NOT NULL ,
         PRIMARY KEY (`id_customer`, `id_customer_message`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller_commission` ( 
        `id_seller_commission` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_customer` INT(11) NOT NULL , 
        `reference` VARCHAR(22),
        `id_order` INT(11) NULL , 
        `id_product` INT(11) NULL , 
        `id_shop` INT(11) NULL,
        `id_product_attribute` INT(11) NULL,
        `product_name` VARCHAR(222) NULL, 
        `price` FLOAT(10,2) NULL , 
        `price_tax_incl` FLOAT(10,2) NULL , 
        `quantity` INT(11) NULL , 
        `total_price` FLOAT(10,2) NULL,
        `total_price_tax_incl` FLOAT(10,2) NULL,
        `use_tax` INT(11) NULL , 
        `status` INT(11) NULL,
        `commission` FLOAT(10,2) NULL, 
        `expired_date` DATETIME NULL,
        `note` text NULL,
        `date_add` DATETIME NULL,
        `date_upd` DATETIME NULL, 
        PRIMARY KEY (`id_seller_commission`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_commission_usage` ( 
        `id_ets_mp_commission_usage` INT(11) NOT NULL AUTO_INCREMENT , 
        `amount` FLOAT(10,2) NOT NULL , 
        `reference` VARCHAR(22),
        `id_customer` INT(11) NOT NULL , 
        `id_shop` INT(11) NOT NULL , 
        `id_voucher` INT(11) NULL , 
        `id_withdraw` INT(11) NULL , 
        `id_order` INT(11) NULL , 
        `id_currency` INT(11) NULL , 
        `status` INT(11) NULL , 
        `note` TEXT NULL , 
        `date_add` DATETIME NULL , 
        `deleted` INT(1) NULL , 
        PRIMARY KEY (`id_ets_mp_commission_usage`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_payment_method` ( 
        `id_ets_mp_payment_method` INT(11) NOT NULL AUTO_INCREMENT ,
        `id_shop` INT(11) NULL , 
        `fee_type` VARCHAR(222) NULL , 
        `fee_fixed` FLOAT(10,2) NULL , 
        `fee_percent` FLOAT(10,2) NULL , 
        `estimated_processing_time` INT(11) NULL , 
        `logo` VARCHAR(222) NULL ,
        `enable` INT(1) NULL , 
        `deleted` INT(1) NULL , 
        `sort` INT(3) NULL ,
        PRIMARY KEY (`id_ets_mp_payment_method`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_payment_method_lang` ( 
        `id_ets_mp_payment_method` INT(11) NOT NULL , 
        `id_lang` INT(11) NOT NULL , 
        `title` VARCHAR(222) CHARACTER SET utf8 COLLATE utf8_bin NULL, 
        `description` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL , 
        `note` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL , 
        PRIMARY KEY (`id_ets_mp_payment_method`, `id_lang`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_payment_method_field` ( 
        `id_ets_mp_payment_method_field` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_ets_mp_payment_method` INT(11) NOT NULL , 
        `type` VARCHAR(222) NOT NULL , 
        `sort` INT(11) NOT NULL , 
        `required` TINYINT(1) NOT NULL , 
        `enable` TINYINT(1) NOT NULL , 
        `deleted` INT(1) NOT NULL , PRIMARY KEY (`id_ets_mp_payment_method_field`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_payment_method_field_lang` ( 
        `id_ets_mp_payment_method_field` INT(11) NOT NULL , 
        `id_lang` INT(11) NOT NULL ,
        `title` VARCHAR(222) NOT NULL , 
        `description` TEXT NOT NULL , 
        PRIMARY KEY (`id_ets_mp_payment_method_field`, `id_lang`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_attribute_group_seller` ( 
        `id_attribute_group` INT(11) NOT NULL , 
        `id_customer` INT(11) NOT NULL , 
        PRIMARY KEY (`id_attribute_group`, `id_customer`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_attribute_seller` ( 
        `id_attribute` INT(11) NOT NULL , 
        `id_customer` INT(11) NOT NULL , 
        PRIMARY KEY (`id_attribute`, `id_customer`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_feature_seller` ( 
        `id_feature` INT(11) NOT NULL , 
        `id_customer` INT(11) NOT NULL , 
        PRIMARY KEY (`id_feature`, `id_customer`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_manufacturer_seller` ( 
        `id_manufacturer` INT(11) NOT NULL , 
        `id_customer` INT(11) NOT NULL , 
        PRIMARY KEY (`id_manufacturer`, `id_customer`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_supplier_seller` ( 
        `id_supplier` INT(11) NOT NULL , 
        `id_customer` INT(11) NOT NULL , 
        PRIMARY KEY (`id_supplier`, `id_customer`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_cart_rule_seller` ( 
        `id_cart_rule` INT(11) NOT NULL , 
        `id_customer` INT(11) NOT NULL , 
        PRIMARY KEY (`id_cart_rule`, `id_customer`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_attachment_seller` (
        `id_attachment` INT(11) NOT NULL , 
        `id_customer` INT(11) NOT NULL , 
        PRIMARY KEY (`id_attachment`, `id_customer`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_withdrawal` (
        `id_ets_mp_withdrawal` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_ets_mp_payment_method` INT(11) NOT NULL , 
        `status` TINYINT(1) NOT NULL , 
        `fee` FLOAT(10,2) NOT NULL , 
        `fee_type` VARCHAR(222) NOT NULL , 
        `date_add` datetime NOT NULL ,
        `processing_date` datetime NULL ,
        PRIMARY KEY (`id_ets_mp_withdrawal`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_withdrawal_field` ( 
        `id_ets_mp_withdrawal_field` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_ets_mp_withdrawal` INT(11) NOT NULL , 
        `id_ets_mp_payment_method_field` INT(11) NOT NULL , 
        `value` TEXT NOT NULL , 
        `file_name` VARCHAR(42) ,
        PRIMARY KEY (`id_ets_mp_withdrawal_field`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller_billing` ( 
        `id_ets_mp_seller_billing` INT(11) NOT NULL AUTO_INCREMENT ,
        `id_customer` INT(11),
        `id_shop` INT(11),
        `seller_confirm` INT(11),
        `amount` FLOAT(10,2) NULL ,
        `amount_tax` FLOAT(10,2) NULL ,
        `fee_type` VARCHAR(222),
        `reference` VARCHAR(22),
        `date_from` DATE NULL ,
        `date_to` DATE  NULL , 
        `active` INT(1) NULL ,
        `used` INT(1) NULL ,
        `date_add` datetime null,
        `date_upd` datetime null,  
        `note` text,
        `id_employee` int(11),  
         PRIMARY KEY (`id_ets_mp_seller_billing`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller_customer_follow`( 
        `id_seller` INT(11) NOT NULL , 
        `id_customer` INT(11) NOT NULL ,
         PRIMARY KEY (`id_seller`, `id_customer`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller_contact` ( 
        `id_contact` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_customer` INT NULL , 
        `id_seller` INT NULL , 
        `id_product` INT NULL , 
        `id_order` int(11) NULL,
        `name` VARCHAR(222) NULL,
        `email` VARCHAR(222) NULL,
        `phone` VARCHAR(222) NULL,
        PRIMARY KEY (`id_contact`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller_contact_message` ( 
        `id_message` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_contact` INT(11) NULL , 
        `id_customer` INT(11) NULL , 
        `id_seller` INT(11) NULL , 
        `id_manager` INT(11),
        `id_employee` INT(11) NULL , 
        `title` TEXT NULL , 
        `message` TEXT NULL , 
        `read` INT(1) NULL,
        `customer_read` INT(1) NULL,
        `attachment`  VARCHAR(222) NULL,
        `attachment_name`  VARCHAR(222) NULL,
        `date_add` DATETIME NULL , 
        PRIMARY KEY (`id_message`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_carrier_seller` ( 
        `id_carrier_reference` INT(11) NOT NULL , 
        `id_customer` INT(11) NOT NULL , 
        PRIMARY KEY (`id_carrier_reference`, `id_customer`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller_manager` ( 
        `id_ets_mp_seller_manager` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_user` INT(11),
        `id_customer` INT(11),
        `email` VARCHAR(222) NOT NULL , 
        `permission` VARCHAR(1000) NOT NULL , 
        `active` INT(1),
        `delete_product` INT(1),
        PRIMARY KEY (`id_ets_mp_seller_manager`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller_report` ( 
        `id_ets_mp_seller_report` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_customer` INT(11),
        `id_seller` INT(11),
        `id_product` INT(11),
        `title` VARCHAR(1000) NULL , 
        `content` text NULL , 
        `date_add` DATETIME NULL , 
        `date_upd` DATETIME NULL , 
        PRIMARY KEY (`id_ets_mp_seller_report`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller_group` ( 
            `id_ets_mp_seller_group` INT(11) NOT NULL AUTO_INCREMENT, 
            `id_shop` INT(11) NOT NULL , 
            `use_fee_global` INT(11) NOT NULL , 
            `number_product_upload` INT(11) NULL,
            `use_commission_global` INT(11) NOT NULL , 
            `enable_commission_by_category` INT(1),
            `fee_type` VARCHAR(222) NOT NULL , 
            `badge_image` VARCHAR(222) NOT NULL , 
            `list_categories` TEXT NULL , 
            `applicable_product_categories` VARCHAR(100) NULL , 
            `fee_amount` FLOAT(10,2) NOT NULL , 
            `fee_tax` INT(11) NOT NULL , 
            `commission_rate` FLOAT(10,2) NOT NULL ,
            `auto_upgrade` FLOAT(10,2) NOT NULL ,  
            `date_add` DATETIME NOT NULL , 
            `date_upd` DATETIME NOT NULL,
        PRIMARY KEY (`id_ets_mp_seller_group`) ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_seller_group_lang` ( 
            `id_ets_mp_seller_group` INT(11) NOT NULL , 
            `id_lang` INT(11) NOT NULL , 
            `name` TEXT NOT NULL , 
            `level_name` TEXT,
            `description` TEXT NOT NULL , 
        PRIMARY KEY (`id_ets_mp_seller_group`, `id_lang`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_product` ( 
            `id_ets_mp_product` INT(11) NOT NULL AUTO_INCREMENT , 
            `id_product` INT(11) NOT NULL , 
            `price` DECIMAL(20,6) NOT NULL , 
            `id_tax_rules_group` INT(11),
            `wholesale_price` DECIMAL(20,6) NOT NULL , 
            `unit_price` DECIMAL(20,6) NOT NULL , 
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
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_shop_category` ( 
        `id_ets_mp_shop_category` INT(11) NOT NULL AUTO_INCREMENT , 
        `active` TINYINT(1) NOT NULL , 
        PRIMARY KEY (`id_ets_mp_shop_category`), INDEX (`active`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_shop_category_lang` ( 
        `id_ets_mp_shop_category` INT(11) NOT NULL , 
        `id_lang` INT(11) NOT NULL , 
        `name` VARCHAR(1000) NOT NULL , 
        PRIMARY KEY (`id_ets_mp_shop_category`, `id_lang`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
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
        return true;
    }
    public static function createIndexDataBase()
    {
        $sqls = array();
        if(Db::getInstance()->executeS("SHOW INDEX FROM `"._DB_PREFIX_."ets_mp_commission_usage` WHERE KEY_NAME = 'index_commission_usage'"))
            $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_commission_usage` DROP INDEX index_commission_usage';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_commission_usage` ADD INDEX(`id_customer`),ADD INDEX(`id_shop`),ADD INDEX(`id_voucher`),ADD INDEX(`id_withdraw`),ADD INDEX(`id_order`),ADD INDEX(`id_currency`),ADD INDEX(`status`), ADD INDEX(`deleted`)';
        if(Db::getInstance()->executeS("SHOW INDEX FROM `"._DB_PREFIX_."ets_mp_payment_method` WHERE KEY_NAME = 'index_payment_method'"))
            $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_payment_method` DROP INDEX index_payment_method';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_payment_method` ADD INDEX(`id_shop`),ADD INDEX(`enable`),ADD INDEX(`deleted`),ADD INDEX(`sort`)';
        if(Db::getInstance()->executeS("SHOW INDEX FROM `"._DB_PREFIX_."ets_mp_payment_method_field` WHERE KEY_NAME = 'index_payment_method_field'"))
            $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_payment_method_field` DROP INDEX index_payment_method_field';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_payment_method_field` ADD INDEX(`id_ets_mp_payment_method`),ADD INDEX(`sort`),ADD INDEX(`required`),ADD INDEX(`enable`),ADD INDEX(`deleted`)';
        if(Db::getInstance()->executeS("SHOW INDEX FROM `"._DB_PREFIX_."ets_mp_registration` WHERE KEY_NAME = 'index_registration'"))
            $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_registration` DROP INDEX index_registration';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_registration` ADD INDEX(`id_customer`),ADD INDEX(`id_shop`),ADD INDEX(`active`),ADD INDEX(`id_shop_category`)';
        if(Db::getInstance()->executeS("SHOW INDEX FROM `"._DB_PREFIX_."ets_mp_seller` WHERE KEY_NAME = 'index_seller'"))
            $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller` DROP INDEX index_seller';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller` ADD INDEX(`id_customer`),ADD INDEX(`id_shop`),ADD INDEX(`active`),ADD INDEX(`payment_verify`),ADD INDEX(`user_shipping`),ADD INDEX(`mail_expired`),ADD INDEX(`mail_going_to_be_expired`),ADD INDEX(`mail_payed`),ADD INDEX(`mail_wait_pay`),ADD INDEX(`id_shop_category`),ADD iNDEX(`id_group`)';
        if(Db::getInstance()->executeS("SHOW INDEX FROM `"._DB_PREFIX_."ets_mp_seller_billing` WHERE KEY_NAME = 'index_seller_billing'"))
            $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_billing` DROP INDEX index_seller_billing';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_billing` ADD INDEX(`id_customer`),ADD INDEX(`id_shop`),ADD INDEX(`active`),ADD INDEX(`used`),ADD INDEX(`id_employee`)';
        $sqls[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ .'ets_mp_seller_carrier`';
        if(Db::getInstance()->executeS("SHOW INDEX FROM `"._DB_PREFIX_."ets_mp_seller_commission` WHERE KEY_NAME = 'index_seller_commission'"))
            $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_commission` DROP INDEX index_seller_commission';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_commission` ADD INDEX(`id_customer`), ADD INDEX(`id_order`),ADD INDEX(`id_product`),ADD INDEX(`id_shop`),ADD INDEX(`id_product_attribute`),ADD INDEX(`status`)';
        if(Db::getInstance()->executeS("SHOW INDEX FROM `"._DB_PREFIX_."ets_mp_seller_contact` WHERE KEY_NAME = 'index_seller_contact'"))
            $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_contact` DROP INDEX index_seller_contact';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_contact` ADD INDEX(`id_customer`),ADD INDEX(`id_seller`),ADD INDEX(`id_product`),ADD INDEX(`id_order`)';
        if(Db::getInstance()->executeS("SHOW INDEX FROM `"._DB_PREFIX_."ets_mp_seller_contact_message` WHERE KEY_NAME = 'index_seller_contact_message'"))
            $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_contact_message` DROP INDEX  index_seller_contact_message';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_contact_message` ADD INDEX(`id_contact`),ADD INDEX(`id_customer`),ADD INDEX(`id_seller`),ADD INDEX(`id_employee`),ADD index(`id_manager`),ADD INDEX(`read`),ADD INDEX(`customer_read`)';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_customer_message` ADD INDEX(`id_manager`)';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_group` ADD INDEX(`id_shop`)';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_manager` ADD INDEX(`id_user`),ADD INDEX(`id_customer`),ADD INDEX(`active`)';
        if(Db::getInstance()->executeS("SHOW INDEX FROM `"._DB_PREFIX_."ets_mp_seller_product` WHERE KEY_NAME = 'index_seller_product'"))
            $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_product` DROP INDEX index_seller_product';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_product` ADD INDEX(`id_customer`),ADD INDEX(`id_product`),ADD INDEX(`approved`),ADD INDEX(`active`),ADD INDEX(`is_admin`)';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_report` ADD INDEX(`id_customer`),ADD INDEX(`id_product`),ADD INDEX(`id_seller`)';
        if(Db::getInstance()->executeS("SHOW INDEX FROM `"._DB_PREFIX_."ets_mp_withdrawal` WHERE KEY_NAME = 'index_withdrawal'"))
            $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_withdrawal` DROP INDEX index_withdrawal';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_withdrawal` ADD INDEX(`id_ets_mp_payment_method`),ADD INDEX(`status`)';
        if(Db::getInstance()->executeS("SHOW INDEX FROM `"._DB_PREFIX_."ets_mp_withdrawal_field` WHERE KEY_NAME = 'index_withdrawal_field'"))
            $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_withdrawal_field` DROP INDEX index_withdrawal_field';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_withdrawal_field` ADD INDEX(`id_ets_mp_withdrawal`),ADD INDEX(`id_ets_mp_payment_method_field`)';
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_product` ADD INDEX(`id_product`) ,ADD INDEX(`id_tax_rules_group`),ADD INDEX(`is_virtual`),ADD INDEX(`status`)';
        foreach($sqls as $sql)
            Db::getInstance()->execute($sql);
        return true;
    }
    public function getFieldConfig($type)
    {
        if($type=='configTabs')
        {
            return array(
                'conditions' => $this->l('Conditions'),
                'application' => $this->l('Application'),
                'memberships' => $this->l('Membership'),
                'seller_settings' => $this->l('Shop'),
                'seller_seo' => $this->l('SEO'),
                'map_settings' => $this->l('Map'),
                'commission_status' => $this->l('Order'),
                'email_settings' => $this->l('Email'),
                'message' => $this->l('Messages'),
                'contact_form' => $this->l('Contact form'),
                'home_page' => $this->l('Home page'),
                'product_page' => $this->l('Product page'),
            );
        }
        if($type=='sidebars')
        {
            return array(
                'dashboard' => array(
                    'title'=>$this->l('Dashboard'),
                    'controller' => 'AdminMarketPlaceDashboard',
                ),
                'orders' => array(
                    'title' => $this->l('Orders'),
                    'controller' => 'AdminMarketPlaceOrders',
                ),
                'products' => array(
                    'title'=> $this->l('Products'),
                    'controller'=>'AdminMarketPlaceProducts',
                ),
                'ratings' => array(
                    'title'=> $this->l('Ratings'),
                    'controller'=>'AdminMarketPlaceRatings',
                ),
                'commission' => array(
                    'title' => $this->l('Commissions'),
                    'controller'=> 'AdminMarketPlaceCommissions',
                ),
                'billing' => array(
                    'title' => $this->l('Membership'),
                    'controller'=> 'AdminMarketPlaceBillings', 
                ),
                'withdraw' => array(
                    'title' => $this->l('Withdrawals'),
                    'controller'=>'AdminMarketPlaceWithdrawals'
                ),
                'sellers_registration' => array(
                    'title' => $this->l('Applications'),
                    'controller'=>'AdminMarketPlaceRegistrations',
                ),
                'sellers' => array(
                    'title'=>$this->l('Shops'),
                    'controller' =>'AdminMarketPlaceSellers',
                    'subs' => array(
                        'AdminMarketPlaceSellers' => array(
                            'title' => $this->l('Shops'),
                            'controller'=> 'AdminMarketPlaceSellers',
                            'icon' => 'sellers',
                        ),
                        'AdminMarketPlaceShopGroups' => array(
                            'title' => $this->l('Shop groups'),
                            'controller'=> 'AdminMarketPlaceShopGroups',
                            'icon' => 'group',
                        ),
                        'AdminMarketPlaceReport' => array(
                            'title' => $this->l('Reports'),
                            'controller' => 'AdminMarketPlaceReport',
                            'icon' => 'report',
                        ),
                        'AdminMarketPlaceCategory' => array(
                            'title' => $this->l('Shop categories'),
                            'controller' => 'AdminMarketPlaceCategory',
                            'icon' => 'shop-category',
                        )
                        
                    ),
                ),
                'settings' => array(
                    'title' => $this->l('Settings'),
                    'controller' => 'AdminMarketPlaceSettings',
                    'subs' => array(  
                        'AdminMarketPlaceSettingsGeneral' => array(
                            'title' => $this->l('General'),
                            'controller'=>'AdminMarketPlaceSettingsGeneral',
                            'icon' => 'settings'                            
                        ),              
                        'AdminMarketPlaceCommissionsUsage' => array(
                            'title' => $this->l('Commissions'),
                            'controller'=> 'AdminMarketPlaceCommissionsUsage',
                            'icon' => 'commissions-usage', 
                        ),
                        'AdminMarketPlaceCronJob' => array(
                            'title' => $this->l('Cronjob'),
                            'controller' => 'AdminMarketPlaceCronJob',
                            'icon' => 'cronjob',
                        ),
                    ),                    
                ),
            );
        }
        if($type=='commission_rate_settings')
        {
            $context = Context::getContext();
            return  array(
                array(
                    'name' => 'ETS_MP_COMMISSION_RATE',
                    'type'=> 'text',
                    'label' => $this->l('Global shop commission rate'),
                    'suffix' => '%',
                    'default'=> '90',
                    'desc' => $this->l('You can customize commission rate for each seller shop or shop group. Click ').Module::getInstanceByName('ets_marketplace')->displayText($this->l('here'),'a','','',(isset($context->employee) && $context->employee->id ? Context::getContext()->link->getAdminLink('AdminMarketPlaceShopGroups') :'#')).$this->l(' to config'),
                    'required' => true,
                    'tab'=>'seller_settings',
                    'validate' => 'isPrice',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Enable commission by categories'),
                    'name' => 'ETS_MP_ENABLE_COMMISSION_BY_CATEGORY',
                    'default'=>0,
                    'desc' => $this->l('You can customize commission by categories for each seller shop or shop group. Click ').Module::getInstanceByName('ets_marketplace')->displayText($this->l('here'),'a','','',isset($context->employee) && $context->employee->id ? Context::getContext()->link->getAdminLink('AdminMarketPlaceShopGroups') :'#','_blank').$this->l(' to config'),
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name'=>'category_commission_rate',
                    'label' => $this->l('Commission rate by categories'),
                    'type' => 'tre_categories',
                    'form_group_class' => 'category_commission_rate',
                    'tree'=> Tools::isSubmit('tabActive') ? Module::getInstanceByName('ets_marketplace')->displayFormCategoryCommissionRate():'',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Exclude taxes before calculating commission'),
                    'name' => 'ETS_MP_COMMISSION_EXCLUDE_TAX',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Recalculate commission when admin add/edit order'),
                    'name' => 'ETS_MP_RECALCULATE_COMMISSION',
                    'default'=>1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'seller_settings',
                ),
            );
        }
        if($type =='commission_usage_settings')
        {
            return  array(
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow sellers to pay for their order using commission balance'),
                    'name' => 'ETS_MP_ALLOW_BALANCE_TO_PAY',
                    'default'=>1,
                    'desc' => $this->l('Customers can use their reward balance to pay for their order if their reward balance is greater than or equal to the order\'s total value.'),
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' =>'text',
                    'name' => 'ETS_MP_MIN_BALANCE_REQUIRED_FOR_ORDER',
                    'label' => $this->l('Minimum commission balance required to be usable to pay for order'),
                    'validate' => 'isUnsignedFloat',
                    'suffix' => Context::getContext()->currency->iso_code,
                    'form_group_class' => 'usage_order',
                    'desc' => $this->l('Commission balance need to exceed this value to allow sellers to use it for checkout process. Leave blank to allow sellers to use commission balance without this limit'),
                ),
                array(
                    'type' => 'text',
                    'name' => 'ETS_MP_MAX_BALANCE_REQUIRED_FOR_ORDER',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Maximum commission balance can be used to pay for each order'),
                    'desc' => $this->l('The maximum amount of commission that can be used to pay for each order when sellers checkout. Leave blank to allow sellers to pay for their orders using any amount of commission they have in their account'),
                    'col' => 5,
                    'suffix' => Context::getContext()->currency->iso_code,
                    'form_group_class' => 'usage_order',
                ),
                array(
                    'type' => 'switch',
                    'name'=>'ETS_MP_ALLOW_CONVERT_TO_VOUCHER',
                    'col' => 3,
                    'label' => $this->l('Allow sellers to convert commission balance into voucher'),
                    'desc' => $this->l('They can use this voucher when checking out their order'),
                    'default' => 1,
                    'form_group_class' => 'line_top',
                    'divider_before' => true,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' => 'text',
                    'name' => 'ETS_MP_MIN_BALANCE_REQUIRED_FOR_VOUCHER',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Minimum commission balance required to be usable to convert into voucher'),
                    'desc' => $this->l('Commission balance need to exceed this value to allow sellers to convert into voucher. Leave blank to allow sellers to use commission balance without this limit'),
                    'col' => 5,
                    'suffix' => Context::getContext()->currency->iso_code,
                    'form_group_class' => 'usage_voucher',
                ),
                array(
                    'type' => 'text',
                    'name' => 'ETS_MP_MAX_BALANCE_REQUIRED_FOR_VOUCHER',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Maximum commission balance that can be converted into voucher (each time)'),
                    'desc' => $this->l('The maximum amount of commission balance that sellers can convert into voucher code (each time they do that). Leave blank to allow sellers to convert any amount of commission balance into voucher code.'),
                    'col' => 5,
                    'suffix' => Context::getContext()->currency->iso_code,
                    'form_group_class' => 'usage_voucher',
                ),
                array(
                    'type' => 'switch',
                    'col' => 3,
                    'name' => 'ETS_MP_ALLOW_VOUCHER_IN_CART',
                    'label' => $this->l('Display "Convert voucher" message in shopping cart'),
                    'default' => 1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'form_group_class' => 'usage_voucher',
                ),
                array(
                    'type'=>'text',
                    'name' => 'ETS_MP_DEFAULT_VOUCHER_NAME',
                    'lang'=>true,
                    'label' => $this->l('Discount name'),
                    'default'=> $this->l('Converted from commission balance'),
                    'form_group_class' => 'usage_voucher',
                ),
                array(
                    'type' => 'switch',
                    'col' => 3,
                    'name' => 'ETS_MP_ALLOW_WITHDRAW',
                    'label' => $this->l('Allow seller to withdraw commission'),
                    'default' => 1,
                    'divider_before' => true,
                    'form_group_class' => 'line_top',
                    'desc' => $this->l('Enable this feature to allow sellers to withdraw their commission balance to their bank account, PayPal account, Amazon gift card, etc. Create withdrawal methods you want in "Withdrawal methods" tab'),
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' => 'text',
                    'validate' => 'isUnsignedFloat',
                    'name' => 'ETS_MP_BALANCE_REQUIRED_FOR_WITHDRAW',
                    'label' => $this->l('Minimum commission balance required to be usable to withdraw'),
                    'desc' => $this->l('Seller balance need to exceed this value to allow sellers to withdraw. Leave blank to allow seller to use commission balance without this limit'),
                    'col' => 5,
                    'suffix' => Context::getContext()->currency->iso_code,
                    'form_group_class' => 'usage_withdraw',
                ),
                array(
                    'type' => 'text',
                    'validate' => 'isUnsignedFloat',
                    'name' => 'ETS_MP_MAX_WITHDRAW',
                    'label' => $this->l('Maximum amount can withdraw each request'),
                    'col' => 5,
                    'suffix' => Context::getContext()->currency->iso_code,
                    'desc' => $this->l('Maximum amount of commission balance that seller can withdraw (each time). Leave blank to allow seller to withdraw any amount of commission balance they have in their account.'),
                    'form_group_class' => 'usage_withdraw',
                ),
                array(
                    'type' => 'switch',
                    'col' => 3,
                    'name' => 'ETS_MP_WITHDRAW_INVOICE_REQUIRED',
                    'label' => $this->l('Require invoice?'),
                    'default' => 0,
                    'desc' => $this->l('Ask seller to submit an invoice when they withdraw their commission balance.'),
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                     'form_group_class' => 'usage_withdraw',
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_WITHDRAW_ONE_ONLY',
                    'col' => 3,
                    'label' => $this->l('Require seller to wait until the last pending withdrawal request to be processed to submit a new one?'),
                    'default' => 0,
                    'desc' => $this->l('Enable this option will limit seller to be able to submit a new withdrawal request if the last one still being processed'),
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                     'form_group_class' => 'usage_withdraw',
                ),
           );
        }
        if($type =='settings')
        {
            $id_root_category = Db::getInstance()->getValue('SELECT id_category FROM `'._DB_PREFIX_.'category` WHERE is_root_category=1');
            $sub_categories_default=array();
            $categories = Db::getInstance()->executeS('SELECT id_category FROM `'._DB_PREFIX_.'category` WHERE id_parent='.(int)$id_root_category);
            if($categories)
            {
                foreach($categories as $category)
                    $sub_categories_default[]= $category['id_category'].',';
            }
            $groups = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'group` g
                INNER JOIN `'._DB_PREFIX_.'group_shop` gs ON (g.id_group=gs.id_group)
                LEFT JOIN `'._DB_PREFIX_.'group_lang` gl ON (g.id_group = gl.id_group AND gl.id_lang ="'.(int)Context::getContext()->language->id.'")
                WHERE gs.id_shop = "'.(int)Context::getContext()->shop->id.'" AND g.id_group!="'.(int)Configuration::get('PS_GUEST_GROUP').'" AND g.id_group!="'.(int)Configuration::get('PS_UNIDENTIFIED_GROUP').'"
                GROUP BY g.id_group');
            $default_groups = '';
            if($groups)
            {
                foreach($groups as $group)
                    $default_groups .= $group['id_group'].',';
            }
            $status = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'order_state` os
                LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.id_order_state =osl.id_order_state AND osl.id_lang="'.(int)Context::getContext()->language->id.'")
                GROUP BY os.id_order_state
            ');
            $disabled_categories =array();
            $roots = Db::getInstance()->executeS('SELECT id_category FROM `'._DB_PREFIX_.'category` WHERE is_root_category=1');
            if($roots)
            {
                foreach($roots as $root)
                    $disabled_categories[$root['id_category']] = $root['id_category'];
            }
            $ets_marketplace = Module::getInstanceByName('ets_marketplace');
            $context = Context::getContext();
            $ETS_MP_SELLER_CATEGORIES = explode(',',Configuration::get('ETS_MP_SELLER_CATEGORIES'));
            if(!Ets_marketplace::validateArray($ETS_MP_SELLER_CATEGORIES))
                $ETS_MP_SELLER_CATEGORIES = array();
            return array(
                array(
                    'type' =>'switch',
                    'label' => $this->l('Enable marketplace'),
                    'name' => 'ETS_MP_ENABLED',
                    'default'=>1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'conditions',
                    'desc'=>$this->l('Enable marketplace and allow customers to become sellers of your website. They can upload their products to sell on your website and get commission')
                ),
                array(
                    'type'=>'checkbox',
                    'name' => 'ETS_MP_SELLER_GROUPS',
                    'label' => $this->l('Applicable customer group'),
                    'desc' => $this->l('Select customer groups who can join marketplace and become a seller'),
                    'values' => array(
                        'query'=> $groups,
                        'id' => 'id_group',
                        'name' => 'name',
                    ),
                    'tab'=>'conditions',
                    'default' => trim($default_groups,','),
                    'required' => true,
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Require customers to submit an application'),
                    'name' => 'ETS_MP_REQUIRE_REGISTRATION',
                    'default'=>1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'application',
                    'desc'=>$this->l('You (admin) will need to manually approve marketplace joining requests')
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'ETS_MP_REGISTRATION_FIELDS',
                    'tab'=>'application',
                    'label'=> $this->l('Select fields to get information from sellers'),
                    'default' => 'seller_name,seller_email,shop_phone,message_to_administrator',
                    'values' => array(
                        'query'=> array(
                            array(
                                'id'=> 'seller_name',
                                'name' => $this->l('Seller name'),
                            ),
                            array(
                                'id'=> 'seller_email',
                                'name' => $this->l('Email'),
                            ),
                            array(
                                'id'=> 'shop_phone',
                                'name' => $this->l('Phone number'),
                            ),
                            array(
                                'id'=> 'message_to_administrator',
                                'name' => $this->l('Introduction'),
                            ),
                            array(
                                'id'=> 'shop_name',
                                'name' => $this->l('Shop name'),
                            ),
                            array(
                                'id'=> 'shop_description',
                                'name' => $this->l('Shop description'),
                            ),
                            array(
                                'id'=> 'shop_address',
                                'name' => $this->l('Shop address'),
                            ),
                            array(
                                'id'=> 'latitude',
                                'name' => $this->l('Latitude'),
                            ),
                            array(
                                'id'=> 'longitude',
                                'name' => $this->l('Longitude'),
                            ),
                            array(
                                'id'=> 'vat_number',
                                'name' => $this->l('VAT number'),
                            ),
                            array(
                                'id'=> 'shop_logo',
                                'name' => $this->l('Shop logo'),
                            ),
                            array(
                                'id'=> 'shop_banner',
                                'name' => $this->l('Shop banner'),
                            ),
                            array(
                                'id'=> 'banner_url',
                                'name' => $this->l('Banner URL'),
                            ),
                            array(
                                'id'=> 'link_facebook',
                                'name' => $this->l('Facebook link'),
                            ),
                            array(
                                'id'=> 'link_instagram',
                                'name' => $this->l('Instagram link'),
                            ),
                            array(
                                'id'=> 'link_google',
                                'name' => $this->l('Google link'),
                            ),
                            array(
                                'id'=> 'link_twitter',
                                'name' => $this->l('Twitter link'),
                            ),
                            array(
                                'id'=> 'id_shop_category',
                                'name' => $this->l('Shop category'),
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'form_group_class' => 'ets_mp_registration_fields'
                ),
                array(
                    'name'=> 'ETS_MP_TERM_LINK',
                    'type'=> 'text',
                    'label' => $this->l('"Terms and condition of use" link'),
                    'desc' => $this->l('Seller is required to read and accept the "Terms and conditions of use" to submit their application. Leave blank to ignore this requirement.'),
                    'tab'=>'application',
                    'lang'=> true,
                    'form_group_class' => 'ets_mp_registration_fields'
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Enable contact feature for seller shop'),
                    'name' => 'ETS_MP_ENABLE_CONTACT_SHOP',
                    'default'=>1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'contact_form',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow guest to contact seller shop'),
                    'name' => 'ETS_MP_ALLOW_GUEST_CONTACT_SHOP',
                    'default'=>1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'contact_form',
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'ETS_MP_CONTACT_FIELDS',
                    'tab'=>'contact_form',
                    'label'=> $this->l('Select fields to display on contact shop form'),
                    'default' => 'product_link,title,reference,message,attachment',
                    'values' => array(
                        'query'=> array(
                            array(
                                'id'=> 'product_link',
                                'name' => $this->l('Product link'),
                            ),
                            array(
                                'id'=> 'name',
                                'name' => $this->l('Name'),
                            ),
                            array(
                                'id'=> 'email',
                                'name' => $this->l('Email'),
                            ),
                            array(
                                'id'=> 'phone',
                                'name' => $this->l('Phone'),
                            ),
                            array(
                                'id'=> 'title',
                                'name' => $this->l('Title'),
                            ),
                            array(
                                'id'=> 'reference',
                                'name' => $this->l('Order reference'),
                            ),
                            array(
                                'id'=> 'message',
                                'name' => $this->l('Message'),
                            ),
                            array(
                                'id'=> 'attachment',
                                'name' => $this->l('Attached file'),
                            ),

                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'name' => $this->l('ETS_MP_SELLER_GROUP_DEFAULT'),
                    'type'=>'select',
                    'label' => $this->l('Default seller group'),
                    'options' => array(
            			 'query' => array_merge(array(array('id_group'=>'0','name'=>'--')),Ets_mp_seller_group::_getSellerGroups()),                             
                         'id' => 'id_group',
            			 'name' => 'name'  
                    ),    
                    'default' => 0,
                    'tab'=>'memberships', 
                ),
                array(
                    'name'=> 'ETS_MP_SELLER_FEE_TYPE',
                    'type' => 'radio',
                    'label' => $this->l('Registration fee to become a seller'),
                    'values' => array(
                        array(
                            'id' => 'ETS_MP_SELLER_FEE_TYPE_no_fee',
                            'value'=>'no_fee',
                            'label' => $this->l('No fee'),
                        ),
                        array(
                            'id' => 'ETS_MP_SELLER_FEE_TYPE_pay_once',
                            'value'=>'pay_once',
                            'label' => $this->l('Pay once')
                        ),
                        array(
                            'id' => 'ETS_MP_SELLER_FEE_TYPE_monthly_fee',
                            'value'=>'monthly_fee',
                            'label' => $this->l('Monthly fee')
                        ),
                        array(
                            'id' => 'ETS_MP_SELLER_FEE_TYPE_quarterly_fee',
                            'value'=>'quarterly_fee',
                            'label' => $this->l('Quarterly fee')
                        ),
                        array(
                            'id' => 'ETS_MP_SELLER_FEE_TYPE_yearly_fee',
                            'value'=>'yearly_fee',
                            'label' => $this->l('Yearly fee')
                        ),
                    ),
                    'tab'=>'memberships',
                    'default' => 'no_fee',
                ),
                array(
                    'name' => 'ETS_MP_SELLER_FEE_AMOUNT',
                    'label' => $this->l('Fee amount'),
                    'tab'=>'memberships',
                    'type'=>'text',
                    'required' => true,
                    'validate' => 'isPrice', 
                    'form_group_class' => 'ets_mp_fee',
                    'suffix' => Context::getContext()->currency->sign,
                ),
                array(
                    'name' => 'ETS_MP_SELLER_FEE_TAX',
                    'label' => $this->l('Fee tax'),
                    'type' => 'select',
                    'options' => array(
            			 'query' => TaxRulesGroup::getTaxRulesGroupsForOptions(),                             
                         'id' => 'id_tax_rules_group',
            			 'name' => 'name'  
                    ),    
                    'default' => 0,
                    'tab'=>'memberships', 
                    'form_group_class'=>'ets_mp_fee'                          
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_ENABLED_IF_NO_FEE',
                    'label' => $this->l('Auto approve seller shop when "No fee" is selected'),
                    'default'=> 1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'memberships',
                    'form_group_class' => 'ets_mp_no_fee',
                ),
                array(
                    'name' => 'ETS_MP_SELLER_FEE_EXPLANATION',
                    'type'=>'textarea',
                    'label'=> $this->l('Fee explanation'),
                    'tab'=>'memberships',
                    'lang'=> true,
                    'desc' => $this->l('Display fee explanation to sellers. Available tag: [highlight][fee_amount][end_highlight]. Leave this field blank if you do not want to display the fee explanation'),
                ),
                array(
                    'name' => 'ETS_MP_MESSAGE_EXPIRE_BEFORE_DAY',
                    'type' => 'text',
                    'label'=> $this->l('Display message when seller account is going to be expired before'),
                    'default'=>7,
                    'validate' => 'isunsignedInt',
                    'tab'=>'memberships',
                    'suffix'=> $this->l('day(s)'),
                    'desc' => $this->l('On X days before seller account is expired, a notification will be display to seller. X is the time value you set above.'),
                ),
                array(
                    'name' =>'ETS_MP_SELLER_PAYMENT_INFORMATION',
                    'validate'=>'isCleanHtml',
                    'label'=> $this->l('Payment information of the marketplace manager'),
                    'default' => $this->l('PayPal account: example@paypal_email'),
                    'type' => 'textarea',
                    'lang'=> true,
                    'tab'=>'memberships',
                    'desc' => $this->l('Enter your PayPal account or bank account here to get registration fee. Available tag:[highlight][shop_id][end_highlight],[highlight][shop_name][end_highlight],[highlight][seller_name][end_highlight],[highlight][seller_email][end_highlight]'),
                    'required' => true,
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_APPROVE_AUTO_BY_BILLING',
                    'label' => $this->l('Approve seller account automatically when billing invoice is set as Paid'),
                    'default'=> 1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'memberships',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Seller products need to be approved by Admin'),
                    'name' => 'ETS_MP_SELLER_PRODUCT_APPROVE_REQUIRED',
                    'default'=>0,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'desc' => $this->l('Uploaded product is invisible until it is approved by Admin'),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Seller needs to have approval from the administrator to edit product'),
                    'name' => 'ETS_MP_EDIT_PRODUCT_APPROVE_REQUIRED',
                    'default'=>0,
                    'tab'=>'seller_settings',
                    'form_group_class' => 'approve_edit',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'desc' => $this->l('Seller can edit product information and submit them to Admin. After being approved by Admin, the product info will be updated to the latest version'),
                ),
                array(
                    'type' =>'checkbox',
                    'label' => $this->l('Seller needs to have approval from the administrator to edit these product information fields'),
                    'name' => 'ETS_MP_FIELD_PRODUCT_APPROVE_REQUIRED',
                    'default'=>0,
                    'tab'=>'seller_settings',
                    'form_group_class' => 'approve_edit field_approve',
                    'required2'=>true,
                    'values' => array(
                        'query' => $this->getFieldChangeProduct(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'desc' => $this->l('Seller can edit the selected product information fields above and submit them to Admin. After being approved by Admin, the product info fields will be updated to the latest version.'),
                ),
                array(
                    'name' => 'ETS_MP_APPLICABLE_CATEGORIES',
                    'label' => $this->l('Applicable product categories'),
                    'desc' => $this->l('Select product categories which sellers can submit their products to sell on marketplace. You can customize applicable product categories for each seller shop or shop group. Click').Module::getInstanceByName('ets_marketplace')->displayText($this->l('here'),'a','','',isset($context->employee) && $context->employee->id ? Context::getContext()->link->getAdminLink('AdminMarketPlaceShopGroups') :'#','_blank').$this->l(' to config'),
                    'type' => 'radio',
                    'default' => 'all_product_categories',
                    'values' => array(
                        array(
                            'id' => 'ETS_MP_APPLICABLE_CATEGORIES_all_product_categories',
                            'value'=>'all_product_categories',
                            'label' => $this->l('All product categories')
                        ),
                        array(
                            'id' => 'ETS_MP_APPLICABLE_CATEGORIES_specific_product_categories',
                            'label' => $this->l('Specific product categories'),
                            'value' => 'specific_product_categories'
                        )
                    ),
                    'tab'=>'seller_settings',
                    'required' => true,
                ),
                array(
                    'name'=>'ETS_MP_SELLER_CATEGORIES',
                    'label' => $this->l('Categories'),
                    'type' => 'tre_categories',
                    'default' => $sub_categories_default,
                    'use_checkbox'=>true,
                    'form_group_class' => 'seller_categories',
                    'tab'=>'seller_settings',
                    'required2'=>true,
                    'tree'=> $ets_marketplace->displayProductCategoryTre(Ets_mp_defines::getCategoriesTree(),$ETS_MP_SELLER_CATEGORIES,'ETS_MP_SELLER_CATEGORIES',$disabled_categories,0,true),
                ),
                array(
                    'name' => 'ETS_MP_SELLER_MAXIMUM_UPLOAD',
                    'type'=> 'text',
                    'label' => $this->l('Maximum number of uploadable products'),
                    'default'=> '',
                    'desc' => $this->l('You can customize maximum number of uploadable products for each seller shop or shop group. Click ').Module::getInstanceByName('ets_marketplace')->displayText($this->l('here'),'a','','',isset($context->employee) && $context->employee->id ? Context::getContext()->link->getAdminLink('AdminMarketPlaceShopGroups') :'#','_blank').$this->l(' to config'),
                    'col'=>2,
                    'tab'=>'seller_settings',
                    'validate' => 'isUnsignedInt',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow sellers to generate their voucher code?'),
                    'name' => 'ETS_MP_SELLER_CAN_CREATE_VOUCHER',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'desc' => $this->l('Sellers can generate their voucher code to send to their customers'),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow sellers to limit their voucher code to a single customer?'),
                    'name' => 'ETS_MP_SELLER_LIMIT_USER_CODE',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'form_group_class' => 'limit_user_code',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to add new product'),
                    'name' => 'ETS_MP_ALLOW_SELLER_CREATE_PRODUCT',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to edit product'),
                    'name' => 'ETS_MP_ALLOW_SELLER_EDIT_PRODUCT',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to delete product'),
                    'name' => 'ETS_MP_ALLOW_SELLER_DELETE_PRODUCT',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to create their own carriers (shipping methods)'),
                    'name' => 'ETS_MP_SELLER_CREATE_SHIPPING',
                    'default'=>0,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to use store\'s global carriers (shipping methods)'),
                    'name' => 'ETS_MP_SELLER_USER_GLOBAL_SHIPPING',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'form_group_class' => 'create_shipping',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow customers to select a specific carrier for each seller shop when checking out'),
                    'name' => 'ETS_MP_ENABLE_MULTI_SHIPPING',
                    'default'=>0,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'form_group_class' => 'create_shipping',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to create their own brands (manufacturers)'),
                    'name' => 'ETS_MP_SELLER_CREATE_BRAND',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to use store\'s global brands (manufacturers)'),
                    'name' => 'ETS_MP_SELLER_USER_GLOBAL_BRAND',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'form_group_class' => 'create_brand',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to create their own suppliers'),
                    'name' => 'ETS_MP_SELLER_CREATE_SUPPLIER',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to use store\'s global suppliers'),
                    'name' => 'ETS_MP_SELLER_USER_GLOBAL_SUPPLIER',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'form_group_class' => 'create_supplier',
                ),
                array(
                    'name' => 'ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT',
                    'type'=> 'checkbox',
                    'label' => $this->l('Available product types for seller'),
                    'desc' => $this->l('Select product types which sellers can upload to their shop'),
                    'tab'=>'seller_settings',
                    'required' => true,
                    'default' => 'standard_product,pack_product,virtual_product',
                    'values' => array(
                        'query' => array(
                            array(
                                'id' => 'standard_product',
                                'name' => $this->l('Standard product'),
                            ),
                            array(
                                'id'=> 'pack_product',
                                'name'=>$this->l('Pack of product'),
                            ),
                            array(
                                'id'=> 'virtual_product',
                                'name'=>$this->l('Virtual product'),
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to create product with combinations'),
                    'name' => 'ETS_MP_SELLER_CREATE_PRODUCT_ATTRIBUTE',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to create their own product attributes'),
                    'name' => 'ETS_MP_SELLER_CREATE_ATTRIBUTE',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'form_group_class' => 'create_attribute',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to use store\'s global attributes'),
                    'name' => 'ETS_MP_SELLER_USER_GLOBAL_ATTRIBUTE',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'form_group_class' => 'create_attribute',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to create their own product features'),
                    'name' => 'ETS_MP_SELLER_CREATE_FEATURE',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to use store\'s global features'),
                    'name' => 'ETS_MP_SELLER_USER_GLOBAL_FEATURE',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'form_group_class' => 'create_feature',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Display reviews that are waiting to be approved'),
                    'name' => 'ETS_MP_SELLER_DISPLAY_REVIEWS_WAITING',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'form_group_class' => 'create_feature',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to approve reviews from customers'),
                    'name' => 'ETS_MP_SELLER_APPROVE_REVIEW',
                    'default'=>0,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'form_group_class' => '',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to delete review'),
                    'name' => 'ETS_MP_SELLER_DELETE_REVIEW',
                    'default'=>0,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'form_group_class' => '',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Return shipping fee to seller'),
                    'name' => 'ETS_MP_RETURN_SHIPPING',
                    'default'=>1,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'form_group_class' => '',
                ),
                array(
                    'name' => 'ETS_MP_SELLER_ALLOWED_INFORMATION_SUBMISSION',
                    'label' => $this->l('Allow seller to submit these product information'),
                    'type'=> 'checkbox',
                    'tab'=>'seller_settings',
                    'required' => true,
                    'values' => array(
                        'query' => array(
                            array(
                                'id'=> 'product_reference',
                                'name' => $this->l('Product reference'),
                            ),
                            array(
                                'id'=> 'short_description',
                                'name' => $this->l('Short description'),
                            ),
                            array(
                                'id' => 'specific_price',
                                'name' => $this->l('Specific price'),
                            ),
                            array(
                                'id' => 'tax',
                                'name' => $this->l('Tax'),
                            ),
                            array(
                                'id'=>'out_of_stock_behavior',
                                'name' => $this->l('Out of stock behavior')
                            ),
                            array(
                                'id'=>'seo',
                                'name' => $this->l('SEO'),
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'default'=> 'product_reference,short_description,specific_price,tax,out_of_stock_behavior,brand,seo',
                    'desc' => $this->l('Above information are not required. You can disable them to simplify the submission of Seller product form'),
                ),
                array(
                    'name' =>'ETS_MP_SELLER_MESSAGE_DISPLAYED',
                    'type' => 'switch',
                    'label' => $this->l('Display seller messages'),
                    'default' =>1,
                    'desc' => $this->l('Display seller messages on the order details'),
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_DISPLAY_CUSTOMER_EMAIL',
                    'type' => 'switch',
                    'label' => $this->l('Display customer email on front office'),
                    'default' =>0,
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_DISPLAY_CUSTOMER_ADDRESS',
                    'type' => 'switch',
                    'label' => $this->l('Display customer address on front office'),
                    'default' =>1,
                    'tab'=>'seller_settings',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),
                array(
                    'name' =>'ETS_MP_SELLER_ALLOWED_EMBED_CHAT',
                    'type' => 'switch',
                    'label' => $this->l('Allow seller to embed live chat code'),
                    'default' =>1,
                    'desc' => $this->l('Allow seller to embed a live chat code to start chatting with their customers, for example: Zendesk, Zopim chat'),
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_SELLER_ALLOWED_IMPORT_EXPORT_PRODUCTS',
                    'type' => 'switch',
                    'label' => $this->l('Allow seller to import / export their products'),
                    'default' =>1,
                    'desc' => $this->l('Allow seller to import / export products via CSV file'),
                    'tab'=>'seller_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_SHOP_ALIAS',
                    'type' =>'text',
                    'lang'=>true,
                    'default' => 'shops',
                    'label' => $this->l('Shop URL alias'),
                    'tab'=>'seller_seo',
                    'required' => true,
                ),
                array(
                    'name' =>'ETS_MP_URL_SUBFIX',
                    'type' => 'switch',
                    'label' => $this->l('Use URL suffix'),
                    'default' => !Configuration::get('PS_ROUTE_product_rule') || Tools::strpos(Configuration::get('PS_ROUTE_product_rule'),'.html') ? 0:1,
                    'desc' => $this->l('Enable to add ".html" to the end of URLs'),
                    'tab'=>'seller_seo',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_SHOP_META_TITLE',
                    'type' =>'text',
                    'lang'=>true,
                    'label' => $this->l('Shop meta title'),
                    'tab'=>'seller_seo',
                    'validate'=>'isCleanHtml',
                ),
                array(
                    'name' =>'ETS_MP_SHOP_META_DESCRIPTION',
                    'type' =>'textarea',
                    'lang'=>true,
                    'label' => $this->l('Shop meta description'),
                    'tab'=>'seller_seo',
                    'validate'=>'isCleanHtml',
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_DISPLAY_FOLLOWED_SHOP',
                    'label' => $this->l('Display followed shops'),
                    'default'=> 1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'home_page',
                ),
                array(
                    'name' =>'ETS_MP_DISPLAY_NUMBER_SHOP',
                    'type' =>'text',
                    'label' => $this->l('Number of shops to display'),
                    'tab'=>'home_page',
                    'validate'=>'isUnsignedInt',
                    'default' =>12,
                    'col'=>2,
                    'required'=>true,
                    'form_group_class'=>'shop_home'
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_DISPLAY_PRODUCT_FOLLOWED_SHOP',
                    'label' => $this->l('Display products from followed shops'),
                    'default'=> 1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'home_page',
                ),
                array(
                    'name' =>'ETS_MP_DISPLAY_NUMBER_PRODUCT_FOLLOWED_SHOP',
                    'type' =>'text',
                    'label' => $this->l('Number of followed products to display'),
                    'tab'=>'home_page',
                    'validate'=>'isUnsignedInt',
                    'default'=>12,
                    'col'=>2,
                    'required'=>true,
                    'form_group_class'=>'shop_product_home'
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_DISPLAY_PRODUCT_TRENDING_SHOP',
                    'label' => $this->l('Display trending products'),
                    'default'=> 1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'home_page',
                ),
                array(
                    'name' =>'ETS_MP_TRENDING_PERIOD_SHOP',
                    'type' =>'text',
                    'label' => $this->l('Trending period'),
                    'tab'=>'home_page',
                    'validate'=>'isUnsignedInt',
                    'default'=>30,
                    'col'=>2,
                    'required'=>true,
                    'suffix' => $this->l('Day(s)'),
                    'form_group_class'=>'trending_product_home'
                ),
                array(
                    'name' =>'ETS_MP_DISPLAY_NUMBER_PRODUCT_TRENDING_SHOP',
                    'type' =>'text',
                    'label' => $this->l('Number of trending products to display'),
                    'tab'=>'home_page',
                    'validate'=>'isUnsignedInt',
                    'default'=>12,
                    'col'=>2,
                    'required'=>true,
                    'form_group_class'=>'trending_product_home'
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_DISPLAY_TOP_SHOP',
                    'label' => $this->l('Display Top shops'),
                    'default'=> 1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'home_page',
                ),
                array(
                    'name' =>'ETS_MP_DISPLAY_NUMBER_TOP_SHOP',
                    'type' =>'text',
                    'label' => $this->l('Number of shops to display'),
                    'tab'=>'home_page',
                    'validate'=>'isUnsignedInt',
                    'default'=>12,
                    'col'=>2,
                    'required'=>true,
                    'form_group_class'=>'top_shop_home'
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_DISPLAY_OTHER_PRODUCT',
                    'label' => $this->l('Display other product'),
                    'default'=> 1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'product_page',
                ),
                array(
                    'name' =>'ETS_MP_DISPLAY_NUMBER_OTHER_PRODUCT',
                    'type' =>'text',
                    'label' => $this->l('Number of other products to display'),
                    'tab'=>'product_page',
                    'validate'=>'isUnsignedInt',
                    'default'=>12,
                    'col'=>2,
                    'required'=>true,
                    'form_group_class'=>'other_product'
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_DISPLAY_OTHER_SELLER_PRODUCT',
                    'label' => $this->l('Same products from other sellers'),
                    'default'=> 1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'product_page',
                ),
                array(
                    'name' =>'ETS_MP_DISPLAY_NUMBER_OTHER_SELLER_PRODUCT',
                    'type' =>'text',
                    'label' => $this->l('Number of products from other sellers to display'),
                    'tab'=>'product_page',
                    'validate'=>'isUnsignedInt',
                    'default'=>12,
                    'col'=>2,
                    'required'=>true,
                    'form_group_class'=>'other_seller_product'
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_DISPLAY_PRODUCT_SOLD',
                    'label' => $this->l('Display number of products sold'),
                    'default'=> 1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'seller_settings',
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_VACATION_MODE_FOR_SELLER',
                    'label' => $this->l('Enable vacation mode for seller'),
                    'desc' => $this->l('Allow sellers to temporarily close their shop. All products in vacation mode will be disabled and cannot be enabled until seller shop is back to online.'),
                    'default'=> 1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'seller_settings',
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_ENABLE_CAPTCHA',
                    'label' => $this->l('Enable captcha'),
                    'default'=> 0,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'seller_settings',
                ),
                
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_ENABLE_MAP',
                    'label' => $this->l('Enable basic shop map'),
                    'default'=> 0,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'desc' => $this->l('Basic map with shop locations. No API key is required.'),
                    'tab'=>'map_settings',
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_SEARCH_ADDRESS_BY_GOOGLE',
                    'label' => $this->l('Enable advanced shop map'),
                    'default'=> 0,
                    'form_group_class' => 'map',
                    'desc' => $this->l('Enable address suggestions, auto-fill coordinates and shop address searching. Google API key is required'),
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'map_settings',
                ),
                array(
                    'type' => 'text',
                    'name'=>'ETS_MP_GOOGLE_MAP_API',
                    'label' => $this->l('API key'),
                    'tab'=>'map_settings',
                    'col'=>4,
                    'required' => true,
                    'form_group_class' => 'map search',
                    'desc' => sprintf($this->l('Please add a new project in your %s'),
                    Module::getInstanceByName('ets_marketplace')->displayText('Google Console','a','','','https://console.cloud.google.com/','_blank','','','','','','noreferrer noopener')
                    .$this->l('create an API key then enable 3 following APIs for the project:')
                    .Module::getInstanceByName('ets_marketplace')->displayText('','br').'Maps JavaScript API: '
                    .Module::getInstanceByName('ets_marketplace')->displayText('https://developers.google.com/maps/documentation/javascript/get-api-key','a','','','https://developers.google.com/maps/documentation/javascript/get-api-key','_blank','','','','','','noreferrer noopener')
                    .Module::getInstanceByName('ets_marketplace')->displayText('','br').'Places API: '
                    .Module::getInstanceByName('ets_marketplace')->displayText('https://developers.google.com/places/web-service/get-api-key','a','','','https://developers.google.com/places/web-service/get-api-key','_blank','','','','','','noreferrer noopener')
                    .Module::getInstanceByName('ets_marketplace')->displayText('','br').'Geocoding API: '
                    .Module::getInstanceByName('ets_marketplace')->displayText('https://developers.google.com/maps/documentation/geocoding/get-api-key','a','','','https://developers.google.com/maps/documentation/geocoding/get-api-key','_blank','','','','','','noreferrer noopener')),
                ),
                array(
                    'type'=>'file',
                    'name' => 'ETS_MP_GOOGLE_MAP_LOGO',
                    'label' => $this->l('Map marker'),
                    'tab'=>'map_settings',
                    'imageType' => 'map',
                    'display_img' => Configuration::get('ETS_MP_GOOGLE_MAP_LOGO') ? __PS_BASE_URI__.'modules/ets_marketplace/views/img/'.Configuration::get('ETS_MP_GOOGLE_MAP_LOGO'):__PS_BASE_URI__.'modules/ets_marketplace/views/img/logo_map.png',
                    'desc' => sprintf($this->l('Accepted formats: jpg, png, gif, jpeg. Limit: %sMb'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')),
                    'form_group_class' => 'map badge_image',
                    'img_del_link' => Configuration::get('ETS_MP_GOOGLE_MAP_LOGO') ? Context::getContext()->link->getAdminLink('AdminMarketPlaceSettingsGeneral').'&delImage=ETS_MP_GOOGLE_MAP_LOGO&current_tab=map_settings':'',
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'ETS_MP_ENABLE_CAPTCHA_FOR',
                    'label' => $this->l('Enable captcha for'),
                    'default'=> 'shop_contact,shop_report',
                    'form_group_class' => 'captcha',
                    'required2' => true,
                    'values' => array(
                        'query' => array(
                            array(
                                'id'=>'shop_contact',
                                'name' => $this->l('Contact form of seller shop')
                            ),
                            array(
                                'id'=>'shop_report',
                                'name' => $this->l('Report')
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'tab'=>'seller_settings',
                ),
                array(
                    'type' => 'select',
                    'name' => 'ETS_MP_ENABLE_CAPTCHA_TYPE',
                    'label' => $this->l('Captcha type'),
                    'default'=> 'shop_contact,shop_report',
                    'form_group_class' => 'captcha',
                    'options' => array(
            			 'query' => array(
                            array(
                                'id'=>'google_v2',
                                'name' => $this->l('Google reCAPTCHA v2')
                            ),
                            array(
                                'id'=>'google_v3',
                                'name' => $this->l('Google reCAPTCHA v3')
                            )
                         ),                             
                         'id' => 'id',
            			 'name' => 'name'  
                    ),    
                    'tab'=>'seller_settings',
                ),
                array(
                    'type'=>'text',
                    'name'=>'ETS_MP_ENABLE_CAPTCHA_SITE_KEY2',
                    'label' => $this->l('Site key'),
                    'form_group_class' => 'captcha v2',
                    'required2' => true,
                    'tab'=>'seller_settings',
                ),
                array(
                    'type'=>'text',
                    'name'=>'ETS_MP_ENABLE_CAPTCHA_SECRET_KEY2',
                    'label' => $this->l('Secret key'),
                    'form_group_class' => 'captcha v2',
                    'required2' => true,
                    'tab'=>'seller_settings',
                ),
                array(
                    'type'=>'text',
                    'name'=>'ETS_MP_ENABLE_CAPTCHA_SITE_KEY3',
                    'label' => $this->l('Site key'),
                    'form_group_class' => 'captcha v3',
                    'required2' => true,
                    'tab'=>'seller_settings',
                ),
                array(
                    'type'=>'text',
                    'name'=>'ETS_MP_ENABLE_CAPTCHA_SECRET_KEY3',
                    'label' => $this->l('Secret key'),
                    'form_group_class' => 'captcha v3',
                    'required2' => true,
                    'tab'=>'seller_settings',
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_NO_CAPTCHA_IS_LOGIN',
                    'label' => $this->l('Do not require registered user'),
                    'default'=> 1,
                    'form_group_class' => 'captcha',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'seller_settings',
                ),
                array(
                    'type' => 'switch',
                    'name' => 'ETS_MP_ENABLE_LOAD_MORE',
                    'label' => $this->l('Infinite scrolling in shop page'),
                    'default'=> 0,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'tab'=>'seller_settings',
                ),
                array(
                    'name' => 'ETS_MP_COMMISSION_PENDING_WHEN',
                    'label' => $this->l('Commission will be created with "Pending" status if order status is'),
                    'type'=> 'checkbox',
                    'tab'=>'commission_status',
                    'form_group_class' => 'commission_status',
                    'values' => array(
                        'query' => OrderState::getOrderStates(Context::getContext()->language->id),
                        'id' => 'id_order_state',
                        'name' => 'name',
                    ),
                    'default' => $this->_getOrderStateDefault('pending'),
                ),
                array(
                    'name' => 'ETS_MP_COMMISSION_APPROVED_WHEN',
                    'label' => $this->l('Commission will be created with "Approved" status if order status is'),
                    'type'=> 'checkbox',
                    'tab'=>'commission_status',
                    'form_group_class' => 'commission_status',
                    'values' => array(
                        'query' => OrderState::getOrderStates(Context::getContext()->language->id),
                        'id' => 'id_order_state',
                        'name' => 'name',
                    ),
                    'default' => $this->_getOrderStateDefault('approved'),
                   
                ),
                array(
                    'name'=>'ETS_MP_VALIATE_COMMISSION_IN_DAYS',
                    'type'=>'text',
                    'label' => $this->l('Only validate commission if order has been changed to statuses above for'),
                    'suffix' => $this->l('Days'),
                    'desc' => $this->l('Commission status will remain "Pending" status until the required days exceeded. Leave blank to validate commission immediately when order status is changed to one of the statuses above'),
                    'tab'=>'commission_status',
                    'form_group_class' => 'commission_status',
                ),
                array(
                    'name' => 'ETS_MP_COMMISSION_CANCELED_WHEN',
                    'label' => $this->l('Cancel commission if order status is'),
                    'type'=> 'checkbox',
                    'tab'=>'commission_status',
                    'form_group_class' => 'commission_status',
                    'values' => array(
                        'query' => OrderState::getOrderStates(Context::getContext()->language->id),
                        'id' => 'id_order_state',
                        'name' => 'name',
                    ),
                    'default' => $this->_getOrderStateDefault('cancel'),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Allow seller to update order status'),
                    'name' => 'ETS_MP_SELLER_CAN_CHANGE_ORDER_STATUS',
                    'default'=>1,
                    'tab'=>'commission_status',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'type' =>'checkbox',
                    'label' => $this->l('Select order status which sellers can update'),
                    'name' => 'ETS_MP_SELLER_ALLOWED_STATUSES',
                    'default'=>'1,3,10,13',
                    'desc' => $this->l('Sellers can change the status of any order which contain their products'),
                    'tab'=>'commission_status',
                    'required' => true,
                    'values' => array(
                        'query'=> $status,
                        'id' => 'id_order_state',
                        'name' => 'name',
                    ),
                    'form_group_class' => 'ets_mp_allowed_statuses'
                ),
                array(
                    'name'=>'ETS_MP_EMAIL_ADMIN_NOTIFICATION',
                    'type' => 'text',
                    'tab'=> 'email_settings',
                    'label' => $this->l('Email addresses to receive notifications'),
                    'desc' => $this->l('Notifcation messages ("New commission has been created","Commission has been validated","New withdrawal request", etc.) will be sent to these emails. Enter email addresses separated by a comma (",") if you want to send notification messages to more than 1 email'),
                    'default' => Configuration::get('PS_SHOP_EMAIL'),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_ADMIN_APPLICATION_REQUEST',
                    'type' => 'switch',
                    'label' => $this->l('When sellers submits an application to join marketplace'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_ADMIN_SHOP_CREATED',
                    'type' => 'switch',
                    'label' => $this->l('When seller creates shop'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_ADMIN_NEW_PRODUCT_UPLOADED',
                    'type' => 'switch',
                    'label' => $this->l('When new product is uploaded by seller'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_ADMIN_PRODUCT_UPDATED',
                    'type' => 'switch',
                    'label' => $this->l('When seller update product'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),    
                array(
                    'name' =>'ETS_MP_EMAIL_ADMIN_COMMISSION_CREATED',
                    'type' => 'switch',
                    'label' => $this->l('When a commission is created for seller'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_ADMIN_COMMISSION_VALIDATED_OR_CANCELED',
                    'type' => 'switch',
                    'label' => $this->l('When a commission is validated or canceled'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ), 
                array(
                    'name' =>'ETS_MP_EMAIL_ADMIN_WITHDRAWAL_CREATED',
                    'type' => 'switch',
                    'label' => $this->l('When a withdrawal is created'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_ADMIN_CONFIRMED_PAYMENT',
                    'type' => 'switch',
                    'label' => $this->l('When seller confirmed payment'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_ADMIN_REPORT',
                    'type' => 'switch',
                    'label' => $this->l('When a shop or product is reported as abused'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_APPLICATION_APPROVED_OR_DECLINED',
                    'type' => 'switch',
                    'label' => $this->l('When seller application is approved or declined'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_BILLING_CREATED',
                    'type' => 'switch',
                    'label' => $this->l('When billing is created for seller'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_SHOP_ACTIVED_OR_DECLINED',
                    'type' => 'switch',
                    'label' => $this->l('When seller shop is activate or declined'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_GOING_TOBE_EXPIRED',
                    'type' => 'switch',
                    'label' => $this->l('When their seller account is going to be expired'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                    'desc' => $this->l('Send email when seller account is going to be expired before').' '.Configuration::get('ETS_MP_MESSAGE_EXPIRE_BEFORE_DAY'). ' '.$this->l('day(s)'),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_EXPIRED',
                    'type' => 'switch',
                    'label' => $this->l('When their seller account is expired'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_DISABLED',
                    'type' => 'switch',
                    'label' => $this->l('When seller account is disabled'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_PRODUCT_APPROVED_OR_DECLINED',
                    'type' => 'switch',
                    'label' => $this->l('When seller product is approved or declined'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),  
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_PRODUCT_UPDATE_APPROVED_OR_DECLINED',
                    'type' => 'switch',
                    'label' => $this->l('When seller product update is approved or declined'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),  
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_PRODUCT_PURCHASED',
                    'type' => 'switch',
                    'label' => $this->l('When seller products are purchased'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),        
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_COMMISSION_CREATED',
                    'type' => 'switch',
                    'label' => $this->l('When commission is created for seller'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_COMMISSION_VALIDATED_OR_CANCELED',
                    'type' => 'switch',
                    'label' => $this->l('When commission is validated or canceled'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_WITHDRAWAL_APPROVED',
                    'type' => 'switch',
                    'label' => $this->l('When seller withdrawal is approved or declined'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                
                array(
                    'name' =>'ETS_MP_EMAIL_NEW_CONTACT',
                    'type' => 'switch',
                    'label' => $this->l('Send notification email to seller/customer when customer/seller contacts'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_REPORT',
                    'type' => 'switch',
                    'label' => $this->l('When a shop or product is reported as abused'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' =>'ETS_MP_EMAIL_SELLER_UPGRADED_GROUP',
                    'type' => 'switch',
                    'label' => $this->l('When shop group is upgraded'),
                    'default' =>1,
                    'tab'=> 'email_settings',
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			),
                ),
                array(
                    'name' => 'ETS_MP_MESSAGE_INVITE',
                    'type' => 'textarea',
                    'validate'=>'isCleanHtml',
                    'lang'=> true,
                    'tab' => 'message',
                    'label' => $this->l('Invite customers to join marketplace'),
                    'desc' => $this->l('Display on “My account/Seller account" area to introduce/invite customers to join marketplace. Available tags: [highlight][fee_amount][end_highlight]'),
                    'default' => $this->l('Your seller profile has not been enabled yet. In order to join our marketplace to sell your products and get commission, please submit an application with required information.'),
                ),
                array(
                    'name' => 'ETS_MP_MESSAGE_SUBMITTED',
                    'type' => 'textarea',
                    'validate'=>'isCleanHtml',
                    'lang'=> true,
                    'tab' => 'message',
                    'label' => $this->l('Message when application is submitted'),
                    'default' => $this->l('Your application has been submitted successfully. Our team are reviewing the application, and we will get back to you as soon as possible.'),
                ),
                array(
                    'name' => 'ETS_MP_MESSAGE_APPLICATION_ACCEPTED',
                    'type' => 'textarea',
                    'lang'=> true,
                    'tab' => 'message',
                    'validate'=>'isCleanHtml',
                    'label' => $this->l('Message when application is accepted'),
                    'default' => $this->l('Congratulations! Your application is accepted. You can create your shop now.'),
                ),
                array(
                    'name' => 'ETS_MP_MESSAGE_APPLICATION_DECLINED',
                    'type' => 'textarea',
                    'lang'=> true,
                    'tab' => 'message',
                    'validate'=>'isCleanHtml',
                    'label' => $this->l('Message when application is declined'),
                    'desc' => $this->l('Available tags: [highlight][application_declined_reason][end_highlight]'),
                    'default' => $this->l('Sorry! Your application is declined.
                    Reason: [application_declined_reason]'),
                ),
                array(
                    'name' => 'ETS_MP_MESSAGE_CREATED_SHOP_NO_FEE',
                    'type' => 'textarea',
                    'lang'=> true,
                    'tab' => 'message',
                    'validate'=>'isCleanHtml',
                    'label' => $this->l('Message when seller create the shop (No fee)'),
                    'default' => $this->l('Thanks for creating your shop. Our team are reviewing it. We will get back to you soon'),
                ),
                array(
                    'name' => 'ETS_MP_MESSAGE_CREATED_SHOP_FEE_REQUIRED',
                    'type' => 'textarea',
                    'validate'=>'isCleanHtml',
                    'lang'=> true,
                    'tab' => 'message',
                    'label' => $this->l('Message when seller create the shop (Fee require)'),
                    'desc' => $this->l('Available tags: [highlight][fee_amount][end_highlight], [highlight][payment_information_manager][end_highlight], [highlight][manager_email][end_highlight], [highlight][manager_phone][end_highlight]'),
                    'default' => $this->l('Thanks for creating your shop. Please send the fee [fee_amount] right now to activate your shop and click on the button "I have just sent the fee" after making payment. [payment_information_manager]'),
                ),
                array(
                    'name' => 'ETS_MP_MESSAGE_CONFIRMED_PAYMENT',
                    'type' => 'textarea',
                    'validate'=>'isCleanHtml',
                    'lang'=> true,
                    'tab' => 'message',
                    'label' => $this->l('Message when seller confirmed the payment'),
                    'default' => $this->l('Thanks for confirming that you have just sent the fee, we will check it and get back to you as soon as possible'),
                ),
                array(
                    'name' => 'ETS_MP_MESSAGE_SHOP_ACTIVED',
                    'type' => 'textarea',
                    'validate'=>'isCleanHtml',
                    'lang'=> true,
                    'tab' => 'message',
                    'label' => $this->l('Message when created shop is accepted'),
                    'default' => $this->l('Congratulations! Your shop is now activate. You can upload products and start selling them'),
                ),
                array(
                    'name' => 'ETS_MP_MESSAGE_SHOP_DECLINED',
                    'type' => 'textarea',
                    'validate'=>'isCleanHtml',
                    'lang'=> true,
                    'tab' => 'message',
                    'label' => $this->l('Message when created shop is declined'),
                    'desc' => $this->l('Available tags: [highlight][shop_declined_reason][end_highlight]'),
                    'default' => $this->l('Sorry! Your created shop is declined.
Reason: [shop_declined_reason]'),
                ),
                array(
                    'name' => 'ETS_MP_MESSAGE_SELLER_GOING_TOBE_EXPIRED',
                    'type' => 'textarea',
                    'validate'=>'isCleanHtml',
                    'label' => $this->l('Message when seller account is going to be expired'),
                    'lang'=> true,
                    'tab' => 'message',
                    'desc' => $this->l('Display this message until when seller renews the account or until the expired date is reached. Available tags: [highlight][remaining_day][end_highlight]'),
                    'default' => $this->l('Your seller account will be expired after [remaining_day] day. Please send the fee to renew the account'),
                ),
                array(
                    'name' => 'ETS_MP_MESSAGE_SELLER_IS_EXPIRED',
                    'type' => 'textarea',
                    'validate'=>'isCleanHtml',
                    'label' => $this->l('Message when seller account is expired'),
                    'lang'=> true,
                    'tab' => 'message',
                    'default' => $this->l('Your seller account is expired. If you want to continue using the seller account, please contact us via our email: [store_email]'),
                    'desc' => $this->l('Available tags: [highlight][store_email][end_highlight]'),
                ),
                array(
                    'name' => 'ETS_MP_MESSAGE_SELLER_IS_DISABLED',
                    'type' => 'textarea',
                    'validate'=>'isCleanHtml',
                    'label' => $this->l('Message when seller account is disabled'),
                    'lang'=> true,
                    'tab' => 'message',
                    'default' => $this->l('Your seller account is disabled. If you want to continue using the seller account, please contact us via our email: [store_email]'),
                    'desc' => $this->l('Available tags: [highlight][store_email][end_highlight]'),
                ),
            );
        }
    }
    public function _getOrderStateDefault($type='pending')
    {
        $orderStates = OrderState::getOrderStates(Context::getContext()->language->id);
        $defaults= array();
        if($type=='pending')
            $templates = array('cheque','bankwire','cashondelivery','outofstock','preparation');
        elseif($type=='approved')
            $templates = array('payment','payment','');
        elseif($type=='cancel')
            $templates = array('order_canceled','refund','payment_error');
        if($orderStates)
        {
            foreach($orderStates as $orderState)
            {
                if(in_array($orderState['template'],$templates))
                    $defaults[] = $orderState['id_order_state'];
            }
        }
        if($defaults)
            return implode(',',$defaults);
        else
            return '';
    }

    public function processDuplicate($id_product,&$errors,$seller=null)
    {
        /** @var Ets_mp_seller $seller */
        if(Validate::isLoadedObject($seller) && get_class($seller)=='Ets_mp_seller')
        {
            $number_product_upload =$seller->getNumberProductUpload();
            $total_products = (int)$seller->getProducts('',0,0,'',true);
            if($number_product_upload && $number_product_upload <= $total_products)
            {
                $errors[] = sprintf($this->l('You do not have permission to add a new product. You are allowed to add %d products maximum.'),$number_product_upload);
                return false;
            }
        }
        if (($product = new Product((int) $id_product)) &&  Validate::isLoadedObject($product) && !$errors) {
            $id_product_old = $product->id;
            if (empty($product->price)) {
                $product_price = new Product($id_product_old, false, null, Context::getContext()->shop->id);
                $product->price = $product_price->price;
            }
            unset(
                $product->id,
                $product->id_product
            );
            $languages = Language::getLanguages(false);
            foreach($languages as $language)
            {
                $product->name[$language['id_lang']] = $this->l('Copy of').' '.$product->name[$language['id_lang']];
                
            }    
            $product->indexed = 0;
            if($seller)
            {
                if($seller->auto_enabled_product=='no')
                {
                    $product->active=0;
                    $approved=0;
                }
                elseif(Configuration::get('ETS_MP_SELLER_PRODUCT_APPROVE_REQUIRED'))
                {
                    $product->active=0;
                    $approved=0;
                }
            }
            if ($product->add()
            && ($seller ? Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_seller_product`(id_customer,id_product,approved,active) VALUES("'.(int)$seller->id_customer.'","'.(int)$product->id.'","'.(isset($approved) ? (int)$approved :($product->active ? 1 :0)).'","'.(int)$product->active.'")') : true)
            && Category::duplicateProductCategories($id_product_old, $product->id)
            && Product::duplicateSuppliers($id_product_old, $product->id)
            && ($combination_images = Product::duplicateAttributes($id_product_old, $product->id)) !== false
            && GroupReduction::duplicateReduction($id_product_old, $product->id)
            && Product::duplicateAccessories($id_product_old, $product->id)
            && Product::duplicateFeatures($id_product_old, $product->id)
            && Product::duplicateSpecificPrices($id_product_old, $product->id)
            && Pack::duplicate($id_product_old, $product->id)
            && Product::duplicateCustomizationFields($id_product_old, $product->id)
            && Product::duplicateTags($id_product_old, $product->id)
            && Product::duplicateDownload($id_product_old, $product->id)) {
                if ($product->hasAttributes()) {
                    Product::updateDefaultAttribute($product->id);
                }
                if ( !Image::duplicateProductImages($id_product_old, $product->id, $combination_images)) {
                    $errors[] = $this->l('An error occurred while copying the image.');
                } else {
                    Hook::exec('actionProductAdd', array('id_product_old' => $id_product_old, 'id_product' => (int) $product->id, 'product' => $product));
                    if (in_array($product->visibility, array('both', 'search')) && Configuration::get('PS_SEARCH_INDEXATION')) {
                        Search::indexation(false, $product->id);
                    }
                    Context::getContext()->cookie->success_message = $this->l('Product successfully duplicated.');
                    foreach($languages as $language)
                    {
                        Db::getInstance()->execute('update `'._DB_PREFIX_.'product_lang`  set link_rewrite = concat(link_rewrite,"-",'.(int)$product->id.') WHERE id_product='.(int)$product->id.' AND id_lang='.(int)$language['id_lang']);
                    }
                    return $product->id;
                }
            } else {
                $errors[] = $this->l('An error occurred while creating an object.');
            }
        }elseif(!$errors)
            $errors[] = $this->l('Product is not valid');
    }
    public function getFieldChangeProduct()
    {
        return array(
            array(
                'id' => 'price',
                'name' => $this->l('Price'),
            ),
            array(
                'id'=> 'name',
                'name'=>$this->l('Name'),
                'lang'=>true,
            ),
            array(
                'id'=>'reference',
                'name' => $this->l('Reference')
            ),
            array(
                'id'=>'image',
                'name'=> $this->l('Image'),
            ),
            array(
                'id'=>'description_short',
                'name' => $this->l('Summary'),
                'lang' => true,
            ),
            array(
                'id'=> 'description',
                'name'=>$this->l('Description'),
                'lang' => true,
            ),
            array(
                'id'=> 'meta_title',
                'name'=>$this->l('Meta title'),
                'lang' => true,
            ),
            array(
                'id'=> 'meta_description',
                'name'=>$this->l('Meta description'),
                'lang' => true,
            ),
            array(
                'id'=> 'link_rewrite',
                'name'=>$this->l('Friendly URL'),
                'lang' => true,
            ),
            array(
                'id' => 'width',
                'name' => 'Width',
            ),
            array(
                'id' => 'height',
                'name' => 'Height',
            ),
            array(
                'id' => 'depth',
                'name' => 'Depth',
            ),
            array(
                'id' => 'weight',
                'name' => 'Weight',
            ),
            array(
                'id' => 'delivery_in_stock',
                'name' => $this->l('Delivery time of in-stock products'),
                'lang' => true,
            ),
            array(
                'id' => 'delivery_out_stock',
                'name' => $this->l('Delivery time of out-of-stock products with allowed orders'),
                'lang' => true,
            ),
            array(
                'id' => 'isbn',
                'name' => $this->l('ISBN'),
            ),
            array(
                'id' => 'ean13',
                'name' => $this->l('EAN-13 or JAN barcode'),
            ),
            array(
                'id' => 'upc',
                'name' => $this->l('UPC barcode '),
            ),
        );
    }
    public static function checkUrlMeta($url,$controller)
    {
        if(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'meta_lang` WHERE url_rewrite ="'.pSQL($url).'"') || self::getIDMeta($controller))
            return true;
        return false;
    }
    public static function getIDMeta($controller)
    {
        return Db::getInstance()->getValue('SELECT id_meta FROM `'._DB_PREFIX_.'meta` WHERE page ="module-ets_marketplace-'.pSQL($controller).'"');
    }
    public static function getCategoriesTree($id_root=0)
    {
        if(!$id_root)
        {
            $id_root = Db::getInstance()->getValue('SELECT c.id_category FROM `'._DB_PREFIX_.'category` c
            INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category = cs.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
            WHERE c.active=1 AND is_root_category=1');
        }
        $sql ='SELECT * FROM `'._DB_PREFIX_.'category` c
        INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category = cs.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.id_category=cl.id_category AND cl.id_lang ="'.(int)Context::getContext()->language->id.'" AND cl.id_shop="'.(int)Context::getContext()->shop->id.'")
        WHERE c.id_category = "'.(int)$id_root.'" AND c.active=1 GROUP BY c.id_category';
        $tree=array();
        if($category = Db::getInstance()->getRow($sql))
        {
            $cat = array(
                'name' => $category['name'],
                'id_category' => $category['id_category']
            );
            $temp = array();
            $Childrens = self::getChildrenCategories($category['id_category']);
            if($Childrens)
            {
                foreach($Childrens as $children)
                {
                    $arg = self::getCategoriesTree($children['id_category']);
                    if($arg && isset($arg['0']))
                    {
                        $temp[] = $arg[0];
                    }
                }
            }
            $cat['children'] = $temp;
            $tree[] = $cat;
        }
        return $tree;
    }
    public static function getChildrenCategories($id_parent)
    {
        $sql = 'SELECT c.id_category,cl.name FROM `'._DB_PREFIX_.'category` c
        INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category = cs.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.id_category = cl.id_category AND cl.id_lang="'.(int)Context::getContext()->language->id.'" AND cl.id_shop="'.(int)Context::getContext()->shop->id.'")
        WHERE c.id_parent="'.(int)$id_parent.'" AND c.active=1
        ';
        return Db::getInstance()->executeS($sql);
    }
    public static function getRateCategoriesTree($id_root=0,$id_seller=0,$id_group=0,$rate_categories=array())
    {
        if(!$id_root)
            $id_root = (int)Db::getInstance()->getValue('SELECT c.id_category FROM `'._DB_PREFIX_.'category` c
            INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category = cs.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
            WHERE c.active=1 AND is_root_category=1');
        if($id_root)
        {
            if($id_seller)
                $sql_leftJoin  = ' LEFT JOIN `'._DB_PREFIX_.'ets_mp_category_commission_seller` commission ON (commission.id_category=c.id_category AND commission.id_seller="'.(int)$id_seller.'")';
            elseif($id_group)
                $sql_leftJoin  = ' LEFT JOIN `'._DB_PREFIX_.'ets_mp_category_commission_group` commission ON (commission.id_category=c.id_category AND commission.id_group="'.(int)$id_group.'")';
            else
                $sql_leftJoin  = ' LEFT JOIN `'._DB_PREFIX_.'ets_mp_category_commission` commission ON (commission.id_category=c.id_category)';
            $sql ='SELECT c.id_category,cl.name,commission.commission_rate FROM `'._DB_PREFIX_.'category` c
            INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category = cs.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.id_category=cl.id_category AND cl.id_lang ="'.(int)Context::getContext()->language->id.'" AND cl.id_shop="'.(int)Context::getContext()->shop->id.'")
            '.(string)$sql_leftJoin.'
            WHERE c.id_category = "'.(int)$id_root.'" AND c.active=1 GROUP BY c.id_category';
            $tree=array();
            if($category = Db::getInstance()->getRow($sql))
            {
                $cat = array(
                    'name' => $category['name'],
                    'id_category' => $category['id_category'],
                    'commission_rate' => isset($rate_categories[$category['id_category']]) ? $rate_categories[$category['id_category']]: ((int)$category['commission_rate']==$category['commission_rate'] && $category['commission_rate'] ? (int)$category['commission_rate'] : $category['commission_rate']),
                );
                $temp = array();
                $Childrens = self::getChildrenRateCategories($category['id_category'],$id_seller,$id_group);
                if($Childrens)
                {
                    foreach($Childrens as $children)
                    {
                        $arg = self::getRateCategoriesTree($children['id_category'],$id_seller,$id_group,$rate_categories);
                        if($arg && isset($arg['0']))
                        {
                            $temp[] = $arg[0];
                        }
                    }
                }
                $cat['children'] = $temp;
                $tree[] = $cat;
            }
            return $tree;
        }
        return array();
    }
    public static function getChildrenRateCategories($id_parent,$id_seller=0,$id_group=0)
    {
        if($id_seller)
            $sql_leftJoin  = ' LEFT JOIN `'._DB_PREFIX_.'ets_mp_category_commission_seller` commission ON (commission.id_category=c.id_category AND commission.id_seller="'.(int)$id_seller.'")';
        elseif($id_group)
            $sql_leftJoin  = ' LEFT JOIN `'._DB_PREFIX_.'ets_mp_category_commission_group` commission ON (commission.id_category=c.id_category AND commission.id_group="'.(int)$id_group.'")';
        else
            $sql_leftJoin  = ' LEFT JOIN `'._DB_PREFIX_.'ets_mp_category_commission` commission ON (commission.id_category=c.id_category)';
        $sql = 'SELECT c.id_category,cl.name,commission.commission_rate FROM `'._DB_PREFIX_.'category` c
            INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category = cs.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.id_category = cl.id_category AND cl.id_lang="'.(int)Context::getContext()->language->id.'" AND cl.id_shop="'.(int)Context::getContext()->shop->id.'")
            '.(string)$sql_leftJoin.'
            WHERE c.id_parent="'.(int)$id_parent.'" AND c.active=1
        ';
        return Db::getInstance()->executeS($sql);
    }
    public static function duplicateRowsFromDefaultShopLang($tableName, $shopId,$identifier)
    {
        $shopDefaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $fields = array();
        $shop_field_exists = $primary_key_exists = false;
        $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . pSQL($tableName) . '`');
        foreach ($columns as $column) {
            $fields[] = '`' . $column['Field'] . '`';
            if ($column['Field'] == 'id_shop') {
                $shop_field_exists = true;
            }
            if ($column['Field'] == $identifier) {
                $primary_key_exists = true;
            }
        }
        if (!$primary_key_exists) {
            return true;
        }
        $sql = 'INSERT IGNORE INTO `' . bqSQL($tableName) . '` (' . implode(',', array_map('bqSQL',$fields)) . ') (SELECT ';
        // For each column, copy data from default language
        reset($columns);
        $selectQueries = array();
        foreach ($columns as $column) {
            if ($identifier != $column['Field'] && $column['Field'] != 'id_lang') {
                $selectQueries[] = '(
                    SELECT `' . bqSQL($column['Field']) . '`
                    FROM `' . bqSQL($tableName) . '` tl
                    WHERE tl.`id_lang` = ' . (int) $shopDefaultLangId . '
                    ' . ($shop_field_exists ? ' AND tl.`id_shop` = ' . (int) $shopId : '') . '
                    AND tl.`' . bqSQL($identifier) . '` = `' . bqSQL(str_replace('_lang', '', $tableName)) . '`.`' . bqSQL($identifier) . '`
                )';
            } else {
                $selectQueries[] = '`' . bqSQL($column['Field']) . '`';
            }
        }
        $sql .= implode(',', $selectQueries);
        $sql .= ' FROM `' . _DB_PREFIX_ . 'lang` CROSS JOIN `' . bqSQL(str_replace('_lang', '', $tableName)) . '` ';

        // prevent insert with where initial data exists
        $sql .= ' WHERE `' . bqSQL($identifier) . '` IN (SELECT `' . bqSQL($identifier) . '` FROM `' . bqSQL($tableName) . '`) )';
        return Db::getInstance()->execute($sql);
    }
    public static function getColumns($table)
    {
        return Db::getInstance()->ExecuteS('DESCRIBE '._DB_PREFIX_.pSQL($table));
    }
    public static function searchCustomerByQuery($query)
    {
        $sql  ='SELECT * FROM `'._DB_PREFIX_.'customer` WHERE id_shop="'.(int)Context::getContext()->shop->id.'" AND (id_customer="'.(int)$query.'" OR email LIKE "%'.pSQL($query).'%" OR CONCAT(firstname," ",lastname) LIKE "%'.pSQL($query).'%")';
        return Db::getInstance()->executeS($sql);
    }
}