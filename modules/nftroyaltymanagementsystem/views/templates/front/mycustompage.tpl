{extends file='page.tpl'}
{block name='page_content'}
<div id="custom-content" class="custom-content">
    <h1>Welcome to the Web3 Dashboard</h1>
    <p>Smart Contract Address: {$smartContractAddress}</p>
</div>

{if $smartContractAddress neq 'NotSet'}
    <div id="container" class="container">
    <h2>Developer information</h2>
<button id="connect">Connect to MetaMask</button><br></br>
<h3>Wallet address</h3>
<div id="wallet_address">No wallet connected</div><br></br>
<h3>Royalties</h3>
<div id="royaltiesDiv">Royalties debt... Loading</div>
<br></br>
<h3>Your skills</h3>
<div id="skillsDiv">Loading skills.. Loading</div>


<br></br>
<div id="allocateRoyaltyDiv">
    <h2>Allocate Royalty</h2>

    <label for="allocateId">Skill ID:</label>
    <input type="number" id="allocateId" placeholder="Enter Skill ID">
    
    <label for="allocateAmount">Allocation Amount:</label>
    <input type="number" id="allocateAmount" placeholder="Enter Allocation Amount">
    
    <label for="allocateAuthor">Author Address:</label>
    <input type="text" id="allocateAuthor" placeholder="Enter Author Address">

    <button id="allocateButton" onclick="allocateRoyaltyClick()">Allocate</button>
    <br><br> <!-- Use <br><br> instead of <br></br> for line breaks in HTML -->

    <div id="txhash"></div>
</div>



{else}
    <!-- If smartContractAddress is empty, show this div -->
    <div>
        This Dashboard does not show content if the Web3 Royalty System has not been bootstrapped by the adminstrator!
    </div>
{/if}



{literal}
<script type="text/javascript">
   
    let contractAddress = "{/literal}{$smartContractAddress}{literal}";
    console.log("This is the contract address",contractAddress)
 //let contractABI = []; // You will fill this array with your contract's ABI

    let contractABI;
    fetch(`https://api-sepolia.etherscan.io/api?module=contract&action=getabi&address=${contractAddress}&apikey=WD2EHXDMAAGN37R5DKYT6A6FF6163227EY`)
    .then(response => response.json())
    .then(data => {
        contractABI = JSON.parse(data.result);
        
        // Do something with the ABI
    });
</script>
{/literal}


<script src="https://cdn.jsdelivr.net/npm/web3@1.3.0/dist/web3.min.js"></script>

<script type="text/javascript">
    if (typeof window.ethereum !== 'undefined') {
        console.log('MetaMask is installed!');
    } else {
        console.log("MetaMask is not installed. Please consider installing it: https://metamask.io/download.html");
    }

    let web3;
    let contract;
   

    // This function initializes the web3 object and contract instance
    async function initWeb3() {
        web3 = new Web3(window.ethereum);
        try {
            await window.ethereum.request({ method: 'eth_requestAccounts' });
            contract = new web3.eth.Contract(contractABI, contractAddress);
            console.log('Contract initialized successfully');
        } catch (error) {
            console.error('User denied account access or an error occurred:', error);
        }
    }

    // Function to handle account change
    function handleAccountsChanged(accounts) {
        
        const walletAddressDiv = document.getElementById('wallet_address');
        
        if (accounts.length === 0) {
            console.log('Please connect to MetaMask.');
            walletAddressDiv.textContent = 'No wallet connected';
        } else {
            console.log('Connected account:', accounts[0]);
            walletAddressDiv.textContent = accounts[0];
            // Fetch the total royalties debt, for example
            fetchRoyaltiesDebt();
            fetchAuthorSkillsClick()
            document.getElementById('connect').style.display = 'none'; // To hide

        }
    }
   
async function fetchAuthorSkillsClick() {
     const accounts = await web3.eth.getAccounts();
      if (accounts.length === 0) {
            console.log('Please connect to MetaMask.');
        } else {
         
            fetchAuthorSkills(accounts[0]);
            
        }
    
}
</script>

{literal}
<script>
async function fetchAuthorSkills(authorAddress) {
    const skillsDiv = document.getElementById('skillsDiv');

    try {
        const result = await contract.methods.getAuthorSkills(authorAddress).call();
        console.log(result); // This will log the result so you can inspect it in the console

        // Now, let's also check if the arrays are not empty
        let hasSkills = result && Object.values(result).some(array => Array.isArray(array) && array.length > 0);

        if (hasSkills) {
            let skillsList = '<ul>';

            Object.entries(result).forEach(([key, value]) => {
                if (value.length > 0) { // Check if the array is not empty
                    let label = key === "0" ? "Skill ID" : "Allocation";
                    let attributeValue = value.join(', ');
                    skillsList += `<li>${label}: ${attributeValue}</li>`;
                }
            });

            skillsList += '</ul>';
            skillsDiv.innerHTML = "Skills for author: " + skillsList;
        } else {
            // If the arrays are empty, show the error message
            //console.log("No skills found or arrays are empty");
            skillsDiv.innerHTML = "You do not have any skills minted yet.";
        }
    } catch (error) {
        // Log the error to the console for debugging
        console.error("Failed to fetch skills: ", error);
        skillsDiv.innerHTML = "Error: Failed to fetch skills."; // Display this message when an error occurs
    }
}






</script>
{/literal}



