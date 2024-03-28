<h2>{l s='Orders' mod='developers'}</h2>
<table class="w-100 custom-table">
    {if $orders|count}
        <tr>
            <th>#</th>
            <th class="text-uppercase">{l s="Date" mod="developers"}</th>
            <th class="text-uppercase">{l s="Product name" mod="developers"}</th>
            <th class="text-uppercase">{l s="Quantity" mod="developers"}</th>
            <th class="text-uppercase">{l s="Price" mod="developers"}</th>
            <th style="text-align: end;" class="text-uppercase">{l s="Status" mod="developers"}</th>
        </tr>
    {/if}
    {foreach $orders as $order}
        <tr>
            <td>
                {$order.details->id}
            </td>
            <td>
                {date('d/m/Y', $order.order->date_add|strtotime)}
            </td>
            <td>
                {$order.details->product_name}
            </td>
            <td>
                {$order.details->product_quantity}
            </td>
            <td>
                {\Tools::displayPrice($order.details->unit_price_tax_incl)}
            </td>
            <td style="text-align: end;">
                <span style="background-color: {$order.status->color}; color: #fff; padding: 5px 10px;">
                    {$order.status->name}
                </span>
            </td>
        </tr>
    {foreachelse}
        <tr>
            <td>
                No orders
            </td>
        </tr>
    {/foreach}

</table>

<div class="row justify-content-end mt-4">
    <span class="col-auto">
        <h3>{l s='Total' mod='developers'}</h3>
    </span>
    <span class="col-auto">{Tools::displayPrice($total)}</span>
</div>