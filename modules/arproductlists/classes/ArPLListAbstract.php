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

include_once dirname(__FILE__).'/ArPLSliderConfig.php';
include_once dirname(__FILE__).'/ArPLTools.php';

/**
 * @property ArProductList $owner
 */
abstract class ArPLListAbstract
{
    private $errors = array();
    
    protected $owner;
    protected $product;

    public $ajax;
    public $product_update;
    public $view;
    public $drag;
    public $center;
    public $loop;
    public $dots;
    public $controls;
    public $autoplay;
    public $autoplayTimeout;
    public $instock;
    public $slide_by;
    public $sortorder;
    public $category_restrictions = array();
    public $category_restrictions2 = array();
            
    public $titleAlign;
    public $responsiveBaseElement;
    public $responsiveBreakdowns;
    
    public $shop_filter;
    
    public $custom_js;
    
    const VIEW_SLIDER = 1;
    const VIEW_STANDARD = 2;
    const VIEW_COMPACT = 3;
    
    public function __construct($owner)
    {
        $this->owner = $owner;
        $this->assignAttributes();
    }
    
    public function getChildrenCategoriesRecoursive($idParent, $idLang, $active = true, $idShop = false)
    {
        $res = array();
        if ($cats = $this->getChildrenCategories($idParent, $idLang, $active, $idShop)) {
            foreach ($cats as $cat) {
                $res[] = $cat['id_category'];
                $res = array_merge($res, $this->getChildrenCategoriesRecoursive($cat['id_category'], $idLang, $active, $idShop));
            }
        }
        return $res;
    }
    
