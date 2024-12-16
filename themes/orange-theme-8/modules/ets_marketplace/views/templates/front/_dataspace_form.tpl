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
            
        };

         async function Add_Skill(sk){

            console.log("Adding ",sk);

            var url = `/AddProductFromDataspace.php`;


            let call = await fetch(
                url,
                {
                    method: 'POST',
                    body:JSON.stringify({ ...sk , Candidate_Seller_ID:wls_server_data.participantID})
                }
            );
            let reply = await call.json();
            if(reply.success!=undefined){
                alert('Product created with id: '+reply.success);
            }
            else{
                alert('Error creating product  '+reply.error);
            }


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

            let call = await fetch(
                url,
                {
                    method: 'GET'
                 }
            );
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

            let table = `<br/><div class="mt-4" style='width:100%; overflow:auto; padding-inline: 5px; border:thin solid rgb(230,230,230);'>
                <table class="table m-0">
                    ${ tableHeader } 
                    ${ tableBody } 
                </table>
                </div><br/><br/><br/>`;
            const tableDiv = document.getElementById('results');
            tableDiv.innerHTML = table;
            console.log("found", reply);


         }



    </script>
    
    <div class="row" style="gap: 1rem">
        <input id='wfm-query' class="col-md-6" type="text" placeholder="Query" name="Query">
        <button type="submit" onclick="Run_WFM_Query();">Submit</button>
    </div>
    
    <div class="row" id="results">

    </div>

</div>

