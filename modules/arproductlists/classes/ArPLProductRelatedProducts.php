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

class ArPLProductRelatedProducts extends ArPLListAbstract
{
    public function getProductList()
    {
        $id_lang = Context::getContext()->language->id;
        $p = new Product($this->product->id, false, $id_lang);
        $products = $p->getAccessories($id_lang);
        if ($this->instock) {
            foreach ($products as $k => $product) {
                if (Product::getQuantity($product['id_product'], $product['cache_default_attribute']) < 1) {
                    unset($products[$k]);
                }
            }
        }
        return $products;
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
        return 'Related products (product accessories)';
    }
}
