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

class ArPLCustomBrands extends ArPLListAbstract
{
    public $grid;
    public $grid_md;
    public $grid_sm;
    public $brand_thumb_size;
    public $cat_title;
    public $brand_ids;
    
    public function getBrandsList()
    {
        $id_lang = Context::getContext()->language->id;
        
        $res = array();
        if (empty($this->brand_ids)) {
            return $res;
        }
        foreach ($this->brand_ids as $id) {
            $brand = new Manufacturer($id, $id_lang);
            if (Validate::isLoadedObject($brand)) {
                $res[] = $brand;
            }
        }
        return $res;
    }


    public function getCategoriesList()
    {
        return array();
    }
    
    public function rules()
    {
        return array(
            'required' => array(
                'brand_thumb_size',
                'brand_ids'
            )
        );
    }
    
    public function getProductList()
    {
        return array();
    }
    
    public function isProductList()
    {
        return false;
    }
    
    public function isCategoryList()
    {
        return false;
    }
    
    public function isBrandList()
    {
        return true;
    }
    
    public static function getTypeTitle()
    {
        return 'Custom brands';
    }
}
