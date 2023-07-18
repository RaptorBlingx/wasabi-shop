<?php
/**
 * Copyright (c) 2021. OrangePix  All rights reserved.
 * DISCLAIMER
 * Do not edit, modify or copy this file.
 * If you wish to customize it, contact us at info@orangepix.it
 *
 * @author    Davide Mazzonetto <davide.mazzonetto@orangepix.it> 
 * @license   Do not edit, modify or copy this file
 * @copyright OrangePix Srl
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_2($object)
{
    return Db::getInstance()->execute("
        ALTER TABLE " . _DB_PREFIX_ . "opxslider_slides_lang
        ADD COLUMN `cta` varchar(255) NOT NULL AFTER `url`;
    ");
}
