<?php
/**
 * 2007-2017 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

return array(
        array(
            'item' =>
                'INSERT INTO `' . _DB_PREFIX_ . 'anmenu` 
                (`id_anmenu`, `id_shop`, `active`, `position`, `label_color`, `drop_column`, `drop_bgcolor`, `drop_bgimage`, `bgimage_position`, `position_x`, `position_y`)
                 VALUES
                 (ID_AN_MENU, AN_ID_SHOP, 1, 1, "#000000", 3, "#ffffff", "", "", 0, 0)',
            'lang' =>
                'INSERT INTO `' . _DB_PREFIX_ . 'anmenu_lang` 
                (`id_anmenu`, `id_lang`, `name`, `link`, `label`)
                 VALUES
                 (ID_AN_MENU, AN_ID_LANG, "Tops", "#" ,"HOT")',
            'items' =>
                array(

            ),
        ),
        array(
            'item' =>
                'INSERT INTO `' . _DB_PREFIX_ . 'anmenu` 
                    (`id_anmenu`, `id_shop`, `active`, `position`, `label_color`, `drop_column`, `drop_bgcolor`, `drop_bgimage`, `bgimage_position`, `position_x`, `position_y`)
                     VALUES
                     (ID_AN_MENU, AN_ID_SHOP, 1, 2, "#00a920", 2, "#ffffff", "", "", 0, 0)',
            'lang' =>
                'INSERT INTO `' . _DB_PREFIX_ . 'anmenu_lang` 
                    (`id_anmenu`, `id_lang`, `name`, `link`, `label`)
                     VALUES
                     (ID_AN_MENU, AN_ID_LANG, "Blouses", "#" ,"NEW")',
            'items' =>
                array(
                    array(
                        'item' =>
                            'INSERT INTO `' . _DB_PREFIX_ . 'andropdown` 
                                    (`id_andropdown`, `id_anmenu`, `active`, `position`, `column`, `content_type`, `categories`, `products`, `manufacturers`)
                                     VALUES
                                     (ID_AN_DROPDOWN, ID_AN_MENU, 1, 2 ,2, "html", "a:0:{}", "a:0:{}", "a:0:{}")',
                        'lang' => 'INSERT INTO `' . _DB_PREFIX_ . 'andropdown_lang` 
                                    (`id_andropdown`, `id_lang`, `static_content`)
                                     VALUES
                                     (ID_AN_DROPDOWN, AN_ID_LANG, "<p>Lorem ipsum dolor sit amet, consectetur adipisicing elitse do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercita tio ullamco laboris nisi ut aliquip.</p>")'
                    ),
                ),
        ),
        array(
            'item' =>
                'INSERT INTO `' . _DB_PREFIX_ . 'anmenu` 
                            (`id_anmenu`, `id_shop`, `active`, `position`, `label_color`, `drop_column`, `drop_bgcolor`, `drop_bgimage`, `bgimage_position`, `position_x`, `position_y`)
                             VALUES
                             (ID_AN_MENU, AN_ID_SHOP, 1, 4, "#111111", 1, "#ffffff", "", "", 0, 0)',
            'lang' =>
                'INSERT INTO `' . _DB_PREFIX_ . 'anmenu_lang` 
                            (`id_anmenu`, `id_lang`, `name`, `link`, `label`)
                             VALUES
                             (ID_AN_MENU, AN_ID_LANG, "Brands", "#" ,"")',
            'items' =>
                array(
                    array(
                        'item' =>
                            'INSERT INTO `' . _DB_PREFIX_ . 'andropdown` 
                                (`id_andropdown`, `id_anmenu`, `active`, `position`, `column`, `content_type`, `categories`, `products`, `manufacturers`)
                                 VALUES
                                 (ID_AN_DROPDOWN, ID_AN_MENU, 1, 1, 1, "manufacturer", "a:0:{}", "a:0:{}", AN_MANUFACTURER)',
                        'lang' => 'INSERT INTO `' . _DB_PREFIX_ . 'andropdown_lang` 
                                (`id_andropdown`, `id_lang`, `static_content`)
                                 VALUES
                                 (ID_AN_DROPDOWN, AN_ID_LANG, "")'
                    ),

                ),
        ),
        array(
            'item' =>
                'INSERT INTO `' . _DB_PREFIX_ . 'anmenu` 
                                (`id_anmenu`, `id_shop`, `active`, `position`, `label_color`, `drop_column`, `drop_bgcolor`, `drop_bgimage`, `bgimage_position`, `position_x`, `position_y`)
                                 VALUES
                                 (ID_AN_MENU, AN_ID_SHOP, 1, 5, "#111111", 1, "#ffffff", "", "", 0, 0)',
            'lang' =>
                'INSERT INTO `' . _DB_PREFIX_ . 'anmenu_lang` 
                                (`id_anmenu`, `id_lang`, `name`, `link`, `label`)
                                 VALUES
                                 (ID_AN_MENU, AN_ID_LANG, "Jeans", "#" ,"")',
            'items' =>
                array(
                    array(
                        'item' =>
                            'INSERT INTO `' . _DB_PREFIX_ . 'andropdown` 
                                        (`id_andropdown`, `id_anmenu`, `active`, `position`, `column`, `content_type`, `categories`, `products`, `manufacturers`)
                                         VALUES
                                         (ID_AN_DROPDOWN, ID_AN_MENU, 1, 1 ,1, "html", "a:0:{}", "a:0:{}", "a:0:{}")',
                        'lang' => 'INSERT INTO `' . _DB_PREFIX_ . 'andropdown_lang` 
                                        (`id_andropdown`, `id_lang`, `static_content`)
                                         VALUES
                                         (ID_AN_DROPDOWN, AN_ID_LANG, "<ul>
                                            <li><a href=\'#\'>Link 1</a></li>
                                            <li><a href=\'#\'>Link 2</a></li>
                                            <li><a href=\'#\'>Link 3</a></li>
                                            <li><a href=\'#\'>Link 4</a></li>
                                            <li><a href=\'#\'>Link 5</a></li>
                                            </ul>")'
                    ),
                ),
        ),
    array(
        'item' =>
            'INSERT INTO `' . _DB_PREFIX_ . 'anmenu` 
                (`id_anmenu`, `id_shop`, `active`, `position`, `label_color`, `drop_column`, `drop_bgcolor`, `drop_bgimage`, `bgimage_position`, `position_x`, `position_y`)
                 VALUES
                 (ID_AN_MENU, AN_ID_SHOP, 1, 6, "#000000", 2, "#ffffff", "", "", 0, 0)',
        'lang' =>
            'INSERT INTO `' . _DB_PREFIX_ . 'anmenu_lang` 
                (`id_anmenu`, `id_lang`, `name`, `link`, `label`)
                 VALUES
                 (ID_AN_MENU, AN_ID_LANG, "Popular", "#" ,"")',
        'items' =>
            array(
                array(
                    'item' =>
                        'INSERT INTO `' . _DB_PREFIX_ . 'andropdown` 
                                (`id_andropdown`, `id_anmenu`, `active`, `position`, `column`, `content_type`, `categories`, `products`, `manufacturers`)
                                 VALUES
                                 (ID_AN_DROPDOWN, ID_AN_MENU, 1, 1, 1, "product", "a:0:{}", AN_RANDOM_PRODUCT, "a:0:{}")',
                    'lang' => 'INSERT INTO `' . _DB_PREFIX_ . 'andropdown_lang` 
                                (`id_andropdown`, `id_lang`, `static_content`)
                                 VALUES
                                 (ID_AN_DROPDOWN, AN_ID_LANG, "")'
                ),


            ),
    ),
    );
