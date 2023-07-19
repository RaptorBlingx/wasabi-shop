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
{if $products}
<div class="list-order-products">
    {foreach from = $products item='product'}
        {if isset($product['deleted']) && $product['deleted']}
            {$product.product_name|escape:'html':'UTF-8'}<br />
        {else}
            <a target="_blank" href="{$link->getProductLink($product.product_id,null,null,null,null,null,$product.product_attribute_id)|escape:'html':'UTF-8'}" title="{$product.product_name|escape:'html':'UTF-8'}">
                {if $product.image}
                    <img src="{$product.image|escape:'html':'UTF-8'}" alt="{$product.product_name|escape:'html':'UTF-8'}" style="width: 40px;"/>
                {else}
                    {$product.product_name|escape:'html':'UTF-8'}
                {/if}
            </a>
        {/if}
    {/foreach}
</div>
{/if}

