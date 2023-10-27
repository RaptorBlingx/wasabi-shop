{*
* 2019 Areama
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@areama.net so we can send you a copy immediately.
*
*
*  @author Areama <contact@areama.net>
*  @copyright  2019 Areama
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Areama
*}

<div id="blockcart-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title h6 text-sm-center" id="myModalLabel"><i class="material-icons rtl-no-flip">&#xE876;</i>{l s='Products successfully added to your shopping cart' mod='arproductlists'}</h4>
            </div>
            <div class="modal-body">
                {foreach from=$products item=product name=cartProducts}
                    <div class="row">
                        <div class="col-md-5 divide-right">
                            <div class="row">
                                <div class="col-md-6">
                                    <img class="product-image" src="{$product.cover.medium.url|escape:'htmlall':'UTF-8'}" alt="{$product.cover.legend|escape:'htmlall':'UTF-8'}" title="{$product.cover.legend|escape:'htmlall':'UTF-8'}" itemprop="image">
                                    <p></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="h6 product-name">{$product.name|escape:'htmlall':'UTF-8'}</h6>
                                    <p>{$product.price|escape:'htmlall':'UTF-8'}</p>
                                    {hook h='displayProductPriceBlock' product=$product type="unit_price"}
                                    {foreach from=$product.attributes item="property_value" key="property"}
                                        <span><strong>{$property|escape:'htmlall':'UTF-8'}</strong>: {$property_value|escape:'htmlall':'UTF-8'}</span><br>
                                    {/foreach}
                                    <p><strong>{l s='Quantity:' mod='arproductlists'}</strong>&nbsp;{$product.cart_quantity|escape:'htmlall':'UTF-8'}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            {if $smarty.foreach.cartProducts.first}
                                <div class="cart-content">
                                    {if $cart.products_count > 1}
                                        <p class="cart-products-count">{l s='There are %products_count% items in your cart.' sprintf=['%products_count%' => $cart.products_count|escape:'htmlall':'UTF-8'] mod='arproductlists'}</p>
                                    {else}
                                        <p class="cart-products-count">{l s='There is %product_count% item in your cart.' sprintf=['%product_count%' =>$cart.products_count|escape:'htmlall':'UTF-8'] mod='arproductlists'}</p>
                                    {/if}
                                    <p><strong>{l s='Total products:' mod='arproductlists'}</strong>&nbsp;{$cart.subtotals.products.value|escape:'htmlall':'UTF-8'}</p>
                                    <p><strong>{l s='Total shipping:' mod='arproductlists'}</strong>&nbsp;{$cart.subtotals.shipping.value|escape:'htmlall':'UTF-8'} {hook h='displayCheckoutSubtotalDetails' subtotal=$cart.subtotals.shipping}</p>
                                    {if $cart.subtotals.tax}
                                        <p><strong>{$cart.subtotals.tax.label|escape:'htmlall':'UTF-8'}</strong>&nbsp;{$cart.subtotals.tax.value|escape:'htmlall':'UTF-8'}</p>
                                    {/if}
                                    {if $cart.subtotals.discounts}
                                        <p><strong>{$cart.subtotals.discounts.label|escape:'htmlall':'UTF-8'}</strong>&nbsp;{$cart.subtotals.discounts.value|escape:'htmlall':'UTF-8'}</p>
                                    {/if}
                                    <p><strong>{l s='Total:' mod='arproductlists'}</strong>&nbsp;{$cart.totals.total.value|escape:'htmlall':'UTF-8'} {$cart.labels.tax_short|escape:'htmlall':'UTF-8'}</p>
                                </div>
                            {/if}
                        </div>
                    </div>
                {/foreach}
                <div class="row">
                    <div class="col-md-5"></div>
                    <div class="col-md-7">
                        <div class="cart-content">
                            <div class="cart-content-btn">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Continue shopping' mod='arproductlists'}</button>
                                <a href="{$cart_url|escape:'htmlall':'UTF-8'}" class="btn btn-primary"><i class="material-icons rtl-no-flip">&#xE876;</i>{l s='Proceed to checkout' mod='arproductlists'}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
