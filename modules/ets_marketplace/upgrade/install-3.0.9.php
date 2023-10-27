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
function upgrade_module_3_0_9($module)
{
    Configuration::updateValue('ETS_MP_SELLER_DISPLAY_REVIEWS_WAITING',1);
    Configuration::updateValue('ETS_MP_SELLER_APPROVE_REVIEW',0);
    Configuration::updateValue('ETS_MP_SELLER_DELETE_REVIEW',0);
    $module->_installTabs();
    $module->uninstallOverrides();
    $module->installOverrides();
    return true;
}