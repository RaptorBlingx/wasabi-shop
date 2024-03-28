{extends file='page.tpl'}
{block name='page_content'}

    <h1>Developer dashboard</h1>

    <br>

    {include file="module:developers/views/templates/front/_partials/orders.tpl"}

    <br><br>
    <hr><br>

    {include file="module:developers/views/templates/front/_partials/products.tpl"}


    <br><br><br><br><br>

    <style>
        .custom-table :is(tr, td, th) {
            padding-bottom: 10px;
        }
    </style>

{/block}