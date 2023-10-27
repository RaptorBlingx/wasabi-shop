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
    public static function getCMSTitle($id_cms,$id_lang){
        $cms = new CMS($id_cms, $id_lang);
        return $cms->meta_title;
    }
}