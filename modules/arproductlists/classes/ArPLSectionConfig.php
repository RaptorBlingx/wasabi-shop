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

class ArPLSectionConfig extends ArPLModel
{
    public $header_color;
    public $header_size;
    public $header_top_margin;
    public $header_bottom_margin;
    public $padding;
    public $border;
    public $border_color;
    public $background;
    
    public function rules()
    {
        return array(
            array(
                array(
                    'padding',
                    'border',
                ), 'safe'
            ),
            array(
                array(
                    'header_size',
                    'header_top_margin',
                    'header_bottom_margin',
                    'border',
                ), 'isInt'
            ),
            array(
                array(
                    'header_color',
                    'border_color',
                    'background'
                ), 'isColor'
            )
        );
    }
    
    public function attributeDescriptions()
    {
        return array(
            'header_color' => $this->l('Leave empty to inherit theme styles', 'ArPLSectionConfig'),
            'header_size' => $this->l('Set 0 to inherit theme styles', 'ArPLSectionConfig')
        );
    }
    
    public function attributeLabels()
    {
        return array(
            'header_color' => $this->l('Header color', 'ArPLSectionConfig'),
            'header_size' => $this->l('Header font size', 'ArPLSectionConfig'),
            'header_top_margin' => $this->l('Header margin top', 'ArPLSectionConfig'),
            'header_bottom_margin' => $this->l('Header margin bottom', 'ArPLSectionConfig'),
            'border' => $this->l('Border', 'ArPLSectionConfig'),
            'border_color' => $this->l('Border color', 'ArPLSectionConfig'),
            'padding' => $this->l('Padding', 'ArPLSectionConfig'),
            'background' => $this->l('Background color', 'ArPLSectionConfig'),
        );
    }
    
    public function attributeTypes()
    {
        return array(
            'header_color' => 'color',
            'border_color' => 'color',
            'background' => 'color'
        );
    }
    
    public function fieldSuffix()
    {
        return array(
            'header_size' => $this->l('px', 'ArPLSectionConfig'),
            'header_top_margin' => $this->l('px', 'ArPLSectionConfig'),
            'header_bottom_margin' => $this->l('px', 'ArPLSectionConfig'),
            'border' => $this->l('px', 'ArPLSectionConfig')
        );
    }
    
    public function attributeDefaults()
    {
        return array(
            'header_color' => '',
            'header_size' => '0',
            'header_top_margin' => '15',
            'header_bottom_margin' => '5',
            'padding' => '0 10px',
            'border' => '1',
            'border_color' => '#ffffff',
            'background' => '#ffffff'
        );
    }
    
    public function getFormTitle()
    {
        return $this->l('Section view settings', 'ArPLSectionConfig');
    }
}
