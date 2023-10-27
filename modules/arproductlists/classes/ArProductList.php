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
include_once dirname(__FILE__).'/ArPLRelatedProducts.php';
include_once dirname(__FILE__).'/ArPLProductRelatedProducts.php';
include_once dirname(__FILE__).'/ArPLRelatedCategories.php';
include_once dirname(__FILE__).'/ArPLCustomBrands.php';
include_once dirname(__FILE__).'/ArPLRuleProducts.php';
include_once dirname(__FILE__).'/ArPLCategoryChildCategories.php';
include_once dirname(__FILE__).'/ArPLCategoryRelatedCategories.php';
include_once dirname(__FILE__).'/ArPLCategoryRelatedProducts.php';
include_once dirname(__FILE__).'/ArPLMostViewedProducts.php';
include_once dirname(__FILE__).'/ArPLMostWantedProducts.php';
include_once dirname(__FILE__).'/ArPLLastCartProducts.php';
include_once dirname(__FILE__).'/ArPLMostBuyedProducts.php';
include_once dirname(__FILE__).'/ArPLLastBuyedProducts.php';
include_once dirname(__FILE__).'/ArPLProductCategories.php';
include_once dirname(__FILE__).'/ArPLSameAttrProducts.php';
include_once dirname(__FILE__).'/ArPLSameFeatureProducts.php';
include_once dirname(__FILE__).'/ArPLProductsIPurchased.php';
include_once dirname(__FILE__).'/ArPLViewedCategories.php';
include_once dirname(__FILE__).'/ArPLCategoryBrands.php';
include_once dirname(__FILE__).'/ArPLMostWantedProductsBrands.php';
include_once dirname(__FILE__).'/ArPLMostViewedProductsBrands.php';

class ArProductList extends ObjectModel
{
    const TABLE_NAME = 'arproductlist';
    
    public $id;
    public $product_context;
    public $category_context;
    public $title;
    public $class;
    public $data;
    public $status;
    public $position;
    
