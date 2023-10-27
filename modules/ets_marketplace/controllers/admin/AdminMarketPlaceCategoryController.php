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
 * Class AdminMarketPlaceCategoryController
 * @property \Ets_marketplace $module
 */
class AdminMarketPlaceCategoryController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->context= Context::getContext();
        $this->bootstrap = true;
    }
    public function init()
    {
       parent::init();
       if(Tools::isSubmit('change_enabled') && ($id_ets_mp_shop_category = Tools::getValue('id_ets_mp_shop_category')) && Validate::isUnsignedId($id_ets_mp_shop_category))
       {
            $errors = '';
            $shopCategroy = new Ets_mp_shop_category($id_ets_mp_shop_category);
            if(!Validate::isLoadedObject($shopCategroy))
                $errors = $this->l('Shop category is not valid');
            else
            {
                $change_enabled = (int)Tools::getValue('change_enabled');
                $shopCategroy->active = $change_enabled ? 1:0;
                if($shopCategroy->update())
                {
                    if($change_enabled)
                    {
                        die(
                            json_encode(
                                array(
                                    'href' =>$this->context->link->getAdminLink('AdminMarketPlaceCategory').'&id_ets_mp_shop_category='.(int)$id_ets_mp_shop_category.'&change_enabled=0&field=active',
                                    'title' => $this->l('Click to disable'),
                                    'success' => $this->l('Updated successfully'),
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
                                    'href' => $this->context->link->getAdminLink('AdminMarketPlaceCategory').'&id_ets_mp_shop_category='.(int)$id_ets_mp_shop_category.'&change_enabled=1&field=active',
                                    'title' => $this->l('Click to enable'),
                                    'success' => $this->l('Updated successfully'),
                                    'enabled' => 0,
                                )
                            )  
                        );
                    }
                }else
                    $errors = $this->l('An error occurred while saving the shop category');
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
    }
    public function initContent()
    {
        parent::initContent();
    }
    public function renderList()
    {
        $this->module->getContent();
        $this->context->smarty->assign(
            array(
                'ets_mp_body_html'=> $this->renderCategory(),
            )
        );
        $html ='';
        if($this->context->cookie->success_message)
        {
            $html .= $this->module->displayConfirmation($this->context->cookie->success_message);
            $this->context->cookie->success_message ='';
        }
        if($this->module->_errors)
                $html .= $this->module->displayError($this->module->_errors);
        return $html.$this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'admin.tpl');
    }
    public function renderCategory()
    {
        if(Tools::isSubmit('del') && ($id_category = Tools::getValue('id_ets_mp_shop_category')) && Validate::isUnsignedId($id_category))
        {
            $category = new Ets_mp_shop_category($id_category);
            if(Validate::isLoadedObject($category) && $category->delete())
            {
                $this->context->cookie->success_message = $this->l('Deleted shop category successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceCategory'));
            }
            else
                $this->module->_errors[] = $this->l('An error occurred while deleting the category');
        }
        if(Tools::isSubmit('addnew') || (Tools::isSubmit('editmp_shop_category') && ($id_category = Tools::getValue('id_ets_mp_shop_category')) && Validate::isUnsignedId($id_category) ))
        {
            return $this->renderFormShopCategory(isset($id_category) ? $id_category :0);
        }
        $fields_list = array(
            'id_ets_mp_shop_category' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'name' => array(
                'title' => $this->l('Category name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag'=>false,
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'type' => 'active',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'active',
                    'value' => 'title',
                    'list' => array(
                        array(
                            'active' => 0,
                            'title' => $this->l('Disabled')
                        ),
                        array(
                            'active' => 1,
                            'title' => $this->l('Enabled')
                        )
                    )
                )
            )
        );
        //Filter
        $show_resset = false;
        $filter = "";
        if(Tools::isSubmit('ets_mp_submit_mp_shop_category'))
        {
            if(($id_ets_mp_shop_category = Tools::getValue('id_ets_mp_shop_category')))
            {
                if(Validate::isUnsignedId($id_ets_mp_shop_category))
                    $filter .=' AND c.id_ets_mp_shop_category='.(int)$id_ets_mp_shop_category;
                $show_resset = true;
            }
            if(($name = Tools::getValue('name')))
            {
                if(Validate::isGenericName($name))
                    $filter .= ' AND cl.name like "%'.pSQL($name).'%"';
                $show_resset = true;
            }
            if(($active = Tools::getValue('active')) || $active!=='')
            {
                if(Validate::isInt($active))
                    $filter .= ' AND c.active='.(int)$active;
                $show_resset = true;
            }
        }
        //Sort
        $sort = "";
        $sort_type=Tools::getValue('sort_type','desc');
        $sort_value = Tools::getValue('sort','date_add');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'id_ets_mp_shop_category':
                    $sort .='c.id_ets_mp_shop_category';
                    break;
                case 'name':
                    $sort .='cl.name';
                    break;
                case 'active':
                    $sort .=' c.active';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                $sort .= ' '.trim($sort_type);
        }
        //Paggination
        $page = (int)Tools::getValue('page');
        if($page <=0)
            $page = 1;
        $totalRecords = (int) Ets_mp_shop_category::getShopCategories($filter,0,0,'',true);
        $paggination = new Ets_mp_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getAdminLink('AdminMarketPlaceCategory').'&page=_page_'.$this->module->getFilterParams($fields_list,'mp_shop_category');
        $paggination->limit =  (int)Tools::getValue('paginator_category_select_limit',20);
        $paggination->name ='category';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $shop_categories = Ets_mp_shop_category::getShopCategories($filter, $start,$paggination->limit,$sort,false);
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $listData = array(
            'name' => 'mp_shop_category',
            'actions' => array('view','delete'),
            'icon' => 'fa fa-dollar',
            'currentIndex' => $this->context->link->getAdminLink('AdminMarketPlaceCategory').($paggination->limit!=20 ? '&paginator_category_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getAdminLink('AdminMarketPlaceCategory'),
            'identifier' => 'id_ets_mp_shop_category',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->l('Shop categories'),
            'fields_list' => $fields_list,
            'field_values' => $shop_categories,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list,'mp_shop_category'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'show_add_new'=> true,
            'link_new' => $this->context->link->getAdminLink('AdminMarketPlaceCategory').'&addnew=1',
            'sort_type' => $sort_type,
        );
        return $this->module->renderList($listData);
    }
    public function renderFormShopCategory($id_category){
        if(Tools::isSubmit('saveShopCategory'))
        {
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            $name_default = Tools::getValue('name_'.$id_lang_default);
            if(!$name_default)
                $this->module->_errors[] = $this->l('Name is required');
            $active = Tools::getValue('active');
            if(!Validate::isInt($active))
                $this->module->_errors[] = $this->l('Status is not valid');
            $languages = Language::getLanguages(false);
            $name = array();
            foreach($languages as $language)
            {
                $name[$language['id_lang']] = Tools::getValue('name_'.$language['id_lang']);
                if($name[$language['id_lang']] && !Validate::isGenericName($name[$language['id_lang']]))
                {
                    $this->module->_errors[] = sprintf($this->l('Name is not valid in %s'),$language['iso_code']);
                }
            }
            if(!$this->module->_errors)
            {
                if($id_category && Validate::isUnsignedId($id_category))
                    $shopCategory = new Ets_mp_shop_category($id_category);
                else
                    $shopCategory = new Ets_mp_shop_category();
                $shopCategory->active = (int)$active;
                foreach($languages as $language)
                {
                    $shopCategory->name[$language['id_lang']] = $name[$language['id_lang']] ?: $name[$id_lang_default];
                }
                if($shopCategory->id)
                {
                    if($shopCategory->update())
                    {
                        $this->context->cookie->success_message = $this->l('Updated shop category successfully');
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceCategory'));
                    }
                    else
                        $this->module->_errors[] = $this->l('An error occurred while saving the shop category');

                }
                elseif($shopCategory->add())
                {
                    $this->context->cookie->success_message = $this->l('Added shop category successfully');
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceCategory'));
                }
                else
                    $this->module->_errors[] = $this->l('An error occurred while saving the shop category');
            }

        }
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => Tools::isSubmit('addnew') ? $this->l('Add new shop category'): $this->l('Edit shop category'),
                    'icon' =>'icon-shop-category',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Category name'),
                        'name' => 'name',
                        'required' => true,
                        'lang' => true,
                    ),
                    array(
                        'type' =>'switch',
                        'label' => $this->l('Status'),
                        'name' => 'active',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'buttons' => array(
                    array(
                        'href' => $this->context->link->getAdminLink('AdminMarketPlaceCategory', true),
                        'icon'=>'process-icon-cancel',
                        'title' => $this->l('Cancel'),
                    )
                ),
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this->module;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'saveShopCategory';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminMarketPlaceCategory', false).(Tools::isSubmit('addnew') ? '&addnew=1':'&editmp_shop_category=1&id_ets_mp_shop_category='.(int)$id_category);
        $helper->token = Tools::getAdminTokenLite('AdminMarketPlaceCategory');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),

            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'fields_value' => $this->getShopCategoryFieldsValues($id_category),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => _PS_IMG_.'mp_seller/',
            'link' => $this->context->link,
            'cancel_url' => $this->context->link->getAdminLink('AdminMarketPlaceCategory', true),
        );
        $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_ets_mp_shop_category');
        return $helper->generateForm(array($fields_form));
    }
    public function getShopCategoryFieldsValues($id_category)
    {
        if($id_category)
            $category = new Ets_mp_shop_category($id_category);
        else
            $category = new Ets_mp_shop_category();
        $fields = array();
        $fields['active'] = Tools::getValue('active',$category->active);
        $fields['id_ets_mp_shop_category'] = $category->id;
        $languages = Language::getLanguages(false);
        if($languages)
        {
            foreach($languages as $language)
            {
                $fields['name'][$language['id_lang']] = Tools::getValue('name_'.$language['id_lang'],isset($category->name[$language['id_lang']]) ? $category->name[$language['id_lang']] :'');
            }
        }
        return $fields;
    }
}