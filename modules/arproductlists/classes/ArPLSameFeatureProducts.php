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
class ArPLSameFeatureProducts extends ArPLListAbstract
{
    public $limit;
    public $loop;
    public $more_link;
    public $more_url;
    
    public $id_feature;
    public $same_category_only;
    public $exclude_same_category;
    
    public $orderBy;
    public $orderWay;
    
    public function getProductList()
    {
        if (empty($this->id_feature)) {
            return array();
        }
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        
        if ($sortOrder = $this->getSortOrder()) {
            $this->orderBy = $sortOrder[0];
            $this->orderWay = $sortOrder[1];
        }
        $random = $this->orderBy == 'rand'? true : false;
        
        $ipa = Tools::getValue('id_product_attribute');
        if (!$ipa) {
            $ipa = $this->product->cache_default_attribute;
        }
        
        $cats  = $this->product->getCategories();
        $default_cat = $this->product->id_category_default;
        
        if (empty($this->category_restrictions) || array_intersect($cats, $this->category_restrictions)) {
            $join = array(
                'LEFT JOIN `' . _DB_PREFIX_ . 'feature_product` fp ON fp.id_product = p.id_product'
            );
            
            if ($this->category_restrictions2 || $this->exclude_same_category || $this->same_category_only) {
                $join[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON cp.id_product = p.id_product';
            }
            
            $productFeatures = $this->product->getFeatures();
            $featureValue = null;
            
            $isCustomValue = false;
            $featureValueObject = null;
            foreach ($productFeatures as $feature) {
                if ($feature['id_feature'] == $this->id_feature) {
                    $featureValue = $feature['id_feature_value'];
                    if ($feature['custom']) {
                        $isCustomValue = true;
                        $featureValueObject = new FeatureValue($featureValue, $id_lang);
                    }
                }
            }
            
            if ($featureValue === null) {
                return array();
            }
            
            $conditions = array(
                'p.id_product != ' . $this->product->id
            );
            if ($isCustomValue) {
                if ($featureValueObject === null || !Validate::isLoadedObject($featureValueObject)) {
                    return array();
                }
                $sql = 'SELECT fv.* FROM `' . _DB_PREFIX_ . 'feature_value` fv '
                        . ' LEFT JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl ON fvl.id_feature_value = fv.id_feature_value '
                        . ' WHERE fv.id_feature = ' . $this->id_feature . ' AND fv.custom = 1 AND fvl.id_lang = ' . (int)$id_lang . ' AND fvl.value = "' . pSQL($featureValueObject->value) . '"';
                $rows = Db::getInstance()->executeS($sql);
                $featureValues = array();
                foreach ($rows as $row) {
                    $featureValues[] = $row['id_feature_value'];
                }
                if (!$featureValues) {
                    return array();
                }
                $conditions[] = 'fp.id_feature = ' . (int)$this->id_feature . ' AND fp.id_feature_value IN (' . implode(', ', $featureValues) . ')';
            } else {
                $conditions[] = 'fp.id_feature = ' . (int)$this->id_feature . ' AND fp.id_feature_value = "' . pSQL($featureValue) . '"';
            }
            
            if ($this->category_restrictions2) {
                $conditions[] = 'cp.id_category IN (' . implode(', ', $this->category_restrictions2) . ')';
            }
            if ($this->same_category_only) {
                $conditions[] = 'cp.id_category = ' . (int)$default_cat . ' AND p.id_category_default = ' . (int)$default_cat;
            } else {
                if ($this->exclude_same_category) {
                    $conditions[] = 'cp.id_category != ' . (int)$default_cat . ' AND p.id_category_default != ' . (int)$default_cat;
                }
            }
            
            return $this->getProducts2($id_lang, $id_shop, $this->limit, $conditions, $join, $this->orderBy, $this->orderWay, true, true, $random, true, null, array(
                'p.id_product'
            ));
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
                'id_feature'
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
        return 'Same feature products';
    }
}
