<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once __DIR__ . '/../../nftroyaltymanagementsystem.php';



class nftroyaltymanagementsystemajaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
        $action = Tools::getValue('action');
        if ($action == 'storeSeedPhrase') {
            $this->ajaxProcessStoreSeedPhrase();
        } elseif ($action == 'storeSmartContractAddress') {
            $this->ajaxProcessSmartContractAddress();
        }
        elseif ($action == 'deploySmartContract') {
            $this->ajaxProcessDeploySmartContract();
        }
        elseif ($action == 'storeWalletAddress') {
            $this->ajaxProcessStoreWalletAddress();
        }
        elseif ($action == 'fundSmartContract') {
            $this->ajaxProcessFundSmartContract();
        }
        elseif ($action == 'distributeRoyalties') {
            $this->ajaxProcessDistributeRoyalties();
        }
        // Add more elseif blocks for other actions as needed
    }

    public function ajaxProcessDistributeRoyalties() {
        $response = array('success' => false, 'message' => '');
    
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request');
            }
    
            // Retrieve contract ABI
            $jsonFilePath = __DIR__ . '/../../contractABI.json'; // Replace with actual file path
            $contractABIJson = file_get_contents($jsonFilePath);
            $contractABI = json_decode($contractABIJson, true);
    
            // Database connection
            $db = Db::getInstance();
    
            // Retrieve private key
            $sqlPrivateKey = "SELECT private_key FROM " . _DB_PREFIX_ . "wallet_private_key WHERE id = 1";
            $resultPrivateKey = $db->executeS($sqlPrivateKey);
            $privateKey = $resultPrivateKey && count($resultPrivateKey) > 0 ? $resultPrivateKey[0]['private_key'] : null;
    
            // Retrieve public address
            $sqlPublicAddress = "SELECT public_address FROM " . _DB_PREFIX_ . "wallet_public_address WHERE id = 1";
            $resultPublicAddress = $db->executeS($sqlPublicAddress);
            $fromAddress = $resultPublicAddress && count($resultPublicAddress) > 0 ? $resultPublicAddress[0]['public_address'] : null;
    
            // Retrieve contract address
            $sqlContractAddress = "SELECT contract_address FROM " . _DB_PREFIX_ . "smart_contract_address WHERE id = 1";
            $resultContractAddress = $db->executeS($sqlContractAddress);
            $contractAddress = $resultContractAddress && count($resultContractAddress) > 0 ? $resultContractAddress[0]['contract_address'] : null;
    
            // Call the distributeSmartContractRoyalties function from your NftRoyaltymanagementsystem module
            $nftModule = new NftRoyaltymanagementsystem();
            $distributionResponse = $nftModule->distributeSmartContractRoyalties($contractABI, $contractAddress, $fromAddress, $privateKey);
    
            // If no exception was thrown, assume success
            if ($distributionResponse) {
                // If $deployResponse is true
                $response['success'] = true;
                $response['message'] = 'Royalties distribution process initiated successfully.';
            } else {
                // If $deployResponse is false or null
                $response['success'] = false;
                $response['message'] = "Distribution failed. Check the logs"; // Or any other failure message
            }

            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', "Got this Distribution response: " . var_export($distributionResponse, true) . "\n", FILE_APPEND);

           
           
            sleep(45);
            $nftModule->getTotalRoyalties($contractAddress, $contractABI);
    
        } catch (Exception $e) {
            // If an exception was caught, set success to false and add the error message
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', $e->getMessage() . "\n", FILE_APPEND);
        }
    
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    

    public function ajaxProcessStoreSeedPhrase()    {
        
        $response = array('success' => false, 'message' => '');
    
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['seed_phrase'])) {
                throw new Exception('Invalid request');
            }
    
            $seedPhrase = pSQL($_POST['seed_phrase']);
            $db = Db::getInstance();
    
            $insertSuccess = $db->update(
                'wallet_private_key',
                array('private_key' => $seedPhrase),
                'id = 1'
            );
            
            if (!$insertSuccess) {
                throw new Exception('Database insertion failed');
            }
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Succesfully saved the admin wallet private key.' . "\n", FILE_APPEND);
    
            $response['success'] = true;
            $response['message'] = 'Data saved successfully';
    
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            // Log the error message
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', $e->getMessage() . "\n", FILE_APPEND);
        }
    
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function ajaxProcessStoreWalletAddress()    {
        
        $response = array('success' => false, 'message' => '');
    
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['seed_phrase'])) {
                throw new Exception('Invalid request');
            }
    
            $seedPhrase = pSQL($_POST['seed_phrase']);
            $db = Db::getInstance();
    
            $insertSuccess = $db->update(
                'wallet_public_address',
                array('public_address' => $seedPhrase),
                'id = 1'
            );
            
            if (!$insertSuccess) {
                throw new Exception('Database insertion failed');
            }
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', 'Succesfully saved the admin wallet public address.' . "\n", FILE_APPEND);
    
            $response['success'] = true;
            $response['message'] = 'Data saved successfully';
    
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            // Log the error message
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', $e->getMessage() . "\n", FILE_APPEND);
        }
    
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function ajaxProcessFundSmartContract() {
        $response = array('success' => false, 'message' => '');
    
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request');
            }
      
            // Retrieve contract ABI
            $jsonFilePath = __DIR__ . '/../../contractABI.json'; // Replace with actual file path
            $contractABIJson = file_get_contents($jsonFilePath);
            $contractABI = json_decode($contractABIJson, true);
    
            // Database connection
            $db = Db::getInstance();
    
            // Retrieve private key
            $sqlPrivateKey = "SELECT private_key FROM " . _DB_PREFIX_ . "wallet_private_key WHERE id = 1";
            $resultPrivateKey = $db->executeS($sqlPrivateKey);
            $privateKey = $resultPrivateKey && count($resultPrivateKey) > 0 ? $resultPrivateKey[0]['private_key'] : null;
    
            // Retrieve public address
            $sqlPublicAddress = "SELECT public_address FROM " . _DB_PREFIX_ . "wallet_public_address WHERE id = 1";
            $resultPublicAddress = $db->executeS($sqlPublicAddress);
            $fromAddress = $resultPublicAddress && count($resultPublicAddress) > 0 ? $resultPublicAddress[0]['public_address'] : null;
    
            // Retrieve contract address
            $sqlContractAddress = "SELECT contract_address FROM " . _DB_PREFIX_ . "smart_contract_address WHERE id = 1";
            $resultContractAddress = $db->executeS($sqlContractAddress);
            $contractAddress = $resultContractAddress && count($resultContractAddress) > 0 ? $resultContractAddress[0]['contract_address'] : null;
    
            // Retrieve amount of Wei to send
            $sqlRoyalties = "SELECT royalties FROM " . _DB_PREFIX_ . "royalties_owed WHERE id = 1";
            $resultRoyalties = $db->executeS($sqlRoyalties);
            $weiAmount = $resultRoyalties && count($resultRoyalties) > 0 ? $resultRoyalties[0]['royalties'] : null;
    
            
        // Call the fundSmartContract function from your NftRoyaltymanagementsystem module
        $nftModule = new NftRoyaltymanagementsystem();
        $fundResponse = $nftModule->fundSmartContract($contractABI, $contractAddress, $fromAddress, $privateKey, $weiAmount);

      // If no exception was thrown, assume success
      if ($fundResponse) {
        // If $deployResponse is true
        $response['success'] = true;
        $response['message'] = "Funding successful."; // Or any other success message
    } else {
        // If $deployResponse is false or null
        $response['success'] = false;
        $response['message'] = "Funding failed. Check the logs"; // Or any other failure message
    }

    file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', "Got this Funding response: " . var_export($fundResponse, true) . "\n", FILE_APPEND);


        } catch (Exception $e) {
            // If an exception was caught, set success to false and add the error message
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', $e->getMessage() . "\n", FILE_APPEND);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
}

    public function ajaxProcessDeploySmartContract() {
        
        $response = array('success' => false, 'message' => '');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request');
            }

            // You might need to get these values from the request or define them here
            $jsonFilePath = __DIR__ . '/../../contractABI.json'; // Replace with actual file path
            $contractABIJson = file_get_contents($jsonFilePath);
            $contractABI = json_decode($contractABIJson, true);
            $bytecodeFilePath = __DIR__ . '/../../contractBytecode.txt'; 
            $contractBytecode = file_get_contents($bytecodeFilePath);
            $privateKey = null;
            $fromAddress = null;
            $db = Db::getInstance();
            $sqlPrivateKey = "SELECT private_key FROM " . _DB_PREFIX_ . "wallet_private_key WHERE id = 1";
            $resultPrivateKey = $db->executeS($sqlPrivateKey);
            if ($resultPrivateKey && count($resultPrivateKey) > 0) {
                $privateKey = $resultPrivateKey[0]['private_key'];
            } else {
                // Handle the case where no result is found
                $privateKey = null;
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

            // Call the deploySmartContract function
          

            $nftModule = new NftRoyaltymanagementsystem();
            $deployResponse = $nftModule->deploySmartContract($contractABI, $contractBytecode, $fromAddress, $privateKey);

           // $deployResponse = deploySmartContract($contractABI, $contractBytecode, $fromAddress, $privateKey);

           if ($deployResponse) {
            // If $deployResponse is true
            $response['success'] = true;
            $response['message'] = "Deployment successful."; // Or any other success message
        } else {
            // If $deployResponse is false or null
            $response['success'] = false;
            $response['message'] = "Deployment failed. Check the logs"; // Or any other failure message
        }
        
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', "Got this Deployment response: " . var_export($deployResponse, true) . "\n", FILE_APPEND);


        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', $e->getMessage() . "\n", FILE_APPEND);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    
    public function ajaxProcessSmartContractAddress()  {
    $response = array('success' => false, 'message' => '');
    
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['smart_contract_address'])) {
                throw new Exception('Invalid request');
            }
    
            $contractAddress = pSQL($_POST['smart_contract_address']);
            $db = Db::getInstance();
    
            $insertSuccess = $db->update(
                'smart_contract_address',
                array('value' => $contractAddress),
                'id = 1'
            );
            
            if (!$insertSuccess) {
                throw new Exception('Database insertion failed');
            }
    
            $response['success'] = true;
            $response['message'] = 'Data saved successfully';
    
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            // Log the error message
            file_put_contents(_PS_ROOT_DIR_ . '/RoyaltySystemActionLog.txt', $e->getMessage() . "\n", FILE_APPEND);
        }
    
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
}

}
