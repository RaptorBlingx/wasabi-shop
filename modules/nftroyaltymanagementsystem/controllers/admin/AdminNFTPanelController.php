<?php
class AdminNFTPanelController extends ModuleAdminController
{
    // Constructor method: Initializes the admin panel controller
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        // Set the template file for the admin panel
        $this->setTemplate('AdminPanel.tpl');
    }

    // Initialize the content for the admin panel
    public function initContent()
    {
        parent::initContent();
        
        // Append a new line to log.txt in the root directory
        file_put_contents(_PS_ROOT_DIR_ . '/log.txt', "s\n", FILE_APPEND);
        // Generate a security token for the admin panel
        $token = Tools::getAdminTokenLite('AdminNFTPanel');
        // Assign the token to the Smarty template
        $this->context->smarty->assign('token', $token);

        // Connect to the database
        $db = Db::getInstance();
            
        // Retrieve all entries from the smart_contract_address table
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "smart_contract_address";
        $smart_contract_address = $db->executeS($sql);

        // Retrieve all entries from the wallet_private_key table
        $sql2 = "SELECT * FROM " . _DB_PREFIX_ . "wallet_private_key";
        $wallet_private_key = $db->executeS($sql2);

        // Mask the private keys if they are set
        foreach ($wallet_private_key as &$key) {
            if ($key['private_key'] != "NotSet") {
                $key['private_key'] = "Private key has been provided. ✅";
            }
        }
        unset($key); // End of the reference loop

        // Retrieve all entries from the wallet_public_address table
        $sql3 = "SELECT * FROM " . _DB_PREFIX_ . "wallet_public_address";
        $wallet_public_address = $db->executeS($sql3);

        // Retrieve all entries from the royalties_owed table
        $sql4 = "SELECT * FROM " . _DB_PREFIX_ . "royalties_owed";
        $royalties_owed = $db->executeS($sql4);

        // Process and format royalties data
        foreach ($royalties_owed as &$row) {
            // Handle 'NotSet' values in royalties
            if ($row['royalties'] == 'NotSet' && $wallet_private_key[0]['private_key'] == "NotSet") {
                $row['royalties'] = 'Null';
            }
            elseif ($row['royalties'] == 'NotSet') {
                $row['royalties'] = '0';
            }
            else {
                // Convert royalties from Wei to Ether and format the number
                $ethValue = bcdiv($row['royalties'], bcpow("10", "18"), 18);
                $row['royalties'] = rtrim(rtrim($ethValue, '0'), '.') ?: '0';
            }
        }
        unset($row); // End of the reference loop
        
        // Assign fetched data to Smarty for rendering in the template
        $this->context->smarty->assign(array(
            'smart_contract_address' => $smart_contract_address,
            'wallet_private_key' => $wallet_private_key,
            'royalties_owed' => $royalties_owed,
            'wallet_public_address' => $wallet_public_address
        ));

        // Set the template file for rendering
        $this->setTemplate('AdminPanel.tpl');
    }
}
