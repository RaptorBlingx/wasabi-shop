<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__.'/vendor/autoload.php'; // Include Composer autoload

use Web3\Web3;
use Web3\Contract;
use Web3p\EthereumTx\Transaction;
use GuzzleHttp\Client;
$infuraKey = "9929d93e0b5b4bd98970ea1e4c7d6176";

class NftRoyaltymanagementsystem extends Module {
    private $infuraKey;
   

    public function __construct() {
        
        $this->name = 'nftroyaltymanagementsystem';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Giorgos Lagos ICCS';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('NFT Royalty Management System');
        $this->description = $this->l('A framework to handle developer royalties on the Ethereum network.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->infuraKey = "9929d93e0b5b4bd98970ea1e4c7d6176";
    }

    public function install() {
        if (!parent::install() || !$this->installAdminTab() || !$this->registerHook('displayBackOfficeHeader') || !$this->registerHook('actionValidateOrder') || !$this->registerHook('displayAdminProductsExtra') || !$this->registerHook('displayCustomerAccount')) {
            return false; 
        }
        
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', '');

        // Other installation tasks
    
        // Create the database table
        return $this->createDatabaseTable();
    }
    
    private function createDatabaseTable() {
        $db = Db::getInstance();
    
        // Create smart_contract_address table
        $query1 = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "smart_contract_address` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `contract_address` VARCHAR(255) NOT NULL
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;";
        $result1 = $db->execute($query1);
    
        // Create private_key table
        $query2 = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "wallet_private_key` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `private_key` VARCHAR(255) NOT NULL
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;";
        $result2 = $db->execute($query2);

        $query3 = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "wallet_public_address` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `public_address` VARCHAR(255) NOT NULL
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;";
        $result3 = $db->execute($query3);

        $query4 = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "royalties_owed` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `royalties` VARCHAR(255) NOT NULL
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;";
        $result4 = $db->execute($query4);

        $queryCreateProductIdsTable = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "product_ids` (
            `product_id` INT(11) UNSIGNED NOT NULL,
            PRIMARY KEY (`product_id`)
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";
        
        $resultCreateProductIdsTable = $db->execute($queryCreateProductIdsTable);
        

        $db->insert('smart_contract_address', array(
            'contract_address' => pSQL("NotSet"),
        ));

        $db->insert('wallet_private_key', array(
            'private_key' => pSQL("NotSet"),
        ));
        $db->insert('wallet_public_address', array(
            'public_address' => pSQL("NotSet"),
        ));
        $db->insert('royalties_owed', array(
            'royalties' => pSQL("NotSet"),
        ));
        
    
        return $result1 && $result2 && $result3 && $result4;
    }
    
    public function getContent()
{
    Tools::redirectAdmin($this->context->link->getAdminLink('AdminNFTPanel'));
}

    public function uninstall() {
        if (!parent::uninstall() || !$this->deleteDatabaseTable()) {
            return false;
        }
    
        if (!$this->uninstallAdminTab()) {
            return false;
        }

        return true;
    }
    
    private function deleteDatabaseTable() {
        $db = Db::getInstance();
    
        // Delete smart_contract_address table
        $query1 = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "smart_contract_address`;";
        $result1 = $db->execute($query1);
    
        // Delete private_key table
        $query2 = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "wallet_private_key`;";
        $result2 = $db->execute($query2);

        $query3 = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "wallet_public_address`;";
        $result3 = $db->execute($query3);

        $query4 = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "royalties_owed`;";
        $result4 = $db->execute($query4);

        $queryDropProductIdsTable = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "product_ids`;";
        $resultDropProductIdsTable = $db->execute($queryDropProductIdsTable);
    
        return $result1 && $result2 && $result3 && $resultDropProductIdsTable;
    }

    public function hookDisplayAdminProductsExtra($params) {
        if (session_id() == '') {
            session_start();
        }
        
        // Generate a new CSRF token on each page load
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
      /*  if (isset($this->context->link) && is_object($this->context->link)) {
            // If link object is available
            $url = $this->context->link->getModuleLink('myModuleName', 'myControllerName');
        } else {
            // Fallback or alternative method to generate the URL
            $url = $this->context->shop->getBaseURL() . 'index.php?fc=module&module=myModuleName&controller=myControllerName';
        }
                    */

        // Assign the URL to the Smarty template

    $ajaxUrl = $this->context->link->getModuleLink('nftroyaltymanagementsystem', 'ajax', array('action' => 'mintNFTSkill'));
    $productId = (int)$params['id_product'];
    $this->context->smarty->assign('ajaxUrl', $ajaxUrl);
    // Assuming you are inside a method of your module class
    $db = Db::getInstance();
    $sql = 'SELECT `contract_address` FROM `' . _DB_PREFIX_ . 'smart_contract_address` WHERE id = 1'; // Adjust as per your requirement
    $contractAddress = $db->getValue($sql);

    // Check if the contract address is set to "NotSet"
    $is_valid = ($contractAddress !== "NotSet");

    $queryCheckProduct = "SELECT * FROM `" . _DB_PREFIX_ . "product_ids` WHERE `product_id` = " . (int)$productId;


    $productExists = (bool)$db->getValue($queryCheckProduct);
    // Check if any rows are returned
  

    // Assign the boolean value to Smarty
    $this->context->smarty->assign('is_valid', $is_valid);
    $this->context->smarty->assign('skillnotexist', !$productExists);

    $this->context->smarty->assign(array(
        'csrf_token' => $_SESSION['csrf_token'], // Assign new CSRF token to Smarty
        'product_id' => $productId,
  //      'link' => $url // Assign AdminNFTPanel link to Smarty
        // other variables
    ));
    
        return $this->display(__FILE__, 'views/templates/admin/productmint.tpl');
}
    
public function hookDisplayCustomerAccount($params)
{
    $this->context->smarty->assign(array(
        'my_custom_link' => $this->context->link->getModuleLink('nftroyaltymanagementsystem', 'mycustompage')
    ));
    return $this->display(__FILE__, 'views/templates/front/myaccount_tab.tpl');
}



public function getEthereumPrice()
{
    $url = "https://api.coingecko.com/api/v3/simple/price?ids=ethereum&vs_currencies=eur"; // URL to fetch Ethereum price in EUR

    $maxAttempts = 5; // Maximum number of retry attempts
    $retryDelay = 2; // Delay between attempts in seconds
    $attempt = 0; // Current attempt counter
    $result = null; // To store the result of the request

    while ($attempt < $maxAttempts && is_null($result)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            // Log the error and reset the result to null if a cURL error occurs
            echo 'Attempt ' . $attempt . ' failed: Error:' . curl_error($ch) . "\n";
            $result = null; // Reset result to continue the loop
            $attempt++;
            sleep($retryDelay); // Wait for a specified delay before retrying
        }
        
        curl_close($ch); // Always close the cURL handle
        
        // If result is successfully obtained, break the loop
        if ($result) {
            break;
        }
    }

    // After exiting the loop, check if a result was successfully obtained
    if (!is_null($result)) {
        $response = json_decode($result, true);
        
        if (isset($response['ethereum']['eur'])) {
            return $response['ethereum']['eur'];
        }
    }
    
    // If no result after all attempts or the 'eur' price isn't available, handle the error
    echo "Error: Unable to retrieve Ethereum price in EUR after $maxAttempts attempts." . "\n";
    return null;
}
public function getEthereumPriceBackup()
{
    $url = "https://min-api.cryptocompare.com/data/price?fsym=ETH&tsyms=EUR";

    $maxAttempts = 5; // Maximum number of retry attempts
    $retryDelay = 2; // Delay between attempts in seconds
    $attempt = 0; // Current attempt counter
    $result = null; // To store the result of the request

    while ($attempt < $maxAttempts && is_null($result)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            // Log the error
            $error = 'Attempt ' . $attempt . ' failed: Error:' . curl_error($ch) . PHP_EOL;
            echo $error;
            $this->logMessage($error); // Log message to file
            $result = null; // Reset result to continue the loop
            $attempt++;
            sleep($retryDelay); // Wait for a specified delay before retrying
        }
        
        curl_close($ch); // Always close the cURL handle

        // If result is successfully obtained, break the loop
        if ($result) {
            break;
        }
    }

