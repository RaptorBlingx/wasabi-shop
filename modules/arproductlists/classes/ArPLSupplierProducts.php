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

class ArPLSupplierProducts extends ArPLListAbstract
{
    public $limit;
    public $more_link;
    public $more_url;
    public $id_supplier;
    
    public $orderBy;
    public $orderWay;
    
    public function getProductList()
    {
        $id_lang = Context::getContext()->language->id;
        $products = $this->getProducts($this->id_supplier, $id_lang, 0, $this->limit, $this->orderBy == 'rand'? null : $this->orderBy, $this->orderBy == 'rand'? null : $this->orderWay, false, true, true);
        if ($products && $this->orderBy == 'rand') {
            shuffle($products);
        }
        return $products;
    }
    
    public function getMoreLink()
    {
        if ($this->more_url) {
            return strtr($this->more_url, array(
                '{lang}' => Context::getContext()->language->iso_code
            ));
        }
        return Context::getContext()->link->getSupplierLink($this->id_supplier);
    }
    
    public function rules()
    {
        return array(
            'required' => array(
                'limit',
                'id_supplier'
            )
        );
    }
    
    public function getOrderOptions()
    {
        return array(
            'orderBy' => array(
                'rand' => 'Random',
                'price' => 'Price',
                'id_product' => 'Product ID',
                'name' => 'Product name',
                'manufacturer_name' => 'Manufacturer name'
            ),
            'orderWay' => array(
                'asc' => 'ASC',
                'desc' => 'DESC',
            )
        );
    }
    
    /**
     * @param $idSupplier
     * @param $idLang
     * @param $p
     * @param $n
     * @param null $orderBy
     * @param null $orderWay
     * @param bool $getTotal
     * @param bool $active
     * @param bool $activeCategory
     *
     * @return array|bool
     */
    public function getProducts(
        $idSupplier,
        $idLang,
        $p,
        $n,
        $orderBy = null,
        $orderWay = null,
        $getTotal = false,
        $active = true,
        $activeCategory = true
    ) {
        $context = Context::getContext();
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

        $sqlGroups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sqlGroups = 'WHERE cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= 1');
        }

        /* Return only the number of products */
        if ($getTotal) {
            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(DISTINCT ps.`id_product`)
			FROM `' . _DB_PREFIX_ . 'product_supplier` ps
			JOIN `' . _DB_PREFIX_ . 'product` p ON (ps.`id_product`= p.`id_product`)
			' . Shop::addSqlAssociation('product', 'p') . '
			WHERE ps.`id_supplier` = ' . (int) $idSupplier . '
			AND ps.id_product_attribute = 0
			' . ($active ? ' AND product_shop.`active` = 1' : '') . '
			' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
			AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `' . _DB_PREFIX_ . 'category_product` cp
				' . (Group::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.`id_category` = cg.`id_category`)' : '') . '
				' . ($activeCategory ? ' INNER JOIN `' . _DB_PREFIX_ . 'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '') . '
				' . $sqlGroups . '
			)');
        }

        $nbDaysNewProduct = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;

        if (strpos('.', $orderBy) > 0) {
            $orderBy = explode('.', $orderBy);
            $orderBy = pSQL($orderBy[0]) . '.`' . pSQL($orderBy[1]) . '`';
        }
        $alias = '';
        if (in_array($orderBy, array('price', 'date_add', 'date_upd'))) {
            $alias = 'product_shop.';
        } elseif ($orderBy == 'id_product') {
            $alias = 'p.';
        } elseif ($orderBy == 'manufacturer_name') {
            $orderBy = 'name';
            $alias = 'm.';
        }
        if (version_compare(_PS_VERSION_, '1.6.1', '>=') === true) {
            $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock,
                                            IFNULL(stock.quantity, 0) as quantity,
                                            pl.`description`,
                                            pl.`description_short`,
                                            pl.`link_rewrite`,
                                            pl.`meta_description`,
                                            pl.`meta_keywords`,
                                            pl.`meta_title`,
                                            pl.`name`,
                                            image_shop.`id_image` id_image,
                                            il.`legend`,
                                            s.`name` AS supplier_name,
                                            DATEDIFF(p.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00", INTERVAL ' . ($nbDaysNewProduct) . ' DAY)) > 0 AS new,
                                            m.`name` AS manufacturer_name' . (Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute' : '') . '
                                     FROM `' . _DB_PREFIX_ . 'product` p
                                    ' . Shop::addSqlAssociation('product', 'p') . '
                                    JOIN `' . _DB_PREFIX_ . 'product_supplier` ps ON (ps.id_product = p.id_product
                                            AND ps.id_product_attribute = 0) ' .
                    (Combination::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
                                    ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')' : '') . '
                                    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product`
                                            AND pl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl') . ')
                                    LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                                            ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
                                    LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image`
                                            AND il.`id_lang` = ' . (int) $idLang . ')
                                    LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON s.`id_supplier` = p.`id_supplier`
                                    LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                                    ' . Product::sqlStock('p', 0);
        } else {
            $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock,
                                            IFNULL(stock.quantity, 0) as quantity,
                                            pl.`description`,
                                            pl.`description_short`,
                                            pl.`link_rewrite`,
                                            pl.`meta_description`,
                                            pl.`meta_keywords`,
                                            pl.`meta_title`,
                                            pl.`name`,
                                            image.`id_image` id_image,
                                            il.`legend`,
                                            s.`name` AS supplier_name,
                                            DATEDIFF(p.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00", INTERVAL ' . ($nbDaysNewProduct) . ' DAY)) > 0 AS new,
                                            m.`name` AS manufacturer_name' . (Combination::isFeatureActive() ? ', product_attribute.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute.id_product_attribute,0) id_product_attribute' : '') . '
                                     FROM `' . _DB_PREFIX_ . 'product` p
                                    ' . Shop::addSqlAssociation('product', 'p') . '
                                    JOIN `' . _DB_PREFIX_ . 'product_supplier` ps ON (ps.id_product = p.id_product
                                            AND ps.id_product_attribute = 0) ' .
                    (Combination::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` product_attribute
                                    ON (p.`id_product` = product_attribute.`id_product` AND product_attribute.`default_on` = 1)' : '') . '
                                    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product`
                                            AND pl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl') . ')
                                    LEFT JOIN `' . _DB_PREFIX_ . 'image` image
                                            ON (image.`id_product` = p.`id_product` AND image.cover=1)
                                    LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image.`id_image` = il.`id_image`
                                            AND il.`id_lang` = ' . (int) $idLang . ')
                                    LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON s.`id_supplier` = p.`id_supplier`
                                    LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                                    ' . Product::sqlStock('p', 0);
        }

        if (Group::isFeatureActive() || $activeCategory) {
            $sql .= 'JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (p.id_product = cp.id_product)';
            if (Group::isFeatureActive()) {
                $sql .= 'JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.`id_category` = cg.`id_category` AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= 1') . ')';
            }
            if ($activeCategory) {
                $sql .= 'JOIN `' . _DB_PREFIX_ . 'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1';
            }
        }

        $sql .= '
				WHERE ps.`id_supplier` = ' . (int) $idSupplier . '
					' . ($active ? ' AND product_shop.`active` = 1' : '') . '
					' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
				GROUP BY ps.id_product
				ORDER BY ' . $alias . pSQL($orderBy) . ' ' . pSQL($orderWay) . '
				LIMIT ' . (((int) $p - 1) * (int) $n) . ',' . (int) $n;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

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
        return 'Supplier products';
    }
}
