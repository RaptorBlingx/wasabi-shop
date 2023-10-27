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
class Ets_mp_seller extends ObjectModel
{
    protected static $instance;
    public $id_customer;
    public $id_shop;
    public $id_billing;
    public $id_shop_category;
    public $shop_name;
    public $id_group;
    public $shop_description;
    public $shop_address;
    public $shop_phone;
    public $shop_logo;
    public $shop_banner;
    public $banner_url;
    public $link_facebook;
    public $link_instagram;
    public $link_google;
    public $link_twitter;
    public $message_to_administrator;
    public $reason;
    public $active;
    public $mail_expired;
    public $mail_going_to_be_expired;
    public $mail_payed;
    public $mail_wait_pay;
    public $payment_verify;
    public $user_shipping;
    public $user_brand;
    public $user_supplier;
    public $user_attribute;
    public $user_feature;
    public $enable_commission_by_category;
    public $commission_rate;
    public $auto_enabled_product;
    public $code_chat;
    public $date_from;
    public $date_to;
    public $date_add;
    public $date_upd;
    public $vat_number;
    public $latitude;
    public $longitude;
    public $vacation_mode;
    public $vacation_type;
    public $vacation_notifications;
    public $date_vacation_start;
    public $date_vacation_end;
    public $seller_email;
    public $seller_name;
    public $firstname;
    public $lastname;
    public $id_language;
    public static $definition = array(
        'table' => 'ets_mp_seller',
        'primary' => 'id_seller',
        'multilang' => true,
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_billing' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_group' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_shop_category' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'shop_phone' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'vat_number' => array('type' => self::TYPE_STRING),
            'shop_logo' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'shop_banner' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'lang' => true),
            'banner_url' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'lang' => true),
            'link_facebook' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'link_instagram' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'link_google' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'link_twitter' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'message_to_administrator' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'reason' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'enable_commission_by_category' => array('type' => self::TYPE_INT),
            'commission_rate' => array('type' => self::TYPE_STRING),
            'auto_enabled_product' => array('type' => self::TYPE_STRING),
            'code_chat' => array('type' => self::TYPE_STRING),
            'active' => array('type' => self::TYPE_INT),
            'mail_expired' => array('type' => self::TYPE_INT),
            'mail_wait_pay' => array('type' => self::TYPE_INT),
            'mail_going_to_be_expired' => array('type' => self::TYPE_INT),
            'mail_payed' => array('type' => self::TYPE_INT),
            'payment_verify' => array('type' => self::TYPE_INT),
            'user_shipping' => array('type' => self::TYPE_INT),
            'user_brand' => array('type' => self::TYPE_INT),
            'user_supplier' => array('type' => self::TYPE_INT),
            'user_attribute' => array('type' => self::TYPE_INT),
            'user_feature' => array('type' => self::TYPE_INT),
            'latitude' => array('type' => self::TYPE_FLOAT),
            'longitude' => array('type' => self::TYPE_FLOAT),
            'vacation_mode' => array('type' => self::TYPE_INT),
            'vacation_type' => array('type' => self::TYPE_STRING),
            'date_from' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_to' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_add' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_upd' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_vacation_start' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_vacation_end' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'shop_name' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'lang' => true),
            'shop_description' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'lang' => true),
            'shop_address' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'lang' => true),
            'vacation_notifications' => array('type' => self::TYPE_STRING, 'lang' => true),
        )
    );

    public function __construct($id_item = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_item, $id_lang, $id_shop);
        $customer = new Customer($this->id_customer);
        $this->seller_email = $customer->email;
        $this->firstname = $customer->firstname;
        $this->lastname = $customer->lastname;
        $this->seller_name = $customer->firstname . ' ' . $customer->lastname;
        $this->id_language = $customer->id_lang;
        if (!$this->user_shipping) {
            $this->user_shipping = 3;
        }
        if (!$this->user_brand)
            $this->user_brand = 3;
        if (!$this->user_supplier)
            $this->user_supplier = 3;
        if (!$this->user_attribute)
            $this->user_attribute = 3;
        if (!$this->user_feature)
            $this->user_feature = 3;
        if ($this->id_billing) {
            $billing = new Ets_mp_billing($this->id_billing);
            if ($billing->active == 0 && $billing->seller_confirm == 0)
                $this->payment_verify = -1;
            else
                $this->payment_verify = 0;
        }
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_mp_seller();
        }
        return self::$instance;
    }

    public function getNumberProductUpload()
    {
        if ($this->id_group) {
            $sellerGroup = new Ets_mp_seller_group($this->id_group);
            if ($sellerGroup->number_product_upload)
                return $sellerGroup->number_product_upload;
        }
        return (int)Configuration::get('ETS_MP_SELLER_MAXIMUM_UPLOAD');
    }

    public function getApplicableProductCategories()
    {
        if ($this->id_group) {
            $sellerGroup = new Ets_mp_seller_group($this->id_group);
            if ($sellerGroup->applicable_product_categories == 'all_product_categories') {
                return array();
            } elseif ($sellerGroup->applicable_product_categories == 'specific_product_categories' && $sellerGroup->list_categories)
                return explode(',', $sellerGroup->list_categories);

        }
        if (Configuration::get('ETS_MP_APPLICABLE_CATEGORIES') == 'all_product_categories') {
            return array();
        } elseif (Configuration::get('ETS_MP_APPLICABLE_CATEGORIES') == 'specific_product_categories' && Configuration::get('ETS_MP_SELLER_CATEGORIES'))
            return explode(',', Configuration::get('ETS_MP_SELLER_CATEGORIES'));
        return array();
    }
    public function getNoApplicableProductCategories()
    {
        if($seller_categories = $this->getApplicableProductCategories())
        {
            return Db::getInstance()->executeS('SELECT c.id_category FROM `'._DB_PREFIX_.'category` c
            INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category=cs.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
            WHERE c.id_category NOT IN ('.implode(',',array_map('intval',$seller_categories)).')');
        }
        return null;
    }
    static public function _getSellers($filter = '', $sort = '', $start = 0, $limit = 10, $total = false)
    {
        if (Module::isEnabled('ets_reviews')) {
            $sql_avg = 'SELECT  seller_product2.id_customer, AVG(pc.grade) as avg_rate,SUM(pc.grade) as total_grade,COUNT(pc.id_ets_rv_product_comment) as count_grade FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` pc
            INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (pc.id_product=p.id_product AND p.active=1)
            INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` seller_product2 ON (seller_product2.id_product=pc.id_product)
            WHERE 1' . (Configuration::get('ETS_PC_MODERATE') ? ' AND pc.validate=1' : '') . ' AND pc.grade!=0  GROUP BY seller_product2.id_customer';
        } elseif (Module::isEnabled('productcomments')) {
            $sql_avg = 'SELECT seller_product2.id_customer, AVG(pc.grade) as avg_rate,SUM(pc.grade) as total_grade,COUNT(pc.id_product_comment) as count_grade FROM `' . _DB_PREFIX_ . 'product_comment` pc
            INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (pc.id_product=p.id_product AND p.active=1)
            INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` seller_product2 ON (seller_product2.id_product=pc.id_product)
            WHERE 1 ' . (Configuration::get('PRODUCT_COMMENTS_MODERATE') ? ' AND pc.validate=1' : '') . ' AND pc.grade!=0 GROUP BY seller_product2.id_customer';
        }
        if ($total) {
            $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_mp_seller` s
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_lang` sl ON (s.id_seller = sl.id_seller AND sl.id_lang="' . (int)Context::getContext()->language->id . '")
                LEFT JOIN `' . _DB_PREFIX_ . 'customer` customer ON (s.id_customer=customer.id_customer)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_billing` b ON (b.id_ets_mp_seller_billing = s.id_billing)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_shop_category` sc ON (sc.id_ets_mp_shop_category = s.id_shop_category)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_shop_category_lang` scl ON (scl.id_ets_mp_shop_category = sc.id_ets_mp_shop_category AND scl.id_lang="' . (int)Context::getContext()->language->id . '")
                LEFT JOIN (
                    SELECT sp.id_customer,count(sp.id_product) as total_product FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product` sp
                    INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (sp.id_product= p.id_product AND p.active=1) GROUP BY sp.id_customer
                ) seller_product ON (seller_product.id_customer=s.id_customer)
                LEFT JOIN (
                    SELECT r.id_seller,COUNT(r.id_customer) as total_reported FROM `' . _DB_PREFIX_ . 'ets_mp_seller_report` r WHERE id_product=0 GROUP BY r.id_seller 
                ) seller_report ON (seller_report.id_seller=s.id_seller)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_group_lang` seller_group_lang ON (seller_group_lang.id_ets_mp_seller_group = s.id_group AND seller_group_lang.id_lang="' . (int)Context::getContext()->language->id . '")
                ' . (isset($sql_avg) ? ' LEFT JOIN (' . (string)$sql_avg . ') seller_rate ON (seller_rate.id_customer = s.id_customer)' : '') . '
            WHERE s.id_shop="' . (int)Context::getContext()->shop->id . '" ' . (string)$filter;
            return Db::getInstance()->getValue($sql);
        } else {
            $sql = 'SELECT s.*,b.active as payment_status,b.seller_confirm,b.reference,seller_product.total_product,seller_report.total_reported' . (isset($sql_avg) ? ',seller_rate.avg_rate,seller_rate.total_grade,seller_rate.count_grade' : '') . ',CONCAT(customer.firstname," ", customer.lastname) as seller_name,customer.email as seller_email, sl.shop_name,sl.shop_address,sl.shop_description,seller_group_lang.name as group_name,scl.name as category_name FROM `' . _DB_PREFIX_ . 'ets_mp_seller` s
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_lang` sl ON (s.id_seller = sl.id_seller AND sl.id_lang ="' . (int)Context::getContext()->language->id . '")
                LEFT JOIN `' . _DB_PREFIX_ . 'customer` customer ON (s.id_customer=customer.id_customer)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_billing` b ON (b.id_ets_mp_seller_billing = s.id_billing)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_shop_category` sc ON (sc.id_ets_mp_shop_category = s.id_shop_category)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_shop_category_lang` scl ON (scl.id_ets_mp_shop_category = sc.id_ets_mp_shop_category AND scl.id_lang="' . (int)Context::getContext()->language->id . '")
                LEFT JOIN (
                    SELECT sp.id_customer,count(sp.id_product) as total_product FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product` sp
                    INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (sp.id_product= p.id_product AND p.active=1) GROUP BY sp.id_customer
                ) seller_product ON (seller_product.id_customer=s.id_customer)
                LEFT JOIN (
                    SELECT r.id_seller,COUNT(r.id_customer) as total_reported FROM `' . _DB_PREFIX_ . 'ets_mp_seller_report` r WHERE id_product=0 GROUP BY r.id_seller 
                ) seller_report ON (seller_report.id_seller=s.id_seller)
                LEFT JOIN (
                    SELECT sp.id_customer,SUM(ps.quantity) as total_sale FROM `' . _DB_PREFIX_ . 'product_sale` ps
                    INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` sp ON (ps.id_product=sp.id_product)
                    GROUP BY sp.id_customer
                ) seller_sale ON (seller_sale.id_customer=s.id_customer)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_group_lang` seller_group_lang ON (seller_group_lang.id_ets_mp_seller_group = s.id_group AND seller_group_lang.id_lang="' . (int)Context::getContext()->language->id . '")
                ' . (isset($sql_avg) ? ' LEFT JOIN (' . (string)$sql_avg . ') seller_rate ON (seller_rate.id_customer = s.id_customer)' : '') . '
            WHERE s.id_shop="' . (int)Context::getContext()->shop->id . '" ' . (string)$filter . ''
                . ($sort ? ' ORDER BY ' . pSQL($sort) : ' ORDER BY s.id_seller DESC')
                . ' LIMIT ' . (int)$start . ',' . (int)$limit . '';
            return Db::getInstance()->executeS($sql);
        }
    }

    public function getNewProducts($filter = '', $page = 0, $per_page = 12, $order_sort = 'p.id_product desc', $total = false, $listIds = false,$id_category=0)
    {
        if ($order_sort) {
            $order_sort = explode(' ', trim($order_sort));
            $order_by = $order_sort[0];
            if (isset($order_sort[1]))
                $order_way = $order_sort[1];
            else
                $order_way = null;
        } else {
            $order_way = null;
            $order_by = null;
        }

        if ($total)
            return $this->getListNewProducts($filter, (int)Context::getContext()->language->id, $page, (int)$per_page, $total,false,false,null,false,$id_category);
        elseif ($listIds)
            return $this->getListNewProducts($filter, (int)Context::getContext()->language->id, $page, (int)$per_page, $total, $order_by, $order_way, null, $listIds,$id_category);
        $newProducts = $this->getListNewProducts($filter, (int)Context::getContext()->language->id, $page, (int)$per_page, $total, $order_by, $order_way, null, $listIds,$id_category);
        if (version_compare(_PS_VERSION_, '1.7', '>='))
            return Ets_marketplace::productsForTemplate($newProducts);
        return $newProducts;
    }

    public function getListNewProducts($filter, $id_lang, $page_number = 0, $nb_products = 10, $count = false, $order_by = null, $order_way = null, Context $context = null, $listIds = false,$id_category=0)
    {
        $now = date('Y-m-d') . ' 00:00:00';
        if (!$context) {
            $context = Context::getContext();
        }
        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }
        if ($page_number < 1) {
            $page_number = 1;
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'date_add';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'product_shop';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die($order_by . ' ' . $order_way);
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
                JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', array_map('intval',$groups)) . ')' : '= ' . (int)Configuration::get('PS_UNIDENTIFIED_GROUP')) . ')
                WHERE cp.`id_product` = p.`id_product`)';
        }
        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }
        $nb_days_new_product = (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ?: 20;
        if ($count) {
            $sql = 'SELECT COUNT(DISTINCT p.`id_product`) AS nb
                    FROM `' . _DB_PREFIX_ . 'product` p
                    ' . Shop::addSqlAssociation('product', 'p') . '
                    INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` seller_product ON (seller_product.id_product=p.id_product AND seller_product.id_customer="' . (int)$this->id_customer . '")
                    LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.id_product=p.id_product)
                    WHERE product_shop.`active` = 1
                    AND product_shop.`date_add` > "' . date('Y-m-d', strtotime('-' . (int)$nb_days_new_product . ' DAY')) . '"
                    ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . (string)$filter . '
                    ' . $sql_groups;
            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } elseif ($listIds) {
            $sql = 'SELECT p.`id_product`
                    FROM `' . _DB_PREFIX_ . 'product` p
                    ' . Shop::addSqlAssociation('product', 'p') . '
                    INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` seller_product ON (seller_product.id_product=p.id_product AND seller_product.id_customer="' . (int)$this->id_customer . '")
                    LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.id_product=p.id_product)
                    WHERE product_shop.`active` = 1
                    AND product_shop.`date_add` > "' . date('Y-m-d', strtotime('-' . (int)$nb_days_new_product . ' DAY')) . '"
                    ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . (string)$filter . '
                    ' . $sql_groups;
            $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $ids = array();
            if ($products) {
                foreach ($products as $product)
                    $ids[] = $product['id_product'];
            }
            return $ids;
        }
        $sql = new DbQuery();
        $sql->select(
            'p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
            pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
            (DATEDIFF(product_shop.`date_add`,
                DATE_SUB(
                    "' . pSQL($now) . '",
                    INTERVAL ' . (int)$nb_days_new_product . ' DAY
                )
            ) > 0) as new'
        );

        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->innerjoin('ets_mp_seller_product', 'seller_product', 'seller_product.id_product=p.id_product');
        $sql->leftJoin('category_product', 'cp', 'cp.id_product=p.id_product');
        $sql->leftJoin('product_sale', 'sale', 'sale.id_product=p.id_product');
        $sql->leftJoin(
            'product_lang',
            'pl',
            '
            p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl')
        );
        $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int)$context->shop->id);
        $sql->leftJoin('image_lang', 'il', 'image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$id_lang);
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        $sql->where('product_shop.`active` = 1');
        $sql->where('seller_product.id_customer=' . (int)$this->id_customer);
        if ($id_category && Validate::isUnsignedInt($id_category))
            $sql->where('cp.`id_category` = ' . (int)$id_category);
        if ($front) {
            $sql->where('product_shop.`visibility` IN ("both", "catalog")');
        }
        $sql->where('product_shop.`date_add` > "' . pSQL(date('Y-m-d', strtotime('-' . (int)$nb_days_new_product . ' DAY'))) . '"');
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql->where('EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
                JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', array_map('intval',$groups)) . ')' : '=' . (int)Configuration::get('PS_UNIDENTIFIED_GROUP')) . ')
                WHERE cp.`id_product` = p.`id_product`)');
        }
        if ($filter)
            $sql->where(ltrim(trim((string)$filter), 'AND'));
        if ($order_by == 'rand') {
            $order_way = '';
            $order_by = 'RAND()';
        }
        $sql->orderBy((isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . pSQL($order_by) . ' ' . pSQL($order_way));
        $sql->limit($nb_products, (int)(($page_number - 1) * $nb_products));
        if (Combination::isFeatureActive()) {
            $sql->select('product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute');
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', 'p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int)$context->shop->id);
        }
        $sql->join(Product::sqlStock('p', 0));
        $sql->groupBy('p.id_product');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (!$result) {
            return false;
        }

        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }
        $products_ids = array();
        foreach ($result as $row) {
            $products_ids[] = $row['id_product'];
        }
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        Product::cacheFrontFeatures($products_ids, $id_lang);
        return Product::getProductsProperties((int)$id_lang, $result);
    }

    public function getBestSellerProducts($filter = '', $page = 0, $per_page = 12, $order_by = 'p.id_product desc', $total = false, $listIds = false,$id_ets_css_sub_category=0)
    {
        if ($total)
            return $this->getBestSalesLight($filter, (int)Context::getContext()->language->id, $page, (int)$per_page, $order_by, true,false,$id_ets_css_sub_category);
        if (!($result = $this->getBestSalesLight($filter, (int)Context::getContext()->language->id, $page, (int)$per_page, $order_by, false, $listIds,$id_ets_css_sub_category)))
            return array();
        if ($listIds)
            return $result;
        if (version_compare(_PS_VERSION_, '1.7', '>='))
            return Ets_marketplace::productsForTemplate($result);
        return $result;
    }

    public function getBestSalesLight($filter='', $idLang=0, $pageNumber = 0, $nbProducts = 10, $order_by = 'ps.quantity DESC', $total = false, $listIds = false,$id_ets_css_sub_category=0)
    {
        $context = Context::getContext();
        if ($pageNumber <= 0) {
            $pageNumber = 1;
        }
        if ($nbProducts < 1) {
            $nbProducts = 10;
        }
        if ($total)
            $sql = 'SELECT COUNT(DISTINCT p.id_product)';
        else
            $sql = '
		SELECT
			p.id_product, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute, pl.`link_rewrite`, pl.`name`, pl.`description_short`, product_shop.`id_category_default`,
			image_shop.`id_image` id_image, il.`legend`,
			ps.`quantity` AS sales, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category, p.show_price, p.available_for_order, IFNULL(stock.quantity, 0) as quantity, p.customizable,
			IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock,
			product_shop.`date_add` > "' . pSQL(date('Y-m-d', strtotime('-' . (Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY'))) . '" as new,
			product_shop.`on_sale`, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity';

        $sql .= ' FROM `' . _DB_PREFIX_ . 'product_sale` ps
        
		LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON ps.`id_product` = p.`id_product`
        LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.id_product=p.id_product)
		' . Shop::addSqlAssociation('product', 'p') . '
        ' . (
            $id_ets_css_sub_category ? ' LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp2 ON (cp2.id_product=p.id_product)' : ''
            ) . '
        INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` seller_product ON (seller_product.id_product=p.id_product AND seller_product.id_customer="' . (int)$this->id_customer . '")
		LEFT JOIN `' . _DB_PREFIX_ . 'product_sale` sale ON (sale.id_product = p.id_product)
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
			ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int)$context->shop->id . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (product_attribute_shop.id_product_attribute=pa.id_product_attribute)
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
			ON p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = ' . (int)$idLang . Shop::addSqlRestrictionOnLang('pl') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
			ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int)$context->shop->id . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$idLang . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
			ON cl.`id_category` = product_shop.`id_category_default`
			AND cl.`id_lang` = ' . (int)$idLang . Shop::addSqlRestrictionOnLang('cl') . Product::sqlStock('p', 0);

        $sql .= '
		WHERE product_shop.`active` = 1
        ' . ($id_ets_css_sub_category ? ' AND cp2.id_category="' . (int)$id_ets_css_sub_category . '"' : '') . '
		AND p.`visibility` != \'none\'';

        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql .= ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
				JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', array_map('intval',$groups)) . ')' : '=' . (int)Configuration::get('PS_UNIDENTIFIED_GROUP')) . ')
				WHERE cp.`id_product` = p.`id_product`)';
        }
        if ($filter)
            $sql .= (string)$filter;
        if ($total) {

            return Db::getInstance()->getValue($sql);
        } elseif ($listIds) {
            $products = Db::getInstance()->executeS($sql);
            $ids = array();
            foreach ($products as $product) {
                $ids[] = $product['id_product'];
            }
            return $ids;
        }
        $sql .= ' GROUP BY p.id_product
		ORDER BY ' . ($order_by ? $order_by : 'ps.quantity DESC') . '
        
		LIMIT ' . (int)(($pageNumber - 1) * $nbProducts) . ', ' . (int)$nbProducts;
        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.7', '<'))
            $result = Product::getProductsProperties($idLang, $result);
        return $result;
    }

    public function getSpecialProducts($filter = '', $page = 0, $per_page = 12, $order_sort = 'p.id_product desc', $total = false, $listIds = false,$id_category=0)
    {
        if ($total)
            return $this->getPricesDrop($filter, (int)Context::getContext()->language->id, $page, (int)$per_page, $total,false,$id_category);
        if ($order_sort) {
            $order_sort = explode(' ', trim($order_sort));
            $order_by = $order_sort[0];
            if (isset($order_sort[1]))
                $order_way = $order_sort[1];
            else
                $order_way = null;
        } else {
            $order_way = null;
            $order_by = null;
        }
        if ($order_by == 'rand') {
            $order_way = null;
            $order_by = null;
        }
        $products = $this->getPricesDrop($filter,
            (int)Context::getContext()->language->id,
            $page,
            (int)$per_page, $total, $order_by, $order_way, false, false, null, $listIds,$id_category
        );
        if ($listIds)
            return $products;
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            return Ets_marketplace::productsForTemplate($products);
        } else
            return $products;
    }

    public function getPricesDrop(
        $filter = '',
        $id_lang=0,
        $page_number = 0,
        $nb_products = 10,
        $count = false,
        $order_by = null,
        $order_way = null,
        $beginning = false,
        $ending = false,
        Context $context = null,
        $listIds = false,
        $id_ets_css_sub_category =0
    )
    {
        if (!Validate::isBool($count)) {
            die(Tools::displayError());
        }

        if (!$context) {
            $context = Context::getContext();
        }
        if ($page_number < 1) {
            $page_number = 1;
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'price';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'product_shop';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        $current_date = date('Y-m-d H:i:00');
        $ids_product = $this->_getProductIdByDate((!$beginning ? $current_date : $beginning), (!$ending ? $current_date : $ending), $context);

        $tab_id_product = array();
        foreach ($ids_product as $product) {
            if (Ets_marketplace::validateArray($product)) {
                $tab_id_product[] = (int)$product['id_product'];
            } else {
                $tab_id_product[] = (int)$product;
            }
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
                JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', array_map('intval',$groups)) . ')' : '=' . (int)Configuration::get('PS_UNIDENTIFIED_GROUP')) . ')
                WHERE cp.`id_product` = p.`id_product`)';
        }

        if ($count) {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT COUNT(DISTINCT p.`id_product`)
            FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') . '
            INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` seller_product ON (seller_product.id_product=p.id_product AND seller_product.id_customer="' . (int)$this->id_customer . '")
            LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.id_product=p.id_product)
            WHERE product_shop.`active` = 1
            AND product_shop.`show_price` = 1
            ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
            ' . ((!$beginning && !$ending) ? 'AND p.`id_product` IN(' . ((Ets_marketplace::validateArray($tab_id_product) && count($tab_id_product)) ? implode(', ', array_map('intval',$tab_id_product)) : 0) . ')' : '') . (string)$filter . '
            ' . (string)$sql_groups);
        } elseif ($listIds) {
            $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT p.`id_product`
            FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') . '
            INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` seller_product ON (seller_product.id_product=p.id_product AND seller_product.id_customer="' . (int)$this->id_customer . '")
            LEFT JOIN `' . _DB_PREFIX_ . 'product_sale` sale ON (sale.id_product = p.id_product)
            LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.id_product=p.id_product)
            WHERE product_shop.`active` = 1
            AND product_shop.`show_price` = 1
            ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
            ' . ((!$beginning && !$ending) ? 'AND p.`id_product` IN(' . ((Ets_marketplace::validateArray($tab_id_product) && count($tab_id_product)) ? implode(', ', array_map('intval',$tab_id_product)) : 0) . ')' : '') . (string)$filter . '
            ' . (string)$sql_groups);
            $ids = array();
            if ($products) {
                foreach ($products as $product) {
                    $ids[] = $product['id_product'];
                }
            }
            return $ids;
        }
        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by = pSQL($order_by[0]) . '.`' . pSQL($order_by[1]) . '`';
        }
        $sql = '
        SELECT
            p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`available_now`, pl.`available_later`,
            IFNULL(product_attribute_shop.id_product_attribute, 0) id_product_attribute,
            pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`,
            pl.`name`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
            DATEDIFF(
                p.`date_add`,
                DATE_SUB(
                    "' . pSQL(date('Y-m-d')) . ' 00:00:00",
                    INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
                )
            ) > 0 AS new
        FROM `' . _DB_PREFIX_ . 'product` p
        ' . Shop::addSqlAssociation('product', 'p') . '
        INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` seller_product ON (seller_product.id_product=p.id_product AND seller_product.id_customer="' . (int)$this->id_customer . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.id_product=p.id_product)
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
            ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int)$context->shop->id . ')
        ' . Product::sqlStock('p', 0, false, $context->shop) . '
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
            p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . '
        )
        LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
            ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int)$context->shop->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$id_lang . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
        WHERE product_shop.`active` = 1
        AND product_shop.`show_price` = 1
        ' . ($id_ets_css_sub_category ? ' AND cp.id_category="' . (int)$id_ets_css_sub_category . '"' : '') . '
        ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
        ' . ((!$beginning && !$ending) ? ' AND p.`id_product` IN (' . ((Ets_marketplace::validateArray($tab_id_product) && count($tab_id_product)) ? implode(', ', array_map('intval',$tab_id_product)) : 0) . ')' : '') . (string)$filter . '
        ' . (string)$sql_groups . '
        GROUP BY p.id_product
        ORDER BY ' . (isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . pSQL($order_by) . ' ' . pSQL($order_way) . '
        LIMIT ' . (int)(($page_number - 1) * $nb_products) . ', ' . (int)$nb_products;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (!$result) {
            return false;
        }

        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }

        return Product::getProductsProperties($id_lang, $result);
    }

    public function _getProductIdByDate($beginning, $ending, Context $context = null, $with_combination = false)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        $ids = Address::getCountryAndState($id_address);
        $id_country = $ids && $ids['id_country'] ? (int)$ids['id_country'] : (int)Configuration::get('PS_COUNTRY_DEFAULT');

        return SpecificPrice::getProductIdByDate(
            $context->shop->id,
            $context->currency->id,
            $id_country,
            $context->customer->id_default_group,
            $beginning,
            $ending,
            0,
            $with_combination
        );
    }

    public function getProducts($filter = '', $page = 0, $per_page = 12, $order_by = 'p.id_product desc', $total = false, $active = false, $listIds = false, $full = true)
    {
        $page = (int)$page;
        if ($page <= 0)
            $page = 1;
        $per_page = (int)$per_page;
        if ($per_page <= 0)
            $per_page = 12;
        $front = true;
        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        $id_lang = (int)Context::getContext()->language->id;
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        $prev_version = version_compare(_PS_VERSION_, '1.6.1.0', '<');
        if (!$total)
            if ($listIds)
                $sql = 'SELECT DISTINCT p.id_product ';
            else
                $sql = 'SELECT DISTINCT p.*,IF( mp.`price`!=0 ,mp.`price`,  p.`price`) as product_price, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS stock_quantity' . ($prev_version ? ' ,IFNULL(product_attribute_shop.id_product_attribute, 0)' : ' ,MAX(product_attribute_shop.id_product_attribute)') . ' id_product_attribute, pl.`description`, pl.`description_short`, pl.`available_now`,
    					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`,IF( mpl.`name`!="" ,mpl.`name`,  pl.`name`) as name, i.`id_image`,
    					il.`legend` as legend, m.`name` AS manufacturer_name,cl.name as default_category,
    					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
    					INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice,sp.approved,IFNULL(mp.id_product,0) as wait_change,mp.status as change_status';
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
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_product_lang` mpl ON (mpl.id_ets_mp_product = mp.id_ets_mp_product AND mpl.id_lang="' . (int)$id_lang . '")
                LEFT JOIN `' . _DB_PREFIX_ . 'product_sale` sale ON (sale.id_product = p.id_product)
                LEFT JOIN `' . _DB_PREFIX_ . 'category` c ON (c.id_category=p.id_category_default)
                LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.id_product=p.id_product)
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.id_category = cl.id_category AND cl.id_lang="' . (int)$id_lang . '")
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . ')' .
            ' LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.id_product=p.id_product AND i.cover=1)
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$id_lang . ')	
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                WHERE sp.id_customer="' . (int)$this->id_customer . '" ' . ($active ? ' AND product_shop.active=1' : '') . ' AND product_shop.`id_shop` = ' . (int)Context::getContext()->shop->id . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '');
        if ($total) {
            $sql .= $filter ? (string)$filter : '';
            return Db::getInstance()->getValue($sql);
        } elseif ($listIds) {
            $products = Db::getInstance()->executeS($sql);
            $ids = array();
            if ($products) {
                foreach ($products as $product)
                    $ids[] = $product['id_product'];
            }
            return $ids;
        } else {
            $sql .= $filter ? (string)$filter : '';
            $sql .= ' GROUP BY p.id_product' . ($order_by ? ' ORDER BY ' . pSQL($order_by) : '') . ' LIMIT ' . (int)($page - 1) * $per_page . ',' . (int)$per_page;
        }
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, true);
        if (!$products) {
            return array();
        }
        if (trim($order_by) == 'product_shop.price asc') {
            Tools::orderbyPrice($products, 'asc');
        } elseif (trim($order_by) == 'product_shop.price desc') {
            Tools::orderbyPrice($products, 'desc');
        }
        if ($full) {
            $products = Product::getProductsProperties($id_lang, $products);
            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $products = Ets_marketplace::productsForTemplate($products);
            }
        }
        return $products;
    }

    public function getProductOther($product)
    {
        $front = true;
        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        $id_lang = (int)Context::getContext()->language->id;
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        $prev_version = version_compare(_PS_VERSION_, '1.6.1.0', '<');
        $sql = 'SELECT DISTINCT p.*, IF(p.id_category_default="' . (int)$product->id_category_default . '",1,0) as category, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS stock_quantity' . ($prev_version ? ' ,IFNULL(product_attribute_shop.id_product_attribute, 0)' : ' ,MAX(product_attribute_shop.id_product_attribute)') . ' id_product_attribute, pl.`description`, pl.`description_short`, pl.`available_now`,
				pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`,
				il.`legend` as legend, m.`name` AS manufacturer_name,cl.name as default_category,
				DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
				INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice,sp.approved';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'product` p
        ' . Shop::addSqlAssociation('product', 'p') .
            (!$prev_version ?
                'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (pa.id_product = p.id_product)' . Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on=1') . '' :
                'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int)Context::getContext()->shop->id . ')'
            )
            . Product::sqlStock('p', 0, false, Context::getContext()->shop) . '
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` sp ON (sp.id_product=p.id_product)
        LEFT JOIN `' . _DB_PREFIX_ . 'category` c ON (c.id_category=p.id_category_default)
        LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.id_product=p.id_product)
        LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.id_category = cl.id_category AND cl.id_lang="' . (int)$id_lang . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . ')' .
            ' LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.id_product=p.id_product AND i.cover=1)
        LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$id_lang . ')	
        LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
        WHERE sp.id_customer="' . (int)$this->id_customer . '" AND product_shop.active=1 AND product_shop.`id_shop` = ' . (int)Context::getContext()->shop->id . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '');
        $sql .= ' AND p.id_product!="' . (int)$product->id . '"';
        $sql .= ' GROUP BY p.id_product ORDER BY category desc';
        $limit = (int)Configuration::get('ETS_MP_DISPLAY_NUMBER_OTHER_PRODUCT') ?: 12;
        $sql .= ' LIMIT 0,' . (int)$limit;
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, true);
        $products = Product::getProductsProperties($id_lang, $products);
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $products = Ets_marketplace::productsForTemplate($products);
        }
        return $products;
    }

    public function getProductSellerOther($product)
    {
        if ($tags = Ets_mp_product::getListtags($product->id)) {
            $front = true;
            $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
            $id_lang = (int)Context::getContext()->language->id;
            if (!Validate::isUnsignedInt($nb_days_new_product)) {
                $nb_days_new_product = 20;
            }
            $prev_version = version_compare(_PS_VERSION_, '1.6.1.0', '<');
            $sql = 'SELECT DISTINCT p.*,count(t.id_tag) as count_tag, IF(p.id_category_default="' . (int)$product->id_category_default . '",1,0) as category, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS stock_quantity' . ($prev_version ? ' ,IFNULL(product_attribute_shop.id_product_attribute, 0)' : ' ,MAX(product_attribute_shop.id_product_attribute)') . ' id_product_attribute, pl.`description`, pl.`description_short`, pl.`available_now`,
    				pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`,
    				il.`legend` as legend, m.`name` AS manufacturer_name,cl.name as default_category,
    				DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
    				INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice,sp.approved';
            $sql .= ' FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') .
                ' INNER JOIN `' . _DB_PREFIX_ . 'product_tag`  pt ON(pt.id_product=p.id_product AND pt.id_lang="' . (int)$id_lang . '")
            INNER JOIN `' . _DB_PREFIX_ . 'tag`  t ON (t.id_tag=pt.id_tag AND t.id_lang="' . (int)$id_lang . '" AND t.name IN ("' . implode('","', array_map('pSQL', explode(',', $tags))) . '") )
            INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` sp ON (sp.id_product=p.id_product)' .
                (!$prev_version ?
                    'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (pa.id_product = p.id_product)' . Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on=1') . '' :
                    'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int)Context::getContext()->shop->id . ')'
                )
                . Product::sqlStock('p', 0, false, Context::getContext()->shop) . '
            LEFT JOIN `' . _DB_PREFIX_ . 'category` c ON (c.id_category=p.id_category_default)
            LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.id_product=p.id_product)
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.id_category = cl.id_category AND cl.id_lang="' . (int)$id_lang . '")
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . ')' .
                ' LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.id_product=p.id_product AND i.cover=1)
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$id_lang . ')	
            LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
            WHERE sp.id_customer !="' . (int)$this->id_customer . '" AND product_shop.active=1 AND product_shop.`id_shop` = ' . (int)Context::getContext()->shop->id . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '');
            $sql .= ' AND p.id_product!="' . (int)$product->id . '"';
            $sql .= ' GROUP BY p.id_product ORDER BY count_tag DESC';  //ORDER BY RAND()
            $limit = (int)Configuration::get('ETS_MP_DISPLAY_NUMBER_OTHER_SELLER_PRODUCT') ?: 12;
            $sql .= ' LIMIT 0,' . (int)$limit;
            $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, true);
            $products = Product::getProductsProperties($id_lang, $products);
            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $products = Ets_marketplace::productsForTemplate($products);
            }
            return $products;
        }
        return array();
    }

    public function getDiscounts($filter = '', $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if ($total)
            $sql = 'SELECT COUNT(DISTINCT cr.id_cart_rule)';
        else
            $sql = 'SELECT cr.*,crl.name,if(cr.reduction_percent,cr.reduction_percent,cr.reduction_amount) as discount';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'cart_rule` cr
        LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_shop` crs ON (cr.id_cart_rule=crs.id_cart_rule)
        LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` crl ON (cr.id_cart_rule=crl.id_cart_rule AND crl.id_lang="' . (int)Context::getContext()->language->id . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_cart_rule_seller` cart_rule_seller ON (cart_rule_seller.id_cart_rule=cr.id_cart_rule)
        WHERE  cart_rule_seller.id_customer="' . (int)$this->id_customer . '" AND (crs.id_cart_rule is null OR crs.id_shop="' . (int)Context::getContext()->shop->id . '")  ' . ($filter ? (string)$filter : '') . ($order_by ? ' ORDER BY ' . pSQL($order_by) : '');
        if (!$total)
            $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
        if ($total)
            return Db::getInstance()->getValue($sql);
        else
            return Db::getInstance()->executeS($sql);
    }
    public function getOrders($filter = '', $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if ($total)
            $sql = 'SELECT COUNT(DISTINCT o.id_order)';
        else
            $sql = 'SELECT o.*,c.commission';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'orders` o 
        INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_order` so ON (o.id_order=so.id_order) 
        LEFT JOIN (SELECT id_order,id_customer,SUM(commission) as commission FROM `' . _DB_PREFIX_ . 'ets_mp_seller_commission` WHERE id_customer="' . (int)$this->id_customer . '" GROUP BY id_order,id_customer) c ON (c.id_order = o.id_order)    
        WHERE so.id_customer="' . (int)$this->id_customer . '"' . ($filter ? (string)$filter : '')
            . ($order_by ? ' ORDER By ' . pSQL($order_by) : '');
        if (!$total)
            $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
        if ($total)
            return Db::getInstance()->getValue($sql);
        else {
            return Db::getInstance()->executeS($sql);
        }
    }

    public function getCommissions($filter = '', $having = "", $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if ($total)
            $sql = 'SELECT COUNT(sc.id)';
        else
            $sql = 'SELECT sc.*,CONCAT(customer.firstname," ",customer.lastname) as seller_name,seller_lang.shop_name,p.id_product as product_id';
        $sql .= ' FROM (
        SELECT id_seller_commission as id, "commission" as type,reference,product_name,price,price_tax_incl,quantity,commission,status,note,date_add,id_shop,id_customer,id_order,id_product,id_product_attribute,"" as id_withdraw,"" as id_voucher FROM `' . _DB_PREFIX_ . 'ets_mp_seller_commission` c
        UNION ALL
        SELECT id_ets_mp_commission_usage as id,"usage" as type,reference,"" as product_name,"" as price,"" as price_tax_incl,"" as quantity,amount as commission,status,note,date_add,id_shop,id_customer,id_order,"" as id_product,"" as id_product_attribute,id_withdraw,id_voucher FROM `' . _DB_PREFIX_ . 'ets_mp_commission_usage` u
        )as sc
        INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller` seller ON (sc.id_customer= seller.id_customer)
        INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_lang` seller_lang ON (seller.id_seller= seller_lang.id_seller AND seller_lang.id_lang="' . (int)Context::getContext()->language->id . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (sc.id_order=o.id_order)
        LEFT JOIN `' . _DB_PREFIX_ . 'customer` customer ON (customer.id_customer=o.id_customer)
        LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product=sc.id_product AND p.active=1)
        WHERE seller.id_seller="' . (int)$this->id . '" AND sc.id_shop="' . (int)Context::getContext()->shop->id . '"' . ($filter ? (string)$filter : '');
        if (!$total) {
            $sql .= ($order_by ? ' ORDER By ' . pSQL($order_by) : '');
            if ($having)
                $sql .= ' HAVING 1 ' . pSQL($having);
            $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
        }
        if ($total)
            return Db::getInstance()->getValue($sql);
        else {
            return Db::getInstance()->executeS($sql);
        }
    }

    public function getManufacturers($filter = '', $having = "", $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if (!Configuration::get('ETS_MP_SELLER_CREATE_BRAND') && !Configuration::get('ETS_MP_SELLER_USER_GLOBAL_BRAND'))
            return $total ? 0 : array();
        if (!Configuration::get('ETS_MP_SELLER_CREATE_BRAND'))
            $filter .= ' AND man_seller.id_customer IS NULL';
        elseif (!Configuration::get('ETS_MP_SELLER_USER_GLOBAL_BRAND'))
            $filter .= ' AND man_seller.id_customer=' . (int)$this->id_customer;
        elseif ($this->user_brand == 1)
            $filter .= ' AND man_seller.id_customer IS NULL';
        elseif ($this->user_brand == 2)
            $filter .= ' AND man_seller.id_customer=' . (int)$this->id_customer;
        else
            $filter .= ' AND (man_seller.id_customer is null OR man_seller.id_customer="' . (int)$this->id_customer . '")';
        if ($total)
            $sql = 'SELECT COUNT(DISTINCT m.id_manufacturer)';
        else
            $sql = 'SELECT m.*,m.id_manufacturer as id,COUNT(p.id_product) as products,count(a.id_address) addresss,seller.id_seller,man_seller.id_customer as id_seller_customer';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'manufacturer` m
            INNER JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` ms ON (m.id_manufacturer=ms.id_manufacturer AND ms.id_shop="' . (int)Context::getContext()->shop->id . '")
            LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer_lang` ml ON (m.id_manufacturer=ml.id_manufacturer AND ml.id_lang ="' . (int)Context::getContext()->language->id . '")    
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_manufacturer_seller` man_seller ON (man_seller.id_manufacturer= m.id_manufacturer)    
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller` seller ON (seller.id_customer=man_seller.id_customer)
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_manufacturer = m. id_manufacturer)
            LEFT JOIN `' . _DB_PREFIX_ . 'address` a ON (a.id_manufacturer=m.id_manufacturer)
            WHERE 1 ' . ($filter ? (string)$filter : '');
        if ($total)
            return Db::getInstance()->getValue($sql);
        {
            $sql .= ' GROUP BY m.id_manufacturer' . ($order_by ? ' ORDER BY ' . pSQL($order_by) : '');
            if ($having)
                $sql .= ' HAVING 1' . (string)$having;
            if ($limit)
                $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
            return Db::getInstance()->executeS($sql);
        }
    }

    public function getSuppliers($filter = '', $having = "", $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if (!Configuration::get('ETS_MP_SELLER_CREATE_SUPPLIER') && !Configuration::get('ETS_MP_SELLER_USER_GLOBAL_SUPPLIER'))
            return $total ? 0 : array();
        if (!Configuration::get('ETS_MP_SELLER_CREATE_SUPPLIER'))
            $filter .= ' AND sup_seller.id_customer IS NULL';
        elseif (!Configuration::get('ETS_MP_SELLER_USER_GLOBAL_SUPPLIER'))
            $filter .= ' AND sup_seller.id_customer =' . (int)$this->id_customer;
        elseif ($this->user_supplier == 1)
            $filter .= ' AND sup_seller.id_customer IS NULL';
        elseif ($this->user_supplier == 2)
            $filter .= ' AND sup_seller.id_customer =' . (int)$this->id_customer;
        else
            $filter .= ' AND (sup_seller.id_customer is null OR sup_seller.id_customer="' . (int)$this->id_customer . '")';
        if ($total)
            $sql = 'SELECT COUNT(DISTINCT s.id_supplier)';
        else
            $sql = 'SELECT s.*,s.id_supplier as id,COUNT(DISTINCT ps.id_product) as products,seller.id_seller,sup_seller.id_customer as id_seller_customer';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'supplier` s
            INNER JOIN `' . _DB_PREFIX_ . 'supplier_shop` ss ON (s.id_supplier=ss.id_supplier AND ss.id_shop="' . (int)Context::getContext()->shop->id . '")
            LEFT JOIN `' . _DB_PREFIX_ . 'supplier_lang` sl ON (s.id_supplier=sl.id_supplier AND sl.id_lang ="' . (int)Context::getContext()->language->id . '")    
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_supplier_seller` sup_seller ON (sup_seller.id_supplier= s.id_supplier)    
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller` seller ON (seller.id_customer=sup_seller.id_customer)
            LEFT JOIN `' . _DB_PREFIX_ . 'product_supplier` ps ON (ps.id_supplier = s.id_supplier)
            WHERE 1 ' . ($filter ? (string)$filter : '');
        if ($total)
            return Db::getInstance()->getValue($sql);
        {
            $sql .= ' GROUP BY s.id_supplier' . ($order_by ? ' ORDER BY ' . pSQL($order_by) : '');
            if ($having)
                $sql .= ' HAVING 1' . (string)$having;
            if ($limit)
                $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
            return Db::getInstance()->executeS($sql);
        }
    }

    public function getProductComments($filter = '', $having = "", $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        $filter .= ' AND sp.id_customer="' . (int)$this->id_customer . '"';
        return Ets_mp_seller::getListProductComments($filter, $having, $start, $limit, $order_by, $total);
    }
    public static function getListProductComments($filter = '', $having = "", $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if (Module::isEnabled('productcomments') || Module::isEnabled('ets_reviews')) {
            if (Module::isEnabled('ets_reviews')) {
                if ($total)
                    $sql = 'SELECT COUNT(DISTINCT pc.id_ets_rv_product_comment)';
                else
                    $sql = 'SELECT pc.id_ets_rv_product_comment as id_comment,pc.id_product,IF(pcl.title,pcl.title,pcol.title) as title,IF(pcl.content,pcl.content,pcol.content) as content,IF(c.id_customer,CONCAT(c.firstname," ",c.lastname),pc.customer_name) as customer,pc.grade,pc.validate,pc.date_add,pl.name,seller_lang.shop_name,seller.id_seller';

                $sql .= ' FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` pc';
            } else {
                if ($total)
                    $sql = 'SELECT COUNT(DISTINCT pc.id_product_comment)';
                else
                    $sql = 'SELECT pc.id_product_comment as id_comment,pc.id_product,pc.title,pc.content,IF(c.id_customer,CONCAT(c.firstname," ",c.lastname),pc.customer_name) as customer,pc.grade,pc.validate,pc.date_add,pl.name,seller_lang.shop_name,seller.id_seller';
                $sql .= ' FROM `' . _DB_PREFIX_ . 'product_comment` pc';
            }
            $sql .= ' INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (pc.id_product=p.id_product)
            INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` sp ON (sp.id_product=p.id_product)
                INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller` seller ON (seller.id_customer=sp.id_customer)
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_lang` seller_lang ON(seller_lang.id_seller=seller.id_seller AND seller_lang.id_lang="' . (int)Context::getContext()->language->id . '")
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product= p.id_product AND pl.id_lang ="' . (int)Context::getContext()->language->id . '")';
            if (Module::isEnabled('ets_reviews')) {
                $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment_lang` pcl ON (pc.id_ets_rv_product_comment = pcl.id_ets_rv_product_comment AND pcl.id_lang="' . (int)Context::getContext()->language->id . '")';
                $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment_lang` pcol ON (pc.id_ets_rv_product_comment = pcol.id_ets_rv_product_comment)';
            }
            $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.id_customer=pc.id_customer)
                WHERE pc.deleted=0 ' . ($filter ? (string)$filter : '');
            if ($total)
                return Db::getInstance()->getValue($sql);
            {
                $sql .= ' GROUP BY id_comment ' . ($order_by ? ' ORDER BY ' . pSQL($order_by) : '');
                if ($having)
                    $sql .= ' HAVING 1' . (string)$having;
                if ($limit)
                    $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
                return Db::getInstance()->executeS($sql);
            }
        }
        return $total ? 0 : array();
    }

    public function getStockAvailables($start = 0, $limit = 12, $order_by = '', $total = false, $filter = '')
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP)
            $shop_group = Shop::getContextShopGroup();
        else
            $shop_group = Context::getContext()->shop->getGroup();
        if ($total)
            $sql = 'SELECT COUNT(DISTINCT stock.id_stock_available)';
        else
            $sql = 'SELECT stock.id_stock_available,stock.quantity,p.id_product,pa.id_product_attribute,pl.name,p.reference,p.active,su.name as supplier_name';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'stock_available` stock
        INNER JOIN `' . _DB_PREFIX_ . 'stock_available` stock_shop ON (stock.id_stock_available = stock_shop.id_stock_available AND stock_shop.id_shop="' . ($shop_group->share_stock ? 0 : (int)Context::getContext()->shop->id) . '")
        INNER JOIN `' . _DB_PREFIX_ . 'product` p ON(stock.id_product=p.id_product)
        INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` sp ON (sp.id_product=p.id_product)
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.id_product= pl.id_product AND pl.id_lang="' . (int)Context::getContext()->language->id . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (pa.id_product_attribute= stock.id_product_attribute)
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa2 ON (pa2.id_product= stock.id_product)
        LEFT JOIN `' . _DB_PREFIX_ . 'supplier` su ON (su.id_supplier=p.id_supplier)
        WHERE sp.id_customer="' . (int)$this->id_customer . '" AND (pa2.id_product_attribute is null OR stock.id_product_attribute!=0)' . ($filter ? (string)$filter : '');
        if ($total)
            return Db::getInstance()->getValue($sql);
        else {
            $sql .= ' GROUP BY stock.id_stock_available' . ($order_by ? ' ORDER BY ' . pSQL($order_by) : '');
            if ($limit)
                $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
            return Db::getInstance()->executeS($sql);
        }
    }

    public function getFeatures($filter = '', $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if (!Configuration::get('ETS_MP_SELLER_CREATE_FEATURE') && !Configuration::get('ETS_MP_SELLER_USER_GLOBAL_FEATURE')) {
            return $total ? 0 : array();
        }
        if (!Configuration::get('ETS_MP_SELLER_CREATE_FEATURE'))
            $filter .= ' AND feature_seller.id_customer is null';
        elseif (!Configuration::get('ETS_MP_SELLER_USER_GLOBAL_FEATURE'))
            $filter .= ' AND feature_seller.id_customer =' . (int)$this->id_customer;
        elseif ($this->user_feature == 1)
            $filter .= ' AND feature_seller.id_customer is null';
        elseif ($this->user_feature == 2)
            $filter .= ' AND feature_seller.id_customer =' . (int)$this->id_customer;
        else
            $filter .= ' AND (feature_seller.id_customer="' . (int)$this->id_customer . '" OR feature_seller.id_customer is null)';
        if ($total)
            $sql = 'SELECT COUNT(DISTINCT f.id_feature)';
        else
            $sql = 'SELECT f.*,fl.name,COUNT(DISTINCT fv.id_feature_value) as total_featuresvalue,feature_seller.id_customer';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'feature` f
        INNER JOIN `' . _DB_PREFIX_ . 'feature_shop` fs ON (f.id_feature = fs.id_feature)
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_feature_seller` feature_seller ON (feature_seller.id_feature = f.id_feature)
        LEFT JOIN `' . _DB_PREFIX_ . 'feature_lang` fl ON (f.id_feature = fl.id_feature AND fl.id_lang="' . (int)Context::getContext()->language->id . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'feature_value` fv ON (fv.id_feature = f.id_feature AND fv.custom=0)
        WHERE fs.id_shop="' . (int)Context::getContext()->shop->id . '"' . ($filter ? $filter : '');
        if (!$total) {
            $sql .= ' GROUP BY f.id_feature' . ($order_by ? ' ORDER By ' . pSQL($order_by) : '');
            if ($limit)
                $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
        }
        if ($total)
            return Db::getInstance()->getValue($sql);
        else
            return Db::getInstance()->executeS($sql);
    }

    public function getFeatureValues($filter = '', $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if (!Configuration::get('ETS_MP_SELLER_CREATE_FEATURE') && !Configuration::get('ETS_MP_SELLER_USER_GLOBAL_FEATURE')) {
            return $total ? 0 : array();
        }
        if (!Configuration::get('ETS_MP_SELLER_CREATE_FEATURE'))
            $filter .= ' AND feature_seller.id_customer is null';
        elseif (!Configuration::get('ETS_MP_SELLER_USER_GLOBAL_FEATURE'))
            $filter .= ' AND feature_seller.id_customer is not null';
        elseif ($this->user_feature == 1)
            $filter .= ' AND feature_seller.id_customer is null';
        elseif ($this->user_feature == 2)
            $filter .= ' AND feature_seller.id_customer is not null';
        else
            $filter .= ' AND (feature_seller.id_customer="' . (int)$this->id_customer . '" OR feature_seller.id_customer is null)';
        if ($total)
            $sql = 'SELECT COUNT(DISTINCT fv.id_feature_value)';
        else
            $sql = 'SELECT fv.*,fvl.value,feature_seller.id_customer';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'feature_value` fv 
        INNER JOIN `' . _DB_PREFIX_ . 'feature` f ON (fv.id_feature = f.id_feature)
        INNER JOIN `' . _DB_PREFIX_ . 'feature_shop` fs ON (f.id_feature AND fs.id_shop)
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_feature_seller` feature_seller ON (feature_seller.id_feature = f.id_feature AND feature_seller.id_customer="' . (int)$this->id_customer . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl ON (fv.id_feature_value= fvl.id_feature_value AND fvl.id_lang="' . (int)Context::getContext()->language->id . '")
        WHERE fv.custom=0 AND fs.id_shop="' . (int)Context::getContext()->shop->id . '"' . ($filter ? (string)$filter : '');
        if (!$total) {
            $sql .= ' GROUP BY fv.id_feature_value' . ($order_by ? ' ORDER By ' . pSQL($order_by) : '');
            if ($limit)
                $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
        }
        if ($total)
            return Db::getInstance()->getValue($sql);
        else
            return Db::getInstance()->executeS($sql);
    }

    public function getAttributeGroups($filter = '', $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if (!Configuration::get('ETS_MP_SELLER_CREATE_PRODUCT_ATTRIBUTE') || (!Configuration::get('ETS_MP_SELLER_CREATE_ATTRIBUTE') && !Configuration::get('ETS_MP_SELLER_USER_GLOBAL_ATTRIBUTE')))
            return $total ? 0 : array();
        elseif (!Configuration::get('ETS_MP_SELLER_CREATE_ATTRIBUTE'))
            $filter .= ' AND agse.id_customer is null';
        elseif (!Configuration::get('ETS_MP_SELLER_USER_GLOBAL_ATTRIBUTE'))
            $filter .= ' AND agse.id_customer =' . (int)$this->id_customer;
        elseif ($this->user_attribute == 1)
            $filter .= ' AND agse.id_customer is null';
        elseif ($this->user_attribute == 2)
            $filter .= ' AND agse.id_customer="' . (int)$this->id_customer . '"';
        else
            $filter .= ' AND (agse.id_customer is null OR agse.id_customer="' . (int)$this->id_customer . '")';
        if ($total)
            $sql = 'SELECT COUNT(DISTINCT ag.id_attribute_group)';
        else
            $sql = 'SELECT ag.*,agl.name,COUNT(a.id_attribute) as total_attribute,agse.id_customer';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'attribute_group` ag 
        INNER JOIN `' . _DB_PREFIX_ . 'attribute_group_shop` ags ON (ags.id_attribute_group= ag.id_attribute_group)
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_attribute_group_seller` agse ON (agse.id_attribute_group=ag.id_attribute_group)
        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (agl.id_attribute_group = ag.id_attribute_group AND agl.id_lang="' . (int)Context::getContext()->language->id . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON (a.id_attribute_group = ag.id_attribute_group)
        WHERE ags.id_shop="' . (int)Context::getContext()->shop->id . '"' . ($filter ? (string)$filter : '');
        if (!$total) {
            $sql .= ' GROUP BY ag.id_attribute_group' . ($order_by ? ' ORDER By ' . pSQL($order_by) : '');
            if ($limit)
                $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
        }
        if ($total)
            return Db::getInstance()->getValue($sql);
        else
            return Db::getInstance()->executeS($sql);
    }
    public function getIDAttribute($name,$color,$name_group)
    {
        $sql = 'SELECT a.id_attribute FROM `'._DB_PREFIX_.'attribute` a
        INNER JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.id_attribute_group=a.id_attribute_group)
        INNER JOIN `'._DB_PREFIX_.'attribute_group_shop` ags ON(ag.id_attribute_group=ags.id_attribute_group AND ags.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_attribute_group_seller` attribute_group_seller ON (ag.id_attribute_group=attribute_group_seller.id_attribute_group)
        LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.id_attribute= al.id_attribute ANd al.id_lang="'.(int)Context::getContext()->language->id.'")
        LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (agl.id_attribute_group=ag.id_attribute_group AND agl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE attribute_group_seller.id_customer="'.(int)$this->id_customer.'" AND al.name="'.pSQL($name).'" AND a.color="'.pSQL($color).'" AND agl.name="'.pSQL($name_group).'"';
        return Db::getInstance()->getValue($sql);
    }
    public function getAttributes($filter = '', $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if ($total) {
            $sql = 'SELECT COUNT(DISTINCT a.id_attribute)';
        } else
            $sql = 'SELECT a.*,al.name';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'attribute` a
        INNER JOIN `' . _DB_PREFIX_ . 'attribute_shop` attribute_shop ON (a.id_attribute = attribute_shop.id_attribute)
        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.id_attribute = al.id_attribute AND al.id_lang="' . (int)Context::getContext()->language->id . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON (a.id_attribute_group = ag.id_attribute_group)
        WHERE attribute_shop.id_shop="' . (int)Context::getContext()->shop->id . '"' . ($filter ? (string)$filter : '');
        if (!$total) {
            $sql .= ' GROUP BY a.id_attribute' . ($order_by ? ' ORDER By ' . pSQL($order_by) : '');
            $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
        }
        if ($total)
            return Db::getInstance()->getValue($sql);
        else
            return Db::getInstance()->executeS($sql);
    }

    public function getTotalCommission($status = false,$id_product=0,$filter=false)
    {
        $sql = 'SELECT SUM(commission) FROM `' . _DB_PREFIX_ . 'ets_mp_seller_commission` 
        WHERE id_customer="' . (int)$this->id_customer . '" AND id_shop="'.(int)Context::getContext()->shop->id.'"' . ($status !== false ? ' AND status="' . (int)$status . '"' : '').($filter ? (string)$filter:'').($id_product ? ' AND id_product="'.(int)$id_product.'"':'');
        return (float)Db::getInstance()->getValue($sql);
    }
    public function getToTalUseCommission($status = false, $pay_for_order = false, $voucher = false, $withdraw = false)
    {
        $sql = 'SELECT SUM(amount) FROM `' . _DB_PREFIX_ . 'ets_mp_commission_usage` WHERE id_customer="' . (int)$this->id_customer . '"' . ($status !== false ? ' AND status="' . (int)$status . '"' : '') . ($pay_for_order ? ' AND id_order!=0' : '') . ($voucher ? ' AND id_voucher!=0' : '') . ($withdraw ? ' AND id_withdraw!=0' : '');
        return (float)Db::getInstance()->getValue($sql);
    }
    public function update($null_values = true)
    {
        $seller_old = Db::getInstance()->getRow('SELECT active,vacation_mode,vacation_type,id_group FROM `' . _DB_PREFIX_ . 'ets_mp_seller` WHERE id_seller=' . (int)$this->id);
        $return = parent::update($null_values);
        if ($return && $seller_old['active'] != $this->active) {
            $this->rebuildLayeredCache($this->active);
            $data = array(
                '{seller_name}' => $this->seller_name,
                '{reason}' => $this->reason,
                '{store_email}' => Configuration::get('ETS_MP_EMAIL_ADMIN_NOTIFICATION') ?: Configuration::get('PS_SHOP_EMAIL'),
            );
            if ($this->active == 1 && Configuration::get('ETS_MP_EMAIL_SELLER_SHOP_ACTIVED_OR_DECLINED')) {
                $subjects = array(
                    'translation' => $this->l('Your shop has been activated'),
                    'origin' => 'Your shop has been activated',
                    'specific' => 'seller'
                );
                Ets_marketplace::sendMail('to_seller_shop_actived', $data, $this->seller_email, $subjects, $this->seller_name);
            } elseif ($this->active == 0 && Configuration::get('ETS_MP_EMAIL_SELLER_DISABLED')) {
                $subjects = array(
                    'translation' => $this->l('Your shop has been disabled'),
                    'origin' => 'Your shop has been disabled',
                    'specific' => 'seller'
                );
                Ets_marketplace::sendMail('to_seller_account_disabled', $data, $this->seller_email, $subjects, $this->seller_name);
            } elseif ($this->active == -2 && Configuration::get('ETS_MP_EMAIL_SELLER_EXPIRED')) {
                $subjects = array(
                    'translation' => $this->l('Your shop is expired'),
                    'origin' => 'Your shop is expired',
                    'specific' => 'seller'
                );
                Ets_marketplace::sendMail('to_seller_account_expired', $data, $this->seller_email, $subjects, $this->seller_name);
            } elseif ($this->active == -3 && Configuration::get('ETS_MP_EMAIL_SELLER_SHOP_ACTIVED_OR_DECLINED')) {
                $subjects = array(
                    'translation' => $this->l('Your shop has been declined'),
                    'origin' => 'Your shop has been declined',
                    'specific' => 'seller'
                );
                Ets_marketplace::sendMail('to_seller_shop_declined', $data, $this->seller_email, $subjects, $this->seller_name);
            }
        }
        if ($this->id_group != $seller_old['id_group']) {
            if ($this->active == 1 && Configuration::get('ETS_MP_EMAIL_SELLER_UPGRADED_GROUP')) {
                $fee_type = $this->getFeeType();
                $fee_type_text = $this->l('No fee');
                switch ($fee_type) {
                    case 'no_fee':
                        $fee_type_text = $this->l('No fee');
                        break;
                    case 'pay_once':
                        $fee_type_text = $this->l('Pay once');
                        break;
                    case 'monthly_fee':
                        $fee_type_text = $this->l('Monthly fee');
                        break;
                    case 'quarterly_fee':
                        $fee_type_text = $this->l('Quarterly fee');
                        break;
                    case 'yearly_fee':
                        $fee_type_text = $this->l('Yearly fee');
                        break;
                }
                $data = array(
                    '{seller_name}' => $this->seller_name,
                    '{fee_type}' => $fee_type_text,
                    '{fee_amount}' => $this->getFeeAmount(),
                    '{commission_rate}' => $this->getCommissionRate(),
                );
                $subjects = array(
                    'translation' => $this->l('Your shop is upgraded'),
                    'origin' => 'Your shop is upgraded',
                    'specific' => 'seller'
                );
                Ets_marketplace::sendMail('to_seller_upgraded_group', $data, $this->seller_email, $subjects, $this->seller_name);
            }
        }
        if ($this->vacation_mode != $seller_old['vacation_mode'] || $this->vacation_type != $seller_old['vacation_type']) {
            if ($this->vacation_mode) {
                if ($this->vacation_type == 'disable_product' || $this->vacation_type == 'disable_product_and_show_notifications')
                    $this->rebuildLayeredCache(0);
                elseif ($this->vacation_type == 'disable_shopping' || $this->vacation_type == 'disable_shopping_and_show_notifications') {
                    $this->rebuildLayeredCache(1, 0);
                } else
                    $this->rebuildLayeredCache(1);
            } else
                $this->rebuildLayeredCache(1);
        }
        return $return;
    }

    public function deleteProducts()
    {
        $products = Db::getInstance()->executeS('SELECT p.id_product FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product` sp
        INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (sp.id_product = p.id_product) 
        WHERE sp.id_customer=' . (int)$this->id_customer);
        if ($products) {
            foreach ($products as $product) {
                $product_class = new Product($product['id_product']);
                $product_class->delete();
            }
        }
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product` WHERE id_customer=' . (int)$this->id_customer);
        $this->deleteAttributes();
        $this->deleteFeatures();
        $this->deleteCartRules();
        return true;
    }

    public function deleteFeatures()
    {
        $features = Db::getInstance()->executeS('SELECT f.id_feature FROM `' . _DB_PREFIX_ . 'ets_mp_feature_seller` fs
        INNER JOIN `' . _DB_PREFIX_ . 'feature` f ON (f.id_feature = fs.id_feature)
        WHERE fs.id_customer=' . (int)$this->id_customer);
        if ($features) {
            foreach ($features as $feature) {
                $feature_class = new Feature($feature['id_feature']);
                $feature_class->delete();
            }
        }
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_feature_seller` WHERE id_customer=' . (int)$this->id_customer);
        return true;
    }

    public function deleteAttributes()
    {
        $attributeGroups = Db::getInstance()->executeS('SELECT ags.id_attribute_group FROM `' . _DB_PREFIX_ . 'ets_mp_attribute_group_seller` ags
        INNER JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON (ag.id_attribute_group = ags.id_attribute_group)
        WHERE ags.id_customer="' . (int)$this->id_customer . '"');
        if ($attributeGroups) {
            foreach ($attributeGroups as $attributeGroup) {
                $attributeGroup_class = new AttributeGroup($attributeGroup['id_attribute_group']);
                $attributeGroup_class->delete();
            }
        }
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_attribute_group_seller` WHERE id_customer=' . (int)$this->id_customer);
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_attribute_seller` WHERE id_customer=' . (int)$this->id_customer);
        return true;
    }

    public function deleteCartRules()
    {
        $cartRules = Db::getInstance()->executeS('SELECT crs.id_cart_rule FROM `' . _DB_PREFIX_ . 'ets_mp_cart_rule_seller` crs
        INNER JOIN `' . _DB_PREFIX_ . 'cart_rule` cr ON (cr.id_cart_rule = crs.id_cart_rule)
        WHERE crs.id_customer=' . (int)$this->id_customer);
        if ($cartRules) {
            foreach ($cartRules as $cartRule) {
                $cartRule_class = new CartRule($cartRule['id_cart_rule']);
                $cartRule_class->delete();
            }
        }
        return true;
    }

    public function deleteCommissions()
    {
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_seller_commission` WHERE id_customer=' . (int)$this->id_customer);
        $commission_usages = Db::getInstance()->executeS('SELECT id_ets_mp_commission_usage FROM `' . _DB_PREFIX_ . 'ets_mp_commission_usage` WHERE id_customer=' . (int)$this->id_customer);
        if ($commission_usages) {
            foreach ($commission_usages as $commission_usage) {
                $commission_usage_class = new Ets_mp_commission_usage($commission_usage['id_ets_mp_commission_usage']);
                $commission_usage_class->delete();
            }
        }
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_commission_usage` WHERE id_customer=' . (int)$this->id_customer);
        return true;
    }

    public function deleteSuppliers()
    {
        $suppliers = Db::getInstance()->executeS('SELECT s.id_supplier FROM `' . _DB_PREFIX_ . 'ets_mp_supplier_seller` ss
        INNER JOIN `' . _DB_PREFIX_ . 'supplier` s ON (ss.id_supplier = s.id_supplier)
        WHERE ss.id_customer=' . (int)$this->id_customer);
        if ($suppliers) {
            foreach ($suppliers as $supplier) {
                $supllier_class = new Supplier($supplier['id_supplier']);
                $supllier_class->delete();
            }
        }
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_supplier_seller` WHERE id_customer=' . (int)$this->id_customer);
        return true;
    }

    public function deleteManufacturers()
    {
        $manufacturers = Db::getInstance()->executeS('SELECT m.id_manufacturer FROM `' . _DB_PREFIX_ . 'ets_mp_manufacturer_seller` ms
        INNER JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.id_manufacturer =ms.id_manufacturer)
        WHERE ms.id_customer = ' . (int)$this->id_customer);
        if ($manufacturers) {
            foreach ($manufacturers as $manufacturer) {
                $manufacturer_class = new Manufacturer($manufacturer['id_manufacturer']);
                $manufacturer_class->delete();
            }
        }
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_manufacturer_seller` WHERE id_customer=' . (int)$this->id_customer);
        return true;
    }

    public function deleteCarriers()
    {
        $carriers = Db::getInstance()->executeS('SELECT c.id_carrier  FROM `' . _DB_PREFIX_ . 'ets_mp_carrier_seller` cs
        INNER JOIN `' . _DB_PREFIX_ . 'carrier` c ON (cs.id_carrier_reference = c.id_reference)
        WHERE c.deleted=0 AND cs.id_customer=' . (int)$this->id_customer);
        if ($carriers) {
            foreach ($carriers as $carrier) {
                $carrier_class = new Carrier($carrier['id_carrier']);
                $carrier_class->delete();
            }
        }
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_carrier_seller` WHERE id_customer=' . (int)$this->id_customer);
        return true;
    }

    public function deleteOrders()
    {
        $orders = Db::getInstance()->executeS('SELECT o.id_order FROM `' . _DB_PREFIX_ . 'ets_mp_seller_order` so
        INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON (so.id_order = o.id_order)
        WHERE so.id_customer=' . (int)$this->id_customer);
        if ($orders) {
            foreach ($orders as $order) {
                $order_class = new Order($order['id_order']);
                $order_class->delete();
            }
        }
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_seller_order` WHERE id_customer=' . (int)$this->id_customer);
        return true;
    }

    public function deleteExtraData()
    {
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_seller_billing` WHERE id_customer=' . (int)$this->id_customer)
            && Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_seller_report` WHERE id_customer=' . (int)$this->id_customer)
            && Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_seller_customer_follow` WHERE id_customer=' . (int)$this->id_customer)
            && Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_seller_manager` WHERE id_customer=' . (int)$this->id_customer);
    }

    public function delete($deleteData = false)
    {
        $result = parent::delete();
        if ($result) {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_seller_manager` WHERE id_customer=' . (int)$this->id_customer);
            if ($this->shop_logo && !Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_mp_registration` WHERE shop_logo="' . pSQL($this->shop_logo) . '"')) {
                if (file_exists(_PS_IMG_DIR_ . 'mp_seller/' . $this->shop_logo))
                    @unlink(_PS_IMG_DIR_ . 'mp_seller/' . $this->shop_logo);
            }
            if ($deleteData) {
                $registration = Ets_mp_registration::_getRegistration($this->id_customer);
                if ($registration && Validate::isLoadedObject($registration))
                    $registration->delete();
                $this->deleteProducts();
                $this->deleteCommissions();
                $this->deleteCarriers();
                $this->deleteSuppliers();
                $this->deleteManufacturers();
                $this->deleteOrders();
                $this->deleteExtraData();
            }
            $this->rebuildLayeredCache(false);

        }
        return $result;
    }

    public function l($string)
    {
        return Translate::getModuleTranslation('ets_marketplace', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }

    public function rebuildLayeredCache($active, $available_for_order = 1)
    {
        $products = Db::getInstance()->executeS('SELECT id_product FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product` WHERE id_customer=' . (int)$this->id_customer . ' AND active=1');
        $productsIds = array();
        foreach ($products as $product) {
            $productsIds[] = $product['id_product'];
        }
        if ($productsIds) {
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'product` SET active=' . ($active ? 1 : 0) . ',available_for_order=' . ($available_for_order ? 1 : 0) . ' WHERE (active=' . ($active ? 0 : 1) . ' OR available_for_order=' . ($available_for_order ? 0 : 1) . ') AND id_product IN (' . implode(',', array_map('intval', $productsIds)) . ')');
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'product_shop` SET active=' . ($active ? 1 : 0) . ',available_for_order=' . ($available_for_order ? 1 : 0) . ' WHERE (active=' . ($active ? 0 : 1) . ' OR available_for_order=' . ($available_for_order ? 0 : 1) . ') AND id_product IN (' . implode(',', array_map('intval', $productsIds)) . ')');
            if (Module::isEnabled('ps_facetedsearch'))
                $search = Module::getInstanceByName('ps_facetedsearch');
            elseif (Module::isEnabled('blocklayered'))
                $search = Module::getInstanceByName('blocklayered');
            if (isset($search) && $search) {
                $search->rebuildLayeredCache($productsIds);
            }
            if ((int)Configuration::get('ETS_SPEED_ENABLE_PAGE_CACHE') && Module::isInstalled('ets_marketplace') && Module::isEnabled('ets_marketplace') && class_exists('Ets_ss_class_cache')) {
                $cacheObjSuperSpeed = new Ets_ss_class_cache();
                if (method_exists($cacheObjSuperSpeed, 'deleteCache'))
                    $cacheObjSuperSpeed->deleteCache();
            }
            if ((int)Configuration::get('ETS_SPEED_ENABLE_PAGE_CACHE') && Module::isInstalled('ets_marketplace') && Module::isEnabled('ets_marketplace') && class_exists('Ets_pagecache_class_cache')) {
                $cacheObjPageCache = new Ets_pagecache_class_cache();
                if (method_exists($cacheObjPageCache, 'deleteCache'))
                    $cacheObjPageCache->deleteCache();
            }
        }
    }

    static public function _getSellerByIdCustomer($id_customer, $id_lang = null, $active = false)
    {
        $id_seller = (int)Db::getInstance()->getValue('SELECT id_seller FROM `' . _DB_PREFIX_ . 'ets_mp_seller` WHERE id_customer=' . (int)$id_customer . ($active ? ' AND active=1' : ''));
        if ($id_seller)
            return new Ets_mp_seller($id_seller, $id_lang);
        else
            return false;
    }

    public function getLink()
    {
        $module = Module::getInstanceByName('ets_marketplace');
        return $module->getShopLink(array('id_seller' => $this->id));
    }

    public function getAVGReviewProduct()
    {
        if (Module::isInstalled('ets_reviews') && Module::isEnabled('ets_reviews')) {
            $sql = 'SELECT AVG(pc.grade) as avg_grade,COUNT(pc.grade) as count_grade FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` pc
            INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (pc.id_product=p.id_product AND p.active=1)
            INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` seller_product ON (seller_product.id_product=pc.id_product)
            WHERE seller_product.id_customer="' . (int)$this->id_customer . '"' . (Configuration::get('ETS_PC_MODERATE') ? ' AND pc.validate=1' : '') . ' AND pc.grade!=0';
            return Db::getInstance()->getRow($sql);
        }
        if (Module::isInstalled('productcomments') && Module::isEnabled('productcomments')) {
            $sql = 'SELECT AVG(pc.grade) as avg_grade,COUNT(pc.grade) as count_grade FROM `' . _DB_PREFIX_ . 'product_comment` pc
            INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (pc.id_product=p.id_product AND p.active=1)
            INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` seller_product ON (seller_product.id_product=pc.id_product)
            WHERE seller_product.id_customer="' . (int)$this->id_customer . '"' . (Configuration::get('PRODUCT_COMMENTS_MODERATE') ? ' AND pc.validate=1' : '') . ' AND pc.grade!=0';
            return Db::getInstance()->getRow($sql);
        }
        return false;
    }

    public function getCarriers($filter = '', $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if (!Configuration::get('ETS_MP_SELLER_CREATE_SHIPPING') && !Configuration::get('ETS_MP_SELLER_USER_GLOBAL_SHIPPING'))
            return $total ? 0 : array();
        if (!Configuration::get('ETS_MP_SELLER_CREATE_SHIPPING'))
            $filter .= ' AND cs.id_carrier_reference is NULL';
        elseif (!Configuration::get('ETS_MP_SELLER_USER_GLOBAL_SHIPPING'))
            $filter .= ' AND cs.id_customer="' . (int)$this->id_customer . '"';
        elseif ($this->user_shipping == 1)
            $filter .= ' AND cs.id_carrier_reference is NULL';
        elseif ($this->user_shipping == 3)
            $filter .= ' AND (cs.id_customer="' . (int)$this->id_customer . '" OR cs.id_carrier_reference is NULL)';
        else
            $filter = ' AND cs.id_customer="' . (int)$this->id_customer . '"';
        if ($total)
            $sql = 'SELECT COUNT(DISTINCT c.id_carrier)';
        else
            $sql = 'SELECT *';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'carrier` c 
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_carrier_seller` cs ON (c.id_reference= cs.id_carrier_reference)
        LEFT JOIN `' . _DB_PREFIX_ . 'carrier_lang` cl ON (c.id_carrier=cl.id_carrier AND cl.id_lang="' . (int)Context::getContext()->language->id . '")     
        WHERE c.deleted=0 ' . ($filter ? (string)$filter : '') . (!$total ? ' GROUP BY c.id_carrier' : '')
            . ($order_by ? ' ORDER By ' . pSQL($order_by) : '');
        if (!$total && $limit)
            $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
        if ($total)
            return Db::getInstance()->getValue($sql);
        else {
            return Db::getInstance()->executeS($sql);
        }
    }

    public function getUserManagers($filter = '', $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if ($total)
            $sql = 'SELECT COUNT(DISTINCT m.id_ets_mp_seller_manager)';
        else
            $sql = 'SELECT m.*,CONCAT(c.firstname," ",c.lastname) as name';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'ets_mp_seller_manager` m 
        INNER JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.id_customer=m.id_user)
        WHERE m.id_customer="' . (int)$this->id_customer . '" ' . ($filter ? (string)$filter : '') . ($order_by ? ' ORDER By ' . pSQL($order_by) : '');
        if ($total)
            return Db::getInstance()->getValue($sql);
        else {
            $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
            return Db::getInstance()->executeS($sql);
        }
    }

    public function _getTotalNumberOfProductSold($id_product = 0,$filter='')
    {
        if (Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN')) {
            $status = explode(',', Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'));
            $sql = 'SELECT sum(product_quantity) FROM `' . _DB_PREFIX_ . 'order_detail` od
            INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON (od.id_order=o.id_order)
            INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` seller_product ON (seller_product.id_product=od.product_id)
            WHERE seller_product.id_customer="' . (int)$this->id_customer . '" AND o.current_state IN (' . implode(',', array_map('intval', $status)) . ')' . ($id_product ? ' AND od.product_id="' . (int)$id_product . '"' : '').($filter ? (string)$filter:'');
            return (int)Db::getInstance()->getValue($sql);
        } else
            return 0;
    }
    public function getTotalTurnOver($id_product=0,$filter=false)
    {
        if(Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'))
        {
            $status = explode(',',Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'));
            if($id_product)
            {
                $sql = 'SELECT SUM(od.total_price_tax_incl/c.conversion_rate) FROM `'._DB_PREFIX_.'orders` o
                INNER JOIN `'._DB_PREFIX_.'order_detail` od ON(od.id_order=o.id_order)
                INNER JOIN `'._DB_PREFIX_.'currency` c ON (o.id_currency=c.id_currency)
                INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_order` seller_order ON (seller_order.id_order=o.id_order AND seller_order.id_customer="'.(int)$this->id_customer.'")
                WHERE o.id_shop="'.(int)Context::getContext()->shop->id.'" AND o.current_state IN ('.implode(',',array_map('intval',$status)).')'.($filter ? (string)$filter:'').' AND od.product_id="'.(int)$id_product.'"';
            }
            else
            {
                $sql = 'SELECT SUM(o.total_paid/c.conversion_rate) FROM `'._DB_PREFIX_.'orders` o
                INNER JOIN `'._DB_PREFIX_.'order_detail` od ON(od.id_order=o.id_order)
                INNER JOIN `'._DB_PREFIX_.'currency` c ON (o.id_currency=c.id_currency)
                INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_order` seller_order ON (seller_order.id_order=o.id_order AND seller_order.id_customer="'.(int)$this->id_customer.'")
                WHERE o.id_shop="'.(int)Context::getContext()->shop->id.'" AND o.current_state IN ('.implode(',',array_map('intval',$status)).')'.($filter ? (string)$filter:'');
            }
            $turn_over = Db::getInstance()->getValue($sql);
            return Tools::convertPrice($turn_over);
        }
        return 0;
    }

    public function getFeeType()
    {
        if ($this->id_group) {
            $group = new Ets_mp_seller_group($this->id_group);
            if (!$group->use_fee_global && $group->fee_type)
                return $group->fee_type;
        }
        return Configuration::get('ETS_MP_SELLER_FEE_TYPE');
    }

    public function getFeeAmount()
    {
        if ($this->id_group) {
            $group = new Ets_mp_seller_group($this->id_group);
            if (!$group->use_fee_global && $group->fee_amount)
                return $group->fee_amount;
        }
        return Configuration::get('ETS_MP_SELLER_FEE_AMOUNT');
    }

    public function getCommissionRate($default = false, $id_product = 0)
    {
        if (!$default && !$id_product && $this->commission_rate)
            return $this->commission_rate;
        else {
            if ($default || !$id_product) {
                if ($this->id_group) {
                    $group = new Ets_mp_seller_group($this->id_group);
                    if (!$group->use_commission_global && $group->commission_rate)
                        return $group->commission_rate;
                }
                return Configuration::get('ETS_MP_COMMISSION_RATE');
            }
            if ($id_product && ($product = new Product($id_product)) && Validate::isLoadedObject($product)) {
                $id_category = $product->id_category_default;
                if ($id_category && $this->enable_commission_by_category && ($commission_rate = (float)Db::getInstance()->getValue('SELECT commission_rate FROM `' . _DB_PREFIX_ . 'ets_mp_category_commission_seller` WHERE id_category="' . (int)$id_category . '" AND id_seller=' . (int)$this->id)))
                    return $commission_rate;
                elseif ($this->commission_rate)
                    return $this->commission_rate;
                elseif ($this->id_group && ($group = new Ets_mp_seller_group($this->id_group)) && Validate::isLoadedObject($group)) {
                    if (!$group->use_commission_global) {
                        if ($id_category && $group->enable_commission_by_category && ($commission_rate = (float)Db::getInstance()->getValue('SELECT commission_rate FROM `' . _DB_PREFIX_ . 'ets_mp_category_commission_group` WHERE id_category="' . (int)$id_category . '" AND id_group=' . (int)$group->id)))
                            return $commission_rate;
                        elseif ($group->commission_rate)
                            return $group->commission_rate;
                    }
                }
                if ($id_category && Configuration::get('ETS_MP_ENABLE_COMMISSION_BY_CATEGORY') && ($commission_rate = (float)Db::getInstance()->getValue('SELECT commission_rate FROM `' . _DB_PREFIX_ . 'ets_mp_category_commission` WHERE id_category=' . (int)$id_category)))
                    return $commission_rate;
                else
                    return Configuration::get('ETS_MP_COMMISSION_RATE');
            }
        }
    }

    public function getFeeTax()
    {
        if ($this->id_group) {
            $group = new Ets_mp_seller_group($this->id_group);
            if (!$group->use_commission_global && $group->id)
                return $group->fee_tax;
        }
        return Configuration::get('ETS_MP_SELLER_FEE_TAX');
    }

    public function checkHasOrder($id_order)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_mp_seller_order` WHERE id_order=' . (int)$id_order . ' AND id_customer=' . (int)$this->id_customer);
    }

    public function checkHasProduct($id_product)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product` WHERE id_product="' . (int)$id_product . '" AND id_customer=' . (int)$this->id_customer);
    }
    public function checkDeleteProduct()
    {
        if(!Configuration::get('ETS_MP_ALLOW_SELLER_DELETE_PRODUCT'))
            return false;
        if($this->id_customer == Context::getContext()->customer->id)
            return true;
        elseif((int)Db::getInstance()->getValue('SELECT delete_product FROM `'._DB_PREFIX_.'ets_mp_seller_manager` WHERE email="'.pSQL(Context::getContext()->customer->email).'" AND id_customer="'.(int)$this->id_customer.'"'))
            return true;
        else
            return false;

    }
    public function getListCarriersUser($id_carrier = 0, $id_reference = 0)
    {
        $carriers = $this->getCarriers(' AND c.active=1 ' . ($id_carrier != 0 ? ' AND c.id_carrier="' . (int)$id_carrier . '"' : '') . ($id_reference != 0 ? ' AND c.id_reference="' . (int)$id_reference . '"' : ''), false, false);
        if ($id_carrier || $id_reference)
            return $carriers ? $carriers[0] : array();
        else
            return $carriers;
    }

    public function checkHasManufacturer($id_manufacturer, $user = true)
    {
        if ($user)
            return $this->getManufacturers(' AND m.active=1 AND m.id_manufacturer=' . (int)$id_manufacturer);
        else
            return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_mp_manufacturer_seller` WHERE id_customer="' . (int)$this->id_customer . '" AND id_manufacturer=' . (int)$id_manufacturer);

    }

    public function checkHasSupplier($id_supplier, $user = true)
    {
        if ($user)
            return $this->getSuppliers(' AND s.active=1 AND s.id_supplier=' . (int)$id_supplier);
        else
            return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_mp_supplier_seller` WHERE id_customer="' . (int)$this->id_customer . '" AND id_supplier=' . (int)$id_supplier);

    }

    public function checkHasFeature($id_feature, $user = true)
    {
        if ($user) {
            return $this->getFeatures(' AND f.id_feature="' . (int)$id_feature . '"') ? true : false;
        } else
            return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_mp_feature_seller` WHERE id_feature="' . (int)$id_feature . '" AND id_customer="' . (int)$this->id_customer . '"');
    }

    public function checkHasAttributeGroup($id_attribute_group, $user = true)
    {
        if ($user)
            return $this->getAttributeGroups(' AND ag.id_attribute_group = "' . (int)$id_attribute_group . '"') ? true : false;
        else {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'attribute_group` ag
            INNER JOIN `' . _DB_PREFIX_ . 'attribute_group_shop` ags ON (ag.id_attribute_group=ags.id_attribute_group AND ags.id_shop="' . (int)Context::getContext()->shop->id . '")
            INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_attribute_group_seller` agse ON (agse.id_attribute_group = ag.id_attribute_group)
            WHERE ag.id_attribute_group="' . (int)$id_attribute_group . '" AND agse.id_customer="' . (int)$this->id_customer . '"';
            return Db::getInstance()->getRow($sql);
        }

    }

    public function checkHasCartRule($id_cart_rule)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_mp_cart_rule_seller` WHERE id_cart_rule="' . (int)$id_cart_rule . '" AND id_customer="' . (int)$this->id_customer . '"');
    }

    public function confirmedPayment()
    {
        $billing = new Ets_mp_billing($this->id_billing);
        $billing->seller_confirm = 1;
        if (Validate::isLoadedObject($billing) && $billing->update(true)) {
            if (Configuration::get('ETS_MP_EMAIL_ADMIN_CONFIRMED_PAYMENT')) {
                $data = array(
                    '{seller_name}' => $this->seller_name,
                    '{billing_number}' => $billing->getBillingNumberInvoice(),
                    '{shop_seller}' => $this->shop_name[Context::getContext()->language->id],
                    '{amount}' => Tools::displayPrice($billing->amount, new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))),
                    '{confirmed_date}' => date('Y-m-d H:i:s'),
                );
                $subjects = array(
                    'translation' => $this->l('A seller has confirmed payment'),
                    'origin' => 'A seller has confirmed payment',
                    'specific' => 'seller',
                );
                Ets_marketplace::sendMail('to_admin_seller_confirmed_payment', $data, '', $subjects);

            }
            die(
                json_encode(
                    array(
                        'success' => ($message = Configuration::get('ETS_MP_MESSAGE_CONFIRMED_PAYMENT', Context::getContext()->language->id)) ? $message : $this->l('Thanks for confirming that you have just sent the fee, we will check it and get back to you as soon as possible'),
                    )
                )
            );
        }
    }

    public static function getMaps($id_seller = 0, $total = false,$params= array())
    {
        $module = Module::getInstanceByName('ets_marketplace');
        $all = isset($params['all']) ?  (int)$params['all']:false;
        if ($all == 1 || $total)
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'ets_mp_seller` s
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_lang` sl ON (s.id_seller = sl.id_seller AND sl.id_lang="' . (int)Context::getContext()->language->id . '")
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` sp ON (s.id_customer=sp.id_customer)
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product = sp.id_product)
            WHERE s.latitude!=0 AND s.longitude!=0 AND s.latitude is not null AND s.longitude is not null' . ($id_seller ? ' AND s.id_seller="' . (int)$id_seller . '"' : ' AND sp.id_product is NOT NULL AND p.active=1 GROUP BY s.id_seller');
        else {
            $distance = isset($params['radius']) ? (int)$params['radius']:100;
            $latitude = isset($params['latitude']) ? (float)$params['latitude']:0;
            $longitude = isset($params['longitude']) ? (float)$params['longitude']:0;
            $multiplicator = 6371;
            $sql = 'SELECT *,(' . (int)$multiplicator . '
				* acos(
					cos(radians(' . (float)$latitude . '))
					* cos(radians(s.latitude))
					* cos(radians(s.longitude) - radians(' . (float)$longitude . '))
					+ sin(radians(' . (float)$latitude . '))
					* sin(radians(s.latitude))
				)
			) as distance FROM `' . _DB_PREFIX_ . 'ets_mp_seller` s
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_lang` sl ON (s.id_seller = sl.id_seller AND sl.id_lang="' . (int)Context::getContext()->language->id . '")
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` sp ON (s.id_customer=sp.id_customer)
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product = sp.id_product)
            WHERE s.latitude!=0 AND s.longitude!=0 AND s.latitude is not null AND s.longitude is not null' . ($id_seller ? ' AND s.id_seller="' . (int)$id_seller . '"' : ' AND sp.id_product is NOT NULL AND p.active=1') .
                ' GROUP BY s.id_seller HAVING distance < ' . (int)$distance . '
			ORDER BY distance ASC
			LIMIT 0,20';
        }
        $maps = Db::getInstance()->executeS($sql);
        if ($total) {
            return count($maps);
        }
        if ($maps) {
            $parnode = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><markers></markers>');
            foreach ($maps as $map) {
                if ($map['latitude'] && $map['longitude']) {
                    $other = $map['shop_phone'] ? $module->l('Shop phone number:', 'seller') . ' ' . $map['shop_phone'] : '';
                    $newnode = $parnode->addChild('marker');
                    $newnode->addAttribute('name', $map['shop_name']);
                    $newnode->addAttribute('addressNoHtml', strip_tags(str_replace(Module::getInstanceByName('ets_marketplace')->displayText('', 'br'), ' ', $map['shop_address'])));
                    $newnode->addAttribute('address', $map['shop_address']);
                    $newnode->addAttribute('other', $other);
                    $newnode->addAttribute('phone', $map['shop_phone']);
                    $newnode->addAttribute('id_store', (int)$map['id_seller']);
                    $newnode->addAttribute('has_store_picture', file_exists(_PS_IMG_DIR_ . '/mp_seller/' . $map['shop_logo']) ? Context::getContext()->link->getMediaLink(__PS_BASE_URI__ . 'img/mp_seller/' . $map['shop_logo']) : false);
                    $newnode->addAttribute('lat', (float)$map['latitude']);
                    $newnode->addAttribute('lng', (float)$map['longitude']);
                    $newnode->addAttribute('link_shop', $module->getShopLink(array('id_seller' => $map['id_seller'])));
                    if (isset($map['distance'])) {
                        $newnode->addAttribute('distance', (int)$map['distance']);
                    }
                }
            }
            header('Content-type: text/xml');
            die($parnode->asXML());
        }
    }
    public function updateCommission($rate_categories)
    {
        if ($this->enable_commission_by_category && $rate_categories) {
            if ($rate_categories) {
                foreach ($rate_categories as $id_category => $rate) {
                    if ($rate && Validate::isUnsignedFloat($rate)) {
                        if (Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_mp_category_commission_seller` WHERE id_category=' . (int)$id_category . ' AND id_seller=' . (int)$this->id))
                            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_mp_category_commission_seller` SET commission_rate="' . (float)$rate . '" WHERE id_category=' . (int)$id_category . ' AND id_seller=' . (int)$this->id);
                        else
                            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_mp_category_commission_seller`(id_category,id_seller,commission_rate) VALUES(' . (int)$id_category . ',' . (int)$this->id . ',' . (float)$rate . ')');
                    } else
                        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_category_commission_seller` WHERE id_category=' . (int)$id_category . ' AND id_seller=' . (int)$this->id);

                }
            }
        }
    }
    public function checkVacation()
    {
        if ($this->vacation_mode) {
            if ($this->date_vacation_start && strtotime($this->date_vacation_start) > strtotime(date('Y-m-d H:i:s')))
                return false;
            if ($this->date_vacation_end && strtotime($this->date_vacation_end) < strtotime(date('Y-m-d H:i:s')))
                return false;
            return true;
        }
        return false;
    }

    public static function getIDCustomerSellerByIDOrder($id_order)
    {
        return (int)Db::getInstance()->getValue('SELECT id_customer FROM `' . _DB_PREFIX_ . 'ets_mp_seller_order` WHERE id_order=' . (int)$id_order);
    }

    public static function getIDByIDOrder($id_order)
    {
        if (($id_customer = (int)Self::getIDCustomerSellerByIDOrder($id_order)) && ($seller = self::_getSellerByIdCustomer($id_customer))) {
            return $seller->id;
        }
        return false;
    }

    public static function getIDCustomerSellerByIdProduct($id_product)
    {
        return (int)Db::getInstance()->getValue('SELECT id_customer FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product` WHERE id_product=' . (int)$id_product);
    }
    public static function getProductSellerByIdProduct($id_product)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product` WHERE id_product=' . (int)$id_product);
    }
    public static function checkSellerByIDContact($id_contact,$id_seller)
    {
        return Db::getInstance()->getValue('SELECT id_seller FROM `'._DB_PREFIX_.'ets_mp_seller_contact` WHERE id_contact="'.(int)$id_contact.'" AND id_seller='.(int)$id_seller);
    }
    public static function getTotalSellerWaitApprove()
    {
        return Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_mp_seller` WHERE payment_verify!=0 AND active!=1');
    }

    public static function getPermistionSellerManager()
    {
        return Db::getInstance()->getValue('SELECT permission FROM `' . _DB_PREFIX_ . 'ets_mp_seller_manager` WHERE email ="' . pSQL(Context::getContext()->customer->email) . '" AND active=1');
    }

    public static function getCurrentSeller($active)
    {
        if(Context::getContext()->customer->isLogged())
        {
            if ($id_seller = Db::getInstance()->getValue('SELECT id_seller FROM `' . _DB_PREFIX_ . 'ets_mp_seller` WHERE id_customer="' . (int)Context::getContext()->customer->id . '" AND id_shop="' . (int)Context::getContext()->shop->id . '"' . ($active ? ' AND active=1' : ''))) {
                return new Ets_mp_seller($id_seller);
            } elseif ($id_customer = Db::getInstance()->getValue('SELECT id_customer FROM `' . _DB_PREFIX_ . 'ets_mp_seller_manager` WHERE email ="' . pSQL(Context::getContext()->customer->email) . '" AND active=1')) {
                return Ets_mp_seller::_getSellerByIdCustomer($id_customer, null, $active);
            }
        }
        return false;
    }

    public static function addOrderToSeller($id_customer, $id_order)
    {
        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_mp_seller_order`(id_order,id_customer) VALUES("' . (int)$id_order . '","' . (int)$id_customer . '")');
    }

    public static function getOrderCartRule($id_customer, $id_order, $id_product)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'order_cart_rule` ocr
                        INNER JOIN `' . _DB_PREFIX_ . 'cart_rule` cr ON (cr.id_cart_rule = ocr.id_cart_rule)
                        INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_cart_rule_seller` crs ON (crs.id_cart_rule = cr.id_cart_rule AND crs.id_customer="' . (int)$id_customer . '")
                        WHERE ocr.id_order=' . (int)$id_order . ' AND cr.reduction_product="' . (int)$id_product . '"';
        return Db::getInstance()->getRow($sql);
    }

    public static function getSellersfollow()
    {
        return Db::getInstance()->executeS('SELECT seller.id_customer FROM 
            `' . _DB_PREFIX_ . 'ets_mp_seller_customer_follow` scl
            INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller` seller ON (seller.id_seller=scl.id_seller AND seller.active=1)
            WHERE scl.id_customer="' . (int)Context::getContext()->customer->id . '"');
    }

    public static function getProductsByIdSellers($id_sellers, $number_product)
    {
        $front = true;
        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        $id_lang = (int)Context::getContext()->language->id;
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        $prev_version = version_compare(_PS_VERSION_, '1.6.1.0', '<');
        $sql = 'SELECT DISTINCT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS stock_quantity' . ($prev_version ? ' ,IFNULL(product_attribute_shop.id_product_attribute, 0)' : ' ,MAX(product_attribute_shop.id_product_attribute)') . ' id_product_attribute, pl.`description`, pl.`description_short`, pl.`available_now`,
        					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`,
        					il.`legend` as legend, m.`name` AS manufacturer_name,cl.name as default_category,
        					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
        					INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice,sp.approved';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'product` p
                            ' . Shop::addSqlAssociation('product', 'p') .
            (!$prev_version ?
                'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (pa.id_product = p.id_product)' . Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on=1') . '' :
                'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int)Context::getContext()->shop->id . ')'
            )
            . Product::sqlStock('p', 0, false, Context::getContext()->shop) . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_product` sp ON (sp.id_product=p.id_product)
                            LEFT JOIN `' . _DB_PREFIX_ . 'category` c ON (c.id_category=p.id_category_default)
                            LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.id_product=p.id_product)
                            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.id_category = cl.id_category AND cl.id_lang="' . (int)$id_lang . '")
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . ')' . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product` AND i.cover=1)
                            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$id_lang . ')	
                            LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                            WHERE product_shop.active=1 AND sp.id_customer IN (' . implode(',', array_map('intval', $id_sellers)) . ') AND product_shop.`id_shop` = ' . (int)Context::getContext()->shop->id . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '');
        $sql .= ' GROUP BY p.id_product ORDER BY RAND() LIMIT 0,' . (int)$number_product;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, true);
    }

    public static function getShopsByIDs($ids, $number_shop)
    {
        $sql = 'SELECT s.*,CONCAT(c.firstname," ", c.lastname) as customer_name,sl.shop_name,sl.shop_address,sl.shop_description,top_order_seller.total_order,seller_product.total_product 
                    FROM `' . _DB_PREFIX_ . 'ets_mp_seller` s
                        LEFT JOIN (
                            SELECT seller.id_seller as id_seller,COUNT(DISTINCT seller_order.id_order) as total_order
                            FROM `' . _DB_PREFIX_ . 'ets_mp_seller_order` seller_order
                            INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON(o.id_order=seller_order.id_order)
                            INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller` seller ON (seller_order.id_customer=seller.id_customer)
                            WHERE o.id_shop="' . (int)Context::getContext()->shop->id . '" GROUP BY seller.id_seller
                        ) as top_order_seller ON (top_order_seller.id_seller=s.id_seller)
                        LEFT JOIN (
                            SELECT seller.id_seller,count(sp.id_product) as total_product FROM `' . _DB_PREFIX_ . 'ets_mp_seller_product` sp
                            INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (sp.id_product= p.id_product AND p.active=1)
                            INNER JOIN `' . _DB_PREFIX_ . 'ets_mp_seller` seller ON(seller.id_customer=sp.id_customer)
                            GROUP BY seller.id_seller
                        ) as seller_product ON (seller_product.id_seller=s.id_seller)
                        LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_lang` sl ON (s.id_seller = sl.id_seller AND sl.id_lang ="' . (int)Context::getContext()->language->id . '")
                        LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (s.id_customer=c.id_customer)
                        WHERE s.id_shop="' . (int)Context::getContext()->shop->id . '" AND s.active=1 AND s.id_customer IN (' . implode(',', array_map('intval', $ids)) . ') ORDER BY top_order_seller.total_order DESC LIMIT 0,' . (int)$number_shop;
        return Db::getInstance()->executeS($sql);
    }

    public static function checkGroupCustomer()
    {
        if (Context::getContext()->customer->isLogged()) {
            if (Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_mp_seller_manager` WHERE email="' . pSQL(Context::getContext()->customer->email) . '" AND active!=0'))
                return true;
            $results = Db::getInstance()->executeS('
            SELECT cg.`id_group`
            FROM `' . _DB_PREFIX_ . 'customer_group` cg
            INNER JOIN `' . _DB_PREFIX_ . 'group` g ON (g.id_group=cg.id_group)
            INNER JOIN `' . _DB_PREFIX_ . 'group_shop` gs ON (g.id_group=gs.id_group AND gs.id_shop="' . (int)Context::getContext()->shop->id . '")
            WHERE cg.`id_customer` = ' . (int)Context::getContext()->customer->id);
            $group_seller = explode(',', Configuration::get('ETS_MP_SELLER_GROUPS'));
            if ($results) {
                foreach ($results as $result) {
                    if (in_array($result['id_group'], $group_seller))
                        return true;
                }
            }
        }
        return false;
    }

    public static function getSellersGoingToBeExpired($checkMail=true,$active=false)
    {
        $day_before_expired = (int)Configuration::get('ETS_MP_MESSAGE_EXPIRE_BEFORE_DAY');
        return Db::getInstance()->executeS('SELECT seller.*,CONCAT(customer.firstname," ",customer.lastname) as seller_name,customer.email as seller_email, seller_lang.shop_name 
            FROM `'._DB_PREFIX_.'ets_mp_seller` seller
            LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_lang` seller_lang ON(seller.id_seller= seller_lang.id_seller AND seller_lang.id_lang="'.(int)Context::getContext()->language->id.'")
            LEFT JOIN `'._DB_PREFIX_.'customer` customer ON (customer.id_customer=seller.id_customer)
            WHERE 1 '.($active ? ' AND seller.active=1': '').($checkMail ? ' AND seller.mail_going_to_be_expired=0 ':'').' AND seller.date_to is not NULL AND seller.date_to <="'.($day_before_expired ? pSQL(date('Y-m-d H:i:s',strtotime("+ $day_before_expired days"))): pSQL(date('Y-m-d H:i:s'))).'"');
    }

    public static function getSellersExpired()
    {
        return Db::getInstance()->executeS('SELECT id_seller FROM `'._DB_PREFIX_.'ets_mp_seller` WHERE date_to!="" AND mail_expired=0 AND date_to < "'.pSQL(date('Y-m-d H:i:s')).'"');
    }
    public static function getSellersWaitApprove()
    {
        return Db::getInstance()->executeS('SELECT id_seller FROM `'._DB_PREFIX_.'ets_mp_seller` WHERE active=-2 AND (date_to ="" OR date_to >= "'.pSQL(date('Y-m-d')).'") AND (date_from ="" OR date_from <= "'.pSQL(date('Y-m-d')).'")');
    }
    public static function getSellersWaitPay()
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ets_mp_seller` s
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_lang` sl ON (s.id_seller=sl.id_seller AND sl.id_lang="'.(int)Configuration::get('PS_LANG_DEFAULT').'")
        INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_billing` sb ON (s.id_customer= sb.id_customer);
        WHERE s.mail_wait_pay!=1 AND (s.active=-1 OR s.active=-2) AND sb.active=0 AND (sb.date_to ="" OR sb.date_to >= "'.pSQL(date('Y-m-d')).'") AND (sb.date_from ="" OR sb.date_from <= "'.pSQL(date('Y-m-d')).'")');
    }
    public static function autoUpdateGroup()
    {
        if(($order_status = Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN')))
        {
            $sql = 'SELECT so.id_customer,SUM(o.total_paid_tax_incl*c.conversion_rate) as total_order FROM `'._DB_PREFIX_.'orders` o
            INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_order` so ON (o.id_order = so.id_order)
            INNER JOIN `'._DB_PREFIX_.'currency` c ON(o.id_currency = c.id_currency)
            WHERE o.current_state IN ('.implode(',',array_map('intval',explode(',',$order_status))).')
            GROUP BY so.id_customer';
            $order_sellers = Db::getInstance()->executeS($sql);
            if($order_sellers)
            {
                $total = 0;
                foreach($order_sellers as $order_seller)
                {
                    $groups = Db::getInstance()->executeS('SELECT id_ets_mp_seller_group FROM `'._DB_PREFIX_.'ets_mp_seller_group` WHERE auto_upgrade <='.(float)$order_seller['total_order'].' ORDER BY auto_upgrade DESC');
                    if($groups && isset($groups[0]) && ($id_group = $groups[0]['id_ets_mp_seller_group']))
                    {
                        $seller = Ets_mp_seller::_getSellerByIdCustomer($order_seller['id_customer']);
                        if($seller->id_group!= $id_group)
                        {
                            $seller->id_group = $id_group;
                            if($seller->update() && Configuration::getGlobalValue('ETS_MP_SAVE_CRONJOB_LOG'))
                            {
                                $total++;
                            }
                        }
                    }
                }
                return $total;
            }
        }
        return false;
    }
    public function getTotalMessagesReply()
    {
        $sql1= 'SELECT COUNT(DISTINCT cm.id_customer_thread) FROM `'._DB_PREFIX_.'customer_message` cm 
        INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_customer_message` scm ON (scm.id_customer_message=cm.id_customer_message AND scm.id_customer="'.(int)$this->id_customer.'")';
        $sql2 = 'SELECT COUNT(DISTINCT id_contact) FROM `'._DB_PREFIX_.'ets_mp_seller_contact_message` WHERE id_seller='.(int)$this->id;
        return Db::getInstance()->getValue($sql1) + Db::getInstance()->getValue($sql2);
    }
    public function getTotalFollow()
    {
        return Db::getInstance()->getValue('SELECT COUNT(id_customer) FROM `'._DB_PREFIX_.'ets_mp_seller_customer_follow` WHERE id_seller='.(int)$this->id);
    }
    public static function customerUnFollowSeller($id_customer,$id_seller)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_mp_seller_customer_follow` WHERE id_seller="'.(int)$id_seller.'" AND id_customer="'.(int)$id_customer.'"');
    }
    public static function customerFollowSeller($id_customer,$id_seller)
    {
        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_seller_customer_follow` WHERE id_seller="'.(int)$id_seller.'" AND id_customer="'.(int)$id_customer.'"'))
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_seller_customer_follow`(id_seller,id_customer) VALUES("'.(int)$id_seller.'","'.(int)$id_customer.'")');
        return true;
    }
    public function checkIsFollow()
    {
        if(Context::getContext()->customer->isLogged())
        {
            if(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_seller_customer_follow` WHERE id_seller="'.(int)$this->id.'" AND id_customer="'.(int)Context::getContext()->customer->id.'"'))
                return 1;
            else
                return 0;
        }
        return -1;
    }
    public function CheckReported($id_customer,$id_product)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_seller_report` WHERE id_customer="'.(int)$id_customer.'" AND id_seller="'.(int)$this->id.'" AND id_product="'.(int)$id_product.'"');
    }
    public function getOrderMessages($filter='',$start=0,$limit=12,$order_by='',$total=false)
    {
        $sql1 ='SELECT o.id_order,"" as id_contact,"" as title_contact,o.reference,cm.read,cm.message,cm.id_employee,"0" as id_seller,cm.date_add,CONCAT(manager.firstname," ",manager.lastname) as manager_name,CONCAT(customer.firstname," ",customer.lastname) as seller_name,CONCAT(c.firstname," ",c.lastname) as customer_name,CONCAT(e.firstname," ",e.lastname) as employee_name,
            cm_min.id_employee as id_employee_min,CONCAT(manager_min.firstname," ",manager_min.lastname) as manager_min_name,CONCAT(customer_min.firstname," ",customer_min.lastname) as seller_min_name,CONCAT(e_min.firstname," ",e_min.lastname) as employee_min_name
            FROM `'._DB_PREFIX_.'customer_thread` ct
            INNER JOIN `'._DB_PREFIX_.'customer_message` cm ON (cm.id_customer_thread=ct.id_customer_thread)
            INNER JOIN (SELECT id_customer_thread,max(id_customer_message) as id_customer_message_max FROM `'._DB_PREFIX_.'customer_message` group by id_customer_thread) last_message ON (last_message.id_customer_message_max=cm.id_customer_message AND last_message.id_customer_thread=ct.id_customer_thread)
            INNER JOIN `'._DB_PREFIX_.'orders` o ON (ct.id_order=o.id_order)
            INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_order` so ON (o.id_order=so.id_order)  
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer=ct.id_customer)
            LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee=cm.id_employee)
            LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_customer_message` scm ON (scm.id_customer_message=cm.id_customer_message)
            LEFT JOIN `'._DB_PREFIX_.'customer` manager ON (manager.id_customer=scm.id_manager)
            LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller` seller ON (seller.id_customer=scm.id_customer)
            LEFT JOIN `'._DB_PREFIX_.'customer` customer ON (customer.id_customer=seller.id_customer)
            LEFT JOIN (
                SELECT id_customer_thread,MIN(id_customer_message) as id_customer_message_min FROM `'._DB_PREFIX_.'customer_message` group by id_customer_thread
            ) first_message ON (first_message.id_customer_thread=ct.id_customer_thread)
            LEFT JOIN `'._DB_PREFIX_.'customer_message` cm_min ON (first_message.id_customer_message_min=cm_min.id_customer_message)
            LEFT JOIN `'._DB_PREFIX_.'employee` e_min ON (e_min.id_employee=cm_min.id_employee)
            LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_customer_message` scm_min ON (scm_min.id_customer_message=cm_min.id_customer_message)
            LEFT JOIN `'._DB_PREFIX_.'customer` manager_min ON (manager_min.id_customer=scm_min.id_manager)
            LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller` seller_min ON (seller_min.id_customer=scm_min.id_customer)
            LEFT JOIN `'._DB_PREFIX_.'customer` customer_min ON (customer_min.id_customer=seller_min.id_customer)   
            WHERE so.id_customer="'.(int)$this->id_customer.'" GROUP BY o.id_order';
        $sql2 = 'SELECT contact.id_order,contact.id_contact,cm.title as title_contact,o.reference,cm.read,cm.message,cm.id_employee,cm.id_seller,cm.date_add,CONCAT(manager.firstname," ",manager.lastname) as manager_name,CONCAT(customer.firstname," ",customer.lastname) as seller_name,if(contact.id_customer!=0,CONCAT(c.firstname," ",c.lastname),contact.name) as customer_name, CONCAT(e.firstname," ",e.lastname) as employee_name,
            "" as id_employee_min,"" as manager_min_name,"" as seller_min_name,"" as employee_min_name
            FROM `'._DB_PREFIX_.'ets_mp_seller_contact` contact
            INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_contact_message` cm ON (contact.id_contact=cm.id_contact)
            INNER JOIN (SELECT id_contact,max(id_message) as id_message_max FROM `'._DB_PREFIX_.'ets_mp_seller_contact_message` GROUP BY id_contact) cmmax ON (cmmax.id_message_max = cm.id_message AND cmmax.id_contact= contact.id_contact)
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer= contact.id_customer)
            LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller` seller ON (seller.id_seller=cm.id_seller)
            LEFT JOIN `'._DB_PREFIX_.'customer` customer ON (customer.id_customer=seller.id_customer)
            LEFT JOIN `'._DB_PREFIX_.'customer` manager ON (manager.id_customer=cm.id_manager)
            LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee=cm.id_employee)
            LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.id_order=contact.id_order)
            WHERE contact.id_seller="'.(int)$this->id.'"';
        $sql = "SELECT * FROM (($sql1) UNION ALL ($sql2)) as tb WHERE 1".($filter ? (string)$filter :'');
        if($total)
            return count(Db::getInstance()->executeS($sql));
        else
        {
            $sql .= ($order_by ? ' ORDER By '.pSQL($order_by):'');
            $sql .= ' LIMIT '.(int)$start.','.(int)$limit;
            return Db::getInstance()->executeS($sql);
        }
    }
    public static function getSellerByQuery($query,$active= true)
    {
        $sql  = 'SELECT *,CONCAT(c.firstname," ",c.lastname) as seller_name FROM `'._DB_PREFIX_.'ets_mp_seller` s
            INNER JOIN `'._DB_PREFIX_.'customer` c ON (s.id_customer=c.id_customer)
            LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_lang` sl ON (s.id_seller = sl.id_seller AND sl.id_lang="'.(int)Context::getContext()->language->id.'")
            WHERE s.id_shop="'.(int)Context::getContext()->shop->id.'" AND s.id_customer = "'.(int)$query.'" OR s.id_seller ="'.(int)$query.'" OR sl.shop_name LIKE "%'.pSQL($query).'%" OR c.email LIKE "%'.pSQL($query).'%" OR CONCAT(c.firstname," ",c.lastname) LIKE "%'.pSQL($query).'%"'.($active ? ' AND s.active=1':'');
        return Db::getInstance()->executeS($sql);
    }
    public static function getTopSellers()
    {
        if(Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'))
            $status = explode(',',Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'));
        else
            $status = array();
        if($status)
        {
            $sql = 'SELECT s.*,CONCAT(customer.firstname," ", customer.lastname) as seller_name,customer.email as seller_email,sl.shop_name,sl.shop_address,sl.shop_description,top_order_seller.total_order FROM `'._DB_PREFIX_.'ets_mp_seller` s
                INNER JOIN (
                    SELECT seller_order.id_customer as id_customer,SUM(od.product_quantity) as total_order
                    FROM `'._DB_PREFIX_.'ets_mp_seller_order` seller_order
                    INNER JOIN `'._DB_PREFIX_.'orders` o ON(o.id_order=seller_order.id_order)
                    INNER JOIN `'._DB_PREFIX_.'order_detail` od ON (o.id_order=od.id_order)
                    WHERE o.id_shop="'.(int)Context::getContext()->shop->id.'" AND o.current_state IN ('.implode(',',array_map('intval',$status)).') GROUP BY seller_order.id_customer
                ) as top_order_seller ON (top_order_seller.id_customer=s.id_customer)
                LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_lang` sl ON (s.id_seller = sl.id_seller AND sl.id_lang ="'.(int)Context::getContext()->language->id.'")
                LEFT JOIN `'._DB_PREFIX_.'customer` customer ON (s.id_customer=customer.id_customer)
                WHERE s.id_shop="'.(int)Context::getContext()->shop->id.'" ORDER BY top_order_seller.total_order DESC LIMIT 0,10';
            return Db::getInstance()->executeS($sql);
        }
        return array();
    }
    public static function getCategories($category,$productIds)
    {
        if(!$productIds)
            return false;
        $range = '';
        $maxdepth = Configuration::get('BLOCK_CATEG_MAX_DEPTH');
        if (Validate::isLoadedObject($category)) {
            if ($maxdepth > 0) {
                $maxdepth += $category->level_depth;
            }
            $range = 'AND nleft >= '.(int)$category->nleft.' AND nright <= '.(int)$category->nright;
        }
        $resultIds = array();
        $resultParents = array();
        $sql ='
			SELECT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
			FROM `'._DB_PREFIX_.'category` c
			INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('cl').')
			INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = '.(int)Context::getContext()->shop->id.')
			WHERE (c.`active` = 1 OR c.`id_category` = '.(int)Configuration::get('PS_HOME_CATEGORY').')
			AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').' AND c.id_category!= "'.(int)$category->id.'"
			'.((int)$maxdepth != 0 ? ' AND `level_depth` <= '.(int)$maxdepth : '').'
			'.$range.'
			AND c.id_category IN (
				SELECT id_category
				FROM `'._DB_PREFIX_.'category_group`
				WHERE `id_group` IN ('.implode(', ', array_map('intval',Customer::getGroupsStatic((int)Context::getContext()->customer->id))).')
			)
            AND c.id_category IN (
                SELECT parent.id_category
                FROM `'._DB_PREFIX_.'category` AS node, `'._DB_PREFIX_.'category` AS parent
                WHERE
                	node.nleft BETWEEN parent.nleft AND parent.nright
                	AND node.id_category IN (SELECT id_category
                FROM `'._DB_PREFIX_.'category_product` cp WHERE id_product IN ('.implode(',',array_map('intval',$productIds)).'))
                ORDER BY parent.nleft
            )
			ORDER BY `level_depth` ASC, '.(Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'cs.`position`').' '.(Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if($result)
        {
            foreach ($result as &$row) {
                $resultParents[$row['id_parent']][] = &$row;
                $resultIds[$row['id_category']] = &$row;
            }

            return self::getTree($resultParents, $resultIds, $maxdepth, ($category ? $category->id : null),$productIds);
        }
        return false;
    }
    public static function getTree($resultParents, $resultIds, $maxDepth,$id_category,$productIds, $currentDepth = 0)
    {
        if (is_null($id_category)) {
            $id_category = Context::getContext()->shop->getCategory();
        }

        $children = array();

        if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth)) {
            foreach ($resultParents[$id_category] as $subcat) {
                $children[] = self::getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'],$productIds, $currentDepth + 1);
            }
        }

        if (isset($resultIds[$id_category])) {
            $link = Context::getContext()->link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']);
            $name = $resultIds[$id_category]['name'];
            $desc = $resultIds[$id_category]['description'];
        } else {
            $link = $name = $desc = '';
        }
        $total_product = (int)Db::getInstance()->getValue('SELECT COUNT(id_product) FROM `'._DB_PREFIX_.'category_product` WHERE id_category="'.(int)$id_category.'" AND id_product in ('.implode(',',array_map('intval',$productIds)).')');
        return array(
            'id' => $id_category,
            'link' => $link,
            'name' => $name,
            'desc'=> $desc,
            'total_product' => $total_product,
            'children' => $children
        );
    }
    public function downloadFileContact($id_contact)
    {
        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_seller_contact` WHERE id_seller="'.(int)$this->id.'" AND id_contact="'.(int)$id_contact.'"'))
            die($this->l('You do not have permission to download this attachment'));
        else
        {
            $attachment = Db::getInstance()->getRow('SELECT attachment,attachment_name FROM `'._DB_PREFIX_.'ets_mp_seller_contact_message` WHERE id_contact="'.(int)$id_contact.'" AND attachment!=""');
            if($attachment)
            {
                $filepath =_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_attachment/'.$attachment['attachment'];
                if(file_exists($filepath)){
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'. ($attachment['attachment_name'] ? : $attachment['attachment']).'"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($filepath));
                    flush(); // Flush system output buffer
                    readfile($filepath);
                    exit;
                }
                else
                    die($this->l('File attachment is null').$filepath);
            }
            else
                die($this->l('File attachment is null'));
        }
    }
    public static function checkSellerByEmail($email)
    {
        $sql1 = 'SELECT r.id_customer FROM `'._DB_PREFIX_.'ets_mp_registration` r
        INNER JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer=r.id_customer)
        WHERE c.email="'.pSQL($email).'"';
        $sql2 = 'SELECT s.id_customer FROM `'._DB_PREFIX_.'ets_mp_seller` s
        INNER JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer=s.id_customer)
        WHERE c.email="'.pSQL($email).'"';
        return Db::getInstance()->getValue($sql1) || Db::getInstance()->getValue($sql2);
    }
    public function getAttribute($name,$color,$name_group)
    {
        $sql = 'SELECT a.id_attribute FROM `'._DB_PREFIX_.'attribute` a
        INNER JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.id_attribute_group=a.id_attribute_group)
        INNER JOIN `'._DB_PREFIX_.'attribute_group_shop` ags ON(ag.id_attribute_group=ags.id_attribute_group AND ags.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_attribute_group_seller` attribute_group_seller ON (ag.id_attribute_group=attribute_group_seller.id_attribute_group)
        LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.id_attribute= al.id_attribute ANd al.id_lang="'.(int)Context::getContext()->language->id.'")
        LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (agl.id_attribute_group=ag.id_attribute_group AND agl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE attribute_group_seller.id_customer="'.(int)$this->id_customer.'" AND al.name="'.pSQL($name).'" AND a.color="'.pSQL($color).'" AND agl.name="'.pSQL($name_group).'"';
        return Db::getInstance()->getValue($sql);
    }
    public function getAttributeGroupByName($name)
    {
        $sql = 'SELECT ag.id_attribute_group FROM `'._DB_PREFIX_.'attribute_group` ag
        INNER JOIN `'._DB_PREFIX_.'attribute_group_shop` ags ON(ag.id_attribute_group=ags.id_attribute_group AND ags.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_attribute_group_seller` attribute_group_seller ON (ag.id_attribute_group=attribute_group_seller.id_attribute_group)
        LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (agl.id_attribute_group=ag.id_attribute_group AND agl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE attribute_group_seller.id_customer="'.(int)$this->id_customer.'" AND agl.name="'.pSQL($name).'"';
        return Db::getInstance()->getValue($sql);
    }
    public static function addAttributeGroupToSeller($id_attribute_group,$id_customer_seller)
    {
        return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_attribute_group_seller`(id_attribute_group,id_customer) VALUES("'.(int)$id_attribute_group.'","'.(int)$id_customer_seller.'")');
    }
    public static function addAttributeToCombination($id_attribute,$id_product_attribute)
    {
        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'product_attribute_combination` WHERE id_attribute="'.(int)$id_attribute.'" AND id_product_attribute="'.(int)$id_product_attribute.'"'))
        {
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_attribute_combination`(id_product_attribute,id_attribute) VALUES("'.(int)$id_product_attribute.'","'.(int)$id_attribute.'")');
        }
    }
    public static function addCarrierToSeller($id_customer_seller,$id_reference)
    {
        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_carrier_seller` WHERE id_customer="'.(int)$id_customer_seller.'" AND id_carrier_reference="'.(int)$id_reference.'"'))
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_carrier_seller`(id_customer,id_carrier_reference) VALUES("'.(int)$id_customer_seller.'","'.(int)$id_reference.'")');
        if(version_compare(_PS_VERSION_, '1.7', '>='))
        {
            $hook_payment = 'Payment';
            if (Db::getInstance()->getValue('SELECT `id_hook` FROM `' . _DB_PREFIX_ . 'hook` WHERE `name` = \'paymentOptions\'')) {
                $hook_payment = 'paymentOptions';
            }
            $list = Shop::getContextListShopID();
            $paymentModules = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT DISTINCT m.`id_module`, h.`id_hook`, m.`name`, hm.`position`
                FROM `' . _DB_PREFIX_ . 'module` m
                LEFT JOIN `' . _DB_PREFIX_ . 'hook_module` hm ON hm.`id_module` = m.`id_module`
                LEFT JOIN `' . _DB_PREFIX_ . 'hook` h ON hm.`id_hook` = h.`id_hook`
                WHERE h.`name` = \'' . pSQL($hook_payment) . '\'
                AND (SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'module_shop` ms WHERE ms.id_module = m.id_module AND ms.id_shop IN(' . implode(', ', array_map('intval',$list)) . ')) = ' . count($list) . '
                AND hm.id_shop IN(' . implode(', ', array_map('intval',$list)) . ')
                GROUP BY hm.id_hook, hm.id_module
                ORDER BY hm.`position`, m.`name` DESC');
            if($paymentModules)
            {
                foreach (Shop::getContextListShopID() as $shopId) {
                    foreach ($paymentModules as $module) {
                        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'module_carrier` WHERE id_module="'.(int)$module['id_module'].'" AND id_reference="'.(int)$id_reference.'" AND id_shop="'.(int)$shopId.'"'))
                        {
                            Db::getInstance()->execute(
                                '
                                    INSERT INTO `' . _DB_PREFIX_ . 'module_' . bqSQL('carrier') . '`
                                    (`id_module`, `id_shop`, `id_' . bqSQL('reference') . '`)
                                    VALUES (' . (int) $module['id_module'] . ',' . (int) $shopId . ',' . (int) $id_reference . ')'
                            );
                        }

                    }
                }
            }
        }
        return true;
    }
    public function checkUserCarrier($id_carrier)
    {
        $carrier = new Carrier($id_carrier);
        return !$carrier->deleted && Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_carrier_seller` WHERE id_customer="'.(int)$this->id_customer.'" AND id_carrier_reference="'.(int)$carrier->id_reference.'"');
    }
    public static function addSupplierToSeller($id_supplier,$id_customer_seller)
    {
        return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_supplier_seller`(id_customer,id_supplier) VALUES("'.(int)$id_customer_seller.'","'.(int)$id_supplier.'")');
    }
    public static function deleteSupplier($id_supplier)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_mp_supplier_seller` WHERE id_supplier='.(int)$id_supplier);
    }
    public static function deleteManufacture($id_manufacturer)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_mp_manufacturer_seller` WHERE id_manufacturer='.(int)$id_manufacturer);
    }
    public function addManufacturer($id_manufacturer)
    {
        return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_manufacturer_seller`(id_customer,id_manufacturer) VALUES("'.(int)$this->id_customer.'","'.(int)$id_manufacturer.'")');
    }
    public function addProduct($id_product,$approved,$active)
    {
        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_seller_product`(id_customer,id_product,approved,active) VALUES("'.(int)$this->id_customer.'","'.(int)$id_product.'","'.(int)$approved.'","'.(int)$active.'")');
    }
    public static function getAddressManufacturer($id_manufacturer,$start,$limit,$total)
    {
        if($total)
            $sql ='SELECT COUNT(DISTINCT a.id_address)';
        else
            $sql ='SELECT a.*,CONCAT(a.firstname," ",a.lastname) as name,s.name as state_name';
        $sql .=' FROM `'._DB_PREFIX_.'address` a
        LEFT JOIN `'._DB_PREFIX_.'state` s ON (a.id_state=s.id_state AND a.id_country=s.id_country)
        WHERE a.id_manufacturer="'.(int)$id_manufacturer.'"
        ';
        if($total)
            return Db::getInstance()->getValue($sql);
        {
            $sql .=' LIMIT '.(int)$start.','.(int)$limit;
            return Db::getInstance()->executeS($sql);
        }
    }
    public function checkValidateCategory($id_category)
    {
        $seller_categories = $this->getApplicableProductCategories();
        $sql = 'SELECT c.id_category FROM `'._DB_PREFIX_.'category` c
        INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category=cs.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
        WHERE c.id_parent!=0 AND c.id_category = "'.(int)$id_category.'" AND c.is_root_category!=1 '.(isset($seller_categories) && $seller_categories ? ' AND c.id_category IN ('.explode(',',array_map('intval',$seller_categories)).')':'');
        return (int)Db::getInstance()->getValue($sql);
    }
    public function getValidateCategories()
    {
        $seller_categories = $this->getApplicableProductCategories();
        $sql = 'SELECT c.id_category,cl.name FROM `'._DB_PREFIX_.'category` c
        INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category=cs.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.id_category=cl.id_category AND cl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE c.id_parent!=0 AND c.is_root_category!=1 '.(isset($seller_categories) && $seller_categories ? ' AND c.id_category IN ('.implode(',',array_map('intval',$seller_categories)).')':'').' ORDER BY cl.name ASC';
        return Db::getInstance()->executeS($sql);
    }
    public static function getSellerByEmail($email)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_seller` WHERE seller_email="'.pSQL($email).'"');
    }
    public function getExportProducts($sample=false)
    {
        $sql = 'SELECT p.id_product,stock.quantity,ps.price,ps.id_category_default,pl.name,pl.description,pl.description_short,pl.link_rewrite FROM `'._DB_PREFIX_.'product` p
        INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.id_product=ps.id_product)
        '.(!$sample ? 'INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_product` seller_product ON (seller_product.id_product=p.id_product)':'').'
        LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product=p.id_product AND pl.id_lang="'.(int)Context::getContext()->language->id.'")
        LEFT JOIN `'._DB_PREFIX_.'stock_available` stock ON (stock.id_product=p.id_product AND stock.id_product_attribute=0)
        WHERE ps.id_shop="'.(int)Context::getContext()->shop->id.'"'.(!$sample ? ' AND seller_product.id_customer="'.(int)$this->id_customer.'"':'');
        if($sample)
            $sql .=' ORDER BY p.id_product ASC LIMIT 0,10';
        $products = Db::getInstance()->executeS($sql);
        if($products)
        {
            foreach($products as &$product)
            {
                $sql ='SELECT pas.price,pas.default_on,stock.quantity,pas.id_product_attribute FROM `'._DB_PREFIX_.'product_attribute` pa
                INNER JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pa.id_product_attribute=pas.id_product_attribute)
                LEFT JOIN `'._DB_PREFIX_.'stock_available` stock ON (stock.id_product_attribute=pa.id_product_attribute)
                WHERE pas.id_shop="'.(int)Context::getContext()->shop->id.'" AND pa.id_product="'.(int)$product['id_product'].'"';
                $product_attributes = Db::getInstance()->executeS($sql);
                if($product_attributes)
                {
                    foreach($product_attributes as &$product_attribute)
                    {
                        $sql = 'SELECT agl.name as name_group, al.name,a.color FROM `'._DB_PREFIX_.'attribute` a
                        INNER JOIN `'._DB_PREFIX_.'attribute_group` ag ON (a.id_attribute_group=ag.id_attribute_group)
                        INNER JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.id_attribute=a.id_attribute)
                        LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.id_attribute=al.id_attribute AND al.id_lang="'.(int)Context::getContext()->language->id.'")
                        LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.id_attribute_group = agl.id_attribute_group AND agl.id_lang="'.(int)Context::getContext()->language->id.'")
                        WHERE pac.id_product_attribute='.(int)$product_attribute['id_product_attribute'];
                        $attributes = Db::getInstance()->executeS($sql);
                        $product_attribute['attributes'] = $attributes;
                        $attribute_specific_price = Db::getInstance()->executeS('SELECT id_currency,id_group,id_customer,id_country, price,from_quantity,reduction,reduction_tax,reduction_type,`from`,`to` FROM `'._DB_PREFIX_.'specific_price` WHERE id_product='.(int)$product['id_product'].' AND id_product_attribute='.(int)$product_attribute['id_product_attribute']);
                        if($attribute_specific_price)
                            $product_attribute['specific_prices'] = $attribute_specific_price;
                    }
                    unset($product_attribute);
                    $product['product_attributes'] = json_encode($product_attributes);
                }
                else
                    $product['product_attributes'] ='';
                $specific_prices = Db::getInstance()->executeS('SELECT id_currency,id_group,id_customer,id_country, price,from_quantity,reduction,reduction_tax,reduction_type,`from`,`to` FROM `'._DB_PREFIX_.'specific_price` WHERE id_product='.(int)$product['id_product'].' AND id_product_attribute=0');
                if($specific_prices)
                    $product['specific_prices'] = json_encode($specific_prices);
                else
                    $product['specific_prices'] = '';
                $sql = 'SELECT id_image FROM `'._DB_PREFIX_.'image` WHERE id_product='.(int)$product['id_product'];
                $images = Db::getInstance()->executeS($sql);
                $list_images=array();
                if($images)
                {
                    foreach($images as $image)
                    {
                        $folders = str_split((string)$image['id_image']);
                        $path = implode('/', $folders) . '/';
                        $url = Module::getInstanceByName('ets_marketplace')->getBaseLink() . '/img/p/' . $path . $image['id_image'] . '.jpg';
                        $list_images[]= $url;
                    }
                    $product['images'] = implode(',',$list_images);
                }
                else
                    $product['images']='';
                $sql = 'SELECT c.id_category FROM `'._DB_PREFIX_.'category` c
                INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category=cs.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
                INNER JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.id_category=c.id_category)
                WHERE cp.id_product="'.(int)$product['id_product'].'"';
                $categories= Db::getInstance()->executeS($sql);
                if($categories)
                {
                    $list_categories = array();
                    foreach($categories as $category)
                        $list_categories[] = $category['id_category'];
                    $product['categories'] = implode(',',$list_categories);
                }
                else
                    $product['categories'] ='';
            }
            unset($product);
        }
    }
    public function getProductAttachments($id_product)
    {
        $sql = 'SELECT a.*,al.name,al.description,pa.id_product FROM `'._DB_PREFIX_.'attachment` a
        LEFT JOIN `'._DB_PREFIX_.'product_attachment` pa ON (a.id_attachment = pa.id_attachment AND pa.id_product="'.(int)$id_product.'")
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_attachment_seller` a_seller ON (a.id_attachment = a_seller.id_attachment AND a_seller.id_customer="'.(int)$this->id_customer.'")
        LEFT JOIN `'._DB_PREFIX_.'attachment_lang` al ON (a.id_attachment = al.id_attachment AND id_lang = "'.(int)Context::getContext()->language->id.'")
        WHERE pa.id_product is not null OR a_seller.id_customer is not null';
        return Db::getInstance()->executeS($sql);
    }
    public function addAttachment($id_attachment)
    {
        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_attachment_seller`  where id_attachment="'.(int)$id_attachment.'" AND id_customer="'.(int)$this->id_customer.'"'))
        {
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_attachment_seller`(id_attachment,id_customer) VALUES("'.(int)$id_attachment.'","'.(int)$this->id_customer.'")');
        }
        return true;
    }
    public function getIDOrderByReferenceIDCustomer($id_customer,$reference)
    {
        $sql = 'SELECT o.id_order FROM `'._DB_PREFIX_.'orders` o 
                    INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_order` seller_order ON (seller_order.id_order=o.id_order)
                    WHERE o.id_customer="'.(int)$id_customer.'" AND seller_order.id_customer="'.(int)$this->id_customer.'" AND o.reference = "'.pSQL($reference).'"
                ';
        return Db::getInstance()->getValue($sql);
    }
    public function getListOrderByIDCustomer($id_customer)
    {
        $sql = 'SELECT o.* FROM `'._DB_PREFIX_.'orders` o 
                        INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_order` seller_order ON (seller_order.id_order=o.id_order)
                        WHERE o.id_customer="'.(int)$id_customer.'" AND seller_order.id_customer="'.(int)$this->id_customer.'"';
        return Db::getInstance()->executeS($sql);
    }
    public function addCartRule($id_cart_rule)
    {
        if(Validate::isLoadedObject(new CartRule($id_cart_rule)))
            return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_cart_rule_seller`(id_cart_rule,id_customer) VALUES("'.(int)$id_cart_rule.'","'.(int)$this->id_customer.'") ');
        return false;
    }
    public static function getIDCustomerSellerByIDCartRule($id_cart_rule)
    {
        if(Validate::isLoadedObject(new CartRule($id_cart_rule)))
            return (int)Db::getInstance()->getValue('SELECT id_customer FROM `'._DB_PREFIX_.'ets_mp_cart_rule_seller` WHERE id_cart_rule="'.(int)$id_cart_rule.'"');
        return false;
    }
    public function addFeature($id_feature)
    {
        if(Validate::isLoadedObject(new Feature($id_feature)))
        {
            return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_feature_seller`(id_feature,id_customer) VALUES("'.(int)$id_feature.'","'.(int)$this->id_customer.'")');
        }
        return false;
    }
    public function addCustomerMessage($id_customer_message)
    {
        return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_seller_customer_message`(id_customer,id_customer_message) VALUES("'.(int)$this->id_customer.'","'.(int)$id_customer_message.'")');
    }
    public function getpreviousOrder($id_order)
    {
        return (int)Db::getInstance()->getValue('SELECT id_order FROM `'._DB_PREFIX_.'ets_mp_seller_order` WHERE id_customer="'.(int)$this->id_customer.'" AND id_order < "'.(int)$id_order.'" ORDER BY id_order DESC');
    }
    public function getNextOrder($id_order)
    {
        return (int)Db::getInstance()->getValue('SELECT id_order FROM `'._DB_PREFIX_.'ets_mp_seller_order` WHERE id_customer="'.(int)$this->id_customer.'" AND id_order > "'.(int)$id_order.'" ORDER BY id_order DESC');
    }
    public static function getOrderStates($id_lang,$currentState)
    {
        if(Configuration::get('ETS_MP_SELLER_ALLOWED_STATUSES'))
        {
            $status = explode(',',Configuration::get('ETS_MP_SELLER_ALLOWED_STATUSES'));
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'order_state` os
            LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) $id_lang . ')
            WHERE deleted = 0 AND (os.id_order_state = "'.(int)$currentState.'" || os.id_order_state IN ('.implode(',',array_map('intval',$status)).'))
            ORDER BY `name` ASC');
            return $result;
        }
        else
            return array();
    }
    public static function getOrderDetails($id_order)
    {
        $order = new Order($id_order);
        if(Validate::isLoadedObject($order))
        {
            $products = $order->getProductsDetail();
            if($products)
            {
                if(version_compare(_PS_VERSION_, '1.7', '>='))
                    $type_image= ImageType::getFormattedName('small');
                else
                    $type_image= ImageType::getFormatedName('small');
                foreach($products as &$product)
                {
                    $product_class = new Product($product['product_id'],false,Context::getContext()->language->id);
                    if(Validate::isLoadedObject($product_class) && $product_class->active==1)
                    {
                        $image=false;
                        if($product['product_attribute_id'])
                        {
                            $sql = 'SELECT * FROM `'._DB_PREFIX_.'product_attribute_image` pai
                        INNER JOIN `'._DB_PREFIX_.'image` i ON pai.id_image=i.id_image WHERE pai.id_product_attribute='.(int)$product['product_attribute_id'];
                            if(!$image = Db::getInstance()->getRow($sql.' AND i.cover=1'))
                                $image  = Db::getInstance()->getRow($sql);
                        }
                        if(!$image)
                        {
                            $sql = 'SELECT i.id_image FROM `'._DB_PREFIX_.'image` i';
                            if($product['product_attribute_id'])
                                $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON (i.id_image=pai.id_image AND pai.id_product_attribute="'.(int)$product['product_attribute_id'].'")';
                            $sql .= ' WHERE i.id_product="'.(int)$product['product_id'].'"';
                            if(!$image = Db::getInstance()->getRow($sql.' AND i.cover=1'))
                            {
                                $image = Db::getInstance()->getRow($sql);
                            }
                        }

                        if($image)
                        {
                            $product['image'] =  Context::getContext()->link->getImageLink($product_class->link_rewrite,$image['id_image'],$type_image);
                        }
                        else
                        {
                            $product['image'] = '';
                        }
                    }
                    else
                        $product['deleted'] = true;

                }
            }
        }
        return array();
    }
    public static function getFeeOrder($id_order)
    {
        if(Module::isInstalled('ets_payment_with_fee') && Module::isEnabled('ets_payment_with_fee'))
        {
            if($method_order = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_paymentmethod_order` WHERE id_order ='.(int)$id_order))
            {
                return isset($method_order['fee']) ? $method_order['fee']:false;

            }
        }
        return false;
    }
    public function getWithdraws()
    {
        $sql = 'SELECT w.*,u.amount,u.note,pml.title as method_name 
            FROM `'._DB_PREFIX_.'ets_mp_withdrawal` w
            INNER JOIN `'._DB_PREFIX_.'ets_mp_commission_usage` u ON (w.id_ets_mp_withdrawal = u.id_withdraw)
            LEFT JOIN `'._DB_PREFIX_.'ets_mp_payment_method_lang` pml ON (pml.id_ets_mp_payment_method=w.id_ets_mp_payment_method AND pml.id_lang="'.(int)Context::getContext()->language->id.'")
            WHERE u.id_customer="'.(int)$this->id_customer.'" ORDER BY w.id_ets_mp_withdrawal DESC';
        return Db::getInstance()->executeS($sql);
    }
    public function getPendingWithdrawal()
    {
        return Db::getInstance()->getValue('SELECT w.id_ets_mp_withdrawal FROM `'._DB_PREFIX_.'ets_mp_withdrawal` w 
                INNER JOIN `'._DB_PREFIX_.'ets_mp_commission_usage` cu ON (cu.id_withdraw=w.id_ets_mp_withdrawal) 
                WHERE cu.id_customer='.(int)$this->id_customer.' AND w.status=0');
    }
    public static function afterAddRule($cartRule)
    {
        foreach (['country', 'carrier', 'group', 'product_rule_group', 'shop'] as $type) {
            Db::getInstance()->delete('cart_rule_' . $type, '`id_cart_rule` = ' . (int) $cartRule->id);
        }

        Db::getInstance()->delete('cart_rule_product_rule', 'NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'cart_rule_product_rule_group`
			WHERE `' . _DB_PREFIX_ . 'cart_rule_product_rule`.`id_product_rule_group` = `' . _DB_PREFIX_ . 'cart_rule_product_rule_group`.`id_product_rule_group`)');
        Db::getInstance()->delete('cart_rule_product_rule_value', 'NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'cart_rule_product_rule`
			WHERE `' . _DB_PREFIX_ . 'cart_rule_product_rule_value`.`id_product_rule` = `' . _DB_PREFIX_ . 'cart_rule_product_rule`.`id_product_rule`)');
        Db::getInstance()->delete('cart_rule_combination', '`id_cart_rule_1` = ' . (int) $cartRule->id . ' OR `id_cart_rule_2` = ' . (int) $cartRule->id);
        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
				VALUES (' . (int) $cartRule->id . ',1)');
        $id_product_rule_group = Db::getInstance()->Insert_ID();
        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule` (`id_product_rule_group`, `type`)
						VALUES (' . (int) $id_product_rule_group . ', "products")');
        $id_product_rule = Db::getInstance()->Insert_ID();
        $values = [];
        $values[] = '(' . (int) $id_product_rule . ',' . (int) $cartRule->reduction_product . ')';
        $values = array_unique($values);
        if (count($values)) {
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES ' . implode(',', $values));
        }
    }
}