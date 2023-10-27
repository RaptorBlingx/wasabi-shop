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

class ArPLLastCartProducts extends ArPLListAbstract
{
    public $limit;
    public $days;
    public $more_link;
    public $more_url;
    
    public function getProductList()
    {
        $id_lang = Context::getContext()->language->id;
        $products = $this->getProducts($id_lang);
        return $products;
    }
    
    public function getProducts($idLang)
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        
        if ($this->days == 1) {
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
        } else {
            $start = date('Y-m-d 00:00:00', strtotime("-" . (int)$this->days . " day"));
            $end = date('Y-m-d 23:59:59');
        }
        
        $join = array(
            ', (SELECT id_product, count(id_product) as carts, date_add
                FROM `'._DB_PREFIX_.'cart_product`
                WHERE `date_add` between "' . pSQL($start) . '" AND "' . pSQL($end) . '" AND id_shop = ' . (int)$id_shop . '
                GROUP BY id_product
                ORDER BY date_add DESC
            ) pv'
        );
        
        $conditions = array(
            'pv.id_product = p.id_product'
        );
        
        $products = $this->getProducts2($id_lang, $id_shop, $this->limit, $conditions, $join, 'pv.date_add', 'DESC', false, true, false);
        
        if ($this->instock) {
            foreach ($products as $k => $product) {
                if (Product::getQuantity($product['id_product'], $product['cache_default_attribute']) < 1) {
                    unset($products[$k]);
                }
            }
        }
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
                'days',
                'limit'
            )
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
        return 'Last added to cart products';
    }
}
