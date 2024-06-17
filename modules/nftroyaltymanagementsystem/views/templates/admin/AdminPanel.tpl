<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Web3 Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f0f0f0;


 
        margin: 0;
    }
    #confirmation-message-fund,
    #confirmation-message-distribute,
    #confirmation-message-deploy,
    #confirmation-message,#confirmation-message2 {
        color: #2C3E50;  /* A modern, desaturated blue color */
        font-weight: 700;  /* Making the font bold */
        font-size: large;
    }

    #royalties-sum {
        color: #D4AC0D;  /* A modern gold color */
        font-weight: 700;  /* Making the font bold */
        font-size: large;
    }

    #submit-button,#seed-phrase,#seed-phrase2,#deploy-contract, #store-public-address,#store-private-key,#distribute-royalties-button, #fund-contract-button {
        padding: 10px;
        margin: 5px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
        outline: none;
        font-size: 16px;
    }

    #seed-phrase, #seed-phrase2 {
        width: 100%;
        max-width: 600px;
    }

    #submit-button,#store-public-address, #store-private-key,#deploy-contract,#distribute-royalties-button, #fund-contract-button {
        background-color: #008CBA; /* Blue */
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    /* Resetting some default browser styles */
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    }

    /* Styling the input field */
    .input-field {
        width: 100%; /* Taking full width of its container */
        padding: 10px; /* Adding some padding */
        margin-bottom: 10px; /* Adding some space below */
        border: 1px solid #ccc; /* Adding a border */
        border-radius: 4px; /* Rounding the corners */
        font-size: 16px; /* Setting a readable font size */
    }

    /* Styling the button */
    .submit-button, {
        background-color: #008CBA; /* A pleasant blue */
        color: white; /* White text */
        padding: 10px 20px; /* Some padding */
        border: none; /* Removing the border */
        border-radius: 4px; /* Rounding the corners */
        cursor: pointer; /* Pointer cursor on hover */
        font-size: 16px; /* Matching the font size */
    }

    /* Adding a hover effect to the button */
    .submit-button:hover {
        background-color: #005f5f; /* Darkening the button color on hover */
    }


    #submit-button:hover,#store-public-address:hover,#store-private-key:hover,#deploy-contract:hover,#distribute-royalties-button:hover, #fund-contract-button:hover {
        background-color: #005f5f;
    }
    

    .line-spacing {
        line-height: 22; /* Adjust as needed */
    }
   
    .section {
    background-color: #ffffff;
    border-radius: 10px; /* Slightly increased for a more modern look */
    padding: 20px; /* Increased padding for better spacing */
    margin-bottom: 10px; /* Consistent spacing with padding */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12); /* Enhanced shadow for depth */
    transition: box-shadow 0.3s ease; /* Smooth transition for hover effect */
}

.section:hover {
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.18); /* Slightly larger shadow on hover for interactive effect */
}

.section h1 {
    margin: 0 0 15px 0; /* Adjusted bottom margin for better visual rhythm */
    font-size: 22px; /* Increased font size for emphasis */
    color: #222; /* Slightly darker for better readability */
    font-weight: 600; /* Increased weight for importance */
    line-height: 1.2; /* Improved line height for readability */
}

.section h2 {
    margin: 10px 0 5px; /* Added top margin for spacing, adjusted bottom */
    font-size: 16px; /* Adjusted font size for hierarchy */
    color: #444; /* Adjusted color for subtle contrast */
    font-weight: 500; /* Less weight than h1 for hierarchy */
    line-height: 1.3; /* Adjusted line height for readability */
    word-break: break-word; /* Retained for long unbroken strings */
}


    .spacing {
        height: 20px;
    }
    #center-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
        gap: 10px; /* Provides space between the elements */
    }

    .action-block {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    #bootstrap,#monitoring {
    font-family: 'Arial', sans-serif; /* Using Arial font, but you can choose any other font */
    font-size: 36px; /* Adjust the size as needed */
    color: #333333; /* A dark grey color for the text */
    text-align: center; /* Center aligns the text */
    margin-top: 20px; /* Adds space above the header */
    margin-bottom: 20px; /* Adds space below the header */
    padding: 10px 0; /* Adds padding above and below the text */
    border-bottom: 2px solid #333333; /* Adds a bottom border */
    width: 70%; /* Sets the width of the header */
    margin-left: auto; /* Centers the header block horizontally */
    margin-right: auto;
}

