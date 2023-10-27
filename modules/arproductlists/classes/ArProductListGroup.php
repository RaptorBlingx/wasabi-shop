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

include_once dirname(__FILE__).'/ArProductListRel.php';
include_once dirname(__FILE__).'/ArProductList.php';

class ArProductListGroup extends ObjectModel
{
    const TABLE_NAME = 'arproductlist_group';
    
    public $id;
    
    public $hook;
    public $id_shop;
    public $title;
    public $type;
    public $device;
    public $status;
    public $position;
    
    protected $list = null;

    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_group',
        'multilang' => false,
        'fields' => array(
            'hook' =>           array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'id_shop'  =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'title' =>          array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'type' =>           array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'device' =>         array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'status' =>         array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'position' =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
        ),
    );
    
    public static function getByHook($hook, $id_shop, $device, $activeOnly = true)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::getTableName(false));
        $where = "`hook` = '" . pSQL($hook) . "'";
        if ($id_shop != 0) {
            $where .= " AND id_shop IN(0, " . (int)$id_shop . ")";
        }
        if ($activeOnly) {
            $where .= " AND `status` = 1";
        }
        if ($device) {
            $where .= ' AND `device` IN (0, ' . (int)$device . ') ';
        }
        $sql->where($where);
        $sql->orderBy('`position` ASC');
        $result = array();
        foreach (Db::getInstance()->executeS($sql) as $row) {
            $result[] = new self($row['id_group']);
        }
        return $result;
    }
    
    public function getLists($id_lang, $device, $activeOnly = true)
    {
        $sql = 'SELECT plr.*, pl.product_context, pll.title FROM `' . ArProductListRel::getTableName() . '` plr 
            LEFT JOIN `' . ArProductListRel::getTableName() . '_lang` pll ON pll.id_rel = plr.id_rel
            LEFT JOIN `' . ArProductList::getTableName() . '` pl ON pl.id_list = plr.id_list
            WHERE plr.id_group = ' . (int)$this->id;
        if ($activeOnly) {
            $sql .= ' AND plr.status = 1 ';
        }
        if ($device) {
            $sql .= ' AND `device` IN (0, ' . (int)$device . ') ';
        }
        $sql .= ' AND pll.id_lang = ' . (int)$id_lang . ' ORDER BY plr.position ASC';
        
        $res = Db::getInstance()->executeS($sql);
        foreach ($res as &$row) {
            $row['typeTitle'] = ArProductList::getTypeTitle($row['class']);
        }
        return $res;
    }
    
    public static function installTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . self::getTableName() . "` (
            `id_group` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `hook` VARCHAR(255) NOT NULL,
            `id_shop` INT(10) UNSIGNED NULL DEFAULT '0',
            `title` VARCHAR(255) NOT NULL,
            `type` VARCHAR(50) NOT NULL,
            `device` TINYINT(3) UNSIGNED NULL DEFAULT '0',
            `status` TINYINT(3) UNSIGNED NOT NULL,
            `position` INT(10) UNSIGNED NOT NULL,
            PRIMARY KEY (`id_group`),
            INDEX `hook` (`hook`),
            INDEX `id_shop` (`id_shop`),
            INDEX `status` (`status`),
            INDEX `position` (`position`)
        )
        COLLATE='utf8_general_ci'";
        
        return Db::getInstance()->execute($sql);
    }
    
    public function getShopName()
    {
        if ($this->id_shop == 0) {
            return 'All shops';
        }
        $shop = new Shop($this->id_shop, Context::getContext()->language->id);
        if (Validate::isLoadedObject($shop)) {
            return $shop->name;
        }
        return '';
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
    
    public static function getHookPosition($hook)
    {
        $sql = new DbQuery();
        $sql->from(self::getTableName(false));
        $sql->select('COUNT(1) AS c');
        $sql->where("`hook` = '" . pSQL($hook) . "'");
        return Db::getInstance()->getValue($sql);
    }
}
