<div class="container mb-4">
    <div class="row">
        <span>Federated Dataspace Search</span>
    </div>

    <script>

        
        //! This is the data that has to be supplied by Prestashop (depending on the screen)
        let wls_server_data = {
           
           
            //! A url of the WFM Hub, this is not the connector supplied upon registration but a single URL for the whole application (maybe an environmental variable?)
            hubURL: {$hubURL|json_encode nofilter},

            
            //! The ID of the participant, supplied in registration
            participantID: {$participantDataspaceId|json_encode nofilter},
            
            //! The URL of the participant, supplied in registration
            participantURL: {$dataspaceHubAddress|json_encode nofilter},
            
            
            //!In the purchase screen this should be the ID of the skill
            skillID: "wasabi:CONNECTOR_TEST1:6",  //???????????????
            purchaseToken: "55fd7a87-fffe-4a13-af1e-9f113c46f812",//???????????????
            
         };

         function Add_Skill(sk){
                console.log("Adding ",sk);
         }


        let shown_skills={};
        async function Run_WFM_Query() {

            if (! wls_server_data.hubURL) {
                return alert('Configuration error')
            }

            const tableHeader = `
                <thead>
                    <tr>                    
                        <th  >Participant</th>
                        <th  >ID</th>
                         <th  >Name</th>
                         <th  >Tags</th>
                         <th  ></th>
                    </tr>
                </thead>
            `;

            let query = document.getElementById('wfm-query').value;
            var url = wls_server_data.hubURL + `/Broker/FindSkills?Participant=${ wls_server_data.participantID }&Query=${ query }`;

            let call = await fetch(url, { method: 'GET' } );
            let reply = await call.json();
            shown_skills = Object.fromEntries(reply.map(x=>[x.SkillID,x]));
            let tableBody = `
                <tbody>
                    ${ reply.map(row => `
                        <tr>
                            <td> ${ row.OwnerID } </td>
                            <td> ${ row.SkillID } </td>
                            <td> ${ row.SkillName } </td>
                            <td> ${ row.Tags.join(", ") } </td>
                            <td class="text-right"> <button onclick='Add_Skill(shown_skills[ "${ row.SkillID }" ]);'>Add</button> </td>
                        </tr>
                    `)
                    .join('')
                 }
                </tbody>
            `;

            let table = `<div class="mt-4" style='width:100%; overflow:auto; padding-inline: 5px; border:thin solid rgb(230,230,230);'>
                <table class="table m-0">
                    ${ tableHeader } 
                    ${ tableBody } 
                </table>
                </div>`;
            const tableDiv = document.getElementById('results');
            tableDiv.innerHTML = table;
            console.log("found", reply);


         }


        //FOR THE PURCHASE LINK


        function Open_Connector_Link() {
            window.open(`${ wls_server_data.participantURL }/UI/DATASET/${ wls_server_data.skillID }/${ wls_server_data.purchaseToken }`, '_blank');
         }


    </script>
    
    <div class="row" style="gap: 1rem">
        <input id='wfm-query' class="col-md-6" type="text" placeholder="Query" name="Query">
        <button type="submit" onclick="Run_WFM_Query();">Submit</button>
    </div>
    
    <div class="row" id="results">

    </div>

    <div class="mt-4" style="border: thin dashed gray; padding:50px; margin-inline: -15px;">
        <h5>This is a sample of what the purchased item screen should contain: </h5>
        <div id="TOKEN"> </div>
        <button type="submit" onclick="Open_Connector_Link()">Download with your Connector Software</button>
    </div>
</div>

<script>
    document.getElementById('TOKEN').textContent = wls_server_data.purchaseToken;
</script>