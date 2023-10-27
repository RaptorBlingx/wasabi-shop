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
function upgrade_module_3_5_2($module)
{
    if(!$module->checkCreatedColumn('ets_mp_withdrawal_field','file_name'))
    {
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_mp_withdrawal_field` ADD `file_name` VARCHAR(42) NULL');
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_mp_withdrawal_field` set file_name = value');
    }
    $module->copy_directory(_PS_UPLOAD_DIR_.'ets_marketplace/', _PS_DOWNLOAD_DIR_.'ets_marketplace/');
    $module->rrmdir(_PS_UPLOAD_DIR_.'ets_marketplace/');
    return true;
}