<?php
/**
 * Copyright (c) 2021. OrangePix  All rights reserved.
 * DISCLAIMER
 * Do not edit, modify or copy this file.
 * If you wish to customize it, contact us at info@orangepix.it
 *
 * @author    Carlos Batista <carlos.batista@orangepix.it> , Samuele Cisaro <samuele.cisaro@orangepix.it>
 * @license   Do not edit, modify or copy this file
 * @copyright OrangePix Srl
 */

$finder = PhpCsFixer\Finder::create()->exclude(['vendor','tests'])->in(__DIR__);

$config = new PhpCsFixer\Config();
return $config->setRules(
    [
        '@PSR12' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
    ]
)->setFinder($finder);
