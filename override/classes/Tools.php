<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */
declare(strict_types=1);
class Tools extends ToolsCore
{
    /*
    * module: opxtools
    * date: 2023-07-18 12:00:47
    * version: 1.0.1
    */
    public static function getCMSTitle($id_cms,$id_lang){
        $cms = new CMS($id_cms, $id_lang);
        return $cms->meta_title;
    }
}