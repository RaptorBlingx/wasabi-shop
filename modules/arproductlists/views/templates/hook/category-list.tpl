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

<div class="arpl-featured-categories arpl-section {if $model->getList()->isSlider()}{if $sliderConfig->nav_pos == 'top'}arpl-controls-top{/if}{/if}" id="arpl-section-{$group->id|intval}-{$model->id|intval}">
    {if $title}
        <div class="arpl-header">
            <h2 class="h2 arpl-title {if $model->getList()->titleAlign == 'left'} arpl-text-left {elseif $model->getList()->titleAlign == 'right'} arpl-text-right {else} arpl-text-center {/if} products-section-title text-uppercase">{$title|escape:'htmlall':'UTF-8'}</h2>
        </div>
    {/if}
    <div class="featured-products {if $model->getList()->isSlider()}arpl-carousel-categories {if $sliderConfig->nav_pos == 'top'}arpl-controls-top{/if} owl-carousel owl-theme{else}arpl-no-carousel-categories{/if} grid categories" id="arpl-section-{$group->id|intval}-{$model->id|intval}-carousel">
        {foreach from=$categories item="category"}
            <div class="arpl-category {if !$model->getList()->isSlider()}arpl-grid-{$model->getList()->grid|intval} arpl-grid-md-{$model->getList()->grid_md|intval} arpl-grid-sm-{$model->getList()->grid_sm|intval}{/if}" data-id="{$category->id|intval}" id="arpl-cat-{$category->id|intval}">
                <a href="{$link->getCategoryLink($category->id, $category->link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$category->name|escape:'htmlall':'UTF-8'}" class="arpl-cat-img">
                    <img width="{$imgWidth|intval}" height="{$imgHeight|intval}" src="{$link->getCatImageLink($category->link_rewrite, $category->id, $imgType)|escape:'html'}" alt="{$category->name|escape:'htmlall':'UTF-8'}" />
                </a>
                {if $model->getList()->cat_title}
                    <h5 class="arpl-cat-title">
                        <a href="{$link->getCategoryLink($category->id, $category->link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$category->name|escape:'htmlall':'UTF-8'}">{$category->name|truncate:25:'...'|escape:'htmlall':'UTF-8'}</a>
                    </h5>
                {/if}
                {if $model->getList()->cat_desc}
                    <p class="arpl-cat-desc">
                        {$category->description|truncate:255:'...':TRUE|strip_tags:'UTF-8'|escape:'htmlall':'UTF-8'}
                    </p>
                {/if}
            </div>
        {/foreach}
    </div>
</div>
{if $model->getList()->isSlider() and !$ajax}
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
                responsiveClass: true,
                {if $model->getList()->responsiveBaseElement == 'parent'}
                    responsiveBaseElement: jQuery('#arpl-section-{$group->id|intval}-{$model->id|intval}-carousel').parent(),
                {/if}
                center: {if $model->getList()->center}true{else}false{/if},
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