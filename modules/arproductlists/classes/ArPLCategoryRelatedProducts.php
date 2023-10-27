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

include_once dirname(__FILE__).'/ArPLRelatedProducts.php';

class ArPLCategoryRelatedProducts extends ArPLRelatedProducts
{
    public function getProductList()
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        
        if ($sortOrder = $this->getSortOrder()) {
            $this->orderBy = $sortOrder[0];
            $this->orderWay = $sortOrder[1];
        }
        
        $orderBy = $this->orderBy == 'rand'? null : $this->orderBy;
        $orderWay = $this->orderBy == 'rand'? null : $this->orderWay;
        $random = $this->orderBy == 'rand'? true : false;
        
        $conditions = array();
        
        $defaultCat = $this->getDefaultCategory();
        if (empty($defaultCat)) {
            return array();
        }
        $relatedCats = ArProductListRelCat::getRelatedCategoriesStatic($defaultCat);
        if (empty($relatedCats)) {
            return array();
        }
        
        $conditions[] = 'p.id_category_default  IN(' . implode(', ', $relatedCats) . ')';
        
        $products = $this->getProducts2($id_lang, $id_shop, $this->limit, $conditions, array(), $orderBy, $orderWay, false, true, $random);
        
        if ($products && $this->orderBy == 'rand') {
            shuffle($products);
        }
        
        return $products;
    }
    
    public function getDefaultCategory()
    {
        $controller = Context::getContext()->controller;
        
        if (isset($controller->php_self) && $controller->php_self == 'category') {
            $category = $controller->getCategory();
            return $category->id;
        } else {
            if ($this->getCurrentCategoryId()) {
                return $this->getCurrentCategoryId();
            } else {
                return false;
            }
        }
        return false;
    }
    
    public function getDefaultSortOrder()
    {
        return $this->orderBy . ':' . $this->orderWay;
    }
    
    public function getOrderOptions()
    {
        return array(
            'orderBy' => array(
                'rand' => 'Random',
                'price' => 'Price',
                'id_product' => 'Product ID',
                'name' => 'Product name',
                'manufacturer_name' => 'Manufacturer name'
            ),
            'orderWay' => array(
                'asc' => 'ASC',
                'desc' => 'DESC',
            )
        );
    }
    
    public function getFrontendOrderOptions()
    {
        return array(
            'price:asc' => 'Price ASC',
            'price:desc' => 'Price DESC',
            'date_add:asc' => 'Date add ASC',
            'date_add:desc' => 'Date add DESC',
            'name:asc' => 'Product name ASC',
            'name:desc' => 'Product name DESC'
        );
    }
    
    public static function getTypeTitle()
    {
        return 'Related categories products';
    }
}
