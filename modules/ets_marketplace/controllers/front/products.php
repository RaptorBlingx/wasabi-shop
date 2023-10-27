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
 *@property \Ets_mp_seller $seller
 * @property \Ets_marketplace $module
 */
class Ets_MarketPlaceProductsModuleFrontController extends ModuleFrontController
{
    private $product_tabs=array();
    private $product;
    private $mpProduct =null;
    private $list_field_approve = array();
    public $errors = array();
    private $seller;
    private $combinations;
    private $seller_product_information =array();
    private $seller_product_types = array();
    public function __construct()
	{
		parent::__construct();  
        $this->display_column_right=false;
        $this->display_column_left =false;
        $this->seller_product_information = explode(',',Configuration::get('ETS_MP_SELLER_ALLOWED_INFORMATION_SUBMISSION'));
        $this->seller_product_types = explode(',',Configuration::get('ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT'));
        $this->product_tabs = array(
            array(
                'tab' => 'BaseSettings',
                'name' => $this->module->l('Base settings','products'),
            ),
            array(
                'tab'=> 'Quantities',
                'name' => $this->module->l('Quantities','products'),
            ),
            array(
                'tab'=>'Combinations',
                'name' => $this->module->l('Combinations','products'),
            ),
            array(
                'tab'=>'Shipping',
                'name' => $this->module->l('Shipping','products'),
            )
        );
        $this->product_tabs[] = array(
            'tab'=>'Price',
            'name' => $this->module->l('Pricing','products'),
        );
        if(in_array('seo',$this->seller_product_information))
            $this->product_tabs[] = array(
                'tab'=>'Seo',
                'name' => $this->module->l('SEO','products'),
            );
        $this->product_tabs[] = array(
            'tab'=>'Options',
            'name' => $this->module->l('Options','products'),
        );
        $this->seller = $this->module->_getSeller(true);
        if($this->seller)
        {
            if(($id_product = Tools::getValue('id_product')) && Validate::isUnsignedId($id_product) && !Tools::isSubmit('ets_mp_submit_mp_front_products'))
            {
                if(($seller_product = $this->seller->checkHasProduct($id_product)) && isset($seller_product['id_product']) && ($id_product = $seller_product['id_product']))
                {
                    $this->product = new Product($id_product);
                    $this->mpProduct = Ets_mp_product::getMpProductByIdProduct($id_product);
                    if(Configuration::get('ETS_MP_SELLER_PRODUCT_APPROVE_REQUIRED') && Configuration::get('ETS_MP_EDIT_PRODUCT_APPROVE_REQUIRED') && $seller_product['approved']==1 && ($fields = Configuration::get('ETS_MP_FIELD_PRODUCT_APPROVE_REQUIRED')))
                    {
                        $this->list_field_approve = explode(',',$fields);
                    }
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
        else
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
	}
    public function postProcess()
    {
        parent::postProcess();
        if(!$this->context->customer->isLogged() || !$this->seller)
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->module->_checkPermissionPage($this->seller))
            die($this->module->l('You do not have permission to access this page','products'));
        $this->_validateFormSubmit();
        if(Tools::isSubmit('submitProductAttachment'))
        {
            $this->_submitProductAttachment();
        }
        if(Tools::isSubmit('submitAddRemoveAttachment'))
            $this->_submitAddRemoveAttachment();
        if(Tools::isSubmit('duplicatemp_front_products')&& ($id_product = Tools::getValue('id_product')) && Validate::isUnsignedId($id_product))
        {
            $product = new Product($id_product);
            if(!Validate::isLoadedObject($product) || !$this->seller->checkHasProduct($id_product))
                $this->errors[] = $this->module->l('Product is not valid','products');
            elseif($id_new = Ets_mp_defines::getInstance()->processDuplicate($id_product,$this->errors,$this->seller))
                Tools::redirect($this->context->link->getModuleLink($this->module->name,'products',array('editmp_front_products'=>1,'id_product'=>$id_new)));
        }
        $bulk_action = Tools::getValue('bulk_action');
        $id_products = Tools::getValue('bulk_action_selected_products');
        if($bulk_action && $id_products && Ets_marketplace::validateArray($id_products,'isInt'))
        {
            $this->_submitBulkActionProduct($id_products);
        }
        $action = Tools::getValue('action');
        if($action=='updateImageOrdering' && ($images=Tools::getValue('images')) && Ets_marketplace::validateArray($images,'isInt'))
        {
            $this->updateImageOrdering($images);
        }
        if(Tools::isSubmit('export'))
            $this->_processExportProduct();
        if(Tools::isSubmit('downloadfilesample'))
            $this->_processExportProduct(true);
        if(Tools::isSubmit('submitDeleteSpecificPrice') && ($id_specific_price = Tools::getValue('id_specific_price')) && Validate::isUnsignedId($id_specific_price))
        {
            $this->submitDeleteSpecificPrice($id_specific_price);
        }
        if(Tools::isSubmit('submitSavePecificPrice'))
        {
            if($this->_checkValidateSpecificPrice())
            {
                $this->_submitSavePecificPrice();
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($this->errors),
                        )
                    )
                );
            }
        }
        if(Tools::isSubmit('submitDeleteProductAttribute') && ($id_product_attribute = Tools::getValue('id_product_attribute')) && Validate::isUnsignedId($id_product_attribute))
        {
            $this->_submitDeleteProductAttribute($id_product_attribute);
        }
        if(Tools::isSubmit('submitDeletecombinations') && ($attributes= Tools::getValue('list_product_attributes')) && Ets_marketplace::validateArray($attributes))
        {
            $this->submitDeletecombinations($attributes);
            
        }
        if(Tools::isSubmit('submitSavecombinations') && ($attributes= Tools::getValue('list_product_attributes')) && Ets_marketplace::validateArray($attributes))
        {
            $this->_submitSavecombinations($attributes);
        }
        if(Tools::isSubmit('submitSaveProduct'))
        {
            $this->_processSaveProduct();
        }
        if(Tools::isSubmit('submitCreateCombination'))
        {
            $this->_submitCreateCombination();
        }
        if(Tools::isSubmit('deletefileproduct') && $this->product->id)
        {
            if($id_product_download = ProductDownload::getIdFromIdProduct($this->product->id))
            {
                $obj = new ProductDownload($id_product_download);
                $obj->delete(true);
            }
            die(
                json_encode(
                    array(
                        'success' => $this->module->l('Deleted successfully','products'),
                    )
                )
            );
        }
        if(Tools::isSubmit('downloadfileproduct') && $this->product->id)
        {
            $this->downloadfileproduct();
        }
        if(Tools::isSubmit('getFromImageProduct') && ($id_image=Tools::getValue('id_image')) && Validate::isUnsignedId($id_image))
        {
            die(
                json_encode(
                    array(
                        'form_image' => $this->_getFromImageProduct($id_image),
                    )
                )
            );
        }
        if(Tools::isSubmit('submitImageProduct') && ($id_image= Tools::getValue('id_image')) && Validate::isUnsignedId($id_image))
        {
            $this->_submitSaveImageProduct($id_image);
        }
        if(Tools::isSubmit('deleteImageProduct') && ($id_image=Tools::getValue('id_image')) && Validate::isUnsignedId($id_image))
        {
            $this->_submitdeleteImageProduct($id_image);
        }
        if(Tools::isSubmit('submitUploadImageSave'))
        {
            $this->submitUploadImageSave();
        }
        if(Tools::isSubmit('getPriceInclTax'))
        {
            $this->getPriceInclTax();
        }
        if(Tools::isSubmit('getPriceExclTax'))
        {
            $this->getPriceExclTax();
        }
        if(Tools::isSubmit('getFormSpecificPrice'))
        {
            $this->getFormSpecificPrice();
        }
        if(Tools::getValue('del')=='yes' && ($id_product = Tools::getValue('id_product')) && Validate::isUnsignedId($id_product) && $this->seller->checkHasProduct($id_product))
        {
            $this->_submitDeleteProduct();
        }
        if(Tools::isSubmit('change_enabled') && ($id_product = Tools::getValue('id_product')) && Validate::isUnsignedId($id_product) && $this->seller->checkHasProduct($id_product))
        {
            $this->_submitChangeEnabled($id_product);
        }
        if(Tools::isSubmit('refreshProductSupplierCombinationForm') && ($id_supplier = Tools::getValue('id_supplier')) && Validate::isUnsignedId($id_supplier) )
        {
            die(
                json_encode(
                    array(
                        'html_form' => $this->refreshProductSupplierCombinationForm($id_supplier),
                    )
                )
            );
        }
    }
    public function submitDeletecombinations($attributes)
    {
        if(Ets_mp_product::deleteCombinations($this->product->id,$attributes))
        {
            die(
                json_encode(
                    array(
                        'success' => $this->module->l('Deleted successfully','products'),
                        'list_combinations' => $this->displayListCombinations(),
                        'html_form_supplier' => $this->renderFormSupplier(),
                    )
                )
            );
        }

    }
    public function _submitDeleteProductAttribute($id_product_attribute)
    {
        $productAttribute = new Combination($id_product_attribute);
        if($this->seller->checkHasProduct($productAttribute->id_product) && $productAttribute->id_product== $this->product->id)
        {
            if($productAttribute->delete())
            {
                die(
                    json_encode(
                        array(
                            'success' => $this->module->l('Deleted successfully','products'),
                            'html_form_supplier' => $this->renderFormSupplier(),
                        )
                    )
                );
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->l('An error occurred while deleting the attribute','products'),
                        )  
                    )
                );
            }    
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->l('You do not have permission','products'),
                    )  
                )
            );
        }
    }
    public function submitDeleteSpecificPrice($id_specific_price)
    {
        $specific_price = new SpecificPrice($id_specific_price);
        if(Validate::isLoadedObject($specific_price) && $specific_price->id_product== $this->product->id)
        {
            if($specific_price->delete())
            {
                die(
                    json_encode(
                        array(
                            'success' => $this->module->l('Deleted successfully','products'),
                        )
                    )
                );
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->l('An error occurred while deleting the specific price','products'),
                        )
                    )
                );
            }
            
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->l('Specific price is not valid','products'),
                    )
                )
            );
        }
    }
    public function _submitBulkActionProduct($id_products)
    {
        $id_product = $id_products[0];
        $errors = array();
        $bulk_action = Tools::getValue('bulk_action');
        if($seller_product = $this->seller->checkHasProduct($id_product))
        {
            switch ($bulk_action) {
              case 'activate_all':
                    if($this->seller->checkVacation() && Tools::strpos($this->seller->vacation_type,'disable_product')!==false)
                    {
                        die(
                            json_encode(
                                array(
                                    'error' => sprintf($this->module->l('You do not have permission to activate this product #%d','products'),$id_product),
                                )
                            )
                        );
                    }
                    $product = new Product($id_product);
                    if(Validate::isLoadedObject($product) &&  $seller_product['approved']==1 && !$product->active)
                    {
                        $product->active=1;
                        if(!$product->update())
                            $errors[] = sprintf($this->module->l('An error occurred while saving the product(#%d)','products'),$id_product);
                    }elseif(!Validate::isLoadedObject($product))
                        $errors[] = sprintf($this->module->l('Product(#%d) is not valid','products'),$id_product);
                    $this->context->cookie->success_message = $this->module->l('Product(s) successfully activated.','products');
                break;
              case 'deactivate_all':
                    if($this->seller->checkVacation() && Tools::strpos($this->seller->vacation_type,'disable_product')!==false)
                    {
                        die(
                            json_encode(
                                array(
                                    'error' => sprintf($this->module->l('You do not have permission to deactivate this product #%d','products'),$id_product),
                                )
                            )
                        );
                    }
                    $product = new Product($id_product);
                    if(Validate::isLoadedObject($product) &&  $product->active)
                    {
                        $product->active=0;
                        if(!$product->update())
                            $errors[] = sprintf($this->module->l('An error occurred while saving the product(#%d)','products'),$id_product);
                    }
                    elseif(!Validate::isLoadedObject($product))
                        $errors[] = sprintf($this->module->l('Product(#%d) is not valid','products'),$id_product);
                    $this->context->cookie->success_message = $this->module->l('Product(s) successfully deactivated.','products');
              break;
              case 'duplicate_all':
                if(Configuration::get('ETS_MP_ALLOW_SELLER_CREATE_PRODUCT'))
                    Ets_mp_defines::getInstance()->processDuplicate($id_product,$errors,$this->seller);
                else
                    $errors[] = $this->module->l('You do not have permission to add a new product','products');
                if($errors)
                {
                    $errors[0] = sprintf($this->module->l('An error occurred while duplicating the product(#%d) : %s','products'),$id_product,$errors[0]);
                }
                $this->context->cookie->success_message = $this->module->l('Product(s) successfully duplicated.','products');
              break;
              case 'delete_all':
                $product = new Product($id_product);
                if(Validate::isLoadedObject($product))
                {
                    if(!$product->delete())
                        $errors[] = sprintf($this->module->l('An error occurred while deleting the product(#%d)','products'),$id_product);
                }
                else
                    $errors[] = sprintf($this->module->l('Product(#%d) is not valid','products'),$id_product);
                $this->context->cookie->success_message = $this->module->l('Product(s) successfully deleted.','products');
              break;
            } 
            
        }
        else
        {
            $errors[] = sprintf($this->module->l('You do not have permission to edit this product #%d','products'),$id_product);
        }
        if($errors)
        {
            $this->context->cookie->success_message='';
            die(
                json_encode(
                    array(
                        'error' => $errors[0],
                    )
                )
            );
        }
        else
        {
            die(
                json_encode(
                    array(
                        'result' => 'ok',
                    )
                )
            );
        }
    }
    public function updateImageOrdering($images)
    {
        foreach($images as $position=>$id_image)
        {
            $image_class = new Image($id_image);
            if(Ets_mp_product::CheckImage($image_class->id,$this->product->id))
            {
                $image_class->position = (int)$position;
                $image_class->update();
            }
            else
            die(
                json_encode(
                    array(
                        'errors' => $this->module->l('You do not permission update this image'),
                    )
                )
            );
        }
        die(
            json_encode(
                array(
                    'success' => $this->module->l('Updated position successfully','products'),
                )
            )
        );
    }
    public function _submitSavecombinations($attributes)
    {
        $data = Tools::getValue('product_combination_bulk');
        if(!Ets_marketplace::validateArray($data))
            $this->errors[] = $this->module->l('Data is not valid');
        else
        {
            if(!isset($data['quantity']) ||  ($data['quantity'] && !Validate::isInt($data['quantity'])))
                $this->errors[] = $this->module->l('Quantity is not valid','products');
            if(!isset($data['cost_price']) || ($data['cost_price'] && !Validate::isPrice($data['cost_price'])))
                $this->errors[] = $this->module->l('Cost price is not valid','products');
            if(!isset($data['impact_on_price_te']) ||  ($data['impact_on_price_te'] && !Validate::isPrice($data['impact_on_price_te'])))
                $this->errors[] = $this->module->l('Impact on price is not valid','products');
            if(!isset($data['impact_on_weight']) || ($data['impact_on_weight'] && !Validate::isFloat($data['impact_on_weight'])))
                $this->errors[] = $this->module->l('Impact on weight is not valid','products');
            if(!isset($data['date_availability']) || ($data['date_availability'] && !Validate::isDate($data['date_availability'])))
                $this->errors[] = $this->module->l('Availability date is not valid','products');
            if(!isset($data['reference']) || ($data['reference'] && !Validate::isReference($data['reference'])))
                $this->errors[] = $this->module->l('Reference is not valid','products');
            if(!isset($data['minimal_quantity']) || ($data['minimal_quantity'] && !Validate::isUnsignedInt($data['minimal_quantity'])))
                $this->errors[] = $this->module->l('Minimum quantity is not valid','products');
            if($this->module->is17)
                if(!isset($data['low_stock_threshold']) || ( $data['low_stock_threshold'] && !Validate::isInt($data['low_stock_threshold'])))
                    $this->errors[] = $this->module->l('Low stock level is not valid','products');
        }
        if(!$this->errors)
        {
            foreach($attributes as  $id_product_attribute)
            {
                $combination = new Combination($id_product_attribute);
                if($combination->id_product==$this->product->id)
                {
                    $combination->quantity = (int)$data['quantity'];
                    $combination->minimal_quantity = (int)$data['minimal_quantity'];
                    $combination->cost_price = (float)$data['cost_price'];
                    $combination->price= (float)$data['impact_on_price_te'];
                    $combination->weight = (float)$data['impact_on_weight'];
                    $combination->available_date = $data['date_availability'];
                    $combination->reference = $data['reference'];
                    if($this->module->is17)
                    {
                        $combination->low_stock_threshold = (int)$data['low_stock_threshold'];
                        $combination->low_stock_alert = isset($data['low_stock_alert'])? (int)$data['low_stock_alert']:0;
                    }    
                    if($combination->update())
                        StockAvailable::setQuantity($this->product->id, (int)$id_product_attribute, $combination->quantity);
                }
            }
            die(
                json_encode(
                    array(
                        'success' => $this->module->l('Updated successfully','products'),
                        'list_combinations' => $this->displayListCombinations(),
                    )
                )
            );
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($this->errors),
                    )
                )
            );
        }
    }
    public function _processSaveProduct()
    {
        $product_type = Tools::getValue('product_type');
        if($this->_checkValidateProduct())
        {
            if($this->_submitSaveProduct())
            {
                $languages = Language::getLanguages(false);
                $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
                $combinations_id_product_attribute = Tools::getValue('combinations_id_product_attribute');
                $show_variations = Tools::getValue('show_variations');
                if($product_type==0 && $show_variations && $combinations_id_product_attribute && Ets_marketplace::validateArray($combinations_id_product_attribute,'isInt'))
                {
                    $combinations_attribute_default = Tools::getValue('combinations_attribute_default') ?: array();
                    $combinations_attribute_quantity = Tools::getValue('combinations_attribute_quantity');
                    $combinations_attribute_available_date = Tools::getValue('combinations_attribute_available_date');
                    $combinations_attribute_minimal_quantity= Tools::getValue('combinations_attribute_minimal_quantity');
                    $combinations_attribute_reference = Tools::getValue('combinations_attribute_reference');
                    $combinations_attribute_location = Tools::getValue('combinations_attribute_location');
                    if($this->module->is17)
                        $combinations_attribute_low_stock_threshold = Tools::getValue('combinations_attribute_low_stock_threshold');
                    else
                        $combinations_attribute_low_stock_threshold = array();
                    $combinations_attribute_wholesale_price= Tools::getValue('combinations_attribute_wholesale_price');
                    $combinations_attribute_price = Tools::getValue('combinations_attribute_price');
                    $combinations_attribute_unity = Tools::getValue('combinations_attribute_unity');
                    $combinations_attribute_weight = Tools::getValue('combinations_attribute_weight');
                    $combinations_attribute_isbn = Tools::getValue('combinations_attribute_isbn',array());
                    $combinations_attribute_ean13= Tools::getValue('combinations_attribute_ean13');
                    $combinations_attribute_upc = Tools::getValue('combinations_attribute_upc');
                    $combination_id_image_attr = Tools::getValue('combination_id_image_attr') ?: array();
                    $combination_attribute_low_stock_alert = Tools::getValue('combination_attribute_low_stock_alert')?:array();
                    if(Ets_marketplace::validateArray($combinations_attribute_low_stock_threshold) && Ets_marketplace::validateArray($combination_id_image_attr) && Ets_marketplace::validateArray($combinations_attribute_upc) && Ets_marketplace::validateArray($combinations_attribute_ean13) && Ets_marketplace::validateArray($combinations_attribute_isbn) && Ets_marketplace::validateArray($combinations_attribute_weight) && Ets_marketplace::validateArray($combinations_attribute_unity) && Ets_marketplace::validateArray($combinations_attribute_price) && Ets_marketplace::validateArray($combinations_attribute_wholesale_price) && Ets_marketplace::validateArray($combination_attribute_low_stock_alert) &&  Ets_marketplace::validateArray($combinations_attribute_default) && Ets_marketplace::validateArray($combinations_attribute_quantity) && Ets_marketplace::validateArray($combinations_attribute_available_date) && Ets_marketplace::validateArray($combinations_attribute_minimal_quantity) && Ets_marketplace::validateArray($combinations_attribute_reference) && Ets_marketplace::validateArray($combinations_attribute_location))
                    {
                        
                        foreach($combinations_id_product_attribute as $id_product_attribute)
                        {
                            $combination = new Combination($id_product_attribute);
                            
                            if(Validate::isLoadedObject($combination) && $combination->id_product == $this->product->id)
                            {
                                if(isset($combinations_attribute_default[$id_product_attribute]))
                                {
                                    $combination->default_on = (int)$combinations_attribute_default[$id_product_attribute];
                                }
                                else
                                    $combination->default_on=0; 
                                if(isset($combinations_attribute_quantity[$id_product_attribute]) && Validate::isUnsignedInt($combinations_attribute_quantity[$id_product_attribute]))
                                    $combination->quantity = (int)$combinations_attribute_quantity[$id_product_attribute];
                                else
                                    $combination->quantity=0;
                                if(isset($combinations_attribute_available_date[$id_product_attribute]) && Validate::isDate($combinations_attribute_available_date[$id_product_attribute]))
                                    $combination->available_date = $combinations_attribute_available_date[$id_product_attribute];
                                else
                                    $combination->available_date ='0000-00-00';
                                if(isset($combinations_attribute_minimal_quantity[$id_product_attribute]) && Validate::isUnsignedInt($combinations_attribute_minimal_quantity[$id_product_attribute]))
                                    $combination->minimal_quantity = (int)$combinations_attribute_minimal_quantity[$id_product_attribute];
                                else
                                    $combination->minimal_quantity=0;
                                if(isset($combinations_attribute_reference[$id_product_attribute]) && Validate::isReference($combinations_attribute_reference[$id_product_attribute]))
                                    $combination->reference = $combinations_attribute_reference[$id_product_attribute];
                                else
                                    $combination->reference='';
                                if(isset($combinations_attribute_location[$id_product_attribute]) && Validate::isGenericName($combinations_attribute_location[$id_product_attribute]))
                                    $combination->location = $combinations_attribute_location[$id_product_attribute];
                                else
                                    $combination->location='';
                                if($this->module->is17)
                                {
                                    if(isset($combinations_attribute_low_stock_threshold[$id_product_attribute]) && Validate::isInt($combinations_attribute_low_stock_threshold[$id_product_attribute]))
                                        $combination->low_stock_threshold = (int)$combinations_attribute_low_stock_threshold[$id_product_attribute];
                                    else
                                        $combination->low_stock_threshold =0;
                                    if(isset($combination_attribute_low_stock_alert[$id_product_attribute]) && Validate::isInt($combination_attribute_low_stock_alert[$id_product_attribute]))
                                        $combination->low_stock_alert = (int)$combination_attribute_low_stock_alert[$id_product_attribute];
                                    else
                                        $combination->low_stock_alert=0;
                                }
                                if(isset($combinations_attribute_wholesale_price[$id_product_attribute]) && Validate::isPrice($combinations_attribute_wholesale_price[$id_product_attribute]))
                                    $combination->wholesale_price = (float)$combinations_attribute_wholesale_price[$id_product_attribute];
                                else
                                    $combination->wholesale_price =0;
                                if(isset($combinations_attribute_price[$id_product_attribute]) && Validate::isNegativePrice($combinations_attribute_price[$id_product_attribute]))
                                    $combination->price = (float)$combinations_attribute_price[$id_product_attribute];
                                else
                                    $combination->price = 0;
                                if(isset($combinations_attribute_unity[$id_product_attribute]) && Validate::isNegativePrice($combinations_attribute_unity[$id_product_attribute]))
                                    $combination->unit_price_impact = $combinations_attribute_unity[$id_product_attribute];
                                else
                                    $combination->unit_price_impact = 0; 
                                if(isset($combinations_attribute_weight[$id_product_attribute]) && Validate::isUnsignedFloat($combinations_attribute_weight[$id_product_attribute]))
                                    $combination->weight = (float)$combinations_attribute_weight[$id_product_attribute]; 
                                else
                                    $combination->weight =0; 
                                if(isset($combination->isbn) && isset($combinations_attribute_isbn[$id_product_attribute]) && Validate::isIsbn($combinations_attribute_isbn[$id_product_attribute]))
                                    $combination->isbn = $combinations_attribute_isbn[$id_product_attribute];
                                else
                                    $combination->isbn=''; 
                                if(isset($combinations_attribute_ean13[$id_product_attribute]) && Validate::isEan13($combinations_attribute_ean13[$id_product_attribute]))
                                    $combination->ean13 = $combinations_attribute_ean13[$id_product_attribute];
                                else
                                    $combination->ean13='';
                                if(isset($combinations_attribute_upc[$id_product_attribute]) && Validate::isUpc($combinations_attribute_upc[$id_product_attribute]))
                                    $combination->upc = $combinations_attribute_upc[$id_product_attribute];
                                else
                                    $combination->upc = '';
                                if($combination->default_on)
                                {
                                    Ets_mp_product::resetAttributeDefault($this->product->id);
                                }
                                if($combination->update())
                                {
                                    StockAvailable::setQuantity($this->product->id, (int)$id_product_attribute, $combination->quantity);
                                    if($this->module->is17)
                                        Ets_mp_product::setLocation($this->product->id,$combination->location,null,$combination->id);
                                    Ets_mp_product::updateImageCombination($id_product_attribute,$combination_id_image_attr);
                                }               
                            }
                        } 
                    }
                }
                else
                {
                    $this->product->deleteProductAttributes();
                }
                $inputPackItems = Tools::getValue('inputPackItems');
                Ets_mp_product::updatePackProduct($this->product->id,$product_type,$inputPackItems);
                if($product_type==2)
                {
                    $virtual = $this->updateDownloadProduct($this->product);
                }
                elseif($id_product_download = ProductDownload::getIdFromIdProduct($this->product->id))
                {
                    $productDownload = new ProductDownload($id_product_download);
                    $productDownload->delete(true);
                }
                $selectedCarriers = Tools::getValue('selectedCarriers');
                Ets_mp_product::updateCarrierProduct($this->product->id,$product_type,$selectedCarriers);
                $id_customization_fields = array();
                $uploadable_files =0;
                $text_fields = 0;
                if(($custom_fields = Tools::getValue('custom_fields')) && Ets_marketplace::validateArray($custom_fields))
                {
                    Configuration::updateValue('PS_CUSTOMIZATION_FEATURE_ACTIVE',1);
                    foreach($custom_fields as $custom_field)
                    {
                        if($id_customization_field = $custom_field['id_customization_field'])
                            $customizationField = new CustomizationField($id_customization_field);
                        else
                        {
                            $customizationField = new CustomizationField();
                            $customizationField->id_product=  $this->product->id;
                        }
                        foreach($languages as $language)
                        {
                            $customizationField->name[$language['id_lang']] = $custom_field['label'][$language['id_lang']] ? : $custom_field['label'][$id_lang_default];
                        }
                        $customizationField->type = (int)$custom_field['type'];
                        if($customizationField->type==1)
                            $text_fields +=1;
                        else
                            $uploadable_files +=1;
                        if(isset($custom_field['required']))
                            $customizationField->required = $custom_field['required'];
                        else    
                            $customizationField->required = 0;
                        if($customizationField->id)
                        {
                            $id_customization_fields[] = $customizationField->id;
                            $customizationField->update();
                        }
                        elseif($customizationField->add())
                            $id_customization_fields[] = $customizationField->id;
                    }
                }
                if(!$this->module->is17 && ($this->product->uploadable_files!=$uploadable_files || $this->product->text_fields!= $text_fields))
                {
                    $this->product->uploadable_files = $uploadable_files;
                    $this->product->text_fields = $text_fields;
                    $this->product->update();
                }
                Ets_mp_product::deleteCustomizationField($this->product->id,$id_customization_fields);
                if($this->seller->auto_enabled_product=='yes')
                    $allow_active=1;
                elseif($this->seller->auto_enabled_product=='no')
                    $allow_active=0;
                elseif(!Configuration::get('ETS_MP_SELLER_PRODUCT_APPROVE_REQUIRED'))
                    $allow_active=1;
                else
                    $allow_active=0;
                $this->_submitProductSupplier();
                $adtoken = Tools::getAdminToken(
                    'AdminProducts'
                    . (int) Tab::getIdFromClassName('AdminProducts')
                    . $this->context->customer->id
                );
                die(
                    json_encode(
                        array(
                            'success' => $this->product->active==0 && !$allow_active ? $this->module->l('Your new product has just been submitted successfully. It is waiting to be approved by Administrator','products'): (($mpproduct =  Ets_mp_product::getMpProductByIdProduct($this->product->id)) && Validate::isLoadedObject($mpproduct) ? $this->module->l('Submitted successfully. It is waiting to be approved by Administrator','products') : $this->module->l('Submitted successfully','products') ),
                            'virtual' => isset($virtual) && $virtual ? array('link_download_file' => $this->context->link->getModuleLink($this->module->name,'products',array('id_product'=>$this->product->id,'downloadfileproduct'=>1)),'link_delete_file' => $this->context->link->getModuleLink($this->module->name,'products',array('id_product'=>$this->product->id,'deletefileproduct'=>1)),):false,
                            'list_combinations' => $this->displayListCombinations(),
                            'id_product'=> $this->product->id,
                            'link_product' => $this->product->active ? $this->context->link->getProductLink($this->product->id) : $this->context->link->getProductLink($this->product->id,null,null,null,null,null,null,false,false,false,array('adtoken'=>$adtoken,'id_employee'=>$this->context->customer->id,'preview'=>1)),
                            'preview_text' => $this->module->l('Preview','products'),
                            'save_text' => $this->module->l('Save','products'),
                            'html_form_supplier' => $this->renderFormSupplier(),                            
                        )
                    )
                );
            }
            
        }
        if($this->errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($this->errors),
                    )
                )
            );
        }
    }
    public function _submitProductSupplier()
    {
        $id_suppliers = Tools::getValue('id_suppliers');
        $product_supplier_reference = Tools::getValue('product_supplier_reference');
        $product_supplier_price_currency = Tools::getValue('product_supplier_price_currency');
        $product_supplier_price = Tools::getValue('product_supplier_price');
        Ets_mp_product::updateSupplierProduct($this->product->id,$id_suppliers,$product_supplier_reference,$product_supplier_price_currency,$product_supplier_price);
    }
    
    public function downloadfileproduct()
    {
        if(($id_product_download = ProductDownload::getIdFromIdProduct($this->product->id)) && ($productDownload = new ProductDownload($id_product_download)) && Validate::isLoadedObject($productDownload))
        {
            $filepath =_PS_DOWNLOAD_DIR_.$productDownload->filename;
            if(file_exists($filepath)){
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.($productDownload->display_filename ? $productDownload->display_filename : basename($filepath) ).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filepath));
                flush(); // Flush system output buffer
                readfile($filepath);
                exit;
            }
            else
            {
                die('Product file does not exist');
            }
        }
    }
    public function _submitSaveImageProduct($id_image)
    {
        $image = new Image($id_image);
        if(Validate::isLoadedObject($image) &&  Ets_mp_product::CheckImage($image->id,$this->product->id))
        {
            $languages = Language::getLanguages(true);
            $errors = array();
            foreach($languages as $language)
            {
                $legend = Tools::getValue('legend_'.$language['id_lang']);
                if(Tools::strlen(strip_tags($legend))<128 && Validate::isCleanHtml(strip_tags($legend)))
                    $image->legend[$language['id_lang']] = strip_tags($legend);
                else
                    $errors[] = $this->module->l('Legend is not valid in','products').' '.$language['iso_code'];
            }
            $image_cover = (int)Tools::getValue('image_cover');
            if($image_cover && $image->id_product == $this->product->id)
            {
                Image::deleteCover($this->product->id);
                $image->cover=1;
            }
            elseif($image_cover)
            {
                $errors[] = $this->module->l('This image has not been accepted as a cover image','products');
            }
            if(!$errors)
            {
                if($image->update())
                {
                    die(
                        json_encode(
                            array(
                                'success' => $this->module->l('Updated image successfully','products'),
                                'id_image' => $image->id,
                                'cover'=> $image_cover ? 1: 0,
                                'list_combinations' => $this->displayListCombinations(),
                            )
                        )
                    );
                }
                else
                {
                    die(
                        json_encode(
                            array(
                                'errors' => $this->module->displayError($this->module->l('An error occurred while updating the image','products')),
                            )
                        )
                    );
                }
            }
            else
                die(
                        json_encode(
                            array(
                                'errors' => $this->module->displayError($errors),
                            )
                        )
                    );
            
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($this->module->l('Image is not valid','products')),
                    )
                )
            );
        }
    }
    public function _submitdeleteImageProduct($id_image)
    {
        $image = new Image($id_image);
        if(Validate::isLoadedObject($image) && Ets_mp_product::CheckImage($image->id,$this->product->id))
        {
            if($image->delete())
            {
                $is_cover =false;
                if($image->cover)
                {
                    $is_cover = true;
                    if($images = Ets_mp_product::getProductImages($this->product->id))
                    {
                        $id_image_new = $images[0]['id_image'];
                        $newImage = new Image($id_image_new);
                        $newImage->cover=1;
                        $newImage->update();
                    }
                }
                die(
                    json_encode(
                        array(
                            'success' => $this->module->l('Deleted image successfully','products'),
                            'id_image' => $image->id,
                            'is_cover' => $is_cover,
                            'id_new_image' => isset($id_image_new) ? $id_image_new :'',
                            'list_combinations' => $this->displayListCombinations(),
                            
                        )
                    )
                );
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->l('An error occurred while deleting the image','products'),
                        )
                    )
                );
            }
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->l('Image is not valid','products'),
                    )
                )
            );
        }
    }
    public function submitUploadImageSave()
    {
        if($this->product->id)
        {
            $this->_submitUploadImageSave($this->product->id,'upload_image');
            if($this->errors)
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($this->errors),
                        )
                    )
                );
            }
        }
        else{
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($this->module->l('Product is null','products')),
                    )
                )
            );   
        }
    }
    public function getPriceInclTax()
    {
        $id_tax_group = (int)Tools::getValue('id_tax_group');
        $tax = $this->module->getTaxValue($id_tax_group);
        $price = (float)Tools::getValue('price');
        die(
            json_encode(
                array(
                    'price' => $price && $tax>0 ? Tools::ps_round($price + ($price*$tax),6):$price
                )
            )
        );
    }
    public function getPriceExclTax()
    {
        $id_tax_group = (int)Tools::getValue('id_tax_group');
        $tax = $this->module->getTaxValue($id_tax_group);
        $price = (float)Tools::getValue('price');
        die(
            json_encode(
                array(
                    'price' => $price && $tax>0 ? Tools::ps_round($price/(1+$tax),6):$price
                )
            )
        );
    }
    public function getFormSpecificPrice()
    {
        if(($id_specific_price = (int)Tools::getValue('id_specific_price')))
        {
            $specific_price = new SpecificPrice((int)$id_specific_price);
            if(!Validate::isUnsignedId($id_specific_price) || !Validate::isLoadedObject($specific_price) || $specific_price->id_product != $this->product->id)
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->l('Specific price is not valid','products'),
                        )
                    )
                );
            }                
        }
        die(
            json_encode(
                array(
                    'form_html' => $this->renderSpecificPrice((int)$id_specific_price),
                )
            )
        );
    }
    public function _submitDeleteProduct()
    {
        if(!$this->seller->checkDeleteProduct())
            die($this->module->l('You do not have permission','products'));
        if($this->product->delete())
        {
            $this->context->cookie->success_message = $this->module->l('Deleted successfully','products');
            Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'products',array('list'=>1)));
        }
    }
    public function _submitChangeEnabled($id_product)
    {
        $change_enabled = (int)Tools::getValue('change_enabled');
        if((int)$change_enabled && $this->seller->checkVacation() && Tools::strpos($this->seller->vacation_type,'disable_product')!==false)
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->l('You do not have permission to enable this product','products'),
                    )
                )  
            );
        }
        $field = Tools::getValue('field');
        if(Validate::isCleanHtml($field))
        {
            if($field=='active')
            {
                if(!Ets_mp_product::checkSellerUpdateStatusProduct($id_product))
                {
                    die(
                        json_encode(
                            array(
                                'errors' => $this->module->l('You do not have permission to enable this product','products'),
                            )
                        )  
                    );
                }
                else
                {
                    $product = new Product($id_product);
                    $product->active = (int)$change_enabled;
                    $product->update();
                    if($change_enabled)
                    {
                        die(
                            json_encode(
                                array(
                                    'href' => $this->context->link->getModuleLink($this->module->name,'products',array('id_product'=> $id_product,'change_enabled'=>0,'field'=>'active')),
                                    'title' => $this->module->l('Click to disable','products'),
                                    'success' => $this->module->l('Updated successfully','products'),
                                    'enabled' => 1,
                                )
                            )  
                        );
                    }
                    else
                    {
                        die(
                            json_encode(
                                array(
                                    'href' => $this->context->link->getModuleLink($this->module->name,'products',array('id_product'=> $id_product,'change_enabled'=>1,'field'=>'active')),
                                    'title' => $this->module->l('Click to enable','products'),
                                    'success' => $this->module->l('Updated successfully','products'),
                                    'enabled' => 0,
                                )
                            )  
                        );
                    }
                }
            }
            if($field=='available_for_order')
            {
                if((int)$change_enabled && $this->seller->checkVacation() && Tools::strpos($this->seller->vacation_type,'disable_shopping')!==false)
                {
                    die(
                        json_encode(
                            array(
                                'errors' => $this->module->l('You do not have permission to change the disabled shopping status','products'),
                            )
                        )  
                    );
                }
                if($change_enabled)
                {
                    $product = new Product($id_product);
                    $product->available_for_order = (int)$change_enabled;
                    $product->update();
                    if($change_enabled)
                    {
                        die(
                            json_encode(
                                array(
                                    'success' => $this->module->l('Updated successfully','products'),
                                    'delete_button'=>true,
                                )
                            )  
                        );
                    }
                }
                
            }
        }
        
    }
    public function initContent()
	{
		parent::initContent();
        $id_product = (int)Tools::getValue('id_product');
        $number_product_upload = $this->seller->getNumberProductUpload();
        $total_products = (int) $this->seller->getProducts('',0,0,'',true);
        if(Tools::isSubmit('addnew') || (Tools::isSubmit('editmp_front_products') && $id_product && Validate::isUnsignedId($id_product)))
        {
            $ok = true;
            if(Tools::isSubmit('addnew'))
            {
                if(!Configuration::get('ETS_MP_ALLOW_SELLER_CREATE_PRODUCT'))
                {
                    $html_content = $this->module->displayWarning($this->module->l('You do not have permission to create new product','products'));
                    $ok = false;
                }
                elseif($number_product_upload && $number_product_upload <=$total_products)
                {
                    $html_content = $this->module->displayWarning(sprintf($this->module->l('You do not have permission to add a new product. You are allowed to add %d products maximum','products'),$number_product_upload));
                    $ok = false;
                }
                else
                    $html_content = '';
            }    
            else
            {
                if(!Configuration::get('ETS_MP_ALLOW_SELLER_EDIT_PRODUCT') || !$this->seller->checkHasProduct($id_product))
                {
                    $html_content = $this->module->displayWarning($this->module->l('You do not have permission to edit product','products'));
                    $ok = false;
                }
                else
                    $html_content = '';
            }
            if($ok)
                $html_content .= ($this->context->cookie->success_message ? $this->module->displayConfirmation($this->context->cookie->success_message):'').$this->renderProductForm();
            $this->context->cookie->success_message='';
        }
        else
        {
            $fields_list = array(
                'input_box' => array(
                    'title' => '',
                    'width' => 40,
                    'type' => 'text',
                    'strip_tag'=> false,
                ),
                'id_product' => array(
                    'title' => $this->module->l('ID','products'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                ),
                'image' => array(
                    'title' => $this->module->l('Image','products'),
                    'type'=>'text',
                    'sort' => false,
                    'filter' => false,
                    'strip_tag'=> false,
                ),
                'name' => array(
                    'title' => $this->module->l('Name','products'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag'=>false,
                ),
                'stock_quantity' => array(
                    'title' => $this->module->l('Quantity','products'),
                    'type' => 'int',
                    'sort' => true,
                    'filter' => true
                ),
                'default_category' => array(
                    'title' => $this->module->l('Default category','products'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag'=>false,
                ),
                'product_price' => array(
                    'title' => $this->module->l('Price','products'),
                    'type' => 'int',
                    'sort' => true,
                    'filter' => true
                ),
                'active' => array(
                    'title' => $this->module->l('Status','products'),
                    'type' => 'active',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'active',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'active' => -2,
                                'title' => $this->module->l('Declined','products')
                            ),
                            1 => array(
                                'active' => -1,
                                'title' => $this->module->l('Pending','products')
                            ),
                            2 => array(
                                'active' => 0,
                                'title' => $this->module->l('Disabled','products')
                            ),
                            3 => array(
                                'active' => 1,
                                'title' => $this->module->l('Enabled','products')
                            )
                        )
                    )
                ),
            );
            //Filter
            $show_resset = false;
            $filter = "";
            if($id_product && !Tools::isSubmit('del') && !Tools::isSubmit('duplicatemp_front_products'))
            {
                if(Validate::isUnsignedId($id_product))
                    $filter .= ' AND p.id_product="'.(int)$id_product.'"';
                $show_resset = true;
            }
            if(($name = Tools::getValue('name')) || $name!='')
            {
                if(Validate::isCatalogName($name))
                    $filter .=' AND IF( mpl.`name`!="" ,mpl.`name`,  pl.`name`) LIKE "%'.pSQL($name).'%"';
                $show_resset = true;
            }
            if(($stock_quantity_min = trim(Tools::getValue('stock_quantity_min'))) || $stock_quantity_min!='') 
            {
                if(Validate::isInt($stock_quantity_min))
                    $filter .=' AND stock.quantity >= "'.(int)$stock_quantity_min.'"';
                $show_resset = true;
            }
            if(($stock_quantity_max = trim(Tools::getValue('stock_quantity_max'))) || $stock_quantity_max!='')
            {
                if(Validate::isInt($stock_quantity_max))
                    $filter .=' AND stock.quantity <= "'.(int)$stock_quantity_max.'"';
                $show_resset = true;
            }
            if(($default_category = trim(Tools::getValue('default_category'))) || $default_category!='')
            {
                if(Validate::isCatalogName($default_category))
                    $filter .=' AND cl.name LIKE "%'.pSQL($default_category).'%"';
                $show_resset = true;
            }
            if(($price_min = trim(Tools::getValue('product_price_min'))) || $price_min!='')
            {
                if(Validate::isPrice(($price_min)))
                    $filter .= ' AND IF( mp.`price`!=0 ,mp.`price`,  p.`price`) >= "'.(float)Tools::convertPrice($price_min,null,false).'"';
                $show_resset = true;
            }
            if(($price_max =trim(Tools::getValue('product_price_max'))) || $price_max!='' )
            {
                if(Validate::isPrice($price_max))
                    $filter .= ' AND IF( mp.`price`!=0 ,mp.`price`,  p.`price`) <= "'.(float)Tools::convertPrice($price_max,null,false).'"';
                $show_resset = true;
            }
            if(($active= trim(Tools::getValue('active'))) || $active!='')
            {
                if($active==1)
                    $filter .= ' AND product_shop.active="1"';
                elseif($active==0)
                    $filter .= ' AND product_shop.active="0" AND sp.approved="1"';
                elseif($active==-2)
                    $filter .= ' AND product_shop.active="0" AND sp.approved="-2"';
                elseif($active==-1)
                    $filter .= ' AND product_shop.active="0" AND sp.approved="0"';
                $show_resset=true;
            }
            //Sort
            $sort = "";
            $sort_value = Tools::getValue('sort','id_product');
            $sort_type=Tools::getValue('sort_type','desc');
            if($sort_value)
            {
                switch ($sort_value) {
                    case 'id_product':
                        $sort .='p.id_product';
                        break;
                    case 'name':
                        $sort .='name';
                        break;
                    case 'stock_quantity':
                        $sort .= 'stock.quantity';
                        break;
                    case 'default_category':
                        $sort .= 'pl.name';
                        break;
                    case 'shop_name':
                        $sort .= 'r.shop_name';
                        break;
                    case 'product_price':
                        $sort .= 'product_price';
                        break;
                    case 'active':
                        $sort .='p.active';
                        break;
                }
                if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                    $sort .= ' '.trim($sort_type);  
            }
            //Paggination
            $page = (int)Tools::getValue('page');
            if($page<=0)
                $page = 1;
            $totalRecords = (int) $this->seller->getProducts($filter,0,0,'',true);
            $paggination = new Ets_mp_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url =$this->context->link->getModuleLink($this->module->name,'products',array('list'=>true, 'page'=>'_page_')).$this->module->getFilterParams($fields_list,'mp_front_products');
            $paggination->limit = (int)Tools::getValue('paginator_product_select_limit',10);
            $paggination->num_links =5;
            $paggination->name ='product';
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $products = $this->seller->getProducts($filter,$page,$paggination->limit,$sort,false,false,false,false);
            if($products)
            {
                if(version_compare(_PS_VERSION_, '1.7', '>='))
                    $type_image= ImageType::getFormattedName('home');
                else
                    $type_image= ImageType::getFormatedName('home');
                foreach($products as &$product)
                {
                    $adtoken = Tools::getAdminToken(
                        'AdminProducts'
                        . (int) Tab::getIdFromClassName('AdminProducts')
                        . $this->context->customer->id
                    );
                    $product['product_price'] = Tools::displayPrice($product['product_price'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));
                    $product['child_view_url'] = $product['active'] ? $this->context->link->getProductLink($product['id_product']) :  $this->context->link->getProductLink($product['id_product'],null,null,null,null,null,null,false,false,false,array('adtoken'=>$adtoken,'id_employee'=>$this->context->customer->id,'preview'=>1));
                    if(!$product['id_image'])
                        $product['id_image'] = Ets_mp_product::getImageByIDProduct($product['id_product']);
                    if($product['id_image'])
                    {
                        
                        $product['image'] = Module::getInstanceByName('ets_marketplace')->displayText(Module::getInstanceByName('ets_marketplace')->displayText('','img','width_80','','','',$this->context->link->getImageLink($product['link_rewrite'],$product['id_image'],$type_image)),'a','','',$product['child_view_url'],'_blank');
                    }
                    else
                        $product['image']='';
                    $product['name'] = Module::getInstanceByName('ets_marketplace')->displayText($product['name'],'a','','',$product['child_view_url']);
                    if($product['id_category_default'])
                        $product['default_category'] = Module::getInstanceByName('ets_marketplace')->displayText($product['default_category'],'a','','',$this->context->link->getCategoryLink($product['id_category_default']),'_blank');
                    if($product['approved']==-2)
                        $product['active']=-2;
                    if(!$product['active'] && !$product['approved'])
                        $product['active']=-1;
                    $product['input_box'] = Module::getInstanceByName('ets_marketplace')->displayText('','input','','bulk_action_selected_products-'.$product['id_product'],'','','','bulk_action_selected_products[]',$product['id_product'],'checkbox');
                }
            }
            $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','products');
            $paggination->style_links = $this->module->l('links','products');
            $paggination->style_results = $this->module->l('results','products');
            $actions = array('view');
            if(Configuration::get('ETS_MP_ALLOW_SELLER_EDIT_PRODUCT'))
                $actions[] = 'edit';
            if(Configuration::get('ETS_MP_ALLOW_SELLER_CREATE_PRODUCT') && (!$number_product_upload || $total_products<$number_product_upload))
                $actions[] = 'duplicate';
            if($this->seller->checkDeleteProduct())
                $actions[] = 'delete';
            $listData = array(
                'name' => 'mp_front_products',
                'actions' => $actions,
                'currentIndex' => $this->context->link->getModuleLink($this->module->name,'products',array('list'=>1)).($paggination->limit!=10 ? '&paginator_product_select_limit='.$paggination->limit:''),
                'postIndex' => $this->context->link->getModuleLink($this->module->name,'products',array('list'=>1)),
                'identifier' => 'id_product',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => $this->module->l('Products','products'),
                'fields_list' => $fields_list,
                'field_values' => $products,
                'paggination' => $paggination->render(),
                'filter_params' => $this->module->getFilterParams($fields_list,'mp_front_products'),
                'show_reset' =>$show_resset,
                'totalRecords' => $totalRecords,
                'sort'=> $sort_value,
                'show_add_new'=> (!$number_product_upload || $number_product_upload > $total_products) && Configuration::get('ETS_MP_ALLOW_SELLER_CREATE_PRODUCT') ? true:false,
                'view_new_tab' => true,
                'link_new' => !$number_product_upload || $number_product_upload > $total_products ? $this->context->link->getModuleLink($this->module->name,'products',array('addnew'=>1)):false,
                'link_export' => Configuration::get('ETS_MP_SELLER_ALLOWED_IMPORT_EXPORT_PRODUCTS')? $this->context->link->getModuleLink($this->module->name,'products',array('export'=>1)): false,
                'link_import' =>(!$number_product_upload || $number_product_upload >$total_products) && Configuration::get('ETS_MP_SELLER_ALLOWED_IMPORT_EXPORT_PRODUCTS') && Configuration::get('ETS_MP_ALLOW_SELLER_CREATE_PRODUCT') ? $this->context->link->getModuleLink($this->module->name,'import'): false,
                'sort_type' => $sort_type,
            );          
            $html_content = ($this->context->cookie->success_message ? $this->module->displayConfirmation($this->context->cookie->success_message):'').$this->_renderFormBulkProduct().($this->seller->checkVacation() && Tools::strpos($this->seller->vacation_type,'disable_product')!==false  ? $this->module->displayWarning($this->module->l('Your shop is in vacation mode. All your products have been disabled and cannot be enabled until your shop is back to online','products')):'').$this->module->renderList($listData);
            $this->context->cookie->success_message ='';
        }
        $this->context->smarty->assign(
            array(
                'html_content' => $html_content,
                'path' => $this->module->getBreadCrumb(),
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false, 
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/products.tpl');      
        else        
            $this->setTemplate('products_16.tpl'); 
    }
    public function renderProductForm()
    {
        $fields_changed = array();
        if($this->mpProduct && Validate::isLoadedObject($this->mpProduct) && $this->mpProduct->filed_change)
        {
            $fields_changed = explode(',',$this->mpProduct->filed_change);
        }
        $valueFieldPost= array();
        $valueFieldPost['product_type'] = $this->product->getType();
        $valueFieldPost['show_variations'] = $valueFieldPost['product_type']==0 && Product::getProductAttributesIds($this->product->id) && $this->product->id ? 1 :0;
        foreach(Language::getLanguages(true) as $language)
        {
            if($fields_changed && in_array('name',$fields_changed))
                $valueFieldPost['name'][$language['id_lang']] = isset( $this->mpProduct->name) ? $this->mpProduct->name[$language['id_lang']]:'';
            else
                $valueFieldPost['name'][$language['id_lang']] = isset($this->product->name[$language['id_lang']]) ? $this->product->name[$language['id_lang']] :'';
            if($fields_changed && in_array('description',$fields_changed))
                $valueFieldPost['description'][$language['id_lang']] = isset($this->mpProduct->description[$language['id_lang']]) ? $this->mpProduct->description[$language['id_lang']]:'';
            else
                $valueFieldPost['description'][$language['id_lang']] = isset($this->product->description[$language['id_lang']]) ? $this->product->description[$language['id_lang']] :'';
            if($fields_changed && in_array('description_short',$fields_changed))
                $valueFieldPost['description_short'][$language['id_lang']] = isset($this->mpProduct->description_short[$language['id_lang']]) ? $this->mpProduct->description_short[$language['id_lang']] :'';
            else
                $valueFieldPost['description_short'][$language['id_lang']] = isset($this->product->description_short[$language['id_lang']]) ? $this->product->description_short[$language['id_lang']]:'';
            if($fields_changed && in_array('meta_title',$fields_changed))
                $valueFieldPost['meta_title'][$language['id_lang']] = isset($this->mpProduct->meta_title[$language['id_lang']]);
            else
                $valueFieldPost['meta_title'][$language['id_lang']] = isset($this->product->meta_title[$language['id_lang']]) ? $this->product->meta_title[$language['id_lang']]:'';
            $valueFieldPost['tags'][$language['id_lang']] = Ets_mp_product::getListtags($this->product->id,$language['id_lang']);
            if($fields_changed && in_array('meta_keywords',$fields_changed))
                $valueFieldPost['meta_keywords'][$language['id_lang']] = isset($this->mpProduct->meta_keywords[$language['id_lang']]) ? $this->mpProduct->meta_keywords[$language['id_lang']] :'';
            else
                $valueFieldPost['meta_keywords'][$language['id_lang']] = isset($this->product->meta_keywords[$language['id_lang']]) ? $this->product->meta_keywords[$language['id_lang']]:'';
            if($fields_changed && in_array('meta_description',$fields_changed))
                $valueFieldPost['meta_description'][$language['id_lang']] = isset($this->mpProduct->meta_description[$language['id_lang']]) ? $this->mpProduct->meta_description[$language['id_lang']] :'';
            else
                $valueFieldPost['meta_description'][$language['id_lang']] = isset($this->product->meta_description[$language['id_lang']]) ? $this->product->meta_description[$language['id_lang']] :'';
            if($fields_changed && in_array('meta_description',$fields_changed))
                $valueFieldPost['link_rewrite'][$language['id_lang']] = isset($this->mpProduct->link_rewrite[$language['id_lang']]) ? $this->mpProduct->link_rewrite[$language['id_lang']] :'';
            else
                $valueFieldPost['link_rewrite'][$language['id_lang']] = isset($this->product->link_rewrite[$language['id_lang']]) ? $this->product->link_rewrite[$language['id_lang']] :'';
            $valueFieldPost['redirect_type'] = Tools::getValue('redirect_type',$this->product->redirect_type);
            if(isset($this->product->delivery_in_stock))
            {
                if($fields_changed  && in_array('delivery_in_stock',$fields_changed))
                    $valueFieldPost['delivery_in_stock'][$language['id_lang']] = isset($this->mpProduct->delivery_in_stock[$language['id_lang']]) ? $this->mpProduct->delivery_in_stock[$language['id_lang']] :'';
                else
                    $valueFieldPost['delivery_in_stock'][$language['id_lang']] = isset($this->product->delivery_in_stock[$language['id_lang']]) ? $this->product->delivery_in_stock[$language['id_lang']] :'';
                
            }
            else
                $valueFieldPost['delivery_in_stock'][$language['id_lang']] = Tools::getValue('delivery_in_stock_'.$language['id_lang']);
            if(isset($this->product->delivery_out_stock))                            
            {
                if($fields_changed && in_array('delivery_out_stock',$fields_changed))
                {
                    $valueFieldPost['delivery_out_stock'][$language['id_lang']] = isset($this->mpProduct->delivery_out_stock[$language['id_lang']]) ? $this->mpProduct->delivery_out_stock[$language['id_lang']] :'';
                }
                else
                    $valueFieldPost['delivery_out_stock'][$language['id_lang']] = isset($this->product->delivery_out_stock[$language['id_lang']]) ? $this->product->delivery_out_stock[$language['id_lang']] :'';
            }
            else
                $valueFieldPost['delivery_out_stock'][$language['id_lang']] = Tools::getValue('delivery_out_stock_'.(int)$language['id_lang']);
                                     
        }
        if($this->product->id)
        {
            if($fields_changed && in_array('price',$fields_changed))
            {
                $valueFieldPost['id_tax_rules_group'] = $this->mpProduct->id_tax_rules_group;
                $valueFieldPost['price_excl'] = $this->mpProduct->price;
                $valueFieldPost['unit_price'] = $this->mpProduct->unit_price;
                $valueFieldPost['wholesale_price'] = $this->mpProduct->wholesale_price;
                $valueFieldPost['price_incl'] = Tools::ps_round($this->mpProduct->price +$this->mpProduct->price*$this->module->getTaxValue($this->mpProduct->id_tax_rules_group),6);
            }
            else
            {
                $valueFieldPost['id_tax_rules_group'] = $this->product->id_tax_rules_group;
                $valueFieldPost['price_excl'] = $this->product->price;
                $valueFieldPost['price_incl'] = Tools::ps_round($this->product->price +$this->product->price*$this->module->getTaxValue($this->product->id_tax_rules_group),6);
                $valueFieldPost['unit_price'] = $this->product->unit_price;
                $valueFieldPost['wholesale_price'] = $this->product->wholesale_price;
            }
        }
        else
        {
            $valueFieldPost['price_excl'] ='';
            $valueFieldPost['price_incl'] ='';
            $valueFieldPost['unit_price'] = '';
            $valueFieldPost['wholesale_price'] = '';
            $valueFieldPost['id_tax_rules_group'] = Product::getIdTaxRulesGroupMostUsed();
        }
        if($fields_changed && in_array('reference',$fields_changed))
            $valueFieldPost['reference'] = $this->mpProduct->reference;
        else
            $valueFieldPost['reference'] = $this->product->reference;
        $valueFieldPost['quantity'] = $this->product->id ? $this->product->getQuantity($this->product->id):999;
        $valueFieldPost['active'] = $this->product->active;
        $valueFieldPost['id_manufacturer'] = $this->product->id_manufacturer;
        $valueFieldPost['condition'] = $this->product->condition;
        $valueFieldPost['available_for_order'] = $this->product->available_for_order;
        $valueFieldPost['show_price'] = $this->product->show_price;
        if(isset($this->product->show_condition))
            $valueFieldPost['show_condition'] = $this->product->show_condition;
        if(isset($this->product->isbn))
        {
            if($fields_changed && in_array('isbn',$fields_changed))
                $valueFieldPost['isbn'] = $this->mpProduct->isbn;
            else
                $valueFieldPost['isbn'] = $this->product->isbn;
        }
        if($fields_changed && in_array('ean13',$fields_changed))
            $valueFieldPost['ean13'] = $this->mpProduct->ean13;
        else
            $valueFieldPost['ean13'] = $this->product->ean13;
        if($fields_changed && in_array('upc',$fields_changed))
            $valueFieldPost['upc'] = $this->mpProduct->upc;
        else
            $valueFieldPost['upc'] = $this->product->upc;
        
        if($fields_changed && in_array('width',$fields_changed))
            $valueFieldPost['width'] = $this->mpProduct->width;
        else
            $valueFieldPost['width'] = $this->product->width;
        if($fields_changed && in_array('height',$fields_changed))
            $valueFieldPost['height'] = $this->mpProduct->height;
        else
            $valueFieldPost['height'] = $this->product->height;
        if($fields_changed && in_array('depth',$fields_changed))
            $valueFieldPost['depth'] = $this->mpProduct->depth;
        else
            $valueFieldPost['depth'] = $this->product->depth;
        if($fields_changed && in_array('weight',$fields_changed))
            $valueFieldPost['weight'] = $this->mpProduct->weight;
        else
            $valueFieldPost['weight'] = $this->product->weight;
        $valueFieldPost['customizationFields'] = Ets_mp_product::getCustomizationFields($this->product->id);
        $valueFieldPost['attachments'] = $this->seller->getProductAttachments($this->product->id);
        $default_currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $this->context->smarty->assign(
            array(
                'languages' => Language::getLanguages(true),
                'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
                'valueFieldPost' => $valueFieldPost,
                'default_currency' => $default_currency ,
                'seller_product_information' => $this->seller_product_information,
                'seller_product_types' => $this->seller_product_types,
                'url_path' => $this->module->getBaseLink().'/modules/'.$this->module->name.'/',
                'ets_mp_url_search_product' => $this->context->link->getModuleLink($this->module->name,'ajax',array('ajaxSearchProduct'=>1)),
                'ets_mp_url_search_customer' => $this->context->link->getModuleLink($this->module->name,'ajax',array('ajaxSearchCustomer'=>1)),
                'ets_mp_url_search_related_product' => $this->context->link->getModuleLink($this->module->name,'ajax',array('ajaxSearchProduct'=>1,'disableCombination'=>1)),
            )
        );
        if($this->product_tabs)
        {
            foreach($this->product_tabs as $key=> &$tab)
            {
                if (method_exists($this, 'renderForm' . $tab['tab'])) {
                    $tab['content_html'] = $this->{'renderForm' . $tab['tab']}();
                }
                else
                    unset($this->product_tabs[$key]);
            }
        }
        $tax_rule_groups = TaxRulesGroup::getTaxRulesGroupsForOptions();
        if($tax_rule_groups)
        {
            foreach($tax_rule_groups as &$tax_rule_group)
            {
                $tax_rule_group['value_tax'] = $this->module->getTaxValue($tax_rule_group['id_tax_rules_group']);
            }
        }
        $current_tab = Tools::getValue('current_tab','BaseSettings');
        if(!in_array($current_tab,array('BaseSettings','Quantities','Combinations','Shipping','Price','Seo','Options')))
            $current_tab ='BaseSettings';
        $this->context->smarty->assign(
            array(
                'product_tabs' => $this->product_tabs,
                'tax_rule_groups' => $tax_rule_groups,
                'current_tab' => $current_tab,
                'customerID' => $this->context->customer->id,
                'mpProduct' => $this->mpProduct,
                'adtoken' => Tools::getAdminToken(
                    'AdminProducts'
                    . (int) Tab::getIdFromClassName('AdminProducts')
                    . $this->context->customer->id
                ),
            )
        );
        return $this->module->displayTpl('product/form.tpl');
    }
    public function renderFormBaseSettings()
    {
        $selected_categories = Product::getProductCategories($this->product->id);
        $disabled_categories = array();
        if($seller_categories = $this->seller->getApplicableProductCategories())
        {
            $seller_not_categories = $this->seller->getNoApplicableProductCategories();
            if($seller_not_categories)
            {
                foreach($seller_not_categories as $category)
                    $disabled_categories[] = $category['id_category'];
            }
        }
        else
        {
            $roots = Category::getRootCategories();
            if($roots)
            {
                foreach($roots as $root)
                    $disabled_categories[] = $root['id_category'];
            }
        }
        $currency_default = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $manufacturers = $this->seller->getManufacturers(' AND m.active=1',false,false,false);
        $show_variations = Product::getProductAttributesIds($this->product->id) ? 1 :0;
        $fields = array(
            array(
                'type' => 'custom_form',
                'form_group_class'=> 'ets_mp_form_pack_product',
                'html_form' => $this->renderFormPackProduct(),
            ),
            array(
                'type' => 'custom_form',
                'form_group_class'=> '',
                'html_form' => $this->renderFormImageProduct(),
            ),
            array(
                'type' => 'radio',
                'name' => 'show_variations',
                'label' => $this->module->l('Combinations','products'),
                'form_group_class' => 'ets_mp_show_variations'.(!$show_variations && (!in_array('standard_product',$this->seller_product_types) || !$this->module->_use_attribute) ?' hide':''),
                'values' => array(
                    array(
                        'id'=>0,
                        'name' => $this->module->l('Simple product','products'),
                    ),
                    array(
                        'id'=>1,
                        'name' => $this->module->l('Product with combinations','products'),
                    ),
                )
            )
            
        );
        if(in_array('short_description',$this->seller_product_information))
            $fields[]= array(
                'type' => 'textarea',
                'name' => 'description_short',
                'label' => $this->module->l('Summary','products'),
                'autoload_rte' => true,
                'lang'=> true,
                'placeholder' => $this->module->l('Fill in a striking short description of the product (displayed on product page and product list as abstract for customers and search engines). For detailed informations use the description tab.','products'),
                'max_text' => Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') ?: 800,
                'small_text' => $this->module->l('characters allowed','products'),
        );
        $fields[] = array(
                'type' => 'textarea',
                'name' => 'description',
                'label' => $this->module->l('Description','products'),
                'autoload_rte' => true,
                'lang'=> true,
                'max_text' => 21844,
                'small_text' => $this->module->l('characters allowed','products'),
            );
        if(in_array('product_reference',$this->seller_product_information))
            $fields[] = array(
                'type' => 'text',
                'name' => 'reference',
                'label' => $this->module->l('Reference','products'),
            );
        $fields[] = array(
                'type' =>'input_group',
                'label' =>$this->module->l('Price','products'),
                'required' => true,
                'inputs' => array(
                    array(
                        'type'=> 'text',
                        'name'=>'price_excl',
                        'label'=> $this->module->l('Price (tax excl.) ','products'),
                        'col' => 'col-lg-6',
                        'suffix' => $currency_default->sign,
                    ),
                    array(
                        'type'=> 'text',
                        'name'=>'price_incl',
                        'label'=> $this->module->l('Price (tax incl.)','products'),
                        'col' => 'col-lg-6',
                        'suffix' => $currency_default->sign,
                    ),
                    array(
                        'type'=> 'select',
                        'name'=>'id_tax_rules_group',
                        'label'=> $this->module->l('Tax rule','products'),
                        'col' => 'col-lg-12',
                        'values' => array(
                            'query' => TaxRulesGroup::getTaxRulesGroupsForOptions(),
                            'id'=> 'id_tax_rules_group',
                            'name' => 'name',
                        ),
                    )
                ),
        );
        $fields[] = array(
                'type' => 'categories',
                'name'=>'id_categories',
                'required' => true,
                'label' => $this->module->l('Categories','products'),
                'categories_tree'=> $this->module->displayProductCategoryTre(Ets_mp_defines::getCategoriesTree(),$selected_categories,'',$disabled_categories,$this->product->id_category_default),
            );
        if($manufacturers)
            $fields[] = array(
                'type' => 'select',
                'name' => 'id_manufacturer',
                'label' => $this->module->l('Brand','products'),
                'values' => array_merge(array(array('id'=>'','name'=> $this->module->l('--'))), $manufacturers),
            );
        if($this->module->_use_feature && ($product_features = $this->module->displayProductFeatures($this->product->id)))
        {
            $fields[] = array(
                'type' => 'product_features',
                'name'=> 'features',
                'label' => $this->module->l('Features','products'),
                'list_features' => $product_features,
            );
        }
        $fields[] = array(
                'type' => 'custom_form',
                'form_group_class'=> 'ets_mp_form_related_product',
                'html_form' => $this->renderFormRelatedProduct(),
        );
        if(!$this->seller->checkVacation() || Tools::strpos($this->seller->vacation_type,'disable_product')===false)
        {
            if($this->seller->auto_enabled_product=='yes')
                $allow_active=1;
            elseif($this->seller->auto_enabled_product=='no')
                $allow_active=0;
            elseif(!Configuration::get('ETS_MP_SELLER_PRODUCT_APPROVE_REQUIRED'))
                $allow_active=1;
            else
                $allow_active=0;
            if($allow_active || (($productSeller = Ets_mp_seller::getProductSellerByIdProduct($this->product->id)) && $productSeller['approved']) )
            {
                $fields[] = array(
                    'type' => 'switch',
                    'name'=> 'active',
                    'label' => $this->module->l('Enabled','products'),
                );
            }
        }
        $this->context->smarty->assign(
            array(
                'fields' => $fields,
                'product_class' => $this->product,
            )
        );
        return $this->module->displayTpl('form.tpl').(Tools::isSubmit('addnew') ?  $this->module->displayConfirmation($this->module->l('Photo uploading form will be displayed when the new product is successfully added','products')):'');
    }
    public function renderFormQuantities()
    {
        $this->context->smarty->assign(
            array(
                'product_class' => $this->product,
                'link_download_file' => $this->context->link->getModuleLink($this->module->name,'products',array('id_product'=>$this->product->id,'downloadfileproduct'=>1)),
                'link_delete_file' => $this->context->link->getModuleLink($this->module->name,'products',array('id_product'=>$this->product->id,'deletefileproduct'=>1)),
                'productDownload' => ($id_product_download = ProductDownload::getIdFromIdProduct($this->product->id)) && ($productDownload = new ProductDownload($id_product_download)) && Validate::isLoadedObject($productDownload) ? $productDownload :false ,
            )
        );
        return $this->module->displayTpl('product/quantities.tpl');
    }
    
    public function renderFormCombinations()
    {
        $product_types = Configuration::get('ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT') ? explode(',',Configuration::get('ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT')):array();
        if($this->module->_use_attribute && in_array('standard_product',$product_types))
        {
            $attributeGroups = $this->seller->getAttributeGroups('',false,false);
            if($attributeGroups)
            {
                foreach($attributeGroups as &$attributeGroup)
                {
                    $attributeGroup['attributes'] = AttributeGroup::getAttributes($this->context->language->id,$attributeGroup['id_attribute_group']);
                    if($attributeGroup['is_color_group'] && $attributeGroup['attributes'])
                    {
                        foreach($attributeGroup['attributes'] as &$attribute)
                        {
                            if(file_exists(_PS_COL_IMG_DIR_.$attribute['id_attribute'].'.jpg'))
                                $attribute['image']=$this->module->getBaseLink().'/img/co/'.$attribute['id_attribute'].'.jpg';
                        }
                    }
                }
            }
        }
        else
            $attributeGroups = array();
        $this->context->smarty->assign(
            array(
                'attributeGroups'=>$attributeGroups,
            )
        );
        $this->context->smarty->assign(
            'list_product_attributes', $this->displayListCombinations()
        );
        return $this->module->displayTpl('product/combinations.tpl');
    }
    public function renderFormPackProduct()
    {
        $pack_products = Ets_mp_product::getPackProducts($this->product->id);
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $type_image= ImageType::getFormattedName('home');
        else
            $type_image= ImageType::getFormatedName('home');
        if($pack_products)
        {
            foreach($pack_products as &$pack_product)
            {
                $id_image =0;
                if($pack_product['id_product_attribute_item'])
                {
                    $pack_product['attribute_name'] = Ets_mp_product::getProductAttributeName($pack_product['id_product_attribute_item']);
                    $id_image = ($image = Product::getCombinationImageById($pack_product['id_product_attribute_item'],$this->context->language->id)) ? $image['id_image']: 0;
                }
                else
                    $pack_product['attribute_name']='';
                if(!$id_image)
                    $id_image = Ets_mp_product::getImageByIDProduct($pack_product['id_product_item'],1);
                if(!$id_image)
                    $id_image = Ets_mp_product::getImageByIDProduct($pack_product['id_product_item']);
                $pack_product['url_image'] = str_replace('http://', Tools::getShopProtocol(), Context::getContext()->link->getImageLink($pack_product['link_rewrite'], $id_image, $type_image));
            }
        }
        $this->context->smarty->assign(
            array(
                'pack_products' => $pack_products,
            )
        );
        return $this->module->displayTpl('product/pack.tpl');
    }
    public function renderFormRelatedProduct()
    {

        $related_products = $this->product->getAccessories($this->context->language->id);
        if($related_products)
        {
            if(version_compare(_PS_VERSION_, '1.7', '>='))
                $type_image= ImageType::getFormattedName('home');
            else
                $type_image= ImageType::getFormatedName('home');
            foreach($related_products as &$related_product)
            {
                if($related_product['id_image'])
                    $related_product['img'] = $this->context->link->getImageLink($related_product['link_rewrite'], $related_product['id_image'], $type_image);
            }
        }
        $this->context->smarty->assign(
            array(
                'related_products' => $related_products,
            )
        );
        return $this->module->displayTpl('product/related.tpl');
    }
    public function renderFormImageProduct()
    {
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $type_image= ImageType::getFormattedName('home');
        else
            $type_image= ImageType::getFormatedName('home');
        $images = Ets_mp_product::getListImages($this->product->id);
        if($images)
        {
            foreach($images as &$image)
            {
                $image['link'] = str_replace('http://', Tools::getShopProtocol(), $this->context->link->getImageLink($this->product->link_rewrite[$this->context->language->id], $image['id_image'], $type_image));
            }
        }
        $this->context->smarty->assign(
            array(
                'product_class' => $this->product,
                'images' => $images
            )
        );
        return $this->module->displayTpl('product/images.tpl');
    }
    public function displayListCombinations()
    {
        if($this->product->id)
        {
            $productAttributes =  Ets_mp_product::getProductAttributes($this->product);
            $product_images = Ets_mp_product::getListImages($this->product->id);
            if($product_images)
            {
                if(version_compare(_PS_VERSION_, '1.7', '>='))
                    $type_image= ImageType::getFormattedName('small');
                else
                    $type_image= ImageType::getFormatedName('small');
                foreach($product_images as &$image)
                {
                    $image['link'] = $this->context->link->getImageLink($this->product->link_rewrite[$this->context->language->id],$image['id_image'],$type_image);
                }
            }
        }
        else
        {
            $product_images =array();
            $productAttributes = array();
        }
        $this->context->smarty->assign(
            array(
                'product_images' => $product_images,
                'default_currency' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT')),
                'product_class' => $this->product,
                'productAttributes' => $productAttributes,
                'is17' => $this->module->is17,
            )
        );
        return $this->module->displayTpl('product/list_combinations.tpl');
    }
    public function renderFormShipping()
    {
        $selected_carriers = array();
        $product_carriers = $this->product->getCarriers();
        if($product_carriers)
        {
            foreach($product_carriers as $product_carrier)
            {
                $selected_carriers[]= $product_carrier['id_reference'];
            }
        }
        if($carriers = $this->seller->getListCarriersUser())
        {
            foreach($carriers as &$carrier)
            {
                if(!$carrier['name'])
                    $carrier['name'] = Configuration::get('PS_SHOP_NAME');
            }
        }
        $this->context->smarty->assign(
            array(
                'carriers' => $carriers,
                'selected_carriers' => $selected_carriers,
            )
        );
        return $this->module->displayTpl('product/shipping.tpl');
    }
    public function renderFormPrice()
    {
        if($specific_prices = Ets_mp_product::getSpecificPrices($this->product->id))
        {
            foreach($specific_prices as &$specific_price)
            {
                if($specific_price['id_product_attribute'])
                {
                    $specific_price['attribute_name'] = Ets_mp_product::getProductAttributeName($specific_price['id_product_attribute']);
                    
                }
                if($specific_price['price']>=0)
                {
                    $specific_price['price_text'] = Tools::displayPrice($specific_price['price'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));
                }
                else
                    $specific_price['price_text'] ='--';
                if($specific_price['reduction_type']=='amount')
                {
                    $specific_price['reduction'] = Tools::displayPrice($specific_price['reduction'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))).($specific_price['reduction_tax'] ? ' ('.$this->module->l('Tax incl.','products').')':' ('.$this->module->l('Tax excl.','products').')');
                }
                else
                    $specific_price['reduction'] = Tools::ps_round($specific_price['reduction']*100,2).'%';
            }
        }
        $this->context->smarty->assign(
            array(
                'specific_prices' => $specific_prices,
                'specific_prices_from'=> $this->renderSpecificPrice(),
                'tax_rules_groups' =>  TaxRulesGroup::getTaxRulesGroupsForOptions(),
                'currency_default' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT')),
            )
        );
        return $this->module->displayTpl('product/price.tpl');
    }
    public function renderSpecificPrice($id_specific_price=0)
    {
        $currencies = Currency::getCurrencies(false,true);
        $countries = Country::getCountries($this->context->language->id,true);
        $groups = Group::getGroups($this->context->language->id,true);
        $productAttributes = Ets_mp_product::getProductAttributes($this->product);
        $specific_price= new SpecificPrice($id_specific_price);
        $this->context->smarty->assign(
            array(
                'currencies' => $currencies,
                'countries' => $countries,
                'groups' => $groups,
                'productAttributes' => $productAttributes,
                'default_currency' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT')),
                'specific_price' => $specific_price,
                'specific_price_customer' => new Customer($specific_price->id_customer),
            )
        );
        return $this->module->displayTpl('product/specific_price.tpl');
    }
    public function renderFormSeo()
    {
        return $this->module->displayTpl('product/seo.tpl');  
    }
    public function renderFormOptions()
    {
        $this->context->smarty->assign(
            array(
                '_is17' => $this->module->is17,
                'product_class' => $this->product,
                'seller' => $this->seller,
                'html_form_supplier' => $this->renderFormSupplier(),
            )
        );
        return $this->module->displayTpl('product/options.tpl');
    }
    public function renderFormSupplier()
    {
        $suppliers = $this->seller->getSuppliers(' AND s.active=1','',false,false);
        if($suppliers)
        {
            foreach($suppliers as &$supplier)
            {
                $supplier['checked'] = Ets_mp_product::getSupplierProduct($this->product->id,$supplier['id_supplier']) ? true:false;
                if($supplier['checked'])
                {
                    
                    $supplier['product_suppliers'] =$this->refreshProductSupplierCombinationForm($supplier['id_supplier']);
                }
                else
                    $supplier['product_suppliers'] = '';
            }
            $currencies = Currency::getCurrencies(false,true);
            if($currencies)
            {
                foreach($currencies as &$currency)
                {
                    if(!isset($currency['symbol']))
                    {
                        if(isset($currency['sign']))
                            $currency['symbol'] = $currency['sign'];
                        else
                        {
                            $currency['symbol'] = (new Currency($currency['id_currency']))->sign;
                        }
                            
                    }
               } 
            }
            $this->context->smarty->assign(
                array(
                    'suppliers' => $suppliers,
                    'id_supplier_default' => $this->product->id_supplier,
                    'currencies' => $currencies,
                    'currency_default' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))
                )
            );
            return $this->module->displayTpl('product/supplier.tpl');
        }
    }
    public function refreshProductSupplierCombinationForm($id_supplier)
    {
        $product_suppliers = Ets_mp_product::getInformationSupplier($this->product->id,$id_supplier);
        $currencies = Currency::getCurrencies(false,true);
        if($currencies)
        {
            foreach($currencies as &$currency)
            {
                if(!isset($currency['symbol']))
                {
                    if(isset($currency['sign']))
                        $currency['symbol'] = $currency['sign'];
                    else
                    {
                        $currency['symbol'] = (new Currency($currency['id_currency']))->sign;
                    }

                }
            }
        }
        $this->context->smarty->assign(
            array(
                'product_suppliers' => $product_suppliers,
                'supplier_class' => new Supplier($id_supplier),
                'currencies' => $currencies,
                'currency_default' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))
                
            )
        );
        return $this->module->displayTpl('product/product_supplier_combination_form.tpl');
    }
    public function _checkValidateSpecificPrice()
    {
        if(!$this->product->id)
            $this->errors[] = $this->module->l('Product is null','products');
        elseif(($id_specific_price= (int)Tools::getValue('id_specific_price')) && ($specific_price = new SpecificPrice((int)$id_specific_price)) && (!Validate::isLoadedObject($specific_price) || $specific_price->id_product!= $this->product->id))
        {
            $this->errors[] = $this->module->l('Specific price is not valid','products');
        }
        else
        {
            $specific_price_id_currency = Tools::getValue('specific_price_id_currency');
            $specific_price_id_group = Tools::getValue('specific_price_id_group');
            $specific_price_id_country  = Tools::getValue('specific_price_id_country');
            $specific_price_id_product_attribute = Tools::getValue('specific_price_id_product_attribute');
            $specific_price_id_customer = Tools::getValue('specific_price_id_customer');
            $specific_price_from = Tools::getValue('specific_price_from');
            $specific_price_to = Tools::getValue('specific_price_to');
            $specific_price_from_quantity = Tools::getValue('specific_price_from_quantity');
            $specific_price_sp_reduction = Tools::getValue('specific_price_sp_reduction');
            if(!(float)$specific_price_sp_reduction)
                $this->errors[] = $this->module->l('No reduction value has been submitted','products');
            elseif((float)$specific_price_sp_reduction<=0)
                $this->errors[] = $this->module->l('Reduction value is not valid','products');
            elseif((int)$specific_price_from_quantity < 1 || !Validate::isUnsignedInt($specific_price_from_quantity))
                $this->errors[] = $this->module->l('From quantity is not valid','products');
            elseif(Ets_mp_product::checkExistSpecific($this->product->id,$specific_price_id_currency,$specific_price_id_group,$specific_price_id_country,$specific_price_id_product_attribute,$specific_price_id_customer,$specific_price_from,$specific_price_to,$specific_price_from_quantity) && !$id_specific_price)
                $this->errors[] = $this->module->l('A specific price already exists for these parameters.','products');
            $specific_price_leave_bprice = Tools::getValue('specific_price_leave_bprice');
            $specific_price_product_price = Tools::getValue('specific_price_product_price');
            if(!$specific_price_leave_bprice &&  (!(float)$specific_price_product_price || !Validate::isUnsignedFloat($specific_price_product_price)))
                $this->errors[] = $this->module->l('Product price is not valid','products');
            if($specific_price_from && $specific_price_from!='0000-00-00 00:00:00' && !Validate::isDate($specific_price_from))
                $this->errors[] = $this->module->l('Available from is not valid','products');
            if($specific_price_to && $specific_price_to!='0000-00-00 00:00:00' && !Validate::isDate($specific_price_to))
                $this->errors[] = $this->module->l('Available to is not valid','products');
        }
        if(!$this->errors)
            return true;
        else
            return false;
    }
    public function _submitSavePecificPrice()
    {
        if($id_specific_price = (int)Tools::getValue('id_specific_price'))
        {
            $specific_price = new SpecificPrice($id_specific_price);
        }
        else
            $specific_price = new SpecificPrice();
        $specific_price->id_product = $this->product->id;
        $specific_price->id_product_attribute = (int)Tools::getValue('specific_price_id_product_attribute');
        $specific_price->id_currency = (int)Tools::getValue('specific_price_id_currency');
        $specific_price->id_country = (int)Tools::getValue('specific_price_id_country');
        $specific_price->id_group = (int)Tools::getValue('specific_price_id_group');
        $specific_price->id_customer = (int)Tools::getValue('specific_price_id_customer');
        $specific_price->from_quantity = (int)Tools::getValue('specific_price_from_quantity');
        $specific_price_from = Tools::getValue('specific_price_from');
        $specific_price->from = $specific_price_from ? $specific_price_from: '0000-00-00 00:00:00';
        $specific_price_to = Tools::getValue('specific_price_to');
        $specific_price->to = $specific_price_to ? $specific_price_to:'0000-00-00 00:00:00';
        $specific_price->id_shop = $this->context->shop->id;
        $specific_price_leave_bprice = Tools::getValue('specific_price_leave_bprice');
        if($specific_price_leave_bprice)
            $specific_price->price=-1;
        else
            $specific_price->price = (float)Tools::getValue('specific_price_product_price');
        $specific_price->reduction_type= Tools::getValue('specific_price_sp_reduction_type');
        $specific_price_sp_reduction = Tools::getValue('specific_price_sp_reduction');
        if($specific_price->reduction_type=='amount')
            $specific_price->reduction = (float)$specific_price_sp_reduction;
        else
            $specific_price->reduction = (float)$specific_price_sp_reduction/100;
        $specific_price->reduction_tax = (int)Tools::getValue('specific_price_sp_reduction_tax');
        if($specific_price->id)
        {
            if($specific_price->update())
                $success = $this->module->l('Updated specific price successfully','products');
            else
                $this->errors[] = $this->module->l('An error occurred while updating the specific price','products');
        }
        else
        {
            if($specific_price->add())
                $success = $this->module->l('Added specific price successfully','products');
            else
                $this->errors[] = $this->module->l('An error occurred while creating the specific price','products');
        }
        if(!$this->errors)
        {
            $specific = Ets_mp_product::getDetailSpecific($specific_price->id);
            if($specific['id_product_attribute'])
            {
                $specific['attribute_name'] = Ets_mp_product::getProductAttributeName($specific['id_product_attribute']);
                
            }
            if($specific['price']>=0)
            {
                $specific['price_text'] = Tools::displayPrice($specific['price'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));
            }
            else
                $specific['price_text'] ='--';
           
            if($specific['reduction_type']=='amount')
            {
                $specific['reduction'] = Tools::displayPrice($specific['reduction'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))).($specific['reduction_tax'] ? ' ('.$this->module->l('Tax incl.','products').')':' ('.$this->module->l('Tax excl.','products').')');
            }
            else
                $specific['reduction'] = Tools::ps_round($specific['reduction']*100,2).'%';
            $specific['form'] = Tools::displayDate($specific_price->from,$this->context->language->id,true);
            $specific['to'] = Tools::displayDate($specific_price->to,$this->context->language->id,true);
            die(
                json_encode(
                    array(
                        'success' => $success,
                        'specific' => $specific,
                    )
                )
            );
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($this->errors)
                    )
                )
            );
        }
            
    }
    public function _checkValidateProduct()
    {
        $id_lang_default =Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages(true);
        $name_default = Tools::getValue('name_'.$id_lang_default);
        if(!trim($name_default))
        {
            $this->errors[] = $this->module->l('Product name is required','products');
        }
        else
        {
            foreach($languages as $language)
            {
                if(($name = Tools::getValue('name_'.$language['id_lang'])) && !Validate::isCatalogName($name))
                    $this->errors[] = $this->module->l('Product name is not valid in','proudcts').' '.$language['iso_code'];
                elseif(Tools::strlen($name) >128)
                    $this->errors[] = '['.$language['iso_code'].'] '.$this->module->l('Name is too long. It should have 128 characters or less.','products');
            }
        }
        if(in_array('seo',$this->seller_product_information))
        {
            $link_rewrite_default = Tools::getValue('link_rewrite_'.$id_lang_default);
            if(!trim($link_rewrite_default))
                $this->errors[] = $this->module->l('Product link rewrite is required','products');
            else{
                foreach($languages as $language)
                {
                    if(($link_rewrite = Tools::getValue('link_rewrite_'.$language['id_lang'])) && !Validate::isLinkRewrite($link_rewrite))
                        $this->errors[] = $this->module->l('Product link rewrite is not valid in','proudcts').' '.$language['iso_code'];
                    elseif(Tools::strlen($link_rewrite) >128)
                        $this->errors[] = '['.$language['iso_code'].'] '.$this->module->l('Product link rewrite is too long. It should have 128 characters or less.','products');
                }
            }
        }
        if(!($id_categories = Tools::getValue('id_categories')))
            $this->errors[] = $this->module->l('Category is required','products');
        elseif(!Ets_marketplace::validateArray($id_categories,'isInt'))
            $this->errors[] = $this->module->l('Category is not valid','products');
        elseif(!$id_category_default =Tools::getValue('id_category_default'))
            $this->errors[] = $this->module->l('Default category is required','products');
        elseif(!Validate::isUnsignedId($id_category_default) || !in_array($id_category_default,$id_categories) || !Validate::isLoadedObject(new Category($id_category_default)))
            $this->errors[] = $this->module->l('Default category is not valid','products');
        elseif($seller_categories = $this->seller->getApplicableProductCategories())
        {
           foreach($id_categories as $id_category)
           {
                if(!in_array($id_category,$seller_categories))
                    $this->errors[] = sprintf($this->module->l('Category #%d is not valid','products'),$id_category);
           }
        }
        foreach($languages as $language)
        {
            if(in_array('short_description',$this->seller_product_information))
            {
                if(($description_short = Tools::getValue('description_short_'.$language['id_lang'])) && !Validate::isCleanHtml($description_short,Configuration::get('PS_ALLOW_HTML_IFRAME')))
                    $this->errors[] = $this->module->l('Summary is not valid in','products').' '.$language['iso_code'];
                $short_description_limit= Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') ? :800;
                if(Tools::strlen(strip_tags($description_short))>$short_description_limit)
                    $this->errors[]= '['.$language['iso_code'].'] '.$this->module->l('Summary is too long. It should have 800 characters or less.','products');
            }
            if(($description = Tools::getValue('description_'.$language['id_lang'])) && !Validate::isCleanHtml($description,Configuration::get('PS_ALLOW_HTML_IFRAME')))
                $this->errors[] = $this->module->l('Description is not valid in','products').' '.$language['iso_code'];
            if(Tools::strlen(strip_tags($description))>21844)
                $this->errors[]= '['.$language['iso_code'].'] '.$this->module->l('Description is too long. It should have 21844 characters or less.','products');
            if( ($meta_title = Tools::getValue('meta_title_'.$language['id_lang'])) && !Validate::isCleanHtml($meta_title))
                $this->errors[] = $this->module->l('Meta title is not valid in','products').' '.$language['iso_code'];
            elseif(Tools::strlen($meta_title) >128)
                $this->errors[] = '['.$language['iso_code'].'] '.$this->module->l('Meta title is too long. It should have 128 characters or less.','products');
            if( ($meta_description=  Tools::getValue('meta_description_'.$language['id_lang'])) && !Validate::isCleanHtml($meta_description))
                $this->errors[] = $this->module->l('Meta description is not valid in','products').' '.$language['iso_code'];
            elseif(Tools::strlen($meta_description) >512)
                $this->errors[] = '['.$language['iso_code'].'] '.$this->module->l('Meta description is too long. It should have 512 characters or less.','products');
            if( ($delivery_in_stock = Tools::getValue('delivery_in_stock_'.$language['id_lang'])) && !Validate::isCleanHtml($delivery_in_stock))
                $this->errors[] = $this->module->l('Time delivery in stock is not valid in','products').' '.$language['iso_code'];  
            elseif(Tools::strlen($delivery_in_stock) >255)
                $this->errors[] = '['.$language['iso_code'].'] '.$this->module->l('Time delivery in stock is too long. It should have 255 characters or less.','products');
            if(($delivery_out_stock = Tools::getValue('delivery_out_stock_'.$language['id_lang'])) && !Validate::isCleanHtml($delivery_out_stock))
                $this->errors[] = $this->module->l('Time delivery out stock is not valid in','products').' '.$language['iso_code'];    
            elseif(Tools::strlen($delivery_out_stock) >255)
                $this->errors[] = '['.$language['iso_code'].'] '.$this->module->l('Time delivery out stock is too long. It should have 255 characters or less.','products');
            if($tags = Tools::getValue('tags_'.$language['id_lang']))
            {
                foreach(explode(',',$tags) as $tag)
                {
                    if($tag && !Validate::isGenericName($tag))
                    {
                        $this->_errors[] = '['.$language['iso_code'].'] '.$this->module->l('Tags are not valid','products');
                        break;
                    }
                }
            }       
        }
        $price_excl = Tools::getValue('price_excl');
        if(trim($price_excl)==='')
        {
            $this->errors[]= $this->module->l('Product price is required','products');
        }
        elseif(!Validate::isPrice($price_excl))
            $this->errors[]= $this->module->l('Product price is not valid','products');
        $unit_price = Tools::getValue('unit_price');
        if($unit_price && !Validate::isPrice($price_excl))
            $this->errors[]= $this->module->l('Unit price per quantity is not valid','products');
        $wholesale_price = Tools::getValue('wholesale_price');
        if($wholesale_price && !Validate::isPrice($wholesale_price))
            $this->errors[]= $this->module->l('Cost price per quantity is not valid','products');
        $width = Tools::getValue('width');
        if(trim($width)!=='' && (!Validate::isUnsignedFloat($width) || $width==0))
            $this->errors[] = $this->module->l('Product width is not valid','products');
        $height = Tools::getValue('height');
        if(trim($height)!=='' && (!Validate::isUnsignedFloat($height) || $height==0))
            $this->errors[] = $this->module->l('Product height is not valid','products');
        $depth = Tools::getValue('depth');
        if(trim($depth)!=='' && (!Validate::isUnsignedFloat($depth) || $depth ==0 ))
            $this->errors[] = $this->module->l('Product depth is not valid','products');
        $weight = Tools::getValue('weight');
        if(trim($weight)!=='' &&  (!Validate::isUnsignedFloat($weight) || $weight==0 ))
            $this->errors[] = $this->module->l('Product weight is not valid','products');
        $additional_shipping_cost = Tools::getValue('additional_shipping_cost');
        if(trim($additional_shipping_cost)!=='' &&  (!Validate::isUnsignedFloat($additional_shipping_cost) || $additional_shipping_cost==0 ))
            $this->errors[] = $this->module->l('Shipping fee is not valid','products');
        if(($condition = Tools::getValue('condition')) && !Validate::isGenericName($condition))
            $this->errors[] = $this->module->l('Condition is not valid','products');
        if($this->module->is17)
        {
            if(($show_condition = Tools::getValue('show_condition')) && !Validate::isBool($show_condition))
                $this->errors[] = $this->module->l('Show condition is not valid','products');
        }
        if(($isbn = Tools::getValue('isbn')) && !Validate::isIsbn($isbn))
            $this->errors[] = $this->module->l('ISBN is not valid','products');
        elseif(Tools::strlen($isbn) >32)
            $this->errors[] = $this->module->l('ISBN is too long. It should have 32 characters or less.','products');
        if(($ean13 = Tools::getValue('ean13')) && !Validate::isEan13($ean13))
            $this->errors[] = $this->module->l('Ean13 is not valid','products');
        elseif(Tools::strlen($ean13) >13)
            $this->errors[] = $this->module->l('Ean13 is too long. It should have 13 characters or less.','products');
        if(($upc = Tools::getValue('upc')) && !Validate::isUpc($upc))
            $this->errors[] = $this->module->l('Upc is not valid','products');
        elseif(Tools::strlen($upc) >12)
            $this->errors[] = $this->module->l('Upc is too long. It should have 12 characters or less.','products');
        if(($custom_fields = Tools::getValue('custom_fields')) && Ets_marketplace::validateArray($custom_fields))
        {
            foreach($custom_fields as $custom_field)
            {
                if(!$custom_field['label'][$id_lang_default])
                {
                    $this->errors[] = $this->module->l('Customization label is required','products');
                    break;
                }
                if(!Validate::isUnsignedInt($custom_field['type']))
                {
                    $this->errors[] = $this->module->l('Customization type is not valid','products');
                    break;
                }
                foreach($languages as $language)
                {
                    if($custom_field['label'][$language['id_lang']] && !Validate::isCleanHtml($custom_field['label'][$language['id_lang']]))
                    {
                        $this->errors[] = $this->module->l('Customization label is not valid','products');
                        break;
                    }
                }
                if($id_customization_field = (int)$custom_field['id_customization_field'])
                {
                    $customizationField = new CustomizationField($id_customization_field);
                    if(!Validate::isLoadedObject($customizationField) || $customizationField->id_product != $this->product->id)
                    {
                        $this->errors[] = $this->module->l('Customization field is not valid','products');
                    }
                }
            }
        }
        $product_type = (int)Tools::getValue('product_type');
        if($product_type==1)
        {
            if(!($inputPackItems = Tools::getValue('inputPackItems')) || !Ets_marketplace::validateArray($inputPackItems))
                $this->errors[] = $this->module->l('This pack is empty. You must add at least one product item.','products');
            else{
                foreach($inputPackItems as $inputPackItem)
                {
                    $packItem = explode('x',$inputPackItem);
                    if(!isset($packItem[0]) || !isset($packItem[1]) || !isset($packItem[2]))
                    {
                        $this->errors[] = sprintf($this->module->l('Pack item #%s is not valid','products'),$inputPackItem);
                    }
                    else
                    {
                        if(!$id_product_item = $packItem[0])
                            $this->errors[] = $this->module->l('Id product pack item is required','products');
                        elseif(($productPackItem = new Product($id_product_item)) && (!Validate::isLoadedObject($productPackItem) || !$this->seller->checkHasProduct($id_product_item)))
                            $this->errors[] = sprintf($this->module->l('Product of pack item #%s is not valid','products'),$inputPackItem);
                        if(($id_product_attribute_item = $packItem[1]) && ($combination = new Combination($id_product_attribute_item)) && (!Validate::isLoadedObject($combination) || $combination->id_product!=$id_product_item))
                            $this->errors[] = sprintf($this->module->l('Combination if pack item #%s is not valid','products'),$inputPackItem);
                        if(!$quantity_item = $packItem[2])
                            $this->errors[] = sprintf($this->module->l('Quantity of pack item #%s is required','products'),$inputPackItem);
                        elseif($quantity_item && !Validate::isUnsignedInt($quantity_item))
                            $this->errors[] = sprintf($this->module->l('Quantity of pack item #%s is not valid','products'),$inputPackItem);
                        
                    }
                    
                }
            }
        }
        if($id_manufacturer = (int)Tools::getValue('id_manufacturer'))
        {
            $manufacture = new Manufacturer($id_manufacturer);
            if(!Validate::isLoadedObject($manufacture) || !$this->seller->checkHasManufacturer($manufacture->id))
               $this->errors[] = $this->module->l('Brand is not valid','products');
        }
        if(($id_features = Tools::getValue('id_features')) && Ets_marketplace::validateArray($id_features,'isInt') && ($id_feature_values = Tools::getValue('id_feature_values')) && Ets_marketplace::validateArray($id_feature_values,'isInt'))
        {
            foreach($id_features as $index=> $id_feature)
            {
                if($id_feature && ($feature = new Feature($id_feature)) && (!Validate::isLoadedObject($feature) || !$this->seller->checkHasFeature($id_feature)))
                    $this->errors[] = sprintf($this->module->l('Feature #%d is not valid','products'),$id_feature);
                elseif($id_feature && isset($id_feature_values[$index]) && ($id_feature_value = $id_feature_values[$index]))
                {
                    if(($featureValue = new FeatureValue($id_feature_value)) && (!Validate::isLoadedObject($featureValue) || $featureValue->id_feature!= $id_feature))
                        $this->errors[] = sprintf($this->module->l('Feature value #%d is not valid','products'),$id_feature_value);
                }
            }
             
        }
        if(Tools::isSubmit('submitCreateCombination') && ($attribute_options = Tools::getValue('attribute_options')))
        {
            $check_attribute = true;
            if(!Ets_marketplace::validateArray($attribute_options))
                $this->errors[] = $this->module->l('Attribute options is not valid','products');
            else
            {
                foreach($attribute_options as $id_attribute_group => $id_attributes)
                {
                    
                    if(!$id_attribute_group || !$id_attributes)
                    {
                        $this->errors[] = $this->module->l('Attribute options is not valid','products');
                        $check_attribute= false;
                        break;
                    }
                }
                if($check_attribute)
                {
                    foreach($attribute_options as $id_attribute_group => $id_attributes)
                    {
                        foreach($id_attributes as $id_attribute)
                        {
                            if(($attributeGroup = new AttributeGroup($id_attribute_group)) && ($attribute = new Attribute($id_attribute)) && (!Validate::isLoadedObject($attributeGroup) || !Validate::isLoadedObject($attribute) || $attribute->id_attribute_group!= $id_attribute_group || !$this->seller->checkHasAttributeGroup($id_attribute_group)))
                                $this->errors[] = sprintf($this->module->l('Attribute #%d is not valid','products'),$attribute->id_attribute_group);
                        }
                        
                            
                    }
                }
            }
        }
        if($product_type!=2 && ($selectedCarriers = Tools::getValue('selectedCarriers')))
        {
            if(!Ets_marketplace::validateArray($selectedCarriers,'isInt'))
                $this->errors[] = $this->module->l('Available carriers are not valid','products');
            else
            {
                foreach($selectedCarriers as $selectedCarrier)
                {
                    if($selectedCarrier && ($carrier = Carrier::getCarrierByReference($selectedCarrier)) && (!Validate::isLoadedObject($carrier) || !$this->seller->getListCarriersUser(0,$selectedCarrier)))
                        $this->errors[] = sprintf($this->module->l('Carrier (#%d) is not valid','products'),$carrier->id);
                    
                }
            }
        }
        if(($id_suppliers = Tools::getValue('id_suppliers')) && Ets_marketplace::validateArray($id_suppliers,'isInt') && ($product_supplier_reference = Tools::getValue('product_supplier_reference')) && Ets_marketplace::validateArray($product_supplier_reference) && ($product_supplier_price = Tools::getValue('product_supplier_price')) && Ets_marketplace::validateArray($product_supplier_price) && ($product_supplier_price_currency = Tools::getValue('product_supplier_price_currency')) && Ets_marketplace::validateArray($product_supplier_price_currency))
        {
            foreach($id_suppliers as $id_supplier)
            {
                $supplier = new Supplier($id_supplier);
                if(!Validate::isLoadedObject($supplier) || !$this->seller->checkHasSupplier($supplier->id,true))
                {
                    $this->errors[] = sprintf($this->module->l('Supplier (#%d) is not valid','products'),$id_supplier);
                }
                else
                {
                    if(isset($product_supplier_reference[$id_supplier]) && ($references = $product_supplier_reference[$id_supplier]))
                    {
                        foreach($references as $reference)
                            if($reference && !Validate::isReference($reference))
                                $this->errors[] = printf($this->module->l('Supplier reference (%s) is not valid','products'),$reference);
                    }
                    if(isset($product_supplier_price[$id_supplier]) && ($prices = $product_supplier_price[$id_supplier]))
                    {
                        foreach($prices as $price)
                        {
                            if($price && !Validate::isPrice($price))
                                $this->errors[] = printf($this->module->l('Product price from supplier : (%s) is not valid','products'),$price);
                        }
                    }
                    if(isset($product_supplier_price_currency[$id_supplier]) && ($currencies = $product_supplier_price_currency[$id_supplier]))
                    {
                        foreach($currencies as $id_currency)
                        {
                            $currency_class = new Currency($id_currency);
                            if(!Validate::isLoadedObject($currency_class))
                                $this->errors[] = printf($this->module->l('Product currency from supplier : (%d) is not valid','products'),$id_currency);
                        }
                    }
                }
            }
        }
        if(($related_products = Tools::getValue('related_products')) && Ets_marketplace::validateArray($related_products,'isInt'))
        {
            foreach($related_products as $related_product)
            {
                $related_product_obj = new Product($related_product);
                if(!Validate::isLoadedObject($related_product_obj) || !$this->seller->checkHasProduct($related_product))
                {
                    $this->errors[] = $this->module->l('Related product is not valid','products');
                    break;
                }    
            }
        }
        if($this->errors)
            return false;
        else
            return true;
    }
    public function _submitSaveProduct()
    {
        $languages = Language::getLanguages(false);
        $seller_categories = $this->seller->getApplicableProductCategories();
        $id_lang_default =Configuration::get('PS_LANG_DEFAULT');
        $fields_changed = array();
        if($languages)
        {
            foreach($languages as $language)
            {
                $name = Tools::getValue('name_'.$language['id_lang']) ? : Tools::getValue('name_'.$id_lang_default);
                if($this->mpProduct && in_array('name',$this->list_field_approve))
                {
                    if(trim($this->product->name[$language['id_lang']]) != trim($name))
                        $fields_changed[] = 'name';
                    $this->mpProduct->name[$language['id_lang']] = $name;
                }
                else
                {
                    if($this->mpProduct)
                        $this->mpProduct->name[$language['id_lang']] = '';
                    $this->product->name[$language['id_lang']] = $name;
                }
                if(in_array('short_description',$this->seller_product_information))
                {
                    $description_short = ($description_short = Tools::getValue('description_short_'.$language['id_lang']))  && Tools::strpos($description_short,'etsmp_imThePlaceholder')===false ? $description_short : ( ($description_short_default = Tools::getValue('description_short_'.$id_lang_default))&& Tools::strpos($description_short_default,'etsmp_imThePlaceholder')===false ? $description_short_default:'');
                    if($this->mpProduct && in_array('description_short',$this->list_field_approve))
                    {
                        $this->mpProduct->description_short[$language['id_lang']] = $description_short;
                        if(strcmp(str_replace(' ','',$this->product->description_short[$language['id_lang']]),str_replace(' ','',$description_short))!==0)
                        {
                            $fields_changed[] = 'description_short';
                        }
                    }
                    else
                        $this->product->description_short[$language['id_lang']] = $description_short;
                }
                $description = Tools::getValue('description_'.$language['id_lang']) ? : Tools::getValue('description_'.$id_lang_default);
                if($this->mpProduct && in_array('description',$this->list_field_approve))
                {
                    $this->mpProduct->description[$language['id_lang']] = $description;
                    if(strcmp(str_replace(' ','',$this->product->description[$language['id_lang']]),str_replace(' ','',$description))!==0)
                        $fields_changed[] = 'description';
                }
                else
                    $this->product->description[$language['id_lang']] = $description;
                if(in_array('seo',$this->seller_product_information))
                {
                    $meta_title = Tools::getValue('meta_title_'.$language['id_lang']) ? : Tools::getValue('meta_title_'.$id_lang_default);
                    if($this->mpProduct && in_array('meta_title',$this->list_field_approve))
                    {
                        $this->mpProduct->meta_title[$language['id_lang']] = $meta_title;
                        if(strcmp($this->product->meta_title[$language['id_lang']],$meta_title)!==0)
                            $fields_changed[] = 'meta_title';
                    }
                    else
                        $this->product->meta_title[$language['id_lang']] = $meta_title;
                    $meta_description = Tools::getValue('meta_description_'.$language['id_lang']) ? : Tools::getValue('meta_description_'.$id_lang_default);
                    if($this->mpProduct && in_array('meta_description',$this->list_field_approve))
                    {
                        $this->mpProduct->meta_description[$language['id_lang']] =$meta_description;
                        if(strcmp($this->product->meta_description[$language['id_lang']],$meta_description)!==0)
                            $fields_changed[]= 'meta_description';
                    }
                    else
                        $this->product->meta_description[$language['id_lang']] = $meta_description;
                    $link_rewrite = Tools::getValue('link_rewrite_'.$language['id_lang']) ?: Tools::getValue('link_rewrite_'.$id_lang_default);
                    if($this->mpProduct && in_array('link_rewrite',$this->list_field_approve))
                    {
                        $this->mpProduct->link_rewrite[$language['id_lang']] = $link_rewrite;
                        if(strcmp($this->product->link_rewrite[$language['id_lang']],$link_rewrite)!==0)
                        {
                            $fields_changed[] ='link_rewrite';
                        }
                    }
                    else
                        $this->product->link_rewrite[$language['id_lang']] = $link_rewrite;
                }
                elseif(!isset($this->product->link_rewrite[$language['id_lang']]) || !$this->product->link_rewrite[$language['id_lang']])
                    $this->product->link_rewrite[$language['id_lang']] = Tools::link_rewrite($name);
                $delivery_in_stock = Tools::getValue('delivery_in_stock_'.$language['id_lang']);
                if($this->mpProduct && in_array('delivery_in_stock',$this->list_field_approve))
                {
                    $this->mpProduct->delivery_in_stock[$language['id_lang']] = $delivery_in_stock;
                    if(trim($this->product->delivery_in_stock[$language['id_lang']]) != trim($delivery_in_stock))
                        $fields_changed[] ='delivery_in_stock';
                }
                else
                    $this->product->delivery_in_stock[$language['id_lang']] = $delivery_in_stock;
                $delivery_out_stock = Tools::getValue('delivery_out_stock_'.$language['id_lang']);
                if($this->mpProduct && in_array('delivery_out_stock',$this->list_field_approve))
                {
                    $this->mpProduct->delivery_out_stock[$language['id_lang']] = $delivery_out_stock;
                    if(trim($this->product->delivery_out_stock[$language['id_lang']]) != trim($delivery_out_stock))
                        $fields_changed[] ='delivery_out_stock';
                }
                else
                    $this->product->delivery_out_stock[$language['id_lang']] = $delivery_out_stock;
            }
        }
        $product_location = Tools::getValue('product_location');
        $product_type = Tools::getValue('product_type');
        $inputPackItems = Tools::getValue('inputPackItems');
        if($product_type==2)
            $this->product->is_virtual=1;
        else
            $this->product->is_virtual=0;
        if($product_type==1 && $inputPackItems)
            $this->product->cache_is_pack=1;
        else
            $this->product->cache_is_pack=0;
        if(in_array('product_reference',$this->seller_product_information))
        {
            $reference = Tools::getValue('reference');
            if($this->mpProduct && in_array('reference',$this->list_field_approve))
            {
                $this->mpProduct->reference = $reference;
                if(trim($this->product->reference)!= trim($reference))
                    $fields_changed[] = 'reference';
            }
            else
                $this->product->reference = $reference;
        }
        $price_excl = (float)Tools::getValue('price_excl');
        $id_tax_rules_group = (int)Tools::getValue('id_tax_rules_group');
        $unit_price = (float)Tools::getValue('unit_price');
        $wholesale_price = (float)Tools::getValue('wholesale_price');
        if($this->mpProduct && in_array('price',$this->list_field_approve))
        {
            $this->mpProduct->price = $price_excl;
            if(in_array('tax',$this->seller_product_information))
                $this->mpProduct->id_tax_rules_group = (int)$id_tax_rules_group;
            $this->mpProduct->unit_price = $unit_price;
            $this->mpProduct->wholesale_price = $wholesale_price;
            $this->mpProduct->unit_price_ratio = $unit_price ? Tools::ps_round($price_excl/$unit_price,6):0;
            if($this->product->price!=$price_excl || $this->product->unit_price!= $unit_price || $this->product->wholesale_price!=$wholesale_price || (in_array('tax',$this->seller_product_information) && $this->product->id_tax_rules_group!= $id_tax_rules_group))
                $fields_changed[] ='price';
        }
        else
        {
            if($this->mpProduct)
            {
                $this->mpProduct->price = 0;
                $this->mpProduct->unit_price=0;
                $this->mpProduct->wholesale_price =0;
                $this->mpProduct->unit_price_ratio =0;
            }
            $this->product->price = $price_excl;
            $this->product->unit_price = $unit_price;
            $this->product->wholesale_price = $wholesale_price;
            $this->product->unit_price_ratio = $unit_price ? Tools::ps_round($price_excl/$unit_price,6):0;
            if(in_array('tax',$this->seller_product_information))
                $this->product->id_tax_rules_group = (int)$id_tax_rules_group;
        }
        $width = (float)Tools::getValue('width');
        $height = (float)Tools::getValue('height');
        $depth = (float)Tools::getValue('depth');
        $weight = (float)Tools::getValue('weight');
        if($this->mpProduct && in_array('width',$this->list_field_approve))
        {
            $this->mpProduct->width = (float)$width;
            if($this->product->width!= (float)$width)
                $fields_changed[] ='width';
        }
        else
            $this->product->width =(float)$width;
        if($this->mpProduct && in_array('height',$this->list_field_approve))
        {
            $this->mpProduct->height = (float)$height;
            if($this->product->height != (float)$height)
                $fields_changed[] = 'height';
        }
        else
            $this->product->height = (float)$height;
        if($this->mpProduct && in_array('depth',$this->list_field_approve))
        {
            $this->mpProduct->depth = (float)$depth;
            if($this->product->depth!= (float)$depth)
                $fields_changed[] = 'depth';
        }
        else
            $this->product->depth = (float)$depth;
        if($this->mpProduct && in_array('weight',$this->list_field_approve))
        {
            $this->mpProduct->weight = (float)$weight;
            if($this->product->weight!=(float)$weight)
                $fields_changed[] ='weight';
        }
        else
            $this->product->weight =(float)$weight;
        if($this->module->is17)
            $this->product->additional_delivery_times = Tools::getValue('additional_delivery_times');
        $this->product->additional_shipping_cost =(float)Tools::getValue('additional_shipping_cost');
        $this->product->id_category_default = (int)Tools::getValue('id_category_default');
        $this->product->minimal_quantity =(int)Tools::getValue('product_minimal_quantity');
        $this->product->location = $product_location;
        if($this->module->is17)
        {
            $this->product->low_stock_threshold = (int)Tools::getValue('product_low_stock_threshold');
            $this->product->low_stock_alert = (int)Tools::getValue('product_low_stock_alert');
        }
        if(in_array('out_of_stock_behavior',$this->seller_product_information))
            $this->product->out_of_stock = (int)Tools::getValue('out_of_stock');
        $this->product->id_manufacturer = (int)Tools::getValue('id_manufacturer');
        $this->product->redirect_type ='404';
        $this->product->condition = Tools::getValue('condition');
        if($this->module->is17)
            $this->product->show_condition = (int)Tools::getValue('show_condition');
        $isbn = Tools::getValue('isbn');
        $ean13 = Tools::getValue('ean13');
        $upc = Tools::getValue('upc');
        if($this->mpProduct && in_array('isbn',$this->list_field_approve))
        {
            $this->mpProduct->isbn = $isbn;
            if(trim($this->product->isbn) !=trim($isbn))
                $fields_changed[] = 'isbn';
        }
        else
            $this->product->isbn = $isbn;
        if($this->mpProduct && in_array('ean13',$this->list_field_approve))
        {
            $this->mpProduct->ean13 = $ean13;
            if(trim($this->product->ean13) != trim($ean13))
                $fields_changed[] ='ean13';
        }
        else
            $this->product->ean13 = $ean13;
        if($this->mpProduct && in_array('upc',$this->list_field_approve))
        {
            $this->mpProduct->upc = $upc;
            if(trim($this->product->upc)!=trim($upc))
                $fields_changed[] ='upc';
        }
        else
            $this->product->upc = $upc;
        if($this->mpProduct && in_array('image',$this->list_field_approve))
        {
            if(Ets_mp_product::getAllNewImageProductChange($this->product->id))
                $fields_changed[] ='image';
        }
        $custom_fields = Tools::getValue('custom_fields');
        if($custom_fields && Ets_marketplace::validateArray($custom_fields) && isset($this->product->customizable))
        {
            $this->product->customizable=1;
        }
        if(($id_supplier_default = Tools::getValue('id_supplier_default')) && Validate::isUnsignedId($id_supplier_default) && Validate::isLoadedObject(new Supplier($id_supplier_default)) && ($id_suppliers = Tools::getValue('id_suppliers')) && in_array($id_supplier_default,$id_suppliers))
            $this->product->id_supplier = $id_supplier_default;
        else
            $this->product->id_supplier=0;
        $active = (int)Tools::getValue('active');
        $available_for_order = (int)Tools::getValue('available_for_order');
        $show_price = $available_for_order ? 1 : (int)Tools::getValue('show_price');
        if(!$this->seller->checkVacation() || Tools::strpos($this->seller->vacation_type,'disable')===false)
        {
            $this->product->available_for_order = $available_for_order;
            $this->product->show_price = $show_price;
        }
        if(!$this->product->id)
        {
            if($this->seller->checkVacation() && Tools::strpos($this->seller->vacation_type,'disable_shopping')!==false)
            {
                $this->product->available_for_order =0;
            }
            if(Tools::isSubmit('active') && Ets_mp_product::checkSellerUpdateStatusProduct($this->product->id) && (!$this->seller->checkVacation() || Tools::strpos($this->seller->vacation_type,'disable_product')===false))
            {
                $this->product->active=(int)$active;
                $approved=1;
            }
            else
            {
                if($this->seller->auto_enabled_product=='yes')
                {
                    $this->product->active=1;
                    $approved=1;
                }
                elseif($this->seller->auto_enabled_product=='no')
                {
                    $this->product->active=0;
                    $approved=0;
                }
                elseif(Configuration::get('ETS_MP_SELLER_PRODUCT_APPROVE_REQUIRED'))
                {
                    $this->product->active=0;
                    $approved=0;
                }
                else
                {
                    $this->product->active=1;
                    $approved=1;
                }
                if($this->seller->checkVacation() && Tools::strpos($this->seller->vacation_type,'disable_product')!==false)
                    $this->product->active=0;
            }
            $this->product->indexed =0;
            if($this->product->add())
            {
                $this->seller->addProduct($this->product->id,$approved,$this->product->active);
                if (Configuration::get('PS_SEARCH_INDEXATION')) {
                    Search::indexation(false, $this->product->id);
                }
                if(Configuration::get('ETS_MP_EMAIL_ADMIN_NEW_PRODUCT_UPLOADED'))
                {
                    $data= array(
                       '{seller_name}' => $this->seller->seller_name,
                       '{seller_email}' => $this->seller->seller_email,
                       '{shop_seller}'=> $this->seller->shop_name[$this->context->language->id],
                       '{shop_seller_url}' => $this->module->getShopLink(array('id_seller'=>$this->seller->id)),
                       '{product_id}' => $this->product->id,
                       '{product_name}' => $this->product->name[$this->context->language->id],
                       '{product_link}' => $this->context->link->getProductLink($this->product->id),
                    );
                    $subjects = array(
                        'translation' => $this->module->l('A new product has been uploaded','products'),
                        'origin'=>'A new product has been uploaded',
                        'specific'=>'products'
                    );
                    Ets_marketplace::sendMail('to_admin_new_product_uploaded',$data,'',$subjects);
                }
            }
        }
        else
        {
            if(Tools::isSubmit('active') && Ets_mp_product::checkSellerUpdateStatusProduct($this->product->id) && (!$this->seller->checkVacation() || Tools::strpos($this->seller->vacation_type,'disable_product')===false))
            {
                $this->product->active=(int)$active;
            }
            if($this->mpProduct)
            {
                if($fields_changed)
                {
                    $this->mpProduct->filed_change = implode(',',$fields_changed);
                    $this->mpProduct->status =-1;
                    if($this->mpProduct->id)
                    {
                        $this->mpProduct->update();
                    }
                    else
                    {
                        $this->mpProduct->id_product = $this->product->id;
                        $this->mpProduct->add();
                    }

                    if(Configuration::get('ETS_MP_EMAIL_ADMIN_PRODUCT_UPDATED'))
                    {
                        $data= array(
                           '{shop_seller}'=> $this->seller->shop_name[$this->context->language->id],
                           '{shop_seller_url}' => $this->module->getShopLink(array('id_seller'=>$this->seller->id)),
                           '{product_name}' => $this->product->name[$this->context->language->id],
                           '{product_link}' => $this->context->link->getProductLink($this->product->id),
                        );
                        $subjects = array(
                            'translation' => $this->module->l('There is a new product update','products'),
                            'origin'=>'There is a new product update',
                            'specific'=>'products'
                        );
                        Ets_marketplace::sendMail('to_admin_when_seller_update_product',$data,'',$subjects);
                    }
                }
                elseif($this->mpProduct->id)
                    $this->mpProduct->delete();

            }
            $this->product->update();
        }
        if($this->product->id)
        {
            $product_quantity = (int)Tools::getValue('product_quantity');
            StockAvailable::setQuantity($this->product->id, 0, $product_quantity);
            StockAvailable::setProductOutOfStock($this->product->id,$this->product->out_of_stock);
            if($this->module->is17)
                Ets_mp_product::setLocation($this->product->id,$product_location);
            $id_categories = Tools::getValue('id_categories');
            Ets_mp_product::updateCategoryProduct($this->product->id,$id_categories,$seller_categories);
            $related_products = Tools::getValue('related_products');
            Ets_mp_product::updateRelatedProducts($this->product->id,$related_products);
            if($this->module->_use_feature)
            {
                $id_features = Tools::getValue('id_features');
                $id_feature_values = Tools::getValue('id_feature_values');
                $feature_value_custom = Tools::getValue('feature_value_custom');
                Ets_mp_product::updateFeatureProduct($this->product->id,$id_features,$id_feature_values,$feature_value_custom);
            }
            if($languages)
            {
                foreach($languages as $language)
                {
                    if($this->product->link_rewrite[$language['id_lang']] && Ets_mp_product::checkExistLinkWrite($this->product->link_rewrite[$language['id_lang']],$language['id_lang'],$this->product->id))
                    {
                        $this->product->link_rewrite[$language['id_lang']] .= '-'.$this->product->id;
                    }
                    if(($tags = Tools::getValue('tags_'.$language['id_lang'])) && Validate::isCleanHtml($tags))
                    {
                        Ets_mp_product::deleteProducttag($this->product->id,$language['id_lang']);
                        Tag::addTags($language['id_lang'],$this->product->id,$tags);
                    }
                    else
                        Ets_mp_product::deleteProducttag($this->product->id,$language['id_lang']);
                }

            }
            return $this->product->update();
        }
        return $this->product->id;
    }
    public function _submitCreateCombination()
    {
        $id_suppliers  = Tools::getValue('id_suppliers');
        if(!Ets_marketplace::validateArray($id_suppliers,'isInt'))
            $id_suppliers = array();
        if(!$this->product->id)
        {
            if($this->_checkValidateProduct())
            {
                $this->_submitSaveProduct();
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($this->errors),
                        )
                    )
                );
            }
        }elseif($id_suppliers)
        {
            if(!$this->_checkValidateProduct())
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($this->errors),
                        )
                    )
                );
        }
        if($this->product->id)
        {
            $this->product->is_virtual =0;
            $this->product->update();
            if(($id_product_download = (int)ProductDownload::getIdFromIdProduct($this->product->id)) && ($productDownload = new ProductDownload()) && Validate::isLoadedObject($productDownload))
            {
                $productDownload->delete(true);
            }
            $attribute_options = Tools::getValue('attribute_options');
            if (!Ets_marketplace::validateArray($attribute_options)) {
                $this->errors[] = $this->module->l('Please select at least one attribute.','products');
            } else {
                $tab = array_values($attribute_options);

                if (count($tab) && Validate::isLoadedObject($this->product)) {
                    
                    Ets_mp_product::setAttributesImpacts($this->product->id, $tab);
                    $this->combinations = array_values(Ets_marketplace::createCombinations($tab));
                    $values = array_values(array_map(array($this, 'addAttribute'), $this->combinations));
                    // @since 1.5.0
                    if ($this->module->is15 && $this->product->depends_on_stock == 0) {
                        $attributes = Product::getProductAttributesIds($this->product->id, true);
                        foreach ($attributes as $attribute) {
                            StockAvailable::removeProductFromStockAvailable($this->product->id, $attribute['id_product_attribute'], Context::getContext()->shop);
                        }
                    }
    
                    SpecificPriceRule::disableAnyApplication();

                    if(!$this->module->is17)
                    {
                        Ets_mp_product::resetAttributeDefault($this->product->id);
                    }    
                    $this->product->generateMultipleCombinations($values, $this->combinations,false);
                    // Reset cached default attribute for the product and get a new one
                    Product::getDefaultAttribute($this->product->id, 0, true);
                    Product::updateDefaultAttribute($this->product->id);
                    // @since 1.5.0
                    if ($this->module->is15 && $this->product->depends_on_stock == 0) {
                        $attributes = Product::getProductAttributesIds($this->product->id, true);
                        $quantity = (int)Tools::getValue('quantity');
                        foreach ($attributes as $attribute) {
                            if (Shop::getContext() == Shop::CONTEXT_ALL) {
                                $shops_list = Shop::getShops();
                                if (is_array($shops_list)) {
                                    foreach ($shops_list as $current_shop) {
                                        if (isset($current_shop['id_shop']) && (int)$current_shop['id_shop'] > 0) {
                                            StockAvailable::setQuantity($this->product->id, (int)$attribute['id_product_attribute'], $quantity, (int)$current_shop['id_shop']);
                                        }
                                    }
                                }
                            } else {
                                StockAvailable::setQuantity($this->product->id, (int)$attribute['id_product_attribute'], $quantity);
                            }
                        }
                    } else {
                        StockAvailable::synchronize($this->product->id);
                    }
    
                    SpecificPriceRule::enableAnyApplication();
                    SpecificPriceRule::applyAllRules(array((int)$this->product->id));
                    $id_product_attribute = Product::getDefaultAttribute((int)$this->product->id);
                    Ets_mp_product::updateCartProduct($this->product->id,$id_product_attribute);
                } else {
                    $this->errors[] = $this->module->l('Unable to initialize these parameters. A combination is missing or an object cannot be loaded.','products');
                }
            }
            $this->_submitProductSupplier();
            if(!$this->errors)
            {
                die(
                    json_encode(
                        array(
                            'success' => $this->module->l('Generated attribute successfully','products'),
                            'list_combinations' => $this->displayListCombinations(),
                            'id_product'=> $this->product->id,
                            'html_form_supplier' => $this->renderFormSupplier(),
                        )
                    )
                );
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($this->errors),
                        )
                    )
                );
            }
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($this->module->l('Product is null','products')),
                    )
                )
            );
        }
    }
    protected function addAttribute($attributes, $price = 0, $weight = 0)
    {
        $quantity = Tools::getValue('quantity');
        $reference = Tools::getValue('reference');
        if ($this->product->id) {
            return array(
                'id_product' => (int)$this->product->id,
                'price' => (float)$price,
                'weight' => (float)$weight,
                'ecotax' => 0,
                'quantity' => Validate::isInt($quantity) ? (int)$quantity:0,
                'reference' => Validate::isReference($reference) ? $reference:'',
                'default_on' => 0,
                'available_date' => '0000-00-00'
            );
        }
        unset($attributes);
        return array();
    }
    public function updateDownloadProduct($product)
    {
        $virtual_product_id = (int)Tools::getValue('virtual_product_id');
        $is_virtual_file = (int)Tools::getValue('is_virtual_file');
        if ($is_virtual_file == 1) {
            if (isset($_FILES['virtual_product_file_uploader']) && $_FILES['virtual_product_file_uploader']['size'] > 0) {
                $virtual_product_filename = ProductDownload::getNewFilename();
                $helper = new HelperUploader('virtual_product_file_uploader');
                $helper->setPostMaxSize(Tools::getOctets(ini_get('upload_max_filesize')))
                    ->setSavePath(_PS_DOWNLOAD_DIR_)->upload($_FILES['virtual_product_file_uploader'], $virtual_product_filename);
            } else {
                $virtual_product_filename = Tools::getValue('virtual_product_filename', ProductDownload::getNewFilename());
                if(!Validate::isSha1($virtual_product_filename))
                    $virtual_product_filename = ProductDownload::getNewFilename();
            }

            $product->deleteProductAttributes();//reset cache_default_attribute
            $virtual_product_expiration_date = Tools::getValue('virtual_product_expiration_date');
            if ($virtual_product_expiration_date && !Validate::isDate($virtual_product_expiration_date)) {
                return false;
            }
            $id_product_download = (int)ProductDownload::getIdFromIdProduct((int)$product->id);
            if (!$id_product_download) {
                $id_product_download = Validate::isUnsignedId($virtual_product_id) ? $virtual_product_id:0;
            }
            $is_shareable = (int)Tools::getValue('virtual_product_is_shareable');
            $virtual_product_name = Tools::getValue('virtual_product_name');
            $virtual_product_nb_days = (int)Tools::getValue('virtual_product_nb_days');
            $virtual_product_nb_downloable =(int)Tools::getValue('virtual_product_nb_downloable');
            if(!Validate::isBool($is_shareable) || !Validate::isGenericName($virtual_product_filename) || !Validate::isUnsignedInt($virtual_product_nb_days) || !Validate::isUnsignedInt($virtual_product_nb_downloable))
            {
                return false;
            }
            $download = new ProductDownload((int)$id_product_download);
            $download->id_product = (int)$product->id;
            $download->display_filename = $virtual_product_name;
            $download->filename = $virtual_product_filename;
            $download->date_add = date('Y-m-d H:i:s');
            $download->date_expiration = $virtual_product_expiration_date ? $virtual_product_expiration_date.' 23:59:59' : '';
            $download->nb_days_accessible = (int)$virtual_product_nb_days;
            $download->nb_downloadable = (int)$virtual_product_nb_downloable;
            $download->active = 1;
            $download->is_shareable = (int)$is_shareable;
            if ($download->save()) {
                return $download->filename;
            }
            
        } else {
            $id_product_download = (int)ProductDownload::getIdFromIdProduct((int)$product->id);
            if (!$id_product_download) {
                $id_product_download = Validate::isUnsignedId($virtual_product_id) ? (int)$virtual_product_id:0;
            }
            if (!empty($id_product_download)) {
                $product_download = new ProductDownload((int)$id_product_download);
                $product_download->date_expiration = date('Y-m-d H:i:s', time() - 1);
                $product_download->active = 0;
                return '';
            }
        }
        return false;
    }
    public function _getFromImageProduct($id_image)
    {
        $image_class = new Image($id_image);
        if(Ets_mp_product::CheckImage($image_class->id,$this->product->id))
        {
            $languages = Language::getLanguages(true);
            $legends = array();
            foreach($languages as $language)
            {
                $legends[$language['id_lang']] = $image_class->legend[$language['id_lang']];
            }
            $folders = str_split((string)$image_class->id);
            $path = implode('/', $folders) . '/';
            $url_image = $this->module->getBaseLink() . '/img/p/' . $path . $image_class->id . '.jpg';
            $this->context->smarty->assign(
                array(
                    'image_class' => $image_class,
                    'legends' => $legends,
                    'languages' => $languages,
                    'url_image'=> $url_image,
                    'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
                )
            );
            return $this->module->displayTpl('product/form_image.tpl');
        }
        else
            return '';
    }
    public function _submitUploadImageSave($idProduct = null, $inputFileName = 'upload_image', $die = true)
    {
        $idProduct = $idProduct ? $idProduct : Tools::getValue('id_product');
        if(!$idProduct)
            $this->errors[] = $this->module->l('Product is required','products');
        elseif(!Validate::isUnsignedId($idProduct) || !Validate::isLoadedObject(new Product($idProduct)) || !$this->seller->checkHasProduct($idProduct))
            $this->errors[] = $this->module->l('Product is not valid','products');
        $image_uploader = new HelperImageUploader($inputFileName);
        $this->module->validateFile($_FILES[$inputFileName]['name'],$_FILES[$inputFileName]['size'],$this->errors,array('jpeg', 'gif', 'png', 'jpg'),Configuration::get('PS_LIMIT_UPLOAD_IMAGE_VALUE')*1024*1024);
       
        if($this->errors)
        $image_uploader->setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg'))->setMaxSize(null);
        $files = $image_uploader->process();
        foreach ($files as &$file) {
            $image = new Image();
            if($file['error'])
            {
                $this->errors[] = $file['error'];
                return false;
            }
            else
            {
                if($this->list_field_approve && in_array('image',$this->list_field_approve))
                {
                    $image->id_product =0;
                    $image->cover =0;
                    $image->position =1+ Ets_mp_product::getHighestPositionImage($this->product->id);
                    $image->cover = 0;
       
                }
                else
                {
                    $image->id_product = (int) ($this->product->id);
                    $image->position = Image::getHighestPosition($this->product->id) + 1;
                    if (!Ets_mp_product::getCover($image->id_product)) {
                        $image->cover = 1;
                    } else {
                        $image->cover = 0;
                    }
                }
                if (($validate = $image->validateFieldsLang(false, true)) !== true) {
                    $this->errors[] = $validate;
                }
    
                if ($this->errors) {
                    continue;
                }
                if (!$image->add()) {
                    $this->errors[] = $this->module->l('An error occurred while creating additional image','products');
                } else {
                    if($image->id_product==0)
                    {
                        Ets_mp_product::addProductImage($this->product->id,$image->id);
                        $this->mpProduct->status=-1;
                        $fields_changed = $this->mpProduct->filed_change ? explode(',',$this->mpProduct->filed_change):array();
                        if(!in_array('image',$fields_changed))
                        {
                            $fields_changed[] ='image';
                            $this->mpProduct->filed_change = implode(',',$fields_changed);
                        }
                        
                        if($this->mpProduct->id)
                            $this->mpProduct->update();
                        else
                        {
                            $this->mpProduct->id_product = $this->product->id;
                            $this->mpProduct->add();
                        }
                    }
                    if (!$new_path = $image->getPathForCreation()) {
                        $this->errors[] = $this->module->l('An error occurred while attempting to create a new folder.','products');
                        continue;
                    }
                    $error = 0;
                    if (!ImageManager::resize($file['save_path'], $new_path . '.' . $image->image_format, null, null, 'jpg', false, $error)) {
                        switch ($error) {
                            case ImageManager::ERROR_FILE_NOT_EXIST:
                                $this->errors[] = $this->module->l('An error occurred while copying image, the file does not exist anymore.','products');
                                break;
                            case ImageManager::ERROR_FILE_WIDTH:
                                $this->errors[] = $this->module->l('An error occurred while copying image, the file width is 0px.','products');
                                break;
                            case ImageManager::ERROR_MEMORY_LIMIT:
                                $this->errors[] = $this->module->l('An error occurred while copying image, check your memory limit.','products');
                                break;
                            default:
                                $this->errors[] = $this->module->l('An error occurred while copying the image.','products');
                                break;
                        }
    
                        continue;
                    } else {
                        $imagesTypes = ImageType::getImagesTypes('products');
                        $generate_hight_dpi_images = (bool) Configuration::get('PS_HIGHT_DPI');
                        foreach ($imagesTypes as $imageType) {
                            if (!ImageManager::resize($file['save_path'], $new_path . '-' . Tools::stripslashes($imageType['name']) . '.' . $image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
                                $this->errors[] =$this->module->l('An error occurred while copying this image:','products').' ' . Tools::stripslashes($imageType['name']);
                                continue;
                            }
    
                            if ($generate_hight_dpi_images) {
                                if (!ImageManager::resize($file['save_path'], $new_path . '-' . Tools::stripslashes($imageType['name']) . '2x.' . $image->image_format, (int) $imageType['width'] * 2, (int) $imageType['height'] * 2, $image->image_format)) {
                                    $this->errors[] = $this->module->l('An error occurred while copying this image:','products') . ' ' . Tools::stripslashes($imageType['name']);
                                    continue;
                                }
                            }
                        }
                    }
                    if(file_exists($file['save_path']))
                        @unlink($file['save_path']);
                    unset($file['save_path']);
                    Hook::exec('actionWatermark', array('id_image' => $image->id, 'id_product' => $this->product->id));
                    if (!$image->update()) {
                        $this->module->l('An error occurred while updating the status.','products');
                        continue;
                    }
                    $shops = Shop::getContextListShopID();
                    $image->associateTo($shops);
                    $json_shops = array();
                    foreach ($shops as $id_shop) {
                        $json_shops[$id_shop] = true;
                    }
                    $file['status'] = 'ok';
                    $file['id'] = $image->id;
                    $file['position'] = $image->position;
                    $file['cover'] = $image->cover;
                    $file['legend'] = $image->legend;
                    $file['path'] = $image->getExistingImgPath();
                    $file['shops'] = $json_shops;
                    if(file_exists(_PS_TMP_IMG_DIR_ . 'product_' . (int) $this->product->id . '.jpg'))
                        @unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int) $this->product->id . '.jpg');
                    if(file_exists(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $this->product->id . '_' . $this->context->shop->id . '.jpg'))
                        @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $this->product->id . '_' . $this->context->shop->id . '.jpg');
                    if(file_exists(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $this->product->id . '_0.jpg'))
                        @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $this->product->id . '_0.jpg');
                    if($die)
                    {
                        if(version_compare(_PS_VERSION_, '1.7', '>='))
                            $type_image= ImageType::getFormattedName('home');
                        else
                            $type_image= ImageType::getFormatedName('home');
                        die(
                            json_encode(
                                array(
                                    'success' => true,
                                    'id_image' => $image->id,
                                    'is_cover' => $image->cover ? true :false,
                                    'link' => $this->context->link->getImageLink($this->product->link_rewrite[$this->context->language->id],$image->id, $type_image),
                                    'list_combinations' => $this->displayListCombinations(),
                                )
                            )
                        );
                    }
                }
            }
            
        }
        return $files;
    }
    public function _processExportProduct($sample=false)
    {
        if($products = $this->seller->getExportProducts($sample))
        {
            ob_get_clean();
            ob_start(); 
            $file =dirname(__FILE__).'/../../'.date('Y-m-d').'-list-products.csv';    
            $fp = fopen($file, 'w');
            $header = array(
                $this->module->l('Name','products'),
                $this->module->l('Image','products'),
                $this->module->l('Quantity','products'),
                $this->module->l('Price','products'),
                $this->module->l('Description','products'),
                $this->module->l('Summary','products'),
                $this->module->l('Link rewrite','products'),
                $this->module->l('Categories','products'),
                $this->module->l('Default category','products'),
                $this->module->l('Combinations','products'),
                $this->module->l('Specific price')
            );
            fputcsv($fp, $header);
            foreach($products as $row) {
                $product=array();
                $product[]=trim($row['name']);
                $product[] = trim($row['images']);
                $product[]=trim($row['quantity']);
                $product[]=trim($row['price']);
                $product[]=trim(str_replace(array("\t","\r\n","  "),' ',$row['description']));
                $product[]= trim(str_replace(array("\t","\r\n","  "),' ',$row['description_short']));
                $product[]=trim($row['link_rewrite']); 
                $product[] = trim($row['categories']);
                $product[]= trim($row['id_category_default']);
                $product[]=trim($row['product_attributes']);
                $product[]=trim($row['specific_prices']);
                fputcsv($fp, $product);             
            }
            fclose($fp);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            if(file_exists($file))
            {
                readfile($file);
                @unlink($file);
            }
            exit;
        }
    }
    public function _renderFormBulkProduct()
    {
        $number_product_upload = $this->seller->getNumberProductUpload();
        $total_products = (int) $this->seller->getProducts('',0,0,'',true);
        $this->context->smarty->assign(
            array(
                'has_edit_product' => Configuration::get('ETS_MP_ALLOW_SELLER_EDIT_PRODUCT') && (!$this->seller->checkVacation() || Tools::strpos($this->seller->vacation_type,'disable_product')===false),
                'has_delete_product' => $this->seller->checkDeleteProduct(),
                'has_duplicate_product' => $total_products < $number_product_upload || !$number_product_upload ? true :false,
            )
        );
        return $this->module->displayTpl('product/product_bulk.tpl');
    }
    public function _submitProductAttachment()
    {
        $errors = array();
        if(isset($_FILES['product_attachment_file']['name']) && $_FILES['product_attachment_file']['name'] && isset($_FILES['product_attachment_file']['tmp_name']) && $_FILES['product_attachment_file']['tmp_name'])
        {
            $this->module->validateFile($_FILES['product_attachment_file']['name'],$_FILES['product_attachment_file']['size'],$errors);
        }
        else
            $errors[] = $this->module->l('File attachment is required','products');
        if(!($product_attachment_name = Tools::getValue('product_attachment_name')))
            $errors[] = $this->module->l('Title attachment is required','products');
        elseif(!Validate::isGenericName($product_attachment_name))
            $errors[] = $this->module->l('Title attachment is not valid','products');
        if(($product_attachment_description = Tools::getValue('product_attachment_description')) && !Validate::isCleanHtml($product_attachment_description))
            $errors[] = $this->module->l('Description attachment is not valid','products');
        if(!($id_product = Tools::getValue('id_product')))
        {
            $errors[] = $this->module->l('Product is required','products');
        }
        elseif(!Validate::isUnsignedId($id_product) || !Validate::isLoadedObject( new Product($id_product)) || !$this->seller->checkHasProduct($id_product))
            $errors[] = $this->module->l('Product is not valid','products');
        if(!$errors)
        {
            $file = Tools::passwdGen(40);
            $file_name = $_FILES['product_attachment_file']['name'];
            if(move_uploaded_file($_FILES['product_attachment_file']['tmp_name'], _PS_DOWNLOAD_DIR_.$file))
            {
                $attachment = new Attachment();
                $attachment->file = $file;
                $attachment->file_name = $file_name;
                $attachment->mime = $_FILES['product_attachment_file']['type'];
                foreach(Language::getLanguages(false) as $language)
                {
                    $attachment->name[$language['id_lang']] = $product_attachment_name;
                    $attachment->description[$language['id_lang']] = $product_attachment_description;
                }
                if($attachment->add())
                {
                    if($attachment->attachProduct($id_product))
                    {
                        $this->seller->addAttachment($attachment->id);
                        die(
                            json_encode(
                                array(
                                    'success' => $this->module->l('Added attachment successfully','products'),
                                    'real_name' => $product_attachment_name,
                                    'file_name' => $file_name,
                                    'mime' => $attachment->mime,
                                    'id'=>$attachment->id,
                                )
                            )
                        );
                    }
                    else
                    {
                        $attachment->delete();
                        $errors[] = $this->module->l('An error occurred while saving the attachment');
                    }    
                }
                else
                    $errors[] = $this->module->l('An error occurred while saving the attachment');
            }
            else
                $errors[] = $this->module->l('An error occurred while uploading the attachment');
        }
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($errors),
                    )
                )
            );
        }
    }
    public function _validateFormSubmit()
    {
        $type_error = 'die_array';
        $submit = false;
        $error = false;
        $bulk_action = Tools::getValue('bulk_action');
        $action = Tools::getValue('action');
        if(Tools::isSubmit('submitDeletecombinations') || Tools::isSubmit('deleteImageProduct') ||Tools::isSubmit('submitUploadImageSave') || Tools::isSubmit('submitImageProduct') ||  Tools::isSubmit('submitCreateCombination')|| Tools::isSubmit('submitSaveProduct') || Tools::isSubmit('submitSavecombinations') || Tools::isSubmit('submitProductAttachment') || Tools::isSubmit('submitSavePecificPrice'))
            $submit = true;
        if(Tools::isSubmit('change_enabled') || Tools::isSubmit('deletefileproduct') || $bulk_action || $action=='updateImageOrdering' || Tools::isSubmit('submitDeleteSpecificPrice') || Tools::isSubmit('submitDeleteProductAttribute'))
        {
            $submit = true;
            $type_error = 'die_text';
        }
        if(Tools::isSubmit('duplicatemp_front_products'))
        {
            $submit = true;
            $type_error ='array';
        }
        if($submit)
        {
            if(!Tools::isSubmit('addnew') && !Tools::isSubmit('editmp_front_products') && !Tools::isSubmit('duplicatemp_front_products') && !$bulk_action && !Tools::isSubmit('change_enabled') && !Tools::isSubmit('deletefileproduct'))
            {
                $error = $this->module->l('Data form submit is not valid','products');
            }
            if(!Configuration::get('ETS_MP_ALLOW_SELLER_CREATE_PRODUCT') && (Tools::isSubmit('addnew') || Tools::isSubmit('duplicatemp_front_products')))
            {
                $error = $this->module->l('You do not have permission to create new product','products');
            }
            if(!Configuration::get('ETS_MP_ALLOW_SELLER_EDIT_PRODUCT') && (Tools::isSubmit('editmp_front_products') || Tools::isSubmit('change_enabled')))
            {
                $error = $this->module->l('You do not have permission to edit product','products');
            }
            
            if($bulk_action)
            {
                switch ($bulk_action) {
                  case 'activate_all':
                        if(!Configuration::get('ETS_MP_ALLOW_SELLER_EDIT_PRODUCT'))
                            $error = $this->module->l('You do not have permission to edit product','products');
                    break;
                  case 'deactivate_all':
                       if(!Configuration::get('ETS_MP_ALLOW_SELLER_EDIT_PRODUCT'))
                            $error = $this->module->l('You do not have permission to edit product','products');
                  break;
                  case 'duplicate_all':
                        if(!Configuration::get('ETS_MP_ALLOW_SELLER_CREATE_PRODUCT'))
                            $error = $this->module->l('You do not have permission to create new product','products');
                  break;
                  case 'delete_all':
                        if(!Configuration::get('ETS_MP_ALLOW_SELLER_DELETE_PRODUCT'))
                            $error = $this->module->l('You do not have permission to delete product','products');
                  break;
                } 
            }
            if($error)
            {
                if($type_error =='die_array' || $type_error=='die_text')
                {
                    die(
                        json_encode(
                            array(
                                'errors' => $type_error =='die_array' ? $this->module->displayError($error) : $error,
                            )
                        )
                    );
                }
                else
                    $this->errors[] = $error;
            }
        }
        
    }
    public function _submitAddRemoveAttachment()
    {
        $id_product = (int)Tools::getValue('id_product');
        $id_attachment = (int)Tools::getValue('id_attachment');
        $added = (int)Tools::getValue('added');
        $error = '';
        if(!Validate::isLoadedObject( new Product($id_product)) || !$this->seller->checkHasProduct($id_product) )
            $error = $this->module->l('Product is not valid','products');
        elseif(!Validate::isLoadedObject(new Attachment($id_attachment)))
            $error = $this->module->l('Attachment is not valid','products');
        else
        {

            if(Ets_mp_product::addRemoveAttachment($added,$id_product,$id_attachment))
            {
                die(
                    json_encode(
                        array(
                            'success' => $added ? $this->module->l('Added attachment successfully','products') : $this->module->l('Removed attachment successfully','products'),
                        )
                    )
                );
            }

        }
        if($error)
        {
            die(
                json_encode(
                    array(
                        'errors' => $error,
                    )
                )
            );
        }
    }
 }