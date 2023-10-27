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
    'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'anmenu` (
        `id_anmenu` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_shop` int(10) unsigned NOT NULL DEFAULT 1,
            `active` tinyint(1) unsigned NOT NULL DEFAULT 1,
            `position` int(10) unsigned NOT NULL DEFAULT 0,
            `label_color` varchar(32) DEFAULT NULL,
            `drop_column` int(10) DEFAULT 0,
            `drop_bgcolor` varchar(32) DEFAULT NULL,
            `drop_bgimage` varchar(128) DEFAULT NULL,
            `bgimage_position` varchar(50) DEFAULT NULL,
            `position_x` int(10) DEFAULT 0,
            `position_y` int(10) DEFAULT 0,
            PRIMARY KEY(`id_anmenu`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET = utf8;',
    'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'anmenu_lang` (
        `id_anmenu` int(10) unsigned NOT NULL,
            `id_lang` int(10) unsigned NOT NULL,
            `name` varchar(254) NOT NULL,
            `link` varchar(254) NOT NULL DEFAULT \'\',
            `label` varchar(128) DEFAULT NULL,
            PRIMARY KEY(`id_anmenu`, `id_lang`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET = utf8;',
    'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'andropdown` (
        `id_andropdown` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_anmenu` int(10) unsigned NOT NULL,
            `active` tinyint(1) unsigned NOT NULL DEFAULT 1,
            `position` int(10) unsigned NOT NULL DEFAULT 0,
            `column` int(10) DEFAULT 0,
            `content_type` varchar(50) NOT NULL,
            `title` varchar(50) NOT NULL,
            `categories` text DEFAULT NULL,
            `products` text DEFAULT NULL,
            `manufacturers` text DEFAULT NULL,
            `drop_bgimage` varchar(128) DEFAULT NULL,
            PRIMARY KEY(`id_andropdown`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET = utf8;',
    'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'andropdown_lang` (
        `id_andropdown` int(10) unsigned NOT NULL,
            `id_lang` int(10) unsigned NOT NULL,
            `static_content` text DEFAULT NULL,
            PRIMARY KEY(`id_andropdown`, `id_lang`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET = utf8;',
);
