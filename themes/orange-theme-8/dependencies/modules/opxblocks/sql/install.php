<?php
/**
 * Copyright (c) 2021. OrangePix  All rights reserved.
 * DISCLAIMER
 * Do not edit, modify or copy this file.
 * If you wish to customize it, contact us at info@orangepix.it
 *
 * @author    Carlos Batista <carlos.batista@orangepix.it> , Samuele Cisaro <samuele.cisaro@orangepix.it>
 * @copyright OrangePix Srl
 * @license   Do not edit, modify or copy this file
 *
 */
$sql = [];
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'htmlblock` (
        `id_htmlblock` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `hook` varchar(100) NOT NULL,
        `active` TINYINT(1) UNSIGNED NOT NULL,
        `id_shop` INT(11) unsigned NOT NULL DEFAULT 1,
        PRIMARY KEY (`id_htmlblock`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'htmlblock_lang` (
        `id_htmlblock` INT(10) UNSIGNED NOT NULL,
        `id_lang` INT(10) UNSIGNED NOT NULL,
        `content` longtext NOT NULL,
        PRIMARY KEY (`id_htmlblock`,`id_lang`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';


foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