    if (!is_null($result)) {
        $response = json_decode($result, true);

        // Log the successful response
        $this->logMessage("Using backup function: " . json_encode($response) . PHP_EOL);

        if (isset($response['EUR'])) {
            return $response['EUR'];
        }
    }

    // If no result after all attempts or the 'EUR' price isn't available
    $errorMessage = "Error: Unable to retrieve Ethereum price in EUR after $maxAttempts attempts." . PHP_EOL;
    echo $errorMessage;
    $this->logMessage($errorMessage); // Log error message to file
    return null;
}

private function logMessage($message)
{
    $filePath = _PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt'; // Define the file path
    file_put_contents($filePath, $message, FILE_APPEND); // Append the log message to the file
}
    
    
    public function getEthereumPriceUSD()
{
    $url = "https://api.coingecko.com/api/v3/simple/price?ids=ethereum&vs_currencies=usd";

    $maxAttempts = 5; // Maximum number of retry attempts
    $retryDelay = 2; // Delay between attempts in seconds
    $attempt = 0; // Current attempt counter
    $result = null; // To store the result of the request

    while ($attempt < $maxAttempts && is_null($result)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            // If a cURL error occurs, log the error and reset the result to null
            echo 'Attempt ' . $attempt . ' failed: Error:' . curl_error($ch) . PHP_EOL;
            $result = null; // Reset result to continue the loop
            $attempt++;
            sleep($retryDelay); // Wait for a specified delay before retrying
        }
        
        curl_close($ch); // Close the cURL handle

        // If result is successfully obtained, break the loop
        if ($result) {
            break;
        }
    }

    // After exiting the loop, check if a result was successfully obtained
    if (!is_null($result)) {
        $response = json_decode($result, true);
        
        if (isset($response['ethereum']['usd'])) {
            return $response['ethereum']['usd'];
        }
    }
    
    // If no result after all attempts or the 'usd' price isn't available, handle the error
    echo "Error: Unable to retrieve Ethereum price in USD after $maxAttempts attempts." . PHP_EOL;
    return null;
}


    public function getRapidGasPrice()
    {
        $url = "https://sepolia.beaconcha.in/api/v1/execution/gasnow";
    
        $maxAttempts = 5; // Maximum number of retry attempts
        $retryDelay = 2; // Delay between attempts in seconds
        $attempt = 0; // Current attempt
        $result = null; // Result of the cURL request
    
        while ($attempt < $maxAttempts && is_null($result)) {
            // Initialize cURL session
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
            // Execute cURL session
            $result = curl_exec($ch);
    
            // Check for errors
            if (curl_errno($ch)) {
                echo 'Attempt ' . $attempt . ' failed: Error:' . curl_error($ch) . "\n";
                $result = null; // Ensure result is null to continue retrying
                $attempt++; // Increment attempt counter
                sleep($retryDelay); // Wait before retrying
            }
    
            curl_close($ch); // Always close the cURL session
        }
    
        if (is_null($result)) {
            // If still no result after retries, handle as needed
            return "0"; // Example fallback value
        }
    
        // Decode the JSON response
        $response = json_decode($result, true);
    
        // Check if the 'rapid' variable is set and return it
        if (isset($response['data']['rapid'])) {
            if ($response['data']['rapid'] == "0") $response['data']['rapid'] = $response['data']['standard'];
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Gas now: ' . $response['data']['rapid'] . PHP_EOL, FILE_APPEND);
            return $response['data']['rapid'];
        } else {
            // Handle the error case where the 'rapid' variable isn't available
            return "0";// Fallback value if 'rapid' not available
        }
    }
    


    
    
    public function saveContractAddress($contractAddress) {
        $db = Db::getInstance();
        $query = 'INSERT INTO ' . _DB_PREFIX_ . 'smart_contract_address (id, contract_address) 
          VALUES (1, \'' . pSQL($contractAddress) . '\') 
          ON DUPLICATE KEY UPDATE contract_address = \'' . pSQL($contractAddress) . '\'';
        return $db->execute($query);
    }
    
    public function getContractAddress() {
        $db = Db::getInstance();
        $query = 'SELECT contract_address FROM ' . _DB_PREFIX_ . 'smart_contract_address';
        $result = $db->getRow($query); // getRow fetches a single row of data
        return $result ? $result['contract_address'] : null;
    }
    public function installAdminTab()
{
    $id_parent = (int)Tab::getIdFromClassName('DEFAULT');

    // Create a new tab object
    $tab = new Tab();

    // Set the properties of the new tab object
    $tab->name = array();
    foreach (Language::getLanguages() as $lang) {
        $tab->name[$lang['id_lang']] = $this->l('Royalty Admin Dashboard');
    }
    $tab->class_name = 'AdminNFTPanel';
    $tab->module = $this->name;
    $tab->id_parent = $id_parent;

    // Add the new tab to the database
    if (!$tab->add()) {
        return false;
    }

    return true;
}

public function uninstallAdminTab()
{
    // Get the ID of your custom tab
    $id_tab = (int)Tab::getIdFromClassName('AdminNFTPanel');

    if ($id_tab) {
        // Delete the tab
        $tab = new Tab($id_tab);
        $tab->delete();
    }

    return true;
}

public function hookActionValidateOrder($params) {
    file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] ' . 'Testing from Order validation' . PHP_EOL . PHP_EOL, FILE_APPEND);
    
    if (!isset($params['order']) || !method_exists($params['order'], 'getProducts')) {
        // Handle the error appropriately
        return; // or throw an exception
    }

    $products = $params['order']->getProducts();
    if (null === $products) {
        // Handle the error if no products are found
        return; // or throw an exception
    }

    $ethPrice = $this->getEthereumPrice();
    if ($ethPrice == null) $ethPrice = $this->getEthereumPriceBackup();
    if ($ethPrice == null) {
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . 'Failed to fetch ETHER price'.PHP_EOL, FILE_APPEND);

        return false;

    }
    $db = Db::getInstance();
    file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . 'Current ethPrice: '. $ethPrice .PHP_EOL, FILE_APPEND);


    //
    $sqlPrivateKey = "SELECT private_key FROM " . _DB_PREFIX_ . "wallet_private_key WHERE id = 1";
        $resultPrivateKey = $db->executeS($sqlPrivateKey);
        $fromAddress = null;
        $contractaddress = null;
        $privateKey = null;
        $jsonFilePath = _PS_ROOT_DIR_ . '/modules/nftroyaltymanagementsystem/contractABI.json'; // Replace with actual file path
        $contractABIJson = file_get_contents($jsonFilePath);
        $contractABI = json_decode($contractABIJson, true);
        if ($resultPrivateKey && count($resultPrivateKey) > 0) {
            $privateKey = $resultPrivateKey[0]['private_key'];
        } else {
            // Handle the case where no result is found
            $privateKey = null;
        }
        $sqlContractAddress = "SELECT contract_address FROM " . _DB_PREFIX_ . "smart_contract_address WHERE id = 1";
        $resultContractAddress = $db->executeS($sqlContractAddress);
        if ($resultContractAddress && count($resultContractAddress) > 0) {
            $contractaddress = $resultContractAddress[0]['contract_address'];
        } else {
            // Handle the case where no result is found
            $contractaddress = null;
        }

        // Query to get the public address
        $sqlPublicAddress = "SELECT public_address FROM " . _DB_PREFIX_ . "wallet_public_address WHERE id = 1";
        $resultPublicAddress = $db->executeS($sqlPublicAddress);
        if ($resultPublicAddress && count($resultPublicAddress) > 0) {
            $fromAddress = $resultPublicAddress[0]['public_address'];
        } else {
            // Handle the case where no result is found
            $fromAddress = null;
        }

   

    //

    foreach ($products as $product) {
        $productID = $product['product_id'];
        $queryCheckProduct = "SELECT * FROM `" . _DB_PREFIX_ . "product_ids` WHERE `product_id` = " . (int)$productID;
        $resultCheckProduct = $db->executeS($queryCheckProduct);

        if ($resultCheckProduct) {
            $checkoutPrice = $product['total_price_tax_incl']; // Assuming this is the checkout price
            $checkoutPriceEth = null;

            if ($ethPrice !== null && $ethPrice > 0) {

                $checkoutPriceEth = number_format($checkoutPrice / $ethPrice, 4, '.', '');
                file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . 'Checkout price: '. $checkoutPrice .PHP_EOL, FILE_APPEND);
                file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . 'Donation converted in ether value: '. $checkoutPriceEth .PHP_EOL, FILE_APPEND);

            }
            $weiConversionFactor = bcpow("10", "18"); // This is 10^18

            // Convert Ether to Wei
            $checkoutPriceWei = bcmul($checkoutPriceEth, $weiConversionFactor);

            $this->tryDonateSkill($contractaddress, $contractABI, $fromAddress, $privateKey, $productID, $checkoutPriceWei);
            sleep(40);
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] ' . 'Product ID: ' . $productID . ' - Total eth: ' . $checkoutPriceEth . PHP_EOL . PHP_EOL, FILE_APPEND);
        }
    }

    $attemptLimit = 5;

    for ($attempt = 0; $attempt < $attemptLimit; $attempt++) {
        $royaltiesResponse = $this->getTotalRoyalties($contractaddress, $contractABI);

        // Check if the response is true
        if ($royaltiesResponse) {
            // If the response is true, break out of the loop
            break;
        }

        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', "Attempt " . $attempt. " to get totalRoyalties failed. Will try again" .  PHP_EOL, FILE_APPEND);

        sleep(10); // wait for 1 second
    }

    if (!$royaltiesResponse) {

        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', "Couldn't get totalRoyalties at all." .  PHP_EOL, FILE_APPEND);

    }


}
public function tryDonateSkill($contractaddress, $contractABI, $fromAddress, $privateKey, $productID, $checkoutPriceWei) {
    $maxAttempts = 5; // Maximum number of attempts
    $attempt = 0; // Current attempt counter
    $retryDelay = 2; // Delay between attempts in seconds
    $success = false; // Flag to track if donateSkill was successful

    // Convert Wei to Ether for logging purposes
  

    while (!$success && $attempt < $maxAttempts) {
        $success = $this->donateSkill($contractaddress, $contractABI, $fromAddress, $privateKey, $productID, $checkoutPriceWei);
        
        if ($success) {
            $logMessage = '[' . date('Y-m-d H:i:s') . '] Donation successful for Product ID: ' . $productID . ' - Total ETH: ' . $checkoutPriceEth . ' after ' . $attempt . ' attempts.' . PHP_EOL;
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', $logMessage, FILE_APPEND);
            break; // Exit the loop if donateSkill returns true
        } else {
            $attempt++; // Increment the attempt counter if donateSkill returns false
            $logMessage = '[' . date('Y-m-d H:i:s') . '] Attempt ' . $attempt . ' to donate for Product ID: ' . $productID . ' - Total ETH: ' . $checkoutPriceEth . ' failed, retrying...' . PHP_EOL;
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', $logMessage, FILE_APPEND);
            sleep($retryDelay); // Wait for the specified delay before retrying
        }
    }

    if (!$success) {
        $logMessage = '[' . date('Y-m-d H:i:s') . '] Failed to complete donation for Product ID: ' . $productID . ' - Total ETH: ' . $checkoutPriceEth . ' after ' . $maxAttempts . ' attempts.' . PHP_EOL;
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', $logMessage, FILE_APPEND);
    }
}



