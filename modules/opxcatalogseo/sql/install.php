<?php
/**
* Copyright (c) OrangePix Srl  All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@orangepix.it
* Web : https://www.orangepix.it
*
* @author    OrangePix Srl <info@orangepix.it>
* @license   Proprietary
* @copyright OrangePix Srl
*/


$sql = [];
$sql[] = 'CREATE TABLE IF NOT EXISTS `_DB_PREFIX_opxcatalogseo_category` (
        `id_opxcatalogseo_category` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `id_category` int(10) NOT NULL,
        `date_add` datetime NOT NULL,
        `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_opxcatalogseo_category`)
    ) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=UTF8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `_DB_PREFIX_opxcatalogseo_category_lang` (
        `id_opxcatalogseo_category` int(10) NOT NULL,
        `id_lang` int(10) NOT NULL,
        `h1` varchar(180)
    ) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=UTF8;';

foreach ($sql as $query) {
    $query = strtr($query, [
        '_DB_PREFIX_'    => _DB_PREFIX_,
        '_MYSQL_ENGINE_' => _MYSQL_ENGINE_
    ]);
    if (Db::getInstance()->execute($query) === false) {
        return false;
    }
}
