<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_'))
	exit;
class Ets_mp_product extends ObjectModel
{
    public $id_product;
    public $price;
    public $id_tax_rules_group;
    public $wholesale_price;
    public $unity;
    public $amount_tax;
    public $unit_price_ratio;
    public $unit_price;
    public $additional_shipping_cost;
    public $reference;
    public $upc;
    public $isbn;
    public $ean13;
    public $location;
    public $width;
    public $height;
    public $depth;
    public $weight;
    public $is_virtual;
    public $filed_change;
    public $description;
    public $description_short;
    public $link_rewrite;
    public $meta_description;
    public $meta_keywords;
    public $meta_title;
    public $name;
    public $available_now;
    public $available_later;
    public $delivery_in_stock;
    public $delivery_out_stock;
    public $date_add;
    public $date_upd;
    public $status = -1;
    public $decline;
    public static $definition = array(
        'table' => 'ets_mp_product',
        'primary' => 'id_ets_mp_product',
        'multilang' => true,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'unit_price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'id_tax_rules_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'wholesale_price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'unity' => array('type' => self::TYPE_STRING,  'validate' => 'isString'),
            'unit_price_ratio' => array('type' => self::TYPE_FLOAT),
            'additional_shipping_cost' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'reference' => array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 64),
            'upc' => array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 64),
            'isbn' => array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 64),
            'ean13' => array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 64),
            'location' => array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 64),
            'width' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'height' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'depth' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'weight' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'is_virtual' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'filed_change' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_add' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_upd' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'decline' => array('type' => self::TYPE_STRING),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 512),
            'meta_keywords' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => false, 'size' => 128),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 128),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'description_short' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'available_now' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'available_later' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'IsGenericName', 'size' => 255),
            'delivery_in_stock' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'delivery_out_stock' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255,),
        )
    );

    public function __construct($id_item = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_item, $id_lang, $id_shop);
    }

    public static function checkExistLinkWrite($link_rewrite, $id_lang, $id_product)
    {
        return (int)Db::getInstance()->getValue('SELECT id_product FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_product!="' . (int)$id_product . '" AND id_lang="' . (int)$id_lang . '" AND link_rewrite LIKE "' . pSQL($link_rewrite) . '"');
    }

    public static function getMpProductByIdProduct($id_product)
    {
        if ($id_ets_mp_product = Db::getInstance()->getValue('SELECT id_ets_mp_product FROM `' . _DB_PREFIX_ . 'ets_mp_product` WHERE id_product="' . (int)$id_product . '"')) {
            return new Ets_mp_product($id_ets_mp_product);
        } else
            return new Ets_mp_product();
    }

    public static function getListImages($id_product)
    {
        if ($id_product) {
            return Db::getInstance()->executeS('
                SELECT i.* FROM `' . _DB_PREFIX_ . 'image` i
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_product_image` mpi ON (mpi.id_image = i.id_image)
                WHERE i.id_product=' . (int)$id_product . ' OR mpi.id_product="' . (int)$id_product . '" ORDER BY i.position ASC'
            );
        }
        return array();
    }

    public static function getCover($id_product)
    {
        return Db::getInstance()->getValue('
            SELECT i.id_image FROM `' . _DB_PREFIX_ . 'image` i
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_product_image` mpi ON (mpi.id_image = i.id_image)
            WHERE (i.id_product=' . (int)$id_product . ' OR mpi.id_product="' . (int)$id_product . '") AND i.cover=1');
    }

    public static function CheckImage($id_image, $id_product)
    {
        return Db::getInstance()->getValue('
            SELECT i.id_image FROM `' . _DB_PREFIX_ . 'image` i
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_product_image` mpi ON (mpi.id_image = i.id_image)
            WHERE (i.id_product=' . (int)$id_product . ' OR mpi.id_product="' . (int)$id_product . '") AND i.id_image=' . (int)$id_image);
    }

    public static function getSellerByIdProduct($id_product)
    {
        $sql = 'SELECT seller.id_seller FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product` p
        INNER JOIN `' . _DB_PREFIX_ . 'customer` customer ON (customer.id_customer=p.id_customer)
        INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller` seller ON (customer.id_customer=seller.id_customer)
        WHERE p.id_product=' . (int)$id_product;
        return (int)Db::getInstance()->getValue($sql);
    }

    public static function getImageLink($id_product, $id_product_attribute, $type_image)
    {
        $id_image = 0;
        if ($id_product_attribute) {
            $sql = 'SELECT i.id_image FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
            INNER JOIN `' . _DB_PREFIX_ . 'image` i ON (i.id_image = pai.id_image AND i.cover=1)
            WHERE pai.id_product_attribute=' . (int)$id_product_attribute;
            $id_image = (int)Db::getInstance()->getValue($sql);
            if (!$id_image) {
                $sql = 'SELECT i.id_image FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
                INNER JOIN `' . _DB_PREFIX_ . 'image` i ON (i.id_image = pai.id_image)
                WHERE pai.id_product_attribute=' . (int)$id_product_attribute;
                $id_image = (int)Db::getInstance()->getValue($sql);

            }
        }
        if (!$id_image) {
            $sql = 'SELECT id_image FROM `' . _DB_PREFIX_ . 'image` WHERE id_product="' . (int)$id_product . '" AND cover=1';
            $id_image = (int)Db::getInstance()->getValue($sql);
        }
        if (!$id_image)
            $id_image = (int)Db::getInstance()->getValue('SELECT id_image FROM `' . _DB_PREFIX_ . 'image` WHERE id_product=' . (int)$id_product);
        if ($id_image) {
            $context = Context::getContext();
            $product = new Product($id_product, false, $context->language->id);
            return Module::getInstanceByName('ets_marketplace')->displayText('', 'img', '', '', '', '', $context->link->getImageLink($product->link_rewrite, $id_image, $type_image));
        }
        return '';
    }

    public static function getProductImages($id_product)
    {
        return Db::getInstance()->executeS('SELECT image.* FROM `' . _DB_PREFIX_ . 'image` image
        INNER JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.id_image = image.id_image ) 
        WHERE image_shop.id_product= ' . (int)$id_product . ' AND image_shop.id_shop="' . (int)Context::getContext()->shop->id . '" ORDER BY image.position ASC');
    }

    public static function declineProductSeller($id_product, $reason)
    {
        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_mp_seller_product`  SET approved="-2",reason="' . pSQL($reason) . '" WHERE id_product=' . (int)$id_product);
    }

    public static function checkSellerUpdateStatusProduct($id_product)
    {
        if (!$id_product) {
            if (Configuration::get('ETS_MP_SELLER_PRODUCT_APPROVE_REQUIRED'))
                return false;
            else
                return true;
        } else {
            $approved = (int)Db::getInstance()->getValue('SELECT approved FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product`  WHERE id_product=' . (int)$id_product);
            if ($approved == 1)
                return true;
            else
                return false;
        }

    }

    public function adminUpdateStatus()
    {
        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_mp_product`  set status ="' . (int)$this->status . '",decline="' . pSQL($this->decline) . '" WHERE id_ets_mp_product=' . (int)$this->id);
    }

    public static function getListtags($id_product, $id_lang = 0)
    {
        if ($id_product) {
            $sql = 'SELECT t.name FROM `' . _DB_PREFIX_ . 'product_tag` pt
            INNER JOIN `' . _DB_PREFIX_ . 'tag` t ON (pt.id_tag = t.id_tag AND pt.id_lang= pt.id_lang)
            WHERE pt.id_product="' . (int)$id_product . '" AND pt.id_lang="' . ($id_lang ?: (int)Context::getContext()->language->id) . '"';
            $tags = Db::getInstance()->executeS($sql);
            if ($tags) {
                $text = '';
                foreach ($tags as $tag)
                    $text .= $tag['name'] . ',';
                return trim($text, ',');
            } else
                return '';
        }
        return '';
    }

    public static function deleteProducttag($id_product, $id_lang)
    {
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'product_tag` WHERE id_product="' . (int)$id_product . '" AND id_lang=' . (int)$id_lang);
    }

    public static function getLocation($id_product, $id_product_attribute = null, $id_shop = null)
    {
        $id_product = (int)$id_product;

        if (null === $id_product_attribute) {
            $id_product_attribute = 0;
        } else {
            $id_product_attribute = (int)$id_product_attribute;
        }

        $query = new DbQuery();
        $query->select('location');
        $query->from('stock_available');
        $query->where('id_product = ' . (int)$id_product);
        $query->where('id_product_attribute = ' . (int)$id_product_attribute);

        $query = StockAvailable::addSqlShopRestriction($query, $id_shop);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public static function setLocation($id_product, $location, $id_shop = null, $id_product_attribute = 0)
    {
        if (
            false === Validate::isUnsignedId($id_product)
            || (((false === Validate::isUnsignedId($id_shop)) && (null !== $id_shop)))
            || (false === Validate::isUnsignedId($id_product_attribute))
            || (false === Validate::isString($location))
        ) {
            $serializedInputData = [
                'id_product' => $id_product,
                'id_shop' => $id_shop,
                'id_product_attribute' => $id_product_attribute,
                'location' => $location,
            ];

            throw new \InvalidArgumentException(sprintf(
                'Could not update location as input data is not valid: %s',
                json_encode($serializedInputData)
            ));
        }

        $existing_id = StockAvailable::getStockAvailableIdByProductId($id_product, $id_product_attribute, $id_shop);

        if ($existing_id > 0) {
            Db::getInstance()->update(
                'stock_available',
                array('location' => $location),
                'id_product = ' . (int)$id_product .
                (($id_product_attribute) ? ' AND id_product_attribute = ' . (int)$id_product_attribute : '') .
                StockAvailable::addSqlShopRestriction(null, $id_shop)
            );
        } else {
            $params = array(
                'location' => $location,
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute,
            );

            StockAvailable::addSqlShopParams($params, $id_shop);
            Db::getInstance()->insert('stock_available', $params, false, true, Db::ON_DUPLICATE_KEY);
        }
    }

    public static function deleteMpProduct($id_product)
    {
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product` WHERE id_product=' . (int)$id_product);
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_rule` WHERE reduction_product="' . (int)$id_product . '"');
        if ($id_ets_mp_product = Db::getInstance()->getValue('SELECT id_ets_mp_product FROM `' . _DB_PREFIX_ . 'ets_mp_product` WHERE id_product=' . (int)$id_product)) {
            $mpProduct = new Ets_mp_product($id_ets_mp_product);
            $mpProduct->delete();
        }
    }

    public static function getProductSellerByIDProduct($id_product, $is_admin = false, $getIDCustomer = false)
    {
        $seller_product = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product` WHERE id_product=' . (int)$id_product . ($is_admin !== false ? ' AND is_admin=' . (int)$is_admin : ''));
        if ($getIDCustomer) {
            if ($seller_product)
                return $seller_product['id_customer'];
            else
                return 0;
        } else
            return $seller_product;
    }

    public static function updateStatus($id_product, $active, $admin = false)
    {
        if ($active) {
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_mp_seller_product` SET active=1 ' . ($admin ? ',approved=1' : '') . ' WHERE id_product=' . (int)$id_product);
        } else {
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_mp_seller_product` SET active=0' . ($admin ? ',approved=0' : '') . ' WHERE active=1 AND id_product=' . (int)$id_product);
        }
    }

    public static function getTrendingProducts($nbProducts, $day = 0,$id_ets_css_sub_category=0)
    {
        if ($day)
            $date = strtotime("-$day day", strtotime(date('Y-m-d')));
        $active = true;
        $front = true;
        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        $id_lang = (int)Context::getContext()->language->id;
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        $prev_version = version_compare(_PS_VERSION_, '1.6.1.0', '<');
        $order_by = ' total_sale DESC';
        $sql = 'SELECT DISTINCT p.*,count(DISTINCT od.id_order) as total_sale ,product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' . ($prev_version ? ' ,IFNULL(product_attribute_shop.id_product_attribute, 0)' : ' ,MAX(product_attribute_shop.id_product_attribute)') . ' id_product_attribute, pl.`description`, pl.`description_short`, pl.`available_now`,
    					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, IFNULL(image_shop.`id_image`, i.`id_image`) id_image,
    					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
    					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
    					INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice
                FROM `' . _DB_PREFIX_ . 'category_product` cp
                INNER JOIN `' . _DB_PREFIX_ . 'order_detail` od ON (od.product_id = cp.id_product)
                INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON (od.id_order=o.id_order)
                LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = cp.`id_product`
                ' . Shop::addSqlAssociation('product', 'p') .
            ' LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('cl') . ')' .
            (!$prev_version ?
                'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (pa.id_product = p.id_product)' . Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on=1') . '' :
                'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int)Context::getContext()->shop->id . ')'
            ) .
            (
            $id_ets_css_sub_category && Validate::isUnsignedId($id_ets_css_sub_category) ? ' LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp2 ON (cp2.id_product=p.id_product)' : ''
            )
            . Product::sqlStock('p', 0, false, Context::getContext()->shop) . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . ')' .
            (!$prev_version ?
                'LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`)' . Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover = 1') :
                'LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.id_shop=' . (int)Context::getContext()->shop->id . ')'
            ) . '
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$id_lang . ')	
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                WHERE ' . ($day ? 'o.date_add >="' . pSQL(date('Y-m-d', $date)) . '"' : '1') . ' AND  product_shop.`id_shop` = ' . (int)Context::getContext()->shop->id
            . ($active ? ' AND product_shop.`active` = 1' : '')
            . ($id_ets_css_sub_category && Validate::isUnsignedId($id_ets_css_sub_category) ? ' AND cp2.id_category="' . (int)$id_ets_css_sub_category . '"' : '')
            . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
            . (Context::getContext()->cart->id ? ' AND product_shop.id_product NOT IN (SELECT id_product FROM `' . _DB_PREFIX_ . 'cart_product` WHERE id_cart="' . (int)Context::getContext()->cart->id . '")' : '')
            . ' GROUP BY p.id_product'
            . (($order_by) ? ($order_by != 'rand' ? ' ORDER BY ' . pSQL($order_by) : ' ORDER BY RAND()') : '') . '
                LIMIT  0,' . (int)$nbProducts;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getFeatureProducts($id_product)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'feature_product` WHERE id_product=' . (int)$id_product;
        $feature_products = Db::getInstance()->executeS($sql);
        if ($feature_products) {
            foreach ($feature_products as &$feature_product) {
                $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'feature_value` fv
                        LEFT JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl ON (fv.id_feature_value = fvl.id_feature_value AND fvl.id_lang="' . (int)Context::getContext()->language->id . '")
                    WHERE fv.id_feature = "' . (int)$feature_product['id_feature'] . '" AND fv.custom=0';
                $feature_product['feature_values'] = Db::getInstance()->executeS($sql);
                $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'feature_value` fv
                        LEFT JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl ON (fv.id_feature_value = fvl.id_feature_value AND fvl.id_lang="' . (int)Context::getContext()->language->id . '")
                    WHERE fv.id_feature = "' . (int)$feature_product['id_feature'] . '" AND fv.id_feature_value="' . (int)$feature_product['id_feature_value'] . '"';
                $feature_product['feature_value'] = Db::getInstance()->getRow($sql);
            }
        }
        return $feature_products;
    }

    public static function getProductAttributeName($id_product_attribute, $small = false)
    {
        $sql = 'SELECT a.id_attribute,al.name,agl.name as group_name FROM `' . _DB_PREFIX_ . 'attribute` a
            INNER JOIN `' . _DB_PREFIX_ . 'attribute_shop` attribute_shop ON (a.id_attribute= attribute_shop.id_attribute AND attribute_shop.id_shop="' . (int)Context::getContext()->shop->id . '")
            INNER JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (a.id_attribute=pac.id_attribute)
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.id_attribute=al.id_attribute AND al.id_lang="' . (int)Context::getContext()->language->id . '")
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (a.id_attribute_group= agl.id_attribute_group AND agl.id_lang="' . (int)Context::getContext()->language->id . '")
            WHERE pac.id_product_attribute ="' . (int)$id_product_attribute . '"
        ';
        $attributes = Db::getInstance()->executeS($sql);
        $name_attribute = '';
        if ($attributes) {
            foreach ($attributes as $attribute) {
                if ($small)
                    $name_attribute .= $attribute['name'] . ' - ';
                else
                    $name_attribute .= $attribute['group_name'] . ' - ' . $attribute['name'] . ', ';
            }
        }
        return $small ? trim($name_attribute, ' - ') : trim($name_attribute, ', ');
    }

    public static function getSellerProducts($filter = '', $page = 0, $per_page = 12, $order_by = 'p.id_product desc', $total = false)
    {
        $page = (int)$page;
        if ($page <= 0)
            $page = 1;
        $per_page = (int)$per_page;
        if ($per_page <= 0)
            $per_page = 12;
        $front = false;
        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        $id_lang = (int)Context::getContext()->language->id;
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        $prev_version = version_compare(_PS_VERSION_, '1.6.1.0', '<');
        if (!$total)
            $sql = 'SELECT DISTINCT p.*,seller_report.total_reported,sp.approved, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' . ($prev_version ? ' ,IFNULL(product_attribute_shop.id_product_attribute, 0)' : ' ,MAX(product_attribute_shop.id_product_attribute)') . ' id_product_attribute, pl.`description`, pl.`description_short`, pl.`available_now`,
    					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`,
    					il.`legend` as legend, m.`name` AS manufacturer_name,cl.name as default_category,CONCAT(customer.firstname," ",customer.lastname) as seller_name,customer.id_customer as id_customer_seller,seller.id_seller,sp.id_product as id_seller_product, seller_lang.shop_name,product_shop.`date_add`,seller.vacation_mode,
    					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
    					INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice,IFNULL(mp.id_product,0) as wait_change,mp.date_upd as date_submited,mp.status decline_change';
        else
            $sql = 'SELECT COUNT(DISTINCT p.id_product) ';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') .
            (!$prev_version ?
                'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (pa.id_product = p.id_product)' . Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on=1') . '' :
                'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int)Context::getContext()->shop->id . ')'
            )
            . Product::sqlStock('p', 0, false, Context::getContext()->shop) . '
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` sp ON (sp.id_product=p.id_product)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_product` mp ON (mp.id_product = p.id_product)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_product_image` mpi ON (mpi.id_product=p.id_product) 
                LEFT JOIN `' . _DB_PREFIX_ . 'customer` customer ON (sp.id_customer=customer.id_customer)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller` seller ON (customer.id_customer=seller.id_customer)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_lang` seller_lang ON (seller_lang.id_seller=seller.id_seller AND seller_lang.id_lang="' . (int)Context::getContext()->language->id . '")
                LEFT JOIN `' . _DB_PREFIX_ . 'category` c ON (c.id_category=p.id_category_default)
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.id_category = cl.id_category AND cl.id_lang="' . (int)$id_lang . '")
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . ')' .
            'LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product` AND i.cover=1)
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$id_lang . ')	
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                LEFT JOIN (
                    SELECT r.id_product,COUNT(r.id_customer) as total_reported FROM `' . _DB_PREFIX_ . 'ets_mp_seller_report` r WHERE id_product!=0 GROUP BY r.id_product 
                ) seller_report ON (seller_report.id_product = sp.id_product)
                WHERE product_shop.`id_shop` = ' . (int)Context::getContext()->shop->id . (version_compare(_PS_VERSION_, '1.7', '>=') ? ' AND p.state=1' : '') . ($filter ? (string)$filter : '') . '
                '
            . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '');
        if ($total)
            return Db::getInstance()->getValue($sql);
        else
            $sql .= ' GROUP BY p.id_product' . ($order_by ? ' ORDER BY ' . pSQL($order_by) : '') . ' LIMIT ' . (int)($page - 1) * $per_page . ',' . (int)$per_page;
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, true);
        if (!$products) {
            return array();
        }
        if ($order_by == 'product_shop.price asc') {
            Tools::orderbyPrice($products, 'asc');
        } elseif ($order_by == 'product_shop.price desc') {
            Tools::orderbyPrice($products, 'desc');
        }
        return $products;
    }

    public static function addProductSeller($id_product, $id_customer,$is_admin=1)
    {
        if (($product = new Product($id_product)) && (!Validate::isLoadedObject(new Customer($id_customer)) || !Ets_mp_seller::_getSellerByIdCustomer($id_customer) || !Validate::isLoadedObject($product) || Ets_mp_product::getProductSellerByIDProduct($id_product, 0))) {
            return false;
        } else {

            if (Ets_mp_seller::getIDCustomerSellerByIdProduct($id_product)) {
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_mp_seller_product` SET id_customer ="' . (int)$id_customer . '" WHERE id_product="' . (int)$product->id . '"');
            } else
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_mp_seller_product`(id_customer,id_product,active,approved,is_admin) VALUES("' . (int)$id_customer . '","' . (int)$product->id . '","' . (int)$product->active . '","' . (int)$product->active . '","'.(int)$is_admin.'")');
            return true;
        }

    }
    public static function addCategoryProduct($id_category,$id_product)
    {
        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'category_product` WHERE id_category="'.(int)$id_category.'" AND id_product="'.(int)$id_product.'"'))
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'category_product`(id_category,id_product) VALUES("'.(int)$id_category.'","'.(int)$id_product.'")');
    }
    public static function deleteProductSeller($id_product)
    {
        if (($product = new Product($id_product)) && (!Validate::isLoadedObject($product) || !self::getProductSellerByIDProduct($id_product, 1))) {
            return false;
        } else {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product` WHERE id_product=' . (int)$id_product);
            return true;
        }
    }

    public static function getImageByIDProduct($id_product,$cover= false)
    {
        return (int)Db::getInstance()->getValue('SELECT id_image FROM `'._DB_PREFIX_.'image` WHERE id_product='.(int)$id_product.($cover ? ' AND cover=1':''));
    }
    public static function getAllOldImageProductChange($id_product)
    {
        $sql = 'SELECT i.id_image FROM `'._DB_PREFIX_.'image` i
            WHERE i.id_product='.(int)$id_product;
        return Db::getInstance()->executeS($sql);
    }
    public static function getAllNewImageProductChange($id_product)
    {
        $sql = 'SELECT i.id_image FROM `'._DB_PREFIX_.'image` i
        INNER JOIN `'._DB_PREFIX_.'ets_mp_product_image` mpi ON (i.id_image = mpi.id_image)
        WHERE i.id_product=0 AND mpi.id_product='.(int)$id_product;
        $mpImages = Db::getInstance()->executeS($sql);
        if($mpImages)
        {
            return $mpImages;
        }
        return array();
    }
    public static function getBestSellingProducts($id_customer_seller=0)
    {
        if(Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'))
        {
            $status = explode(',',Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'));
            $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
            $id_lang = (int)Context::getContext()->language->id;
            if (!Validate::isUnsignedInt($nb_days_new_product))
                $nb_days_new_product = 20;
            $prev_version = version_compare(_PS_VERSION_, '1.6.1.0', '<');
            $sql = 'SELECT DISTINCT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' . ($prev_version? ' ,IFNULL(product_attribute_shop.id_product_attribute, 0)':' ,MAX(product_attribute_shop.id_product_attribute)') . ' id_product_attribute, pl.`description`, pl.`description_short`, pl.`available_now`,
    			product_sale.quantity quantity_sale, pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`,
    			il.`legend` as legend, m.`name` AS manufacturer_name,cl.name as default_category,CONCAT(customer.firstname," ",customer.lastname) as seller_name,customer.id_customer as id_customer_seller,seller.id_seller, seller_lang.shop_name,product_shop.`date_add`,
    			DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
    			INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice'.($id_customer_seller ? ',commission.commission':'');
            $sql .= ' FROM `'._DB_PREFIX_.'product` p
                    '.Shop::addSqlAssociation('product', 'p').
                (!$prev_version?
                    'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product = p.id_product)'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on=1').'':
                    'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)Context::getContext()->shop->id.')'
                )
                .Product::sqlStock('p', 0, false, Context::getContext()->shop).'
                    INNER JOIN (
                        SELECT od.product_id,sum(product_quantity) as quantity FROM `'._DB_PREFIX_.'order_detail` od
                        INNER JOIN `'._DB_PREFIX_.'orders` o ON (od.id_order=o.id_order)
                        WHERE o.current_state IN ('.implode(',',array_map('intval',$status)).')
                        GROUP BY od.product_id
                    ) as product_sale ON (product_sale.product_id=p.id_product)
                    INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_product` sp ON (sp.id_product=p.id_product '.($id_customer_seller ? ' AND sp.id_customer='.(int)$id_customer_seller:'').')
                    '.($id_customer_seller ? ' LEFT JOIN (SELECT id_product,id_customer,SUM(commission) as commission FROM `'._DB_PREFIX_.'ets_mp_seller_commission` WHERE id_customer="'.(int)$id_customer_seller.'" GROUP BY id_product,id_customer) commission ON (commission.id_product = p.id_product)':'').'
                    LEFT JOIN `'._DB_PREFIX_.'customer` customer ON (customer.id_customer=sp.id_customer)
                    LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller` seller ON (customer.id_customer=seller.id_customer)
                    LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_lang` seller_lang ON (seller_lang.id_seller=seller.id_seller AND seller_lang.id_lang="'.(int)$id_lang.'")
                    LEFT JOIN `'._DB_PREFIX_.'category` c ON (c.id_category=p.id_category_default)
                    LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.id_category = cl.id_category AND cl.id_lang="'.(int)$id_lang.'")
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . ')'.
                ' LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product` AND i.cover=1)
                    LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')	
                    LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                    WHERE product_shop.`id_shop` = ' . (int)Context::getContext()->shop->id ;
            $sql .= ' GROUP BY p.id_product ORDER BY product_sale.quantity DESC LIMIT 0,10';
            return Db::getInstance()->executeS($sql);
        }
        return array();
    }
    public static function getProductComment($id_comment)
    {
        if(Module::isInstalled('productcomments'))
            return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'product_comment` WHERE id_product_comment="'.(int)$id_comment.'"');
        return array();
    }
    public static function updateStatusProductComment($id_comment,$validate)
    {
        if(Module::isInstalled('productcomments'))
            return Db::getInstance()->execute('update `'._DB_PREFIX_.'product_comment` SET validate="'.(int)$validate.'" WHERE id_product_comment="'.(int)$id_comment.'"');
        return array();
    }
    public static function deleteProductComment($id_comment)
    {
        if(Module::isInstalled('productcomments'))
            return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_comment` WHERE id_product_comment="'.(int)$id_comment.'"');
        return array();
    }
    public static function approveChangedImageProduct($id_product,$errors)
    {
        if(!$errors)
        {
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'image` SET id_product="'.(int)$id_product.'" WHERE id_product=0
            AND id_image IN (SELECT id_image FROM `'._DB_PREFIX_.'ets_mp_product_image` WHERE id_product="'.(int)$id_product.'")');
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'image_shop` SET id_product="'.(int)$id_product.'" WHERE id_product=0
            AND id_image IN (SELECT id_image FROM `'._DB_PREFIX_.'ets_mp_product_image` WHERE id_product="'.(int)$id_product.'")');
                Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_mp_product_image` WHERE id_product='.(int)$id_product);
        }
    }
    public static function getIDProductByLinkWrite($link_rewrite)
    {
        return Db::getInstance()->getValue('SELECT id_product FROM `'._DB_PREFIX_.'product_lang` WHERE link_rewrite="'.pSQL($link_rewrite).'"');
    }
    public static function getMaxIdProduct()
    {
        return Db::getInstance()->getValue('SELECT MAX(id_product) FROM `'._DB_PREFIX_.'product`');
    }
    public static function getIndexableAttributeGroup($id_attribute_group)
    {
        if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered')) {
            return Db::getInstance()->getValue('SELECT indexable FROM `' . _DB_PREFIX_ . 'layered_indexable_attribute_group` WHERE id_attribute_group=' . (int)$id_attribute_group);
        }
        return '';
    }
    public static function getLanguageSearchAttributeGroup($id_attribute_group,$id_lang)
    {
        if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered')) {
            return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'layered_indexable_attribute_group_lang_value` WHERE id_attribute_group="' . (int)$id_attribute_group . '" AND id_lang="' . (int)$id_lang . '"');
        }
        return '';
    }
    public static function getLanguageSearchAttribute($id_attribute,$id_lang)
    {
        if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered')) {
            return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'layered_indexable_attribute_lang_value` WHERE id_attribute="' . (int)$id_attribute . '" AND id_lang="' . (int)$id_lang . '"');
        }
        return '';
    }
    public static function getIndexAbleFeature($id_feature)
    {
        if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered')) {
            return (int)Db::getInstance()->getValue('SELECT indexable FROM `'._DB_PREFIX_.'layered_indexable_feature` WHERE id_feature='.(int)$id_feature);
        }
        return '';
    }
    public static function getLanguageSearchFeature($id_feature,$id_lang)
    {
        if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered')) {
            return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'layered_indexable_feature_lang_value` WHERE id_feature="'.(int)$id_feature.'" AND id_lang="'.(int)$id_lang.'"');
        }
        return '';
    }
    public static function getLanguageSearchFeatureValue($id_feature_value,$id_lang)
    {
        if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered')) {
            return Db::getInstance()->getRow('SELECT url_name FROM `'._DB_PREFIX_.'layered_indexable_feature_value_lang_value` WHERE id_feature_value="'.(int)$id_feature_value.'" AND id_lang="'.(int)$id_lang.'"');
        }
        return '';
    }

    public static function updateCarrierAssoShop($id_carrier)
    {
        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'carrier_shop` WHERE id_carrier="'.(int)$id_carrier.'" AND id_shop="'.(int)Context::getContext()->shop->id.'"'))
            return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'carrier_shop`(id_carrier,id_shop) VALUES("'.(int)$id_carrier.'","'.(int)Context::getContext()->shop->id.'")');
        else
            return true;
    }
    public static function getRangeCarrier($id_carrier,$shipping_method)
    {
        if($shipping_method==Carrier::SHIPPING_METHOD_PRICE)
            $range_prices = Db::getInstance()->executeS('SELECT id_range_price as id_range,delimiter1,delimiter2 FROM `'._DB_PREFIX_.'range_price` WHERE id_carrier="'.(int)$id_carrier.'"');
        else
            $range_weights = Db::getInstance()->executeS('SELECT id_range_weight as id_range,delimiter1,delimiter2 FROM `'._DB_PREFIX_.'range_weight` WHERE id_carrier="'.(int)$id_carrier.'"');
        $zones =Zone::getZones(true);
        $deliveries = array();
        if($zones)
        {
            foreach($zones as &$zone)
            {
                $zone['checked'] = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'carrier_zone` WHERE id_carrier="'.(int)$id_carrier.'" AND id_zone="'.(int)$zone['id_zone'].'"');
                if(isset($range_prices) && $range_prices)
                {
                    foreach($range_prices as $range_price)
                    {
                        $deliveries[$zone['id_zone']][$range_price['id_range']] = Db::getInstance()->getValue('SELECT price FROM `'._DB_PREFIX_.'delivery` WHERE id_zone="'.(int)$zone['id_zone'].'" AND id_carrier="'.(int)$id_carrier.'" AND id_range_price="'.(int)$range_price['id_range'].'"');
                    }
                }
                elseif(isset($range_weights) && $range_weights)
                {
                    foreach($range_weights as $range_weight)
                    {
                        $deliveries[$zone['id_zone']][$range_weight['id_range']] = Db::getInstance()->getValue('SELECT price FROM `'._DB_PREFIX_.'delivery` WHERE id_zone="'.(int)$zone['id_zone'].'" AND id_carrier="'.(int)$id_carrier.'" AND id_range_weight="'.(int)$range_weight['id_range'].'"');
                    }
                }
            }
        }
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        return array(
            'zones' => $zones,
            'currency'=> $currency,
            'ranges' => isset($range_prices) ? $range_prices : $range_weights,
            'deliveries' => $deliveries,
        );
    }
    public static function addDeliveryCarrierToAllShop($id_carrier)
    {
        return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'delivery` SET `id_shop_group` = NULL,id_shop=NULL WHERE `id_carrier` = "'.(int)$id_carrier.'"');
    }
    public static function resetAttributeDefault($id_product)
    {
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product_attribute` SET default_on=NULL WHERE id_product="'.(int)$id_product.'" AND default_on=1');
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product_attribute_shop` SET default_on=NULL WHERE id_product="'.(int)$id_product.'" AND default_on=1');
        return true;
    }
    public static function updateCartProduct($id_product,$id_product_attribute)
    {
        Db::getInstance()->execute('update `'._DB_PREFIX_.'cart_product`  set id_product_attribute="'.(int)$id_product_attribute.'" where id_product="'.(int)$id_product.'" AND id_product_attribute=0');
    }
    public static function setAttributesImpacts($id_product, $tab)
    {
        $attributes = array();
        foreach ($tab as $group) {
            foreach ($group as $attribute) {
                $price = 0;
                $weight = 0;
                $attributes[] = '('.(int)$id_product.', '.(int)$attribute.', '.(float)$price.', '.(float)$weight.')';
            }
        }

        return Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'attribute_impact` (`id_product`, `id_attribute`, `price`, `weight`)
		VALUES '.implode(',', $attributes).'
		ON DUPLICATE KEY UPDATE `price` = VALUES(price), `weight` = VALUES(weight)');
    }
    public static function getCustomizationFields($id_product)
    {
        $sql = 'SELECT id_customization_field FROM `'._DB_PREFIX_.'customization_field` WHERE id_product='.(int)$id_product.(version_compare(_PS_VERSION_, '1.7', '>=') ? ' AND is_deleted=0':'');
        $customizationFields = Db::getInstance()->executeS($sql);
        $objects = array();
        if($customizationFields)
        {
            foreach($customizationFields as $customizationField)
            {
                $objects[] = new CustomizationField($customizationField['id_customization_field']);
            }
        }
        return $objects;
    }
    public static function addRemoveAttachment($add,$id_product,$id_attachment){
        if($add)
        {
            if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'product_attachment`  WHERE id_product="'.(int)$id_product.'" AND id_attachment="'.(int)$id_attachment.'"'))
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_attachment`  (id_product,id_attachment) VALUES("'.(int)$id_product.'","'.(int)$id_attachment.'")');
        }
        else
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_attachment`  WHERE id_product="'.(int)$id_product.'" AND id_attachment="'.(int)$id_attachment.'"');
        return true;
    }
    public static function deleteCombinations($id_product,$attributes)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_attribute` WHERE id_product_attribute IN ('.implode(',',array_map('intval',$attributes)).') AND id_product='.(int)$id_product);
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_supplier` WHERE id_product_attribute IN ('.implode(',',array_map('intval',$attributes)).') AND id_product='.(int)$id_product);
        return true;
    }
    public static function updateCategoryProduct($id_product,$id_categories,$seller_categories)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_product` WHERE id_product='.(int)$id_product.(isset($seller_categories) && $seller_categories ? ' AND id_category IN ('.implode(',',array_map('intval',$seller_categories)).')':''));
        if($id_categories && Ets_marketplace::validateArray($id_categories,'isInt'))
        {
            foreach($id_categories as $id_category)
            {
                if(Validate::isUnsignedId($id_category) && Validate::isLoadedObject(new Category($id_category)))
                {
                    if(!isset($seller_categories) || !$seller_categories ||  (isset($seller_categories) && $seller_categories && in_array($id_category,$seller_categories)))
                        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'category_product` (id_product,id_category,position) VALUES("'.(int)$id_product.'","'.(int)$id_category.'","1")');
                }
            }
        }
    }
    public static function updateRelatedProducts($id_product,$related_products)
    {
        if($related_products && Ets_marketplace::validateArray($related_products,'isInt'))
        {
            foreach($related_products as $related_product)
            {
                if($related_product!=$id_product && Validate::isLoadedObject(new Product($related_product)) && !Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'accessory` WHERE id_product_1="'.(int)$id_product.'" AND id_product_2="'.(int)$related_product.'"'))
                {
                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'accessory`(id_product_1,id_product_2) VALUES("'.(int)$id_product.'","'.(int)$related_product.'")');
                }
            }
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'accessory` WHERE id_product_1="'.(int)$id_product.'" AND id_product_2 NOT IN ('.implode(',',array_map('intval',$related_products)).')');
        }
        else
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'accessory` WHERE id_product_1="'.(int)$id_product.'"');
    }
    public static function updateFeatureProduct($id_product,$id_features,$id_feature_values,$feature_value_custom)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'feature_product` WHERE id_product='.(int)$id_product);
        if($id_features && Ets_marketplace::validateArray($id_features,'isInt') && $id_feature_values && Ets_marketplace::validateArray($id_feature_values,'isInt') && $feature_value_custom && Ets_marketplace::validateArray($feature_value_custom))
        {
            foreach($id_features as $key=> $id_feature)
            {
                if($id_feature)
                {
                    if(isset($id_feature_values[$key]) && $id_feature_values[$key])
                    {
                        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'feature_product` WHERE id_product="'.(int)$id_product.'" AND id_feature = "'.(int)$id_feature.'" AND id_feature_value="'.(int)$id_feature_values[$key].'"'))
                        {
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'feature_product`(id_product,id_feature,id_feature_value) VALUES("'.(int)$id_product.'","'.(int)$id_feature.'","'.(int)$id_feature_values[$key].'")');
                        }
                    }
                    elseif(isset($feature_value_custom[$key]) && $feature_value_custom[$key])
                    {
                        $feature_value = new FeatureValue();
                        $feature_value->id_feature = $id_feature;
                        $feature_value->custom=1;
                        foreach(Language::getLanguages(false) as $language)
                            $feature_value->value[$language['id_lang']] = $feature_value_custom[$key];
                        if($feature_value->add())
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'feature_product`(id_product,id_feature,id_feature_value) VALUES("'.(int)$id_product.'","'.(int)$id_feature.'","'.(int)$feature_value->id.'")');
                    }
                }
            }
        }
    }
    public static function getHighestPositionImage($id_product)
    {
        return (int)Db::getInstance()->getValue('SELECT MAX(i.position) FROM `'._DB_PREFIX_.'image` i 
            LEFT JOIN `'._DB_PREFIX_.'ets_mp_product_image` mpi ON(i.id_image = mpi.id_image)
            WHERE i.id_product="'.(int)$id_product.'" AND mpi.id_product="'.(int)$id_product.'"');
    }
    public static function addProductImage($id_product,$id_image)
    {
        return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_product_image`(id_product,id_image) VALUES("'.(int)$id_product.'","'.(int)$id_image.'")');
    }
    public static function updatePackProduct($id_product,$product_type,$id_pack_products)
    {
        if($product_type==1)
        {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'pack` WHERE id_product_pack='.(int)$id_product);
            if($id_pack_products && Ets_marketplace::validateArray($id_pack_products))
            {
                foreach($id_pack_products as $inputPackItem)
                {
                    $packItem = explode('x',$inputPackItem);
                    $id_product_item = (int)$packItem[0];
                    $id_product_attribute_item = (int)$packItem[1];
                    $quantity_item = (int)$packItem[2];
                    if(Validate::isLoadedObject(new Product($id_product_item)))
                    {
                        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'pack` WHERE id_product_pack="'.(int)$id_product.'" AND id_product_item="'.(int)$id_product_item.'" AND id_product_attribute_item="'.(int)$id_product_attribute_item.'"'))
                        {
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'pack`(id_product_pack,id_product_item,id_product_attribute_item,quantity) VALUES("'.(int)$id_product.'","'.(int)$id_product_item.'","'.(int)$id_product_attribute_item.'","'.(int)$quantity_item.'")');
                        }
                    }

                }
            }

        }
        else
        {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'pack` WHERE id_product_pack='.(int)$id_product);
        }
    }
    public static function updateCarrierProduct($id_product,$product_type,$id_carriers)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_carrier` WHERE id_product='.(int)$id_product);
        if($product_type!=2 && $id_carriers && Ets_marketplace::validateArray($id_carriers,'isInt'))
        {
            foreach($id_carriers as $id_carrier)
            {
                if($id_carrier && Carrier::getCarrierByReference($id_carrier))
                {
                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_carrier`(id_product,id_carrier_reference,id_shop) VALUES("'.(int)$id_product.'","'.(int)$id_carrier.'","'.(int)Context::getContext()->shop->id.'")');
                }
            }
        }
    }
    public static function updateSupplierProduct($id_product,$id_suppliers,$product_supplier_reference,$product_supplier_price_currency,$product_supplier_price)
    {
        if($id_product && Validate::isUnsignedId($id_product) &&  $id_suppliers && Ets_marketplace::validateArray($id_suppliers,'isInt') && $product_supplier_reference && Ets_marketplace::validateArray($product_supplier_reference) && $product_supplier_price && Ets_marketplace::validateArray($product_supplier_price) && $product_supplier_price_currency && Ets_marketplace::validateArray($product_supplier_price_currency))
        {
            foreach($id_suppliers as $id_supplier)
            {
                $references = isset($product_supplier_reference[$id_supplier]) ? $product_supplier_reference[$id_supplier] : array();
                $supplier_prices = isset($product_supplier_price[$id_supplier]) ? $product_supplier_price[$id_supplier] :array() ;
                $currencies = isset($product_supplier_price_currency[$id_supplier]) ? $product_supplier_price_currency[$id_supplier] : array();
                if($currencies)
                {
                    $no_product_attribute = false;
                    foreach($currencies as $id_product_attribute=> $id_currency)
                    {
                        if($id_product_attribute==0)
                            $no_product_attribute=true;
                        if(isset($references[$id_product_attribute]))
                            $reference = $references[$id_product_attribute];
                        else
                            $reference ='';
                        if(isset($supplier_prices[$id_product_attribute]))
                            $supplier_price = (float)$supplier_prices[$id_product_attribute];
                        else
                            $supplier_price =0;
                        if($id_product_supplier = Db::getInstance()->getValue('SELECT id_product_supplier FROM `'._DB_PREFIX_.'product_supplier` WHERE id_product_attribute="'.(int)$id_product_attribute.'" AND id_product="'.(int)$id_product.'" AND id_supplier="'.(int)$id_supplier.'"'))
                            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product_supplier` SET product_supplier_reference="'.pSQL($reference).'",product_supplier_price_te="'.(float)$supplier_price.'",id_currency ="'.(int)$id_currency.'" WHERE id_product_supplier="'.(int)$id_product_supplier.'"');
                        else
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_supplier`(id_product,id_product_attribute,id_supplier,product_supplier_reference,product_supplier_price_te,id_currency) VALUES("'.(int)$id_product.'","'.(int)$id_product_attribute.'","'.(int)$id_supplier.'","'.pSQL($reference).'","'.(float)$supplier_price.'","'.(int)$id_currency.'")');
                    }
                    if(!$no_product_attribute)
                    {
                        if($id_product_supplier = Db::getInstance()->getValue('SELECT id_product_supplier FROM `'._DB_PREFIX_.'product_supplier` WHERE id_product_attribute="0" AND id_product="'.(int)$id_product.'" AND id_supplier="'.(int)$id_supplier.'"'))
                            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product_supplier` SET product_supplier_reference="",product_supplier_price_te="0",id_currency ="'.(int)Configuration::get('PS_CURRENCY_DEFAULT').'" WHERE id_product_supplier="'.(int)$id_product_supplier.'"');
                        else
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_supplier`(id_product,id_product_attribute,id_supplier,product_supplier_reference,product_supplier_price_te,id_currency) VALUES("'.(int)$id_product.'","0","'.(int)$id_supplier.'","","0","'.(int)Configuration::get('PS_CURRENCY_DEFAULT').'")');
                    }
                }
            }
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_supplier` WHERE id_product="'.(int)$id_product.'" AND id_supplier NOT IN ('.implode(',',array_map('intval',$id_suppliers)).')');
        }
        else
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_supplier` WHERE id_product="'.(int)$id_product.'"');
    }
    public static function getPackProducts($id_product)
    {
        $sql = 'SELECT p.*,pl.name,pl.link_rewrite, pa.* FROM `'._DB_PREFIX_.'product` p
        INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.id_product=ps.id_product AND ps.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.id_product=pl.id_product AND pl.id_lang="'.(int)Context::getContext()->language->id.'")
        LEFT JOIN `'._DB_PREFIX_.'pack` pa ON (p.id_product=pa.id_product_item)
        WHERE id_product_pack = "'.(int)$id_product.'" GROUP BY p.id_product';
        return Db::getInstance()->executeS($sql);
    }
    public static function getProductAttributes($product)
    {
        $productAttributes =  Db::getInstance()->executeS('SELECT pa.*,sa.quantity FROM `'._DB_PREFIX_.'product_attribute` pa
            LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON (pa.id_product_attribute=sa.id_product_attribute)
            WHERE pa.id_product='.(int)$product->id.' ORDER BY pa.id_product_attribute ASC');
        if($productAttributes)
        {
            foreach($productAttributes as &$productattribute)
            {

                $productattribute['name_attribute'] = Ets_mp_product::getProductAttributeName($productattribute['id_product_attribute']);
                $attribute_images = Db::getInstance()->executeS('SELECT id_image FROM `'._DB_PREFIX_.'product_attribute_image` WHERE id_product_attribute='.(int)$productattribute['id_product_attribute']);
                $productattribute['images'] = array();
                if($attribute_images)
                {
                    foreach($attribute_images as $attribute_image)
                        $productattribute['images'][] = $attribute_image['id_image'];
                }
                if($product->id_tax_rules_group)
                {
                    $tax = Module::getInstanceByName('ets_marketplace')->getTaxValue($product->id_tax_rules_group);
                    $productattribute['price_tax_incl'] = Tools::ps_round($productattribute['price'] + ($productattribute['price']*$tax),6);
                }
                else
                    $productattribute['price_tax_incl']= $productattribute['price'];
            }
        }
        return $productAttributes;
    }
    public static function getSpecificPrices($id_product)
    {
        return Db::getInstance()->executeS('
            SELECT sp.*,cul.name as currency_name, col.name as country_name, gl.name as group_name,CONCAT(c.firstname," ",c.lastname) as customer_name FROM `'._DB_PREFIX_.'specific_price` sp
            LEFT JOIN '._DB_PREFIX_.(version_compare(_PS_VERSION_, '1.7.6.0', '>=')? 'currency_lang':'currency').' cul ON (cul.id_currency= sp.id_currency '.(version_compare(_PS_VERSION_, '1.7.6.0', '>=') ? ' AND cul.id_lang ="'.(int)Context::getContext()->language->id.'"':'').')
            LEFT JOIN `'._DB_PREFIX_.'country_lang` col ON (col.id_country= sp.id_country AND col.id_lang="'.(int)Context::getContext()->language->id.'") 
            LEFT JOIN `'._DB_PREFIX_.'group_lang` gl ON (gl.id_group=sp.id_group AND gl.id_lang="'.(int)Context::getContext()->language->id.'")
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer=sp.id_customer)
            WHERE sp.id_product='.(int)$id_product.' ORDER BY sp.id_specific_price asc');
    }
    public static function getSupplierProduct($id_product,$id_supplier)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'product_supplier` WHERE id_product="'.(int)$id_product.'" AND id_supplier="'.(int)$id_supplier.'"');
    }
    public static function getInformationSupplier($product,$id_supplier)
    {
        if(Validate::isLoadedObject($product))
        {
            $has_attribute = $product->hasAttributes();
                $currency_default = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
                $product_suppliers = Db::getInstance()->executeS('SELECT p.id_product,'.($has_attribute ? ' pa.id_product_attribute':'0').' as id_product_attribute,pl.name as product_name,ps.product_supplier_reference,ps.product_supplier_price_te,IF(ps.id_currency,ps.id_currency,"'.(int)$currency_default->id.'") as id_currency,IF('.(version_compare(_PS_VERSION_, '1.7.6.0', '>=') ? 'cl.symbol':'cl.sign').','.(version_compare(_PS_VERSION_, '1.7.6.0', '>=') ? 'cl.symbol':'cl.sign').',"'.pSQL($currency_default->sign).'") as symbol FROM `'._DB_PREFIX_.'product` p
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.id_product = pl.id_product AND pl.id_lang = "'.(int)Context::getContext()->language->id.'")
                '.($has_attribute ? ' LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON(p.id_product=pa.id_product)':'').'
                LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (ps.id_product = p.id_product'.($has_attribute ? ' AND pa.id_product_attribute=ps.id_product_attribute':'').' AND ps.id_supplier="'.(int)$id_supplier.'")
                LEFT JOIN '._DB_PREFIX_.(version_compare(_PS_VERSION_, '1.7.6.0', '>=') ? 'currency_lang':'currency').' cl ON (cl.id_currency=ps.id_currency'.(version_compare(_PS_VERSION_, '1.7.6.0', '>=') ?' AND cl.id_lang="'.(int)Context::getContext()->language->id.'"':'').')
                WHERE p.id_product="'.(int)$product->id.'"
                GROUP BY p.id_product'.($has_attribute ? ',pa.id_product_attribute':'').'
            ');
                if($product_suppliers)
                {
                    foreach($product_suppliers as &$product_supplier)
                    {
                        if($product_supplier['id_product_attribute'])
                            $product_supplier['product_name'] = Ets_mp_product::getProductAttributeName($product_supplier['id_product_attribute']);
                    }
                }
                return $product_suppliers;
        }
        return array();
    }
    public static function checkExistSpecific($id_product,$id_currency,$id_group,$id_country,$id_product_attribute,$id_customer,$from,$to,$quantity)
    {
        $sql ='SELECT * FROM `'._DB_PREFIX_.'specific_price` 
            WHERE `id_product`="'.(int)$id_product.'" 
            AND `id_currency`="'.(int)$id_currency.'" 
            AND `id_group`="'.(int)$id_group.'" 
            AND `id_country`="'.(int)$id_country.'" 
            AND `id_product_attribute`= "'.(int)$id_product_attribute.'"
            AND `id_customer`= "'.(int)$id_customer.'" 
            AND `from` = "'.($from ? pSQL($from):'0000-00-00 00:00:00' ).'"
            AND `to`= "'.($to ? pSQL($to):'0000-00-00 00:00:00' ).'"
            AND `from_quantity`="'.(int)$quantity.'"';
        return Db::getInstance()->getRow($sql);
    }
    public static function getDetailSpecific($id_specific)
    {
        return Db::getInstance()->getRow('
                SELECT sp.*,cul.name as currency_name, col.name as country_name, gl.name as group_name,CONCAT(c.firstname," ",c.lastname) as customer_name FROM `'._DB_PREFIX_.'specific_price` sp
                LEFT JOIN '._DB_PREFIX_.(version_compare(_PS_VERSION_, '1.7.6.0', '>=')? 'currency_lang':'currency').' cul ON (cul.id_currency= sp.id_currency '.(version_compare(_PS_VERSION_, '1.7.6.0', '>=')? 'AND cul.id_lang="'.(int)Context::getContext()->language->id.'"':'').')
                LEFT JOIN `'._DB_PREFIX_.'country_lang` col ON (col.id_country= sp.id_country AND col.id_lang="'.(int)Context::getContext()->language->id.'") 
                LEFT JOIN `'._DB_PREFIX_.'group_lang` gl ON (gl.id_group=sp.id_group AND gl.id_lang="'.(int)Context::getContext()->language->id.'")
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer=sp.id_customer)
                WHERE sp.id_specific_price = "'.(int)$id_specific.'"');
    }
    public static function updateImageCombination($id_product_attribute,$images)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_attribute_image` WHERE id_product_attribute='.(int)$id_product_attribute);
        if(isset($images[$id_product_attribute]) && $images[$id_product_attribute])
        {
            foreach($images[$id_product_attribute] as $id_image)
            {
                if(Validate::isInt($id_image) && Validate::isLoadedObject(new Image($id_image)))
                {
                    if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'product_attribute_image` WHERE id_product_attribute="'.(int)$id_product_attribute.'" AND id_image="'.(int)$id_image.'"'))
                        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_attribute_image`(id_product_attribute,id_image) VALUES("'.(int)$id_product_attribute.'","'.(int)$id_image.'")');
                }

            }
        }
    }
    public static function deleteCustomizationField($id_product,$excludeIDs = array())
    {
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customization_field` SET is_deleted=1 WHERE id_product="'.(int)$id_product.'"'.($excludeIDs ? ' AND id_customization_field NOT IN ('.implode(',',array_map('intval',$excludeIDs)).')':''));
        else
        {
            $ids =  Db::getInstance()->executeS('SELECT id_customization_field FROM `'._DB_PREFIX_.'customization_field` WHERE id_product="'.(int)$id_product.'"'.($excludeIDs ? ' AND id_customization_field NOT IN ('.implode(',',array_map('intval',$excludeIDs)).')':''));
            if($ids)
            {
                foreach($ids as $id)
                {
                    $customizationField = new CustomizationField($id['id_customization_field']);
                    $customizationField->delete();
                }
            }
        }
        return true;
    }
    public static function ajaxSearchProductByQuery($query,$id_customer_seller,$active,$excludeVirtuals,$exclude_packs,$disableCombination,$excludeIds)
    {
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $type_image= ImageType::getFormattedName('home');
        else
            $type_image= ImageType::getFormatedName('home');
        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`cache_default_attribute`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_product` seller_product ON (seller_product.id_product=p.id_product AND seller_product.id_customer="'.(int)$id_customer_seller.'")
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int)Context::getContext()->shop->id . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) Context::getContext()->language->id . ')
                WHERE (p.id_product="'.(int)$query.'" OR pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
            (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . implode(',',array_map('intval',explode(',',$excludeIds))) . ') ' : ' ') .
            ($active ? ' AND p.active=1':'').
            ($excludeVirtuals ? ' AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            ($exclude_packs ? ' AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
            ' GROUP BY p.id_product';
        $items = Db::getInstance()->executeS($sql);
        if ($items && ($disableCombination || $excludeIds)) {
            foreach ($items as $item) {
                if(!$item['id_image'])
                    $item['id_image'] = Db::getInstance()->getValue('SELECT id_image FROM `'._DB_PREFIX_.'image` WHERE id_product='.(int)$item['id_product']);
                echo $item['id_product'].'|0|'.trim(str_replace('|','',$item['name'])).'|'.$item['reference'].'|'.($item['id_image'] ? str_replace('http://', Tools::getShopProtocol(), Context::getContext()->link->getImageLink($item['link_rewrite'], $item['id_image'], $type_image)):'')."\n";
            }
        }
        elseif ($items) {
            // packs
            $results = array();
            foreach ($items as $item) {
                // check if product have combination
                if(!$item['id_image'])
                    $item['id_image'] = Db::getInstance()->getValue('SELECT id_image FROM `'._DB_PREFIX_.'image` WHERE id_product='.(int)$item['id_product']);
                if (Combination::isFeatureActive() && $item['cache_default_attribute']) {
                    $sql = 'SELECT pa.`id_product_attribute`, pa.`reference`, pai.`id_image`, al.`name` AS attribute_name,
                                a.`id_attribute`
                            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) Context::getContext()->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                            WHERE pa.`id_product` = ' . (int) $item['id_product'] . '
                            GROUP BY pa.`id_product_attribute`
                            ORDER BY pa.`id_product_attribute`';

                    $combinations = Db::getInstance()->executeS($sql);
                    if (!empty($combinations)) {
                        foreach ($combinations as $combination) {
                            $results[$combination['id_product_attribute']]['id'] = $item['id_product'];
                            $results[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
                            $results[$combination['id_product_attribute']]['name'] = $item['name'].' '.Ets_mp_product::getProductAttributeName($combination['id_product_attribute']);
                            if (!empty($combination['reference'])) {
                                $results[$combination['id_product_attribute']]['ref'] = $combination['reference'];
                            } else {
                                $results[$combination['id_product_attribute']]['ref'] = !empty($item['reference']) ? $item['reference'] : '';
                            }
                            if (empty($results[$combination['id_product_attribute']]['image'])) {
                                if(!$combination['id_image'])
                                    $combination['id_image'] = $item['id_image'];
                                $results[$combination['id_product_attribute']]['image'] = str_replace('http://', Tools::getShopProtocol(), Context::getContext()->link->getImageLink($item['link_rewrite'], $combination['id_image'], $type_image));
                            }
                            echo $item['id_product'].'|'.(int)$combination['id_product_attribute'].'|'.trim(str_replace('|','',$results[$combination['id_product_attribute']]['name'])).'|'.$results[$combination['id_product_attribute']]['ref'].'|'.$results[$combination['id_product_attribute']]['image']."\n";
                        }
                    } else {
                        echo $item['id_product'].'|0|'.trim(str_replace('|','',$item['name'])).'|'.$item['reference'].'|'.str_replace('http://', Tools::getShopProtocol(), Context::getContext()->link->getImageLink($item['link_rewrite'], $item['id_image'], $type_image))."\n";
                    }
                } else {
                    echo $item['id_product'].'|0|'.trim(str_replace('|','',$item['name'])).'|'.$item['reference'].'|'.str_replace('http://', Tools::getShopProtocol(), Context::getContext()->link->getImageLink($item['link_rewrite'], $item['id_image'], $type_image))."\n";
                }
            }
        }
    }
}