    protected $list = null;


    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_list',
        'multilang' => true,
        'fields' => array(
            'class' =>              array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'data' =>               array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'status' =>             array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'product_context' =>    array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'category_context' =>   array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'position' =>           array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'title' =>              array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true),
        ),
    );
    
    public function getProductList()
    {
        return $this->getList()->getProductList();
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
    
    public static function getByHook($hook)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::getTableName(false));
        $sql->where("`hook` = '" . pSQL($hook) . "' AND `status` = 1");
        $sql->orderBy('`position` ASC');
        return Db::getInstance()->executeS($sql);
    }
    
    public static function getAll($id_lang, $productContext = false, $categoryContext = false)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::getTableName(false), 'pl');
        $sql->leftJoin(self::getTableName(false) . '_lang', 'pll', 'pll.id_list = pl.id_list');
        $where = array();
        $where[] = '`status` = 1 AND pll.id_lang = ' . (int)$id_lang;
        if ($productContext !== false) {
            $where[] = '`product_context` = ' . (int)$productContext;
        }
        if ($categoryContext !== false) {
            $where[] = '`category_context` = ' . (int)$categoryContext;
        }
        $sql->where(implode(' AND ', $where));
        $sql->orderBy('`position` ASC');
        $res = Db::getInstance()->executeS($sql);
        foreach ($res as &$row) {
            $row['typeTitle'] = self::getTypeTitle($row['class']);
        }
        
        return $res;
    }
    
    public static function getTypeTitle($type)
    {
        return $type::getTypeTitle();
    }


    public static function installTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . self::getTableName() . "` (
            `id_list` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `hook` VARCHAR(255) NULL DEFAULT NULL,
            `product_context` TINYINT(3) UNSIGNED NULL DEFAULT '0',
            `category_context` TINYINT(3) UNSIGNED NULL DEFAULT '0',
            `class` VARCHAR(255) NULL DEFAULT NULL,
            `data` TEXT NULL,
            `status` TINYINT(3) UNSIGNED NULL DEFAULT NULL,
            `position` INT(10) UNSIGNED NULL DEFAULT NULL,
            PRIMARY KEY (`id_list`),
            INDEX `hook` (`hook`),
            INDEX `position` (`position`)
        )
        COLLATE='utf8_general_ci'";
        
        $res = Db::getInstance()->execute($sql);
        
        $sql = "CREATE TABLE IF NOT EXISTS `" . self::getTableName() . "_lang` (
            `id_list` INT(10) UNSIGNED NOT NULL,
            `id_lang` INT(10) UNSIGNED NOT NULL,
            `title` VARCHAR(255) NULL DEFAULT NULL,
            PRIMARY KEY (`id_list`, `id_lang`)
        )
        COLLATE='utf8_general_ci'";
        
        return $res && Db::getInstance()->execute($sql);
    }
    
    public static function isListExists($class)
    {
        return Db::getInstance()->executeS('SELECT * FROM `' . self::getTableName() . '` WHERE `class` = "' . pSQL($class) . '"');
    }
    
    public static function getDefaultLists()
    {
        return array(
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLCategory',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '1',
                'title' => 'Products of category'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLBrandProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '2',
                'title' => 'Brand products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLSupplierProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '3',
                'title' => 'Supplier products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLViewedProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '4',
                'title' => 'Viewed products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLHomeFeatured',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '5',
                'title' => 'Featured products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLBestSellers',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"date_add","orderWay":"asc"}',
                'status' => '1',
                'position' => '6',
                'title' => 'Best sellers'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLPriceDrop',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '7',
                'title' => 'Prices drop'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLNewProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '8',
                'title' => 'New Products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLCustomProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '9',
                'title' => 'Custom products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLPromotions',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '10',
                'title' => 'Promotions'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLChildCategories',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '11',
                'title' => 'Child categories'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLCustomCategories',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '12',
                'title' => 'Custom categories'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLCustomBrands',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '13',
                'title' => 'Custom brands'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLMostViewedProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"days":1}',
                'status' => '1',
                'position' => '14',
                'title' => 'Most viewed products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLMostWantedProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"days":30}',
                'status' => '1',
                'position' => '15',
                'title' => 'Most wanted products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLLastCartProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"days":30}',
                'status' => '1',
                'position' => '16',
                'title' => 'Last added to cart products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLMostBuyedProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"days":30}',
                'status' => '1',
                'position' => '17',
                'title' => 'Most buyed products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLLastBuyedProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"days":30}',
                'status' => '1',
                'position' => '18',
                'title' => 'Last buyed products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLViewedProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"days":30}',
                'status' => '1',
                'position' => '19',
                'title' => 'Viewed categories'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLProductsIPurchased',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"days":30}',
                'status' => '1',
                'position' => '20',
                'title' => 'Products I purchased'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLMostWantedProductsBrands',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"days":30}',
                'status' => '1',
                'position' => '21',
                'title' => 'Brands of most wanted products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '0',
                'class' => 'ArPLMostViewedProductsBrands',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"days":30}',
                'status' => '1',
                'position' => '22',
                'title' => 'Brands of viewed products'
            ),
            // product context
            array(
                'hook' => '',
                'product_context' => '1',
                'category_context' => '0',
                'class' => 'ArPLSameCategoryProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '1',
                'title' => 'Same category products'
            ),
            array(
                'hook' => '',
                'product_context' => '1',
                'category_context' => '0',
                'class' => 'ArPLSameBrandProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '2',
                'title' => 'Same brand products'
            ),
            array(
                'hook' => '',
                'product_context' => '1',
                'category_context' => '0',
                'class' => 'ArPLWithThisProductAlsoBuy',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '3',
                'title' => 'With this product also buy'
            ),
            array(
                'hook' => '',
                'product_context' => '1',
                'category_context' => '0',
                'class' => 'ArPLPromotionsWithProduct',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '4',
                'title' => 'Promotions with this product'
            ),
            array(
                'hook' => '',
                'product_context' => '1',
                'category_context' => '0',
                'class' => 'ArPLRelatedProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '5',
                'title' => 'Related products'
            ),
            array(
                'hook' => '',
                'product_context' => '1',
                'category_context' => '0',
                'class' => 'ArPLRelatedCategories',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '6',
                'title' => 'Related categories'
            ),
            array(
                'hook' => '',
                'product_context' => '1',
                'category_context' => '0',
                'class' => 'ArPLRuleProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '7',
                'title' => 'Rule based products'
            ),
            array(
                'hook' => '',
                'product_context' => '1',
                'category_context' => '0',
                'class' => 'ArPLProductRelatedProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '8',
                'title' => 'Related products'
            ),
            array(
                'hook' => '',
                'product_context' => '1',
                'category_context' => '0',
                'class' => 'ArPLProductCategories',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '9',
                'title' => 'Product categories'
            ),
            array(
                'hook' => '',
                'product_context' => '1',
                'category_context' => '0',
                'class' => 'ArPLSameAttrProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '10',
                'title' => 'Same attribute products'
            ),
            array(
                'hook' => '',
                'product_context' => '1',
                'category_context' => '0',
                'class' => 'ArPLSameFeatureProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '11',
                'title' => 'Same feature products'
            ),
            array(
                'hook' => '',
                'product_context' => '1',
                'category_context' => '0',
                'class' => 'ArPLSameReferenceProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '12',
                'title' => 'Same reference products'
            ),
            // category context
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '1',
                'class' => 'ArPLCategoryChildCategories',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '1',
                'title' => 'Category children categories'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '1',
                'class' => 'ArPLCategoryRelatedCategories',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '2',
                'title' => 'Category related categories'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '1',
                'class' => 'ArPLCategoryRelatedProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '3',
                'title' => 'Related categories products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '1',
                'class' => 'ArPLSubcategoriesFeaturedProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '4',
                'title' => 'Featured products from subcategories'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '1',
                'class' => 'ArPLSubcategoriesNewProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '5',
                'title' => 'New products from subcategories'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '1',
                'class' => 'ArPLSubcategoriesBestSellers',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '6',
                'title' => 'Best sellers from subcategories'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '1',
                'class' => 'ArPLSubcategoriesProducts',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '7',
                'title' => 'Subcategories products'
            ),
            array(
                'hook' => '',
                'product_context' => '0',
                'category_context' => '1',
                'class' => 'ArPLCategoryBrands',
                'data' => '{"titleAlign":"center","ajax":0,"cat_title":1,"cat_desc":1,"thumb_size":0,"view":1,"drag":1,"controls":1,"dots":1,"loop":1,"autoplay":0,"autoplayTimeout":3000,"responsiveBaseElement":"parent","responsiveBreakdowns":"","grid":6,"grid_md":3,"grid_sm":2,"ids":[],"cat_ids":[],"more_link":0,"limit":8,"orderBy":"id_product","orderWay":"asc"}',
                'status' => '1',
                'position' => '7',
                'title' => 'Category brands'
            ),
        );
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
}
