<?php

putenv('HUMANERDIA_PRODUCT_SLUG=' . (getenv('HUMANERDIA_PRODUCT_SLUG') ?: 'ovos-skill'));
require __DIR__ . '/render_humanerdia_product_image.php';