public function hookActionProductSave($params)
{
    //$productId = $params['id_product'];
    $productinstance = $params['product'];
    $isActive = $productinstance->active;
  //  file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. 'A product has been updated.'. PHP_EOL . PHP_EOL, FILE_APPEND);
    // Now you can use $isActive for your logic
    if ($isActive) {
       // file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. 'A product has been activated.'. PHP_EOL . PHP_EOL, FILE_APPEND);
    if (isset($params['product']) && is_object($params['product'])) {
        $jsonFilePath = _PS_ROOT_DIR_ . '/modules/nftroyaltymanagementsystem/contractABI.json'; // Replace with actual file path
        $contractABIJson = file_get_contents($jsonFilePath);
        $contractABI = json_decode($contractABIJson, true);
        $db = Db::getInstance();
        $productID = $params['product']->id;
        $queryCheckProduct = "SELECT * FROM `" . _DB_PREFIX_ . "product_ids` WHERE `product_id` = " . (int)$productID;
        $resultCheckProduct = $db->executeS($queryCheckProduct);


        
        if (!empty($resultCheckProduct)) {
            // The product ID exists in the table
         //   file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. 'Update of an existing skill, no need to transact on chain.'. PHP_EOL . PHP_EOL, FILE_APPEND);
         //   echo "Product ID " . $productID . " exists in the table.";
        } else {

           
            // The product ID does not exist in the table
            echo "Product ID " . $productID . " does not exist in the table.";

    // Prepare the insert query
            //$db = Db::getInstance();
            $sqlPrivateKey = "SELECT private_key FROM " . _DB_PREFIX_ . "wallet_private_key WHERE id = 1";
            $resultPrivateKey = $db->executeS($sqlPrivateKey);
            $fromAddress = null;
            $contractaddress = null;
            $privateKey = null;
            if ($resultPrivateKey && count($resultPrivateKey) > 0) {
                $privateKey = $resultPrivateKey[0]['private_key'];
            } else {
                // Handle the case where no result is found
                $privateKey = null;
            }
            $sqlContractAddress = "SELECT contract_address FROM " . _DB_PREFIX_ . "smart_contract_address WHERE id = 1";
            $resultContractAddress = $db->executeS($sqlContractAddress);
            if ($resultContractAddress && count($resultContractAddress) > 0) {
                $contractaddress = $resultContractAddress[0]['contract_address'];
            } else {
                // Handle the case where no result is found
                $contractaddress = null;
            }

            // Query to get the public address
            $sqlPublicAddress = "SELECT public_address FROM " . _DB_PREFIX_ . "wallet_public_address WHERE id = 1";
            $resultPublicAddress = $db->executeS($sqlPublicAddress);
            if ($resultPublicAddress && count($resultPublicAddress) > 0) {
                $fromAddress = $resultPublicAddress[0]['public_address'];
            } else {
                // Handle the case where no result is found
                $fromAddress = null;
            }
            if ($contractaddress != "NotSet") {
            $queryInsertProduct = "INSERT INTO `" . _DB_PREFIX_ . "product_ids` (`product_id`) VALUES (" . (int)$productID . ")";
            $resultInsertProduct = $db->execute($queryInsertProduct);

            if ($resultInsertProduct) {
                echo "Product ID " . $productID . " has been successfully inserted into the table.";
            } else {
                echo "There was an error inserting Product ID " . $productID . " into the table.";
            }


            $developerWallets = [
                '0x7F8842f16aF8F86b76c3cC8757c2E19C45D4faB8',
                '0x7f5FE5F88c4568c645CAB807A87da6eBE35C2b17'
            ];
            $allocations = [
                50, // Allocation for the first developer wallet
                50  // Allocation for the second developer wallet
            ];
            $dependentSkills = [];
            $dependencyAllocations = [];
    
            //
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. 'A new skill has been uploaded.'. PHP_EOL . PHP_EOL, FILE_APPEND);
            $this->tryMintSkill(
                $contractaddress, // Replace with your contract address
                $contractABI,
                $fromAddress, // From address
                $privateKey, // Private key
                (string) $productID,
                $developerWallets,
                $allocations,
                $dependentSkills,
                $dependencyAllocations
            );
    
        }
    else {
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. 'A new skill has been uploaded but NFT royalty system is not activated.'. PHP_EOL . PHP_EOL, FILE_APPEND);
    }
    }
        
    }
    }
   // $activated = $params['activated'];
  //  file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. $params. PHP_EOL . PHP_EOL, FILE_APPEND);
    //file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. $product. PHP_EOL . PHP_EOL, FILE_APPEND);
    

}
public function tryMintSkill($contractaddress, $contractABI, $fromAddress, $privateKey, $productID, $developerWallets, $allocations, $dependentSkills, $dependencyAllocations) {
    $maxAttempts = 5;
    $attempt = 0;
    $retryDelay = 2;

    while ($attempt < $maxAttempts) {
        $attempt++;
        $success = false; // Explicitly set $success to false at the start of each attempt

        $this->logMessage("Attempting to mint skill with identifier: $productID, attempt $attempt");

        try {
            $success = $this->mintSkill(
                $contractaddress,
                $contractABI,
                $fromAddress,
                $privateKey,
                (string) $productID,
                $developerWallets,
                $allocations,
                $dependentSkills,
                $dependencyAllocations
            );

            if ($success === true) {
                $this->logMessage("mintSkill successful for Product ID: $productID on attempt $attempt.");
                return true;
            } else {
                $this->logMessage("Got this Minting skill response: " . var_export($success, true));
            }
        } catch (Exception $e) {
            $this->logMessage("mintSkill attempt $attempt for Product ID: $productID threw an exception: " . $e->getMessage());
        }

        if (!$success && $attempt < $maxAttempts) {
            $this->logMessage("Retrying in $retryDelay seconds...");
            sleep($retryDelay);
        }
    }

    $this->logMessage("Failed to mintSkill for Product ID: $productID after $maxAttempts attempts.");
    return false;
}