</style>
</head>
<body>

<h1 id="bootstrap"> System initialization</h1>
<div id="center-container">
<input type="text" id="seed-phrase" class="input-field" placeholder="Enter your wallet's public address">
<button id="store-public-address" class="submit-button">Store wallet address</button>
<div id="confirmation-message2" style="color:green;"></div><br></br></div>

<div id="center-container">
<input type="text" id="seed-phrase2" class="input-field" placeholder="Enter your wallet's private key">
<button id="store-private-key" class="submit-button">Store private key</button>
<div id="confirmation-message" style="color:green;"></div></div>



<br></br>
<div id="center-container">
{foreach from=$wallet_public_address item=row}
    <div class="section">
        <h1>Admin wallet public address</h1>
        <h2>{$row.public_address}</h2>
    </div>
{/foreach}

<div id="center-container">
{foreach from=$wallet_private_key item=row}
<div class="section">
        <h1>Admin wallet private key</h1>
        <h2>{$row.private_key}</h2>
    </div></div>{/foreach}
<div class="spacing"></div></div>
<div id="center-container">
{foreach from=$smart_contract_address item=row}
    <div class="section">
        <h1>Smart Contract address</h1>
        <h2>{$row.contract_address}</h2>
    </div>
{/foreach} </div>
<br></br>

<div id="center-container">
<button id="deploy-contract">Deploy Smart Contract</button>
<div id="confirmation-message-deploy" style="color:green;"></div>
<br></br></div>


<h1 id="bootstrap"> System monitoring</h1>


<div id="center-container">
<div class="action-block">
{foreach from=$royalties_owed item=row}
    <div class="section">
        <div id="royalties-sum" style="color:rgb(211, 182, 18);">Royalties owed: {$row.royalties} ether</div>
    </div>
{/foreach}
</div>


</div>
    <div class="action-block">
        <button id="fund-contract-button">Fund Smart Contract</button>
        <div id="confirmation-message-fund" style="color:green;"></div>
    </div>

    <div class="action-block">
        <button id="distribute-royalties-button">Distribute Royalties</button>
        <div id="confirmation-message-distribute" style="color:green;"></div>
    </div>
</div>



</body>
</html>


<script type="text/javascript">
var seedPhrase = {$wallet_private_key|json_encode}[0].private_key;
var SmartContractAddress1 = {$smart_contract_address|json_encode}[0].contract_address;
var urlPostContractAddress1 = '{$link->getModuleLink('royaltysystem', 'ajax', array('action' => 'storeSmartContractAddress'))}';

console.log(seedPhrase, SmartContractAddress1, urlPostContractAddress1);

$(document).ready(function(){


    // Check the contract_address of seedPhrase
    if (seedPhrase === "NotSet"){
    
        $('#store-private-key').css('background-color', '#008CBA');
        $('#store-public-address').css('background-color', '#008CBA');
         $('#fund-contract-button, #distribute-royalties-button').css('background-color', 'black');  // Red otherwise
            $('#deploy-contract').css('background-color', 'red').prop('disabled', true);
   
        $('#deploy-contract').css('background-color', 'red');  // Blue if seedPhrase is "NotSet"
    } else {
        $('#deploy-contract').css('background-color', '#008CBA');  // Red otherwise
        $('#store-private-key').css('background-color', 'red');
        $('#store-public-address').css('background-color', 'red');
         if (SmartContractAddress1 === "NotSet"){
        $('#fund-contract-button, #distribute-royalties-button').css('background-color', 'red');  // Blue if SmartContractAddress1 is "NotSet"
        $('#deploy-contract').css('background-color', '#008CBA');
    } else {
        $('#fund-contract-button, #distribute-royalties-button').css('background-color', '#008CBA');  // Red otherwise
        $('#deploy-contract').css('background-color', 'red');
    }
    }

    
   
});
</script>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script type="text/javascript">
{literal}
        

    $(document).ready(function() {
         
        $('#distribute-royalties-button').on('click', function() {

            document.getElementById("confirmation-message-distribute").innerHTML = "Distributing royalties..";

            var url = '{/literal}{$link->getModuleLink('nftroyaltymanagementsystem', 'ajax', array('action' => 'distributeRoyalties'))}{literal}';
         //   console.log(url);

            $.ajax({
                url: url,
                type: 'POST',
                success: function(response) {
                    
                document.getElementById("confirmation-message-distribute").innerHTML = response.message;


                      if (response.success) {
                console.log('Royalties distributed:', response);
                   //  document.getElementById("confirmation-message-distribute").innerHTML = "Succesfully distributed!";
                    } else {
                        console.log('Royalties distribution FAILED:', response);
                  //document.getElementById("confirmation-message-distribute").innerHTML = "Failed to distribute: Check the logs and try again";
                    }
                },
                error: function(error) {
                    console.log("problem");
                    console.error('Error:', error);
                    // Handle the error response here
                }
            });
        });
    });
{/literal}
</script>



