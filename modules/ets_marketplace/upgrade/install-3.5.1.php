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
function upgrade_module_3_5_1()
{

    try{
        if(file_exists(dirname(__FILE__).'/../cronjob_log.txt'))
        {
            file_put_contents(_PS_ETS_MARKETPLACE_LOG_DIR_.'ets_merketplace.log', Tools::file_get_contents(dirname(__FILE__).'/../cronjob_log.txt'));
            @unlink(dirname(__FILE__).'/../cronjob_log.txt');
        }
        if(file_exists(dirname(__FILE__).'/../cronjob_time.txt')) {
            Configuration::updateGlobalValue('ETS_MP_TIME_LOG_CRONJOB',Tools::file_get_contents(dirname(__FILE__).'/../cronjob_time.txt'));
            @unlink(dirname(__FILE__).'/../cronjob_time.txt');
        }
        Ets_mp_defines::createIndexDataBase();
    }
    catch(Exceptions $e)
    {
        if($e)
            return true;
    }
    return true;
}