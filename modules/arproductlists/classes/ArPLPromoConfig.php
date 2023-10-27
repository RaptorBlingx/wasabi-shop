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

class ArPLPromoConfig extends ArPLModel
{
    public $border_color;
    public $border_radius;
    public $background;
    
    public $action_section_bg;
    public $price_color;
    public $old_price_color;
    public $line_color;
    public $you_save_color;
    public $save_amount_color;
    
    public function attributeLabels()
    {
        return array(
            'border_color' => $this->l('Border color', 'ArPLPromoConfig'),
            'border_radius' => $this->l('Border radius', 'ArPLPromoConfig'),
            'background' => $this->l('Background color', 'ArPLPromoConfig'),
            'action_section_bg' => $this->l('Action section background', 'ArPLPromoConfig'),
            'price_color' => $this->l('Actual price color', 'ArPLPromoConfig'),
            'old_price_color' => $this->l('Old price color', 'ArPLPromoConfig'),
            'line_color' => $this->l('Line color', 'ArPLPromoConfig'),
            'you_save_color' => $this->l('"You save" color', 'ArPLPromoConfig'),
            'save_amount_color' => $this->l('Save amount color', 'ArPLPromoConfig'),
        );
    }
    
    public function fieldSuffix()
    {
        return array(
            'border_radius' => $this->l('px', 'ArPLPromoConfig'),
        );
    }
    
    public function rules()
    {
        return array(
            array(
                array(
                    'border_color',
                    'background',
                    'action_section_bg',
                    'price_color',
                    'old_price_color',
                    'line_color',
                    'you_save_color',
                    'save_amount_color',
                ), 'safe'
            ),
            array(
                array(
                    'border_radius'
                ), 'isInt'
            )
        );
    }
    
    public function attributeDefaults()
    {
        return array(
            'border_color' => '#DDDDDD',
            'border_radius' => 0,
            'background' => '#fefefe',
            'action_section_bg' => '#fef2b8',
            'price_color' => '#fb515d',
            'old_price_color' => '#999999',
            'line_color' => '#fb515d',
            'you_save_color' => '#999999',
            'save_amount_color' => '#333333',
        );
    }
    
    public function attributeTypes()
    {
        return array(
            'border_color' => 'color',
            'background' => 'color',
            'action_section_bg' => 'color',
            'price_color' => 'color',
            'old_price_color' => 'color',
            'line_color' => 'color',
            'you_save_color' => 'color',
            'save_amount_color' => 'color'
        );
    }
            
    public function getFormTitle()
    {
        return $this->l('Promo section settings', 'ArPLPromoConfig');
    }
}
