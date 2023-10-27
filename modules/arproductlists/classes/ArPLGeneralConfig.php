<?php
/**
* 2012-2019 Areama
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@areama.net so we can send you a copy immediately.
*
*  @author    Areama <support@areama.net>
*  @copyright 2019 Areama
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Areama
*/

include_once dirname(__FILE__).'/ArPLModel.php';

class ArPLGeneralConfig extends ArPLModel
{
    public $sandbox;
    public $allowed_ips;
    public $custom_css;
    public $view_cleanup_after;
    
    public function rules()
    {
        return array(
            array(
                array(
                    'sandbox',
                    'allowed_ips',
                    'custom_css'
                ), 'safe'
            ),
            array(
                array(
                    'view_cleanup_after'
                ), 'isInt'
            )
        );
    }
    
    public function attributeTypes()
    {
        return array(
            'sandbox' => 'switch',
            'allowed_ips' => 'textarea',
            'custom_css' => 'textarea',
            'view_cleanup_after' => 'text'
        );
    }
    
    public function attributeDefaults()
    {
        return array(
            'sandbox' => 0,
            'allowed_ips' => $this->getCurrentIP(),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            'sandbox' => $this->l('Sandbox mode', 'ArPLGeneralConfig'),
            'allowed_ips' => $this->l('Allowed IPs', 'ArPLGeneralConfig'),
            'custom_css' => $this->l('Custom CSS', 'ArPLGeneralConfig'),
            'view_cleanup_after' => $this->l('Store product view data for last X days', 'ArPLGeneralConfig'),
        );
    }
    
    public function attributeDescriptions()
    {
        return array(
            'allowed_ips' => sprintf($this->l('One IP address per line. Your current IP %s', 'ArPLGeneralConfig'), $this->getCurrentIP()),
            'sandbox' => $this->l('If enabled, module will be shown for allowed IPs only.', 'ArPLGeneralConfig')
        );
    }
    
    public function getCurrentIP()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    
    public function getFormTitle()
    {
        return $this->l('General config', 'ArPLGeneralConfig');
    }
}
