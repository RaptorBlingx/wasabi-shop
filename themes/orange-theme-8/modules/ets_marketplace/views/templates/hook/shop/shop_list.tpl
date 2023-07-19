{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
{if $sellers}
    {foreach from=$sellers item='seller'}
        <li class="seller-miniature ">
            <div class="thumbnail-container rounded shadow-none border">
                {if $seller.shop_logo}
                    <a class="thumbnail seller-thumbnail" href="{$seller.link|escape:'html':'UTF-8'}" tabindex="0">
                        <img class="w-100 text-center mx-auto p-3" style="max-width:250px" src="{$link->getMediaLink("`$smarty.const.__PS_BASE_URI__`img/mp_seller/`$seller.shop_logo|escape:'html':'UTF-8'`")}" alt="{$seller.shop_name|escape:'html':'UTF-8'}" />
                    </a>
                {/if}
                <div class="seller-description border-0 shadow-none">
                    <h3 class="h3 seller-name"><a href="{$seller.link|escape:'html':'UTF-8'}">{$seller.shop_name|escape:'html':'UTF-8'}</a></h3>
                    <div class="product">
                        <div class="number-product">
                            {$seller.total_product|intval} {if $seller.total_product>1}{l s='products' mod='ets_marketplace'}{else}{l s='product' mod='ets_marketplace'}{/if}
                        </div>
                        {if isset($seller.avg_rate) && $seller.avg_rate}
                            <div class="ets_review">
                                    {for $foo=1 to $seller.avg_rate_int}
                                        <span class="ets_star fa fa-star"></span>
                                    {/for}
                                    {if $seller.avg_rate_int < $seller.avg_rate}
                                        <span class="ets_star fa fa-star-half-o"></span>
                                        {for $foo= $seller.avg_rate_int+2 to 5}
                                            <span class="ets_star fa fa-star-o"></span>
                                        {/for}
                                    {else}
                                        {for $foo= $seller.avg_rate_int+1 to 5}
                                            <span class="ets_star fa fa-star-o"></span>
                                        {/for}
                                    {/if}
                                    <span class="total_review">({$seller.count_grade|intval})</span>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </li>
    {/foreach}
{else}
    <div class="alert alert-warning">{l s='No shops available' mod='ets_marketplace'}</div>
{/if}