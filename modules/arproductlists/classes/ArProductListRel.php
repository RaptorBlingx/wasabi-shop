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

include_once dirname(__FILE__).'/ArPLListAbstract.php';

include_once dirname(__FILE__).'/ArPLHomeFeatured.php';
include_once dirname(__FILE__).'/ArPLCategory.php';
include_once dirname(__FILE__).'/ArPLViewedProducts.php';
include_once dirname(__FILE__).'/ArPLPriceDrop.php';
include_once dirname(__FILE__).'/ArPLBestSellers.php';
include_once dirname(__FILE__).'/ArPLNewProducts.php';
include_once dirname(__FILE__).'/ArPLCustomProducts.php';
include_once dirname(__FILE__).'/ArPLPromotions.php';
include_once dirname(__FILE__).'/ArPLSameCategoryProducts.php';
include_once dirname(__FILE__).'/ArPLSameBrandProducts.php';
include_once dirname(__FILE__).'/ArPLSameReferenceProducts.php';
include_once dirname(__FILE__).'/ArPLBrandProducts.php';
include_once dirname(__FILE__).'/ArPLSupplierProducts.php';
include_once dirname(__FILE__).'/ArPLWithThisProductAlsoBuy.php';
include_once dirname(__FILE__).'/ArPLPromotionsWithProduct.php';
include_once dirname(__FILE__).'/ArPLCustomCategories.php';
include_once dirname(__FILE__).'/ArPLChildCategories.php';
include_once dirname(__FILE__).'/ArPLRelatedProducts.php';
include_once dirname(__FILE__).'/ArPLRelatedCategories.php';
include_once dirname(__FILE__).'/ArPLCustomBrands.php';
include_once dirname(__FILE__).'/ArPLRuleProducts.php';
include_once dirname(__FILE__).'/ArPLCategoryChildCategories.php';
include_once dirname(__FILE__).'/ArPLCategoryRelatedCategories.php';
include_once dirname(__FILE__).'/ArPLCategoryRelatedProducts.php';
include_once dirname(__FILE__).'/ArPLSubcategoriesFeaturedProducts.php';
include_once dirname(__FILE__).'/ArPLSubcategoriesNewProducts.php';
include_once dirname(__FILE__).'/ArPLSubcategoriesProducts.php';
include_once dirname(__FILE__).'/ArPLSubcategoriesBestSellers.php';
include_once dirname(__FILE__).'/ArPLProductCategories.php';
include_once dirname(__FILE__).'/ArPLSameAttrProducts.php';
include_once dirname(__FILE__).'/ArPLSameFeatureProducts.php';
include_once dirname(__FILE__).'/ArPLProductsIPurchased.php';
include_once dirname(__FILE__).'/ArPLViewedCategories.php';
include_once dirname(__FILE__).'/ArPLCategoryBrands.php';
include_once dirname(__FILE__).'/ArPLMostWantedProductsBrands.php';
include_once dirname(__FILE__).'/ArPLMostViewedProductsBrands.php';

class ArProductListRel extends ObjectModel
{
    const TABLE_NAME = 'arproductlist_rel';
    
    public $id;
    
    public $title;
    public $more_link;
    
    public $id_group;
    public $id_list;
    public $class;
    public $data;
    public $device;
    public $status;
    public $position;
    
    public $list = null;

    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_rel',
        'multilang' => true,
        'fields' => array(
            'class' =>          array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'data' =>           array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'id_group' =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_list' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'device' =>         array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'status' =>         array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'position' =>       array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            
            'title' =>          array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true),
            'more_link' =>      array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true),
        ),
    );
    
    public function getProductList()
    {
        return $this->getList()->getProductList();
    }
    
    public function getCategoriesList()
    {
        return $this->getList()->getCategoriesList();
    }
    
    public function getBrandsList()
    {
        return $this->getList()->getBrandsList();
    }
    
    /**
     *
     * @return ArPLListAbstract
     */
    public function getList()
    {
        if ($this->list === null) {
            $className = $this->class;
            $this->list = new $className($this);
        }
        return $this->list;
    }
    
    public static function installTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . self::getTableName() . "` (
            `id_rel` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_group` INT(10) UNSIGNED NOT NULL,
            `id_list` INT(10) UNSIGNED NOT NULL,
            `class` VARCHAR(50) NULL DEFAULT NULL,
            `data` TEXT NULL,
            `status` TINYINT(4) UNSIGNED NULL DEFAULT NULL,
            `device` TINYINT(4) UNSIGNED NULL DEFAULT '0',
            `position` INT(10) UNSIGNED NOT NULL,
            PRIMARY KEY (`id_rel`),
            INDEX `id_group` (`id_group`),
            INDEX `id_list` (`id_list`),
            INDEX `position` (`position`),
            INDEX `device` (`device`),
            INDEX `status` (`status`)
        )
        COLLATE='utf8_general_ci'";
        
        $res = Db::getInstance()->execute($sql);
        
        $sql = "CREATE TABLE IF NOT EXISTS `" . self::getTableName() . "_lang` (
            `id_rel` INT(10) UNSIGNED NOT NULL,
            `id_lang` INT(10) UNSIGNED NOT NULL,
            `title` VARCHAR(255) NULL DEFAULT NULL,
            `more_link` VARCHAR(255) NULL DEFAULT NULL,
            PRIMARY KEY (`id_rel`, `id_lang`)
        )
        COLLATE='utf8_general_ci'";
        
        return $res && Db::getInstance()->execute($sql);
    }
    
    public static function uninstallTable()
    {
        $res = Db::getInstance()->execute('DROP TABLE IF EXISTS `' . self::getTableName() . '`');
        return $res && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . self::getTableName() . '_lang`');
    }
    
    public static function getTableName($withPrefix = true)
    {
        if ($withPrefix) {
            return (_DB_PREFIX_ . self::TABLE_NAME);
        }
        return self::TABLE_NAME;
    }
    
    public static function getPosition($id_group)
    {
        $sql = new DbQuery();
        $sql->from(self::getTableName(false));
        $sql->select('COUNT(1) AS c');
        $sql->where("`id_group` = '" . (int)$id_group . "'");
        return Db::getInstance()->getValue($sql);
    }


    public function toArray()
    {
        $res = array();
        $fields = self::$definition['fields'];
        $id_lang = Context::getContext()->language->id;
        foreach ($this as $attr => $value) {
            if (in_array($attr, array_keys($fields))) {
                if (isset($fields[$attr]['lang']) && $fields[$attr]['lang'] == true) {
                    if (is_array($value) && isset($value[$id_lang])) {
                        $res[$attr] = $value[$id_lang];
                    }
                } else {
                    $res[$attr] = $value;
                }
            }
        }
        $list = new ArProductList($res['id_list'], $id_lang);
        $res['id_rel'] = $this->id;
        $res['typeTitle'] = ArProductList::getTypeTitle($this->class);
        $res['product_context'] = $list->product_context;
        return $res;
    }
}
