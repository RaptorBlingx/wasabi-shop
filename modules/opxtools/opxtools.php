<?php
/**
 * Copyright (c) 2021. OrangePix  All rights reserved.
 * DISCLAIMER
 * Do not edit, modify or copy this file.
 * If you wish to customize it, contact us at info@orangepix.it
 *
 * @author    Davide Mazzonetto <davide.mazzonetto@orangepix.it> , Andrea Pozza <andrea.pozza@orangepix.it>
 * @copyright OrangePix Srl
 * @license   Do not edit, modify or copy this file
 *
 */


if (!defined('_PS_VERSION_')) {
    exit;
}

class Opxtools extends Module
{
    public function __construct()
    {
        $this->name = 'opxtools';
        $this->tab = 'AdminParentThemes';
        $this->version = '1.0.1';
        $this->author = 'OrangePix Srl';
        $this->need_instance = 0;
        $this->ps_versions_compliancy['min'] = '1.7.0';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('OPXTools');
        $this->description = $this->l('Custom tools for development');    
    }

    
}
