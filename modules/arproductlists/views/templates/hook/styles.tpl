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

/* slider styles */
.arpl-carousel-products.owl-carousel .owl-nav button, .arpl-carousel-categories.owl-carousel .owl-nav button{
    {if $sliderConfig->nav_width}
        width: {$sliderConfig->nav_width|intval}px;
    {/if}
    {if $sliderConfig->nav_height}
        height: {$sliderConfig->nav_height|intval}px;
    {/if}
    {if $sliderConfig->nav_bg}
        background-color: {$sliderConfig->nav_bg|escape:'htmlall':'UTF-8'};
    {/if}
    {if $sliderConfig->nav_color}
        color: {$sliderConfig->nav_color|escape:'htmlall':'UTF-8'};
    {/if}
}
.arpl-carousel-products.owl-carousel.arpl-controls-top .owl-nav,
.arpl-carousel-categories.owl-carousel.arpl-controls-top .owl-nav{
    top: {$sliderConfig->nav_offset|intval}px;
}
.arpl-carousel-products.owl-carousel .owl-nav button:hover, .arpl-carousel-categories.owl-carousel .owl-nav button:hover{
    {if $sliderConfig->nav_h_bg}
        background-color: {$sliderConfig->nav_h_bg|escape:'htmlall':'UTF-8'};
    {/if}
    {if $sliderConfig->nav_h_color}
        color: {$sliderConfig->nav_h_color|escape:'htmlall':'UTF-8'};
    {/if}
}
.arpl-carousel-products.owl-theme .owl-dots .owl-dot span,
.arpl-carousel-categories.owl-theme .owl-dots .owl-dot span{
    {if $sliderConfig->dots_size}
        width: {$sliderConfig->dots_size|intval}px;
        height: {$sliderConfig->dots_size|intval}px;
    {/if}
    {if $sliderConfig->dots_bg}
        background-color: {$sliderConfig->dots_bg|escape:'htmlall':'UTF-8'};
    {/if}
}
.arpl-carousel-products.owl-theme .owl-dots .owl-dot:hover span,
.arpl-carousel-categories.owl-theme .owl-dots .owl-dot:hover span,
.arpl-carousel-products.owl-theme .owl-dots .owl-dot.active span,
.arpl-carousel-categories.owl-theme .owl-dots .owl-dot.active span{
    {if $sliderConfig->dots_h_bg}
        background-color: {$sliderConfig->dots_h_bg|escape:'htmlall':'UTF-8'};
    {/if}
}
/* end slider styles */


/* promo styles */
.arpl-promo-inner{
    {if $promoConfig->border_color}
        border: 1px solid {$promoConfig->border_color|escape:'htmlall':'UTF-8'};
    {else}
        border: 0 none;
    {/if}
    {if $promoConfig->border_radius}
        border-radius: {$promoConfig->border_radius|intval}px;
    {/if}
    {if $promoConfig->background}
        background-color: {$promoConfig->background|escape:'htmlall':'UTF-8'};
    {/if}
}

{if $promoConfig->action_section_bg}
    .ar-pl-promo-total-item{
        background-color: {$promoConfig->action_section_bg|escape:'htmlall':'UTF-8'};
    }
{/if}

{if $promoConfig->price_color}
    .ar-pl-promo-total-actual-price{
        color: {$promoConfig->price_color|escape:'htmlall':'UTF-8'};
    }
{/if}

{if $promoConfig->old_price_color}
    .ar-pl-promo-total-old-price{
        color: {$promoConfig->old_price_color|escape:'htmlall':'UTF-8'};
    }
{/if}

{if $promoConfig->line_color}
    .ar-pl-promo-total-old-price::before{
        background-color: {$promoConfig->line_color|escape:'htmlall':'UTF-8'};
    }
{/if}

{if $promoConfig->you_save_color}
    .ar-pl-promo-total-save span{
        color: {$promoConfig->you_save_color|escape:'htmlall':'UTF-8'};
    }
{/if}

{if $promoConfig->save_amount_color}
    .ar-pl-promo-total-save{
        color: {$promoConfig->save_amount_color|escape:'htmlall':'UTF-8'};
    }
{/if}
/* end promo styles */