<script type="text/javascript">
{literal}
        

    $(document).ready(function() {
         
        $('#fund-contract-button').on('click', function() {

            document.getElementById("confirmation-message-fund").innerHTML = "Funding the contract..";

            var url = '{/literal}{$link->getModuleLink('nftroyaltymanagementsystem', 'ajax', array('action' => 'fundSmartContract'))}{literal}';
         //   console.log(url);

            $.ajax({
                url: url,
                type: 'POST',
                success: function(response) {

                document.getElementById("confirmation-message-fund").innerHTML = response.message;
           
                      if (response.success) {
                    console.log('Contract funded:', response);
                    // document.getElementById("confirmation-message-fund").innerHTML = "Succesfully funded!";
                    } else {
                  //        document.getElementById("confirmation-message-fund").innerHTML = "Failed to fund: Check the logs and try again";
                    }
                    // Handle the success response here
                },
                error: function(error) {
                    console.log("problem");
                    console.error('Error:', error);
                    // Handle the error response here
                }
            });
        });
    });
{/literal}
</script>


<script type="text/javascript">
{literal}
        

    $(document).ready(function() {
         
        $('#deploy-contract').on('click', function() {

            document.getElementById("confirmation-message-deploy").innerHTML = "Deploying..";

            var url = '{/literal}{$link->getModuleLink('nftroyaltymanagementsystem', 'ajax', array('action' => 'deploySmartContract'))}{literal}';
            console.log(url);

            $.ajax({
                url: url,
                type: 'POST',
                success: function(response) {

                     document.getElementById("confirmation-message-deploy").innerHTML = response.message;
                   
                    if (response.success) {
                        console.log('Contract Deployed:', response);
                    } else {
                        console.log('Contract Deployment failed:', response);                 
                    }
                },
                error: function(error) {
                    console.log("problem");
                    console.error('Error:', error);
                    document.getElementById("confirmation-message-deploy").innerHTML = "Something went wrong! check the logs and try again.";
                }
            });
        });
    });
{/literal}
</script>

<script type="text/javascript">

{literal}
    $(document).ready(function(){
        $('#store-private-key').on('click', function(){
            var seedPhrase = $('#seed-phrase2').val();
            
            var url = '{/literal}{$link->getModuleLink('nftroyaltymanagementsystem', 'ajax', array('action' => 'storeSeedPhrase'))}{literal}';
                console.log(url);
            $.ajax({
                url: url,
                type: 'POST',
                data: {seed_phrase: seedPhrase},
                success: function(response) {
                    console.log('Data saved:', response);
                    document.getElementById("confirmation-message").innerHTML = "Private key stored successfully!";

                    
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        });
    });
    {/literal}
    
</script>

<script type="text/javascript">

{literal}
    $(document).ready(function(){
        $('#store-public-address').on('click', function(){
            var seedPhrase = $('#seed-phrase').val();
            
            var url = '{/literal}{$link->getModuleLink('nftroyaltymanagementsystem', 'ajax', array('action' => 'storeWalletAddress'))}{literal}';
                console.log(url);
            $.ajax({
                url: url,
                type: 'POST',
                data: {seed_phrase: seedPhrase},
                success: function(response) {
                    console.log('Data saved:', response);
                    document.getElementById("confirmation-message2").innerHTML = "Wallet address stored successfully!";
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        });
    });
    {/literal}
    
</script>
