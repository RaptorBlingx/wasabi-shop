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
*  @author    Areama <support@areama.net>
*  @copyright 2019 Areama
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Areama
*/

include_once dirname(__FILE__).'/ArProductList.php';
include_once dirname(__FILE__).'/ArProductListGroup.php';
include_once dirname(__FILE__).'/ArProductListRel.php';
include_once dirname(__FILE__).'/ArProductListRelCat.php';

include_once dirname(__FILE__).'/ArPLProductListRule.php';
include_once dirname(__FILE__).'/ArPLProductListRuleGroup.php';
include_once dirname(__FILE__).'/ArPLProductListRuleCondition.php';
include_once dirname(__FILE__).'/ArProductViews.php';

class ArPLInstaller
{
    protected $module = null;
    
    protected $tabs = array(
        'AdminArPlGroup',
        'AdminArPlList',
        'AdminArPlRelCat',
        'AdminArPlRules',
    );
    
    protected $hooks = array(
        'displayHeader',
        
        'displayHome',
        'displayHomeAfter',
        'displayHomeTop',
        'arHomePageHook1',
        'arHomePageHook2',
        'arHomePageHook3',
        
        'displayLeftColumn',
        
        'displayHeaderCategory',
        'displayFooterCategory',
        'arCategoryPageHook1',
        'arCategoryPageHook2',
        'arCategoryPageHook3',
        'arCategoryPageHook4',
        'arCategoryPageHook5',
        'arCategoryPageHook6',
        'arCategoryPageHook7',
        'arCategoryPageHook8',
        'arCategoryPageHook9',
        'arCategoryPageHook10',
        
        'displayReassurance',
        'displayProductAdditionalInfo',
        'displayFooterProduct',
        'displayCartModalContent',
        'displayCartModalFooter',
        'arProductPageHook1',
        'arProductPageHook2',
        'arProductPageHook3',
        
        'displayNotFound',
        'ar404PageHook1',
        'ar404PageHook2',
        'ar404PageHook3',
        
        'displayShoppingCartFooter',
        'displayShoppingCart',
        'arCartPageHook1',
        'arCartPageHook2',
        'arCartPageHook3',
        
        'displayOrderConfirmation',
        'displayOrderConfirmation1',
        'displayOrderConfirmation2',
        'arThankYouPageHook1',
        'arThankYouPageHook2'
    );

    public function __construct($module)
    {
        $this->setModule($module);
    }
    
    public function setModule($module)
    {
        $this->module = $module;
    }
    
    public function getModule()
    {
        return $this->module;
    }
    
    public function install()
    {
        Configuration::updateValue('ARPL_INSTALL_TS', time());
        return $this->installHook() && $this->installTabs() && $this->installDB() && $this->installDefaults() && $this->installOverrides();
    }
    
    public function uninstall()
    {
        return $this->uninstallDB() && $this->uninstallDefaults() && $this->unistallTabs();
    }
    
    public function unistallTabs()
    {
        foreach ($this->tabs as $tabName) {
            $id_tab = Tab::getIdFromClassName($tabName);
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        return true;
    }
    
    public function uninstallDB()
    {
        return ArProductList::uninstallTable() &&
                ArProductListGroup::uninstallTable() &&
                ArProductListRel::uninstallTable() &&
                ArProductListRelCat::uninstallTable() &&
                ArPLProductListRule::uninstallTable() &&
                ArPLProductListRuleGroup::uninstallTable() &&
                ArPLProductListRuleCondition::uninstallTable() &&
                ArProductViews::uninstallTable() &&
                ArCategoryViews::uninstallTable();
    }
    
    public function installTabs()
    {
        foreach ($this->tabs as $tabName) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = $tabName;
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = $tabName;
            }
            $tab->id_parent = -1;
            $tab->module = $this->module->name;
            $tab->add();
        }
        return true;
    }
    
    public function installHook()
    {
        $res = true;
        foreach ($this->hooks as $hook) {
            $res = $res && $this->module->registerHook($hook);
        }
        return $res;
    }
    
    public function installDB()
    {
        return ArProductList::installTable() &&
                ArProductListGroup::installTable() &&
                ArProductListRel::installTable() &&
                ArProductListRelCat::installTable() &&
                ArPLProductListRule::installTable() &&
                ArPLProductListRuleGroup::installTable() &&
                ArPLProductListRuleCondition::installTable() &&
                ArProductViews::installTable() &&
                ArCategoryViews::installTable();
    }
    
    public function uninstallDefaults()
    {
        $this->module->initConfig(false);
        foreach ($this->module->getForms() as $model) {
            $model->clearConfig();
        }
        return true;
    }
    
    public function installDefaults()
    {
        $this->module->initConfig(false);
        foreach ($this->module->getForms() as $model) {
            $model->loadDefaults();
            $model->saveToConfig(false);
        }
        $this->installDefaultLists();
        $this->module->generateCSS();
        return true;
    }
    
    public function installDefaultLists()
    {
        $lists = ArProductList::getDefaultLists();
        $langs = Language::getLanguages();
        foreach ($lists as $list) {
            if (!ArProductList::isListExists($list['class'])) {
                $title = array();
                foreach ($langs as $lang) {
                    $title[$lang['id_lang']] = $list['title'];
                }
                $model = new ArProductList();
                $model->product_context = $list['product_context'];
                $model->category_context = $list['category_context'];
                $model->class = $list['class'];
                $model->data = $list['data'];
                $model->status = $list['status'];
                $model->position = $list['position'];
                $model->title = $title;
                $model->save();
            }
        }
    }
    
    public function installOverrides()
    {
        return true;
    }
}
