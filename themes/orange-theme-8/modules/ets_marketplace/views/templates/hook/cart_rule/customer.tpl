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

<label class="control-label col-lg-3">
	<span  title="{l s='Optional: The cart rule will be available to everyone if you leave this field blank.' mod='ets_marketplace'}">
		{l s='Limit to a single customer' mod='ets_marketplace'}
	</span>
</label>
<div class="col-lg-9">
	<div class="input-group col-lg-12">
		<div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fa-user"></i>
            </span>
        </div>
		<input id="id_customer" name="id_customer" value="{if isset($valueFieldPost.customer)}{$valueFieldPost.customer->id|intval}{/if}" type="hidden" />
		<input id="customerFilter" class="input-xlarge ac_input" name="customerFilter" value="" autocomplete="off" type="text" />
		{if isset($valueFieldPost.customer) && $valueFieldPost.customer}
            <div class="customer_selected">{$valueFieldPost.customer->firstname|escape:'html':'UTF-8'}&nbsp;{$valueFieldPost.customer->lastname|escape:'html':'UTF-8'} ({$valueFieldPost.customer->email|escape:'html':'UTF-8'}) <span class="delete_customer_search">{l s='Delete' mod='ets_marketplace'}</span><div></div></div>
        {/if}
        <div class="input-group-append">
            <span class="input-group-text">
                <i class="fa-search"></i>
            </span>
        </div>
	</div>
</div>