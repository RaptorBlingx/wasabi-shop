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

class ArPLLastBuyedProducts extends ArPLListAbstract
{
    public $limit;
    public $days;
    public $more_link;
    public $more_url;
    
    public function getProductList()
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
            ', (SELECT product_id, count(product_id) as orders, o.date_add
                FROM `'._DB_PREFIX_.'order_detail` od
                LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.id_order = od.id_order
                WHERE o.date_add between "' . pSQL($start) . '" AND "' . pSQL($end) . '" AND o.id_shop = ' . (int)$id_shop . '
                GROUP BY product_id
                ORDER BY o.date_add DESC
            ) pv'
        );
        
        $conditions = array(
            'pv.product_id = p.id_product'
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
        return 'Last buyed products';
    }
}
