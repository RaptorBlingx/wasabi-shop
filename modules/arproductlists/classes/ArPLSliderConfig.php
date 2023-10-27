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

class ArPLSliderConfig extends ArPLModel
{
    public $nav_pos;
    public $nav_offset;
    public $nav_width;
    public $nav_height;
    public $nav_bg;
    public $nav_color;
    public $nav_h_bg;
    public $nav_h_color;
    
    public $dots_size;
    public $dots_bg;
    public $dots_h_bg;
    
    public $breakdowns;
    
    public function rules()
    {
        return array(
            array(
                array(
                    'nav_offset',
                    'nav_width',
                    'nav_height',
                    'dots_size',
                ), 'isInt'
            ),
            array(
                array(
                    'nav_pos',
                    'nav_bg',
                    'nav_color',
                    'nav_h_bg',
                    'nav_h_color',
                    'dots_bg',
                    'dots_h_bg'
                ), 'isColor'
            ),
            array(
                array(
                    'breakdowns'
                ), 'validateBreakdowns'
            )
        );
    }
    
    public function validateBreakdowns($value)
    {
        if (!$value) {
            return true;
        }
        $breakdowns = explode(PHP_EOL, $value);
        foreach ($breakdowns as $breakdown) {
            $v = trim($breakdown);
            if (!preg_match('{^\d+:\d+$}is', $v)) {
                return false;
            }
        }
        return true;
    }
    
    public function fieldSuffix()
    {
        return array(
            'nav_width' => $this->l('px', 'ArPLSliderConfig'),
            'nav_height' => $this->l('px', 'ArPLSliderConfig'),
            'dots_size' => $this->l('px', 'ArPLSliderConfig'),
            'nav_offset' => $this->l('px', 'ArPLSliderConfig')
        );
    }
    
    public function attributeTypes()
    {
        return array(
            'nav_pos' => 'select',
            'nav_bg' => 'color',
            'nav_color' => 'color',
            'nav_h_bg' => 'color',
            'nav_h_color' => 'color',
            'dots_bg' => 'color',
            'dots_h_bg' => 'color',
            'breakdowns' => 'textarea'
        );
    }
    
    public function attributeDefaults()
    {
        return array(
            'nav_pos' => 'aside',
            'nav_offset' => '0',
            'nav_width' => 36,
            'nav_height' => 60,
            'dots_size' => 10,
            'nav_bg' => '',
            'nav_color' => '#333333',
            'nav_h_bg' => '#869791',
            'nav_h_color' => '#FFFFFF',
            'dots_bg' => '#D6D6D6',
            'dots_h_bg' => '#869791',
            'breakdowns' => $this->getDefaultBreakdowns()
        );
    }
    
    public function navPosSelectOptions()
    {
        return array(
            array(
                'id' => 'aside',
                'name' => $this->l('Aside', 'ArPLSliderConfig')
            ),
            array(
                'id' => 'top',
                'name' => $this->l('Top', 'ArPLSliderConfig')
            )
        );
    }
    
    public static function getDefaultBreakdownsStatic()
    {
        return array(
            '0:1',
            '520:2',
            '900:3',
            '1000:4'
        );
    }
    
    public function getDefaultBreakdowns()
    {
        return implode(PHP_EOL, self::getDefaultBreakdownsStatic());
    }
    
    public function attributeLabels()
    {
        return array(
            'nav_pos' => $this->l('Nav buttons position', 'ArPLSliderConfig'),
            'nav_offset' => $this->l('Nav buttons top offset', 'ArPLSliderConfig'),
            'nav_width' => $this->l('Nav buttons width', 'ArPLSliderConfig'),
            'nav_height' => $this->l('Nav buttons height', 'ArPLSliderConfig'),
            'dots_size' => $this->l('Dots size', 'ArPLSliderConfig'),
            'nav_bg' => $this->l('Nav buttons background', 'ArPLSliderConfig'),
            'nav_color' => $this->l('Nav buttons color', 'ArPLSliderConfig'),
            'nav_h_bg' => $this->l('Nav buttons hover background', 'ArPLSliderConfig'),
            'nav_h_color' => $this->l('Nav buttons hover color', 'ArPLSliderConfig'),
            'dots_bg' => $this->l('Dots background', 'ArPLSliderConfig'),
            'dots_h_bg' => $this->l('Dots hover background', 'ArPLSliderConfig'),
            'breakdowns' => $this->l('Responsive breakdowns', 'ArPLSliderConfig'),
        );
    }
    
    public function getFormTitle()
    {
        return $this->l('Slider config', 'ArPLSliderConfig');
    }
}
