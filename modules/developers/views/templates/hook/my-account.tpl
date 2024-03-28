<a id="ets_mp_messages-link" href="{$link}" class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
    <span class="link-item">
        <i class="material-icons">free_breakfast</i>
        My developer account
    </span>
</a>

{if in_array($customer.developer_status, [Developers::STATUS_APPROVED])}
    <style>
    #ets_mp_registration-link {
        display: none;
    }
    </style>
{/if}