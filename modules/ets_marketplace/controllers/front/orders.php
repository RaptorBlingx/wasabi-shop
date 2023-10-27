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
/**
 * Class Ets_MarketPlaceOrdersModuleFrontController
 * @property \Ets_marketplace $module
 * @property \Ets_mp_seller $seller;
 */
class Ets_MarketPlaceOrdersModuleFrontController extends ModuleFrontController
{
    public $seller;
    public $_errors= array();
    public $_success ='';
    public function __construct()
	{
		parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
        if($this->module->is17)
        {
            $smarty = $this->context->smarty;
            smartyRegisterFunction($smarty, 'function', 'displayAddressDetail', array('AddressFormat', 'generateAddressSmarty'));
            smartyRegisterFunction($smarty, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
        }
	}
    public function postProcess()
    {
        parent::postProcess();
        if(!$this->context->customer->isLogged() || !($this->seller = $this->module->_getSeller(true)) )
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->module->_checkPermissionPage($this->seller))
            die($this->module->l('You do not have permission','orders'));
        if(($id_order = (int)Tools::getValue('id_order')) && Validate::isUnsignedId($id_order))
        {
            if(!$this->seller->checkHasOrder($id_order))
                die($this->module->l('You do not have permission','orders'));
        }
        if(Tools::isSubmit('submitShippingNumber'))
        {
            $this->_submitShippingNumber();
        }
        if(Tools::isSubmit('submitChangeState') && ($id_order = (int)Tools::getValue('id_order')) && Validate::isUnsignedId($id_order) && ($id_order_state = (int)Tools::getValue('id_order_state')) && Validate::isUnsignedId($id_order_state) )
        {
            $order= new Order($id_order);
            $current_order_state = $order->getCurrentOrderState();
            if(!Configuration::get('ETS_MP_SELLER_CAN_CHANGE_ORDER_STATUS') || !($status =Configuration::get('ETS_MP_SELLER_ALLOWED_STATUSES')))
            {
                $this->_errors[] = $this->module->l('You do not have permission to edit order status','orders');
            }elseif(($status = explode(',',$status)) && !in_array($id_order_state,$status))
            {
                $this->_errors[] = $this->module->l('Order status is not valid','orders');
            }
            elseif($id_order_state == $current_order_state->id)
                $this->_errors[] = $this->module->l('The order has already been assigned this status','orders');
            elseif(!Validate::isLoadedObject($order) || !$this->seller->checkHasOrder($id_order))
                $this->_errors[] = $this->module->l('Order is not valid','orders');
            else
            {
                $history = new OrderHistory();
                $order_state = new OrderState($id_order_state);
                $history->id_order = $order->id;
                $history->id_employee = 1;
                $use_existings_payment = false;
                if (!$order->hasInvoice()) {
                    $use_existings_payment = true;
                }
                $history->changeIdOrderState((int) $order_state->id, $order, $use_existings_payment);

                $carrier = new Carrier($order->id_carrier, $order->id_lang);
                $templateVars = array();
                if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number) {
                    $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
                }
                if ($history->addWithemail(true, $templateVars)) {
                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                        foreach ($order->getProducts() as $product) {
                            if (StockAvailable::dependsOnStock($product['product_id'])) {
                                StockAvailable::synchronize($product['product_id'], (int) $product['id_shop']);
                            }
                        }
                    }
                    $this->_success = $this->module->l('Updated order status successfully','orders');
                }
                else
                    $this->_errors[] = $this->module->l('An error occurred while changing order status, or we were unable to send an email to the customer.','orders');
            }
        }
        if(Tools::isSubmit('submitMessage') && $id_order = (int)Tools::getValue('id_order'))
        {
            $order = new Order($id_order);
            $id_customer = (int)Tools::getValue('id_customer');
            $customer = new Customer($id_customer);
            $visibility = (int)Tools::getValue('visibility');
            $message = Tools::getValue('message');
            if (!Validate::isLoadedObject($customer)) {
                $this->_errors[] = $this->module->l('The customer is invalid.','orders');
            } elseif (!$message) {
                $this->_errors[] = $this->module->l('The message cannot be blank.','orders');
            }
            elseif(!Validate::isCleanHtml($message))
            {
                $this->_errors[] = $this->module->l('The message is not valid.','orders');
            } else {
                /* Get message rules and and check fields validity */
                $rules = call_user_func(array('Message', 'getValidationRules'), 'Message');
                foreach ($rules['required'] as $field) {
                    if (($value = Tools::getValue($field)) == false && (string) $value != '0') {
                        $val = Tools::getValue('id_message');
                        if (!$val || $field != 'passwd') {
                            $this->_errors[] = $field.' '.$this->module->l('is required','orders');
                        }
                    }
                }
                foreach ($rules['size'] as $field => $maxLength) {
                    $field_value = Tools::getValue($field);
                    if ($field_value && Tools::strlen($field_value) > $maxLength) {
                        $this->_errors[] = $field.' '.$this->module->l('is too long','orders').' '. $maxLength;
                    }
                }
                foreach ($rules['validate'] as $field => $function) {
                    $field_value = Tools::getValue($field);
                    if ($field_value) {
                        if (!Validate::$function(htmlentities($field_value, ENT_COMPAT, 'UTF-8'))) {
                            $this->_errors[] = $field. ' '.$this->module->l(' is not valid','orders');
                        }
                    }
                    unset($function);
                }
                if (!count($this->_errors)) {
                    //check if a thread already exist
                    $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($customer->email, $order->id);
                    if (!$id_customer_thread) {
                        $customer_thread = new CustomerThread();
                        $customer_thread->id_contact = 0;
                        $customer_thread->id_customer = (int) $order->id_customer;
                        $customer_thread->id_shop = (int) $this->context->shop->id;
                        $customer_thread->id_order = (int) $order->id;
                        $customer_thread->id_lang = (int) $this->context->language->id;
                        $customer_thread->email = $customer->email;
                        $customer_thread->status = 'open';
                        $customer_thread->token = Tools::passwdGen(12);
                        $customer_thread->add();
                    } else {
                        $customer_thread = new CustomerThread((int) $id_customer_thread);
                    }
                    $customer_message = new CustomerMessage();
                    $customer_message->id_customer_thread = $customer_thread->id;
                    $customer_message->id_employee = 1;
                    $customer_message->message = $message;
                    $customer_message->private = $visibility;
                    $customer_message->read=1;
                    $add = true;
                    if (!$customer_message->add()) {
                    {
                        $add = false;
                        $this->_errors[] = $this->module->l('An error occurred while saving the message.','orders');
                    }
                    } elseif ($customer_message->private) {
                        $this->_success = $this->module->l('Message sent successfully.','orders');
                    } else {
                        $message = $customer_message->message;
                        if (Configuration::get('PS_MAIL_TYPE', null, null, $order->id_shop) != Mail::TYPE_TEXT) {
                            $message = Tools::nl2br($customer_message->message);
                        }

                        $varsTpl = array(
                            '{lastname}' => $customer->lastname,
                            '{firstname}' => $customer->firstname,
                            '{id_order}' => $order->id,
                            '{order_name}' => $order->getUniqReference(),
                            '{message}' => $message,
                        );
                        $subjects = array(
                            'translation' => $this->module->l('New message regarding your order','orders'),
                            'origin'=>'New message regarding your order',
                            'specific' => 'orders'
                        );
                        if (
                            !Ets_marketplace::sendMail('order_merchant_comment',$varsTpl,$customer->email,$subjects,$customer->firstname . ' ' . $customer->lastname)
                        ) {
                            $this->_errors[] = $this->module->l('An error occurred while sending an email to the customer.','orders');
                        }
                    }
                    if($add)
                    {
                        $this->seller->addCustomerMessage($customer_message->id);
                    }
                    
                }
            }
        }
    }
    public function initContent()
	{
		parent::initContent();
        $html = '';
        if($this->_errors)
            $html .= $this->module->displayError($this->_errors);
        if($this->_success)
            $html .= $this->module->displayConfirmation($this->_success);
        $this->context->smarty->assign(
            array(
                'path' => $this->module->getBreadCrumb(),
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false, 
                'html_content' => $html.$this->_initContent(),
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/orders.tpl');      
        else        
            $this->setTemplate('orders_16.tpl'); 
    }
    public function _initContent()
    {
        $id_order = (int)Tools::getValue('id_order');
        if(Tools::isSubmit('list') || !$id_order)
        {
            $orderStates =OrderState::getOrderStates($this->context->language->id,true);
            $fields_list = array(
                'reference'=>array(
                    'title' => $this->module->l('Reference','orders'),
                    'type'=> 'text',
                    'sort' => true,
                    'filter' => true,
                ),
                'date_add' => array(
                    'title' => $this->module->l('Date','orders'),
                    'type' => 'date',
                    'sort' => true,
                    'filter' => true
                ),
                'total_paid_tax_incl' => array(
                    'title' => $this->module->l('Total','orders'),
                    'type' => 'int',
                    'sort' => true,
                    'filter' => true
                ),
                'commission' => array(
                    'title' => $this->module->l('Commissions','orders'),
                    'type' => 'int',
                    'sort' => true,
                    'filter' => true
                ),
                'products' => array(
                    'title' => $this->module->l('Products','orders'),
                    'type' => 'text',
                    'strip_tag'=> false,
                ),
                'current_state' => array(
                    'title' => $this->module->l('Status','orders'),
                    'type' => 'select',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'list' => $orderStates,
                        'id_option' => 'id_order_state',
                        'value' => 'name',
                    ),
                )
            );
            //Filter
            $show_resset = false;
            $filter = "";
            if(($reference = Tools::getValue('reference')) || $reference!='')
            {
                if(Validate::isReference($reference))
                    $filter .=' AND o.reference LIKE "%'.pSQL($reference).'%"';
                $show_resset = true;
            }
            if(($payment = Tools::getValue('payment')) || $payment!='')
            {
                if(Validate::isGenericName($payment))
                    $filter .=' AND o.payment LIKE "%'.pSQL($payment).'%"';
                $show_resset = true;
            }
            if(($date_add_min = trim(Tools::getValue('date_add_min'))) || $date_add_min!='')
            {
                if(Validate::isDate($date_add_min))
                    $filter .=' AND o.date_add >= "'.pSQL($date_add_min).' 00:00:00"';
                $show_resset = true;
            }
            if(($date_add_max = trim(Tools::getValue('date_add_max'))) || $date_add_max !='')
            {
                if(Validate::isDate($date_add_max))
                    $filter .=' AND o.date_add <= "'.pSQL($date_add_max).' 23:59:59"';
                $show_resset = true;
            }
            if(($total_paid_tax_incl_min = trim(Tools::getValue('total_paid_tax_incl_min'))) || $total_paid_tax_incl_min!='')
            {
                if(Validate::isFloat($total_paid_tax_incl_min))
                    $filter .=' AND o.total_paid_tax_incl >= "'.(float)$total_paid_tax_incl_min.'"';
                $show_resset = true;
            }
            if(($total_paid_tax_incl_max = trim(Tools::getValue('total_paid_tax_incl_max'))) || $total_paid_tax_incl_max!='')
            {
                if(Validate::isFloat($total_paid_tax_incl_max))
                    $filter .=' AND o.total_paid_tax_incl <= "'.(float)$total_paid_tax_incl_max.'"';
                $show_resset = true;
            }
            if(($commission_min = trim(Tools::getValue('commission_min'))) && $commission_min!='')
            {
                if(Validate::isFloat($commission_min))
                    $filter .=' AND c.commission >= "'.(float)$commission_min.'"';
                $show_resset = true;
            }
            if(($commission_max =trim(Tools::getValue('commission_max'))) || $commission_max!='')
            {
                if(Validate::isFloat($commission_max))
                    $filter .=' AND c.commission <= "'.(float)$commission_max.'"';
                $show_resset = true;
            }
            if(($current_state = trim(Tools::getValue('current_state'))) || $current_state!='')
            {
                if(Validate::isUnsignedId($current_state))
                    $filter .=' AND o.current_state = "'.(int)$current_state.'"';
                $show_resset = true;
            }
            //Sort
            $sort = "";
            $sort_type=Tools::getValue('sort_type','desc');
            $sort_value = Tools::getValue('sort','date_add');
            if($sort_value)
            {
                switch ($sort_value) {
                    case 'reference':
                        $sort .='o.reference';
                        break;
                    case 'date_add':
                        $sort .= 'o.date_add';
                        break;
                    case 'total_paid_tax_incl':
                        $sort .= 'o.total_paid_tax_incl';
                        break;
                    case 'commission':
                        $sort .= 'c.commission';
                        break;
                    case 'payment':
                        $sort .= 'o.payment';
                        break;
                    case 'current_state':
                        $sort .= 'o.current_state';
                        break;
                }
                if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                    $sort .= ' '.trim($sort_type);  
            }
            //Paggination
            $page = (int)Tools::getValue('page');
            if($page<=0)
                $page = 1;
            $totalRecords = (int) $this->seller->getOrders($filter,0,0,'',true);
            $paggination = new Ets_mp_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url =$this->context->link->getModuleLink($this->module->name,'orders',array('list'=>true, 'page'=>'_page_')).$this->module->getFilterParams($fields_list,'ms_orders');
            $paggination->limit =  (int)Tools::getValue('paginator_order_select_limit',10);
            $paggination->name ='order';
            $paggination->num_links =5;
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $orders = $this->seller->getOrders($filter, $start,$paggination->limit,$sort,false);
            if($orders)
            {
                foreach($orders as &$order)
                {
                    $order['total_paid_tax_incl'] = Tools::displayPrice($order['total_paid_tax_incl'],new Currency($order['id_currency']));
                    $order['current_state'] = $this->module->displayOrderState($order['current_state']);  
                    $order['child_view_url'] = $this->context->link->getModuleLink($this->module->name,'orders',array('id_order'=>$order['id_order']));    
                    $order['commission'] = Tools::displayPrice($order['commission'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));  
                    $order['products'] = $this->getProductList($order['id_order']);            
                }
            }
            $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','orders');
            $paggination->style_links = $this->module->l('links','orders');
            $paggination->style_results = $this->module->l('results','orders');
            $listData = array(
                'name' => 'ms_orders',
                'actions' => array('view',),
                'currentIndex' => $this->context->link->getModuleLink($this->module->name,'orders',array('list'=>1)).($paggination->limit!=10 ?'&paginator_order_select_limit='.$paggination->limit:''),
                'postIndex' => $this->context->link->getModuleLink($this->module->name,'orders',array('list'=>1)),
                'identifier' => 'id_order',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => $this->module->l('Orders','orders'),
                'fields_list' => $fields_list,
                'field_values' => $orders,
                'paggination' => $paggination->render(),
                'filter_params' => $this->module->getFilterParams($fields_list,'ms_orders'),
                'show_reset' =>$show_resset,
                'totalRecords' => $totalRecords,
                'sort'=> $sort_value,
                'show_add_new'=> false,
                'sort_type' => $sort_type,
            );            
            return $this->module->renderList($listData);
        }
        elseif($id_order)
        {
            return $this->getQuickViewOrder($id_order);
        }
    }
    public function getQuickViewOrder($id_order)
    {
        $order = new Order($id_order);
        if (!Validate::isLoadedObject($order)) {
            $this->errors[] = $this->module->l('The order cannot be found within your database.','orders');
        }
        $customer = new Customer($order->id_customer);
        $carrier = new Carrier($order->id_carrier);
        $products = $this->getProducts($order);
        $currency = new Currency((int)$order->id_currency);
        // Carrier module call
        $carrier_module_call = null;
        if ($carrier->is_module) {
            $module = Module::getInstanceByName($carrier->external_module_name);
            if (method_exists($module, 'displayInfoByCart')) {
                $carrier_module_call = call_user_func(array($module, 'displayInfoByCart'), $order->id_cart);
            }
        }

        // Retrieve addresses information
        $addressInvoice = new Address($order->id_address_invoice, $this->context->language->id);
        if (Validate::isLoadedObject($addressInvoice) && $addressInvoice->id_state) {
            $invoiceState = new State((int)$addressInvoice->id_state);
        }

        if ($order->id_address_invoice == $order->id_address_delivery) {
            $addressDelivery = $addressInvoice;
            if (isset($invoiceState)) {
                $deliveryState = $invoiceState;
            }
        } else {
            $addressDelivery = new Address($order->id_address_delivery, $this->context->language->id);
            if (Validate::isLoadedObject($addressDelivery) && $addressDelivery->id_state) {
                $deliveryState = new State((int)($addressDelivery->id_state));
            }
        }

        $this->toolbar_title = '';

        // gets warehouses to ship products, if and only if advanced stock management is activated
        $warehouse_list = null;

        $order_details = $order->getOrderDetailList();
        foreach ($order_details as $order_detail) {
            $product = new Product($order_detail['product_id']);

            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
                && $product->advanced_stock_management) {
                $warehouses = Warehouse::getWarehousesByProductId($order_detail['product_id'], $order_detail['product_attribute_id']);
                foreach ($warehouses as $warehouse) {
                    if (!isset($warehouse_list[$warehouse['id_warehouse']])) {
                        $warehouse_list[$warehouse['id_warehouse']] = $warehouse;
                    }
                }
            }
        }

        $payment_methods = array();
        foreach (PaymentModule::getInstalledPaymentModules() as $payment) {
            $module = Module::getInstanceByName($payment['name']);
            if (Validate::isLoadedObject($module) && $module->active) {
                $payment_methods[] = $module->displayName;
            }
        }

        // display warning if there are products out of stock
        $display_out_of_stock_warning = false;
        $current_order_state = $order->getCurrentOrderState();
        if (Configuration::get('PS_STOCK_MANAGEMENT') && (!Validate::isLoadedObject($current_order_state) || ($current_order_state->delivery != 1 && $current_order_state->shipped != 1))) {
            $display_out_of_stock_warning = true;
        }

        // products current stock informations (from stock_available)
        $stockLocationIsAvailable = false;
        foreach ($products as &$product) {
            // Get total customized quantity for current product
            $customized_product_quantity = 0;

            if (Ets_marketplace::validateArray($product['customizedDatas'])) {
                foreach ($product['customizedDatas'] as $customizationPerAddress) {
                    foreach ($customizationPerAddress as $customization) {
                        $customized_product_quantity += (int)$customization['quantity'];
                    }
                }
            }
            $product['customized_product_quantity'] = $customized_product_quantity;
            $product['current_stock'] = StockAvailable::getQuantityAvailableByProduct($product['product_id'], $product['product_attribute_id'], $product['id_shop']);
            $resume = OrderSlip::getProductSlipResume($product['id_order_detail']);
            $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
            $product['amount_refundable'] = $product['total_price_tax_excl'] - $resume['amount_tax_excl'];
            $product['amount_refundable_tax_incl'] = $product['total_price_tax_incl'] - $resume['amount_tax_incl'];
            $product['amount_refund'] = $order->getTaxCalculationMethod() ? Tools::displayPrice($resume['amount_tax_excl'], $currency) : Tools::displayPrice($resume['amount_tax_incl'], $currency);
            $product['refund_history'] = OrderSlip::getProductSlipDetail($product['id_order_detail']);
            $product['return_history'] = OrderReturn::getProductReturnDetail($product['id_order_detail']);

            // if the current stock requires a warning
            if ($product['current_stock'] <= 0 && $display_out_of_stock_warning) {
                $this->module->displayWarning($this->module->l('This product is out of stock: ','orders') . ' ' . $product['product_name']);
            }
            if ($product['id_warehouse'] != 0) {
                $warehouse = new Warehouse((int)$product['id_warehouse']);
                $product['warehouse_name'] = $warehouse->name;
                $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
                if (!empty($warehouse_location)) {
                    $product['warehouse_location'] = $warehouse_location;
                } else {
                    $product['warehouse_location'] = false;
                }
            } else {
                $product['warehouse_name'] = '--';
                $product['warehouse_location'] = false;
            }

            if (!empty($product['location'])) {
                $stockLocationIsAvailable = true;
            }
        }

        // Package management for order
        foreach ($products as &$product) {
            $pack_items = $product['cache_is_pack'] ? Pack::getItemTable($product['id_product'], $this->context->language->id, true) : array();
            foreach ($pack_items as &$pack_item) {
                $pack_item['current_stock'] = StockAvailable::getQuantityAvailableByProduct($pack_item['id_product'], $pack_item['id_product_attribute'], $pack_item['id_shop']);
                $this->setProductImageInformations($pack_item);
                if ($pack_item['image'] != null) {
                    $name = 'product_mini_' . (int)$pack_item['id_product'] . (isset($pack_item['id_product_attribute']) ? '_' . (int)$pack_item['id_product_attribute'] : '') . '.jpg';
                    // generate image cache, only for back office
                    $pack_item['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $pack_item['image']->getExistingImgPath() . '.jpg', $name, 45, 'jpg');
                    if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                        $pack_item['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                    } else {
                        $pack_item['image_size'] = false;
                    }
                }
            }
            $product['pack_items'] = $pack_items;
        }

        $gender = new Gender((int)$customer->id_gender, $this->context->language->id);

        $history = $order->getHistory($this->context->language->id);

        foreach ($history as &$order_state) {
            $order_state['text-color'] = Tools::getBrightness($order_state['color']) < 150 ? 'white' : 'black';
        }

        $shipping_refundable_tax_excl = $order->total_shipping_tax_excl;
        $shipping_refundable_tax_incl = $order->total_shipping_tax_incl;
        $slips = OrderSlip::getOrdersSlip($customer->id, $order->id);
        foreach ($slips as $slip) {
            $shipping_refundable_tax_excl -= $slip['total_shipping_tax_excl'];
            $shipping_refundable_tax_incl -= $slip['total_shipping_tax_incl'];
        }
        $shipping_refundable_tax_excl = max(0, $shipping_refundable_tax_excl);
        $shipping_refundable_tax_incl = max(0, $shipping_refundable_tax_incl);
        if(Module::isInstalled('ets_payment_with_fee') && Module::isEnabled('ets_payment_with_fee'))
        {
            if($fee_order = Ets_mp_seller::getFeeOrder($order->id))
            {
                if($fee_order)
                {
                    $priceFormatter = new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter();
                    $this->context->smarty->assign(
                        array(
                            'payment_fee' => $priceFormatter->format($fee_order, Currency::getCurrencyInstance((int)$order->id_currency)),
                        )
                    );
                }
            }
        }
        // Smarty assign
        $this->context->smarty->assign(array(
            'order' => $order,
            'order_state' => new OrderState($order->current_state, $this->context->language->id),
            'link' => $this->context->link,
            'cart' => new Cart($order->id_cart),
            'order_customer' => $customer,
            'gender' => $gender,
            'customer_addresses' => $customer->getAddresses($this->context->language->id),
            'addresses' => array(
                'delivery' => $addressDelivery,
                'deliveryState' => isset($deliveryState) ? $deliveryState : null,
                'invoice' => $addressInvoice,
                'invoiceState' => isset($invoiceState) ? $invoiceState : null,
            ),
            'current_index' => $this->context->link->getModuleLink($this->module->name,'orders'),
            'customerStats' => $customer->getStats(),
            'products' => $products,
            'can_edit' => false,
            'currentIndex' => $this->context->link->getModuleLink($this->module->name,'orders'),
            'stock_management' => true,
            'discounts' => $order->getCartRules(),
            'orders_total_paid_tax_incl' => $order->getOrdersTotalPaid(), // Get the sum of total_paid_tax_incl of the order with similar reference
            'total_paid' => $order->getTotalPaid(),
            'returns' => OrderReturn::getOrdersReturn($order->id_customer, $order->id),
            'shipping_refundable_tax_excl' => $shipping_refundable_tax_excl,
            'shipping_refundable_tax_incl' => $shipping_refundable_tax_incl,
            'customer_thread_message' => CustomerThread::getCustomerMessages($order->id_customer, null, $order->id),
            'orderMessages' => OrderMessage::getOrderMessages($order->id_lang),
            'messages' => $this->module->is17 ? CustomerThread::getCustomerMessagesOrder($order->id_customer, $order->id) : Message::getMessagesByOrderId($order->id, true),
            'carrier' => new Carrier($order->id_carrier),
            'history' => $history,
            'states' => Ets_mp_seller::getOrderStates($this->context->language->id,$order->current_state),
            'warehouse_list' => $warehouse_list,
            'currentState' => $order->getCurrentOrderState(),
            'currency' => new Currency($order->id_currency),
            'currencies' => Currency::getCurrenciesByIdShop($order->id_shop),
            'previousOrder' => (int)$this->seller->getpreviousOrder($id_order),
            'nextOrder' => (int)$this->seller->getNextOrder($id_order),
            'carrierModuleCall' => $carrier_module_call,
            'iso_code_lang' => $this->context->language->iso_code,
            'id_lang' => $this->context->language->id,
            'current_id_lang' => $this->context->language->id,
            'invoices_collection' => $order->getInvoicesCollection(),
            'not_paid_invoices_collection' => $order->getNotPaidInvoicesCollection(),
            'payment_methods' => $payment_methods,
            'invoice_management_active' => Configuration::get('PS_INVOICE', null, null, $order->id_shop),
            'display_warehouse' => (int)Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
            'carrier_list' => $this->getCarrierList($order),
            'recalculate_shipping_cost' => (int)Configuration::get('PS_ORDER_RECALCULATE_SHIPPING'),
            'ETS_MP_DISPLAY_CUSTOMER_ADDRESS' => (int)Configuration::get('ETS_MP_DISPLAY_CUSTOMER_ADDRESS'),
            'stock_location_is_available' => $stockLocationIsAvailable,
            'link_base' => $this->module->getBaseLink(),
            'HOOK_CONTENT_ORDER' => '',
            'HOOK_CONTENT_SHIP' => '',
            'HOOK_TAB_ORDER' => '',
            'HOOK_TAB_SHIP' => '',
        ));
        return $this->module->displayTpl('orders/view.tpl');
    }
    protected function setProductImageInformations(&$pack_item)
    {
        if (isset($pack_item['id_product_attribute']) && $pack_item['id_product_attribute']) {
            $attributeImage = Product::getCombinationImageById($pack_item['id_product_attribute']);
            $id_image = $attributeImage && isset($attributeImage['id_image']) ? $attributeImage['id_image']:0;
        }
        if (!isset($id_image) || !$id_image) {
            $id_image = Ets_mp_product::getImageByIDProduct($pack_item['id_product'],1);
            if(!$id_image)
                $id_image = Ets_mp_product::getImageByIDProduct($pack_item['id_product']);
        }
        $pack_item['image'] = null;
        $pack_item['image_size'] = null;

        if ($id_image) {
            $pack_item['image'] = new Image($id_image);
        }
    }
    protected function getProducts($order)
    {
        $products = $order->getProducts();
        foreach ($products as &$product) {
            if ($product['image'] != null) {
                $name = 'product_mini_' . (int)$product['product_id'] . (isset($product['product_attribute_id']) ? '_' . (int)$product['product_attribute_id'] : '') . '.jpg';
                // generate image cache, only for back office
                $product['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $product['image']->getExistingImgPath() . '.jpg', $name, 45, 'jpg');
                if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                    $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                } else {
                    $product['image_size'] = false;
                }
            }
        }
        ksort($products);
        return $products;
    }

    protected function getCarrierList($order)
    {
        $cart = new Cart($order->id_cart);
        $address = new Address((int)$cart->id_address_delivery);
        return Carrier::getCarriersForOrder(Address::getZoneById((int)$address->id), null, $cart);
    }
    public function getProductList($id_order)
    {
        $this->context->smarty->assign(
            array(
                'products' => Ets_mp_seller::getOrderDetails($id_order),
                'link'=> $this->context->link,
            )
        );
        return $this->module->displayTpl('orders/product_list.tpl');
    }
    public function _submitShippingNumber()
    {
        $id_order = (int)Tools::getValue('id_order');
        $id_order_carrier = (int)Tools::getValue('id_order_carrier');
        $orderCarrier = new OrderCarrier($id_order_carrier);
        $shipping_tracking_number = Tools::getValue('shipping_tracking_number');
        $error = '';
        if($shipping_tracking_number && !Validate::isTrackingNumber($shipping_tracking_number))
            $error = $this->module->l('Tracking number is not valid','orders');
        elseif(!$id_order || !Validate::isLoadedObject(new Order($id_order)))
            $error = $this->module->l('Order is not valid');
        elseif(!Validate::isLoadedObject($orderCarrier) || $orderCarrier->id_order != $id_order)
            $error = $this->module->l('Order carrier is not valid');
        else
        {
            $old_tracking_number = $orderCarrier->tracking_number;
            $orderCarrier->tracking_number = $shipping_tracking_number;
            $order = new Order($id_order);
            $order->shipping_number = $shipping_tracking_number;
            $order->update();  
            if($orderCarrier->update())
            {
                $carrier = new Carrier((int) $order->id_carrier, $order->id_lang);
                if (!empty($shipping_tracking_number) && $old_tracking_number != $shipping_tracking_number) {
                    if($this->module->is17)
                    {
                        if ($orderCarrier->sendInTransitEmail($order)) {
                            $customer = new Customer((int) $order->id_customer);
                            Hook::exec('actionAdminOrdersTrackingNumberUpdate', array(
                                'order' => $order,
                                'customer' => $customer,
                                'carrier' => $carrier,
                            ), null, false, true, false, $order->id_shop);
                        }
                    }
                    else
                    {
                        $customer = new Customer((int) $order->id_customer);
                        $templateVars = array(
                            '{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
                            '{firstname}' => $customer->firstname,
                            '{lastname}' => $customer->lastname,
                            '{id_order}' => $order->id,
                            '{shipping_number}' => $order->shipping_number,
                            '{order_name}' => $order->getUniqReference()
                        );
                        if (@Mail::Send((int)$order->id_lang, 'in_transit', Mail::l('Package in transit', (int)$order->id_lang), $templateVars,
                            $customer->email, $customer->firstname.' '.$customer->lastname, null, null, null, null,
                            _PS_MAIL_DIR_, true, (int)$order->id_shop)) {
                            Hook::exec('actionAdminOrdersTrackingNumberUpdate', array('order' => $order, 'customer' => $customer, 'carrier' => $carrier), null, false, true, false, $order->id_shop);
                        }
                    }
                }
                die(
                    json_encode(
                        array(
                            'success' => $this->module->l('Updated successfully','orders'),
                            'shipping_tracking_number' => $carrier->url ? $this->module->displayText($orderCarrier->tracking_number,'a','','',str_replace('@', $order->shipping_number, $carrier->url)) : $orderCarrier->tracking_number,
                            'id_order_carrier' => $orderCarrier->id,
                        )
                    )
                );
            }
            else
                $error = $this->module->l('An error occurred while saving the order carrier','orders');
        }
        if($error)
        {
            die(
                json_encode(
                    array(
                        'error' => $error,
                    )
                )
            );
        }
    }
 }