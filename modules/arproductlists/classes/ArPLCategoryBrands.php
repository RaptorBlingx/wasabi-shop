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

class ArPLCategoryBrands extends ArPLListAbstract
{
    public $grid;
    public $grid_md;
    public $grid_sm;
    public $brand_thumb_size;
    public $cat_title;
    public $limit;
    
    public function getBrandsList()
    {
        $id_lang = Context::getContext()->language->id;
        $controller = Context::getContext()->controller;
        
        if (isset($controller->php_self) && $controller->php_self == 'category') {
            $category = $controller->getCategory();
        } else {
            return array();
        }
        
        $sql = 'SELECT DISTINCT(p.id_manufacturer) FROM `' . _DB_PREFIX_ . 'product` p
            LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON cp.id_product = p.id_product
            WHERE p.id_category_default = ' . (int)$category->id . ' OR cp.id_category = ' . (int)$category->id . ' LIMIT ' . (int)$this->limit;
        $res = array();
        if ($rows = Db::getInstance()->executeS($sql)) {
            foreach ($rows as $row) {
                $brand = new Manufacturer($row['id_manufacturer'], $id_lang);
                if (Validate::isLoadedObject($brand)) {
                    $res[] = $brand;
                }
            }
        }
        
        return $res;
    }
    
    public function rules()
    {
        return array(
            'required' => array(
                'brand_thumb_size',
                'limit'
            )
        );
    }
    
    public function isCategoryList() {
        return false;
    }
    
    public function isProductList() {
        return false;
    }
    
    public function isBrandList() {
        return true;
    }
    
    public static function getTypeTitle()
    {
        return 'Category brands';
    }
}
