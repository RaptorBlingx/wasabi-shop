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

if (!defined('_PS_VERSION_')) {
    exit();
}

class Ets_mp_paymentmethod extends ObjectModel
{
    protected static $instance;
    /**
     * @var int
     */
    public $id_shop;
    /**
     * @var string
     */
    public $fee_type;
    /**
     * @var int
     */
    public $fee_fixed;
    /**
     * @var int
     */
    public $fee_percent;
    /**
     * @var int
     */
    public $enable;
    public $deleted;
    public $sort;
    public $estimated_processing_time;
    public $logo;
    public $title;
    public $description;
    public $note;
    public static $definition = array(
        'table' => 'ets_mp_payment_method',
        'primary' => 'id_ets_mp_payment_method',
        'multilang' => true,
        'fields' => array(
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString'
            ),
            'description' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString'
            ),
            'note' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString'
            ),
            'fee_type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'fee_fixed' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat'
            ),
            'fee_percent' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat'
            ),
            'estimated_processing_time' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isUnsignedInt'
            ),
            'logo' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'enable' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'sort' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'deleted' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
        )
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_mp_paymentmethod();
        }
        return self::$instance;
    }
    public function getListFields()
    {
        $sql ='SELECT * FROM `'._DB_PREFIX_.'ets_mp_payment_method_field` pmf
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_payment_method_field_lang` pmfl ON (pmf.id_ets_mp_payment_method_field=pmfl.id_ets_mp_payment_method_field AND pmfl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE pmf.id_ets_mp_payment_method="'.(int)$this->id.'" AND pmf.deleted=0 AND pmf.enable=1 ORDER BY pmf.sort ASC';
        return Db::getInstance()->executeS($sql);
    }
    public static function getPaymentMethod($id){
        $sql = "SELECT * FROM `"._DB_PREFIX_."ets_mp_payment_method` WHERE id_ets_mp_payment_method = ".(int)$id;
        $payment_method = Db::getInstance()->getRow($sql);
        $payment_method['fee_percent'] = (float)$payment_method['fee_percent'];
        $payment_method['fee_fixed'] = (float)$payment_method['fee_fixed'];
        if($payment_method){
            $sqlLang = "SELECT * FROM `"._DB_PREFIX_."ets_mp_payment_method_lang` WHERE id_ets_mp_payment_method = ".(int)$id;
            $payment_method_langs = Db::getInstance()->executeS($sqlLang);
            $payment_method['langs'] = array();
            foreach ($payment_method_langs as $pml) {
                $payment_method['langs'][$pml['id_lang']] = array(
                    'id' => $pml['id_ets_mp_payment_method'],
                    'title' => $pml['title'],
                    'description' => $pml['description'],
                    'note' => $pml['note'],
                    'id_lang' => $pml['id_lang'],
                );
            } 
        }
        return $payment_method;
    }
    public static function getListPaymentMethodField($id_pm, $id_lang = null,$fiels_values=array()){
        $languages= Language::getLanguages(false);
        if(Tools::isSubmit('submit_payment_method'))
        {
            $results = array();
            if ($fiels_values && Ets_marketplace::validateArray($fiels_values)) {
                foreach($fiels_values as $item)
                {
                    $result = array();
                    if(isset($item['id']) && $item['id'])
                        $result['id'] = $item['id'];
                    else
                    {
                        $result['id'] = 0;
                    }    
                    $result['type'] = $item['type'];
                    $result['required'] = $item['required'];
                    $result['enable'] = $item['enable'];
                    foreach($languages as $language)
                    {
                        $result['title'][$language['id_lang']] = trim($item['title'][$language['id_lang']]) ;
                        $result['description'][$language['id_lang']] = trim($item['description'][$language['id_lang']]); 
                    }
                    $results[] = $result;
                }
            }
            return $results;
        }
        elseif($id_pm)
        {
            $filter_where = '';
            if($id_lang){
                $filter_where .= "AND pmfl.id_lang = ".(int)$id_lang;
            }
            $sql = "SELECT pmf.*, pmfl.title, pmfl.description, pmfl.id_lang FROM (
                SELECT * FROM `"._DB_PREFIX_."ets_mp_payment_method_field` WHERE id_ets_mp_payment_method = ".(int)$id_pm." AND `deleted` = 0
            ) pmf
            JOIN `"._DB_PREFIX_."ets_mp_payment_method_field_lang` pmfl ON pmf.id_ets_mp_payment_method_field = pmfl.id_ets_mp_payment_method_field
            WHERE 1 ".(string)$filter_where."
            ORDER BY pmf.sort ASC";
            $payment_method_fields = Db::getInstance()->executeS($sql);
            if(!$id_lang && $payment_method_fields){
                $results = array();
                foreach ($payment_method_fields as $field) {
                    $results[$field['id_ets_mp_payment_method_field']]['id'] = $field['id_ets_mp_payment_method_field'];
                    $results[$field['id_ets_mp_payment_method_field']]['type'] = $field['type'];
                    $results[$field['id_ets_mp_payment_method_field']]['enable'] = $field['enable'];
                    $results[$field['id_ets_mp_payment_method_field']]['description'][$field['id_lang']] = $field['description'];
                    $results[$field['id_ets_mp_payment_method_field']]['required'] = $field['required'];
                    $results[$field['id_ets_mp_payment_method_field']]['title'][$field['id_lang']] = $field['title'];
                }
                return $results;
            }
            return $payment_method_fields;
        }
    }
    public static function getListPaymentMethods($enable=false)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'ets_mp_payment_method` pm
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_payment_method_lang` pml ON (pm.id_ets_mp_payment_method =pml.id_ets_mp_payment_method AND pml.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE pm.id_shop="'.(int)Context::getContext()->shop->id.'" AND pm.deleted=0 '.($enable ? ' AND pm.enable=1':'').' ORDER BY pm.sort ASC';
        return Db::getInstance()->executeS($sql);
    }
    public function deleteAllField()
    {
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_mp_payment_method_field` SET deleted=1 WHERE id_ets_mp_payment_method='.(int)$this->id);
    }
    public static function updatePaymentMethodOrdering($paymentMethods)
    {
        if($paymentMethods)
        {
            foreach($paymentMethods as $position=>$id_ets_mp_payment_method)
            {
                $sort= $position+1;
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_mp_payment_method` SET sort="'.(int)$sort.'" WHERE id_ets_mp_payment_method='.(int)$id_ets_mp_payment_method);
            }
        }
        return true;
    }
    public static function updatePaymentMethodFieldOrdering($paymentmethodfields)
    {
        if($paymentmethodfields)
        {
            foreach($paymentmethodfields as $position=>$id_ets_mp_payment_method_field)
            {
                $sort= $position+1;
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_mp_payment_method_field` SET sort="'.(int)$sort.'" WHERE id_ets_mp_payment_method_field='.(int)$id_ets_mp_payment_method_field);
            }
        }
        return true;
    }
    public static function getCountPaymentMethods()
    {
        return Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'ets_mp_payment_method` WHERE id_shop="'.(int)Context::getContext()->shop->id.'" AND deleted=0');
    }
}
