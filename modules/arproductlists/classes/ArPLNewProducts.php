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

class ArPLNewProducts extends ArPLListAbstract
{
    public $limit;
    public $loop;
    public $more_link;
    public $more_url;
    
    public $orderBy;
    public $orderWay;
    
    public function getProductList()
    {
        $id_lang = Context::getContext()->language->id;
        if ($sortOrder = $this->getSortOrder()) {
            $this->orderBy = $sortOrder[0];
            $this->orderWay = $sortOrder[1];
        }
        $products = Product::getNewProducts($id_lang, 0, $this->limit, false, $this->orderBy == 'rand'? null : $this->orderBy, $this->orderBy == 'rand'? null : $this->orderWay);
        if ($this->instock) {
            foreach ($products as $k => $product) {
                if (Product::getQuantity($product['id_product'], $product['cache_default_attribute']) < 1) {
                    unset($products[$k]);
                }
            }
        }
        if ($products && $this->orderBy == 'rand') {
            shuffle($products);
        }
        return $products;
    }
    
    public function getOrderOptions()
    {
        return array(
            'orderBy' => array(
                'rand' => 'Random',
                'price' => 'Price',
                'id_product' => 'Product ID',
                'name' => 'Product name'
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
    
    public function rules()
    {
        return array(
            'required' => array(
                'limit'
            )
        );
    }
    
    public function getMoreLink()
    {
        if ($this->more_url) {
            return strtr($this->more_url, array(
                '{lang}' => Context::getContext()->language->iso_code
            ));
        }
        return Context::getContext()->link->getPageLink('new-products');
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
        return 'New products';
    }
}
