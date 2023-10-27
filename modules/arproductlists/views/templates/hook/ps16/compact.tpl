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

<li data-id-product="{$product.id_product|intval}" data-id-product-attribute="{$product.id_product_attribute|intval}" itemscope itemtype="http://schema.org/Product">
    <div class="arpl-thumb">
        <a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
            <img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
            {if isset($product.new) && $product.new == 1}
                    <a class="new-box" href="{$product.link|escape:'html':'UTF-8'}">
                            <span class="new-label">{l s='New' mod='arproductlists'}</span>
                    </a>
            {/if}
            {if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
                    <a class="sale-box" href="{$product.link|escape:'html':'UTF-8'}">
                            <span class="sale-label">{l s='Sale!' mod='arproductlists'}</span>
                    </a>
            {/if}
        </a>
    </div>
    <div class="arpl-content">
        <div class="arpl-content-title">
            <h3 class="h3 product-title" itemprop="name"><a href="{$product.link|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a></h3>
        </div>
        {if $product.features}
            <div class="arpl-content-features">
                <p>
                    {foreach $product.features as $feature}
                        {$feature.name|escape:'htmlall':'UTF-8'} - {$feature.value|escape:'htmlall':'UTF-8'}
                    {/foreach}
                </p>
            </div>
        {/if}
        <div class="arpl-content-desc">
            <p>{$product.description_short|strip_tags|escape:'htmlall':'UTF-8'}</p>
        </div>
        
        <div class="arpl-content-price">
            {block name='product_price_and_shipping'}
                {if $product.show_price}
                  <div class="product-price-and-shipping">
                    {if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                            <div class="content_price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                    {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                                            <span itemprop="price" class="price product-price">
                                                    {hook h="displayProductPriceBlock" product=$product type="before_price"}
                                                    {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                                            </span>
                                            <meta itemprop="priceCurrency" content="{$currency->iso_code|escape:'htmlall':'UTF-8'}" />
                                            {if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
                                                {hook h="displayProductPriceBlock" product=$product type="old_price"}
                                                <span class="old-price product-price">
                                                    {displayWtPrice p=$product.price_without_reduction}
                                                </span>
                                                {if $product.specific_prices.reduction_type == 'percentage'}
                                                    <span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
                                                {/if}
                                            {/if}
                                            {if $PS_STOCK_MANAGEMENT && isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
                                                <span class="unvisible">
                                                    {if ($product.allow_oosp || $product.quantity > 0)}
                                                        <link itemprop="availability" href="https://schema.org/InStock" />{if $product.quantity <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later|escape:'htmlall':'UTF-8'}{else}{l s='In Stock' mod='arproductlists'}{/if}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now|escape:'htmlall':'UTF-8'}{else}{l s='In Stock' mod='arproductlists'}{/if}{/if}
                                                    {elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
                                                        <link itemprop="availability" href="https://schema.org/LimitedAvailability" />{l s='Product available with different options' mod='arproductlists'}

                                                    {else}
                                                        <link itemprop="availability" href="https://schema.org/OutOfStock" />{l s='Out of stock' mod='arproductlists'}
                                                    {/if}
                                                </span>
                                            {/if}
                                            {hook h="displayProductPriceBlock" product=$product type="price"}
                                            {hook h="displayProductPriceBlock" product=$product type="unit_price"}
                                    {/if}
                            </div>
                    {/if}

                    {hook h='displayProductPriceBlock' product=$product type="before_price"}

                    {hook h='displayProductPriceBlock' product=$product type='unit_price'}

                    {hook h='displayProductPriceBlock' product=$product type='weight'}
                  </div>
                {/if}
            {/block}
        </div>
        {block name='product_variants'}
          {*if $product.main_variants}
            {include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
          {/if*}
        {/block}
    </div>
    <div class="arpl-buttons">
        {if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
                {if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
                    {capture}add=1&amp;id_product={$product.id_product|intval}{if isset($product.id_product_attribute) && $product.id_product_attribute}&amp;ipa={$product.id_product_attribute|intval}{/if}{if isset($static_token)}&amp;token={$static_token|escape:'htmlall':'UTF-8'}{/if}{/capture}
                    <a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='arproductlists'}" data-id-product-attribute="{$product.id_product_attribute|intval}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
                        <span>{l s='Add to cart' mod='arproductlists'}</span>
                    </a>
                {else}
                    <span class="button ajax_add_to_cart_button btn btn-default disabled">
                        <span>{l s='Add to cart' mod='arproductlists'}</span>
                    </span>
                {/if}
        {/if}
    </div>
</li>