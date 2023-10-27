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
 * Class Ets_MarketPlaceAttributesModuleFrontController
 * @property \Ets_mp_seller $seller
 * @property \Ets_marketplace $module
 */
class Ets_MarketPlaceSuppliersModuleFrontController extends ModuleFrontController
{
    public $_errors= array();
    public $_success ='';
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
        if(!Configuration::get('ETS_MP_SELLER_CREATE_SUPPLIER') && !Configuration::get('ETS_MP_SELLER_USER_GLOBAL_SUPPLIER'))
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->context->customer->isLogged() || !($this->seller = $this->module->_getSeller(true)) )
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->module->_checkPermissionPage($this->seller))
            die($this->module->l('You do not have permission to access this page','suppliers'));
        if(Tools::isSubmit('change_enabled') && ($id_supplier = Tools::getValue('id_supplier')) && Validate::isUnsignedId($id_supplier))
        {
            $errors = '';
            $supplier = new Supplier($id_supplier);
            if(!Validate::isLoadedObject($supplier) || !$this->seller->checkHasSupplier($id_supplier,false))
                $errors = $this->module->l('Supplier is not valid','suppliers');
            else
            {
                $change_enabled = (int)Tools::getValue('change_enabled');
                $supplier->active = $change_enabled ? 1:0;
                if($supplier->update())
                {
                    if($change_enabled)
                    {
                        die(
                            json_encode(
                                array(
                                    'href' =>$this->context->link->getModuleLink($this->module->name,'suppliers',array('id_supplier'=>$id_supplier,'change_enabled'=>0,'field'=>'active')),
                                    'title' => $this->module->l('Click to disable','suppliers'),
                                    'success' => $this->module->l('Updated successfully','suppliers'),
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
                                    'href' => $this->context->link->getModuleLink($this->module->name,'suppliers',array('id_supplier'=>$id_supplier,'change_enabled'=>1,'field'=>'active')),
                                    'title' => $this->module->l('Click to enable','suppliers'),
                                    'success' => $this->module->l('Updated successfully','suppliers'),
                                    'enabled' => 0,
                                )
                            )  
                        );
                    }
                }else
                    $errors = $this->module->l('An error occurred while saving the supplier','suppliers');
            }
            if($errors)
            {
                die(
                    json_encode(
                        array(
                            'errors' => $errors
                        )
                    )
                );
            }
        }
        if(($id_supplier = (int)Tools::getValue('id_supplier')) && Validate::isUnsignedId($id_supplier) && !Tools::isSubmit('ets_mp_submit_mp_supplier'))
        {
            if(!$this->seller->checkHasSupplier($id_supplier,false))
                die($this->module->l('You do not have permission to configure this supplier','supplier'));
        }
        if(Tools::getValue('del')=='yes' && ($id_supplier =Tools::getValue('id_supplier')) && Validate::isUnsignedId($id_supplier))
        {
            $supplier = new Supplier($id_supplier);
            if(!Validate::isLoadedObject($supplier) || !$this->seller->checkHasSupplier($id_supplier,false))
                $this->_errors[] = $this->module->l('Supplier is not valid','suppliers');
            elseif($supplier->delete())
            {
                Ets_mp_seller::deleteSupplier($supplier->id);
                $this->context->cookie->success_message = $this->module->l('Deleted successfully','suppliers');
                Tools::redirect($this->context->link->getModuleLink($this->module->name,'suppliers',array('list'=>1)));
            }
            else
                $this->_errors[] = $this->module->l('An error occurred while deleting the supplier','suppliers');
            
        }
        if(Tools::isSubmit('deletelogo') && ($id_supplier = Tools::getValue('id_supplier')) && Validate::isUnsignedId($id_supplier))
        {
            $supplier = new Supplier($id_supplier);
            if(!Validate::isLoadedObject($supplier) || !$this->seller->checkHasSupplier($id_supplier,false))
                $this->_errors[] = $this->module->l('Suppliers are not valid','suppliers');
            else
            {
                if(file_exists(_PS_SUPP_IMG_DIR_ . $id_supplier . '.jpg')) {
                    @unlink(_PS_SUPP_IMG_DIR_ . $id_supplier . '.jpg');
                }
                $images_types = ImageType::getImagesTypes('manufacturers');
                foreach ($images_types as $image_type) {
                    if(file_exists( _PS_SUPP_IMG_DIR_ . $id_supplier . '-' . Tools::stripslashes($image_type['name']) . '.jpg'))
                        @unlink( _PS_SUPP_IMG_DIR_ . $id_supplier . '-' . Tools::stripslashes($image_type['name']) . '.jpg');
                    if(file_exists(_PS_SUPP_IMG_DIR_ . $id_supplier . '-' . Tools::stripslashes($image_type['name']) . '2x.jpg'))
                        @unlink(_PS_SUPP_IMG_DIR_ . $id_supplier . '-' . Tools::stripslashes($image_type['name']) . '2x.jpg');
                }
                $this->context->cookie->success_message = $this->module->l('Deleted logo successfully','suppliers');
                Tools::redirect($this->context->link->getModuleLink($this->module->name,'suppliers',array('list'=>1)));
            }
        }
        if(Tools::isSubmit('changeUserSuppliers'))
        {
            $user_supplier = Tools::getValue('user_supplier');
            if(!in_array($user_supplier,array(1,2,3)))
                $user_supplier = 1;
            $this->seller->user_supplier = (int)$user_supplier;
            if($this->seller->update())
            {
                die(
                    json_encode(
                        array(
                            'success' => $this->module->l('Updated successfully','suppliers'),
                        )
                    )
                );
            }

        }
        if(Tools::isSubmit('submitSaveSupplier'))
        {
            $this->_submitSaveSupplier();
        }
    }
    public function initContent()
	{
		parent::initContent();
        if($this->context->cookie->success_message)
        {
            $this->_success = $this->context->cookie->success_message;
            $this->context->cookie->success_message ='';
        }    
        $this->context->controller->addJqueryPlugin('tagify');
        if(Tools::isSubmit('addnew') || Tools::isSubmit('editmp_supplier')){
             $display_form =true;
        }
        else
            $display_form = Configuration::get('ETS_MP_SELLER_USER_GLOBAL_SUPPLIER') ? false :true;
        $this->context->smarty->assign(
            array(
                'path' => $this->module->getBreadCrumb(),
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false, 
                'display_form' => $display_form,
                'html_content' => $this->_initContent(),
                'ets_seller' => $this->seller,
                '_errors' => $this->_errors ? $this->module->displayError($this->_errors):'',
                '_success' => $this->_success ? $this->module->displayConfirmation($this->_success):'',
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/suppliers.tpl');      
        else        
            $this->setTemplate('suppliers_16.tpl'); 
    }
    public function renderSupplierForm()
    {
        $languages = Language::getLanguages(false);
        $valueFieldPost= array();
        if(($id_supplier = Tools::getValue('id_supplier')) && Validate::isUnsignedId($id_supplier))
        {
            $supplier = new Supplier($id_supplier);
            if(Validate::isLoadedObject($supplier) && !$this->seller->checkHasSupplier($id_supplier,false))
                die($this->module->l('You do not have permission to configure this supplier','supplier'));
        }
        else
            $supplier= new Supplier();
        if($languages)
        {
            foreach($languages as $language)
            {
                $valueFieldPost['description'][$language['id_lang']] = Tools::getValue('description_'.$language['id_lang'],isset($supplier->description[$language['id_lang']]) ? $supplier->description[$language['id_lang']]:'');
                $valueFieldPost['meta_title'][$language['id_lang']] = Tools::getValue('meta_title_'.$language['id_lang'],isset($supplier->meta_title[$language['id_lang']]) ? $supplier->meta_title[$language['id_lang']]:'' );
                $valueFieldPost['meta_description'][$language['id_lang']] = Tools::getValue('meta_description_'.$language['id_lang'],isset($supplier->meta_description[$language['id_lang']]) ? $supplier->meta_description[$language['id_lang']] :'');
                $valueFieldPost['meta_keywords'][$language['id_lang']] = Tools::getValue('meta_keywords_'.$language['id_lang'],isset($supplier->meta_keywords[$language['id_lang']]) ? $supplier->meta_keywords[$language['id_lang']]:'');
            }
        }
        $valueFieldPost['name'] = Tools::getValue('name',$supplier->name);
        $valueFieldPost['active'] =Tools::getValue('active',$supplier->active);
        if($id_supplier && ($id_address = Address::getAddressIdBySupplierId($id_supplier)))
        {
            $address = new Address($id_address);
        }
        else
            $address = new Address();
        $valueFieldPost['phone'] = Tools::getValue('phone',$address->phone);
        $valueFieldPost['phone_mobile'] = Tools::getValue('phone_mobile',$address->phone_mobile);
        $valueFieldPost['address1'] = Tools::getValue('address1',$address->address1);
        $valueFieldPost['address2'] = Tools::getValue('address2',$address->address2);
        $valueFieldPost['id_country'] =(int)Tools::getValue('id_country',$address->id_country);
        $valueFieldPost['id_state'] = (int)Tools::getValue('id_state',$address->id_state) ;
        $valueFieldPost['postcode'] = Tools::getValue('postcode',$address->postcode);
        $valueFieldPost['city'] =Tools::getValue('city',$address->city);
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $type_image= ImageType::getFormattedName('small');
        else
            $type_image= ImageType::getFormatedName('small');
        if($supplier->id && file_exists(_PS_SUPP_IMG_DIR_.(int)$supplier->id.'.jpg'))
        {
            if($this->module->is17)
                $valueFieldPost['logo'] = $this->context->link->getSupplierImageLink($supplier->id,$type_image);
            else
                $valueFieldPost['logo'] = $this->module->getBaseLink().'/img/su/'.(int)$supplier->id.'.jpg';
        }
        $countries =Country::getCountries($this->context->language->id,true,false,false);
        if($countries)
        {
            foreach($countries as &$country)
            {
                $country['id'] = $country['id_country'];
            }
        }
        $states = State::getStates($this->context->language->id,false);
        if($states)
        {
            foreach($states as &$state)
            {
                $state['id'] = $state['id_state'];
                $state['parent'] = $state['id_country'];
            }
        }
        $fields=array(
            array(
                'type'=>'text',
                'name'=>'name',
                'label' => $this->module->l('Name','suppliers'),
                'required' => true,
                'desc' => $this->module->l('Invalid characters: <>;=#{} ','suppliers'),
            ),
            array(
                'type'=>'textarea',
                'name'=>'description',
                'label' => $this->module->l('Description','suppliers'),
                'lang' => true,
                'autoload_rte'=>true,
            ),
            array(
                'type'=>'text',
                'name'=>'phone',
                'label' => $this->module->l('Phone','suppliers'),
                'desc' => $this->module->l('Phone number for this supplier ','suppliers'),
            ),
            array(
                'type'=>'text',
                'name'=>'phone_mobile',
                'label' => $this->module->l('Mobile phone','suppliers'),
                'desc' => $this->module->l('Mobile phone number for this supplier','suppliers'),
            ),
            array(
                'type'=>'text',
                'name'=>'address1',
                'label' => $this->module->l('Address','suppliers'),
                'required' => true,
            ),
            array(
                'type'=>'text',
                'name'=>'address2',
                'label' => $this->module->l('Address(2)','suppliers'),
            ),
            array(
                'type'=>'text',
                'name'=>'postcode',
                'label' => $this->module->l('Zip/postal code','suppliers'),
            ),
            array(
                'type'=>'text',
                'name'=>'city',
                'label' => $this->module->l('City','suppliers'),
                'required' => true,
            ),
            array(
                'type'=>'select',
                'label'=>$this->module->l('Country','suppliers'),
                'name'=>'id_country',
                'required' => true, 
                'form_group_class' => 'js-manufacturer-address-country', 
                'values'=>$countries,
            ),
            array(
                'type'=>'select',
                'label'=>$this->module->l('State','suppliers'),
                'name'=>'id_state',
                'required' => true, 
                'form_group_class' => 'js-manufacturer-address-state', 
                'values'=>$states,
            ),
            array(
                'type' => 'file',
                'name' =>'logo',
                'link_del' => $this->context->link->getModuleLink($this->module->name,'suppliers',array('id_supplier'=>$supplier->id,'deletelogo'=>1)),
                'label' => $this->module->l('Logo','suppliers'),
                'desc' => sprintf($this->module->l('Accepted formats: jpg, jpeg, gif, png. Limit: %dMB','suppliers'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')),
            ),
            array(
                'type'=>'text',
                'name'=>'meta_title',
                'label' => $this->module->l('Meta title','suppliers'),
                'lang' => true,
                'desc' => $this->module->l('Invalid characters: <>;=#{} ','suppliers'),
            ),
            array(
                'type'=>'textarea',
                'name'=>'meta_description',
                'label' => $this->module->l('Meta description','suppliers'),
                'lang' => true,
                'desc' => $this->module->l('Invalid characters: <>;=#{} ','suppliers'),
            ),
            array(
                'type'=>'tags',
                'name'=>'meta_keywords',
                'label' => $this->module->l('Meta keywords','suppliers'),
                'lang' => true,
                'desc' => $this->module->l('To add tags, click in the field, write something, and then press the "Enter" key. Invalid characters: <>;=#{} ','suppliers'),
            ),
            array(
                'type' =>'switch',
                'name' => 'active',
                'label'=> $this->module->l('Enabled','suppliers'),
            )
        );  
        $this->context->smarty->assign(
            array(
                'fields' => $fields,
                'languages' => $languages,
                'valueFieldPost' => $valueFieldPost,
                'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
            )
        );
        $html_form = $this->module->displayTpl('form.tpl');
        $this->context->smarty->assign(
            array(
                'url_path' => $this->module->getBaseLink().'/modules/'.$this->module->name.'/',
                'html_form' => $html_form,
                'id_supplier' => $id_supplier,
                'link_cancel' => $this->context->link->getModuleLink($this->module->name,'suppliers',array('list'=>1))
            )
        );
        return $this->module->displayTpl('supplier/supplier_form.tpl');
    }
    public function _initContent()
    {
        if(Tools::isSubmit('addnew') || Tools::isSubmit('editmp_supplier')){
            if(!Configuration::get('ETS_MP_SELLER_CREATE_SUPPLIER'))
                return $this->module->displayWarning($this->module->l('You do not have permission to create new supplier','suppliers'));
            return  $this->renderSupplierForm();
        }
        else
        {
            $fields_list = array(
                'id_supplier' => array(
                    'title' => $this->module->l('ID','suppliers'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                ),
                'logo'=> array(
                    'title' => $this->module->l('Logo','suppliers'),
                    'type'=>'text',
                    'strip_tag' => false,
                    'sort'=>false,
                    'filter'=> false,
                ),
                'name' => array(
                    'title' => $this->module->l('Name','suppliers'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,
                ),
                'products' => array(
                    'title' => $this->module->l('Products','suppliers'),
                    'type' => 'text',
                    'sort' => true,
                ),
                'active' => array(
                    'title' => $this->module->l('Enabled','suppliers'),
                    'type' => 'active',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'active',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'active' => 1,
                                'title' => $this->module->l('Yes','suppliers')
                            ),
                            1 => array(
                                'active' => 0,
                                'title' => $this->module->l('No','suppliers')
                            )
                        )
                    )
                ),
            );
            //Filter
            $show_resset = false;
            $filter = "";
            $having="";
            if(($id_supplier = Tools::getValue('id_supplier')) && !Tools::getValue('del'))
            {
                if(Validate::isUnsignedId($id_supplier))
                    $filter .= ' AND s.id_supplier="'.(int)$id_supplier.'"';
                $show_resset = true;
            }
            
            if(($name = trim(Tools::getValue('name'))) || $name!=='')
            {
                if(Validate::isCleanHtml($name))
                    $filter .=' AND s.name LIKE "%'.pSQL($name).'%"';
                $show_resset = true;
            }
            if(($active = trim(Tools::getValue('active'))) || $active !=='')
            {
                if(Validate::isBool($active))
                    $filter .= ' AND s.active="'.(int)$active.'"';
                $show_resset=true;
            }
            //Sort
            $sort = "";
            $sort_value = Tools::getValue('sort','id_supplier');
            $sort_type = Tools::getValue('sort_type','desc');
            if($sort_value)
            {
                switch ($sort_value) {
                    case 'id_supplier':
                        $sort .='s.id_supplier';
                        break;
                    case 'name':
                        $sort .='s.name';
                        break;
                    case 'active':
                        $sort .='s.active';
                        break;
                }
                if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                    $sort .= ' '.trim($sort_type);  
            }
            //Paggination
            $page = Tools::getValue('page');
            if($page<=0)
                $page = 1;
            $totalRecords = (int) $this->seller->getSuppliers($filter,$having,0,0,'',true);
            $paggination = new Ets_mp_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url =$this->context->link->getModuleLink($this->module->name,'suppliers',array('list'=>true, 'page'=>'_page_')).$this->module->getFilterParams($fields_list,'mp_supplier');
            $paggination->limit =  (int)Tools::getValue('paginator_supplier_select_limit',10);
            $paggination->name ='supplier';
            $paggination->num_links =5;
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $suppliers = $this->seller->getSuppliers($filter, $having,$start,$paggination->limit,$sort,false);
            if($suppliers)
            {
                if(version_compare(_PS_VERSION_, '1.7', '>='))
                    $type_image= ImageType::getFormattedName('small');
                else
                    $type_image= ImageType::getFormatedName('small');
                foreach($suppliers as &$supplier)
                {
                    if(file_exists(_PS_SUPP_IMG_DIR_.$supplier['id_supplier'].'.jpg'))
                    {
                        if($this->module->is17)
                            $supplier['logo'] = Module::getInstanceByName('ets_marketplace')->displayText('','img','','','','',$this->context->link->getSupplierImageLink($supplier['id_supplier'],$type_image).'?time='.time());
                        else
                            $supplier['logo'] = Module::getInstanceByName('ets_marketplace')->displayText('','img','ets_mp_logo_mnf','','','',$this->module->getBaseLink().'/img/su/'.$supplier['id_supplier'].'.jpg?time='.time());
                    }
                    if($supplier['id_seller'])
                    {
                        $supplier['action_edit']=true;
                        $supplier['child_view_url'] = $this->context->link->getSupplierLink($supplier['id_supplier']);
                    }
                    else
                    {
                        $supplier['action_edit'] = false;
                        $supplier['child_view_url'] = $this->context->link->getSupplierLink($supplier['id_supplier']);
                    }
                    $supplier['name'] =Module::getInstanceByName('ets_marketplace')->displayText($supplier['name'],'a','','',$this->context->link->getSupplierLink($supplier['id_supplier']));
                }
            }
            $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','suppliers');
            $paggination->style_links = 'links';
            $paggination->style_results = 'results';
            $listData = array(
                'name' => 'mp_supplier',
                'actions' => array('view','edit', 'delete'),
                'currentIndex' => $this->context->link->getModuleLink($this->module->name,'suppliers',array('list'=>1)).($paggination->limit!=10 ? '&paginator_supplier_select_limit='.$paggination->limit:''),
                'postIndex' => $this->context->link->getModuleLink($this->module->name,'suppliers',array('list'=>1)),
                'identifier' => 'id_supplier',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => $this->module->l('Suppliers','suppliers'),
                'fields_list' => $fields_list,
                'field_values' => $suppliers,
                'paggination' => $paggination->render(),
                'filter_params' => $this->module->getFilterParams($fields_list,'mp_supplier'),
                'show_reset' =>$show_resset,
                'totalRecords' => $totalRecords,
                'sort'=> $sort_value,
                'show_add_new'=>  Configuration::get('ETS_MP_SELLER_CREATE_SUPPLIER') && $this->seller->user_supplier!=1 ? true:false,
                'link_new' => $this->context->link->getModuleLink($this->module->name,'suppliers',array('addnew'=>1)),
                'sort_type' => $sort_type,
            );           
            return $this->module->renderList($listData);
        }
    }
    public function _submitSaveSupplier()
    {
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        if($id_supplier = (int)Tools::getValue('id_supplier'))
        {
            $supplier = new Supplier($id_supplier);
            if(!Validate::isUnsignedId($id_supplier) || !Validate::isLoadedObject($supplier) || !$this->seller->checkHasSupplier($id_supplier,false))
                $this->_errors[] = $this->module->l('Supplier is not valid','suppliers');
        }
        else
            $supplier = new Supplier();
        $name = Tools::getValue('name');
        if(!$name)
            $this->_errors[] = $this->module->l('Name is required','suppliers');
        elseif($name && !Validate::isCatalogName($name))
            $this->_errors[] = $this->module->l('Name is not valid','suppliers');
        else
            $supplier->name = $name;
        foreach($languages as $language)
        {
            if(($description = Tools::getValue('description_'.$language['id_lang'])) && !Validate::isCleanHtml($description))
                $this->_errors[] = $this->module->l('Description is not valid in','suppliers').' '.$language['iso_code'];
            elseif($description)
                $supplier->description[$language['id_lang']] = $description;
            else
                $supplier->description[$language['id_lang']] = Tools::getValue('description_'.$id_lang_default);
            if(($meta_title= Tools::getValue('meta_title_'.$language['id_lang'])) && !Validate::isGenericName($meta_title) )
                $this->_errors[] = $this->module->l('Meta title is not valid in','suppliers').' '.$language['iso_code'];
            elseif($meta_title)
                $supplier->meta_title[$language['id_lang']] = $meta_title;
            else
                $supplier->meta_title[$language['id_lang']] = Tools::getValue('meta_title_'.$id_lang_default);
            if(($meta_description= Tools::getValue('meta_description_'.$language['id_lang'])) && !Validate::isGenericName($meta_description))
                $this->_errors[] = $this->module->l('Meta description is not valid in','suppliers').' '.$language['iso_code'];
            elseif($meta_description)
                $supplier->meta_description[$language['id_lang']] = $meta_description;
            else
                $supplier->meta_description[$language['id_lang']] = Tools::getValue('meta_description_'.$id_lang_default);
            if(($meta_keywords= Tools::getValue('meta_keywords_'.$language['id_lang'])) && !Validate::isGenericName($meta_keywords))
                $this->_errors[] = $this->module->l('Meta keywords is not valid in','suppliers').' '.$language['iso_code'];
            elseif($meta_keywords)
                $supplier->meta_keywords[$language['id_lang']] = $meta_keywords;
            else
                $supplier->meta_keywords[$language['id_lang']] = Tools::getValue('meta_keywords_'.$id_lang_default);
        }
        
        $supplier->active = (int)Tools::getValue('active');
        if(isset($_FILES['logo']) && isset($_FILES['logo']['name']) && $_FILES['logo']['name'] && isset($_FILES['logo']['size']) && $_FILES['logo']['size'])
        {
            $this->module->validateFile($_FILES['logo']['name'],$_FILES['logo']['size'],$this->_errors,array('jpg', 'gif', 'jpeg', 'png'));
        }
        if(!($address1 = Tools::getValue('address1')))
            $this->_errors[] = $this->module->l('Address is required','suppliers');
        elseif(!Validate::isAddress($address1))
            $this->_errors[] = $this->module->l('Address is not valid','suppliers');
        if(($address2 = Tools::getValue('address2')) && !Validate::isAddress($address2))
            $this->_errors[] = $this->module->l('Address(2) is not valid','suppliers');
        if(($phone = Tools::getValue('phone')) && !Validate::isPhoneNumber($phone))
            $this->_errors[] = $this->module->l('Phone is not valid','suppliers');
        if(($phone_mobile = Tools::getValue('phone_mobile')) && !Validate::isPhoneNumber($phone_mobile))
            $this->_errors[] = $this->module->l('Mobile phone is not valid','suppliers');
        if(!($id_country = (int)Tools::getValue('id_country')))
            $this->_errors[] = $this->module->l('Country is required','suppliers');
        elseif(($country = new Country($id_country)) && (!Validate::isLoadedObject($country) || !$country->active))
            $this->_errors[] = $this->module->l('Country is not valid','suppliers');
        if(($id_state = Tools::getValue('id_state')) && (!Validate::isUnsignedId($id_state) || (($state = new State($id_state)) && (!Validate::isLoadedObject($state) || $state->id_country != $id_country  || !$state->active)) ) )
            $this->_errors[] = $this->module->l('State is not valid','suppliers');
        if(!($city = Tools::getValue('city')))
            $this->_errors[] = $this->module->l('City is required','suppliers');
        elseif(!Validate::isCityName($city))
            $this->_errors[] = $this->module->l('City is not valid','suppliers');
        if(($postcode = Tools::getValue('postcode')) && !Validate::isPostCode($postcode))
            $this->_errors[] = $this->module->l('Zip/postal code is not valid','suppliers');
        elseif($postcode && !$country->checkZipCode($postcode))
            $this->_errors[] = sprintf($this->module->l('Zip/postal code is not valid - should look like "%s"','suppliers'),$country->zip_code_format);
        if(!$this->_errors)
        {
            if($supplier->id)
            {
                if($supplier->update())
                {
                    $this->submitAddressSupplier($supplier);
                    $this->supplierImageUpload($supplier->id);
                    if(!$this->_errors)
                    {
                        $this->context->cookie->success_message = $this->module->l('Updated successfully','suppliers');
                        Tools::redirect($this->context->link->getModuleLink($this->module->name,'suppliers',array('list'=>1)));
                    }
                }
                else
                    $this->_errors[] = $this->module->l('An error occurred while saving the supplier','suppliers');
            }
            elseif($supplier->add())
            {
                $this->submitAddressSupplier($supplier);
                $this->supplierImageUpload($supplier->id);
                if(!$this->_errors)
                {
                    Ets_mp_seller::addSupplierToSeller($supplier->id,$this->seller->id_customer);
                    $this->context->cookie->success_message = $this->module->l('Added successfully','suppliers');
                    Tools::redirect($this->context->link->getModuleLink($this->module->name,'suppliers',array('list'=>1)));
                }
            } 
            else
                $this->_errors[] = $this->module->l('An error occurred while saving the supplier','suppliers');   
        } 
    }
    public function submitAddressSupplier($supplier)
    {
        if($id_address = Address::getAddressIdBySupplierId($supplier->id))
            $address = new Address($id_address);
        else
            $address = new Address();
        $address->alias = $supplier->name;
        $address->firstname = 'supplier';
        $address->lastname = 'supplier';
        $address->id_supplier = $supplier->id;
        $address->phone = Tools::getValue('phone');
        $address->phone_mobile = Tools::getValue('phone_mobile');
        $address->address1 = Tools::getValue('address1');
        $address->address2 = Tools::getValue('address2');
        $address->id_country = (int)Tools::getValue('id_country');
        $address->city = Tools::getValue('city');
        $address->postcode = Tools::getValue('postcode');
        if(($id_state = Tools::getValue('id_state')) && Validate::isUnsignedId($id_state) && ($state = new State($id_state)) && Validate::isLoadedObject($state) && $state->id_country == $address->id_country )
            $address->id_state = $id_state;
        else
            $address->id_state =null;
        
        if($address->id)
        {
            if(!$address->update())
                $this->_errors[] = $this->module->l('An error occurred while saving the supplier address','suppliers');
        }
        else
        {
            if(!$address->add())
                $this->_errors[] = $this->module->l('An error occurred while creating the supplier address','suppliers');
        }
    }
    public function supplierImageUpload($id_supplier)
    {
        if(isset($_FILES['logo']) && isset($_FILES['logo']['name']) && $_FILES['logo']['name'])
        {
            if(file_exists(_PS_SUPP_IMG_DIR_.$id_supplier.'.jpg'))
            {
                @unlink(_PS_SUPP_IMG_DIR_.$id_supplier.'.jpg');
            }
            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
			if(move_uploaded_file($_FILES['logo']['tmp_name'], $temp_name))
            {
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES['logo']['name'], '.'), 1));
                if(ImageManager::resize($temp_name, _PS_SUPP_IMG_DIR_.$id_supplier.'.jpg', null, null, $type))
                {
                    $res=true;
                    $generate_hight_dpi_images= (bool) Configuration::get('PS_HIGHT_DPI');
                    if(file_exists(_PS_SUPP_IMG_DIR_ . $id_supplier . '.jpg')) {
                        $images_types = ImageType::getImagesTypes('suppliers');
                        foreach ($images_types as $image_type) {
                            $res &= ImageManager::resize(
                                _PS_SUPP_IMG_DIR_ . $id_supplier . '.jpg',
                                _PS_SUPP_IMG_DIR_ . $id_supplier . '-' . Tools::stripslashes($image_type['name']) . '.jpg',
                                (int) $image_type['width'],
                                (int) $image_type['height']
                            );
            
                            if ($generate_hight_dpi_images) {
                                $res &= ImageManager::resize(
                                    _PS_SUPP_IMG_DIR_ . $id_supplier . '.jpg',
                                    _PS_SUPP_IMG_DIR_ . $id_supplier . '-' . Tools::stripslashes($image_type['name']) . '2x.jpg',
                                    (int) $image_type['width'] * 2,
                                    (int) $image_type['height'] * 2
                                );
                            }
                        }
                        $current_logo_file = _PS_TMP_IMG_DIR_ . 'supplier_mini_' . $id_supplier . '_' . $this->context->shop->id . '.jpg';
                        if ($res && file_exists($current_logo_file)) {
                            unlink($current_logo_file);
                        }
                    }
                    return $res;
                }
                else
                  $this->_errors[] = $this->module->l('An error occurred while uploading the supplier logo','suppliers');  
                
            }
            else
                $this->_errors[] = $this->module->l('An error occurred while uploading the supplier logo','suppliers');
        }
    }
 }