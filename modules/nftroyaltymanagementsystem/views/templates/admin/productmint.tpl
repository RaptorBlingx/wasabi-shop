<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
        }

        .form-container {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .input-field {
            width: calc(100% - 20px);
            margin-top: 5px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .submit-button {
            width: 100%;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .submit-button:hover {
            background-color: #45a049;
        }

        .confirmation-message {
            margin-top: 20px;
            color: green;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .container {
                width: 90%;
            }
        }
    </style>
</head>
<body>

{if $is_valid}
{if $skillnotexist}
<div class="container">
    <h1>NFT Minting - Skill Wrapping</h1>
    <div id="data-container" class="form-container"> <!-- Changed ID here -->
              <label for="developer-wallets">Developer Wallets:</label>
                <textarea id="developer-wallets" class="input-field" placeholder="Enter developer wallets, separated by commas"></textarea>
            </div>
            
            <div class="form-group">
                <label for="allocations">Allocations (%):</label>
                <input type="text" id="allocations" class="input-field" placeholder="Enter allocations, separated by commas">
            </div>
            
            <div class="form-group">
                <label for="dependent-skills">Dependent Skills:</label>
                <input type="text" id="dependent-skills" class="input-field" placeholder="Enter dependent skills, separated by commas">
            </div>
            
            <div class="form-group">
                <label for="dependency-allocations">Dependency Allocations:</label>
                <input type="text" id="dependency-allocations" class="input-field" placeholder="Enter dependency allocations, separated by commas">
            </div>
            <input type="hidden" name="csrf_token" value="{$csrf_token}">
        <div class="form-group">
<button id="submit-button" class="submit-button" type="button" disabled>Submit Data</button>

        </div>
        <div id="confirmation-message" class="confirmation-message"></div>
    </div>
</div>
{else}
<br></br><br>
    <h1>This skill has already been wrapped on-chain.</h1>
{/if}
{else}
<br></br><br></br><br></br>
    <h1>Web3 system has not been initialized. Please contact the Marketplace Adminstrator.</h1>
{/if}

</body>
</html>


<script type="text/javascript">
    var ajaxUrl = '{$ajaxUrl|escape:'javascript'}';
</script>


<script defer src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    function checkInputs() {
        var allFilled = true;
        $('#developer-wallets, #allocations').each(function() {
            if ($(this).val() === '') {
                allFilled = false;
                return false; // break the loop
            }
        });

        $('#submit-button').prop('disabled', !allFilled);
    }

    // Call checkInputs on input/change event
    $('#developer-wallets, #allocations').on('input change', checkInputs);

    // Rest of your code for the submit button click event
});


console.log("test");
$(document).ready(function() {
    console.log("test");
    $('#submit-button').click(function(event) {
        event.preventDefault();
        console.log("clicked button");
var developerWallets = $('#developer-wallets').val().split(',').map(function(item) { return item.trim(); });
var allocations = $('#allocations').val().split(',').map(function(item) { return parseInt(item.trim(), 10); });
var dependentSkills = $('#dependent-skills').val() ? $('#dependent-skills').val().split(',').map(function(item) { return item.trim(); }) : [];
var dependencyAllocations = $('#dependency-allocations').val() ? $('#dependency-allocations').val().split(',').map(function(item) { return parseInt(item.trim(), 10); }) : [];
var csrfToken = $('input[name="csrf_token"]').val();
 var productId = {$product_id};
// Check for empty strings and NaN in the arrays
developerWallets = developerWallets.filter(function (e) { return e; });
allocations = allocations.filter(function (e) { return !isNaN(e); });
dependentSkills = dependentSkills.filter(function (e) { return e; });
dependencyAllocations = dependencyAllocations.filter(function (e) { return !isNaN(e); });

$.ajax({
    url: ajaxUrl,
    type: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({
        developerWallets: developerWallets,
        allocations: allocations,
        dependentSkills: dependentSkills,
        dependencyAllocations: dependencyAllocations,
        csrf_token: csrfToken,
        product_id_:productId
    }),
    dataType: 'json',

            success: function(response) {
                $('#confirmation-message').text("Data submitted successfully. " + response.message);
            },
            error: function(xhr, status, error) {
                $('#confirmation-message').text("An error occurred: " + error);
            }
        });
    });
});

</script>