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

class Ets_mp_withdraw_field extends ObjectModel
{
    public $id_ets_mp_payment_method_field;
    public $id_ets_mp_withdrawal;
    public $value;
    public $file_name;
    public static $definition = array(
        'table' => 'ets_mp_withdrawal_field',
        'primary' => 'id_ets_mp_withdrawal_field',
        'fields' => array(
            'id_ets_mp_withdrawal' => array(
                'type' => self::TYPE_INT,
            ),
            'id_ets_mp_payment_method_field' => array(
                'type' => self::TYPE_INT,
            ),
            'value' => array(
                'type' => self::TYPE_STRING,
            ),
            'file_name' => array(
                'type' => self::TYPE_STRING,
            ),
        )
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
	public function delete()
    {
        if(parent::delete())
        {
            if($this->file_name && file_exists(_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_withdraw/'.$this->file_name)) {
                @unlink(_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_withdraw/'.$this->file_name);
            }
            elseif($this->value && file_exists(_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_withdraw/'.$this->value))
            {
                @unlink(_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_withdraw/'.$this->value);
            }
        }
    }
	public function download(){
        if(($this->file_name && file_exists(_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_withdraw/'.$this->file_name)) || ($this->value && file_exists(_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_withdraw/'.$this->value)))
        {
            $ctype = "application/pdf";
            header("Pragma: public"); // required
            header("Expires: 0");
            header("X-Robots-Tag: noindex, nofollow", true);
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false); // required for certain browsers
            header("Content-Type: $ctype");
            header("Content-Disposition: attachment; filename=\"" . $this->value . "\";");
            header("Content-Transfer-Encoding: Binary");
            $file_url = _PS_ETS_MARKETPLACE_UPLOAD_DIR_ . 'mp_withdraw/'.($this->file_name ? : $this->value);
            if ($fsize = @filesize($file_url)) {
                header("Content-Length: " . $fsize);
            }
            ob_clean();
            flush();
            readfile($file_url);
            exit();
        }
    }
 }