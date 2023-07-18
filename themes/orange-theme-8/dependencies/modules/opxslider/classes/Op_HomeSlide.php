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
namespace OrangePix\Classes;

use Context;
use Db;
use ObjectModel;

class Op_HomeSlide extends ObjectModel
{
    public $title;
    public $description;
    public $url;
    public $legend;
    public $cta;
    public $image;
    public $image_mobile;
    public $active;
    public $position;
    public $id_shop;

    const MODULE_PATH = _PS_MODULE_DIR_.'opxslider';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'opxslider_slides',
        'primary' => 'id_opxslider_slides',
        'multilang' => true,
        'fields' => array(
            'active' =>			array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'position' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),

            // Lang fields
            'description' =>	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 4000),
            'title' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
            'legend' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
            'cta' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
            'url' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isUrl', 'required' => true, 'size' => 255),
            'image' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
            'image_mobile' =>   array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
        )
    );

    public function __construct($id_slide = null, $id_lang = null, $id_shop = null, Context $context = null)
    {
        parent::__construct($id_slide, $id_lang, $id_shop);
    }

    public function add($autodate = true, $null_values = false)
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;

        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute(
            '
			INSERT INTO `'._DB_PREFIX_.'opxslider` (`id_shop`, `id_opxslider_slides`)
			VALUES('.(int)$id_shop.', '.(int)$this->id.')'
        );
        return $res;
    }

    public function delete()
    {
        $res = true;

        $images = $this->image;
        $images_mobile = $this->image_mobile;
        foreach ($images as $image) {
            if (preg_match('/sample/', $image) === 0) {
                if ($image && file_exists(self::MODULE_PATH.'/views/img/'.$image)) {
                    $res &= @unlink(self::MODULE_PATH.'/views/img/'.$image);
                }
            }
        }
        foreach ($images_mobile as $image) {
            if (preg_match('/sample/', $image) === 0) {
                if ($image && file_exists(self::MODULE_PATH.'/views/img/'.$image)) {
                    $res &= @unlink(self::MODULE_PATH.'/views/img/'.$image);
                }
            }
        }

        $res &= $this->reOrderPositions();

        $res &= Db::getInstance()->execute(
            '
			DELETE FROM `'._DB_PREFIX_.'opxslider`
			WHERE `id_opxslider_slides` = '.(int)$this->id
        );

        $res &= parent::delete();
        return $res;
    }

    public function reOrderPositions()
    {
        $id_slide = $this->id;
        $context = Context::getContext();
        $id_shop = $context->shop->id;

        $max = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
			SELECT MAX(hss.`position`) as position
			FROM `'._DB_PREFIX_.'opxslider_slides` hss, `'._DB_PREFIX_.'opxslider` hs
			WHERE hss.`id_opxslider_slides` = hs.`id_opxslider_slides` AND hs.`id_shop` = '.(int)$id_shop
        );

        if ((int)$max == (int)$id_slide) {
            return true;
        }

        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
			SELECT hss.`position` as position, hss.`id_opxslider_slides` as id_slide
			FROM `'._DB_PREFIX_.'opxslider_slides` hss
			LEFT JOIN `'._DB_PREFIX_.'opxslider` hs ON (hss.`id_opxslider_slides` = hs.`id_opxslider_slides`)
			WHERE hs.`id_shop` = '.(int)$id_shop.' AND hss.`position` > '.(int)$this->position
        );

        foreach ($rows as $row) {
            $current_slide = new Op_HomeSlide($row['id_slide']);
            --$current_slide->position;
            $current_slide->update();
            unset($current_slide);
        }

        return true;
    }

    public static function getAssociatedIdsShop($id_slide)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
			SELECT hs.`id_shop`
			FROM `'._DB_PREFIX_.'opxslider` hs
			WHERE hs.`id_opxslider_slides` = '.(int)$id_slide
        );

        if (!is_array($result)) {
            return false;
        }

        $return = array();

        foreach ($result as $id_shop) {
            $return[] = (int)$id_shop['id_shop'];
        }

        return $return;
    }
}
