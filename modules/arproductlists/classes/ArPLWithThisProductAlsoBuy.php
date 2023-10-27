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

class ArPLWithThisProductAlsoBuy extends ArPLListAbstract
{
    public $limit;
    
    public function getProductList()
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        return $this->getProducts($id_lang, $id_shop, $this->limit, 'id_product', 'ASC', array($this->product->id));
    }
    
    public function rules()
    {
        return array(
            'required' => array(
                'limit'
            )
        );
    }
    
    public function getProducts($id_lang, $id_shop, $limit, $orderBy, $orderWay, $exclude)
    {
        $sql = 'SELECT id_cart FROM ' . _DB_PREFIX_ . 'cart_product cp WHERE cp.id_product = ' . (int)$this->product->id . ' AND cp.id_shop = ' . (int)$id_shop . ' ORDER BY id_cart DESC LIMIT 30';
        
        $cartIds = array();
        $productIds = array();
        if ($res = Db::getInstance()->executeS($sql)) {
            foreach ($res as $row) {
                $cartIds[] = $row['id_cart'];
            }
        }
        if ($cartIds) {
            $sql = 'SELECT id_product FROM ' . _DB_PREFIX_ . 'cart_product cp WHERE cp.id_cart IN (' . implode(', ', $cartIds) . ') AND cp.id_product NOT IN (' . implode(', ', $exclude) . ')';
            if ($res = Db::getInstance()->executeS($sql)) {
                foreach ($res as $row) {
                    if (!in_array($row['id_product'], $productIds)) {
                        $productIds[] = $row['id_product'];
                    }
                }
            }
        }
        if ($productIds) {
            if ($sortOrder = $this->getSortOrder()) {
                $this->orderBy = $sortOrder[0];
                $this->orderWay = $sortOrder[1];
            }
            
            $conditions = array(
                'p.id_product IN (' . implode(',', $productIds) . ')'
            );
            
            $join = array();
            
            $orderBy = $this->orderBy == 'rand'? null : $this->orderBy;
            $orderWay = $this->orderBy == 'rand'? null : $this->orderWay;
            $random = $this->orderBy == 'rand'? true : false;
            $products = $this->getProducts2($id_lang, $id_shop, $this->limit, $conditions, $join, $orderBy, $orderWay, false, true, $random);
            
            if ($this->instock) {
                foreach ($products as $k => $product) {
                    if (Product::getQuantity($product['id_product'], $product['cache_default_attribute']) < 1) {
                        unset($products[$k]);
                    }
                }
            }
            return $products;
        }
        return array();
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
        return 'With this product also buy';
    }
}
