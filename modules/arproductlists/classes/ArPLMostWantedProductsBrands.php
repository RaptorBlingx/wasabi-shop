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

class ArPLMostWantedProductsBrands extends ArPLListAbstract
{
    public $days;
    public $more_link;
    public $more_url;
    public $grid;
    public $grid_md;
    public $grid_sm;
    public $brand_thumb_size;
    public $cat_title;
    public $limit;
    
    public function getBrandsList()
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
        
        $sql = 'SELECT id_product, count(id_product) as carts
            FROM `'._DB_PREFIX_.'cart_product`
            WHERE `date_add` between "' . pSQL($start) . '" AND "' . pSQL($end) . '" AND id_shop = ' . (int)$id_shop . '
            GROUP BY id_product
            ORDER BY carts DESC';
        
        $ids = array();
        if ($rows = Db::getInstance()->executeS($sql)) {
            foreach ($rows as $row) {
                $ids[] = $row['id_product'];
            }
        }
        if ($ids) {
            $sql = 'SELECT DISTINCT(id_manufacturer) FROM `' . _DB_PREFIX_ . 'product` WHERE id_product IN (' . implode(',', $ids) . ') LIMIT ' . (int)$this->limit;
            if ($rows = Db::getInstance()->executeS($sql)) {
                foreach ($rows as $row) {
                    $brand = new Manufacturer($row['id_manufacturer'], $id_lang);
                    if (Validate::isLoadedObject($brand)) {
                        $res[] = $brand;
                    }
                }
            }
        }
        
        return $res;
    }
    
    public function rules()
    {
        return array(
            'required' => array(
                'brand_thumb_size',
                'limit'
            )
        );
    }
    
    public function isCategoryList() {
        return false;
    }
    
    public function isProductList() {
        return false;
    }
    
    public function isBrandList() {
        return true;
    }
    
    public static function getTypeTitle()
    {
        return 'Brands of most wanted products';
    }
}
