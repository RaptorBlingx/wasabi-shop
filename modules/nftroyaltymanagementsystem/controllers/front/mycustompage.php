<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class NftRoyaltyManagementSystemMycustompageModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        // Retrieve the smart contract address using the provided SQL
        $contractaddress = $this->getContractAddress();

        // Assign the variable to Smarty
        $this->context->smarty->assign('smartContractAddress', $contractaddress);

        $this->setTemplate('module:nftroyaltymanagementsystem/views/templates/front/mycustompage.tpl');
    }

    public function getContractAddress() {
        $db = Db::getInstance();
        $sqlContractAddress = "SELECT contract_address FROM " . _DB_PREFIX_ . "smart_contract_address WHERE id = 1";
        $resultContractAddress = $db->executeS($sqlContractAddress);
        if ($resultContractAddress && count($resultContractAddress) > 0) {
            return $resultContractAddress[0]['contract_address'];
        } else {
            // Handle the case where no result is found
            return null;
        }
    }
}
