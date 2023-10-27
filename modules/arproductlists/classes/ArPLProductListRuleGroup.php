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

class ArPLProductListRuleGroup extends ObjectModel
{
    const TABLE_NAME = 'arproductlist_rule_group';
    
    public $id;
    public $id_rule;
    public $status;
    public $position;
    public $op;
    
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_group',
        'multilang' => false,
        'fields' => array(
            'id_rule'  =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'status'    =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'position'    =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'op'      =>      array('type' => self::TYPE_STRING, 'validate' => 'isString')
        ),
    );
    
    public static function installTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . self::getTableName() . "` (
            `id_group` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_rule` INT(10) UNSIGNED NOT NULL,
            `op` VARCHAR(10) NULL DEFAULT NULL,
            `status` TINYINT(3) UNSIGNED NOT NULL,
            `position` INT(10) UNSIGNED NULL DEFAULT NULL,
            PRIMARY KEY (`id_group`),
            INDEX `id_rule` (`id_rule`),
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
}
