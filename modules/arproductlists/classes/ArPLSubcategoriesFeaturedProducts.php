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

include_once dirname(__FILE__).'/ArPLChildCategories.php';

class ArPLSubcategoriesFeaturedProducts extends ArPLChildCategories
{
    public $limit;
    public $more_link;
    public $more_url;
    
    public $orderBy;
    public $orderWay;
    
    public function getProductList()
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        $controller = Context::getContext()->controller;
        
        $category = null;
        if (isset($controller->php_self) && $controller->php_self == 'category') {
            $category = $controller->getCategory();
        } elseif (Tools::getValue('category_id') && ($controller instanceof ArProductListsAjaxModuleFrontController)) {
            $category = new Category(Tools::getValue('category_id'), $id_lang);
        }
        
        if (!isset($category) || empty($category)) {
            return array();
        }
        $defaultCat = $category->id;
        
        $childCategories = array();
        $categories = $this->getChildrenCategories($defaultCat, $id_lang);
        foreach ($categories as $category) {
            $childCategories[] = $category['id_category'];
        }
        if (empty($childCategories)) {
            return array();
        }
        $homeFeaturedCategory = Configuration::get('HOME_FEATURED_CAT');
        
        $q = 'SELECT pc.id_product FROM ' . _DB_PREFIX_ . 'category_product pc LEFT JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = pc.id_product WHERE pc.id_category = ' . (int)$homeFeaturedCategory . ' AND p.active = 1 ORDER BY pc.position';
        
        $rows = Db::getInstance()->executeS($q);
        if (empty($rows)) {
            return array();
        }
        $productIds = array();
        foreach ($rows as $row) {
            $productIds[] = $row['id_product'];
        }
        
        $q = 'SELECT pc.id_product FROM ' . _DB_PREFIX_ . 'category_product pc WHERE id_product IN (' . implode(',', $productIds) . ') AND id_category IN (' . implode(',', $childCategories) . ')';
        
        $rows = Db::getInstance()->executeS($q);
        if (empty($rows)) {
            return array();
        }
        $productIds = array();
        foreach ($rows as $row) {
            $productIds[] = $row['id_product'];
        }
        
