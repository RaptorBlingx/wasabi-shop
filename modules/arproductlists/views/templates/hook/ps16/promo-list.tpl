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

<div class="ar-pl-promotions arpl-section" id="arpl-section-{$groupModel->id|intval}-{$model->id|intval}">
{if $title}
    <h2 class="h2 arpl-title {if $model->getList()->titleAlign == 'left'} arpl-text-left {elseif $model->getList()->titleAlign == 'right'} arpl-text-right {else} arpl-text-center {/if} products-section-title text-uppercase">{$title|escape:'htmlall':'UTF-8'}</h2>
{/if}
    <div class="arpl-promo-list">
    {foreach $list as $key => $promo}
        <div class="arpl-promo arpl-promo-cols-{$promo.groups|count + 1}" id="arpl-promo-{$key|escape:'htmlall':'UTF-8'}" data-id="{$key|escape:'htmlall':'UTF-8'}">
            <input type="hidden" name="token" id="arpl-promo-token-{$key|escape:'htmlall':'UTF-8'}" value="{$static_token|escape:'htmlall':'UTF-8'}" />
            <div class="arpl-promo-inner">
                {foreach from=$promo.groups item=group key=k name=group}
                    <div data-id="{$k|intval}" class="ar-pl-promo-group">
                        <div class="ar-pl-promo-products owl-carousel owl-theme" id="ar-pl-promo-products-{$groupModel->id|intval}-{$k|escape:'htmlall':'UTF-8'}">
                            {foreach from=$group item=product name=product}
                                <div data-id="{$product.id_product|intval}" class="ar-pl-promo-product {if $smarty.foreach.product.first}active{/if}">
                                    <div data-id-product="{$product.id_product|intval}" class="ajax_block_product">
                                        <div class="product-container" itemscope itemtype="https://schema.org/Product">
                                                <div class="left-block">
                                                        <div class="product-image-container">
                                                                <a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
                                                                        <img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                                                                </a>
                                                                {if isset($quick_view) && $quick_view}
                                                                        <div class="quick-view-wrapper-mobile">
                                                                        <a class="quick-view-mobile" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}">
                                                                                <i class="icon-eye-open"></i>
                                                                        </a>
                                                                </div>
                                                                <a class="quick-view" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}">
                                                                        <span>{l s='Quick view' mod='arproductlists'}</span>
                                                                </a>
                                                                {/if}
                                                                {if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                                                                        <div class="content_price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                                                                {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                                                                                        <span itemprop="price" class="price product-price">
                                                                                                {hook h="displayProductPriceBlock" product=$product type="before_price"}
                                                                                                {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                                                                                        </span>
                                                                                        <meta itemprop="priceCurrency" content="{$currency->iso_code|escape:'html':'UTF-8'}" />
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
                                                                                                            <link itemprop="availability" href="https://schema.org/InStock" />{if $product.quantity <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later|escape:'html':'UTF-8'}{else}{l s='In Stock' mod='arproductlists'}{/if}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now|escape:'html':'UTF-8'}{else}{l s='In Stock' mod='arproductlists'}{/if}{/if}
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
                                                        </div>
                                                        {if isset($product.is_virtual) && !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
                                                        {hook h="displayProductPriceBlock" product=$product type="weight"}
                                                </div>
                                                <div class="right-block">
                                                        <h5 itemprop="name">
                                                                {if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
                                                                <a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
                                                                        {$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
                                                                </a>
                                                        </h5>
                                                        {capture name='displayProductListReviews'}{hook h='displayProductListReviews' product=$product}{/capture}
                                                        {if $smarty.capture.displayProductListReviews}
                                                                <div class="hook-reviews">
                                                                {hook h='displayProductListReviews' product=$product}
                                                                </div>
                                                        {/if}
                                                        {if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                                                        <div class="content_price">
                                                                {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                                                                        {hook h="displayProductPriceBlock" product=$product type='before_price'}
                                                                        <span class="price product-price">
                                                                                {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                                                                        </span>
                                                                        {if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
                                                                                {hook h="displayProductPriceBlock" product=$product type="old_price"}
                                                                                <span class="old-price product-price">
                                                                                        {displayWtPrice p=$product.price_without_reduction}
                                                                                </span>
                                                                                {hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
                                                                                {if $product.specific_prices.reduction_type == 'percentage'}
                                                                                        <span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
                                                                                {/if}
                                                                        {/if}
                                                                        {hook h="displayProductPriceBlock" product=$product type="price"}
                                                                        {hook h="displayProductPriceBlock" product=$product type="unit_price"}
                                                                        {hook h="displayProductPriceBlock" product=$product type='after_price'}
                                                                {/if}
                                                        </div>
                                                        {/if}
                                                        <div class="button-container">
                                                                {if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
                                                                        {if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
                                                                                {capture}add=1&amp;id_product={$product.id_product|intval}{if isset($product.id_product_attribute) && $product.id_product_attribute}&amp;ipa={$product.id_product_attribute|intval}{/if}{if isset($static_token)}&amp;token={$static_token|escape:'html':'UTF-8'}{/if}{/capture}
                                                                                <a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='arproductlists'}" data-id-product-attribute="{$product.id_product_attribute|intval}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
                                                                                        <span>{l s='Add to cart' mod='arproductlists'}</span>
                                                                                </a>
                                                                        {else}
                                                                                <span class="button ajax_add_to_cart_button btn btn-default disabled">
                                                                                        <span>{l s='Add to cart' mod='arproductlists'}</span>
                                                                                </span>
                                                                        {/if}
                                                                {/if}
                                                                <a class="button lnk_view btn btn-default" href="{$product.link|escape:'html':'UTF-8'}" title="{l s='View' mod='arproductlists'}">
                                                                        <span>{if (isset($product.customization_required) && $product.customization_required)}{l s='Customize' mod='arproductlists'}{else}{l s='More' mod='arproductlists'}{/if}</span>
                                                                </a>
                                                        </div>
                                                        {if isset($product.color_list)}
                                                                <div class="color-list-container">{$product.color_list nofilter}</div>
                                                        {/if}
                                                        <div class="product-flags">
                                                                {if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                                                                        {if isset($product.online_only) && $product.online_only}
                                                                                <span class="online_only">{l s='Online only' mod='arproductlists'}</span>
                                                                        {/if}
                                                                {/if}
                                                                {if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
                                                                        {elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
                                                                                <span class="discount">{l s='Reduced price!' mod='arproductlists'}</span>
                                                                        {/if}
                                                        </div>
                                                        {if (!$PS_CATALOG_MODE && $PS_STOCK_MANAGEMENT && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                                                                {if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
                                                                        <span class="availability">
                                                                                {if ($product.allow_oosp || $product.quantity > 0)}
                                                                                        <span class="{if $product.quantity <= 0 && isset($product.allow_oosp) && !$product.allow_oosp} label-danger{elseif $product.quantity <= 0} label-warning{else} label-success{/if}">
                                                                                                {if $product.quantity <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later|escape:'html':'UTF-8'}{else}{l s='In Stock' mod='arproductlists'}{/if}{else}{l s='Out of stock' mod='arproductlists'}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now|escape:'html':'UTF-8'}{else}{l s='In Stock' mod='arproductlists'}{/if}{/if}
                                                                                        </span>
                                                                                {elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
                                                                                        <span class="label-warning">
                                                                                                {l s='Product available with different options' mod='arproductlists'}
                                                                                        </span>
                                                                                {else}
                                                                                        <span class="label-danger">
                                                                                                {l s='Out of stock' mod='arproductlists'}
                                                                                        </span>
                                                                                {/if}
                                                                        </span>
                                                                {/if}
                                                        {/if}
                                                </div>
                                        </div><!-- .product-container> -->
                                </div>
                                </div>
                            {/foreach}
                        </div>
                        {if $group|count > 1}
                            <script>
                                window.addEventListener('load', function(){
                                    var arPlOWL{$groupModel->id|intval}{$k|intval} = jQuery('#ar-pl-promo-products-{$groupModel->id|intval}-{$k|escape:'htmlall':'UTF-8'}').arplOwlCarousel({
                                        loop: false,
                                        margin: 0,
                                        nav: true,
                                        dots: false,
                                        responsiveClass: true,
                                        center: false,
                                        lazyLoad: false,
                                        autoplay: false,
                                        autoplayHoverPause: true,
                                        items: 1,
                                        navText: [
                                            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path fill="currentColor" d="M238.475 475.535l7.071-7.07c4.686-4.686 4.686-12.284 0-16.971L50.053 256 245.546 60.506c4.686-4.686 4.686-12.284 0-16.971l-7.071-7.07c-4.686-4.686-12.284-4.686-16.97 0L10.454 247.515c-4.686 4.686-4.686 12.284 0 16.971l211.051 211.05c4.686 4.686 12.284 4.686 16.97-.001z" class=""></path></svg>',
                                            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path fill="currentColor" d="M17.525 36.465l-7.071 7.07c-4.686 4.686-4.686 12.284 0 16.971L205.947 256 10.454 451.494c-4.686 4.686-4.686 12.284 0 16.971l7.071 7.07c4.686 4.686 12.284 4.686 16.97 0l211.051-211.05c4.686-4.686 4.686-12.284 0-16.971L34.495 36.465c-4.686-4.687-12.284-4.687-16.97 0z" class=""></path></svg>'
                                        ],
                                    });
                                    arPlOWL{$groupModel->id|intval}{$k|intval}.on('changed.owl.carousel', function(event) {
                                        setTimeout(function(){
                                            $(event.target).find('.owl-item>div').removeClass('active');
                                            $(event.target).find('.owl-item.active>div').addClass('active');
                                            var key = [];
                                            $(event.target).parents('.arpl-promo').find('.ar-pl-promo-product.active').each(function(){
                                                key.push($(this).data('id'));
                                            });
                                            $(event.target).parents('.arpl-promo').find('.ar-pl-promo-total-item').removeClass('active');
                                            $('#ar-pl-price-{$groupModel->id|intval}-' + key.join('-')).addClass('active');
                                        }, 200);
                                    });
                                });
                            </script>
                        {/if}
                    </div>
                {/foreach}
                <div class="ar-pl-promo-group ar-pl-promo-total">
                    {foreach from=$promo.prices item=price name=price}
                        <div class="ar-pl-promo-total-item {if $smarty.foreach.price.first}active{/if}" id="ar-pl-price-{$groupModel->id|intval}-{$price.key|escape:'htmlall':'UTF-8'}">
                            <div class="ar-pl-promo-total-old-price">{$price.oldPrice|escape:'htmlall':'UTF-8'}</div>
                            <div class="ar-pl-promo-total-actual-price">{$price.actualPrice|escape:'htmlall':'UTF-8'}</div>
                            {if $promo.cart_rule.reduction_percent}
                                <div class="ar-pl-promo-total-discount">-{$promo.cart_rule.reduction_percent|intval}%</div>
                            {/if}
                            <button class="btn btn-primary ar-pl-promo-total-add-to-cart" type="button" data-promo-id="{$key|escape:'htmlall':'UTF-8'}">{l s='Buy now' mod='arproductlists'}</button>
                            <div class="ar-pl-promo-total-save">
                                <span>{l s='you save' mod='arproductlists'}</span> {$price.save|escape:'htmlall':'UTF-8'}
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    {/foreach}
    </div>
</div>