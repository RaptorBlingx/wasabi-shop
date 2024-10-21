<?php

if (!defined('_PS_VERSION_'))
	exit;
/**
 * @property \Ets_mp_seller $seller
 * @property \Ets_marketplace $module
 */
class Ets_MarketPlaceProductsModuleFrontControllerOverride extends Ets_MarketPlaceProductsModuleFrontController
{
    private \Product $product;
    public static $productID;

    public function __construct()
    {
        parent::__construct();
        $this->seller = $this->module->_getSeller(true);
        if($this->seller)
        {
            if(($id_product = Tools::getValue('id_product')) && Validate::isUnsignedId($id_product) && !Tools::isSubmit('ets_mp_submit_mp_front_products'))
            {
                if(($seller_product = $this->seller->checkHasProduct($id_product)) && isset($seller_product['id_product']) && ($id_product = $seller_product['id_product']))
                {
                    $this->product = new Product($id_product);
                }    
                else
                    die($this->module->l('You do not have permission to access this product','products'));
                $this->product->loadStockData();
            }
            else
            {
                $this->product = new Product();
            }

        }
    }

    public function initContent()
    {
        $seller = $this->context->customer;
        $this->context->smarty->assign(
            array(
                'dataspaceHubAddress' => rtrim($seller->wls_url, '/'),
                'participantDataspaceId' => $seller->wls_id,
                'hubURL' => Configuration::get('WASABI_DATASPACE_HUB_URL'),
            )
        );
        return parent::initContent();
    }

    public function renderProductForm()
    {
        $is_skill = $this->product->getType() == Product::PTYPE_VIRTUAL && ! empty($this->product->skill);
        $this->context->smarty->assign([
            'is_skill' => $is_skill,
            'product_skill' => $this->product->skill
        ]);
        $tpl = _PS_THEME_DIR_ . 'module/'.$this->module->name.'views/templates/custom/extra_tab.tpl';
        if (file_exists($tpl)) {
            $this->context->smarty->assign('extra_tab', $this->display($tpl));
        }
        return parent::renderProductForm();
    }

    public function setMedia()
    {
        parent::setMedia();
        Media::addJsDef([
            'product_is_skill' => $this->product->getType() == Product::PTYPE_VIRTUAL && ! empty($this->product->skill)
        ]);
    }

    public function _validateFormSubmit()
    {
        $submit = Tools::isSubmit('submitSaveProduct');
        $error = false;
        if($submit)
        {
            $product_type = Tools::getValue('product_type');
            if ($product_type == 3) {
                $errorMessage = $this->translator->trans('The %s field is not valid', [
                    '%s' => 'Skill'
                ], 'Admin.Notifications.Error');
                $skill = Tools::getValue('product_skill');
                $result = Hook::exec('actionSkillUpdate', ['skill' => $skill]);
                $isValid = $result == '';
                if (! $isValid) {
                    $error = (is_string($result) && ! empty($result)) ? $result : $errorMessage;
                }
            }
            if($error)
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($error),
                        )
                    )
                );
            }
        }

        return parent::_validateFormSubmit();
        
    }

    public function _submitSaveProduct()
    {
        $isAdd = ! Validate::isLoadedObject($this->product);
        parent::_submitSaveProduct();
        if ($isAdd && Tools::getValue('product_type') == 3 && self::$productID) {
            $this->product = new Product(self::$productID);
            Ets_mp_product::updateStatus(
                id_product: self::$productID,
                active: false,
                admin: true
            );
            $this->product->active = false;
            $this->product->update();
        }
    }
}