{**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author PrestaShop SA <contact@prestashop.com>
  * @copyright 2007-2017 PrestaShop SA
  * @license https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
  * International Registered Trademark & Property of PrestaShop SA
  *}
  {extends file=$layout}

  {block name='content'}


  <section id="main" class="{if $product.is_virtual == 1}bg-light{/if} pt-5">
    <div class="container-fluid">
      <div class="row">
        <div class="{if $product.is_virtual == 1}col-xl-5{else}col-xl-6{/if} pr-xl-5">
          {block name='page_content_container'}
          <section class="page-content--product" id="content">
            {block name='page_content'}
            {block name='product_flags'}
            {include file='catalog/_partials/product-flags.tpl'}
            {/block}
            {block name='product_cover_thumbnails'}
            {include file='catalog/_partials/product-cover-thumbnails.tpl'}
            {/block}
            {/block}
          </section>
          {/block}
        </div>
        <div class="{if $product.is_virtual == 1}col-xl-7{else}col-xl-6{/if} ">
          {block name='page_header_container'}
          {block name='page_header'}
          <h2 class="text-primary mt-3 text-uppercase"><small>{$product.category_name}</small></h2>
          <h1 class="h1 product-name">{block name='page_title'}{$product.name}{/block}</h1>
          {/block}
          {/block}

          <div class="mt-4 pt-2">
            {block name='product_prices'}
            {include file='catalog/_partials/product-prices.tpl'}
            {/block}
          </div>

          <hr class="my-4 pt-2">
          <div class="product-information ">
            {block name='product_description_short'}
            <div id="product-description-short-{$product.id}" class="mb-4 pb-2">{$product.description_short nofilter}
            </div>
            {/block}

            {if $product.is_customizable && count($product.customizations.fields)}
            {block name='product_customization'}
            {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations}
            {/block}
            {/if}

            <div class="product-actions">
              {block name='product_buy'}
              <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                <input type="hidden" name="token" value="{$static_token}">
                <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                <input type="hidden" name="id_customization" value="{$product.id_customization}"
                  id="product_customization_id">

                {block name='product_variants'}
                {include file='catalog/_partials/product-variants.tpl'}
                {/block}



                {block name='product_pack'}
                {if $packItems}
                <section class="product-pack mb-4">
                  <p class="h4">{l s='This pack contains' d='Shop.Theme.Catalog'}</p>
                  {foreach from=$packItems item="product_pack"}
                  {block name='product_miniature'}
                  {include file='catalog/_partials/miniatures/pack-product.tpl' product=$product_pack}
                  {/block}
                  {/foreach}
                </section>
                {/if}
                {/block}

                {block name='product_discounts'}
                {include file='catalog/_partials/product-discounts.tpl'}
                {/block}

                {block name='product_add_to_cart'}
                {include file='catalog/_partials/product-add-to-cart.tpl'}
                {/block}

                

                {block name='product_refresh'}
                {if !isset($product.product_url)}
                <input class="product-refresh ps-hidden-by-js" name="refresh" type="submit"
                  value="{l s='Refresh' d='Shop.Theme.Actions'}">
                {/if}
                {/block}
              </form>
              {/block}

            </div>

            <div class="row">
              <div class="col-12">
                    
                {block name='hook_display_reassurance'}
                  {hook h='displayReassurance'}
                {/block}
                
              </div>
            </div>

          </div>
        </div>
        
      </div>

      

      <div class="row mt-5">
        <div class="col-12">
          {block name='product_tabs'}
          {include file='catalog/_partials/product-tabs.tpl'}
          {/block}
        </div>
      </div>

      {block name='product_accessories'}
      {if $accessories}
      <section class="product-accessories mt-3">
        <p class="products-section-title">{l s='You might also like' d='Shop.Theme.Catalog'}</p>
        <div class="products">
          {foreach from=$accessories item="product_accessory"}
          {block name='product_miniature'}
          {include file='catalog/_partials/miniatures/product.tpl' product=$product_accessory}
          {/block}
          {/foreach}
        </div>
      </section>
      {/if}
      {/block}

      {block name='product_footer'}
      {hook h='displayFooterProduct' product=$product category=$category}
      {/block}

      {block name='product_images_modal'}
      {include file='catalog/_partials/product-images-modal.tpl'}
      {/block}

      {block name='page_footer_container'}
      <footer class="page-footer">{block name='page_footer'}{/block}</footer>
      {/block}    
    </div>
  </section>


  {/block}