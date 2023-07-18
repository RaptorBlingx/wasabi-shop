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
  
  {block name="breadcrumb"}
    <span class="shop-by-brand-breadcrumbs">
      {include file='../_partials/breadcrumb.tpl'}
    </span>
  
  {/block}
  
  
  {block name='content'}
  
    <section>
      <div class="w-100">
        <div class="row">
          <div class="col-12 text-center">
            <h1 class="h2 text-primary">Tutti i brand</h1>
          </div>
        </div>
      </div>
    </section>
  
    <section class="section-padding pt-3">
      <div class="w-100">
        {block name='brand_miniature'}
  
          {assign var="index_letter" value=""}
          {assign var="previus_letter" value=""}
          {assign var="total_brands" value="0"}
  
  
  
          <div class="row justify-content-center">
            <div class="col-auto">
              <div class="p-3 rounded bg-secondary text-center d-inline-block mx-auto">
                <div class="row justify-content-center">
                  {foreach from=$brands item=brand}
                    {if {$brand.nb_products|substr:0:1|lower} > 0}
                      {if ($brand.name|substr:0:1|lower) != $index_letter}
                        <div class="col-auto">
                          {$index_letter = $brand.name|substr:0:1|lower}
                          <a href="#brand-{$index_letter}"
                            class="d-inline-block h4 font-weight-bold m-0 text-white text-uppercase">{$index_letter}</a>
                        </div>
                      {/if}
                    {/if}
                  {/foreach}
                </div>
              </div>
            </div>
          </div>
  
          <div class="row justify-content-start">
            {foreach from=$brands item=brand}
              {if {$brand.nb_products|substr:0:1|lower} > 0}
                {if ($brand.name|substr:0:1|lower) == $previus_letter}
                  {include file='catalog/_partials/miniatures/brand.tpl' brand=$brand}
                {else}
                  {$previus_letter = $brand.name|substr:0:1|lower}
                  <div class="col-12 mt-5 text-center text-md-left">
                    <h3 class="h1 text-primary" id="brand-{$previus_letter}">{$previus_letter}</h3>
                  </div>
                  {include file='catalog/_partials/miniatures/brand.tpl' brand=$brand}
                {/if}
                {$total_brands = $total_brands + 1}
              {/if}
            {/foreach}
          </div>
  
          <div class="row mt-5">
            <div class="col-12 text-center">
              {$total_brands} brands totali
            </div>
          </div>
  
        </div>
      {/block}
    </section>
  {/block}