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

if(!defined('_PS_VERSION_'))
	exit;
class HTMLTemplateBillingPdf extends HTMLTemplate
{
	public $billing;

	public function __construct($object, $smarty)
	{
		$this->billing = $object;
		$this->smarty = $smarty;
		// header informations
		$this->title = $this->billing->getBillingNumberInvoice();
        $this->date = Tools::displayDate($this->billing->date_add);
		// footer informations
		$this->shop = new Shop(Context::getContext()->shop->id);
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
        $tax = $this->billing->amount_tax- $this->billing->amount;
        $this->billing->amount_tax = Tools::displayPrice($this->billing->amount_tax,new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));
		$this->smarty->assign(array(
			'billing_model' => $this->billing,
            'PS_SHOP_NAME' => Configuration::get('PS_SHOP_NAME'),
            'PS_SHOP_ADDR1' => Configuration::get('PS_SHOP_ADDR1'),
            'PS_SHOP_ADDR2' => Configuration::get('PS_SHOP_ADDR2'),
            'PS_SHOP_CODE' => Configuration::get('PS_SHOP_CODE'),
            'PS_SHOP_CITY' => Configuration::get('PS_SHOP_CITY'),
            'PS_SHOP_COUNTRY' => Configuration::get('PS_SHOP_COUNTRY_ID') ? (new Country(Configuration::get('PS_SHOP_COUNTRY_ID'),Context::getContext()->language->id))->name:'',
            'PS_SHOP_STATE' => Configuration::get('PS_SHOP_STATE_ID') ? (new State(Configuration::get('PS_SHOP_STATE_ID'),Context::getContext()->language->id))->name :'',
            'PS_SHOP_PHONE' => Configuration::get('PS_SHOP_PHONE'),
            'seller' => Ets_mp_seller::_getSellerByIdCustomer($this->billing->id_customer,Context::getContext()->language->id),
            'tax' => $tax ? Tools::displayPrice($tax,new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))):'',
		));
		return $this->smarty->fetch(_PS_MODULE_DIR_ . 'ets_marketplace/views/templates/hook/pdf/billing_content.tpl');
	}
    public function getHeader()
    {
        $this->smarty->assign(
            array(
                'header' => HTMLTemplateBillingPdf::l('Billing invoice'),                                
            )
        );
        return parent::getHeader();
    }

	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename()
	{
		return $this->billing->getBillingNumberInvoice().'.pdf';
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return $this->billing->getBillingNumberInvoice().'.pdf';
	}
}