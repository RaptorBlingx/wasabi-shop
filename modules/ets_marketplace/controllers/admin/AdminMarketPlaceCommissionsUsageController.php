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
 * Class AdminMarketPlaceCommissionsUsageController
 * @property \Ets_marketplace $module
 */
class AdminMarketPlaceCommissionsUsageController extends ModuleAdminController
{
    public function __construct()
    {
       parent::__construct();
       $this->context= Context::getContext();
       $this->bootstrap = true;
       
    }
    public function initContent()
    {
        parent::initContent();
        if($this->ajax)
        {
            $this->renderList();
        }
    }
    public function renderList()
    {
        $this->module->getContent();
        $tabActive = Tools::getValue('tabActive');
        if(!in_array($tabActive,array('payment_settings','commission_usage','commission_rate')))
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceCommissionsUsage').'&tabActive=commission_rate');
        if(Tools::isSubmit('saveCommissionUsageSettings'))
        {
            if($this->_checkFormBeforeSubmit())
            {
                $this->_saveFromSettings();
            }
        }
        if(Tools::isSubmit('saveCommissionRateSettings'))
        {
            $this->_saveCommissionRateSettings();
        }
        $this->context->smarty->assign(
            array(
                'ets_mp_body_html'=>$tabActive=='commission_rate' ? $this->_renderCommissionRateSetting() :  ($tabActive=='commission_usage' ? $this->_renderCommissionUsageSettings() : $this->renderPayments()),
                'tabActive' => $tabActive
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
    public function _saveCommissionRateSettings()
    {
        $ETS_MP_COMMISSION_RATE = Tools::getValue('ETS_MP_COMMISSION_RATE');
        if(trim($ETS_MP_COMMISSION_RATE)=='')
            $this->module->_errors[] = $this->l('Global shop commission rate is required');
        elseif(!Validate::isFloat($ETS_MP_COMMISSION_RATE))
            $this->module->_errors[] = $this->l('Global shop commission rate is not valid');
        elseif( Validate::isFloat($ETS_MP_COMMISSION_RATE) && ($ETS_MP_COMMISSION_RATE >100 || $ETS_MP_COMMISSION_RATE<=0))
            $this->module->_errors[] = $this->l('Global shop commission rate must be between 0% and 100%');
        $ETS_MP_ENABLE_COMMISSION_BY_CATEGORY = (int)Tools::getValue('ETS_MP_ENABLE_COMMISSION_BY_CATEGORY');
        $ETS_MP_COMMISSION_EXCLUDE_TAX = (int)Tools::getValue('ETS_MP_COMMISSION_EXCLUDE_TAX',Configuration::get('ETS_MP_COMMISSION_EXCLUDE_TAX'));
        $ETS_MP_RECALCULATE_COMMISSION = (int)Tools::getValue('ETS_MP_RECALCULATE_COMMISSION',Configuration::get('ETS_MP_RECALCULATE_COMMISSION'));
        if($ETS_MP_ENABLE_COMMISSION_BY_CATEGORY)
        {
            $rate_categories = Tools::getValue('rate_category');
            $categories = array();
            if($rate_categories)
            {
                foreach($rate_categories as $id_category=>$rate)
                {
                    if(trim($rate)!='')
                    {
                        if(!Validate::isFloat($rate))
                        {
                            if(!isset($categories[$id_category]))
                                $categories[$id_category] = new Category($id_category,$this->context->language->id);
                            $this->module->_errors[] = sprintf($this->l('Commission rate by category %s is not valid'),$categories[$id_category]->name);
                        }
                        elseif( Validate::isFloat($rate) && ($rate >100 || $rate<=0))
                        {
                            if(!isset($categories[$id_category]))
                                $categories[$id_category] = new Category($id_category,$this->context->language->id);
                            $this->module->_errors[] = sprintf($this->l('Commission rate by category %s must be between 0%s and 100%s'),$categories[$id_category]->name,'%','%');
                        }
                    }
                }
            }
        }
        if(!$this->module->_errors)
        {
            Configuration::updateValue('ETS_MP_COMMISSION_RATE',(float)$ETS_MP_COMMISSION_RATE);
            Configuration::updateValue('ETS_MP_ENABLE_COMMISSION_BY_CATEGORY',$ETS_MP_ENABLE_COMMISSION_BY_CATEGORY);
            Configuration::updateValue('ETS_MP_RECALCULATE_COMMISSION',$ETS_MP_RECALCULATE_COMMISSION);
            Configuration::updateValue('ETS_MP_COMMISSION_EXCLUDE_TAX',$ETS_MP_COMMISSION_EXCLUDE_TAX);
            if($ETS_MP_ENABLE_COMMISSION_BY_CATEGORY)
            {
                if($rate_categories)
                {
                    foreach($rate_categories as $id_category=>$rate)
                    {
                        Ets_mp_commission::saveRateByCategory($id_category,$rate);
                    }
                }
            }
            $this->context->cookie->success_message = $this->l('Updated successfully');
        }
        
    }
    public function _checkFormBeforeSubmit()
    {
        $languages = Language::getLanguages(false);
        $configs = Ets_mp_defines::getInstance()->getFieldConfig('commission_usage_settings');
        if($configs)
        {
            foreach($configs as $config)
            {
                $name = $config['name'];
                $value = Tools::getValue($name);
                if(isset($config['required']) && $config['required'] && !$value)
                    $this->module->_errors[] = $config['label'].' '. $this->l('is required');
                if(isset($config['lang']) && $config['lang'])
                { 
                    if((isset($config['validate']) && $config['validate'] && method_exists('Validate',$config['validate'])))
                    {
                        $validate = $config['validate'];
                        foreach($languages as $lang)
                        {
                            if(($value = trim(Tools::getValue($name.'_'.$lang['id_lang']))) && !Validate::$validate($value))
                                $this->module->_errors[] =  $config['label'].' '.$this->l('is not valid in ').$lang['iso_code'];
                        }
                        unset($validate);
                    }
                }
                else
                {
                    if((isset($config['validate']) && $config['validate'] && method_exists('Validate',$config['validate'])))
                    {
                        $validate = $config['validate'];
                        if($value && !Validate::$validate($value))
                             $this->module->_errors[] = $config['label'].' '. $this->l('is not valid');
                        unset($validate);
                    } 
                }
                    
            }
        }
        if(!$this->module->_errors)
            return true;
    }
    public function _saveFromSettings()
    {
        $languages = Language::getLanguages(false);
        $id_language_default = Configuration::get('PS_LANG_DEFAULT');
        $configs = Ets_mp_defines::getInstance()->getFieldConfig('commission_usage_settings');
        if($configs)
        {
            foreach($configs as $config)
            {
                Configuration::deleteByName($config['name']);
                $name_value = Tools::getValue($config['name']);
                if($config['type']=='checkbox' || $config['type']=='categories'|| $config['type']=='tre_categories'|| $config['type']=='list_product')
                {
                    Configuration::updateValue($config['name'],$name_value ? implode(',',$name_value) :'' );
                }
                elseif($config['type']!='custom_html')
                {
                    if(isset($config['lang']) && $config['lang'])
                    {
                        $values = array();
                        $name_value_default = Tools::getValue($config['name'].'_'.$id_language_default);
                        foreach($languages as $language)
                        {
                            $name_value = Tools::getValue($config['name'].'_'.$language['id_lang']);
                            $values[$language['id_lang']] = $name_value ? :$name_value_default;
                        }
                        Configuration::updateValue($config['name'],$values,true);
                    }
                    else
                        Configuration::updateValue($config['name'],$name_value,true);
                }
                
            }
        }
        $this->context->cookie->success_message = $this->l('Updated successfully');
    }
    public function _renderCommissionUsageSettings()
    {
        $languages = Language::getLanguages(false);
        $fields_form = array(
    		'form' => array(
    			'legend' => array(
    				'title' => $this->l('Commission'),
    				'icon' => 'icon-settings'
    			),
    			'input' => array(),
                'submit' => array(
    				'title' => $this->l('Save'),
    			)
            ),
    	);
        $configs = Ets_mp_defines::getInstance()->getFieldConfig('commission_usage_settings');
        $fields = array();
        foreach($configs as $config)
        {
            $fields_form['form']['input'][] = $config;
            if($config['type']!='checkbox' && $config['type']!='categories' && $config['type']!='tre_categories')
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    foreach($languages as $language)
                    {
                        $fields[$config['name']][$language['id_lang']] = Tools::getValue($config['name'].'_'.$language['id_lang'],Configuration::get($config['name'],$language['id_lang']));
                    }
                    
                }
                else
                    $fields[$config['name']] = Tools::getValue($config['name'],Configuration::get($config['name']));
            }
            elseif($config['type']!='custom_html')
                $fields[$config['name']] = Tools::isSubmit('saveCommissionUsageSettings') ?  Tools::getValue($config['name']) : (Configuration::get($config['name']) ? explode(',',Configuration::get($config['name'])):array());
        }
        $helper = new HelperForm();
    	$helper->show_toolbar = false;
    	$helper->table = 'commission_usage';
    	$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
    	$helper->default_form_language = $lang->id;
    	$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
    	$this->fields_form = array();
    	$helper->module = $this->module;
    	$helper->identifier = $this->identifier;
    	$helper->submit_action = 'saveCommissionUsageSettings';
    	$helper->currentIndex = $this->context->link->getAdminLink('AdminMarketPlaceCommissionsUsage', false).'&tabActive=commission_usage';
    	$helper->token = Tools::getAdminTokenLite('AdminMarketPlaceCommissionsUsage');
    	$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));            
        $helper->tpl_vars = array(
    		'base_url' => $this->context->shop->getBaseURL(),
    		'language' => array(
    			'id_lang' => $language->id,
    			'iso_code' => $language->iso_code
    		),
    		'fields_value' => $fields,
    		'languages' => $this->context->controller->getLanguages(),
    		'id_language' => $this->context->language->id,
            'isConfigForm' => true,
        );
        return $helper->generateForm(array($fields_form));
    }
    public function _renderCommissionRateSetting()
    {
        $languages = Language::getLanguages(false);
        $fields_form = array(
    		'form' => array(
    			'legend' => array(
    				'title' => $this->l('Commission rate'),
    				'icon' => 'icon-settings'
    			),
    			'input' => array(),
                'submit' => array(
    				'title' => $this->l('Save'),
    			)
            ),
    	);
        $configs = Ets_mp_defines::getInstance()->getFieldConfig('commission_rate_settings');
        $fields = array();
        foreach($configs as $config)
        {
            $fields_form['form']['input'][] = $config;
            if($config['type']!='checkbox' && $config['type']!='categories' && $config['type']!='tre_categories')
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    foreach($languages as $language)
                    {
                        $fields[$config['name']][$language['id_lang']] = Tools::getValue($config['name'].'_'.$language['id_lang'],Configuration::get($config['name'],$language['id_lang']));
                    }
                    
                }
                else
                    $fields[$config['name']] = Tools::getValue($config['name'],Configuration::get($config['name']));
            }
            elseif($config['type']!='custom_html')
                $fields[$config['name']] = Tools::isSubmit('saveCommissionRateSettings') ?  Tools::getValue($config['name']) : (Configuration::get($config['name']) ? explode(',',Configuration::get($config['name'])):array());
        }
        $helper = new HelperForm();
    	$helper->show_toolbar = false;
    	$helper->table = 'commission_usage';
    	$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
    	$helper->default_form_language = $lang->id;
    	$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
    	$this->fields_form = array();
    	$helper->module = $this->module;
    	$helper->identifier = $this->identifier;
    	$helper->submit_action = 'saveCommissionRateSettings';
    	$helper->currentIndex = $this->context->link->getAdminLink('AdminMarketPlaceCommissionsUsage', false).'&tabActive=commission_rate';
    	$helper->token = Tools::getAdminTokenLite('AdminMarketPlaceCommissionsUsage');
    	$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));            
        $helper->tpl_vars = array(
    		'base_url' => $this->context->shop->getBaseURL(),
    		'language' => array(
    			'id_lang' => $language->id,
    			'iso_code' => $language->iso_code
    		),
    		'fields_value' => $fields,
    		'languages' => $this->context->controller->getLanguages(),
    		'id_language' => $this->context->language->id,
            'isConfigForm' => true,
        );
        return $helper->generateForm(array($fields_form));
    }
    public function renderPayments(){
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $currency = Currency::getDefaultCurrency();
        $link_pm = Context::getContext()->link->getAdminLink('AdminMarketPlaceCommissionsUsage').'&tabActive=payment_settings';
        $errors = array();
        $id_payment_method = (int)Tools::getValue('payment_method');
        $action = Tools::getValue('action');
        if(Tools::isSubmit('delete_logo') && $id_payment_method && Validate::isUnsignedId($id_payment_method))
        {
            $paymentMethod = new Ets_mp_paymentmethod($id_payment_method);
            if(Validate::isLoadedObject($paymentMethod) && $paymentMethod->logo)
            {
                if(file_exists(_PS_IMG_DIR_.'mp_payment/'.$paymentMethod->logo))
                    @unlink(_PS_IMG_DIR_.'mp_payment/'.$paymentMethod->logo);
                $paymentMethod->logo ='';
                $paymentMethod->update();
            }
            Tools::redirectAdmin($link_pm . '&payment_method=' . $paymentMethod->id . '&edit_pm=1&conf=7');
        }
        if($action=='updatePaymentMethodOrdering' && ($paymentMethods= Tools::getValue('paymentmethod')) && Ets_marketplace::validateArray($paymentMethods) )
        {
            if(Ets_mp_paymentmethod::updatePaymentMethodOrdering($paymentMethods))
            {
                die(
                    json_encode(
                        array(
                            'success' => $this->l('Updated successfully')
                        )
                    )
                );
            }

        }
        if($action=='updatePaymentMethodFieldOrdering' && ($paymentmethodfields= Tools::getValue('paymentmethodfield')) && Ets_marketplace::validateArray($paymentmethodfields))
        {
            if(Ets_mp_paymentmethod::updatePaymentMethodFieldOrdering($paymentmethodfields))
            {
                die(
                    json_encode(
                        array(
                            'success' => $this->l('Updated successfully')
                        )
                    )
                );
            }

        }
        $valuefields = array();
        if($id_payment_method && Validate::isUnsignedId($id_payment_method))
            $paymentMethod = new Ets_mp_paymentmethod($id_payment_method);
        else
        {
            $paymentMethod = new Ets_mp_paymentmethod();
            $paymentMethod->id_shop= $this->context->shop->id;
            $paymentMethod->sort = 1+ Ets_mp_paymentmethod::getCountPaymentMethods();
        }
        foreach($languages as $language)
        {
            $valuefields['title'][$language['id_lang']] = Tools::getValue('payment_method_name_'.$language['id_lang'],isset($paymentMethod->title[$language['id_lang']]) ? $paymentMethod->title[$language['id_lang']] :'');
            $valuefields['description'][$language['id_lang']] = Tools::getValue('payment_method_desc_'.$language['id_lang'],isset($paymentMethod->description[$language['id_lang']]) ? $paymentMethod->description[$language['id_lang']]:'');
            $valuefields['note'][$language['id_lang']] = Tools::getValue('payment_method_note_'.$language['id_lang'],isset($paymentMethod->note[$language['id_lang']]) ? $paymentMethod->note[$language['id_lang']]:'');
        }
        $payment_method_fee_type = Tools::getValue('payment_method_fee_type',$paymentMethod->fee_type);
        $valuefields['fee_type'] = $payment_method_fee_type;
        $payment_method_fee_fixed = Tools::getValue('payment_method_fee_fixed',$paymentMethod->fee_fixed);
        $valuefields['fee_fixed'] = $payment_method_fee_fixed;
        $payment_method_fee_percent = Tools::getValue('payment_method_fee_percent',$paymentMethod->fee_percent);
        $valuefields['fee_percent'] = $payment_method_fee_percent;
        $payment_method_estimated = Tools::getValue('payment_method_estimated',$paymentMethod->estimated_processing_time);
        $valuefields['estimated_processing_time'] = $payment_method_estimated;
        $payment_method_enabled = Tools::getValue('payment_method_enabled',$paymentMethod->enable);
        $valuefields['enable'] = $payment_method_enabled;
        $valuefields['payment_method'] = $id_payment_method;
        $valuefields['logo'] = $paymentMethod->logo;
        $fiels_values = Tools::getValue('payment_method_field', array());
        $pmf = Ets_mp_paymentmethod::getListPaymentMethodField($id_payment_method,null,Ets_marketplace::validateArray($fiels_values) ? $fiels_values:array());
        $this->context->smarty->assign(
            array(
                'valuefields' => $valuefields,
                'payment_method_fields' => $pmf,
                'link_base' => $this->module->getBaseLink()
            )
        );
        if (Tools::isSubmit('submit_payment_method')) {
            $payment_method_name_default = Tools::getValue('payment_method_name_'.$id_lang_default);
            if(!$payment_method_name_default)
                $errors[] = $this->l('Title of withdrawal method is required.');
            foreach($languages as $language)
            {
                if(($name = Tools::getValue('payment_method_name_'.$language['id_lang'])) && !Validate::isCleanHtml($name))
                    $errors[] = $this->l('Title of payment method is not valid in').' '.$language['iso_code'];
                if(($desc = Tools::getValue('payment_method_desc_'.$language['id_lang'])) && !Validate::isCleanHtml($desc))
                    $errors[] = $this->l('Description of payment method is not valid in').' '.$language['iso_code'];
                if(($note=Tools::getValue('payment_method_note_'.$language['id_lang'])) && !Validate::isCleanHtml($note))
                    $errors[] = $this->l('Description of payment method is not valid in').' '.$language['iso_code'];
            }
            if ($payment_method_fee_type != 'NO_FEE')
            {
                if ($payment_method_fee_type == 'FIXED') {
                    if (!$payment_method_fee_fixed) {
                        $errors[] = $this->l('Fee (fixed amount) is required');
                    } elseif (!Validate::isFloat($payment_method_fee_fixed)) {
                        $errors[] = $this->l('Fee (fixed amount) must be a decimal number.');
                    }
                } elseif ($payment_method_fee_type == 'PERCENT') {
                    if (!$payment_method_fee_percent) {
                        $errors[] = $this->l('Fee (percentage) is required');
                    } elseif (!Validate::isFloat($payment_method_fee_percent)) {
                        $errors[] = $this->l('Fee (percentage) must be a decimal number.');
                    }
                    elseif($payment_method_fee_percent >100)
                        $errors[] = $this->l('Fee (percentage) max 100%');
                }
            }
            if ($payment_method_estimated) {
                if (!Validate::isUnsignedInt($payment_method_estimated)) {
                    $errors[] = $this->l('Estimated processing time must be an integer');
                }
            }
            if (($pmf = Tools::getValue('payment_method_field', array())) && Ets_marketplace::validateArray($pmf)) {
                foreach ($pmf as $item) {
                    if (isset($item['title']) && Ets_marketplace::validateArray($item['title']) && $item['title']) {
                        if(!isset($item['title'][$id_lang_default]) || !$item['title'][$id_lang_default])
                            $errors[] = $this->l('Title of withdrawal method field is required');
                        foreach ($item['title'] as $title) {
                            if($title){
                                if (!Validate::isString($title)) {
                                    $errors[] = $this->l('Title of withdrawal method field must be a string');
                                }
                            }
                        }
                    }
                }
            }
            if(isset($_FILES['logo']) && isset($_FILES['logo']['name']) && $_FILES['logo']['name'] && !$errors)
            {
                if(!Validate::isFileName(str_replace(array(' ','(',')','!','@','#','+'),'_',$_FILES['logo']['name'])))
                    $errors[] = '"'.$_FILES['logo']['name'].'" '.$this->l('file name is not valid');
                else
                {
                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES['logo']['name'], '.'), 1));
                    if(!is_dir(_PS_IMG_DIR_.'mp_payment/'))
                    {
                        @mkdir(_PS_IMG_DIR_.'mp_payment/',0777,true);
                        @copy(dirname(__FILE__).'/index.php', _PS_IMG_DIR_.'mp_payment/index.php');
                    }
                    $target_file = _PS_IMG_DIR_.'mp_payment/';
                    $file_name = Tools::strtolower(Tools::passwdGen(12,'NO_NUMERIC')).'.'.$type;
                    $target_file .=$file_name;
                    if(!in_array($type, array('jpg', 'gif', 'jpeg', 'png')))
                    {
                        $errors[] = $this->l('Logo is not valid');
                    }
                    else
                    {
                        $max_sizefile = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');
                        if($_FILES['logo']['size'] > $max_sizefile*1024*1024)
                            $errors[] =sprintf($this->l('Image is too large (%s Mb). Maximum allowed: %s Mb'),Tools::ps_round((float)$_FILES['logo']['size']/1048576,2), Tools::ps_round(Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),2));
                    }
                }
                if(!$errors)
                {
                    if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                        if($paymentMethod->logo)
                            $logo_old = $paymentMethod->logo;
                        $paymentMethod->logo = $file_name;
                    } else {
                        $errors[] = $this->l('Sorry, there was an error while uploading your logo.');
                    }
                }
            }
            if (!$errors) {
                $paymentMethod->fee_type = $payment_method_fee_type;
                $paymentMethod->fee_fixed = (float)$payment_method_fee_fixed;
                $paymentMethod->fee_percent =(float)$payment_method_fee_percent;
                $paymentMethod->estimated_processing_time =$payment_method_estimated;
                $paymentMethod->enable = (int)$payment_method_enabled;
                $payment_method_desc_default = trim(Tools::getValue('payment_method_desc_'.$id_lang_default));
                $payment_method_note_default = trim(Tools::getValue('payment_method_note_'.$id_lang_default));
                foreach($languages as $language)
                {
                    $payment_method_name = trim(Tools::getValue('payment_method_name_'.$language['id_lang']));
                    $paymentMethod->title[$language['id_lang']] = $payment_method_name? : $payment_method_name_default;
                    $payment_method_desc = trim(Tools::getValue('payment_method_desc_'.$language['id_lang']));
                    $paymentMethod->description[$language['id_lang']] = $payment_method_desc ?: $payment_method_desc_default;
                    $payment_method_note = trim(Tools::getValue('payment_method_note_'.$language['id_lang']));
                    $paymentMethod->note[$language['id_lang']] = $payment_method_note? : $payment_method_note_default;
                }
                $ok=0;
                if($paymentMethod->id)
                {
                    if($paymentMethod->update(true))
                    {
                        if(isset($logo_old) && $logo_old && file_exists(_PS_IMG_DIR_.'mp_payment/'.$logo_old))
                            @unlink(_PS_IMG_DIR_.'mp_payment/'.$logo_old);
                        $ok=1;
                    }
                    else
                        $errors[] = $this->l('Update failed');
                }
                elseif ($paymentMethod->add(true,true)) {
                    $ok=2;
                }
                if($ok)
                {
                    $paymentMethod->deleteAllField();
                    if (($pmf = Tools::getValue('payment_method_field', array())) && Ets_marketplace::validateArray($pmf) ) {
                        $sort=1;
                        foreach($pmf as $item)
                        {
                            if(isset($item['id']) && $item['id'])
                                $paymentField = new Ets_mp_paymentmethodfield($item['id']);
                            else
                            {
                                $paymentField = new Ets_mp_paymentmethodfield();
                                $paymentField->id_ets_mp_payment_method = $paymentMethod->id;
                            }
                            $paymentField->type = $item['type'];
                            $paymentField->required = $item['required'];
                            $paymentField->enable = $item['enable'];
                            $paymentField->deleted=0;
                            $paymentField->sort=$sort;
                            $sort++;
                            foreach($languages as $language)
                            {
                                $paymentField->title[$language['id_lang']] = trim($item['title'][$language['id_lang']]) ? trim($item['title'][$language['id_lang']]) : trim($item['title'][$id_lang_default]);
                                $paymentField->description[$language['id_lang']] = trim($item['description'][$language['id_lang']]) ? trim($item['description'][$language['id_lang']]) : trim($item['description'][$id_lang_default]);
                            }
                            if($paymentField->id)
                                $paymentField->update();
                            else
                                $paymentField->add();
                        }
                    }
                    if($ok==1)
                    {
                        $this->context->cookie->success_message = $this->l('Updated successfully');
                        Tools::redirectAdmin($link_pm . '&payment_method=' . $paymentMethod->id . '&edit_pm=1');
                    }
                    if($ok==2)
                    {
                        $this->context->cookie->success_message = $this->l('Added successfully');
                        Tools::redirectAdmin($link_pm . '&payment_method=' . $paymentMethod->id . '&edit_pm=1');
                    }
                }
                else
                    $errors[] = $this->l('Add failed');
            }
        }
        if ((int)Tools::isSubmit('create_pm') || (Tools::isSubmit('edit_pm') && (int)$id_payment_method) ) {
            $this->context->smarty->assign(array(
                'languages' => $languages,
                'default_lang' => (int)Configuration::get('PS_LANG_DEFAULT'),
                'currency' => $currency,
                'link_pm' => $link_pm,
                'errors' => $errors ? $this->module->displayError($errors):false,
            ));
            return $this->context->smarty->fetch(_PS_MODULE_DIR_.'ets_marketplace/views/templates/hook/payment/form_payment_method.tpl');
        }
        elseif(Tools::isSubmit('delete_pm') && $id_payment_method)
        {
            $paymentMethod = new Ets_mp_paymentmethod($id_payment_method);
            $paymentMethod->deleted=1;
            $paymentMethod->update();
            Tools::redirectAdmin($link_pm . '&conf=1');
        }
        $payment_methods = Ets_mp_paymentmethod::getListPaymentMethods();
        $default_currency = Currency::getDefaultCurrency()->iso_code;
        $this->context->smarty->assign(array(
            'payment_methods' => $payment_methods,
            'default_currency' => $default_currency,
            'link_pm' => $link_pm
        ));
        return  $this->context->smarty->fetch(_PS_MODULE_DIR_.'ets_marketplace/views/templates/hook/payment/payments.tpl');
    }
}