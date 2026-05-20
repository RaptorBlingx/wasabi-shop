<?php

require dirname(__DIR__) . '/config/config.inc.php';

$idProduct = 38;
$source = _PS_TMP_IMG_DIR_ . 'humanerdia-product-cover.jpg';

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

imagefilledrectangle($im, 0, 0, $width, $height, $bg);
imagefilledrectangle($im, 0, 0, $width, 150, $dark);

$bars = [42, 78, 120, 168, 120, 78, 42];
$x = 365;
foreach ($bars as $i => $barHeight) {
    $color = $i % 2 ? $green : $teal;
    imagefilledrectangle($im, $x, 75 - (int) ($barHeight / 2), $x + 18, 75 + (int) ($barHeight / 2), $color);
    $x += 32;
}

imagestring($im, 5, 610, 55, 'HumanEnerDIA', imagecolorallocate($im, 255, 255, 255));
imagestring($im, 5, 325, 325, 'OVOS Skill', $dark);
imagestring($im, 5, 235, 390, 'Industrial Energy Management', $teal);

for ($i = 0; $i < 5; $i++) {
    $y = 535 + ($i * 72);
    imagefilledrectangle($im, 250, $y, 950, $y + 32, $line);
    imagefilledrectangle($im, 250, $y, 250 + (120 * ($i + 2)), $y + 32, $i % 2 ? $green : $teal);
}

imagestring($im, 4, 300, 940, 'Voice assistant bundle for EnMS / ISO 50001 workflows', $muted);
imagestring($im, 3, 405, 1015, 'WASABI digital download', $muted);

imagejpeg($im, $source, 92);
imagedestroy($im);

Db::getInstance()->execute(
    'UPDATE `' . _DB_PREFIX_ . 'image` SET cover = NULL WHERE id_product = ' . (int) $idProduct
);

$image = new Image();
$image->id_product = $idProduct;
$image->position = Image::getHighestPosition($idProduct) + 1;
$image->cover = true;

if (!$image->add()) {
    throw new RuntimeException('Could not create product image row.');
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
    'id_product' => $idProduct,
    'id_image' => (int) $image->id,
    'path' => $path . '.jpg',
], JSON_PRETTY_PRINT) . PHP_EOL;
