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
class Ets_MarketPlaceMapModuleFrontController extends ModuleFrontController
{
    public function __construct()
	{
		parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
	}
    public function postProcess()
    {
        parent::postProcess();
        if(Tools::isSubmit('getmaps'))
        {
            $params = array(
                'all' => (int)Tools::getValue('all'),
                'radius' => (int)Tools::getValue('radius', 100),
                'latitude' =>(float)Tools::getValue('latitude'),
                'longitude' => (float)Tools::getValue('longitude'),
            );
            Ets_mp_seller::getMaps(0,false,$params);
        }

    }
    public function initContent()
	{
		parent::initContent();
        $this->module->setMetas();
        $this->context->smarty->assign(
            array(
                'html_content' =>$this->_initContent(), 
                'path' => $this->module->getBreadCrumb(),
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false,
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/map.tpl');      
        else        
            $this->setTemplate('map_16.tpl'); 
    }
    public function _initContent()
    {
        if(!Configuration::get('ETS_MP_ENABLE_MAP'))
            return $this->module->displayWarning($this->module->l('Page not found','map'));
        $default_country = new Country((int)Tools::getCountry());
        $link_map_google = 'http'.((Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 's' : '').'://maps.google.com/maps/api/js?region='.Tools::substr($default_country->iso_code, 0, 2);
        $this->context->smarty->assign(
            array(
                'base_link' => $this->module->getBaseLink(),
                'coordinates' => $this->getcoordinatesDefault(),
                'ETS_MP_GOOGLE_MAP_API' => Configuration::get('ETS_MP_SEARCH_ADDRESS_BY_GOOGLE') && Configuration::get('ETS_MP_GOOGLE_MAP_API') ? Configuration::get('ETS_MP_GOOGLE_MAP_API'):'',
                'link_map_google' => $link_map_google,
                'is_17' => $this->module->is17,
            )
        );
        return $this->module->displayTpl('shop/map.tpl');
    }
    public function getcoordinatesDefault()
    {
        if(($defaultLat = Configuration::get('ETS_MP_LATITUDE_DEFAULT')) && ($defaultLong = Configuration::get('ETS_MP_LONGITUDE_DEFAULT')))
        {
            return array(
                'defaultLat' => $defaultLat,
                'defaultLong' => $defaultLong,
            );
        }
        else
        {
            return array(
                'defaultLat' => 0,
                'defaultLong' => 0,
            );
                
        }
    }
}