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
class Ets_MarketPlaceAttributesModuleFrontController extends ModuleFrontController
{
    public $seller;
    public $_errors= array();
    public $_success ='';
    public function __construct()
	{
		parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
	}
    public function postProcess()
    {
        parent::postProcess();
        @ini_set('display_errors', 'off');
        $id_attribute = (int)Tools::getValue('id_attribute');
        $id_attribute_group = (int)Tools::getValue('id_attribute_group');
        if(!$this->context->customer->isLogged() || !($this->seller = $this->module->_getSeller(true)) )
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->module->_checkPermissionPage($this->seller))
            die($this->module->l('You do not have permission to access this page','attributes'));
        if(Tools::isSubmit('changeUserAttribute'))
        {
            $user_attribute = (int)Tools::getValue('user_attribute');
            if(!in_array($user_attribute,array(1,2,3)))
                $user_attribute = 1;
            $this->seller->user_attribute = (int)$user_attribute;
            if($this->seller->update())
            {
                die(
                    json_encode(
                        array(
                            'success' => $this->module->l('Updated successfully','attribute'),
                        )
                    )
                );
            }
        }
        if(Configuration::get('ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT'))
            $product_types = explode(',',Configuration::get('ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT'));
        else
            $product_types = array();
        if(!in_array('standard_product',$product_types) || !$this->module->_use_attribute)
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!Tools::isSubmit('list_group') && !(Tools::isSubmit('deleteimage') && $id_attribute) && !(Tools::isSubmit('editmp_attribute') && $id_attribute) && !(Tools::isSubmit('newAttribute') && $id_attribute_group) && !Tools::isSubmit('newGroup') && !(Tools::isSubmit('viewGroup') && $id_attribute_group) && !(Tools::isSubmit('del') && $id_attribute_group) && !(Tools::isSubmit('editmp_attribute_group') && $id_attribute_group))
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'attributes',array('list_group'=>1)));
        if($id_attribute_group && !$this->seller->checkHasAttributeGroup($id_attribute_group,Tools::isSubmit('viewGroup') ? true :false) && !Tools::isSubmit('ets_mp_submit_mp_attribute_group'))
            die($this->module->l('You do not have permission to config this attribute group','attributes'));
        if($id_attribute && !Tools::isSubmit('ets_mp_submit_mp_attribute'))
        {
            $attribute = new Attribute($id_attribute);
            if(!$this->seller->checkHasAttributeGroup($attribute->id_attribute_group,false))
                die($this->module->l('You do not have permission to config this attribute','attributes'));
        }
        if(Tools::isSubmit('submitSaveAttributeGroup'))
        {
            $this->_submitSaveAttributeGroup();
        }
        if(Tools::isSubmit('submitSaveAttribute'))
        {
            $this->_submitSaveAttribute();
        }
        if(Tools::isSubmit('del') && $id_attribute)
        {
            $attribute = new Attribute($id_attribute);
            if(!$this->seller->checkHasAttributeGroup($attribute->id_attribute_group,false))
                die($this->module->l('You do not have permission to delete this attribute','attributes'));
            elseif($attribute->delete())
            {
                $this->context->cookie->_success = $this->module->l('Deleted successfully','attributes');
                $this->context->cookie->write();
                if(file_exists(_PS_COL_IMG_DIR_.$id_attribute.'.jpg'))
                {
                    @unlink(_PS_COL_IMG_DIR_.$id_attribute.'.jpg');
                }
                Tools::redirect($this->context->link->getModuleLink($this->module->name,'attributes',array('viewGroup'=>1,'id_attribute_group'=>$attribute->id_attribute_group)));
            }
            else
                $this->_errors[] = $this->module->l('An error occurred while deleting the attribute','attributes');
        }
        elseif(Tools::isSubmit('del') && $id_attribute_group && Validate::isUnsignedId($id_attribute_group))
        {
            $attributeGroup = new AttributeGroup($id_attribute_group);
            if(!$this->seller->checkHasAttributeGroup($id_attribute_group,false))
                die($this->module->l('You do not have permission to delete this attribute','attributes'));
            elseif($attributeGroup->delete())
            {
                $this->context->cookie->_success = $this->module->l('Deleted successfully','attributes');
                $this->context->cookie->write();
                Tools::redirect($this->context->link->getModuleLink($this->module->name,'attributes',array('list_group'=>1)));
            }
            else
                $this->_errors[] = $this->module->l('An error occurred while deleting the attribute group','attributes');
        }
        if(Tools::isSubmit('deleteimage') && ($id_attribute=(int)Tools::getValue('id_attribute')) && Validate::isUnsignedId($id_attribute))
        {
            $attribute = new Attribute($id_attribute);
            if(!$this->seller->checkHasAttributeGroup($attribute->id_attribute_group,false))
                die($this->module->l('You do not have permission to delete this attribute image','attributes'));  
            else
            {
                if(file_exists(_PS_COL_IMG_DIR_.$id_attribute.'.jpg'))
                {
                    @unlink(_PS_COL_IMG_DIR_.$id_attribute.'.jpg');
                    $this->context->cookie->_success = $this->module->l('Deleted image successfully','attributes');
                    
                }    
                Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'attributes',array('editmp_attribute'=>1,'id_attribute'=>$id_attribute)));  
            }
              
        }
        if($this->context->cookie->_success)
        {
            $this->_success = $this->context->cookie->_success;
            $this->context->cookie->_success = '';
            $this->context->cookie->write();
        }
    }
    public function _submitSaveAttributeGroup()
    {
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages(false);
        $name_default = Tools::getValue('name_'.$id_lang_default);
        if(!$name_default)
            $this->_errors[] = $this->module->l('Name is required','attributes');
        $public_name_default = Tools::getValue('public_name_'.$id_lang_default);
        if(!$public_name_default)
            $this->_errors[] = $this->module->l('Public name is required','attributes');
        foreach($languages as $language)
        {
            if(($name = Tools::getValue('name_'.$language['id_lang'])) && !Validate::isCleanHtml($name))
                $this->_errors[] = $this->module->l('Name is not valid in','attributes').' '.$language['iso_code'];
            if(($public_name=Tools::getValue('public_name_'.$language['id_lang'])) && !Validate::isCleanHtml($public_name))
                $this->_errors[] = $this->module->l('Public name is not valid in','attributes').' '.$language['iso_code'];
            if(($url_name = Tools::getValue('url_name_'.$language['id_lang'])) && !Validate::isLinkRewrite($url_name))
                $this->_errors[] = $this->module->l('Url name is not valid in','attributes').' '.$language['iso_code'];
            if(($meta_title =Tools::getValue('meta_title_'.$language['id_lang'])) && !Validate::isCleanHtml($meta_title))
                $this->_errors[] = $this->module->l('Meta title name is not valid in','attributes').' '.$language['iso_code'];
        }
        $group_type= Tools::getValue('group_type');
        if(!in_array($group_type,array('select','radio','color')))
            $this->_errors[] = $this->module->l('Group type is not valid','attributes');
        if(($id_attribute_group = (int)Tools::getValue('id_attribute_group')) && Validate::isUnsignedId($id_attribute_group))
        {
            $attributeGroup = new AttributeGroup($id_attribute_group);
            if(!Validate::isLoadedObject($attributeGroup) || !$this->seller->checkHasAttributeGroup($id_attribute_group,false))
                $this->_errors[] = $this->module->l('Attribute group is not valid','attributes');
        }
        else    
            $attributeGroup = new AttributeGroup();
        if(!$this->_errors)
        {
            $attributeGroup->group_type = $group_type;
            foreach($languages as $language){
                $name = Tools::getValue('name_'.$language['id_lang']);
                $attributeGroup->name[$language['id_lang']] = $name ? : $name_default;
                $public_name = Tools::getValue('public_name_'.$language['id_lang']);
                $attributeGroup->public_name[$language['id_lang']] = $public_name? : $public_name_default;
            }
            if($attributeGroup->id)
            {
                if($attributeGroup->update())
                {
                    $this->context->cookie->_success = $this->module->l('Updated successfully','attributes');
                    $this->context->cookie->write();
                    Tools::redirect($this->context->link->getModuleLink($this->module->name,'attributes',array('list_group'=>1)));
                }
                else
                    $this->_errors[] = $this->module->l('An error occurred while updating the attribute group','attributes');
            }
            else
            {
                if($attributeGroup->add())
                {
                    $this->context->cookie->_success = $this->module->l('Added successfully','attributes');
                    if(Ets_mp_seller::addAttributeGroupToSeller($attributeGroup->id,$this->seller->id_customer))
                    {
                        $this->_success = $this->module->l('Updated successfully','attributes');
                        Tools::redirect($this->context->link->getModuleLink($this->module->name,'attributes',array('list_group'=>1)));
                    }
                }
                else
                    $this->_errors[] = $this->module->l('An error occurred while creating the attribute group','attributes');
            }
        }
    }
    public function _submitSaveAttribute()
    {
        $id_attribute = (int)Tools::getValue('id_attribute');
        $id_attribute_group = (int)Tools::getValue('id_attribute_group');
        if($id_attribute && Validate::isUnsignedId($id_attribute))
        {
            $attribute = new Attribute($id_attribute);
            if(!Validate::isLoadedObject($attribute) || !$this->seller->checkHasAttributeGroup($attribute->id_attribute_group,false))
                $this->_errors[] = $this->module->l('Attribute is not valid','attributes');
        }
        else
        {
            $attribute = new Attribute();
            if(!$id_attribute_group)
                $this->_errors[] = $this->module->l('Attribute group is required','attributes');
            elseif(($attributeGroup = new AttributeGroup($id_attribute_group)) && (!Validate::isLoadedObject($attributeGroup) || !$this->seller->checkHasAttributeGroup($id_attribute_group,false)))
                $this->_errors[] = $this->module->l('Attribute group is not valid','attributes');
        }
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $name_default = Tools::getValue('name_'.$id_lang_default);
        if(!$name_default)
            $this->_errors[] = $this->module->l('Name is required','attributes');
        foreach($languages as $language)
        {
            if(($name = Tools::getValue('name_'.$language['id_lang'])) && !Validate::isCleanHtml($name))
                $this->_errors[] = $this->module->l('Name is not valid in','attributes').' '.$language['iso_code'];
            if(($url_name = Tools::getValue('url_name_'.$language['id_lang'])) && !Validate::isLinkRewrite($url_name))
                $this->_errors[] = $this->module->l('Url name is not valid in','attributes').' '.$language['iso_code'];
            if(($meta_title = Tools::getValue('meta_title_'.$language['id_lang'])) && !Validate::isCleanHtml($meta_title))
                $this->_errors[] = $this->module->l('Meta title name is not valid in','attributes').' '.$language['iso_code'];
        }
        if(isset($_FILES['image']) && isset($_FILES['image']['name']) && $_FILES['image']['name'] && isset($_FILES['image']['size']) && $_FILES['image']['size'])
        {
            $this->module->validateFile($_FILES['image']['name'],$_FILES['image']['size'],$this->_errors, array('jpg', 'gif', 'jpeg', 'png'));
        }
        if(!$this->_errors)
        {
            foreach($languages as $language)
            {
                $name = Tools::getValue('name_'.$language['id_lang']);
                $attribute->name[$language['id_lang']] = $name? : $name_default;
            }
            $color = Tools::getValue('color');
            $attribute->color = Validate::isColor($color) ? $color :'';
            $attribute->id_attribute_group = (int)$id_attribute_group;
            $ok=false;
            if($attribute->id)
            {
                if($attribute->update())
                {
                    $this->context->cookie->_success = $this->module->l('Updated successfully','attributes');
                    $this->context->cookie->write();
                    $ok=true;
                }
                else
                    $this->_errors[] = $this->module->l('An error occurred while updating the attribute','attributes');
            }
            else
            {
                if($attribute->add())
                {
                    $this->context->cookie->_success = $this->module->l('Added successfully','attributes');
                    $ok=true;
                }
                else
                    $this->_errors[] = $this->module->l('Add failed','attributes');
            }
            if($ok)
            {
                if(isset($_FILES['image']) && isset($_FILES['image']['name']) && $_FILES['image']['name'])
                {
                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES['image']['name'], '.'), 1));
                    if(in_array($type, array('jpg', 'gif', 'jpeg', 'png')))
                    {
                        if(file_exists(_PS_COL_IMG_DIR_.$attribute->id.'.jpg'))
                        {
                            @unlink(_PS_COL_IMG_DIR_.$attribute->id.'.jpg');
                        }
                        $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
        				if(move_uploaded_file($_FILES['image']['tmp_name'], $temp_name))
                        {
                            ImageManager::resize($temp_name, _PS_COL_IMG_DIR_.$attribute->id.'.jpg', '40', '40', $type);
                        }
                        else    
                            $this->_errors[] = $this->module->l('An error occurred while uploading the logo','attributes');
                    }
                    
                }
                if(!$this->_errors)
                    Tools::redirect($this->context->link->getModuleLink($this->module->name,'attributes',array('viewGroup'=>1,'id_attribute_group'=>$attribute->id_attribute_group)));
                else
                    $this->context->cookie->_success ='';
            }
        }
    }
    public function initContent()
	{
		parent::initContent();
        $this->context->controller->addJqueryPlugin('colorpicker');
        $this->context->smarty->assign(
            array(
                'path' => $this->module->getBreadCrumb(),
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false,
                'html_content' => $this->_initContent(),
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/attributes.tpl');      
        else        
            $this->setTemplate('attributes_16.tpl'); 
    }
    public function _initContent()
    {
        $html = '';
        $id_attribute = (int)Tools::getValue('id_attribute');
        $id_attribute_group = (int)Tools::getValue('id_attribute_group');
        if(Tools::isSubmit('newGroup') || (Tools::isSubmit('editmp_attribute_group') && $id_attribute_group))
        {
            if(!Configuration::get('ETS_MP_SELLER_CREATE_ATTRIBUTE') && Tools::isSubmit('newGroup'))
                $html .= $this->module->displayWarning($this->module->l('You do not have permission to create new attribute group','attributes'));
            else
                $html .= $this->_renderFormAttributeGroup();
            $display_form= true;
        }elseif(Tools::isSubmit('list_group'))
        {
            $html .= $this->_renderAttributeGroupList();
            $display_form = Configuration::get('ETS_MP_SELLER_USER_GLOBAL_ATTRIBUTE') ? false :true;
        }
        if((Tools::isSubmit('newAttribute') && $id_attribute_group) || (Tools::isSubmit('editmp_attribute') && $id_attribute))
        {
            $html .= $this->_renderFormAttribute();
            $display_form =true;
        }
        elseif(Tools::isSubmit('viewGroup') && $id_attribute_group)
        {
            $html .= $this->_renderAttributeList($id_attribute_group);
            $display_form =true;
        }
        $this->context->smarty->assign(
            array(
                'html_content' => $html,
                'display_form' => isset($display_form) ? $display_form:true,
                'ets_errors' => $this->_errors ? $this->module->displayError($this->_errors) :false,
                'ets_success' => $this->_success ? $this->_success :false,
                'ets_seller' => $this->seller,
            )
        );
        return $this->module->displayTpl('attributes.tpl');
    }
    public function _renderAttributeGroupList()
    {
        $fields_list = array(
            'id_attribute_group' => array(
                'title' => $this->module->l('ID','attributes'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'name'=>array(
                'title' => $this->module->l('Name','attributes'),
                'type'=> 'text',
                'sort' => true,
                
                'filter' => true,
            ),
            'total_attribute'=>array(
                'title' => $this->module->l('Values','attributes'),
                'type' => 'text',
                'sort'=>true,
            ),
            'position' => array(
                'title' => $this->module->l('Position','attributes'),
                'type'=> 'text',
                'sort' => true,
                'filter' => true,
            ),
        );
        $show_resset = false;
        $filter = "";
        if(($id_attribute_group = trim(Tools::getValue('id_attribute_group'))) && !Tools::isSubmit('del'))
        {
            $show_resset = true;
            if(Validate::isUnsignedId($id_attribute_group))
                $filter .=' AND ag.id_attribute_group="'.(int)$id_attribute_group.'"';            
        }
        if(($name = trim(Tools::getValue('name'))) || $name!='')
        {
            if(Validate::isCleanHtml($name))
                $filter .=' AND agl.name like "%'.pSQL($name).'%"';
            $show_resset = true;
        }
        if(($position = trim(Tools::getValue('position'))) || $position!='')
        {
            if(Validate::isInt($position))
                $filter .=' AND ag.position="'.(int)$position.'"';
            $show_resset = true;
        }
        $sort = "";
        $sort_value = Tools::getValue('sort','id_attribute_group');
        $sort_type=Tools::getValue('sort_type');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'id_attribute_group':
                    $sort .='ag.id_attribute_group';
                    break;
                case 'name':
                    $sort .='agl.name';
                    break;
                case 'total_attribute':
                    $sort .='total_attribute';
                    break;
                case 'position':
                    $sort .='ag.position';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('acs','desc')))
                $sort .= ' '.trim($sort_type);
        }
        $page = (int)Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $totalRecords = (int)$this->seller->getAttributeGroups($filter,0,0,'',true);
        $paggination = new Ets_mp_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getModuleLink($this->module->name,'attributes',array('list_group'=>1,'page'=>'_page_')).$this->module->getFilterParams($fields_list,'mp_attribute_group');
        $paggination->limit =  (int)Tools::getValue('paginator_attributeGroup_select_limit',10);
        $paggination->num_links =5;
        $paggination->name ='attributeGroup';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $attribute_groups = $this->seller->getAttributeGroups($filter,$start,$paggination->limit,$sort,false);
        if($attribute_groups)
        {
            foreach($attribute_groups as &$attribute_group)
            {
                $attribute_group['child_view_url'] = $this->context->link->getModuleLink($this->module->name,'attributes',array('viewGroup'=>1,'id_attribute_group'=>$attribute_group['id_attribute_group']));
                if(!$attribute_group['id_customer'])
                    $attribute_group['action_edit'] = false;
            }
        }
        $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','attributes');
        $paggination->style_links = $this->module->l('links','attributes');
        $paggination->style_results = $this->module->l('results','attributes');
        $listData = array(
            'name' => 'mp_attribute_group',
            'actions' => array('view','edit','delete'),
            'currentIndex' => $this->context->link->getModuleLink($this->module->name,'attributes',array('list_group'=>1)).($paggination->limit!=10 ? '&paginator_attributeGroup_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getModuleLink($this->module->name,'attributes',array('list_group'=>1)),
            'identifier' => 'id_attribute_group',
            'show_toolbar' => true,
            'show_action' =>true,
            'title' => $this->module->l('Attributes','attributes'),
            'fields_list' => $fields_list,
            'field_values' => $attribute_groups,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list,'mp_attribute_group'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'show_add_new'=> Configuration::get('ETS_MP_SELLER_CREATE_ATTRIBUTE') && $this->seller->user_attribute!=1 ? true :false,
            'link_new' => $this->context->link->getModuleLink($this->module->name,'attributes',array('newGroup'=>1)),
            'sort_type' => $sort_type,
        );            
        return $this->module->renderList($listData);
    }
    public function _renderAttributeList($id_attribute_group)
    {
        $attributeGroup = new AttributeGroup($id_attribute_group,$this->context->language->id);
        $fields_list = array(
            'id_attribute' => array(
                'title' => $this->module->l('ID','attributes'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'name'=>array(
                'title' => $this->module->l('Value','attributes'),
                'type'=> 'text',
                'sort' => true,
                'filter' => true,
            ),
            'position' => array(
                'title' => $this->module->l('Position','attributes'),
                'type'=> 'text',
                'sort' => true,
                'filter' => true,
            ),
        );
        $show_resset = false;
        $filter = ' AND a.id_attribute_group="'.(int)$id_attribute_group.'"';
        if(($id_attribute =trim(Tools::getValue('id_attribute'))) && !Tools::isSubmit('del'))
        {
            $show_resset = true;
            if(Validate::isUnsignedId($id_attribute))
                $filter .=' AND a.id_attribute="'.(int)$id_attribute.'"';            
        }
        if(($name = trim(Tools::getValue('name'))) || $name!='')
        {
            if(Validate::isCleanHtml($name))
                $filter .=' AND al.name like "%'.pSQL($name).'%"';
            $show_resset = true;
        }
        if(($position = trim(Tools::getValue('position'))) || $position!='')
        {
            if(Validate::isInt($position))
                $filter .=' AND a.position="'.(int)$position.'"';
            $show_resset = true;
        }
        $sort = "";
        $sort_type=Tools::getValue('sort_type','desc');
        $sort_value = Tools::getValue('sort','id_attribute');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'id_attribute':
                    $sort .='a.id_attribute';
                    break;
                case 'name':
                    $sort .='al.name';
                    break;
                case 'position':
                    $sort .='a.position';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                $sort .= ' '.trim($sort_type);
        }
        $page = (int)Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $totalRecords = (int)$this->seller->getAttributes($filter,0,0,'',true);
        $paggination = new Ets_mp_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getModuleLink($this->module->name,'attributes',array('list_group'=>1,'page'=>'_page_')).$this->module->getFilterParams($fields_list,'mp_attribute');
        $paggination->limit =  (int)Tools::getValue('paginator_attribute_select_limit',10);
        $paggination->num_links =5;
        $paggination->name ='attribute';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $attributes = $this->seller->getAttributes($filter,$start,$paggination->limit,$sort,false);
        $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','attributes');
        $paggination->style_links = $this->module->l('links','attributes');
        $paggination->style_results = $this->module->l('results','attributes');
        $listData = array(
            'name' => 'mp_attribute',
            'actions' => $this->seller->checkHasAttributeGroup($id_attribute_group,false) ? array('view','delete'):array(),
            'currentIndex' => $this->context->link->getModuleLink($this->module->name,'attributes',array('viewGroup'=>1,'id_attribute_group'=>$id_attribute_group)).($paggination->limit!=10 ? '&paginator_attribute_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getModuleLink($this->module->name,'attributes',array('viewGroup'=>1,'id_attribute_group'=>$id_attribute_group)),
            'identifier' => 'id_attribute',
            'show_toolbar' => true,
            'show_action' =>true,
            'title' => $this->module->l('Attributes','attributes').' > '.$attributeGroup->name,
            'fields_list' => $fields_list,
            'field_values' => $attributes,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list,'mp_attribute'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'show_add_new'=> $this->seller->checkHasAttributeGroup($id_attribute_group,false) ? true : false,
            'link_back_to_list' => $this->context->link->getModuleLink($this->module->name,'attributes',array('list_group'=>1)),
            'link_new' => $this->context->link->getModuleLink($this->module->name,'attributes',array('newAttribute'=>1,'id_attribute_group'=>$id_attribute_group)),
            'sort_type' => $sort_type,
        );            
        return $this->module->renderList($listData);
    }
    public function _renderFormAttributeGroup()
    {
        if(($id_attribute_group = (int)Tools::getValue('id_attribute_group')) && Validate::isUnsignedId($id_attribute_group))
        {
            $attributeGroup = new AttributeGroup($id_attribute_group);
            if(Validate::isLoadedObject($attributeGroup) && !$this->seller->checkHasAttributeGroup($id_attribute_group,false))
                die($this->module->l('You do not have permission to config this attribute group','attributes'));
        }
        else
            $attributeGroup = new AttributeGroup();
        $languages = Language::getLanguages(true);
        $valueFieldPost= array();
        $valueFieldPost['id_attribute_group'] = $attributeGroup->id;
        $valueFieldPost['group_type'] = $attributeGroup->group_type;
        if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered'))
        {
            $valueFieldPost['layered_indexable'] = Ets_mp_product::getIndexableAttributeGroup($attributeGroup->id);
        }
        foreach(Language::getLanguages(true) as $language)
        {
            $valueFieldPost['name'][$language['id_lang']] = Tools::getValue('name_'.(int)$language['id_lang'],isset($attributeGroup->name[$language['id_lang']]) ? $attributeGroup->name[$language['id_lang']]:'');
            $valueFieldPost['public_name'][$language['id_lang']] = Tools::getValue('public_name_'.(int)$language['id_lang'],isset($attributeGroup->public_name[$language['id_lang']]) ? $attributeGroup->public_name[$language['id_lang']]:'');
            if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered'))
            {
                $facetedSearch = Ets_mp_product::getLanguageSearchAttributeGroup($attributeGroup->id,$language['id_lang']);
                $valueFieldPost['url_name'][$language['id_lang']] = isset($facetedSearch['url_name']) ? $facetedSearch['url_name'] :'';
                $valueFieldPost['meta_title'][$language['id_lang']] = isset($facetedSearch['meta_title']) ? $facetedSearch['meta_title']:'';
            }
        }
        $fields = array(
            array(
                'type' => 'text',
                'name' => 'name',
                'label' => $this->module->l('Name','attributes'),
                'lang' => true,
                'required' => true,
            ),
            array(
                'type' => 'text',
                'name' => 'public_name',
                'label' => $this->module->l('Public name','attributes'),
                'lang' => true,
                'required' => true,
            )
        );
        if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered'))
        {
            $fields2 = array(
                array(
                    'type' => 'text',
                    'name' =>'url_name',
                    'label' => $this->module->l('URL','attributes'),
                    'lang' => true,
                    'desc' => $this->module->l('When the Faceted Search module is enabled, you can get more detailed URLs by choosing the word that best represent this attribute. By default, PrestaShop uses the attribute\'s name, but you can change that setting using this field.','attributes'),
                ),
                array(
                    'type' => 'text',
                    'name'=> 'meta_title',
                    'label' => $this->module->l('Meta title','attributes'),
                    'lang' => true,
                    'desc' => $this->module->l('When the Faceted Search module is enabled, you can get more detailed page titles by choosing the word that best represent this attribute. By default, PrestaShop uses the attribute\'s name, but you can change that setting using this field.','attributes')
                ),
                array(
                    'type' => 'switch',
                    'name' =>'layered_indexable',
                    'label' => $this->module->l('Indexable'),
                    'desc' => $this->module->l('Use this attribute in URL generated by the Faceted Search module.','attributes'),
                    
                )
            );
            $fields = array_merge($fields,$fields2);
        }
        $fields[]=array(
            'type' =>'select',
            'name' => 'group_type',
            'label' => $this->module->l('Attribute type','attributes'),
            'values' => array(
                array(
                    'id' => 'select',
                    'name' => $this->module->l('Drop-down list','attributes')
                ),
                array(
                    'id' => 'radio',
                    'name' => $this->module->l('Radio buttons','attributes')
                ),
                array(
                    'id' => 'color',
                    'name' => $this->module->l('Color or texture','attributes')
                )
            ),
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
                'html_form' => $html_form,
                'id_attribute_group' => (int)$id_attribute_group,
                'link_cancel' => $this->context->link->getModuleLink($this->module->name,'attributes',array('list_group'=>1))
            )
        );
        return $this->module->displayTpl('attributes/attribute_group_form.tpl');    
    }
    public function _renderFormAttribute()
    {
        if(($id_attribute = (int)Tools::getValue('id_attribute')) && Validate::isUnsignedId($id_attribute))
        {
            $attribute = new Attribute($id_attribute);
            if(Validate::isLoadedObject($attribute) && !$this->seller->checkHasAttributeGroup($attribute->id_attribute_group,false))
                die($this->module->l('You do not have permission to config this attribute','attributes'));
        }
        else
            $attribute = new Attribute();
        if($id_attribute_group = (int)$attribute->id_attribute_group)
            $attributeGroup = new AttributeGroup($id_attribute_group,$this->context->language->id);
        else
        {
            $id_attribute_group = (int)Tools::getValue('id_attribute_group');
            $attributeGroup = new AttributeGroup($id_attribute_group,$this->context->language->id);
        }
        if(Validate::isLoadedObject($attributeGroup) && !$this->seller->checkHasAttributeGroup($attributeGroup->id,false))
            die($this->module->l('You do not have permission to config this attribute group','attributes'));
        $languages = Language::getLanguages(false);
        $valueFieldPost= array();
        foreach(Language::getLanguages(true) as $language)
        {
            $valueFieldPost['name'][$language['id_lang']] = Tools::getValue('name_'.(int)$language['id_lang'],isset($attribute->name[$language['id_lang']]) ? $attribute->name[$language['id_lang']]:'');
            if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered'))
            {
                $faceSearchAttribute = Ets_mp_product::getLanguageSearchAttribute($attribute->id,$language['id_lang']);
                $valueFieldPost['url_name'][$language['id_lang']] = isset($faceSearchAttribute['url_name']) ? $faceSearchAttribute['url_name']:'';
                $valueFieldPost['meta_title'][$language['id_lang']] = isset($faceSearchAttribute['meta_title']) ? $faceSearchAttribute['meta_title'] :'';
            }
        }
        $valueFieldPost['color'] = $attribute->color;
        if($attribute->id && file_exists(_PS_COL_IMG_DIR_.$attribute->id.'.jpg'))
            $valueFieldPost['image']=$this->module->getBaseLink().'/img/co/'.$attribute->id.'.jpg?time='.time();
        $fields = array(
            array(
                'type' => 'text',
                'name' => 'name',
                'required' => true,
                'label' => $this->module->l('Name','attributes'),
                'lang' => true,
            ),
        );
        if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered'))
        {
            $fields2 = array(
                array(
                    'type' => 'text',
                    'name' =>'url_name',
                    'label' => $this->module->l('URL','attributes'),
                    'lang' => true,
                    'desc' => $this->module->l('When the Faceted Search module is enabled, you can get more detailed URLs by choosing the word that best represent this attribute. By default, PrestaShop uses the attribute\'s name, but you can change that setting using this field.','attributes')
                ),
                array(
                    'type' => 'text',
                    'name'=> 'meta_title',
                    'label' => $this->module->l('Meta title','attributes'),
                    'lang' => true,
                    'desc' => $this->module->l('When the Faceted Search module is enabled, you can get more detailed page titles by choosing the word that best represent this attribute. By default, PrestaShop uses the attribute\'s name, but you can change that setting using this field.','attributes')
                )
            );
            $fields = array_merge($fields,$fields2);
        }
        if($attributeGroup->group_type=='color')
        {
            $fields2 = array(
                array(
                    'type' => 'color',
                    'name'=> 'color',
                    'label' => $this->module->l('Color','attributes'),
                ),
                array(
                    'type' => 'file',
                    'name' =>'image',
                    'link_del' => $this->context->link->getModuleLink($this->module->name,'attributes',array('id_attribute'=>$attribute->id,'deleteimage'=>1)),
                    'label' => $this->module->l('Texture','attributes'),
                ),
            );
            $fields = array_merge($fields,$fields2);
        }
        
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
                'html_form' => $html_form,
                'id_attribute' => (int)$id_attribute,
                'id_attribute_group' => $attributeGroup->id,
                'attributeGroup' => $attributeGroup,
                'link_cancel' => $this->context->link->getModuleLink($this->module->name,'attributes',array('viewGroup'=>1,'id_attribute_group'=>$attributeGroup->id))
            )
        );
        return $this->module->displayTpl('attributes/attribute_form.tpl');
    }
 }