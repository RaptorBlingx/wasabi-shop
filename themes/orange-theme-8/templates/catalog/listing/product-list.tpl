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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file=$layout}

{block name='content'}
  <section id="main" class="mb-5">

    <div class="">
      <h1 class="h2 text-primary mb-2">
        {if isset($category) && $category.name}
          {$category.name}
        {elseif isset($manufacturer.name)}
          {$manufacturer.name}
        {elseif isset($page.page_name) && ($page.page_name == 'search')} 
          Risultati ricerca: "{$smarty.get.s}"
        {/if}
      </h1>
      <div class="text-black-50 mb-5">
        {if isset($category) && $category.name}
        {$category.description nofilter}
        {elseif isset($category.title)}
          {$manufacturer.description nofilter}
        {/if}
      </div>
    </div>

    <section id="products">
      {if $listing.products|count}

        <div>
          {block name='product_list_top'}
            {include file='catalog/_partials/products-top.tpl' listing=$listing}
          {/block}
        </div>

        {block name='product_list_active_filters'}
          <div class="visible--desktop">
            {$listing.rendered_active_filters nofilter}
          </div>
        {/block}

        <div>
          {block name='product_list'}
            {include file='catalog/_partials/products.tpl' listing=$listing}
          {/block}
        </div>

        <div id="js-product-list-bottom">
          {block name='product_list_bottom'}
            {include file='catalog/_partials/products-bottom.tpl' listing=$listing}
          {/block}
        </div>

      {else}
          <div id="js-product-list-top"></div>
          <div id="js-product-list">
              {include file='errors/not-found.tpl'}
          </div>
          <div id="js-product-list-bottom"></div>
      {/if}
    </section>

  </section>
{/block}