/* tabbed styles */
.arpl-group-tabbed .nav-item .nav-link, .arpl-group-tabbed .nav-item .nav-separtor{
    {if $tabsConfig->tab_bg}
        background-color: {$tabsConfig->tab_bg|escape:'htmlall':'UTF-8'};
    {/if}
    {if $tabsConfig->tab_color}
        color: {$tabsConfig->tab_color|escape:'htmlall':'UTF-8'};
    {/if}
    {if $tabsConfig->border and $tabsConfig->border_color}
        border-bottom: {$tabsConfig->border|intval}px solid {$tabsConfig->border_color|escape:'htmlall':'UTF-8'};
    {/if}
}
.arpl-group-tabbed .nav-tabs .nav-link.active, .arpl-group-tabbed .nav-tabs .nav-link:focus{
    {if $tabsConfig->border and $tabsConfig->border_color}
        border: {$tabsConfig->border|intval}px solid {$tabsConfig->border_color|escape:'htmlall':'UTF-8'};
        border-bottom: 0px solid;
    {/if}
}
.arpl-group-tabbed .nav-tabs .nav-link:hover{
    border: 1px solid transparent;
    border-bottom: 0px solid;
}
.arpl-group-tabbed .nav-item .nav-link.active, .arpl-group-tabbed .nav-item .nav-separtor.active{
    {if $tabsConfig->active_tab_bg}
        background-color: {$tabsConfig->active_tab_bg|escape:'htmlall':'UTF-8'};
    {/if}
    {if $tabsConfig->active_tab_color}
        color: {$tabsConfig->active_tab_color|escape:'htmlall':'UTF-8'};
    {/if}
}
.arpl-group .arpl-tab-content > .tab-pane{
    {if $tabsConfig->active_tab_bg}
        background-color: {$tabsConfig->active_tab_bg|escape:'htmlall':'UTF-8'};
    {/if}
}
{if $tabsConfig->border and $tabsConfig->border_color}
    .arpl-tab-content{
        border-left: {$tabsConfig->border|intval}px solid {$tabsConfig->border_color|escape:'htmlall':'UTF-8'};
        border-right: {$tabsConfig->border|intval}px solid {$tabsConfig->border_color|escape:'htmlall':'UTF-8'};
        border-bottom: {$tabsConfig->border|intval}px solid {$tabsConfig->border_color|escape:'htmlall':'UTF-8'};
    }
    .arpl-group-tabbed .nav-item .nav-link.active, .arpl-group-tabbed .nav-item .nav-separtor.active{
        border-color: {$tabsConfig->border_color|escape:'htmlall':'UTF-8'};
        border-width: {$tabsConfig->border|intval}px;
        border-bottom-color: transparent;
    }
    .arpl-group-tabbed .nav-tabs{
        border-color: {$tabsConfig->border_color|escape:'htmlall':'UTF-8'};
    }
{/if}
{if $tabsConfig->tab_padding}
    .arpl-group-tabbed .nav-tabs .nav-link{
        padding: {$tabsConfig->tab_padding|escape:'htmlall':'UTF-8'};
    }
{/if}
{if $tabsConfig->pane_padding}
    .arpl-tab-content .tab-pane.active{
        padding: {$tabsConfig->pane_padding|escape:'htmlall':'UTF-8'};
    }
{/if}
/* end tabbed styles */

/* section styles */
.arpl-group .arpl-title{
    {if $sectionConfig->header_color}
        color: {$sectionConfig->header_color|escape:'htmlall':'UTF-8'};
    {/if}
    {if $sectionConfig->header_size}
        font-size: {$sectionConfig->header_size|intval}px;
    {/if}
    {if $sectionConfig->header_top_margin}
        margin-top: {$sectionConfig->header_top_margin|intval}px;
    {/if}
    {if $sectionConfig->header_bottom_margin}
        margin-bottom: {$sectionConfig->header_bottom_margin|intval}px;
    {/if}
}
.arpl-non-tabbed-group .arpl-section{
    {if $sectionConfig->padding}
        padding: {$sectionConfig->padding|escape:'htmlall':'UTF-8'};
    {/if}
    {if $sectionConfig->border and $sectionConfig->border_color}
        border: {$sectionConfig->border|intval}px solid {$sectionConfig->border_color|escape:'htmlall':'UTF-8'};
    {/if}
    {if $sectionConfig->background}
        background-color: {$sectionConfig->background|escape:'htmlall':'UTF-8'};
    {/if}
}
/* end section styles */

/* custom styles */
{$generalConfig->custom_css nofilter}
/* end custom styles */