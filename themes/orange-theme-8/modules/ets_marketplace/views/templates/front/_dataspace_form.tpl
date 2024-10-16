<div class="container mb-4">
    <div class="row">
        <span class="text-danger">Test Dataspace</span>
    </div>
    <form action="{$dataspaceHubAddress}/Broker/FindSkills" target="_blank">
        <div class="row" style="gap: 1rem">
            <input type="hidden" name="Participant" value="{$participantDataspaceId}">
            <input class="col-md-6" type="text" placeholder="Query" name="Query">
            <button class="col-auto" type="submit">Submit</button>
        </div>
    </form>
</div>