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
<label class="form-control-label col-lg-3 required">
    {l s='Product' mod='ets_marketplace'}
</label>
<div class="col-lg-9">
	<div class="input-group col-lg-12">
		<input id="reduction_product" name="reduction_product" value="{if isset($valueFieldPost.product)}{$valueFieldPost.product->id|intval}{/if}" type="hidden"  />
		{if isset($valueFieldPost.product) && $valueFieldPost.product}
            <div class="product_selected">{$valueFieldPost.product->name|escape:'html':'UTF-8'} <span class="delete_product_search">{l s='Delete' mod='ets_marketplace'}</span><div></div></div>
        {/if}
        <input id="productFilter" class="input-xlarge ac_input" name="productFilter" value="" autocomplete="off" type="text" placeholder="{l s='Type product name here' mod='ets_marketplace'}" />
        <div class="input-group-append">
            <span class="input-group-text">
                <i class="fa-search"></i>
            </span>    
        </div>
        <span class="help-block"> {l s='Each discount code is only able to apply for one product' mod='ets_marketplace'} </span>
	</div>
</div>