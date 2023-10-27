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
class AdminMarketPlaceAjaxController extends ModuleAdminController
{
    public function __construct()
    {
       parent::__construct();
    }
    public function init()
    {
        parent::init();
        $context = Context::getContext();
        if(Tools::isSubmit('getSellerProductByAdmin') && ($query = (string)Tools::getValue('q')) && Validate::isCleanHtml($query))
        {
            $sellers = Ets_mp_seller::getSellerByQuery($query);
            die(
                json_encode(
                    array(
                        'sellers' => $sellers,
                    )
                )
            );
        }
        if(Tools::isSubmit('submitAddSellerProduct') && ($id_product = (int)Tools::getValue('id_product')) && ($id_customer =(int)Tools::getValue('id_customer')))
        {
            if(Ets_mp_product::addProductSeller($id_product,$id_customer))
            {
                die(
                    json_encode(
                        array(
                            'success' =>$this->l('Updated successfully'),
                        )
                    )
                );
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' =>$this->l('Product is not valid'),
                        )
                    )
                );
            }
        }
        if(Tools::isSubmit('submitDeleteSellerProduct') && ($id_product = (int)Tools::getValue('id_product')))
        {
            if(Ets_mp_product::deleteProductSeller($id_product))
            {
                die(
                    json_encode(
                        array(
                            'success' =>$this->l('Deleted successfully'),
                        )
                    )
                );
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' =>$this->l('Product is not valid'),
                        )
                    )
                );
            }
        }
    }
}