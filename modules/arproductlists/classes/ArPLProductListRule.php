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

class ArPLProductListRule extends ObjectModel
{
    const TABLE_NAME = 'arproductlist_rule';
    
    public $id;
    public $name;
    public $status;
    public $rel_rule;
    
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_rule',
        'multilang' => false,
        'fields' => array(
            'rel_rule'  =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'status'    =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'name'      =>      array('type' => self::TYPE_STRING, 'validate' => 'isString')
        ),
    );
    
    public static function installTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . self::getTableName() . "` (
            `id_rule` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NULL DEFAULT NULL,
            `rel_rule` INT(10) UNSIGNED NULL DEFAULT NULL,
            `status` TINYINT(3) UNSIGNED NULL DEFAULT NULL,
            PRIMARY KEY (`id_rule`),
            INDEX `status` (`status`)
        )
        COLLATE='utf8_general_ci'";
        
        return Db::getInstance()->execute($sql);
    }
    
    public static function uninstallTable()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . self::getTableName() . '`');
    }
    
    public static function getTableName($withPrefix = true)
    {
        if ($withPrefix) {
            return (_DB_PREFIX_ . self::TABLE_NAME);
        }
        return self::TABLE_NAME;
    }
    
    public function getGroups()
    {
        return Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'arproductlist_rule_group WHERE id_rule = ' . (int)$this->id);
    }
    
    public function clearRuleData()
    {
        $groups = $this->getGroups();
        foreach ($groups as $group) {
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'arproductlist_rule_condition WHERE id_group = ' . (int)$group['id_group']);
        }
        Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'arproductlist_rule_group WHERE id_rule = ' . (int)$this->id);
    }
    
    public static function findRules($product)
    {
        $features = $product->getFeatures();
        $id_manufacturer = $product->id_manufacturer;
        $id_category = $product->id_category_default;
        
        $where = array();
        $whereFeatures = array();
        foreach ($features as $feature) {
            $whereFeatures[] = "(t.id_feature = {$feature['id_feature']} AND t.id_feature_value = {$feature['id_feature_value']})";
        }
        if ($whereFeatures) {
            $where[] = '(' . implode(' OR ', $whereFeatures) . ')';
        }
        if ($id_category) {
            $where[] = 't.id_category = ' . (int)$id_category;
        }
        if ($id_manufacturer) {
            $where[] = 't.id_manufacturer = ' . (int)$id_manufacturer;
        }
        $sql = 'SELECT DISTINCT(r.id_rule), r.rel_rule FROM ' . _DB_PREFIX_ . 'arproductlist_rule_condition t 
            LEFT JOIN ' . _DB_PREFIX_ . 'arproductlist_rule_group rg ON t.id_group = rg.id_group
            LEFT JOIN ' . _DB_PREFIX_ . 'arproductlist_rule r ON r.id_rule = rg.id_rule WHERE ' . implode(' OR ', $where) . ' AND t.status = 1';
        
        $rules = Db::getInstance()->executeS($sql);
        
        $relRules = array();
        
        foreach ($rules as $rule) {
            $w = self::buildRuleQuery($rule['id_rule']);
            $preQuery = 'SELECT DISTINCT(p.id_product) FROM ' . _DB_PREFIX_ . 'feature_product t
                RIGHT JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = t.id_product';
            $query = ($preQuery . ' WHERE ' . $w);
            if (Db::getInstance()->executeS($query . ' AND p.id_product = ' . (int)$product->id)) {
                $relRules[] = $rule['rel_rule'];
            }
        }
        $ids = array();
        foreach ($relRules as $id_rule) {
            foreach (self::getRuleProducts($id_rule) as $id) {
                $ids[] = $id;
            }
        }
        return $ids;
    }
    
    public static function getRuleProducts($id_rule)
    {
        $where = self::buildRuleQuery($id_rule);
        if (empty($where)) {
            return array();
        }
        $preQuery = 'SELECT DISTINCT(p.id_product) FROM ' . _DB_PREFIX_ . 'feature_product t
            RIGHT JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = t.id_product';
        $query = ($preQuery . ' WHERE ' . $where);
        $ids = array();
        if ($res = Db::getInstance()->executeS($query)) {
            foreach ($res as $row) {
                $ids[] = $row['id_product'];
            }
        }
        return $ids;
    }


    public static function buildRuleQuery($id_rule)
    {
        $groups = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'arproductlist_rule_group t WHERE t.id_rule = ' . (int)$id_rule . ' AND status = 1 ORDER BY t.position ASC');
        $q = '';
        foreach ($groups as $k => $group) {
            $q .=  ((($k != 0)? $group['op'] : '') . " (/*group {$group['id_group']}*/" . self::buildGroupQuery($group)) . ') ';
        }
        
        return $q;
    }
    
    public static function buildGroupQuery($group)
    {
        $conditions = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'arproductlist_rule_condition t WHERE t.id_group = ' . (int)$group['id_group'] . ' AND status = 1 ORDER BY t.position ASC');
        $q = '';
        foreach ($conditions as $k => $condition) {
            if ($condition['id_feature'] && $condition['id_feature_value']) {
                $q .= ((($k != 0)? $condition['op'] : '') .  " (id_feature = {$condition['id_feature']} AND id_feature_value = {$condition['id_feature_value']}) ");
            }
            if ($condition['id_category']) {
                $q .= ((($k != 0)? $condition['op'] : '') .  " (id_category_default = {$condition['id_category']}) ");
            }
            if ($condition['id_manufacturer']) {
                $q .= ((($k != 0)? $condition['op'] : '') .  " (id_manufacturer = {$condition['id_manufacturer']}) ");
            }
        }
        return $q;
    }
}
