<?php

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;

$json = file_get_contents('php://input');
$data = json_decode($json);

// Check if decoding was successful
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(
        [
            'error' => json_last_error_msg(),
            'data' => $json
        ]
    );
    exit;
}


require_once dirname(__FILE__) . '/config/config.inc.php';
require_once dirname(__FILE__) . '/init.php';
require_once dirname(__FILE__) . '/modules/ets_marketplace/classes/Ets_mp_product.php';
require_once dirname(__FILE__) . '/modules/ets_marketplace/classes/seller.php';

$sellerID = $data->Candidate_Seller_ID;
unset($data->Candidate_Seller_ID);
$productId = Product::getIdByReference($data->SkillID);

if ($productId) {
    // Product already exists
    header('Content-Type: application/json');
    http_response_code(409);
    echo json_encode(
        [
            'error' => 'Product already exists',
        ]
    );
    exit;
}
 
$product = new Product();

$langsIds = Language::getIDs();

$product->name = array_fill_keys($langsIds, $data->SkillName);
$link_rewrite = Tools::link_rewrite($data->SkillName);
$product->link_rewrite = array_fill_keys($langsIds, $link_rewrite);
$product->price = 10.05; // Set the price


$product->id_category_default = 12; // Default category ID
$product->active = 1; // Make the product active
$product->reference = $data->SkillID; 
$product->description = array_fill_keys($langsIds, $data->Description->en);
$product->description_short = array_fill_keys(
    $langsIds, 'From : '.$data->OwnerID.' , Tags: '.implode(', ', $data->Tags)
);
$product->product_type = ProductType::TYPE_VIRTUAL;
$product->is_virtual = true;

//this needs to be a pre + maybe if we had the existing one we could limit the list in the ui?

$product->skill = json_encode($data, JSON_PRETTY_PRINT);//"{}"; // Replace with skill in json format

$product->price =  $data->Price_Declared;

if ($product->add()) {
    // Assign the product to categories
    $product->addToCategories(array(12)); // Replace with your category IDs
    StockAvailable::setQuantity($product->id, 0, 100); // 100 is the stock quantity

    // Search seller by WLS_ID
    $seller = (new PrestaShopCollection('Customer'))->where('wls_id', '=', $sellerID)->getFirst();
    if ($seller instanceof Customer) {
        Ets_mp_product::addProductSeller($product->id, $seller->id);
    }

    // Amazzingfilter handles products listing, we force indexing
    $amazzingfilter = Module::getInstanceByName('amazzingfilter');
    if ($amazzingfilter instanceof AmazzingFilter) {
        $amazzingfilter->indexProduct([$product->id]);
    }

    header('Content-Type: application/json');
    echo    json_encode([   'success' => $product->id]) ;
} else {
    header('Content-Type: application/json');
    http_response_code(500);
    echo  json_encode( [   'error' => 'Failed to created product']);
}
