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

class Cart extends CartCore
{
    public function getPackageShippingCost($id_carrier = null, $use_tax = true, Country $default_country = null, $product_list = null, $id_zone = null,bool $keepOrderPrices=false,$default=false)
    {
        if($IDzone = (int)Hook::exec('actionGetIDZoneByAddressID'))
        {
            $id_zone = $IDzone;
        }
        if (null === $product_list) {
            $products = $this->getProducts(false, false, null, true);
        } else {
            $products = $product_list;
        }
        $marketplace = Module::getInstanceByName('ets_marketplace');
        if($sellers = $marketplace->checkMultiSellerProductList($products))
        {
            $shipping_cost = 0;
            foreach($sellers as $seller)
            {
                $is_virtual= true;
                if($seller)
                {
                    foreach($seller as $p)
                    {
                        if(!$p['is_virtual'])
                        {
                            $is_virtual = false;
                        }
                    }
                }
                if(($price=parent::getPackageShippingCost($id_carrier,$use_tax,$default_country,$seller,$id_zone,$keepOrderPrices))===false && !$is_virtual)
                    return false;
                $shipping_cost += $is_virtual ? 0 : ( $price? :0);
            }
            if($default || !Module::isEnabled('ets_shippingcost'))
                return $shipping_cost;
            if(Module::isEnabled('ets_shippingcost'))
                Module::getInstanceByName('ets_shippingcost')->getPackageShippingCost($id_carrier,$use_tax,$shipping_cost);
            return $shipping_cost;
        }
        else {
            $shipping_cost = parent::getPackageShippingCost($id_carrier,$use_tax,$default_country,$products,$id_zone,$keepOrderPrices);
            if($default || !Module::isEnabled('ets_shippingcost'))
                return $shipping_cost;
            if(Module::isEnabled('ets_shippingcost'))
                Module::getInstanceByName('ets_shippingcost')->getPackageShippingCost($id_carrier,$use_tax,$shipping_cost);
            return $shipping_cost;
        }
    }
    public function getPackageList($flush = false)
    {
        if(($address_type =  Tools::getValue('address_type')) && $address_type=='shipping_address')
            $this->id_address_delivery = (int)Tools::getValue('id_address',$this->id_address_delivery);
        $final_package_list = parent::getPackageList($flush);
        if($final_package_list)
        {
            foreach($final_package_list as $final_packages)
            {
                foreach($final_packages as $final_package)
                {
                    if(!$final_package['carrier_list'] || ($final_package['carrier_list'] && isset($final_package['carrier_list'][0]) && $final_package['carrier_list'][0]===0))
                    {  
                        return array();
                    }
                }
                
            }
        }
        return $final_package_list;
    }
    public function getDeliveryOptionList(Country $default_country = null, $flush = false)
    {
        $products = $this->getProducts(false, false, null, true);
        $marketplace = Module::getInstanceByName('ets_marketplace');
        if($marketplace->checkMultiSellerProductList($products) && $marketplace->is17 && Configuration::get('ETS_MP_ENABLE_MULTI_SHIPPING'))
        {
            $delivery_option_list = $marketplace->getDeliveryOptionList($default_country,$flush);
            return $delivery_option_list;
        }
        else
        {
            $delivery_option_list = parent::getDeliveryOptionList($default_country,$flush);
        
            return $marketplace->changeDeliveryOptionList($delivery_option_list);
        }
    }
}