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
{assign var='nbItemsPerLine' value=4}
{assign var='nbItemsPerLineTablet' value=3}
{assign var='nbItemsPerLineMobile' value=2}
<script type="text/javascript">
    var ets_mp_nbItemsPerLine ={$nbItemsPerLine|intval};
    var ets_mp_nbItemsPerLineTablet ={$nbItemsPerLineTablet|intval};
    var ets_mp_nbItemsPerLineMobile ={$nbItemsPerLineMobile|intval};
</script>
{if isset($products) && $products}
    <div class="page_seller_follow">
        <h4 class="follow-title">{l s='Trending products' mod='ets_marketplace'}</h4>
        {include file="$tpl_dir./product-list.tpl" class="product_list grid row products ets_marketplace_product_list_wrapper slide" id="product_page_seller_follow"}
    </div>
{/if}
