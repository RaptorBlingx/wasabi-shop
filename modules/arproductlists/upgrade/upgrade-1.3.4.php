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

include_once dirname(__FILE__).'/../classes/ArPLProductListRule.php';
include_once dirname(__FILE__).'/../classes/ArPLProductListRuleGroup.php';
include_once dirname(__FILE__).'/../classes/ArPLProductListRuleCondition.php';

function upgrade_module_1_3_4($module)
{
    $tabs = array('AdminArPlRules');
    foreach ($tabs as $tabName) {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $tabName;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        $tab->id_parent = -1;
        $tab->module = $module->name;
        $tab->add();
    }
    
    ArPLProductListRule::installTable();
    ArPLProductListRuleGroup::installTable();
    ArPLProductListRuleCondition::installTable();
    
    $installer = new ArPLInstaller($module);
    $installer->installDefaultLists();
    
    return true;
}