public function tryDistributeSmartContractRoyalties($contractABI, $contractAddress, $fromAddress, $privateKey) {
    $maxAttempts = 5; // Define the maximum number of retry attempts
    $attempt = 0; // Initialize the attempt counter
    $retryDelay = 2; // Define the delay between retry attempts in seconds
    $success = false; // Flag to monitor success status

    while (!$success && $attempt < $maxAttempts) {
        $attempt++; // Increment attempt counter

        $this->logMessage("Attempting to distribute royalties, attempt $attempt");

        try {
            $success = $this->distributeSmartContractRoyalties(
                $contractABI, 
                $contractAddress, 
                $fromAddress, 
                $privateKey
            );

            if ($success === true) {
                $this->logMessage("Royalties distribution successful on attempt $attempt.");
                return true; // Operation was successful, exit the loop and function
            } else {
                $this->logMessage("Royalties distribution attempt $attempt failed, retrying...");
            }
        } catch (Exception $e) {
            $this->logMessage("Royalties distribution attempt $attempt failed with an exception: " . $e->getMessage());
            // Explicitly set $success to false to ensure the loop can continue after an exception
            $success = false;
        }

        if (!$success && $attempt < $maxAttempts) {
            $this->logMessage("Retrying in $retryDelay seconds...");
            sleep($retryDelay); // Wait before retrying if not the last attempt
        }
    }

    if (!$success) {
        $this->logMessage("Failed to distribute royalties after $maxAttempts attempts.");
    }

    return false; // Return false as the operation was not successful after all attempts
}


    public function hookDisplayBackOfficeHeader($params) {
       


        $jsonFilePath = _PS_ROOT_DIR_ . '/modules/nftroyaltymanagementsystem/contractABI.json'; // Replace with actual file path
        $contractABIJson = file_get_contents($jsonFilePath);

        if ($contractABIJson === false) {
            // Handle error, file not found or unable to read
        }

        // Decode JSON string to PHP array
        $contractABI = json_decode($contractABIJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle JSON decoding error
        }
        $bytecodeFilePath = _PS_ROOT_DIR_ . '/modules/nftroyaltymanagementsystem/contractBytecode.txt'; 
        $contractBytecode = file_get_contents($bytecodeFilePath);
        
        if ($contractBytecode === false) {
            // Handle error, file not found or unable to read
        }

        // MINT SKILL
        // Hardcoded example values for demonstration purposes
        
        /*$this->deploySmartContract($contractABI,$contractBytecode,'0x07D7DBAE2a0203280c0783b0a2d1F2a8E09dC312','dacf2f38ad67317f4ce4e0d43489f1b69e3cb904b567a5f3550fd93b8be715c7');

        $this->getEthBalance("0x07D7DBAE2a0203280c0783b0a2d1F2a8E09dC312");*/
        
    }

    


