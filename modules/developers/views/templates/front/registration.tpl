{extends file='page.tpl'}
{block name='page_content'}

    {if $status === null}
        {* Not a developer *}
        <div class="jumbotron">
            <h1 class="display-4">{l s='Become a developer' mod='developers'}</h1>
            <p class="lead">{l s='Submit a request to become a developer.' mod='developers'}</p>
            <hr class="my-4">
            <form class="m-0" method="post">
                <button name="become-developer" class="btn btn-primary btn-lg" href="#" role="button">{l s='Submit request' mod='developers'}</button>
            </form>
        </div>
    {elseif $status === \Developers::STATUS_PENDING}
        <div class="jumbotron">
            <h1 class="display-4">{l s='Request submitted' mod='developers'}</h1>
            <p class="lead">{l s='Your request to become a developer has been submitted successfully. Be patient and wait for approvation.' mod='developers'}</p>
        </div>
    {elseif $status === \Developers::STATUS_REFUSED}
        <div class="jumbotron">
            <h1 class="display-4">{l s='Account blocked' mod='developers'} <i class="material-icons">warning</i></h1>
            <p class="lead">{l s='Your developer account has been blocked' mod='developers'}</p>
        </div>
    {/if}


{/block}