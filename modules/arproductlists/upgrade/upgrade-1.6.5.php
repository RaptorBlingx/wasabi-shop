<?php
/**
* 2012-2019 Areama
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@areama.net so we can send you a copy immediately.
*
*  @author    Areama <contact@areama.net>
*  @copyright 2019 Areama
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Areama
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__).'/../classes/ArPLInstaller.php';
include_once dirname(__FILE__).'/../classes/ArCategoryViews.php';


function upgrade_module_1_6_5($module)
{
    $installer = new ArPLInstaller($module);
    $installer->installDefaultLists();
    
    $hooks = array(
        'displayLeftColumn'
    );
    
    foreach ($hooks as $hook) {
        $module->registerHook($hook);
    }
    
    return true;
}
