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
{if $blockCategoryTree}
    {if !in_array($blockCategoryTree[0].id_category,$disabled_categories)}
    <li style="list-style: none;">
        <div class="checkbox {if $blockCategoryTree[0].children} has-child{/if}">
            <span>
                {if $displayInput}
                    <input class="category" name="{$name|escape:'html':'UTF-8'}[]" value="{$blockCategoryTree[0].id_category|intval}"{if in_array($blockCategoryTree[0].id_category,$selected_categories)} checked="checked"{/if}{if in_array($blockCategoryTree[0].id_category,$disabled_categories)} disabled="disabled"{/if}  type="checkbox" />
                {/if}
                <span class="label">{$blockCategoryTree[0].name|escape:'html':'UTF-8'}</span>
                {if !$backend}
                    <input class="default-category" value="{$blockCategoryTree[0].id_category|intval}" name="id_category_default" type="radio" {if in_array($blockCategoryTree[0].id_category,$disabled_categories)} disabled="disabled"{/if}{if in_array($blockCategoryTree[0].id_category,$selected_categories)} checked="checked"{/if} />
                {/if}
                {if !$displayInput}
                    (ID: {$blockCategoryTree[0].id_category|intval})
                {/if}
            </span>
        </div>
    {/if}
        {if $blockCategoryTree[0].children}
            {if !in_array($blockCategoryTree[0].id_category,$disabled_categories)}
  		        <ul class="children">
            {/if}
                {foreach from=$blockCategoryTree[0].children item=child name=blockCategoryTree}
                    {if $smarty.foreach.blockCategoryTree.last}
            			{include file="$branche_tpl_path_input" node=$child last='true'}
            		{else}
            			{include file="$branche_tpl_path_input" node=$child}
            		{/if}
                    
            	{/foreach}
            {if !in_array($blockCategoryTree[0].id_category,$disabled_categories)}
            </ul>
            {/if}
        {/if}
    {if !in_array($blockCategoryTree[0].id_category,$disabled_categories)}
    </li>
    {/if}
{/if}