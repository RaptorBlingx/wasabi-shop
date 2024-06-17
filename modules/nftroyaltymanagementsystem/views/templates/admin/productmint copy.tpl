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
    <div class="container">
        <h1>System initialization</h1>
        
<form id="data-form" class="form-container">
            <div class="form-group">
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
            
            <div class="form-group">
                <button type="submit" id="submit-button" class="submit-button">Submit Data</button>
            </div>
            <div id="confirmation-message" class="confirmation-message"></div>
        </form>
    </div>


</body>
</html>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
{literal}
$(document).ready(function() {
    $('#data-form').on('submit', function(event) {
      
        // ... [existing code]

        var url = '{/literal}{$link->getModuleLink('nftroyaltymanagementsystem', 'ajax', array('action' => 'mintNFTSkill'))}{literal}';
        console.log("AJAX URL:", url);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                developerWallets: developerWallets,
                allocations: allocations,
                dependentSkills: dependentSkills,
                dependencyAllocations: dependencyAllocations
            },
            success: function(response) {
                console.log("Success Response:", response);
                document.getElementById("confirmation-message").innerHTML = "Data submitted successfully. " + response.message;
            },
            error: function(xhr, status, error) {
                console.error("Error Response:", xhr, status, error);
                document.getElementById("confirmation-message").innerHTML = "An error occurred: " + error;
            }
        });
    });
});
{/literal}
</script>
