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
{if isset($sellers) && $sellers}
    <div class="home-top-sellers">
        <h4 class="follow-title">{l s='Your followed shops' mod='ets_marketplace'}</h4>
    	<div id="page_home_followed_seller" class="list-sellers sellers ets_marketplace_product_list_wrapper slide">
        	{foreach from=$sellers item="seller"}
                  <div class="seller-miniature">
                        <div class="thumbnail-container">
                            <a class="thumbnail seller-thumbnail" href="{$seller.link|escape:'html':'UTF-8'}" tabindex="0">
                                <img style="width:250px" src="{$link->getMediaLink("`$smarty.const.__PS_BASE_URI__`img/mp_seller/`$seller.shop_logo|escape:'html':'UTF-8'`")}" alt="{$seller.shop_name|escape:'html':'UTF-8'}" />
                            </a>
                            <div class="seller-description">
                                <h3 class="h3 seller-name"><a href="{$seller.link|escape:'html':'UTF-8'}">{$seller.shop_name|escape:'html':'UTF-8'}</a></h3>
                                <div class="number-product">{$seller.total_product|intval} {if $seller.total_product>1}{l s='products' mod='ets_marketplace'}{else}{l s='product' mod='ets_marketplace'}{/if}</div>
                            </div>
                        </div>
                  </div>
            {/foreach}
        </div>
    </div>
{/if}