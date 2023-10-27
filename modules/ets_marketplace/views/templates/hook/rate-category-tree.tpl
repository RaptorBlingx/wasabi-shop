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

<li style="list-style: none;">
    <label for="rate_category_{$node.id_category|intval}">- {$node.name|escape:'html':'UTF-8'} </label>
    <div class="checkbox {if $node.children|@count > 0} has-child{/if}">
        <div class="input-group{if !$rate_categories || in_array($node.id_category,$rate_categories)} not-hide{else} hide{/if}">
            <input type="text" id="rate_category_{$node.id_category|intval}" name="rate_category[{$node.id_category|intval}]" value="{$node.commission_rate|escape:'html':'UTF-8'}"/>
            <span class="input-group-addon"> % </span>
        </div>
    </div>
</li>
{if $node.children|@count > 0} 
	{foreach from=$node.children item=child name=categoryTreeBranch}
        <li class="children">
            <ul>
                {if $smarty.foreach.categoryTreeBranch.last}
        			{include file="$branche_tpl_path_input" node=$child last='true'}
        		{else}
        			{include file="$branche_tpl_path_input" node=$child last='false'}
        		{/if}
            </ul>
        </li>
	{/foreach}
{/if} 
