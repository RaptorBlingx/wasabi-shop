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
{if $delivery_option_list}
    <div class="ets-mp-delivery-options-list">
        <div class="from-group">
            {foreach $delivery_option_list key ='id_address' item='option'}
                {if $option}
                    <h4 class="delivery-option_label">{l s='Please select your preferred shipping method for following product(s):' mod='ets_marketplace'}</h4>
                    {foreach from=$option item='id_package' item='package'}
                        <div class="row delivery-option">
                            <label class="control-label">
                                <p class="shop_names">
                                    <i class="ets_svg_icon ets_svg_fill_gray">
                                    <svg class="w_14 h_14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M576 216v16c0 13.255-10.745 24-24 24h-8l-26.113 182.788C514.509 462.435 494.257 480 470.37 480H105.63c-23.887 0-44.139-17.565-47.518-41.212L32 256h-8c-13.255 0-24-10.745-24-24v-16c0-13.255 10.745-24 24-24h67.341l106.78-146.821c10.395-14.292 30.407-17.453 44.701-7.058 14.293 10.395 17.453 30.408 7.058 44.701L170.477 192h235.046L326.12 82.821c-10.395-14.292-7.234-34.306 7.059-44.701 14.291-10.395 34.306-7.235 44.701 7.058L484.659 192H552c13.255 0 24 10.745 24 24zM312 392V280c0-13.255-10.745-24-24-24s-24 10.745-24 24v112c0 13.255 10.745 24 24 24s24-10.745 24-24zm112 0V280c0-13.255-10.745-24-24-24s-24 10.745-24 24v112c0 13.255 10.745 24 24 24s24-10.745 24-24zm-224 0V280c0-13.255-10.745-24-24-24s-24 10.745-24 24v112c0 13.255 10.745 24 24 24s24-10.745 24-24z"/></svg>
                                    </i> {l s='Shop name:' mod='ets_marketplace'}&nbsp;{implode(' + ',$package.shop_names) nofilter}</p>
                                {if $package.product_list}
                                    <ul class="media-list">
                                        {foreach from=$package.product_list item='product'}
                                            <li class="media">
                                                <div class="media-left">
                                                    <img src="{$product.image|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
                                                </div>
                                                <div class="media-body">
                                                    <span class="product-name">{$product.name|escape:'html':'UTF-8'}</span><br />
                                                    <span class="product-price">{displayPrice price=$product.total}</span><br/>
                                                    {if isset($product.shop_name) && $product.shop_name}
                                                        <span class="shop-name">{l s='By' mod='ets_marketplace'}: {$product.shop_name|escape:'html':'UTF-8'}</span>
                                                    {/if}
                                                </div>
                                            </li>
                                            
                                        {/foreach}
                                    </ul>
                                {/if}
                                {if $package.carrier_list}
                                    <select class="form-control ets-carriers-selected">
                                        {foreach from=$package.carrier_list item='carrier'}
                                            <option value="{$carrier.id_carrier|intval}" {if $carrier.selected} selected="selected"{/if}>{$carrier.name|escape:'html':'UTF-8'}{if $carrier.delay} - {$carrier.delay|escape:'html':'UTF-8'}{/if} ({if $carrier.price_with_tax!=0}{displayPrice price =$carrier.price_with_tax}{else}{l s='Free' mod='ets_marketplace'}{/if})</option>
                                        {/foreach}
                                    </select>
                                {/if}
                            </label>
                        </div>
                    {/foreach}
                {/if}
            {/foreach}
        </div>
    </div>
{/if}