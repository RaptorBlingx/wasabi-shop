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
function upgrade_module_2_0_4($object)
{
    $sqls = array();
    if(!$object->checkCreatedColumn('ets_mp_seller_commission','use_tax'))
    {
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_mp_seller_commission` ADD `use_tax` INT(11)';
        $sqls[] = 'UPDATE `'._DB_PREFIX_.'ets_mp_seller_commission` SET use_tax="'.(int)(Configuration::get('ETS_MP_COMMISSION_EXCLUDE_TAX')? 0:1).'"';
    }
    if($sqls)
    {
        foreach($sqls as $sql)
            Db::getInstance()->execute($sql);
    }
    return true;
}