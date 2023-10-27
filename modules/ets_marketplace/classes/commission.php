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

class Ets_mp_commission extends ObjectModel
{
    protected static $instance;
    public $id_customer;
    public $reference;
    public $id_order;
    public $id_product;
    public $id_product_attribute;
    public $id_shop;
    public $product_name;
    public $price;
    public $price_tax_incl;
    public $quantity;
    public $total_price;
    public $total_price_tax_incl;
    public $use_tax;
    public $status;
    public $commission;
    public $note;
    public $expired_date;
    public $date_add;
    public $date_upd;
    public static $definition = array(
        'table' => 'ets_mp_seller_commission',
        'primary' => 'id_seller_commission',
        'multilang' => false,
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT),
            'reference' => array('type' => self::TYPE_STRING),
            'id_order' => array('type' => self::TYPE_STRING),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_product' => array('type' => self::TYPE_STRING,),
            'id_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'product_name' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'price' => array('type' => self::TYPE_STRING, 'validate' => 'isPrice'),
            'price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'quantity' => array('type' => self::TYPE_STRING),
            'total_price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'status' => array('type' => self::TYPE_INT),
            'commission' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'expired_date' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'note' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'use_tax' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_upd' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
        )
    );

    public function __construct($id_item = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_item, $id_lang, $id_shop);
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_mp_commission();
        }
        return self::$instance;
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_marketplace', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }

    public function update($null_value = true)
    {
        $status_old = (int)Db::getInstance()->getValue('SELECT status FROM `' . _DB_PREFIX_ . 'ets_mp_seller_commission` WHERE id_seller_commission=' . (int)$this->id);
        $result = parent::update($null_value);
        if ($result && $this->status != $status_old) {
            if (Configuration::get('ETS_MP_EMAIL_SELLER_COMMISSION_VALIDATED_OR_CANCELED') || Configuration::get('ETS_MP_EMAIL_ADMIN_COMMISSION_VALIDATED_OR_CANCELED')) {
                $seller = Ets_mp_seller::_getSellerByIdCustomer($this->id_customer);
                $data = array(
                    '{seller_name}' => $seller->seller_name,
                    '{commission_ID}' => $this->id,
                    '{amount}' => Tools::displayPrice($this->commission, new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))),
                    '{reason}' => $this->note,
                    '{created_date}' => $this->date_add,
                    '{approved_date}' => date('Y-m-d H:i:s'),
                    '{canceled_date}' => date('Y-m-d H:i:s'),
                );
                if (Configuration::get('ETS_MP_EMAIL_SELLER_COMMISSION_VALIDATED_OR_CANCELED')) {
                    if ($this->status == 1) {
                        $subjects = array(
                            'translation' => $this->l('Your commission has been validated'),
                            'origin' => 'Your commission has been validated',
                            'specific' => 'commission'
                        );
                        Ets_marketplace::sendMail('to_seller_commission_validated', $data, $seller->seller_email, $subjects, $seller->seller_name);
                    } elseif ($this->status == 0) {
                        $subjects = array(
                            'translation' => $this->l('Your commission has been canceled'),
                            'origin' => 'Your commission has been canceled',
                            'specific' => 'commission'
                        );
                        Ets_marketplace::sendMail('to_seller_commission_canceled', $data, $seller->seller_email, $subjects, $seller->seller_name);
                    }
                }
                if (Configuration::get('ETS_MP_EMAIL_ADMIN_COMMISSION_VALIDATED_OR_CANCELED')) {
                    if ($this->status == 1) {
                        $subjects = array(
                            'translation' => $this->l('A commission has been validated'),
                            'origin' => 'A commission has been validated',
                            'specific' => 'commission'
                        );
                        Ets_marketplace::sendMail('to_admin_commission_validated', $data, '', $subjects);
                    } elseif ($this->status == 0) {
                        $subjects = array(
                            'translation' => $this->l('A commission has been canceled'),
                            'origin' => 'A commission has been canceled',
                            'specific' => 'commission',
                        );
                        Ets_marketplace::sendMail('to_admin_commission_canceled', $data, '', $subjects);
                    }

                }
            }

        }
        return $result;
    }

    public function add($auto_date = true, $null_value = true)
    {
        do {
            $reference = Ets_mp_commission::generateReference();
        } while (Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_mp_seller_commission` WHERE reference="' . pSQL($reference) . '"'));
        $this->reference = $reference;
        $res = parent::add($auto_date, $null_value);
        if ($res && (Configuration::get('ETS_MP_EMAIL_ADMIN_COMMISSION_CREATED') || Configuration::get('ETS_MP_EMAIL_ADMIN_COMMISSION_CREATED'))) {
            if ($this->status == -1)
                $status = Module::getInstanceByName('ets_marketplace')->displayText($this->l('Pending'), 'span', 'ets_mp_status pending');
            elseif ($this->status == 0)
                $status = Module::getInstanceByName('ets_marketplace')->displayText($this->l('Canceled'), 'span', 'ets_mp_status canceled');
            elseif ($this->status == 1)
                $status = Module::getInstanceByName('ets_marketplace')->displayText($this->l('Approved'), 'span', 'ets_mp_status approved');
            $seller = Ets_mp_seller::_getSellerByIdCustomer($this->id_customer);
            if ($this->id_order)
                $order = new Order($this->id_order);
            $data = array(
                '{seller_name}' => $seller->seller_name,
                '{commission_ID}' => $this->id,
                '{amount}' => Tools::displayPrice($this->commission, new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))),
                '{product_name}' => $this->product_name,
                '{order_reference}' => isset($order) ? $order->reference : '',
                '{created_date}' => $this->date_add,
                '{status}' => $status,
            );
            if (Configuration::get('ETS_MP_EMAIL_SELLER_COMMISSION_CREATED')) {
                $subjects = array(
                    'translation' => $this->l('New seller commission has been created'),
                    'origin' => 'New seller commission has been created',
                    'specific' => 'commission'
                );
                Ets_marketplace::sendMail('to_seller_commission_created', $data, $seller->seller_email, $subjects, $seller->seller_name);
            }
            if (Configuration::get('ETS_MP_EMAIL_ADMIN_COMMISSION_CREATED')) {
                $subjects = array(
                    'translation' => $this->l('New commission has been created'),
                    'origin' => 'New commission has been created',
                    'specific' => 'commission'
                );
                Ets_marketplace::sendMail('to_admin_commission_created_for_seller', $data, '', $subjects);

            }
        }
        return $res;
    }

    public static function generateReference()
    {
        return Tools::strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
    }

    public static function getCommistionBYIDOrder($id_order)
    {
        return Db::getInstance()->executeS('SELECT id_seller_commission FROM `' . _DB_PREFIX_ . 'ets_mp_seller_commission` WHERE id_order="' . (int)$id_order . '"');
    }

    public static function deleteCommistion($id_order, $id_product, $id_product_attribute)
    {
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_mp_seller_commission` WHERE id_order="' . (int)$id_order . '" AND id_product="' . (int)$id_product . '" AND id_product_attribute="' . (int)$id_product_attribute . '"');
    }

    public static function getCommistion($id_order, $id_product, $id_product_attribute)
    {
        if ($id_commission = (int)Db::getInstance()->getValue('SELECT id_seller_commission FROM `' . _DB_PREFIX_ . 'ets_mp_seller_commission` WHERE id_order="' . (int)$id_order . '" AND id_product="' . (int)$id_product . '" AND id_product_attribute="' . (int)$id_product_attribute . '"'))
            $commission = new Ets_mp_commission($id_commission);
        else
            $commission = new Ets_mp_commission();
        return $commission;
    }

    public static function changeCommissionWhenUpdateOrder($orderDetail)
    {
        if (!Configuration::get('ETS_MP_ENABLED') || !Validate::isLoadedObject($orderDetail))
            return true;
        $order = new Order($orderDetail->id_order);
        $product = Db::getInstance()->getRow('SELECT product_name, sum(total_price_tax_excl) as total_price_tax_excl,sum(total_price_tax_incl) as total_price_tax_incl, sum(unit_price_tax_excl) as unit_price_tax_excl,sum(unit_price_tax_incl) as unit_price_tax_incl, sum(product_quantity) as product_quantity,product_id,product_attribute_id FROM `' . _DB_PREFIX_ . 'order_detail` WHERE id_order="' . (int)$orderDetail->id_order . '" AND product_id="' . (int)$orderDetail->product_id . '" AND product_attribute_id="' . (int)$orderDetail->product_attribute_id . '"');
        if (($id_customer = Ets_mp_seller::getIDCustomerSellerByIdProduct($product['product_id']))) {
            if ($id_customer != Ets_mp_seller::getIDCustomerSellerByIDOrder($order->id)) {
                Ets_mp_seller::addOrderToSeller($id_customer, $order->id);
            }
            $commission = self::getCommistion($order->id, $product['product_id'], $product['product_attribute_id']);
            $commission->id_product = (int)$product['product_id'];
            $commission->id_customer = $id_customer;
            $commission->id_order = (int)$order->id;
            $commission->id_product_attribute = (int)$product['product_attribute_id'];
            $commission->product_name = $product['product_name'];
            $commission->quantity = (int)$product['product_quantity'];
            $commission->price = (float)Tools::ps_round(Tools::convertPrice($product['unit_price_tax_excl'], null, false), 6);
            $commission->price_tax_incl = (float)Tools::ps_round(Tools::convertPrice($product['unit_price_tax_incl'], null, false), 6);
            $commission->total_price = (float)Tools::ps_round(Tools::convertPrice($product['total_price_tax_excl'], null, false), 6);
            $commission->total_price_tax_incl = (float)Tools::ps_round(Tools::convertPrice($product['total_price_tax_incl'], null, false), 6);
            $commission->id_shop = $order->id_shop;
            $commission->date_add = date('Y-m-d H:i:s');
            $commission->date_upd = date('Y-m-d H:i:s');
            $seller = Ets_mp_seller::_getSellerByIdCustomer($id_customer);
            $commistion_rate = (float)$seller->getCommissionRate(false, $commission->id_product);

            if (Configuration::get('ETS_MP_COMMISSION_EXCLUDE_TAX')) {
                $commission->commission = (float)Tools::ps_round(Tools::convertPrice($product['total_price_tax_excl'], null, false) * $commistion_rate / 100, 6);
                $commission->use_tax = 0;
            } else {
                $commission->commission = (float)Tools::ps_round(Tools::convertPrice($product['total_price_tax_incl'], null, false) * $commistion_rate / 100, 6);
                $commission->use_tax = 1;
            }
            if (!$commission->id) {
                if (Configuration::get('ETS_MP_COMMISSION_PENDING_WHEN') && ($status_pedding = explode(',', Configuration::get('ETS_MP_COMMISSION_PENDING_WHEN'))) && in_array($order->current_state, $status_pedding)) {
                    $commission->status = -1;
                } elseif (Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN') && ($status_approved = explode(',', Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'))) && in_array($order->current_state, $status_approved)) {
                    if (!$days = (int)Configuration::get('ETS_MP_VALIATE_COMMISSION_IN_DAYS'))
                        $commission->status = 1;
                    else {
                        $commission->status = -1;
                        $commission->expired_date = date('Y-m-d H:i:s', strtotime("+ $days days"));
                    }
                } elseif (Configuration::get('ETS_MP_COMMISSION_CANCELED_WHEN') && ($status_canceled = explode(',', Configuration::get('ETS_MP_COMMISSION_CANCELED_WHEN'))) && in_array($order->current_state, $status_canceled)) {
                    $commission->status = 0;
                } else
                    $commission->status = -1;
                $commission->add();
            } else
                $commission->update();
        }
        return '';
    }

    public static function getOrders($filter = '', $having = "", $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if ($total)
            $sql = 'SELECT COUNT(DISTINCT o.id_order)';
        else
            $sql = 'SELECT o.*,so.id_order as id_order_seller,CONCAT(customer.firstname, " ", customer.lastname) as seller_name,customer.id_customer as id_customer_seller, s.id_seller,CONCAT(c.firstname, " ", c.lastname) as customer_name,sl.*,sum(sc.commission) as total_commission,if(sc.use_tax,SUM(sc.total_price_tax_incl-sc.commission),SUM(sc.total_price-sc.commission)) as admin_earned';
        $sql .= ' FROM `' . _DB_PREFIX_ . 'orders` o 
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_order` so ON (o.id_order=so.id_order)
        LEFT JOIN `' . _DB_PREFIX_ . 'customer` customer ON(so.id_customer=customer.id_customer)
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller` s ON (s.id_customer=customer.id_customer AND s.id_shop="' . (int)Context::getContext()->shop->id . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_lang` sl ON (s.id_seller=sl.id_seller AND sl.id_lang="' . (int)Context::getContext()->language->id . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.id_customer=o.id_customer)
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_commission` sc ON (sc.id_order=o.id_order)
        WHERE 1 ' . ($filter ? (string)$filter : '');
        if (!$total) {
            $sql .= ' GROUP BY o.id_order ' . ($order_by ? ' ORDER By ' . pSQL($order_by) : '');
            if ($having)
                $sql .= ' HAVING 1 ' . (string)$having;
            $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
        }
        if ($total)
            return Db::getInstance()->getValue($sql);
        else {
            return Db::getInstance()->executeS($sql);
        }
    }

    public static function getSellerCommissions($filter = '', $having = "", $start = 0, $limit = 12, $order_by = '', $total = false)
    {
        if ($total)
            $sql = 'SELECT COUNT(sc.id)';
        else
            $sql = 'SELECT sc.*,CONCAT(customer.firstname," ",customer.lastname) as seller_name,customer.id_customer as id_customer_seller ,seller.id_seller as seller_id,seller_lang.shop_name,p.id_product as product_id';
        $sql .= ' FROM (
        SELECT id_seller_commission as id, "commission" as type,reference,product_name,price,price_tax_incl,quantity,commission,if(c.use_tax,c.total_price_tax_incl-c.commission,c.total_price-c.commission) as admin_earning,status,note,date_add,id_shop,id_customer,id_order,id_product,id_product_attribute,"" as id_withdraw,"" as id_voucher FROM `' . _DB_PREFIX_ . 'ets_mp_seller_commission` c
        UNION ALL
        SELECT id_ets_mp_commission_usage as id,"usage" as type,reference,"" as product_name,"" as price,"" as price_tax_incl,"" as quantity,amount as commission,"" as admin_earning,status,note,date_add,id_shop,id_customer,id_order,"" as id_product,"" as id_product_attribute,id_withdraw,id_voucher FROM `' . _DB_PREFIX_ . 'ets_mp_commission_usage` u
        )as sc
        LEFT JOIN `' . _DB_PREFIX_ . 'customer` customer ON (customer.id_customer=sc.id_customer)
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller` seller ON (customer.id_customer= seller.id_customer)
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_mp_seller_lang` seller_lang ON (seller.id_seller= seller_lang.id_seller AND seller_lang.id_lang="' . (int)Context::getContext()->language->id . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product=sc.id_product)
        WHERE sc.id_shop="' . (int)Context::getContext()->shop->id . '"' . ($filter ? (string)$filter : '');
        if (!$total) {
            $sql .= ($order_by ? ' ORDER By ' . pSQL($order_by) : '');
            if ($having)
                $sql .= ' HAVING 1 ' . (string)$having;
            $sql .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
        }
        if ($total)
            return Db::getInstance()->getValue($sql);
        else {
            return Db::getInstance()->executeS($sql);
        }
    }

    public static function getCommistionexpired()
    {
        return Db::getInstance()->executeS('SELECT id_seller_commission FROM `'._DB_PREFIX_.'ets_mp_seller_commission` WHERE status=-1 AND expired_date!="0000-00-00 00:00:00" AND expired_date <="'.pSQL(date('Y-m-d H:i:s')).'"');
    }
    public static function saveRateByCategory($id_category,$rate)
    {
        if($rate)
        {
            if(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_mp_category_commission` WHERE id_category='.(int)$id_category))
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_mp_category_commission` SET commission_rate="'.(float)$rate.'" WHERE id_category='.(int)$id_category);
            else
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_category_commission`(id_category,commission_rate) VALUES('.(int)$id_category.','.(float)$rate.')');
        }
        else
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_mp_category_commission` WHERE id_category='.(int)$id_category);
    }
    public static function getMinDateUpdCommission()
    {
        $sql = 'SELECT MIN(sc.date_upd) FROM `'._DB_PREFIX_.'ets_mp_seller_commission` sc
                    WHERE sc.id_product!=0 AND sc.id_shop="'.(int)Context::getContext()->shop->id.'" AND sc.status=1';
        return Db::getInstance()->getValue($sql);
    }
    public static function getMaxDateUpdCommission()
    {
        $sql = 'SELECT MAX(sc.date_upd) FROM `'._DB_PREFIX_.'ets_mp_seller_commission` sc
                    WHERE sc.id_product!=0 AND sc.id_shop="'.(int)Context::getContext()->shop->id.'" AND sc.status=1';
        return Db::getInstance()->getValue($sql);
    }
    public static function getLatestSellerCommissions()
    {
        $sql ='SELECT sc.*,customer.id_customer as id_customer_seller,CONCAT(customer.firstname," ",customer.lastname) as seller_name,seller.id_seller,seller_lang.shop_name';
        $sql .= ' FROM `'._DB_PREFIX_.'ets_mp_seller_commission` sc
        LEFT JOIN `'._DB_PREFIX_.'customer` customer ON (customer.id_customer=sc.id_customer)
        LEFT  JOIN `'._DB_PREFIX_.'ets_mp_seller` seller ON (customer.id_customer= seller.id_customer)
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_lang` seller_lang ON (seller.id_seller= seller_lang.id_seller AND seller_lang.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE sc.id_shop="'.(int)Context::getContext()->shop->id.'" AND sc.status=1 ORDER BY sc.id_seller_commission DESC LIMIT 0,10';
        return Db::getInstance()->executeS($sql);
    }
    public static function getTopSellerCommissions()
    {
        $sql = 'SELECT s.*,CONCAT(customer.firstname," ", customer.lastname) as seller_name,customer.email as seller_email,sl.shop_name,sl.shop_address,sl.shop_description,seller_commission.total_commission FROM `'._DB_PREFIX_.'ets_mp_seller` s
            INNER JOIN (
                SELECT id_customer,SUM(commission) as total_commission FROM `'._DB_PREFIX_.'ets_mp_seller_commission` WHERE status=1 GROUP BY id_customer
            ) as seller_commission ON (seller_commission.id_customer=s.id_customer)
            LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller_lang` sl ON (s.id_seller = sl.id_seller AND sl.id_lang ="'.(int)Context::getContext()->language->id.'")
            LEFT JOIN `'._DB_PREFIX_.'customer` customer ON (customer.id_customer=s.id_customer)
            WHERE s.id_shop="'.(int)Context::getContext()->shop->id.'" ORDER BY seller_commission.total_commission DESC LIMIT 0,10';
        return Db::getInstance()->executeS($sql);
    }
    public static function getTotalTurnOver($filter=false)
    {
        if(Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'))
        {
            $status = explode(',',Configuration::get('ETS_MP_COMMISSION_APPROVED_WHEN'));
            $sql = 'SELECT SUM(o.total_paid_tax_incl/c.conversion_rate) FROM `'._DB_PREFIX_.'orders` o
                INNER JOIN `'._DB_PREFIX_.'currency` c ON (o.id_currency=c.id_currency)
                INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_order` seller_order ON (seller_order.id_order=o.id_order)
                WHERE o.id_shop="'.(int)Context::getContext()->shop->id.'" AND o.current_state IN ('.implode(',',array_map('intval',$status)).')'.($filter ? (string)$filter:'').'
            ';
            return (float)Db::getInstance()->getValue($sql);
        }
        return 0;
    }
    public static function getTotalSellerRevenve($filter=false)
    {
        $sql = 'SELECT if(sc.use_tax,SUM(sc.total_price_tax_incl-sc.commission),SUM(sc.total_price-sc.commission)) FROM `'._DB_PREFIX_.'ets_mp_seller_commission` sc
        WHERE sc.id_product!=0 AND sc.id_shop="'.(int)Context::getContext()->shop->id.'" AND sc.status=1'.($filter ? (string)$filter:'');
        return Db::getInstance()->getValue($sql);
    }
    public static function getTotalSellerCommission($filter=false)
    {
        $sql = 'SELECT sum(commission) FROM `'._DB_PREFIX_.'ets_mp_seller_commission` WHERE status=1 AND id_shop="'.(int)Context::getContext()->shop->id.'"'.($filter ? (string)$filter:'');
        return Db::getInstance()->getValue($sql);
    }
}