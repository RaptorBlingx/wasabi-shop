<?php

putenv('HUMANERDIA_PRODUCT_SLUG=' . (getenv('HUMANERDIA_PRODUCT_SLUG') ?: 'ovos-skill'));
require __DIR__ . '/publish_humanerdia_product.php';
