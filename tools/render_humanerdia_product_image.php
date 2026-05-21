<?php

require dirname(__DIR__) . '/config/config.inc.php';

$catalog = require __DIR__ . '/humanerdia_product_catalog.php';
$slug = getenv('HUMANERDIA_PRODUCT_SLUG') ?: ($argv[1] ?? 'ovos-skill');

if (!isset($catalog[$slug])) {
    throw new RuntimeException("Unknown HumanEnerDIA product slug: {$slug}");
}

$config = $catalog[$slug];
$idProduct = (int) Db::getInstance()->getValue(
    'SELECT id_product FROM ' . _DB_PREFIX_ . 'product WHERE reference = "' . pSQL($config['reference']) . '"'
);

if (!$idProduct) {
    throw new RuntimeException('Product not found for image rendering.');
}

$source = _PS_TMP_IMG_DIR_ . 'humanerdia-' . $slug . '-cover.jpg';

if (!is_dir(_PS_TMP_IMG_DIR_)) {
    mkdir(_PS_TMP_IMG_DIR_, 0775, true);
}

$width = 1200;
$height = 1200;
$im = imagecreatetruecolor($width, $height);

$bg = imagecolorallocate($im, 250, 252, 251);
$teal = imagecolorallocate($im, 17, 118, 128);
$green = imagecolorallocate($im, 77, 168, 103);
$dark = imagecolorallocate($im, 20, 35, 42);
$muted = imagecolorallocate($im, 93, 110, 116);
$line = imagecolorallocate($im, 215, 226, 224);
$white = imagecolorallocate($im, 255, 255, 255);

imagefilledrectangle($im, 0, 0, $width, $height, $bg);
imagefilledrectangle($im, 0, 0, $width, 150, $dark);

$bars = [42, 78, 120, 168, 120, 78, 42];
$x = 365;
foreach ($bars as $i => $barHeight) {
    $color = $i % 2 ? $green : $teal;
    imagefilledrectangle($im, $x, 75 - (int) ($barHeight / 2), $x + 18, 75 + (int) ($barHeight / 2), $color);
    $x += 32;
}

imagestring($im, 5, 610, 55, 'HumanEnerDIA', $white);
imagestring($im, 5, 305, 325, $config['cover_heading'], $dark);
imagestring($im, 5, 235, 390, 'Industrial Energy Management', $teal);

if ($config['cover_variant'] === 'full-stack') {
    $blocks = [
        [260, 520, 420, 660, $teal, 'Portal'],
        [470, 520, 630, 660, $green, 'Analytics'],
        [680, 520, 840, 660, $teal, 'OVOS'],
        [365, 715, 525, 855, $green, 'MQTT'],
        [575, 715, 735, 855, $teal, 'Grafana'],
    ];

    foreach ($blocks as [$x1, $y1, $x2, $y2, $color, $label]) {
        imagefilledrectangle($im, $x1, $y1, $x2, $y2, $line);
        imagefilledrectangle($im, $x1 + 10, $y1 + 10, $x2 - 10, $y2 - 10, $color);
        imagestring($im, 5, $x1 + 40, $y1 + 55, $label, $white);
    }
} else {
    for ($i = 0; $i < 5; $i++) {
        $y = 535 + ($i * 72);
        imagefilledrectangle($im, 250, $y, 950, $y + 32, $line);
        imagefilledrectangle($im, 250, $y, 250 + (120 * ($i + 2)), $y + 32, $i % 2 ? $green : $teal);
    }
}

imagestring($im, 4, 180, 940, $config['cover_footer'], $muted);
imagestring($im, 3, 405, 1015, $config['cover_badge'], $muted);

imagejpeg($im, $source, 92);
imagedestroy($im);

$existingImageId = (int) Db::getInstance()->getValue(
    'SELECT i.id_image
     FROM `' . _DB_PREFIX_ . 'image` i
     LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` ish
       ON ish.id_image = i.id_image AND ish.id_shop = 1
     WHERE i.id_product = ' . $idProduct . '
     ORDER BY COALESCE(ish.cover, i.cover, 0) DESC, i.position ASC, i.id_image ASC'
);

if ($existingImageId) {
    $image = new Image($existingImageId);
    if (!Validate::isLoadedObject($image)) {
        throw new RuntimeException('Could not load existing product image row.');
    }
} else {
    $image = new Image();
    $image->id_product = $idProduct;
    $image->position = Image::getHighestPosition($idProduct) + 1;
    $image->cover = true;

    if (!$image->add()) {
        throw new RuntimeException('Could not create product image row.');
    }
}

Db::getInstance()->execute(
    'UPDATE `' . _DB_PREFIX_ . 'image` SET cover = NULL WHERE id_product = ' . $idProduct
);
Db::getInstance()->execute(
    'UPDATE `' . _DB_PREFIX_ . 'image_shop`
     SET cover = NULL
     WHERE id_image IN (
         SELECT id_image
         FROM (
             SELECT id_image
             FROM `' . _DB_PREFIX_ . 'image`
             WHERE id_product = ' . $idProduct . '
         ) product_images
     )
     AND id_shop = 1'
);
Db::getInstance()->update(
    'image',
    ['cover' => 1],
    'id_image = ' . (int) $image->id
);
Db::getInstance()->update(
    'image_shop',
    ['cover' => 1],
    'id_image = ' . (int) $image->id . ' AND id_shop = 1'
);

foreach (Language::getLanguages(false) as $language) {
    Db::getInstance()->execute(
        'INSERT INTO `' . _DB_PREFIX_ . 'image_lang` (`id_image`, `id_lang`, `legend`)
         VALUES (' . (int) $image->id . ', ' . (int) $language['id_lang'] . ', "' . pSQL($config['name']) . '")
         ON DUPLICATE KEY UPDATE `legend` = VALUES(`legend`)'
    );
}

$path = $image->getPathForCreation();
if (!ImageManager::resize($source, $path . '.jpg')) {
    throw new RuntimeException('Could not create base product image.');
}

foreach (ImageType::getImagesTypes('products') as $type) {
    ImageManager::resize(
        $source,
        $path . '-' . stripslashes($type['name']) . '.jpg',
        (int) $type['width'],
        (int) $type['height']
    );
}

@unlink($source);

echo json_encode([
    'slug' => $slug,
    'id_product' => $idProduct,
    'id_image' => (int) $image->id,
    'path' => $path . '.jpg',
], JSON_PRETTY_PRINT) . PHP_EOL;
