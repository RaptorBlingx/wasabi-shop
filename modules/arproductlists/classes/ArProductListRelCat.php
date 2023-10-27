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

class ArProductListRelCat extends ObjectModel
{
    const TABLE_NAME = 'arproductlist_rel_cat';
    
    public $id;
    
    public $id_cat;
    public $id_rel;
    public $id_shop;
    public $status;
    public $position;

    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_relcat',
        'multilang' => false,
        'fields' => array(
            'id_cat' =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_rel' =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_shop' =>      array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'status' =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'position' =>     array('type' => self::TYPE_INT, 'validate' => 'isInt')
        ),
    );
    
    public static function installTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . self::getTableName() . "` (
            `id_relcat` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_cat` INT UNSIGNED NULL,
            `id_rel` INT UNSIGNED NULL,
            `id_shop` INT UNSIGNED NULL,
            `status` INT UNSIGNED NULL,
            `position` INT UNSIGNED NULL,
            PRIMARY KEY (`id_relcat`),
            INDEX `id_cat` (`id_cat`),
            INDEX `id_rel` (`id_rel`),
            INDEX `id_shop` (`id_shop`),
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
    
    public static function getPosition($id_cat)
    {
        $sql = new DbQuery();
        $sql->from(self::getTableName(false));
        $sql->select('COUNT(1) AS c');
        $sql->where("`id_cat` = '" . (int)$id_cat . "'");
        return Db::getInstance()->getValue($sql);
    }
    
    public function getRelatedCategories($idsOnly = true, $activeOnly = true, $id_shop = null)
    {
        return self::getRelatedCategoriesStatic($this->id, $idsOnly, $activeOnly, $id_shop);
    }
    
    public static function getRelatedCategoriesStatic($id, $idsOnly = true, $activeOnly = true, $id_shop = null)
    {
        if (null === $id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }
        $sql = new DbQuery();
        $sql->from(self::getTableName(false));
        $where = "id_cat = " . (int)$id . " AND id_shop = " . (int)$id_shop;
        if ($activeOnly) {
            $where .= " AND status = 1";
        }
        $sql->where($where);
        $sql->orderBy('position ASC');
        $ids = array();
        if ($res = Db::getInstance()->executeS($sql)) {
            foreach ($res as $row) {
                if ($idsOnly) {
                    $ids[] = $row['id_rel'];
                } else {
                    $category = new Category($row['id_rel'], Context::getContext()->language->id);
                    $ids[] = array(
                        'id' => $row['id_relcat'],
                        'category' => $category,
                        'status' => $row['status'],
                        'path' => self::getCategoryPath($category)
                    );
                }
            }
        }
        return $ids;
    }
    
    public static function getMainCategories($id_shop = null)
    {
        if (null === $id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }
        $res = array();
        if ($rows = Db::getInstance()->executeS('SELECT DISTINCT(id_cat) FROM ' . self::getTableName() . ' WHERE id_shop = ' . (int)$id_shop)) {
            foreach ($rows as $row) {
                $category = new Category($row['id_cat'], Context::getContext()->language->id);
                $res[$row['id_cat']] = array(
                    'id' => $row['id_cat'],
                    'category' => $category,
                    'path' => self::getCategoryPath($category),
                    'rels' => self::getRelatedCategoriesStatic($row['id_cat'], false, false)
                );
            }
        }
        return $res;
    }
    
    public static function getCategoryPath($category)
    {
        $parents = $category->getParentsCategories();
        $parents = array_reverse($parents);
        $path = array();
        foreach ($parents as $parent) {
            if ($parent['id_category'] != $category->id) {
                $path[] = $parent['name'];
            }
        }
        return implode(' / ', $path);
    }
}
