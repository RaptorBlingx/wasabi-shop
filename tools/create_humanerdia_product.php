<?php

require dirname(__DIR__) . '/config/config.inc.php';

$sourceFile = getenv('HUMANERDIA_RELEASE_ZIP') ?: dirname(__DIR__) . '/upload/HumanEnerDIA-OVOS-skill-v1.0.0.zip';
$checksum = getenv('HUMANERDIA_RELEASE_SHA256') ?: trim(@file_get_contents(dirname(__DIR__) . '/upload/HumanEnerDIA-OVOS-skill-v1.0.0.zip.sha256'));
$reference = 'HUMANERDIA-OVOS-SKILL-1.0.0';
$categoryId = 12; // Skills
$sellerId = 4;
$sellerCustomerId = 6;

if (!is_readable($sourceFile)) {
    fwrite(STDERR, "Release ZIP not readable: {$sourceFile}\n");
    exit(1);
}

$languages = Language::getLanguages(false);
$name = 'HumanEnerDIA OVOS Skill for Industrial Energy Management';
$rewrite = Tools::link_rewrite($name);
$shortDescription = '<p>OVOS-based digital assistant skill for manufacturing energy management, ISO 50001 context, machine status, anomalies, forecasts, KPIs, and action-plan queries.</p>';
$description = '<p><strong>HumanEnerDIA</strong> is an Open Voice OS skill for industrial energy management. It connects to the HumanEnerDIA/EnMS backend so operators can ask natural-language questions about factory energy performance, machine status, anomalies, forecasts, KPIs, and ISO 50001 action-plan context.</p>'
    . '<p><strong>Requirements:</strong> Docker, an OVOS-compatible runtime, and a reachable HumanEnerDIA/EnMS API endpoint. The optional Qwen GGUF model is distributed separately and is not included in this download.</p>'
    . '<p><strong>Installation summary:</strong> extract the ZIP, set <code>ENMS_API_URL</code>, install the skill with <code>python3 -m pip install -e .</code>, start the OVOS bridge, then run the smoke query: <code>what is the power of compressor one</code>.</p>'
    . '<p><strong>License/IPR:</strong> this WASABI artifact is offered under <code>Apache-2.0 OR GPL-3.0-or-later</code>. Backend services and optional model weights may have separate licenses.</p>'
    . '<p><strong>Known limitation:</strong> fast operational queries are ready for demonstration; the local LLM fallback is slower and should be presented as robustness support for difficult phrasing.</p>';

if ($checksum) {
    $description .= '<p><strong>ZIP SHA256:</strong> <code>' . htmlspecialchars($checksum, ENT_QUOTES, 'UTF-8') . '</code></p>';
}

$existingProductId = (int) Db::getInstance()->getValue(
    'SELECT id_product FROM ' . _DB_PREFIX_ . 'product WHERE reference = "' . pSQL($reference) . '"'
);

$product = $existingProductId ? new Product($existingProductId) : new Product();
$product->reference = $reference;
$product->id_shop_default = 1;
$product->id_category_default = $categoryId;
$product->id_tax_rules_group = 5; // zero-rate group for the free launch listing
$product->price = 0;
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
    $product->description_short[$idLang] = $shortDescription;
    $product->description[$idLang] = $description;
    $product->meta_title[$idLang] = $name;
    $product->meta_description[$idLang] = 'HumanEnerDIA OVOS skill bundle for WASABI White Label Shop distribution.';
    $product->available_now[$idLang] = 'Available as digital download';
}

$ok = $existingProductId ? $product->update() : $product->add();
if (!$ok) {
    fwrite(STDERR, "Failed to save product\n");
    exit(1);
}

$productId = (int) $product->id;
$product->updateCategories([$categoryId]);
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
    fwrite(STDERR, "Failed to copy release ZIP to {$targetFile}\n");
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
    'id_customer' => $sellerCustomerId,
    'id_product' => $productId,
    'approved' => 1,
    'active' => 1,
    'is_admin' => 1,
    'reason' => null,
]);

Search::indexation(false, $productId);
Product::flushPriceCache();

echo json_encode([
    'id_product' => $productId,
    'reference' => $reference,
    'name' => $name,
    'category_id' => $categoryId,
    'seller_id' => $sellerId,
    'download_file' => $download->display_filename,
    'stored_file' => $storedFilename,
], JSON_PRETTY_PRINT) . PHP_EOL;
