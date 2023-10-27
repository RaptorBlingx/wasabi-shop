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

class ArPLCustomProducts extends ArPLListAbstract
{
    public $ids;
    public $more_link;
    public $more_url;
    
    public function getProductList()
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        $res = array();
        
        foreach ($this->ids as $id) {
            $sql = 'SELECT p.*, pl.*, m.name as manufacturer_name FROM `' . _DB_PREFIX_ . 'product` p '
                    . ' LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON pl.id_product = p.id_product '
                    . ' LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa ON sa.id_product = p.id_product AND sa.id_product_attribute = p.cache_default_attribute ';
            if ($this->is16()) {
                if (version_compare(_PS_VERSION_, '1.6.1', '>=') === true) {
                    $sql = 'SELECT p.*, pl.*, image_shop.*, il.*, m.name as manufacturer_name FROM `' . _DB_PREFIX_ . 'product` p '
                        . 'LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON pl.id_product = p.id_product ';
                    if ($this->instock) {
                        $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa ON sa.id_product = p.id_product AND sa.id_product_attribute = p.cache_default_attribute ';
                    }
                    $sql .= 'LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$id_shop.') '
                        . 'LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.') ';
                } else {
                    $sql = 'SELECT p.*, pl.*, image.*, il.*, m.name as manufacturer_name FROM `' . _DB_PREFIX_ . 'product` p '
                        . 'LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON pl.id_product = p.id_product ';
                    if ($this->instock) {
                        $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa ON sa.id_product = p.id_product AND sa.id_product_attribute = p.cache_default_attribute ';
                    }
                    $sql .= 'LEFT JOIN `'._DB_PREFIX_.'image` image ON (image.`id_product` = p.`id_product` AND image.cover=1) '
                        . 'LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.') ';
                }
            }
            $sql .= 'LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.id_manufacturer = p.id_manufacturer ';
            $sql .= 'WHERE pl.id_lang = ' . (int)$id_lang . ' AND p.id_product IN (' . (int)$id . ') '
                . 'AND pl.id_shop = ' . (int)$id_shop . ' AND p.active = 1';
            if ($this->instock) {
                $sql .= ' AND (p.quantity > 0 OR sa.quantity > 0)';
            }
            $res[] = Db::getInstance()->getRow($sql);
        }
        if ($this->instock) {
            foreach ($res as $k => $product) {
                if (Product::getQuantity($product['id_product'], $product['cache_default_attribute']) < 1) {
                    unset($res[$k]);
                }
            }
        }
        return Product::getProductsProperties($id_lang, $res);
    }
    
    public function rules()
    {
        return array(
            'required' => array(
                'ids'
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
        return null;
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
        return 'Custom products';
    }
}
