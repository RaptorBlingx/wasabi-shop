<h2>{l s='Products' mod='developers'}</h2>
<table class="w-100 custom-table">
    {if $products|count}
        <tr>
            <th>#</th>
            <th class="text-uppercase">{l s="Product name" mod="developers"}</th>
            <th class="text-uppercase">{l s="Price" mod="developers"}</th>
            <th class="text-uppercase">{l s="Status" mod="developers"}</th>
            <th></th>
        </tr>
    {/if}
    {foreach $products as $product}
        <tr>
            <td>{$product->id}</td>
            <td>{$product->name}</td>
            <td>{Tools::displayPrice($product->price)}</td>
            <td>
                <span {if !$product->active}style="color: red"{/if}>
                    {if $product->active}
                        {l s='Online'}
                    {else}
                        {l s='Offline'}
                    {/if}
                </span>
            </td>
            <td style="width: 0; white-space: nowrap">
                {if $product->active}
                    <a class="btn btn-primary d-inline py-2" href="{Context::getContext()->link->getProductLink($product)}">Show</a>
                {/if}
            </td>
        </tr>
    {foreachelse}
        <tr>
            <td>
                No products
            </td>
        </tr>
    {/foreach}

</table>