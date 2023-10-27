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
include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('opxslider.php');

$home_slider = new Opxslider();
$slides = array();

if (!Tools::isSubmit('secure_key') || Tools::getValue('secure_key') != $home_slider->secure_key || !Tools::getValue('action')) {
    die(1);
}

if (Tools::getValue('action') == 'updateSlidesPosition' && Tools::getValue('slides')) {
    $slides = Tools::getValue('slides');

    foreach ($slides as $position => $id_slide) {
        $res = Db::getInstance()->execute(
            '
			UPDATE `'._DB_PREFIX_.'opxslider_slides` SET `position` = '.(int)$position.'
			WHERE `id_opxslider_slides` = '.(int)$id_slide
        );
    }

    $home_slider->clearCache();
}