public function deploySmartContract($contractABI, $contractBytecode, $fromAddress, $privateKey) {
    $success = true;
    try {
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', '±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±' . PHP_EOL, FILE_APPEND);
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. 'BOOTSTRAPPING NFT ROYALTY MANAGEMENT SYSTEM' . PHP_EOL . PHP_EOL, FILE_APPEND);
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', '±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±±' . PHP_EOL. PHP_EOL, FILE_APPEND);

        $web3 = new Web3('https://sepolia.infura.io/v3/' . $this->infuraKey);
        $eth = $web3->eth;
      
        // Initialize the contract with the ABI and Bytecode
        $contract = new Contract($web3->provider, $contractABI);
        $data = '0x' . $contractBytecode;

        // Get the current nonce
        $eth->getTransactionCount($fromAddress, function ($err, $nonce) use ($eth, $fromAddress, $data, $privateKey, &$success) {
            if ($err !== null) {
                file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Error: ' . $err->getMessage() . PHP_EOL, FILE_APPEND);
                $success = false;
                return;
            }

            // Create the transaction
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', '[' . date('Y-m-d H:i:s') . '] '.'Preparing admin wallet deployment transaction with nonce: ' . $nonce . PHP_EOL, FILE_APPEND);
           
            $transaction = [
                'nonce' => '0x' . dechex($nonce->toString()),
                'from' => $fromAddress,
                'data' => $data,
                'gas' => '0x' . dechex(8000000), 
                'gasPrice' => '0x' . dechex($this->getRapidGasPrice()),  //https://sepolia.beaconcha.in/gasnow
                'chainId' => 11155111, // chain ID here
            ];
            // Sign the transaction using ethereum-tx
            $tx2 = new Transaction($transaction);
           
            $signedTransaction = $tx2->sign($privateKey);
           
            // Send the transaction
            $eth->sendRawTransaction('0x' . $signedTransaction, function ($err, $txHash) use ($eth, &$success) {
                if ($err !== null) {
                    file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Send Error: ' . $err->getMessage() . PHP_EOL, FILE_APPEND);
                    $success = false;
                    return;
                }
                file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', '[' . date('Y-m-d H:i:s') . '] '.'Transaction has been submitted with hash: '. $txHash . PHP_EOL, FILE_APPEND);
            
                // Initialize polling variables
                $retryCount = 0;
                $maxRetries = 12;
                $interval = 15; // seconds
                $receipt = null;
            
                while ($retryCount < $maxRetries && !$receipt) {
                    $eth->getTransactionReceipt($txHash, function ($err, $r) use ($eth, &$receipt, &$retryCount, &$success) {
                        if ($err !== null) {
                            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Waiting for transaction to be verified on-chain..' . $err->getMessage() . PHP_EOL, FILE_APPEND);
                        } elseif ($r) {
                            $receipt = $r;
                            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', '[' . date('Y-m-d H:i:s') . '] '. 'Transaction has been succesfully verified on-chain: ' . json_encode($receipt) . PHP_EOL, FILE_APPEND);
                        }
                    });
            
                    if ($receipt) break; // Exit if receipt is found
            
                    sleep($interval); // Wait for the next retry
                    $retryCount++;
                }
            
                if (!$receipt) {
                    $success = false;
                    file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Max retries reached without obtaining a receipt - SYSTEM FAIL' . PHP_EOL, FILE_APPEND);
                    return;
                }
            
                if ($receipt && isset($receipt->contractAddress)) {
                    $etherscanKey = "WD2EHXDMAAGN37R5DKYT6A6FF6163227EY";
                    $apiEtherscanUrl = 'https://api-sepolia.etherscan.io/api';
                    $contractAddress = $receipt->contractAddress;
                    $filePath = _PS_ROOT_DIR_ . '/modules/nftroyaltymanagementsystem/solidityCode.sol';
                    $flattenedSourceCode = file_get_contents($filePath);
            
                    $verificationData = [
                        'apikey' => $etherscanKey,
                        'module' => 'contract',
                        'action' => 'verifysourcecode',
                        'contractaddress' => $contractAddress,
                        'sourceCode' => $flattenedSourceCode,
                        'contractName' => 'YourContractName',
                        'compilerVersion' => 'v0.8.19+commit.7dd6d404',
                        'optimizationUsed' => 1, // 1 for 'Yes', 0 for 'No'
                        'runs' => 20, // Number of optimization runs
                        // Include any other required fields
                    ];
            
                    $client = new Client();
                    $verificationRetryCount = 0;
                    $verificationMaxRetries = 10;
                    $verificationInterval = 10; // seconds
            
                    while ($verificationRetryCount < $verificationMaxRetries) {
                        try {
                            $response = $client->request('POST', $apiEtherscanUrl, [
                                'form_params' => $verificationData
                            ]);
            
                            $responseBody = $response->getBody()->getContents();
                            $responseData = json_decode($responseBody, true);
                           
            
                            if ($responseData['status'] == "1") {
                                file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', '[' . date('Y-m-d H:i:s') . '] '. 'Contract source code has been succesfully verified on Etherscan. Response from verification API request: '.json_encode($responseData) . PHP_EOL, FILE_APPEND);
                                break; // Successful response, exit loop
                            }
                        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Attempting to verify contract source code on Etherscan..' . PHP_EOL, FILE_APPEND);
                        }
            
                        sleep($verificationInterval);
                        $verificationRetryCount++;
                    } // Loop end
            
                   
            
                    if ($verificationRetryCount == $verificationMaxRetries) {
                        $success = false;
                        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Maximum verification retries reached without success - SYSTEM FAIL.' . PHP_EOL, FILE_APPEND);
                        return;
                    }
                    $this->saveContractAddress($contractAddress);

                } else {
                    file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Something has gone wrong while bootstrapping the system' . PHP_EOL, FILE_APPEND);
                }
            });
            
        });
    } 
    catch (Exception $e) {
        $success = false;
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Exception: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
    return $success;
}
public function tryDeploySmartContract($contractABI, $contractBytecode, $fromAddress, $privateKey) {
    $maxAttempts = 5; // Maximum number of retry attempts
    $retryDelay = 2; // Delay between attempts in seconds
    $attempt = 0; // Current attempt counter
    $deployResponse = null; // Initialize deployment response
    
    while ($attempt < $maxAttempts && is_null($deployResponse)) {
        $attempt++; // Increment attempt counter at the beginning of each loop iteration
        
        try {
            // Attempt to deploy the smart contract
            $deployResponse = $this->deploySmartContract($contractABI, $contractBytecode, $fromAddress, $privateKey);
            
            // Assuming a false response indicates failure and should trigger a retry
            if ($deployResponse === false) {
                $deployResponse = null; // Ensure loop continues to retry
                $this->logMessage("Attempt $attempt to deploy Smart Contract failed, retrying in $retryDelay seconds...");
            } else if ($deployResponse) {
                // A truthy $deployResponse indicates success
                $this->logMessage("Smart Contract deployed successfully on attempt $attempt.");
                break; // Exit the loop if deployment is successful
            }
        } catch (Exception $e) {
            // Log any exceptions and ensure the loop can continue by resetting $deployResponse
            $deployResponse = null;
            $this->logMessage("Attempt $attempt to deploy Smart Contract failed with an exception: " . $e->getMessage());
        }
        
        if (is_null($deployResponse) && $attempt < $maxAttempts) {
            sleep($retryDelay); // Wait before retrying if not the last attempt
        }
    }
    
    if (is_null($deployResponse)) {
        // Log failure after maximum attempts
        $this->logMessage("Failed to deploy Smart Contract after $maxAttempts attempts.");
    }
    
    return $deployResponse; // Return the deployment response, or null if unsuccessful
}

    
    
    public function getEthBalance($address) {
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Attempting to get balance.' . PHP_EOL, FILE_APPEND);

        try {
            $web3 = new Web3('https://sepolia.infura.io/v3/9929d93e0b5b4bd98970ea1e4c7d6176');
            $eth = $web3->eth;
        
            $eth->getBalance($address, function ($err, $balance) use ($address) {
                $logMessage = '[' . date('Y-m-d H:i:s') . '] Address: ' . $address . ' - ';
        
                if ($err !== null) {
                    $logMessage .= 'Error: ' . $err->getMessage();
                } else {
                    $logMessage .= 'Balance: ' . $balance . ' ETH';
                }
        
                file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt',  $logMessage . PHP_EOL, FILE_APPEND);

                $contractAddress = $this->getContractAddress();

                if ($contractAddress) {
                    // Log the retrieved contract address
                    file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Retrieved Contract Address: ' . $contractAddress . PHP_EOL, FILE_APPEND);
                } else {
                    // Log an error or a notice if the contract address is not found
                    file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'No Contract Address found in the database.' . PHP_EOL, FILE_APPEND);
                }

            });
        } catch (Exception $e) {
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
        }
    }

  //  getEthBalance("0x07D7DBAE2a0203280c0783b0a2d1F2a8E09dC312");
  public function getTotalRoyalties($contractAddress, $contractABI) {
    $success = true;
    try {
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', '[' . date('Y-m-d H:i:s') . '] Attempting to calculate total royalties.' . PHP_EOL, FILE_APPEND);
        $web3 = new Web3('https://sepolia.infura.io/v3/' . $this->infuraKey);
        $eth = $web3->eth;

        $contract = new Contract($web3->provider, $contractABI);
        $contract->at($contractAddress);

        // Call the calculateTotalRoyalties function
        $contract->call('calculateTotalRoyalties', function ($err, $result) use (&$success) {
            if ($err !== null) {
                $success = false;
                file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Error: ' . $err->getMessage() . PHP_EOL, FILE_APPEND);
                return;
            }

            $totalRoyalties = $result[0]->toString(); // Assuming the result is in the first index

            // Log the total royalties
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', '[' . date('Y-m-d H:i:s') . '] Total Royalties: ' . $totalRoyalties . PHP_EOL, FILE_APPEND);

            // Save to database
            $db = Db::getInstance();
            $sqlUpdateRoyalties = "UPDATE `" . _DB_PREFIX_ . "royalties_owed` SET royalties = '" . $totalRoyalties . "' WHERE id = 1;";
            $db->execute($sqlUpdateRoyalties);
        });

    } catch (Exception $e) {
        $success = false;
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Exception Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
    return $success;
}
 
public function distributeSmartContractRoyalties($contractABI, $contractAddress, $fromAddress, $privateKey) {
    $success = true;
    try {
        $web3 = new Web3('https://sepolia.infura.io/v3/' . $this->infuraKey);
        $eth = $web3->eth;
        $contract = new Contract($web3->provider, $contractABI);
        $contract->at($contractAddress);

        // Prepare transaction data for distributeRoyalties function
        $data = $contract->getData('distributeRoyalties');
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', "Attempting to distribute royalties!" . "\n", FILE_APPEND);

        // Get transaction count (nonce) and send the transaction
        $eth->getTransactionCount($fromAddress, function ($err, $nonce) use ($eth, $fromAddress, $data, $privateKey, $contractAddress, &$success) {
            if ($err !== null) {
                $success = false;
                throw new Exception('Error getting nonce: ' . $err->getMessage());
            }

            // Prepare the transaction
            $transaction = [
                'nonce' => '0x' . dechex($nonce->toString()),
                'from' => $fromAddress,
                'to' => $contractAddress,
                'data' => '0x' . $data,
                'gas' => '0x' . dechex(8000000), // Adjust as needed
                'gasPrice' => '0x' . dechex($this->getRapidGasPrice()), // Adjust as needed
                'chainId' => 11155111, // chain ID here
            ];

            // Sign and send the transaction
            $tx = new Transaction($transaction);
            $signedTransaction = $tx->sign($privateKey);
            $eth->sendRawTransaction('0x' . $signedTransaction, function ($err, $txHash) use (&$success) {
                if ($err !== null) {
                    file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Error: ' . $err->getMessage() . "\n", FILE_APPEND);
                    $success = false;
                    throw new Exception('Error sending transaction: ' . $err->getMessage());
                }
                file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Royalties distributed successfully. Transaction hash: ' . $txHash . "\n", FILE_APPEND);
            });
        });

    } catch (Exception $e) {
        $success = false;
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Error: ' . $e->getMessage() . "\n", FILE_APPEND);
    }
    return $success;
}


  public function mintSkill($contractAddress, $contractABI, $fromAddress, $privateKey, $id, $developerWallets, $allocations, $dependentSkills, $dependencyAllocations) {
    $success = true;
    try {
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. 'Attempting to mint skill with identifier: '. $id. PHP_EOL . PHP_EOL, FILE_APPEND);
        $web3 = new Web3('https://sepolia.infura.io/v3/' . $this->infuraKey);
        $eth = $web3->eth;

        $contract = new Contract($web3->provider, $contractABI);


        $contract->at($contractAddress);
        


        $data = $contract->getData('mintSkill',(int) $id, $developerWallets, $allocations, $dependentSkills, $dependencyAllocations);

        
        
        

        $eth->getTransactionCount($fromAddress, function ($err, $nonce) use ($eth, $fromAddress, $data, $privateKey,$contractAddress, $id, &$success) {
            if ($err !== null) {
                $success = false;
                return;
            }

            // Create the transaction
            $transaction = [
                'nonce' => '0x' . dechex($nonce->toString()),
                'from' => $fromAddress,
                'to' => $contractAddress, // Contract address
                'data' => '0x' .$data,
                'gas' => '0x' . dechex(8000000), // Adjust gas as needed
                'gasPrice' => '0x' . dechex($this->getRapidGasPrice()), // Adjust gas price as needed
                'chainId' => 11155111, // Replace with actual chain ID
            ];

            // Sign the transaction
            $tx = new Transaction($transaction);
            $signedTransaction = $tx->sign($privateKey);

            // Send the transaction
            $eth->sendRawTransaction('0x' . $signedTransaction, function ($err, $txHash) use ($id,&$success) {
                if ($err !== null) {
                    $success = false;
                    return;
                }

                file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. 'Skill ' .$id. ' succesfully minted with tx has: '. $txHash .PHP_EOL . PHP_EOL, FILE_APPEND);
            });
        });
    } catch (Exception $e) {
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', "Exception occured!" , FILE_APPEND);

        $success = false;
    }
    file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', "Got this Minting skill response: " . var_export($success, true) . "\n", FILE_APPEND);

    return $success;
}
public function tryFundSmartContract($contractABI, $contractAddress, $fromAddress, $privateKey, $weiAmount) {
    $maxAttempts = 5; // Maximum number of retry attempts
    $attempt = 0; // Current attempt counter
    $retryDelay = 2; // Delay between attempts in seconds
    $success = false; // Initialize success flag

    while (!$success && $attempt < $maxAttempts) {
        $attempt++; // Increment attempt counter

        $this->logMessage("Attempting to fund smart contract with identifier: $contractAddress, attempt $attempt");

        try {
            // Call the existing fundSmartContract function
            $success = $this->fundSmartContract(
                $contractABI,
                $contractAddress,
                $fromAddress,
                $privateKey,
                $weiAmount
            );

            if ($success === true) {
                $this->logMessage("fundSmartContract successful for Contract Address: $contractAddress on attempt $attempt.");
                return true; // Successful funding, exit loop
            } else {
                $this->logMessage("Got this funding response: " . var_export($success, true));
            }
        } catch (Exception $e) {
            $this->logMessage("fundSmartContract attempt $attempt for Contract Address: $contractAddress threw an exception: " . $e->getMessage());
        }

        if (!$success && $attempt < $maxAttempts) {
            $this->logMessage("Retrying in $retryDelay seconds...");
            sleep($retryDelay); // Wait before retrying
        }
    }

    $this->logMessage("Failed to fundSmartContract for Contract Address: $contractAddress after $maxAttempts attempts.");
    return false; // Return false if all attempts fail
}


