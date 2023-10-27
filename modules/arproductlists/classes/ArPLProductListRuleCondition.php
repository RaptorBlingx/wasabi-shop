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

class ArPLProductListRuleCondition extends ObjectModel
{
    const TABLE_NAME = 'arproductlist_rule_condition';
    
    public $id;
    public $id_group;
    public $id_feature;
    public $id_feature_value;
    public $id_category;
    public $id_manufacturer;
    public $status;
    public $position;
    public $op;
    
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_cond',
        'multilang' => false,
        'fields' => array(
            'id_group'  =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_feature'  =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_feature_value'  =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_category'  =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_manufacturer'  =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'status'    =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'position'    =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'op'      =>      array('type' => self::TYPE_STRING, 'validate' => 'isString')
        ),
    );
    
    public static function installTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . self::getTableName() . "` (
            `id_cond` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_group` INT(10) UNSIGNED NOT NULL DEFAULT '0',
            `id_feature` INT(10) UNSIGNED NOT NULL DEFAULT '0',
            `id_feature_value` INT(10) UNSIGNED NOT NULL DEFAULT '0',
            `id_category` INT(10) UNSIGNED NOT NULL DEFAULT '0',
            `id_manufacturer` INT(10) UNSIGNED NOT NULL DEFAULT '0',
            `op` VARCHAR(10) NULL DEFAULT NULL,
            `status` TINYINT(3) UNSIGNED NOT NULL,
            `position` INT(10) UNSIGNED NOT NULL,
            PRIMARY KEY (`id_cond`),
            INDEX `status` (`status`),
            INDEX `id_feature` (`id_feature`),
            INDEX `id_feature_value` (`id_feature_value`),
            INDEX `id_category` (`id_category`),
            INDEX `id_manufacturer` (`id_manufacturer`),
            INDEX `id_group` (`id_group`)
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
}
