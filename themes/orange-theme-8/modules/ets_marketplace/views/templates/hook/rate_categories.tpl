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
<script type="text/javascript">
{if $default_rate_categories}
    var default_rate_categories =[{$default_rate_categories|escape:'html':'UTF-8'}];
{else}
    var default_rate_categories = 'all';
{/if}
var rate_seller_groups= {literal}{}{/literal};
{if $rate_seller_groups}
    {foreach from =$rate_seller_groups key='id_group' item='rate_seller_group'}
        {if $rate_seller_group}
            rate_seller_groups[{$id_group|intval}] =[{$rate_seller_group|escape:'html':'UTF-8'}];
        {else}
            rate_seller_groups[{$id_group|intval}] = 'all';
        {/if}
    {/foreach}
{/if}
</script>
<ul class="ets_mp_categories_rate">
<li class="ets_mp_categories_rate_title">
    <span>{l s='Categories' mod='ets_marketplace'}</span>
    <span>{l s='Commission rate' mod='ets_marketplace'}</span>
</li>
{if $blockCategoryTree}
    <li style="list-style: none;">
        <label for="rate_category_{$blockCategoryTree[0].id_category|intval}">{$blockCategoryTree[0].name|escape:'html':'UTF-8'}</label>
        <div class="checkbox {if $blockCategoryTree[0].children} has-child{/if}">
            <div class="input-group{if !$rate_categories || in_array($blockCategoryTree[0].id_category,$rate_categories)} not-hide{else} hide{/if}">
                <input id="rate_category_{$blockCategoryTree[0].id_category|intval}" name="rate_category[{$blockCategoryTree[0].id_category|intval}]"  type="text" value="{$blockCategoryTree[0].commission_rate|escape:'html':'UTF-8'}" />
                <span class="input-group-addon"> % </span>
            </div>
        </div>
    </li>
    {if $blockCategoryTree[0].children}
        {foreach from=$blockCategoryTree[0].children item=child name=blockCategoryTree}
            {if $smarty.foreach.blockCategoryTree.last}
    			{include file="$branche_tpl_path_input" node=$child last='true'}
    		{else}
    			{include file="$branche_tpl_path_input" node=$child}
    		{/if}
            
    	{/foreach}
    {/if}
{/if}
</ul>