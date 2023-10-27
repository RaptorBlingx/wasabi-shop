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
{if !$contentOnly}
<div class="featured-products arpl-section {if $model->getList()->isSlider()}{if $sliderConfig->nav_pos == 'top'}arpl-controls-top{/if}{/if} {if $model->getList()->product_update}arpl-refresh-on-product-update{/if} {if $model->getList()->sortorder and $model->getList()->getFrontendOrderOptions()}arpl-has-sort{/if} {if $title}arpl-has-title{/if}" id="arpl-section-{$group->id|intval}-{$model->id|intval}" data-group-id="{$group->id|intval}" data-list-id="{$model->id|intval}">
    {if $title or ($model->getList()->sortorder and $model->getList()->getFrontendOrderOptions())}
        <div class="arpl-header">
            {if $title}
                <h2 class="h2 arpl-title products-section-title text-uppercase {if $model->getList()->titleAlign == 'left'} arpl-text-left {elseif $model->getList()->titleAlign == 'right'} arpl-text-right {else} arpl-text-center {/if}">{$title|escape:'htmlall':'UTF-8'}</h2>
            {/if}
            {if $model->getList()->sortorder and $model->getList()->getFrontendOrderOptions()}
                <div class="arpl-sort-container">
                    <select class="arpl-sort form-control" onchange="arPL.loadAjaxTab({$group->id|intval}, {$model->id|intval}, 1);">
                        {foreach from=$model->getList()->getFrontendOrderOptions() item=$sortItem key=$sortKey}
                            <option {if $model->getList()->getDefaultSortOrder() == $sortKey}selected="selected"{/if} value="{$sortKey}">{$sortItem}</option>
                        {/foreach}
                    </select>
                </div>
            {/if}
        </div>
    {/if}
{/if}
    <div class="featured-products {if $model->getList()->isSlider()}arpl-carousel-products {if $sliderConfig->nav_pos == 'top'}arpl-controls-top{/if} owl-carousel owl-theme{else}arpl-no-carousel-products{/if} grid products arpl-content" id="arpl-section-{$group->id|intval}-{$model->id|intval}-carousel">
        {if $model->getList()->isSlider() or $model->getList()->isStandard()}
            {foreach from=$products item="product"}
                {include file="catalog/_partials/miniatures/product.tpl" product=$product}
            {/foreach}
        {elseif $model->getList()->isCompact()}
            <ul class="arpl-product-list-view">
                {foreach from=$products item="product"}
                    {include file="./compact.tpl" product=$product}
                {/foreach}
            </ul>
        {/if}
    </div>
{if !$contentOnly}
    {if $model->getList()->getMoreLink() and $model->getList()->more_link}
        <div class="arpl-more-container">
            <a class="arpl-more-link" href="{$model->getList()->getMoreLink() nofilter}">
                {$model->more_link|escape:'htmlall':'UTF-8'}<i class="material-icons">&#xE315;</i>
            </a>
        </div>
    {/if}
</div>
{/if}
{if $model->getList()->isSlider() and !$ajax and !$contentOnly}
    <script>
        var arPL_list_{$group->id|intval}{$model->id|intval} = null;
        window.addEventListener('load', function(){
            arplInitOWL{$group->id|intval}{$model->id|intval}();
            prestashop.on('updateProductList', function(){
                arplInitOWL{$group->id|intval}{$model->id|intval}();
            });
        });
        function arplInitOWL{$group->id|intval}{$model->id|intval}(){
            var el = jQuery('#arpl-section-{$group->id|intval}-{$model->id|intval}-carousel').arplOwlCarousel({
                loop: {if $model->getList()->loop}true{else}false{/if},
                margin: 0,
                nav: {if $model->getList()->controls}true{else}false{/if},
                dots: {if $model->getList()->dots}true{else}false{/if},
                center: {if $model->getList()->center}true{else}false{/if},
                responsiveClass: true,
                {if $model->getList()->responsiveBaseElement == 'parent'}
                    responsiveBaseElement: jQuery('#arpl-section-{$group->id|intval}-{$model->id|intval}-carousel').parent(),
                {/if}
                lazyLoad: false,
                {if $model->getList()->slide_by}
                    slideBy: {$model->getList()->slide_by|intval},
                {/if}
                {if $model->getList()->autoplay}
                    autoplay: true,
                    autoplayTimeout: {$model->getList()->autoplayTimeout|intval},
                {/if}
                autoplayHoverPause: true,
                mouseDrag: {if $model->getList()->drag}true{else}false{/if},
                touchDrag: {if $model->getList()->drag}true{else}false{/if},
                navText: [
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path fill="currentColor" d="M238.475 475.535l7.071-7.07c4.686-4.686 4.686-12.284 0-16.971L50.053 256 245.546 60.506c4.686-4.686 4.686-12.284 0-16.971l-7.071-7.07c-4.686-4.686-12.284-4.686-16.97 0L10.454 247.515c-4.686 4.686-4.686 12.284 0 16.971l211.051 211.05c4.686 4.686 12.284 4.686 16.97-.001z" class=""></path></svg>',
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path fill="currentColor" d="M17.525 36.465l-7.071 7.07c-4.686 4.686-4.686 12.284 0 16.971L205.947 256 10.454 451.494c-4.686 4.686-4.686 12.284 0 16.971l7.071 7.07c4.686 4.686 12.284 4.686 16.97 0l211.051-211.05c4.686-4.686 4.686-12.284 0-16.971L34.495 36.465c-4.686-4.687-12.284-4.687-16.97 0z" class=""></path></svg>'
                ],
                onInitialized: function(e) {
                    if (typeof bLazyObject != 'undefined') {
                        bLazyObject.load($('#arpl-section-{$group->id|intval}-{$model->id|intval}-carousel .b-lazy:not(.b-initialized)'), true);
                    }
                },
                onTranslate: function(e) {
                    if (typeof bLazyObject != 'undefined') {
                        bLazyObject.load($('#arpl-section-{$group->id|intval}-{$model->id|intval}-carousel .b-lazy:not(.b-initialized)'), true);
                    }
                    $(document).find(".owl-item.active .product-thumbnail img.js-lazy").trigger("appear");
                    {if !$model->getList()->autoplay}
                        arPL_list_{$group->id|intval}{$model->id|intval}.trigger('stop.owl.autoplay');
                    {/if}
                },
                responsive:{$model->getList()->getResponsiveBreakdowns(1) nofilter}
            });
            arPL_list_{$group->id|intval}{$model->id|intval} = el;
            {if $model->getList()->custom_js}
                {$model->getList()->custom_js nofilter}
            {/if}
        }
        {if $initSlider}
            arplInitOWL{$group->id|intval}{$model->id|intval}();
        {/if}
    </script>
{/if}