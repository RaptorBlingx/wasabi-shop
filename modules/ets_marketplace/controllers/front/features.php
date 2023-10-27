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
 * Class Ets_MarketPlaceFeaturesModuleFrontController
 * @property \Ets_mp_seller $seller;
 * @property \Ets_marketplace $module;
 */
class Ets_MarketPlaceFeaturesModuleFrontController extends ModuleFrontController
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
        if(!$this->context->customer->isLogged() || !($this->seller = $this->module->_getSeller(true)) )
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->module->_checkPermissionPage($this->seller,'attributes'))
            die($this->module->l('You do not have permission to access page','features'));
        if(!$this->module->_use_feature)
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->_checkAccess())
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'features',array('list_feature'=>1)));
        if(Tools::isSubmit('changeUserFeature'))
        {
            $user_feature = (int)Tools::getValue('user_feature');
            $this->seller->user_feature = (int)$user_feature;
            if($this->seller->update())
            {
                die(
                    json_encode(
                        array(
                            'success' => $this->module->l('Updated successfully','features'),
                        )
                    )
                );
            }

        }
        if(($id_feature = (int)Tools::getValue('id_feature')) && $id_feature && !Tools::isSubmit('ets_mp_submit_mp_feature') && !$this->seller->checkHasFeature($id_feature,Tools::isSubmit('viewFeature') ? true:false ))
            die($this->module->l('You do not permission to config feature','features'));
        if(($id_feature_value = Tools::getValue('id_feature_value')) && Validate::isUnsignedId($id_feature_value)  && !Tools::isSubmit('ets_mp_submit_mp_feature_value'))
        {
            $featureValue = new FeatureValue($id_feature_value);
            if(!$this->seller->checkHasFeature($featureValue->id_feature,false))
                die($this->module->l('You do not permission to config feature value','features'));
        }
        if(Tools::isSubmit('submitSaveFeature'))
            $this->_submitSaveFeature();
        if(Tools::isSubmit('submitSaveFeatureValue'))
        {
            $this->_submitSaveFeatureValue();
        }
        if(Tools::isSubmit('del') && $id_feature_value)
        {
            $featureValue = new FeatureValue($id_feature_value);
            
            if(!Validate::isLoadedObject($featureValue) || !$this->seller->checkHasFeature($featureValue->id_feature,false))
                $this->_errors[] = $this->module->l('Feature value is not valid','features');
            elseif($featureValue->delete())
            {
                $this->context->cookie->_success = $this->module->l('Delete successfully','features');
                Tools::redirect($this->context->link->getModuleLink($this->module->name,'features',array('viewFeature'=>1,'id_feature'=>$featureValue->id_feature)));
            }
            else
                $this->_errors[] = $this->module->l('An error occurred while deleting the feature value','features');
        }
        elseif(Tools::isSubmit('del') && $id_feature)
        {
            $feature = new Feature($id_feature);
            if(!Validate::isLoadedObject($feature) || !$this->seller->checkHasFeature($id_feature,false))
                $this->_errors[] = $this->module->l('Feature is not valid','features');
            elseif($feature->delete())
            {
                $this->context->cookie->_success = $this->module->l('Deleted successfully','features');
                Tools::redirect($this->context->link->getModuleLink($this->module->name,'features',array('list_feature'=>1)));
            }
            else
                $this->_errors[] = $this->module->l('An error occurred while deleting the Feature','features'); 
        }
        if($this->context->cookie->_success)
        {
            $this->_success = $this->context->cookie->_success;
            $this->context->cookie->_success = '';
            $this->context->cookie->write();
        }
    }
    public function _checkAccess()
    {
        $id_feature = (int)Tools::getValue('id_feature');
        $id_feature_value = (int)Tools::getValue('id_feature_value');
        $res = !Tools::isSubmit('list_feature');
        $res &= !Tools::isSubmit('newFeature');
        $res &= !(Tools::isSubmit('editmp_feature') && $id_feature);
        $res &= !(Tools::isSubmit('del') && $id_feature);
        $res &= !(Tools::isSubmit('del') && $id_feature_value);
        $res &= !(Tools::isSubmit('viewFeature') && $id_feature);
        $res &= !(Tools::isSubmit('newFeatureValue') && (int)$id_feature);
        $res &= !(Tools::isSubmit('editmp_feature_value') && $id_feature_value);
        return !$res;
    }
    public function initContent()
	{
		parent::initContent();
        $this->context->smarty->assign(
            array(
                'path' => $this->module->getBreadCrumb(),
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false,
                'html_content' => $this->_initContent(),
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/features.tpl');      
        else        
            $this->setTemplate('features_16.tpl'); 
    }
    public function _initContent()
    {
        $html = '';
        $id_feature = (int)Tools::getValue('id_feature');
        $id_feature_value =(int)Tools::getValue('id_feature_value');
        if(Tools::isSubmit('newFeature') || (Tools::isSubmit('editmp_feature') && $id_feature && Validate::isUnsignedId($id_feature)))
        {
            if(!Configuration::get('ETS_MP_SELLER_CREATE_FEATURE') && Tools::isSubmit('newFeature'))
                $html .= $this->module->l('You do not have permission to create new feature','features');
            else
                $html .= $this->_renderFormFeature();
            $display_form = true;
        }
        elseif(Tools::isSubmit('list_feature'))
        {
            $html = $this->_renderListFeatures();
            $display_form = Configuration::get('ETS_MP_SELLER_USER_GLOBAL_FEATURE') ? false :true;
        }
        if((Tools::isSubmit('newFeatureValue')&& $id_feature) || (Tools::isSubmit('editmp_feature_value') && $id_feature_value && Validate::isUnsignedId($id_feature_value)))
        {
            $html .= $this->_renderFormFeatureValue();
            $display_form = true;
        }
        elseif(Tools::isSubmit('viewFeature') && $id_feature)
        {
            $html .= $this->_renderListFeatureValues($id_feature);
            $display_form = true;
        }
        if(Configuration::get('ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT'))
            $product_types = explode(',',Configuration::get('ETS_MP_SELLER_PRODUCT_TYPE_SUBMIT'));
        else
            $product_types = array();
        $this->context->smarty->assign(
            array(
                'html_content' => $html,
                'ets_errors' => $this->_errors ? $this->module->displayError($this->_errors) :false,
                'ets_success' => $this->_success ? $this->_success :false,
                'product_types' => $product_types,
                'display_form' => $display_form,
                'ets_seller' => $this->seller,
            )
        );
        return $this->module->displayTpl('features.tpl');
    }
    public function _renderListFeatures()
    {
        $fields_list = array(
            'id_feature' => array(
                'title' => $this->module->l('ID','features'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'name'=>array(
                'title' => $this->module->l('Name','features'),
                'type'=> 'text',
                'sort' => true,
                
                'filter' => true,
            ),
            'total_featuresvalue'=>array(
                'title' => $this->module->l('Values','features'),
                'type' => 'text',
                'sort'=>true,
            ),
            'position' => array(
                'title' => $this->module->l('Position','features'),
                'type'=> 'text',
                'sort' => true,
                'filter' => true,
            ),
        );
        $show_resset = false;
        $filter = "";
        if(($id_feature = trim(Tools::getValue('id_feature'))) && !Tools::isSubmit('del'))
        {
            $show_resset = true;
            if(Validate::isInt($id_feature))
                $filter .=' AND f.id_feature="'.(int)$id_feature.'"';            
        }
        if(($name = trim(Tools::getValue('name'))) || $name !='')
        {
            if(Validate::isCleanHtml($name))
                $filter .=' AND fl.name like "%'.pSQL($name).'%"';
            $show_resset = true;
        }
        if(($position = trim(Tools::getValue('position'))) || $position!='')
        {
            if(Validate::isInt($position))
                $filter .=' AND f.position="'.(int)$position.'"';
            $show_resset = true;
        }
        $sort = "";
        $sort_type=Tools::getValue('sort_type','desc');
        $sort_value = Tools::getValue('sort','id_feature');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'id_feature':
                    $sort .='f.id_feature';
                    break;
                case 'name':
                    $sort .='fl.name';
                    break;
                case 'total_featuresvalue':
                    $sort .='total_featuresvalue';
                    break;
                case 'position':
                    $sort .='f.position';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('acs','desc')))
                $sort .= ' '.trim($sort_type);
        }
        $page = (int)Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $totalRecords = (int)$this->seller->getFeatures($filter,0,0,'',true);
        $paggination = new Ets_mp_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getModuleLink($this->module->name,'features',array('list_feature'=>1,'page'=>'_page_')).$this->module->getFilterParams($fields_list,'mp_feature');
        $paggination->limit =  (int)Tools::getValue('paginator_features_select_limit',10);
        $paggination->name ='features';
        $paggination->num_links =5;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $features = $this->seller->getFeatures($filter,$start,$paggination->limit,$sort,false);
        if($features)
        {
            foreach($features as &$feature)
            {
                $feature['child_view_url'] = $this->context->link->getModuleLink($this->module->name,'features',array('viewFeature'=>1,'id_feature'=>$feature['id_feature']));
                if(!$feature['id_customer'])
                    $feature['action_edit'] = false;
            }
        }
        $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','features');
        $paggination->style_links = $this->module->l('links','features');
        $paggination->style_results = $this->module->l('results','features');
        $listData = array(
            'name' => 'mp_feature',
            'actions' => array('view','edit','delete'),
            'currentIndex' => $this->context->link->getModuleLink($this->module->name,'features',array('list_feature'=>1)).($paggination->limit!=10 ? '&paginator_features_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getModuleLink($this->module->name,'features',array('list_feature'=>1)),
            'identifier' => 'id_feature',
            'show_toolbar' => true,
            'show_action' =>true,
            'title' => $this->module->l('Features','features'),
            'fields_list' => $fields_list,
            'field_values' => $features,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list,'mp_feature'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'show_add_new'=> Configuration::get('ETS_MP_SELLER_CREATE_FEATURE') && $this->seller->user_feature!=1 ? true :false,
            'link_new' => $this->context->link->getModuleLink($this->module->name,'features',array('newFeature'=>1)),
            'sort_type' => $sort_type,
        );            
        return $this->module->renderList($listData);
    }
    public function _renderFormFeature()
    {
        if(($id_feature = (int)Tools::getValue('id_feature')) && Validate::isUnsignedId($id_feature))
        {
            $feature = new Feature($id_feature);
            if(Validate::isLoadedObject($feature) && !$this->seller->checkHasFeature($id_feature,false))
                die($this->module->l('You do not permission to config feature','features'));
        }
        else
            $feature = new Feature();
        $languages = Language::getLanguages(true);
        $valueFieldPost= array();
        $valueFieldPost['id_feature'] = $feature->id;
        if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered'))
        {
            $valueFieldPost['layered_indexable'] = Ets_mp_product::getIndexAbleFeature($feature->id);
        }
        foreach(Language::getLanguages(true) as $language)
        {
            $valueFieldPost['name'][$language['id_lang']] = Tools::getValue('name_'.(int)$language['id_lang'],isset($feature->name[$language['id_lang']]) ? $feature->name[$language['id_lang']] :'');
            if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered'))
            {
                $searchFeature = Ets_mp_product::getLanguageSearchFeature($feature->id,$language['id_lang']);
                $url_name = $searchFeature && isset($searchFeature['url_name']) ? $searchFeature['url_name']:'';
                $meta_title = $searchFeature && isset($searchFeature['meta_title']) ? $searchFeature['meta_title']:'';
                $valueFieldPost['url_name'][$language['id_lang']] = Tools::getValue('url_name_'.$language['id_lang'],$url_name); 
                $valueFieldPost['meta_title'][$language['id_lang']] = Tools::getValue('meta_title_'.$language['id_lang'],$meta_title);
            }
        }
        $fields = array(
            array(
                'type' => 'text',
                'name' => 'name',
                'label' => $this->module->l('Name','attributes'),
                'desc' => $this->module->l('Your name for this attribute. Invalid characters: <>;=#{}','attributes'),
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
                    'desc' => $this->module->l('When the Faceted Search module is enabled, you can get more detailed URLs by choosing the word that best represent this feature. By default, PrestaShop uses the feature\'s name, but you can change that setting using this field.','features'),
                ),
                array(
                    'type' => 'text',
                    'name'=> 'meta_title',
                    'label' => $this->module->l('Meta title','attributes'),
                    'lang' => true,
                    'desc' => $this->module->l('When the Faceted Search module is enabled, you can get more detailed page titles by choosing the word that best represent this feature. By default, PrestaShop uses the feature\'s name, but you can change that setting using this field.','features')
                ),
                array(
                    'type' => 'switch',
                    'name' =>'layered_indexable',
                    'label' => $this->module->l('Indexable','features'),
                    'desc' => $this->module->l('Use this attribute in URL generated by the Faceted Search module.','features')
                )
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
                'id_feature' => (int)$id_feature,
                'link_cancel' => $this->context->link->getModuleLink($this->module->name,'features',array('list_feature'=>1))
            )
        );
        return $this->module->displayTpl('feature_form.tpl');    
    }
    public function _submitSaveFeature()
    {
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages(false);
        $name_default = Tools::getValue('name_'.$id_lang_default);
        if(!$name_default)
            $this->_errors[] = $this->module->l('Name is required','features');
        foreach($languages as $language)
        {
            if(($name = Tools::getValue('name_'.$language['id_lang'])) && !Validate::isCleanHtml($name))
                $this->_errors[] = $this->module->l('Name is not valid in','features').' '.$language['iso_code'];
            if(($url_name = Tools::getValue('url_name_'.$language['id_lang'])) && !Validate::isLinkRewrite($url_name))
                $this->_errors[] = $this->module->l('Url name is not valid in','features').' '.$language['iso_code'];
            if(($meta_title =Tools::getValue('meta_title_'.$language['id_lang'])) && !Validate::isCleanHtml($meta_title))
                $this->_errors[] = $this->module->l('Meta title name is not valid in','features').' '.$language['iso_code'];
        }
        if(($id_feature = Tools::getValue('id_feature')) && Validate::isUnsignedId($id_feature))
        {
            $feature = new Feature($id_feature);
            if(!Validate::isLoadedObject($feature) || !$this->seller->checkHasFeature($id_feature))
                $this->_errors[] = $this->module->l('Feature is not valid','features');
        }
        else    
            $feature = new Feature();
        
        if(!$this->_errors)
        {
            foreach($languages as $language){
                $name = Tools::getValue('name_'.$language['id_lang']);
                $feature->name[$language['id_lang']] =  $name? $name : $name_default;
            }
            if($feature->id)
            {
                if($feature->update())
                {
                    $this->context->cookie->_success = $this->module->l('Updated successfully','features');
                    $this->context->cookie->write();
                    Tools::redirect($this->context->link->getModuleLink($this->module->name,'features',array('list_feature'=>1)));
                }
                else
                    $this->_errors[] = $this->module->l('An error occurred while updating the feature','features');
            }
            else
            {
                if($feature->add())
                {
                    $this->context->cookie->_success = $this->module->l('Added successfully','features');
                    $this->seller->addFeature($feature->id);
                    $this->_success = $this->module->l('Updated successfully','features');
                    Tools::redirect($this->context->link->getModuleLink($this->module->name,'features',array('list_feature'=>1)));
                }
                else
                    $this->_errors[] = $this->module->l('An error occurred while creating the feature','features');
            }
        }
    }
    public function _renderListFeatureValues($id_feature)
    {
        $feature = new Feature($id_feature,$this->context->language->id);
        $fields_list = array(
            'id_feature_value' => array(
                'title' => $this->module->l('ID','features'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'value'=>array(
                'title' => $this->module->l('Value','features'),
                'type'=> 'text',
                'sort' => true,
                'filter' => true,
            ),
        );
        $show_resset = false;
        $filter = ' AND fv.id_feature= "'.(int)$id_feature.'"';
        if(($id_feature_value = trim(Tools::getValue('id_feature_value'))) && !Tools::isSubmit('del'))
        {
            $show_resset = true;
            if(Validate::isInt($id_feature_value))
                $filter .=' AND fv.id_feature_value="'.(int)$id_feature_value.'"';            
        }
        if(($value = trim(Tools::getValue('value'))) || $value!='')
        {
            if(Validate::isCleanHtml($value))
                $filter .=' AND fvl.value like "%'.pSQL($value).'%"';
            $show_resset = true;
        }
        $sort = "";
        $sort_type=Tools::getValue('sort_type','desc');
        $sort_value = Tools::getValue('sort','id_feature_value');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'id_feature_value':
                    $sort .='fv.id_feature_value';
                    break;
                case 'value':
                    $sort .='fvl.value';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('acs','desc')))
                $sort .= ' '.trim($sort_type);
        }
        $page = (int)Tools::getValue('page');
        if($page<=0)
            $page=1;
        $totalRecords = (int)$this->seller->getFeatureValues($filter,0,0,'',true);
        $paggination = new Ets_mp_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getModuleLink($this->module->name,'features',array('viewFeature'=>1,'id_feature'=>$id_feature,'page'=>'_page_')).$this->module->getFilterParams($fields_list,'mp_feature_value');
        $paggination->limit =  (int)Tools::getValue('paginator_featurevalues_select_limit',10);
        $paggination->num_links = 5;
        $paggination->name ='featurevalues';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $feature_values = $this->seller->getFeatureValues($filter,$start,$paggination->limit,$sort,false);
        if($feature_values)
        {
            foreach($feature_values as &$feature_value)
                if(!$feature_value['id_customer'])
                    $feature_value['action_edit']=false;
        }
        $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','features');
        $paggination->style_links = $this->module->l('links','features');
        $paggination->style_results = $this->module->l('results','features');
        $listData = array(
            'name' => 'mp_feature_value',
            'actions' => $this->seller->checkHasFeature($id_feature,false) ? array('view','delete'):array(),
            'currentIndex' => $this->context->link->getModuleLink($this->module->name,'features',array('viewFeature'=>1,'id_feature'=>$id_feature)).($paggination->limit!=10 ? '&paginator_featurevalues_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getModuleLink($this->module->name,'features',array('viewFeature'=>1,'id_feature'=>$id_feature)),
            'identifier' => 'id_feature_value',
            'show_toolbar' => true,
            'show_action' =>true,
            'title' => $this->module->l('Feature','features').' > '.$feature->name,
            'fields_list' => $fields_list,
            'field_values' => $feature_values,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list,'mp_feature_value'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'show_add_new'=> Configuration::get('ETS_MP_SELLER_CREATE_FEATURE') && $this->seller->user_feature!=1 && $this->seller->checkHasFeature($id_feature,false) ? true :false,
            'link_back_to_list' => $this->context->link->getModuleLink($this->module->name,'features',array('list_feature'=>1)),
            'link_new' => $this->context->link->getModuleLink($this->module->name,'features',array('newFeatureValue'=>1,'id_feature'=>$id_feature)),
            'sort_type' => $sort_type,
        );            
        return $this->module->renderList($listData);
    }
    public function _renderFormFeatureValue()
    {
        if(($id_feature_value = (int)Tools::getValue('id_feature_value')) && Validate::isUnsignedId($id_feature_value))
        {
            $featurevalue= new FeatureValue($id_feature_value);
            if(Validate::isLoadedObject($featurevalue) && !$this->seller->checkHasFeature($featurevalue->id_feature,false))
                die($this->module->l('You do not permission to config feature value','features'));
        }
        else
            $featurevalue = new FeatureValue();
        $languages = Language::getLanguages(true);
        $valueFieldPost= array();
        $valueFieldPost['id_feature_value'] = $featurevalue->id;
        foreach(Language::getLanguages(true) as $language)
        {
            $valueFieldPost['value'][$language['id_lang']] = Tools::getValue('value_'.(int)$language['id_lang'],isset($featurevalue->value[$language['id_lang']]) ? $featurevalue->value[$language['id_lang']]:'');
            if(Module::isEnabled('ps_facetedsearch') || Module::isEnabled('blocklayered'))
            {
                $searchFeatureValue = Ets_mp_product::getLanguageSearchFeatureValue($featurevalue->id,$language['id_lang']);
                $url_name = $searchFeatureValue && isset($searchFeatureValue['url_name']) ? $searchFeatureValue['url_name']:'';
                $meta_title = $searchFeatureValue && isset($searchFeatureValue['meta_title']) ? $searchFeatureValue['meta_title']:'';
                $valueFieldPost['url_name'][$language['id_lang']] = Tools::getValue('url_name_'.$language['id_lang'],$url_name); 
                $valueFieldPost['meta_title'][$language['id_lang']] = Tools::getValue('meta_title_'.$language['id_lang'],$meta_title);
            }
        }
        $fields = array(
            array(
                'type' => 'text',
                'name' => 'value',
                'label' => $this->module->l('Value','features'),
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
                    'label' => $this->module->l('Url','attributes'),
                    'lang' => true,
                    'desc' => $this->module->l('When the Faceted Search module is enabled, you can get more detailed URLs by choosing the word that best represent this feature\'s value. By default, PrestaShop uses the value\'s name, but you can change that setting using this field.','features'),
                ),
                array(
                    'type' => 'text',
                    'name'=> 'meta_title',
                    'label' => $this->module->l('Meta title','attributes'),
                    'lang' => true,
                    'desc' => $this->module->l('When the Faceted Search module is enabled, you can get more detailed page titles by choosing the word that best represent this feature\'s value. By default, PrestaShop uses the value\'s name, but you can change that setting using this field.','features')
                )
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
        $id_feature = (int)Tools::getValue('id_feature');
        $feature = new Feature($id_feature,$this->context->language->id);
        $this->context->smarty->assign(
            array(
                'html_form' => $html_form,
                'id_feature_value' => (int)$id_feature_value,
                'feature' => $feature,
                'link_cancel' => $this->context->link->getModuleLink($this->module->name,'features',array('viewFeature'=>1,'id_feature'=>$id_feature))
            )
        );
        return $this->module->displayTpl('feature_value_form.tpl');
    }
    public function _submitSaveFeatureValue()
    {
        
        if(($id_feature_value = (int)Tools::getValue('id_feature_value')) && Validate::isUnsignedId($id_feature_value))
        {
            $featureValue = new FeatureValue($id_feature_value);
            if(!Validate::isLoadedObject($featureValue) || !$this->seller->checkHasFeature($featureValue->id_feature,false))
                $this->_errors[] = $this->module->l('Feature value is not valid','features');      
        }
        else
        {
            $featureValue = new FeatureValue();
            if(!($id_feature  = (int)Tools::getValue('id_feature')))
            {
                $this->_errors[] = $this->module->l('Feature is required','features');
            }
            elseif(($feature = new Feature($id_feature)) && (!Validate::isLoadedObject($feature) || !$this->seller->checkHasFeature($id_feature)))
                $this->_errors[] = $this->module->l('Feature is not valid','features');
            $featureValue->id_feature = $id_feature;
        }
        $id_lang_default =(int)Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages(false);
        $value_default = Tools::getValue('value_'.$id_lang_default);
        if(!$value_default)
            $this->_errors[] = $this->module->l('Value is required','features');
        if($languages)
        {
            foreach($languages as $language)
            {
                
                if(($value = Tools::getValue('value_'.$language['id_lang'])) && !Validate::isCleanHtml($value))
                    $this->_errors[]= $this->module->l('Value is not valid in','features').' '.$language['iso_code'];
                if(($url_name = Tools::getValue('url_name_'.$language['id_lang'])) && !Validate::isLinkRewrite($url_name))
                    $this->_errors[]= $this->module->l('Url is not valid in','features').' '.$language['iso_code'];
                if(($meta_title = Tools::getValue('meta_title_'.$language['id_lang'])) && !Validate::isCleanHtml($meta_title))
                    $this->_errors[]= $this->module->l('Meta title is not valid in','features').' '.$language['iso_code'];    
            }
        }
        if(!$this->_errors)
        {
            
            foreach($languages as $language)
            {
                $value = Tools::getValue('value_'.$language['id_lang']);
                $featureValue->value[$language['id_lang']] = $value && Validate::isCleanHtml($value) ? $value : $value_default;
            }
            $featureValue->custom=0;
            if($featureValue->id)
            {
                if($featureValue->update())
                    $this->context->cookie->_success = $this->module->l('Updated successfully','features');
                else
                    $this->_errors[] = $this->module->l('An error occurred while saving the feature value','features');
            }
            else
            {
                if($featureValue->add())
                    $this->context->cookie->_success = $this->module->l('Added successfully','features');
                else
                    $this->_errors[] = $this->module->l('An error occurred while saving the feature value','features');
            }
            if(!$this->_errors)
                Tools::redirect($this->context->link->getModuleLink($this->module->name,'features',array('viewFeature'=>1,'id_feature'=>$featureValue->id_feature)));
        }
    }
 }