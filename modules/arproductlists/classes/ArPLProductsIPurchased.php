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

class ArPLProductsIPurchased extends ArPLListAbstract
{
    public $limit;
    
    public $orderBy;
    public $orderWay;
    
    public function getProductList()
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        $id_customer = Context::getContext()->customer->id;
        
        if (empty($id_customer)) {
            return array();
        }
        
        $ids = $this->getProductIds($id_customer, $id_shop);
        
        if ($sortOrder = $this->getSortOrder()) {
            $this->orderBy = $sortOrder[0];
            $this->orderWay = $sortOrder[1];
        }
        
        $conditions = array(
            'p.id_product IN (' . implode(', ', $ids) . ')'
        );
        
        $orderBy = $this->orderBy == 'rand'? null : $this->orderBy;
        $orderWay = $this->orderBy == 'rand'? null : $this->orderWay;
        $random = $this->orderBy == 'rand'? true : false;
        $products = $this->getProducts2($id_lang, $id_shop, $this->limit, $conditions, array(), $orderBy, $orderWay, false, true, $random);
        
        
        return $products;
    }
    
    protected function getProductIds($id_customer, $id_shop)
    {
        $res = array();
        
        $sql = 'SELECT DISTINCT(od.product_id) FROM ' . _DB_PREFIX_ . 'order_detail od
            LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON o.id_order = od.id_order
            WHERE o.id_customer = ' . (int)$id_customer . ' AND o.id_shop = ' . (int)$id_shop . ' LIMIT ' . $this->limit;
        $rows = Db::getInstance()->executeS($sql);
        
        foreach ($rows as $row) {
            $res[] = $row['product_id'];
        }
        return $res;
    }
    
    public function getOrderOptions()
    {
        return array(
            'orderBy' => array(
                'rand' => 'Random',
                'price' => 'Price',
                'id_product' => 'Product ID',
                'name' => 'Product name',
                'manufacturer_name' => 'Manufacturer name',
                'position' => 'Position',
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
            'manufacturer_name:desc' => 'Manufacturer name DESC',
            'position:asc' => 'Position ASC',
            'position:desc' => 'Position DESC',
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
        return 'Products I purchased';
    }
}
