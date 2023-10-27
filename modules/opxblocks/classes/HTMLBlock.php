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

namespace OrangePix\Classes;

use ObjectModel;

class HTMLBlock extends ObjectModel
{
    /** @var bool Status */
    public $active = true;

    public $name;
    public $hook;
    public $position;
    public $content;
    public $id_shop;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'htmlblock',
        'primary' => 'id_htmlblock',
        'multilang' => true,
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName','required' => true],
            'hook' => ['type' => self::TYPE_STRING, 'validate' => 'isHookName', 'required' => true],
            'position' => ['type' => self::TYPE_INT],
            'content' => ['type' => self::TYPE_HTML, 'lang' => true, 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT],
        ],
    ];
}