public function fundSmartContract($contractABI, $contractAddress, $fromAddress, $privateKey, $weiAmount) {
    $success = true;

    try {
        $web3 = new Web3('https://sepolia.infura.io/v3/' . $this->infuraKey);
       // $web3 = new Web3('https://sepolia.infura.io/v3/' . $this->$infuraKey);
        $eth = $web3->eth;
        $contract = new Contract($web3->provider, $contractABI);
        $contract->at($contractAddress);

        // Prepare transaction data
        $data = $contract->getData('loadContract');
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', "Attempting to load smart contract with funds!" . "\n", FILE_APPEND);
        // Get transaction count (nonce) and send the transaction
        $eth->getTransactionCount($fromAddress, function ($err, $nonce) use ($eth, $fromAddress, $data, $privateKey, $contractAddress, $weiAmount, &$success) {
            if ($err !== null) {
                $success = false;
                throw new Exception('Error getting nonce: ' . $err->getMessage());
            }

            // Prepare the transaction
            $transaction = [
                'nonce' => '0x' . dechex($nonce->toString()),
                'from' => $fromAddress,
                'to' => $contractAddress,
                'value' => '0x' . dechex($weiAmount),
                'data' => '0x' . $data,
                'gas' => '0x' . dechex(8000000), // Adjust as needed
                'gasPrice' => '0x' . dechex($this->getRapidGasPrice()), // Adjust as needed
                'chainId' => 11155111, // chain ID here
            ];

            // Sign and send the transaction
            $tx = new Transaction($transaction);
            $signedTransaction = $tx->sign($privateKey);
            $eth->sendRawTransaction('0x' . $signedTransaction, function ($err, $txHash) use (&$success) {
                if ($err !== null) {
                    $success = false;
                    file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Error: ' . $err->getMessage() . "\n", FILE_APPEND);
                    throw new Exception('Error sending transaction: ' . $err->getMessage());
                }
                file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Contract funded successfully. Transaction hash: ' . $txHash . "\n", FILE_APPEND);
            //    $response['success'] = true;
           //     $response['message'] = 'Transaction sent successfully. Transaction hash: ' . $txHash;
            });
        });

    } catch (Exception $e) {
        $success = false;
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Error: ' . $e->getMessage() . "\n", FILE_APPEND);
    }

    return $success;
}


