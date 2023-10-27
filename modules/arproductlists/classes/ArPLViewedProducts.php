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

class ArPLViewedProducts extends ArPLListAbstract
{
    public $limit;
    
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
        
        $products = array();
        
        if ($this->orderBy == 'pv.view_date') {
            $sql = 'SELECT DISTINCT(pv.id_product) FROM `' . ArProductViews::getTableName() . '` pv '
                    . 'WHERE pv.ip = "' . Tools::getRemoteAddr() . '"'
                    . 'ORDER BY pv.view_date ' . $this->orderWay . ' LIMIT ' . $this->limit;
            if ($rows = Db::getInstance()->executeS($sql)) {
                foreach ($rows as $row) {
                    $conditions = array(
                        'p.id_product = ' . $row['id_product']
                    );
                    if ($productsTmp = $this->getProducts2($id_lang, $id_shop, $this->limit, $conditions, array(), 'p.id_product', $orderWay, false, true, $random)) {
                        $products[] = reset($productsTmp);
                    }
                }
            }
        } else {
            $sql = 'SELECT DISTINCT(pv.id_product) FROM `' . ArProductViews::getTableName() . '` pv '
                    . 'LEFT JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = pv.id_product '
                    . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = p.id_product '
                    . 'WHERE pv.ip = "' . Tools::getRemoteAddr() . '" AND p.active = 1 AND ps.active = 1 AND p.id_product IS NOT NULL AND ps.id_shop = ' . (int)$id_shop;
            if ($rows = Db::getInstance()->executeS($sql)) {
                $ids = array();
                foreach ($rows as $row) {
                    $ids[] = $row['id_product'];
                }
                $conditions = array(
                    'p.id_product IN(' . implode(', ', $ids) . ')'
                );
                $products = $this->getProducts2($id_lang, $id_shop, $this->limit, $conditions, array(), $orderBy, $orderWay, false, true, $random);
            }
        }
        
        
        return Product::getProductsProperties($id_lang, $products);
    }
    
    protected function getViewedProductIds()
    {
        $ids = array_reverse(explode(',', Context::getContext()->cookie->arpl_viewed));
        $res = array();
        foreach ($ids as $id) {
            if ($id) {
                $res[] = $id;
            }
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
                'pv.view_date' => 'View time'
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
            'pv.view_date:asc' => 'View time ASC',
            'pv.view_date:desc' => 'View time DESC',
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
        return 'Viewed products';
    }
}
