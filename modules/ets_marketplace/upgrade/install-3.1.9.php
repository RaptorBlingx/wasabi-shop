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
function upgrade_module_3_1_9($object)
{
    $sqls = array();
    if(!$object->checkCreatedColumn('ets_mp_seller_group','list_categories'))
    {
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_group` ADD `list_categories` text NULL';
    }
    if(!$object->checkCreatedColumn('ets_mp_seller_group','applicable_product_categories'))
    {
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_group` ADD `applicable_product_categories` varchar(100) NULL';
    }
    if($sqls)
    {
        foreach($sqls as $sql)
            Db::getInstance()->execute($sql);
    }
    $object->registerHook('displayOrderDetail');
    Configuration::updateValue('ETS_MP_DISPLAY_TOP_SHOP',1);
    Configuration::updateValue('ETS_MP_DISPLAY_NUMBER_TOP_SHOP',12);
    return true;
}