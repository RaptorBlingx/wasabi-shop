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
                                    {include file="catalog/_partials/miniatures/product.tpl" product=$product}
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