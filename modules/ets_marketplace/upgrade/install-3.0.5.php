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
function upgrade_module_3_0_5($object)
{
    $sqls = array();
    if(!$object->checkCreatedColumn('ets_mp_seller_group','auto_upgrade'))
    {
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_group` ADD `auto_upgrade` FLOAT(10,2)';
    }
    if(!$object->checkCreatedColumn('ets_mp_seller_lang','banner_url'))
    {
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_lang` ADD `banner_url` text';
    }
    if(!$object->checkCreatedColumn('ets_mp_registration','banner_url'))
    {
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_registration` ADD `banner_url` text';
    }
    if($sqls)
    {
        foreach($sqls as $sql)
            Db::getInstance()->execute($sql);
    }
    $object->_installFieldConfigDefault();
    return true;
}