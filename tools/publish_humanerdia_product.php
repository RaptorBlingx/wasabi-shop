<?php

require dirname(__DIR__) . '/config/config.inc.php';

$catalog = require __DIR__ . '/humanerdia_product_catalog.php';
$slug = getenv('HUMANERDIA_PRODUCT_SLUG') ?: ($argv[1] ?? 'ovos-skill');

if (!isset($catalog[$slug])) {
    fwrite(STDERR, "Unknown HumanEnerDIA product slug: {$slug}\n");
    exit(1);
}

$config = $catalog[$slug];
$uploadDir = dirname(__DIR__) . '/upload/';
$sourceFile = getenv('HUMANERDIA_RELEASE_FILE')
    ?: getenv('HUMANERDIA_RELEASE_ZIP')
    ?: $uploadDir . $config['artifact_filename'];
$checksum = resolveChecksum(
    getenv('HUMANERDIA_RELEASE_SHA256') ?: null,
    getenv('HUMANERDIA_RELEASE_CHECKSUM_FILE') ?: ($uploadDir . $config['checksum_filename'])
);

if (!is_readable($sourceFile)) {
    fwrite(STDERR, "Release file not readable: {$sourceFile}\n");
    exit(1);
}

$languages = Language::getLanguages(false);
$name = $config['name'];
$rewrite = Tools::link_rewrite($name);
$description = $config['description'];

if ($checksum) {
    $description .= '<p><strong>Artifact SHA256:</strong> <code>'
        . htmlspecialchars($checksum, ENT_QUOTES, 'UTF-8')
        . '</code></p>';
}

$existingProductId = (int) Db::getInstance()->getValue(
    'SELECT id_product FROM ' . _DB_PREFIX_ . 'product WHERE reference = "' . pSQL($config['reference']) . '"'
);

$product = $existingProductId ? new Product($existingProductId) : new Product();
$product->reference = $config['reference'];
$product->id_shop_default = 1;
$product->id_category_default = (int) $config['category_id'];
$product->id_tax_rules_group = 5;
$product->price = (float) $config['price'];
$product->wholesale_price = 0;
$product->minimal_quantity = 1;
$product->active = 1;
$product->available_for_order = 1;
$product->show_price = 1;
$product->online_only = 1;
$product->visibility = 'both';
$product->redirect_type = '404';
$product->condition = 'new';
$product->is_virtual = 1;
$product->product_type = 'virtual';
$product->state = 1;
$product->indexed = 1;

foreach ($languages as $language) {
    $idLang = (int) $language['id_lang'];
    $product->name[$idLang] = $name;
    $product->link_rewrite[$idLang] = $rewrite;
    $product->description_short[$idLang] = $config['short_description'];
    $product->description[$idLang] = $description;
    $product->meta_title[$idLang] = $name;
    $product->meta_description[$idLang] = $config['meta_description'];
    $product->available_now[$idLang] = $config['available_now'];
}

$ok = $existingProductId ? $product->update() : $product->add();
if (!$ok) {
    fwrite(STDERR, "Failed to save product\n");
    exit(1);
}

$productId = (int) $product->id;
$product->updateCategories([(int) $config['category_id']]);
StockAvailable::setQuantity($productId, 0, 1000, 1);
Product::updateIsVirtual($productId, true);
Configuration::updateValue('PS_VIRTUAL_PROD_FEATURE_ACTIVE', 1);

$oldDownloadId = (int) ProductDownload::getIdFromIdProduct($productId, false);
if ($oldDownloadId) {
    $oldDownload = new ProductDownload($oldDownloadId);
    $oldDownload->delete(true);
}

$storedFilename = ProductDownload::getNewFilename();
$targetFile = _PS_DOWNLOAD_DIR_ . $storedFilename;
if (!copy($sourceFile, $targetFile)) {
    fwrite(STDERR, "Failed to copy release file to {$targetFile}\n");
    exit(1);
}
@chmod($targetFile, 0664);

$download = new ProductDownload();
$download->id_product = $productId;
$download->display_filename = basename($sourceFile);
$download->filename = $storedFilename;
$download->date_add = date('Y-m-d H:i:s');
$download->date_expiration = '0000-00-00 00:00:00';
$download->nb_days_accessible = 0;
$download->nb_downloadable = 0;
$download->active = 1;
$download->is_shareable = 0;
if (!$download->add()) {
    fwrite(STDERR, "Failed to create product download row\n");
    exit(1);
}

Db::getInstance()->execute(
    'DELETE FROM ' . _DB_PREFIX_ . 'ets_mp_seller_product WHERE id_product = ' . $productId
);
Db::getInstance()->insert('ets_mp_seller_product', [
    'id_customer' => (int) $config['seller_customer_id'],
    'id_product' => $productId,
    'approved' => 1,
    'active' => 1,
    'is_admin' => 1,
    'reason' => null,
]);

Search::indexation(false, $productId);
Product::flushPriceCache();

echo json_encode([
    'slug' => $slug,
    'id_product' => $productId,
    'reference' => $config['reference'],
    'name' => $name,
    'category_id' => (int) $config['category_id'],
    'seller_id' => (int) $config['seller_id'],
    'download_file' => $download->display_filename,
    'stored_file' => $storedFilename,
    'sha256' => $checksum,
], JSON_PRETTY_PRINT) . PHP_EOL;

function resolveChecksum(?string $directChecksum, string $checksumFile): ?string
{
    if ($directChecksum) {
        return trim($directChecksum);
    }

    if (!is_readable($checksumFile)) {
        return null;
    }

    $contents = trim((string) file_get_contents($checksumFile));
    if ($contents === '') {
        return null;
    }

    $parts = preg_split('/\s+/', $contents);
    return $parts[0] ?? null;
}
