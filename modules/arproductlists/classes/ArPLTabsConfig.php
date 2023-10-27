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

class ArPLTabsConfig extends ArPLModel
{
    public $tab_bg;
    public $tab_color;
    public $active_tab_bg;
    public $active_tab_color;
    public $border;
    public $border_color;
    public $pane_padding;
    public $tab_padding;
    
    public function rules()
    {
        return array(
            array(
                array(
                    'tab_bg',
                    'tab_color',
                    'active_tab_bg',
                    'active_tab_color',
                    'border',
                    'border_color',
                    'pane_padding',
                    'tab_padding'
                ), 'safe'
            )
        );
    }
    
    public function attributeLabels()
    {
        return array(
            'tab_bg' => $this->l('Tab background', 'ArPLTabsConfig'),
            'tab_color' => $this->l('Tab color', 'ArPLTabsConfig'),
            'active_tab_bg' => $this->l('Active tab background', 'ArPLTabsConfig'),
            'active_tab_color' => $this->l('Active tab color', 'ArPLTabsConfig'),
            'border' => $this->l('Border width', 'ArPLTabsConfig'),
            'border_color' => $this->l('Border color', 'ArPLTabsConfig'),
            'pane_padding' => $this->l('Tab pane padding', 'ArPLTabsConfig'),
            'tab_padding' => $this->l('Tab padding', 'ArPLTabsConfig')
        );
    }
    
    public function fieldSuffix()
    {
        return array(
            'border' => $this->l('px', 'ArPLTabsConfig'),
        );
    }
    
    public function attributeTypes()
    {
        return array(
            'tab_bg' => 'color',
            'tab_color' => 'color',
            'active_tab_bg' => 'color',
            'active_tab_color' => 'color',
            'border_color' => 'color'
        );
    }
    
    public function attributeDefaults()
    {
        return array(
            'tab_bg' => '',
            'tab_color' => '',
            'active_tab_bg' => '#ffffff',
            'active_tab_color' => '',
            'border' => '1',
            'border_color' => '#dddddd',
            'pane_padding' => '20px 0 0 0',
            'tab_padding' => '7px 12px'
        );
    }
    
    public function attributeDescriptions()
    {
        return array(
            'tab_bg' => $this->l('Leave empty to inherit theme styles', 'ArPLTabsConfig'),
            'active_tab_bg' => $this->l('Leave empty to inherit theme styles', 'ArPLTabsConfig'),
            'tab_color' => $this->l('Leave empty to inherit theme styles', 'ArPLTabsConfig'),
            'active_tab_color' => $this->l('Leave empty to inherit theme styles', 'ArPLTabsConfig')
        );
    }
    
    public function getFormTitle()
    {
        return $this->l('Tabbed view settings', 'ArPLTabsConfig');
    }
}
