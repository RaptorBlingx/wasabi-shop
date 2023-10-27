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

  if (!defined('_PS_VERSION_')) {
      exit;
  }

  function upgrade_module_1_0_1($object)
  {
      return Db::getInstance()->execute('ALTER table `' . _DB_PREFIX_ . 'htmlblock` ADD COLUMN `position` INT NULL DEFAULT 1 after `active`');
  }