    /**
     * Get children of the given Category.
     *
     * @param int $idParent Parent Category ID
     * @param int $idLang Language ID
     * @param bool $active Active children only
     * @param bool $idShop Shop ID
     *
     * @return array Children of given Category
     */
    public function getChildrenCategories($idParent, $idLang, $active = true, $idShop = false)
    {
        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        $cacheId = 'Category::getChildren_' . (int) $idParent . '-' . (int) $idLang . '-' . (bool) $active . '-' . (int) $idShop;
        if (!Cache::isStored($cacheId)) {
            $query = 'SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`, category_shop.`id_shop`
			FROM `' . _DB_PREFIX_ . 'category` c
			LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . ')
			' . Shop::addSqlAssociation('category', 'c') . '
			WHERE `id_lang` = ' . (int) $idLang . '
			AND c.`id_parent` = ' . (int) $idParent . '
			' . ($active ? 'AND `active` = 1' : '') . '
			GROUP BY c.`id_category`
			ORDER BY category_shop.`position` ASC LIMIT ' . (int)$this->limit;
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }
    
    public function getProducts2($id_lang, $id_shop, $limit, $conditions = array(), $join = array(), $orderBy = null, $orderWay = null, $getTotal = false, $active = true, $random = false, $checkAccess = true, $context = null, $groupBy = array())
    {
        $joins = array();
        $where = array();

        if (empty($orderBy) || $orderBy == 'position') {
            $orderBy = 'name';
        }

        if (empty($orderWay)) {
            $orderWay = 'ASC';
        }
        
        if (strpos($orderBy, '.') > 0) {
            $orderBy = explode('.', $orderBy);
            $orderBy = pSQL($orderBy[0]) . '.' . pSQL($orderBy[1]) . '';
        }

        if ($orderBy == 'price') {
            if ($this->shop_filter) {
                $alias = '';
            } else {
                $alias = '';
            }
            $orderBy = 'actual_price';
        } elseif ($orderBy == 'name') {
            $alias = 'pl.';
        } elseif ($orderBy == 'manufacturer_name') {
            $orderBy = 'name';
            $alias = 'm.';
        } elseif ($orderBy == 'quantity') {
            $alias = 'stock.';
        } elseif (strpos($orderBy, '.')) {
            $d = explode('.', $orderBy);
            $alias = $d[0] . '.';
            $orderBy = $d[1];
        } else {
            $alias = 'p.';
        }
        
        $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON pl.id_product = p.id_product';
        $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.id_manufacturer = p.id_manufacturer';
        if ($this->shop_filter) {
            $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` pa ON pa.id_product = p.id_product AND pa.id_product_attribute = p.cache_default_attribute AND pa.id_shop = ' . (int)$id_shop;
        } else {
            $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product = p.id_product AND pa.id_product_attribute = p.cache_default_attribute';
        }
        
        
        if ($this->is17() || $this->is8x()) {
            $sql = 'SELECT p.*, pl.*, m.name as manufacturer_name, (IF (p.price = 0, pa.price, p.price)) AS actual_price FROM `' . _DB_PREFIX_ . 'product` p ';
        } elseif ($this->is16()) {
            if (version_compare(_PS_VERSION_, '1.6.1', '>=') === true) {
                $sql = 'SELECT p.*, pl.*, image_shop.id_image, image_shop.cover, il.*, m.name as manufacturer_name, (IF (p.price = 0, pa.price, p.price)) AS actual_price FROM `' . _DB_PREFIX_ . 'product` p ';
                $joins[] = 'LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$id_shop.')';
                $joins[] = 'LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')';
            } else {
                $sql = 'SELECT p.*, pl.*, image.id_image, image.cover, il.*, m.name as manufacturer_name, (IF (p.price = 0, pa.price, p.price)) AS actual_price FROM `' . _DB_PREFIX_ . 'product` p ';
                $joins[] = 'LEFT JOIN `'._DB_PREFIX_.'image` image ON (image.`id_product` = p.`id_product` AND image.cover=1)';
                $joins[] = 'LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')';
            }
        }
        
        $where[] = 'pl.id_lang = ' . (int)$id_lang;
        if ($this->shop_filter) {
            $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON ps.id_product = p.id_product';
            $where[] = 'ps.id_shop = ' . (int)$id_shop;
            $where[] = 'ps.`visibility` IN ("both", "catalog")';
            if ($active) {
                $where[] = 'ps.active = 1';
            }
        } else {
            $where[] = 'p.`visibility` IN ("both", "catalog")';
        }
        if (!$this->shop_filter && $active) {
            $where[] = 'p.active = 1';
        }
        $where[] = 'pl.id_shop = ' . (int)$id_shop;
        $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa ON sa.id_product = p.id_product AND sa.id_product_attribute = p.cache_default_attribute AND sa.id_shop = ' . (int)$id_shop;
        if ($this->instock) {
            $where[] = '(sa.quantity > 0)';
        }
        if ($conditions) {
            $where = array_merge($where, $conditions);
        }
        if ($join) {
            $joins = array_merge($joins, $join);
        }
        $sql .= PHP_EOL . implode(" " . PHP_EOL, $joins);
        $sql .= PHP_EOL . ' WHERE ' . implode(' AND ', $where);
        
        if ($groupBy) {
            $sql .= PHP_EOL . ' GROUP BY ' . implode(', ', $groupBy);
        }
        
        if ($random) {
            $sql .= ' ORDER BY RAND() ';
        } elseif ($orderBy && $orderWay) {
            $sql .= ' ORDER BY ' . $alias . '`' . bqSQL($orderBy) . '` ' . pSQL($orderWay);
        }
        if ($limit) {
            $sql .= ' LIMIT ' . (int)$limit;
        }
        
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        
        if (!$result) {
            return array();
        }
        
        if ($orderBy == 'price') {
            Tools::orderbyPrice($result, $orderWay);
        }
        
        $products = Product::getProductsProperties($id_lang, $result);
        return $products;
    }
    
    public function getBestSales($id_lang, $id_shop, $limit, $conditions = array(), $join = array(), $orderBy = null, $orderWay = null)
    {
        $where = array();
        $joins = array();
        $context = Context::getContext();
        
        $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON ps.`id_product` = p.`id_product`';
        $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa ON sa.id_product = p.id_product AND sa.id_product_attribute = p.cache_default_attribute';
        
        if ($this->instock) {
            $where[] = '(sa.quantity > 0)';
        }
        
        $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'product_shop` product_shop ON product_shop.id_product = p.id_product';
        $where[] = 'product_shop.id_shop = ' . (int)$id_shop;
        $where[] = 'product_shop.`visibility` IN ("both", "catalog")';
        $where[] = 'product_shop.active = 1';
        
        $where[] = 'pl.id_lang = ' . (int)$id_lang;
        $where[] = 'pl.id_shop = ' . (int)$id_shop;
        
        $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON p.`id_product` = pl.`id_product`';
        $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`) AND tr.`id_country` = ' . (int) $context->country->id . ' AND tr.`id_state` = 0';
        $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'tax` t ON (t.`id_tax` = tr.`id_tax`)';
        $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)';
        
        $finalOrderBy = $orderBy;
        $orderTable = '';

        $invalidOrderBy = !Validate::isOrderBy($orderBy);
        if ($invalidOrderBy || is_null($orderBy)) {
            $orderBy = 'quantity';
            $orderTable = 'ps';
        }

        if ($orderBy == 'date_add' || $orderBy == 'date_upd') {
            $orderTable = 'product_shop';
        }

        $invalidOrderWay = !Validate::isOrderWay($orderWay);
        if ($invalidOrderWay || is_null($orderWay)) {
            $orderWay = 'DESC';
        }

        $interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;

        if ($this->is17() || $this->is8x()) {
            $sql = 'SELECT p.*, product_shop.*, IFNULL(sa.quantity, 0) as quantity,'
                . ' pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
                pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
                m.`name` AS manufacturer_name, p.`id_manufacturer` as id_manufacturer,
                image_shop.`id_image` id_image, il.`legend`,
                ps.`quantity` AS sales, t.`rate`, pl.`meta_keywords`, pl.`meta_title`, pl.`meta_description`,
                DATEDIFF(p.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
                INTERVAL ' . (int) $interval . ' DAY)) > 0 AS new FROM `' . _DB_PREFIX_ . 'product_sale` ps ';
            $joins[] = 'LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$id_shop.')';
            $joins[] = 'LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')';
        } elseif ($this->is16()) {
            if (version_compare(_PS_VERSION_, '1.6.1', '>=') === true) {
                $sql = 'SELECT p.*, product_shop.*, IFNULL(sa.quantity, 0) as quantity,'
                    . ' pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
                    pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
                    m.`name` AS manufacturer_name, p.`id_manufacturer` as id_manufacturer,
                    image_shop.`id_image` id_image, il.`legend`,
                    ps.`quantity` AS sales, t.`rate`, pl.`meta_keywords`, pl.`meta_title`, pl.`meta_description`,
                    DATEDIFF(p.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
                    INTERVAL ' . (int) $interval . ' DAY)) > 0 AS new FROM `' . _DB_PREFIX_ . 'product_sale` ps ';
                $joins[] = 'LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$id_shop.')';
                $joins[] = 'LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')';
            } else {
                $sql = 'SELECT p.*, product_shop.*, IFNULL(sa.quantity, 0) as quantity,'
                    . ' pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
                    pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
                    m.`name` AS manufacturer_name, p.`id_manufacturer` as id_manufacturer,
                    image.`id_image` id_image, il.`legend`,
                    ps.`quantity` AS sales, t.`rate`, pl.`meta_keywords`, pl.`meta_title`, pl.`meta_description`,
                    DATEDIFF(p.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
                    INTERVAL ' . (int) $interval . ' DAY)) > 0 AS new FROM `' . _DB_PREFIX_ . 'product_sale` ps ';
                $joins[] = 'LEFT JOIN `'._DB_PREFIX_.'image` image ON (image.`id_product` = p.`id_product` AND image.cover=1)';
                $joins[] = 'LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')';
            }
        }

        if ($conditions) {
            $where = array_merge($where, $conditions);
        }
        if ($join) {
            $joins = array_merge($joins, $join);
        }
        
        $sql .= PHP_EOL . implode(" " . PHP_EOL, $joins);
        
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql .= ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
                        JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= 1') . ')
                        WHERE cp.`id_product` = p.`id_product`)';
        }
        
        $sql .= PHP_EOL . ' WHERE ' . implode(' AND ', $where) . ' GROUP BY ps.id_product ';

        
        
        if ($finalOrderBy != 'price') {
            $sql .= ' ORDER BY ' . (!empty($orderTable) ? '`' . pSQL($orderTable) . '`.' : '') . '`' . pSQL($orderBy) . '` ' . pSQL($orderWay) . ' LIMIT ' . (int)$limit;
        }
        
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        
        if ($finalOrderBy == 'price') {
            Tools::orderbyPrice($result, $orderWay);
        }
        if (!$result) {
            return array();
        }
        return Product::getProductsProperties($id_lang, $result);
    }
    
    public function isSlider()
    {
        return $this->view == self::VIEW_SLIDER;
    }
    
    public function isStandard()
    {
        return $this->view == self::VIEW_STANDARD;
    }
    
    public function isCompact()
    {
        return $this->view == self::VIEW_COMPACT;
    }
    
    public function getDefaultBreakdowns()
    {
        $breakdowns = Configuration::get('ARPLS_BREAKDOWNS');
        if (empty($breakdowns)) {
            $breakdowns = ArPLSliderConfig::getDefaultBreakdownsStatic();
        }
        return $breakdowns;
    }
    
    public function validate()
    {
        $this->errors = array();
        $rules = $this->rules();
        if (empty($rules)) {
            return true;
        }
        foreach ($rules as $rule => $attrs) {
            foreach ($attrs as $attr) {
                $method = 'validate' .Tools::ucfirst($rule);
                if (method_exists($this, $method)) {
                    $this->$method($attr);
                }
            }
        }
        return empty($this->errors);
    }
    
    public function validateRequired($attr)
    {
        if (property_exists($this, $attr)) {
            if (is_string($this->$attr)) {
                $value = trim($this->$attr);
            } else {
                $value = $this->$attr;
            }
            if (empty($value)) {
                $this->errors['list_' . $attr] = 'This field is required';
            }
        }
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function rules()
    {
        return array();
    }
    
    public function getResponsiveBreakdowns($jsonEncodedString = false)
    {
        $result = array();
        if (!$this->responsiveBreakdowns) {
            $breakdowns = explode(PHP_EOL, $this->getDefaultBreakdowns());
        } else {
            $breakdowns = explode("\n", $this->responsiveBreakdowns);
        }
        
        foreach ($breakdowns as $breakdown) {
            $v = trim($breakdown);
            if (preg_match('{^(\d+):(\d+)$}is', $v, $matches)) {
                $result["$matches[1]"] = array(
                    'items' => $matches[2]
                );
            }
        }
        
        if ($jsonEncodedString) {
            return json_encode($result, JSON_FORCE_OBJECT + JSON_NUMERIC_CHECK);
        }
        
        return $result;
    }
    
    public function setProduct($product)
    {
        if ($product instanceof ProductCore || $product instanceof Product) {
            $this->product = $product;
        } elseif (is_object($product)) {
            $this->product = new Product($product->id, false, Context::getContext()->language->id);
        }
    }
    
    protected function assignAttributes()
    {
        if ($this->owner) {
            $data = ArPLTools::jsonDecode($this->owner->data);
            foreach ($data as $k => $v) {
                if (property_exists(get_called_class(), $k)) {
                    $this->$k = $v;
                }
            }
        }
    }
    
    public function getProductList()
    {
        return array();
    }
    
    public function getCategoriesList()
    {
        return array();
    }
    
    public function getMoreLink()
    {
        return null;
    }
    
    public function getOrderOptions()
    {
        return null;
    }
    
    public function getFrontendOrderOptions()
    {
        return array();
    }
    
    public function getSortOrder()
    {
        $sortOrder = trim(Tools::getValue('sortOrder'));
        if (trim($sortOrder) && in_array($sortOrder, array_keys($this->getFrontendOrderOptions()))) {
            return explode(':', $sortOrder);
        }
        return null;
    }
    
    public function getCurrentCategoryId()
    {
        return (int)Tools::getValue('category_id');
    }
    
    public function getCurrentProductId()
    {
        return (int)Tools::getValue('product_id');
    }
    
    public function getDefaultSortOrder()
    {
        return null;
    }


    abstract public function isProductList();
    abstract public function isCategoryList();
    abstract public function isBrandList();
    public static function getTypeTitle()
    {
        return '';
    }
    
    public function is16()
    {
        if ((version_compare(_PS_VERSION_, '1.6.0', '>=') === true)
                && (version_compare(_PS_VERSION_, '1.7.0', '<') === true)) {
            return true;
        }
        return false;
    }
    
    public function is17()
    {
        if ((version_compare(_PS_VERSION_, '1.7.0', '>=') === true)
                && (version_compare(_PS_VERSION_, '1.8.0', '<') === true)) {
            return true;
        }
        return false;
    }
    
    public function is8x()
    {
        if ((version_compare(_PS_VERSION_, '8.0.0', '>=') === true)
                && (version_compare(_PS_VERSION_, '9.0.0', '<') === true)) {
            return true;
        }
        return false;
    }
}
