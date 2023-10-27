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
 * @param Ets_marketplace $object
 * @return bool
 */
function upgrade_module_2_0_3($object)
{
    $sqls = array();
    if(!$object->checkCreatedColumn('ets_mp_seller','user_shipping'))
    {
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller` ADD `user_shipping` INT(1) NULL AFTER `payment_verify`';
        $sqls[] = 'UPDATE `'._DB_PREFIX_.'ets_mp_seller` SET user_shipping=1';
    }
    if(!$object->checkCreatedColumn('ets_mp_seller_contact','name'))
    {
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_contact` ADD `name` VARCHAR(222)';
    }
    if(!$object->checkCreatedColumn('ets_mp_seller_contact','email'))
    {
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_contact` ADD `email` VARCHAR(222)';
    }
    if(!$object->checkCreatedColumn('ets_mp_seller_contact','phone'))
    {
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_contact` ADD `phone` VARCHAR(222)';
    }
    $sqls[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_mp_carrier_seller` ( 
        `id_carrier_reference` INT(11) NOT NULL , 
        `id_customer` INT(11) NOT NULL , 
        PRIMARY KEY (`id_carrier_reference`, `id_customer`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
    if($sqls)
    {
        foreach($sqls as $sql)
            Db::getInstance()->execute($sql);
    }
    $object->uninstallOverrides();
    $object->installOverrides();
    $object->installLinkDefault();
    Configuration::updateValue('ETS_MP_CONTACT_FIELDS_VALIDATE','title,message');
    Configuration::updateValue('ETS_MP_CONTACT_FIELDS','title,message');
    Ets_mp_defines::createIndexDataBase();
    return true;
}