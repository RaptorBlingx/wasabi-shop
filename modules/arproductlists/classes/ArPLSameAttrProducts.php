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

/**
 * @property ProductCore $product
 */
class ArPLSameAttrProducts extends ArPLListAbstract
{
    public $limit;
    public $loop;
    public $more_link;
    public $more_url;
    
    public $attribute_group;
    public $same_category_only;
    public $exclude_same_category;
    
    public $orderBy;
    public $orderWay;
    
    public function getProductList()
    {
        if (empty($this->attribute_group)) {
            return array();
        }
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        
        $ipa = Tools::getValue('id_product_attribute');
        if (!$ipa) {
            $ipa = $this->product->cache_default_attribute;
        }
        
        $cats  = $this->product->getCategories();
        $default_cat = $this->product->id_category_default;
        
        if (empty($this->category_restrictions) || array_intersect($cats, $this->category_restrictions)) {
            $sql = 'SELECT pac.* FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac
                LEFT JOIN ' . _DB_PREFIX_ . 'attribute a ON a.id_attribute = pac.id_attribute
                WHERE pac.id_product_attribute = ' .  (int)$ipa . ' AND a.id_attribute_group = ' . (int)$this->attribute_group;

            $res = Db::getInstance()->getRow($sql);
            if (empty($res)) {
                return array();
            }
            
            $id_attribute = $res['id_attribute'];
            
            $sql = 'SELECT p.*, pl.*, pa.id_product_attribute AS cache_default_attribute FROM ' . _DB_PREFIX_ . 'product p
                LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product = p.id_product
                LEFT JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = p.id_product
                LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON pa.id_product = p.id_product
                LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pac.id_product_attribute = pa.id_product_attribute
                LEFT JOIN ' . _DB_PREFIX_ . 'attribute a ON a.id_attribute = pac.id_attribute
                LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON al.id_attribute = a.id_attribute 
                LEFT JOIN ' . _DB_PREFIX_ . 'stock_available sa ON sa.id_product = p.id_product AND sa.id_product_attribute = pa.id_product_attribute ';
            if ($this->category_restrictions2 || $this->exclude_same_category || $this->same_category_only) {
                $sql .= ' LEFT JOIN ' . _DB_PREFIX_ . 'category_product cp ON cp.id_product = p.id_product ';
            }
            $sql .= ' WHERE p.id_product != ' . $this->product->id . ' AND a.id_attribute_group = ' . (int)$this->attribute_group . ' AND a.id_attribute = ' . (int)$id_attribute
                . ' AND al.id_lang = ' . (int)$id_lang
                . ' AND pl.id_lang = ' . (int)$id_lang
                . ' AND ps.id_shop = ' . (int)$id_shop
                . ' AND pl.id_shop = ' . (int)$id_shop;
            if ($this->category_restrictions2) {
                $sql .= ' AND cp.id_category IN (' . implode(', ', $this->category_restrictions2) . ') ';
            }
            if ($this->same_category_only) {
                $sql .= ' AND cp.id_category = ' . (int)$default_cat . ' AND p.id_category_default = ' . (int)$default_cat . ' ';
            } else {
                if ($this->exclude_same_category) {
                    $sql .= ' AND cp.id_category != ' . (int)$default_cat . ' AND p.id_category_default != ' . (int)$default_cat . ' ';
                }
            }
            if ($this->instock) {
                $sql .= ' AND (p.quantity > 0 OR sa.quantity > 0)';
            }
            $sql .= ' GROUP BY p.id_product LIMIT ' . $this->limit;
            
            if ($res = Db::getInstance()->executeS($sql)) {
                return Product::getProductsProperties($id_lang, $res);
            }
        }
        
        return array();
    }
    
    public function getOrderOptions()
    {
        return array(
            'orderBy' => array(
                'rand' => 'Random',
                'price' => 'Price',
                'date_add' => 'Date add',
                'sales' => 'Sales count'
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
            'date_add:asc' => 'Date add ASC',
            'date_add:desc' => 'Date add DESC',
            'name:asc' => 'Product name ASC',
            'name:desc' => 'Product name DESC',
            'manufacturer_name:asc' => 'Manufacturer name ASC',
            'manufacturer_name:desc' => 'Manufacturer name DESC',
            'sales:asc' => 'Sales count ASC',
            'sales:desc' => 'Sales count DESC',
        );
    }
    
    public function getMoreLink()
    {
        if ($this->more_url) {
            return strtr($this->more_url, array(
                '{lang}' => Context::getContext()->language->iso_code
            ));
        }
        return false;
    }
    
    public function rules()
    {
        return array(
            'required' => array(
                'limit',
                'attribute_group'
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
        return 'Same attribute products';
    }
}
