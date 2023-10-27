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

class ArCategoryViews extends ObjectModel
{
    const TABLE_NAME = 'arproductlist_views_category';
    
    public $id;
    public $id_category;
    public $ip;
    public $view_date;


    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_view',
        'multilang' => true,
        'fields' => array(
            'id_category' =>         array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'ip' =>                 array('type' => self::TYPE_STRING),
            'view_date' =>          array('type' => self::TYPE_STRING)
        ),
    );

    public static function installTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . self::getTableName() . "` (
            `id_view` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_category` INT(11) UNSIGNED NOT NULL,
            `ip` VARCHAR(32) NULL DEFAULT NULL,
            `view_date` DATETIME NULL DEFAULT NULL,
            PRIMARY KEY (`id_view`),
            INDEX `id_category` (`id_category`),
            INDEX `view_date` (`view_date`)
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
    
    public static function viewCategory($id_category)
    {
        $ip = Tools::getRemoteAddr();
        self::cleanUp();
        return Db::getInstance()->Execute("INSERT INTO `" . self::getTableName() .
            "` (id_view, id_category, ip, view_date) VALUES (NULL, " . (int)$id_category . ", '" .
            pSQL($ip) . "', '" . pSQL(date('Y-m-d H:i:s')) . "')");
    }
    
    public static function cleanUp()
    {
        $ttl = (int)ConfigurationCore::get('ARPLG_VIEW_CLEANUP_AFTER');
        if ($ttl == 0) {
            return ;
        }
        $time = strtotime("-{$ttl} days");
        $date = date('Y-m-d H:i:s', $time);
        $sql = "DELETE FROM `" . self::getTableName() . "` WHERE `view_date` < '{$date}'";
        return Db::getInstance()->Execute($sql);
    }
}