<script>
async function allocateRoyaltyClick() {
    const skillId = document.getElementById('allocateId').value;
    const allocationAmount = document.getElementById('allocateAmount').value;
    const authorAddress = document.getElementById('allocateAuthor').value;

    // Basic validation
    if (!skillId || !allocationAmount || !authorAddress) {
        alert('Please fill in all fields correctly.');
        return;
    }

    try {
        document.getElementById("txhash").innerText = "Transanction pending..."

        // Retrieve the current user's first account
        const accounts = await web3.eth.getAccounts();
        const fromAddress = accounts[0]; // Use the first account as the transaction sender

        if (!fromAddress) {
            throw new Error('No accounts found. Please check if your wallet is connected.');
        }

        // Assuming you have a web3 instance and contract set up
        const result = await contract.methods.allocateRoyalty(skillId, allocationAmount, authorAddress).send({ from: fromAddress });

        console.log('Allocation successful:', result);
        document.getElementById("txhash").innerText = "Tx hash: " + result.transactionHash;

      
    } catch (error) {
        console.error('Allocation failed:', error);
        alert('Royalty allocation failed. See console for details.');
    }
}


async function allocateRoyalty(id, newAllocation, newAuthor) {
    const royaltiesDiv = document.getElementById('royaltiesDiv');
    try {
        // Fetching the connected accounts
        const accounts = await web3.eth.getAccounts();

        // Make sure accounts array is not empty
        if (accounts.length === 0) {
            console.error('No connected accounts found');
            royaltiesDiv.innerText = "Please connect to MetaMask.";
            return;
        }

        await contract.methods.allocateRoyalty(id, newAllocation, newAuthor).send({ from: accounts[0] });
        console.log('Transaction sent from account:', accounts[0]);
        royaltiesDiv.innerText = "Royalty allocation successful.";
    } catch (error) {
        console.error(error);
        royaltiesDiv.innerText = "Error in royalty allocation: " + error.message;
    }
}


 



    // Function to fetch royalties debt
   async function fetchRoyaltiesDebt() {
     const accounts = await web3.eth.getAccounts();
      if (accounts.length === 0) {
            console.error('No connected accounts found');
            royaltiesDiv.innerText = "Please connect to MetaMask.";
            return;
        }
    const royaltiesDiv = document.getElementById('royaltiesDiv');
    try {
        const resultWei = await contract.methods.checkBalance(accounts[0]).call();
        // Assuming web3 is initialized and available
        const resultEther = web3.utils.fromWei(resultWei, 'ether');
        royaltiesDiv.innerText = "Total Debt: " + resultEther + " ETH";
    } catch (error) {
        console.error(error);
        royaltiesDiv.innerText = "Not a registered developer!";
    }
}


    // Event listener for the "Connect to MetaMask" button
    document.getElementById('connect').addEventListener('click', function() {
        initWeb3().then(() => {
            web3.eth.getAccounts().then(handleAccountsChanged);
        });
    });

    // Listen for account changes
    window.ethereum.on('accountsChanged', handleAccountsChanged);
</script>






<style>
/* Styling for the main container and sections by id */
#custom-content, #container{
    max-width: 100%;
    width: 100%
    margin: 20px auto; /* This centers the container horizontally */
    padding: 20px;

    
    /* Add these lines to center content inside the container */
    display: flex-box;
    flex-direction: column; /* Stack children vertically */
    justify-content: center; /* Center children vertically */
    align-items: center; /* Center children horizontally */
    text-align: center; /* Center text for each child if needed */
}

/* Specific headings within identified sections */
#custom-content h1, #container h2, #container h3 {
    color: #333;
}
label {
    display: inline-block; /* Aligns the label side by side with the input */
    margin-right: 10px; /* Adds some spacing between the label and the input */
    vertical-align: middle; /* Centers the label vertically with the input */
    width: auto; /* Sets the label width to be only as wide as the text */
}

/* Input styling */
#allocateId, #allocateAmount {
    width: calc(100% - 24px - 10px); /* Adjusts the input width, accounting for label width and margin */
    padding: 12px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box; /* Ensures padding doesn't increase width */
    font-size: 16px;
}
/* Button styling by id */
#connect {
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

#connect:hover {
    background-color: #45a049;
}

/* Input styling by id */
#allocateId, #allocateAmount {
    width: 50%;
    padding: 12px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 16px;
}

#allocateAuthor {
    width: 90%;
    padding: 12px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 16px;
}

/* Responsive design targeting specific ids */
@media screen and (max-width: 768px) {
    #custom-content, #container, #allocateRoyaltyDiv {
        margin: 10px;
        padding: 10px;
    }

}
#allocateRoyaltyDiv {
    display: flex;
    flex-direction: column; /* Stack the children vertically */
   
    align-items: center; /* Center the children horizontally */
    text-align: center; /* Center the text within each child element, if needed */
}

 #allocateButton {
        background-color: #4CAF50; /* Green */
        border: none;
        color: white;
        padding: 12px 24px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin-top: 8px;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    #allocateButton:hover {
        background-color: #45a049;
    }

    /* Responsive design for the button */
    @media screen and (max-width: 768px) {
        #allocateButton {
            width: 100%;
            padding: 12px;
            box-sizing: border-box; /* Ensures padding doesn't increase width */
            font-size: 18px; /* Optionally, increase font size for better mobile readability */
        }
    }
    body, input, button, select, textarea {
    font-family: 'Open Sans', sans-serif; /* Fallback to sans-serif if 'Open Sans' is not available */
}
</style>


{/block}


