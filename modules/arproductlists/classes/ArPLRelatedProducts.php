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

class ArPLRelatedProducts extends ArPLListAbstract
{
    public $limit;
    public $more_link;
    public $more_url;
    
    public $orderBy;
    public $orderWay;
    
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
        
        $defaultCat = $this->product->id_category_default;
        $relatedCats = ArProductListRelCat::getRelatedCategoriesStatic($defaultCat);
        if (empty($relatedCats)) {
            return array();
        }
        
        $conditions = array(
            'p.id_product != ' . (int)$this->product->id,
            'p.id_category_default  IN(' . implode(', ', $relatedCats) . ')'
        );
        
        $products = $this->getProducts2($id_lang, $id_shop, $this->limit, $conditions, array(), $orderBy, $orderWay, false, true, $random);
        
        return $products;
    }
    
    public function getMoreLink()
    {
        if ($this->more_url) {
            return strtr($this->more_url, array(
                '{lang}' => Context::getContext()->language->iso_code
            ));
        }
        return null;
    }
    
    public function rules()
    {
        return array(
            'required' => array(
                'limit'
            )
        );
    }
    
    public function getOrderOptions()
    {
        return array(
            'orderBy' => array(
                'rand' => 'Random',
                'id_product' => 'Product ID',
                'name' => 'Product name',
                'price' => 'Price',
                'manufacturer_name' => 'Manufacturer name'
            ),
            'orderWay' => array(
                'asc' => 'ASC',
                'desc' => 'DESC',
            )
        );
    }
    
    public function getDefaultSortOrder()
    {
        return $this->orderBy . ':' . $this->orderWay;
    }
    
    public function getFrontendOrderOptions()
    {
        return array(
            'price:asc' => 'Price ASC',
            'price:desc' => 'Price DESC',
            'id_product:asc' => 'Date add ASC',
            'id_product:desc' => 'Date add DESC',
            'name:asc' => 'Product name ASC',
            'name:desc' => 'Product name DESC',
            'manufacturer_name:asc' => 'Manufacturer name ASC',
            'manufacturer_name:desc' => 'Manufacturer name DESC'
        );
    }
    
    public function isProductList()
    {
        return true;
    }
    
    public function isCategoryList()
    {
        return false;
    }
    
    public function isBrandList()
    {
        return false;
    }
    
    public static function getTypeTitle()
    {
        return 'Related category products';
    }
}