        $products = $this->getProducts($productIds, $id_lang, 0, $this->limit, $this->orderBy == 'rand'? null : $this->orderBy, $this->orderBy == 'rand'? null : $this->orderWay, false, true, true, null, array());
        if ($products && $this->orderBy == 'rand') {
            shuffle($products);
        }
        return $products;
    }
    
    public function getProducts(
        $ids,
        $idLang,
        $p,
        $n,
        $orderBy = null,
        $orderWay = null,
        $getTotal = false,
        $active = true,
        $activeCategory = true,
        Context $context = null,
        $exclude = array()
    ) {
        if (!$context) {
            $context = Context::getContext();
        }
        
        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        if ($p < 1) {
            $p = 1;
        }

        if (empty($orderBy) || $orderBy == 'position') {
            $orderBy = 'name';
        }

        if (empty($orderWay)) {
            $orderWay = 'ASC';
        }

        if (!Validate::isOrderBy($orderBy) || !Validate::isOrderWay($orderWay)) {
            die(Tools::displayError());
        }

        $groups = FrontController::getCurrentCustomerGroups();
        $sqlGroups = count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= 1';

        /* Return only the number of products */
        if ($getTotal) {
            $sql = '
				SELECT p.`id_product`
				FROM `' . _DB_PREFIX_ . 'product` p
				' . Shop::addSqlAssociation('product', 'p') . '
				WHERE p.id_product  IN(' . implode(', ', $ids) . ') '
                . ($active ? ' AND product_shop.`active` = 1' : '') . '
				' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
                                ' . ($exclude? ' AND p.`id_product` NOT IN (' . implode(', ', $exclude) . ')' : '') . '
				AND EXISTS (
					SELECT 1
					FROM `' . _DB_PREFIX_ . 'category_group` cg
					LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_category` = cg.`id_category`)' .
                    ($activeCategory ? ' INNER JOIN `' . _DB_PREFIX_ . 'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '') . '
					WHERE p.`id_product` = cp.`id_product` AND cg.`id_group` ' . $sqlGroups . '
				)';

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

            return (int) count($result);
        }
        if (strpos($orderBy, '.') > 0) {
            $orderBy = explode('.', $orderBy);
            $orderBy = pSQL($orderBy[0]) . '.`' . pSQL($orderBy[1]) . '`';
        }

        if ($orderBy == 'price') {
            $alias = 'product_shop.';
        } elseif ($orderBy == 'name') {
            $alias = 'pl.';
        } elseif ($orderBy == 'manufacturer_name') {
            $orderBy = 'name';
            $alias = 'm.';
        } elseif ($orderBy == 'quantity') {
            $alias = 'stock.';
        } else {
            $alias = 'p.';
        }

        if (version_compare(_PS_VERSION_, '1.6.1', '>=') === true) {
            $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity'
                . (Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute' : '') . '
                            , pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`,
                            pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
                                    DATEDIFF(
                                            product_shop.`date_add`,
                                            DATE_SUB(
                                                    "' . date('Y-m-d') . ' 00:00:00",
                                                    INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
                                            )
                                    ) > 0 AS new'
                . ' FROM `' . _DB_PREFIX_ . 'product` p
                            ' . Shop::addSqlAssociation('product', 'p') .
                (Combination::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
                                                    ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')' : '') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                                    ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl') . ')
                                    LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                                            ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
                                    ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $idLang . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m
                                    ON (m.`id_manufacturer` = p.`id_manufacturer`)
                            ' . Product::sqlStock('p', 0);
        } else {
            $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity'
                . (Combination::isFeatureActive() ? ', product_attribute.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute.`id_product_attribute`,0) id_product_attribute' : '') . '
                            , pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`,
                            pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
                                    DATEDIFF(
                                            product_shop.`date_add`,
                                            DATE_SUB(
                                                    "' . date('Y-m-d') . ' 00:00:00",
                                                    INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
                                            )
                                    ) > 0 AS new'
                . ' FROM `' . _DB_PREFIX_ . 'product` p
                            ' . Shop::addSqlAssociation('product', 'p') .
                (Combination::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` product_attribute
                                                    ON (p.`id_product` = product_attribute.`id_product` AND product_attribute.`default_on` = 1)' : '') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                                    ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl') . ')
                                    LEFT JOIN `' . _DB_PREFIX_ . 'image` image
                                            ON (image.`id_product` = p.`id_product` AND image.cover=1)
                            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
                                    ON (image.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $idLang . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m
                                    ON (m.`id_manufacturer` = p.`id_manufacturer`)
                            ' . Product::sqlStock('p', 0);
        }

        if (Group::isFeatureActive() || $activeCategory) {
            $sql .= 'JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (p.id_product = cp.id_product)';
            if (Group::isFeatureActive()) {
                $sql .= 'JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.`id_category` = cg.`id_category` AND cg.`id_group` ' . $sqlGroups . ')';
            }
            if ($activeCategory) {
                $sql .= 'JOIN `' . _DB_PREFIX_ . 'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1';
            }
        }

        $sql .= '
				WHERE p.id_product  IN(' . implode(', ', $ids) . ') 
				' . ($active ? ' AND product_shop.`active` = 1' : '') . '
				' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
                                ' . ($exclude? ' AND p.`id_product` NOT IN (' . implode(', ', $exclude) . ')' : '') . '
				GROUP BY p.id_product
				ORDER BY ' . $alias . '`' . bqSQL($orderBy) . '` ' . pSQL($orderWay) . '
				LIMIT ' . (((int) $p - 1) * (int) $n) . ',' . (int) $n;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($orderBy == 'price') {
            Tools::orderbyPrice($result, $orderWay);
        }
        
        if ($this->instock) {
            foreach ($result as $k => $product) {
                if (Product::getQuantity($product['id_product'], $product['cache_default_attribute']) < 1) {
                    unset($result[$k]);
                }
            }
        }

        return Product::getProductsProperties($idLang, $result);
    }
    
    public function rules()
    {
        return array(
            'required' => array(
                'limit'
            )
        );
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
        return 'Subcategories featured products';
    }
}
