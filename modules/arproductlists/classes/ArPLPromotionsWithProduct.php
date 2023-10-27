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

class ArPLPromotionsWithProduct extends ArPLListAbstract
{
    public $limit;
    public $loop;
    
    public function getProductList()
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        $rules = $this->getCartRules();
        $return = array();
        foreach ($rules as $rule) {
            if (isset($return[$rule['id_cart_rule']])) {
                $return[$rule['id_cart_rule']]['groups'][$rule['id_product_rule_group']] = $this->getCartRulesProducts($rule['id_product_rule_group'], $id_lang, $id_shop);
            } else {
                $return[$rule['id_cart_rule']] = array(
                    'cart_rule' => $rule,
                    'groups' => array(
                        $rule['id_product_rule_group'] => $this->getCartRulesProducts($rule['id_product_rule_group'], $id_lang, $id_shop)
                    )
                );
            }
        }
        
        return array_splice($return, 0, $this->limit);
    }
    
    public function rules()
    {
        return array(
            'required' => array(
                'limit'
            )
        );
    }
    
    public function getCartRulesIds()
    {
        $sql = 'SELECT cr.id_cart_rule, crpr.id_product_rule_group, (SELECT COUNT(1) FROM ' . _DB_PREFIX_ . 'cart_rule_product_rule_group crprg2 WHERE crprg2.id_cart_rule = cr.id_cart_rule) `group_count` FROM ' . _DB_PREFIX_ . 'cart_rule cr
                LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_product_rule_group crprg ON crprg.id_cart_rule = cr.id_cart_rule
                LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_product_rule crpr ON crpr.id_product_rule_group = crprg.id_product_rule_group
                LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_product_rule_value crprv ON crprv.id_product_rule = crpr.id_product_rule
                LEFT JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = crprv.id_item
            WHERE cr.product_restriction = 1 AND (cr.`code` IS NULL OR cr.`code` = "") AND crpr.`type` = "products" AND p.active = 1 AND p.id_product = ' . (int)$this->product->id . ' 
            AND cr.date_from < "' . date('Y-m-d H:i:s') . '" AND cr.date_to > "' . date('Y-m-d H:i:s') . '"
            GROUP BY crpr.id_product_rule_group 
            HAVING `group_count` = 2
            ORDER BY cr.id_cart_rule, p.price DESC';
        
        $ids = array();
        if ($res = Db::getInstance()->executeS($sql)) {
            foreach ($res as $row) {
                $ids[] = $row['id_cart_rule'];
            }
        }
        return $ids;
    }
    
    public function getCartRules()
    {
        if (!$ids = $this->getCartRulesIds()) {
            return array();
        }
        $sql = 'SELECT cr.*, crpr.id_product_rule_group, (SELECT COUNT(1) FROM ' . _DB_PREFIX_ . 'cart_rule_product_rule_group crprg2 WHERE crprg2.id_cart_rule = cr.id_cart_rule) `group_count` FROM ' . _DB_PREFIX_ . 'cart_rule cr
                LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_product_rule_group crprg ON crprg.id_cart_rule = cr.id_cart_rule
                LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_product_rule crpr ON crpr.id_product_rule_group = crprg.id_product_rule_group
                LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_product_rule_value crprv ON crprv.id_product_rule = crpr.id_product_rule
                LEFT JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = crprv.id_item
            WHERE cr.product_restriction = 1 AND (cr.`code` IS NULL OR cr.`code` = "") AND crpr.`type` = "products" AND p.active = 1 AND cr.id_cart_rule IN (' . implode(',', $ids) . ') 
            GROUP BY crpr.id_product_rule_group 
            HAVING `group_count` = 2
            ORDER BY cr.id_cart_rule, p.price DESC';
        return Db::getInstance()->executeS($sql);
    }
    
    public function getCartRulesProducts($id_product_rule_group, $id_lang, $id_shop)
    {
        $sql = 'SELECT p.*, pl.*, image_shop.`id_image` id_image, il.`legend` FROM ' . _DB_PREFIX_ . 'cart_rule cr
                LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_product_rule_group crprg ON crprg.id_cart_rule = cr.id_cart_rule
                LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_product_rule crpr ON crpr.id_product_rule_group = crprg.id_product_rule_group
                LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_product_rule_value crprv ON crprv.id_product_rule = crpr.id_product_rule
                LEFT JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = crprv.id_item
                LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product = p.id_product
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$id_shop.') 
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang . ') 
            WHERE crpr.id_product_rule_group = ' . (int)$id_product_rule_group . ' AND crpr.`type` = "products" AND p.active = 1 AND pl.id_lang = ' . (int)$id_lang . ' AND pl.id_shop = ' . (int)$id_shop . '
            ORDER BY p.price DESC';
        $products = Db::getInstance()->executeS($sql);
        if ($this->instock) {
            foreach ($products as $k => $product) {
                if (Product::getQuantity($product['id_product'], $product['cache_default_attribute']) < 1) {
                    unset($products[$k]);
                }
            }
        }
        return Product::getProductsProperties($id_lang, $products);
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
        return 'Promotions with product';
    }
}
