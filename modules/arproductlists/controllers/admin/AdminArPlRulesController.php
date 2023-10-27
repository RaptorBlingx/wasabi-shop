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

include_once dirname(__FILE__).'/../../classes/ArPLProductListRule.php';
include_once dirname(__FILE__).'/../../classes/ArPLProductListRuleGroup.php';
include_once dirname(__FILE__).'/../../classes/ArPLProductListRuleCondition.php';
include_once dirname(__FILE__).'/../../classes/ArPLTools.php';

class AdminArPlRulesController extends ModuleAdminController
{
    public function __construct()
    {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();
        $this->meta_title = $this->l('Product lists');
    }
    
    public function ajaxProcessLoadRules()
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'arproductlist_rule';
        $rules = Db::getInstance()->executeS($sql);
        foreach ($rules as &$rule) {
            $rule['name'] = $rule['name']? $rule['name'] : ('Rule #' . $rule['id_rule']);
        }
        die(ArPLTools::jsonEncode($rules));
    }
    
    public function ajaxProcessReload()
    {
        $id = Tools::getValue('id');
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'arproductlist_rule';
        if ($id) {
            $sql .= ' WHERE id_rule = ' . (int)$id;
        }
        $rules = Db::getInstance()->executeS($sql);
        $allRules = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'arproductlist_rule');
        $manufacturers = Manufacturer::getManufacturers(false, Context::getContext()->language->id);
        $idLang = Context::getContext()->language->id;
        $categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
			SELECT *
			FROM `' . _DB_PREFIX_ . 'category` c
			' . Shop::addSqlAssociation('category', 'c') . '
			LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . '
			WHERE 1 ' . ($idLang ? 'AND `id_lang` = ' . (int) $idLang : '') . '
			' . 'AND `active` = 1' . '
			' . (!$idLang ? 'GROUP BY c.id_category' : '') . '
			' . 'ORDER BY c.`level_depth` ASC, category_shop.`position` ASC'
        );
        $features = Feature::getFeatures(Context::getContext()->language->id);
        $content = '';
        foreach ($rules as &$rule) {
            $groups = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'arproductlist_rule_group WHERE id_rule = ' . (int)$rule['id_rule'] . ' ORDER BY position');
            foreach ($groups as &$group) {
                $conditions = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'arproductlist_rule_condition WHERE id_group = ' . (int)$group['id_group'] . ' ORDER BY position');
                foreach ($conditions as &$condition) {
                    if ($condition['id_feature']) {
                        $condition['feature_values'] = FeatureValue::getFeatureValuesWithLang(Context::getContext()->language->id, $condition['id_feature']);
                    } else {
                        $condition['feature_values'] = array();
                    }
                }
                $group['conditions'] = $conditions;
            }
            $rule['groups'] = $groups;
            $content .= $this->module->render('_partials/_rule.tpl', array(
                'rule' => $rule,
                'manufacturers' => $manufacturers,
                'categories' => $categories,
                'features' => $features,
                'rules' => $allRules
            ));
        }
        
        die(ArPLTools::jsonEncode(array(
            'success' => 1,
            'content' => $content
        )));
    }
    
    public function ajaxProcessSave()
    {
        $rule = Tools::getValue('rule');
        $errors = array();
        if (!isset($rule['groups']) || empty($rule['groups'])) {
            $errors[] = 'Rule should contains at least one group and one condition';
        }
        
        if ($errors) {
            die(ArPLTools::jsonEncode(array(
                'success' => 0,
                'errors' => $errors
            )));
        }
        
        if ($rule['id']) {
            $model = new ArPLProductListRule($rule['id']);
            $model->clearRuleData();
        } else {
            $model = new ArPLProductListRule();
        }
        $model->name = pSQL($rule['name']);
        $model->status = 1;
        $model->rel_rule = (isset($rule['rel_rule']))? (int)$rule['rel_rule'] : 0;
        $model->save();
        
        foreach ($rule['groups'] as $k => $group) {
            $groupModel = new ArPLProductListRuleGroup();
            $groupModel->op = pSQL($group['op']);
            $groupModel->position = $k + 1;
            $groupModel->status = 1;
            $groupModel->id_rule = $model->id;
            $groupModel->save();
            foreach ($group['conditions'] as $kk => $cond) {
                $condModel = new ArPLProductListRuleCondition();
                $condModel->id_group = $groupModel->id;
                $condModel->position = $kk + 1;
                $condModel->op = pSQL($cond['op']);
                $condModel->status = (int)$cond['status'];
                if ($cond['type'] == 'feature') {
                    $condModel->id_feature = (int)$cond['id_feature'];
                    $condModel->id_feature_value = (int)$cond['id_feature_value'];
                } elseif ($cond['type'] == 'category') {
                    $condModel->id_category = (int)$cond['id_category'];
                } elseif ($cond['type'] == 'manufacturer') {
                    $condModel->id_manufacturer = (int)$cond['id_manufacturer'];
                }
                $condModel->save();
            }
        }
        
        die(ArPLTools::jsonEncode(array(
            'success' => 1,
            'id' => $model->id,
            'name' => $model->name? $model->name : ('Rule #' . $model->id)
        )));
    }
    
    public function ajaxProcessRemove()
    {
        $id = Tools::getValue('id');
        $model = new ArPLProductListRule($id);
        $model->clearRuleData();
        $model->delete();
        Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'arproductlist_rule SET rel_rule = 0 WHERE rel_rule = ' . (int)$id);
        
        die(ArPLTools::jsonEncode(array(
            'success' => 1
        )));
    }
    
    public function ajaxProcessLoadFeatures()
    {
        $models = FeatureCore::getFeatures(Context::getContext()->language->id);
        die(ArPLTools::jsonEncode($models));
    }
    
    public function ajaxProcessLoadFeatureValues()
    {
        $idFeature = Tools::getValue('feature');
        $models = FeatureValueCore::getFeatureValuesWithLang(Context::getContext()->language->id, $idFeature);
        die(ArPLTools::jsonEncode($models));
    }
    
    public function ajaxProcessLoadManufacturers()
    {
        $models = ManufacturerCore::getManufacturers(false, Context::getContext()->language->id);
        die(ArPLTools::jsonEncode($models));
    }
    
    public function ajaxProcessLoadCategories()
    {
        $idLang = Context::getContext()->language->id;
        $models = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
			SELECT *
			FROM `' . _DB_PREFIX_ . 'category` c
			' . Shop::addSqlAssociation('category', 'c') . '
			LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . '
			WHERE 1 ' . ($idLang ? 'AND `id_lang` = ' . (int) $idLang : '') . '
			' . 'AND `active` = 1' . '
			' . (!$idLang ? 'GROUP BY c.id_category' : '') . '
			' . 'ORDER BY c.`level_depth` ASC, category_shop.`position` ASC'
        );
        die(ArPLTools::jsonEncode($models));
    }
}
