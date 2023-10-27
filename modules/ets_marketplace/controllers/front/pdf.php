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
 * Class Ets_MarketPlacePdfModuleFrontController
 * @property \Ets_mp_seller $seller;
 * @property \Ets_marketplace $module;
 */
class Ets_MarketPlacePdfModuleFrontController extends ModuleFrontController
{
    public $seller;
    public function __construct()
	{
		parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
	}
    public function postProcess()
    {
        parent::postProcess();
        if(!$this->context->customer->isLogged() || !($this->seller = $this->module->_getSeller(true)) )
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->module->_checkPermissionPage($this->seller,'orders'))
            die($this->module->l('You do not have permission','pdf'));
        $submitAction = Tools::getValue('submitAction');
        if($submitAction=='generateInvoicePDF')
            $this->processGenerateInvoicePDF();
        if($submitAction=='generateDeliverySlipPDF')
            $this->processGenerateDeliverySlipPDF();
            
    }
    public function generateInvoicePDFByIdOrder($id_order)
    {
        $order = new Order((int) $id_order);
        if (!Validate::isLoadedObject($order)) {
            die($this->module->l('The order cannot be found within your database.','pdf'));
        }
        if(!$this->checkOrderValidateSeller($order->id))
            die($this->module->l('The invoice was not found.','pdf'));
        $order_invoice_list = $order->getInvoicesCollection();
        $this->generatePDF($order_invoice_list, PDF::TEMPLATE_INVOICE);
    }
    public function processGenerateInvoicePDF()
    {
        $id_order = Tools::getValue('id_order');
        $id_order_invoice = Tools::getValue('id_order_invoice');
        if ($id_order && Validate::isUnsignedId($id_order)) {
            $this->generateInvoicePDFByIdOrder($id_order);
        } elseif ($id_order_invoice && Validate::isUnsignedId($id_order_invoice)) {
            $this->generateInvoicePDFByIdOrderInvoice($id_order_invoice);
        } else {
            die($this->module->l('The order ID -- or the order invoice ID -- is missing.','pdf'));
        }
    }
    public function generateInvoicePDFByIdOrderInvoice($id_order_invoice)
    {
        $order_invoice = new OrderInvoice((int) $id_order_invoice);
        if (!Validate::isLoadedObject($order_invoice)) {
            die($this->module->l('The order invoice cannot be found within your database.','pdf'));
        }
        if(!$this->checkOrderValidateSeller($order_invoice->id_order))
            die($this->module->l('The invoice was not found.','pdf'));
        $this->generatePDF($order_invoice, PDF::TEMPLATE_INVOICE);
    }

    public function generatePDF($object, $template)
    {
        $pdf = new PDF($object, $template, Context::getContext()->smarty);
        $pdf->render();
        die();
    }
    public function processGenerateDeliverySlipPDF()
    {
        $id_delivery = Tools::getValue('id_delivery');
        $id_order= Tools::getValue('id_order');
        $id_order_invoice = Tools::getValue('id_order_invoice');
        if ($id_order && Validate::isUnsignedId($id_order)) {
            $this->generateDeliverySlipPDFByIdOrder((int) $id_order);
        } elseif ($id_order_invoice && Validate::isUnsignedId($id_order_invoice)) {
            $this->generateDeliverySlipPDFByIdOrderInvoice((int) $id_order_invoice);
        } elseif ($id_delivery && Validate::isUnsignedId($id_delivery)) {
            $order = Order::getByDelivery((int)$id_delivery);
            $this->generateDeliverySlipPDFByIdOrder((int) $order->id);
        } else {
            die($this->module->l('The order ID -- or the order invoice ID -- is missing.','pdf'));
        }
    }
    public function generateDeliverySlipPDFByIdOrder($id_order)
    {
        $order = new Order((int) $id_order);
        if (!Validate::isLoadedObject($order)) {
            die($this->module->l('Can\'t load order object','pdf'));
        }
        if(!$this->checkOrderValidateSeller($order->id))
            die($this->module->l('The invoice was not found.','pdf'));
        $order_invoice_collection = $order->getInvoicesCollection();
        $this->generatePDF($order_invoice_collection, PDF::TEMPLATE_DELIVERY_SLIP);
    }

    public function generateDeliverySlipPDFByIdOrderInvoice($id_order_invoice)
    {
        $order_invoice = new OrderInvoice((int) $id_order_invoice);
        if(!$this->checkOrderValidateSeller($order_invoice->id_order))
            die($this->module->l('The invoice was not found.','pdf'));
        if (!Validate::isLoadedObject($order_invoice)) {
            die($this->module->l('Can\'t load order invoice object','pdf'));
        }
        if(!$this->checkOrderValidateSeller($order_invoice->id_order))
            die($this->module->l('The invoice was not found.','pdf'));
        $this->generatePDF($order_invoice, PDF::TEMPLATE_DELIVERY_SLIP);
    }
    public function checkOrderValidateSeller($id_order)
    {
        if($this->seller->id_customer == Ets_mp_seller::getIDCustomerSellerByIDOrder($id_order))
            return true;
        else
            return false;
    }
}