public function donateSkill($contractAddress, $contractABI, $fromAddress, $privateKey, $id, $donationAmount) {
    $success = true;
    try {
        file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. 'Attempting to donate to skill with identifier: '. $id . ' and amount: ' . $donationAmount . PHP_EOL . PHP_EOL, FILE_APPEND);
        $web3 = new Web3('https://sepolia.infura.io/v3/' . $this->infuraKey);
        $eth = $web3->eth;

        $contract = new Contract($web3->provider, $contractABI);
        $contract->at($contractAddress);

        // Note: Assuming $id is a string and $donationAmount is already in the correct format (uint256)
        $data = $contract->getData('donateSkill', (int) $id, $donationAmount);
        //file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. 'Data:'. $data. PHP_EOL . PHP_EOL, FILE_APPEND);
        
        $eth->getTransactionCount($fromAddress, function ($err, $nonce) use ($eth, $fromAddress, $data, $privateKey, $contractAddress, $id, &$success) {
            if ($err !== null) {
                $success = false;
                return;
            }

            // Create the transaction
            $transaction = [
                'nonce' => '0x' . dechex($nonce->toString()),
                'from' => $fromAddress,
                'to' => $contractAddress,
                'data' => '0x' . $data,
                'gas' => '0x' . dechex(8000000),
                'gasPrice' => '0x' . dechex($this->getRapidGasPrice()),
                'chainId' => 11155111, // Adjust as needed
            ];

            // Sign the transaction
            $tx = new Transaction($transaction);
            $signedTransaction = $tx->sign($privateKey);

            // Send the transaction
            $eth->sendRawTransaction('0x' . $signedTransaction, function ($err, $txHash) use ($id,&$success) {
                if ($err !== null) {
                    $success = false;
                    file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. 'Donation for skill failed ' . $err. PHP_EOL . PHP_EOL, FILE_APPEND);
                    return;
                }

                file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', PHP_EOL . '[' . date('Y-m-d H:i:s') . '] '. 'Donation for skill ' . $id . ' successfully made with tx hash: '. $txHash . PHP_EOL . PHP_EOL, FILE_APPEND);
            });
        });
    } catch (Exception $e) {
        $success = false;
    }
    file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', "Got this Donation skill response: " . var_export($success, true) . "\n", FILE_APPEND);

    return $success;
}
    

}
