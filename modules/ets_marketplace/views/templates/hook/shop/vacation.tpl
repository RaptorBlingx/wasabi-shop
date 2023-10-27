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
<div class="panel">
    <div class="panel-heading">{l s='Vacation mode' mod='ets_marketplace'}</div>
    <section>
        <form id="vacation-form" action="{$link->getModuleLink('ets_marketplace','vacation')|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data">
            <section>
                <div class="form-group row">
                    <label class="col-md-3 form-control-label"> {l s='Enable vacation mode' mod='ets_marketplace'} </label>
                    <div class="col-md-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                			<input name="vacation_mode" id="vacation_mode_on" value="1" {if isset($smarty.post.vacation_mode)}{if $smarty.post.vacation_mode} checked="checked"{/if}{else}{if $seller->vacation_mode==1} checked="checked"{/if}{/if} type="radio" />
                			<label for="vacation_mode_on" class="radioCheck">
                				<i class="color_success"></i> {l s='Yes' mod='ets_marketplace'}
                			</label>
                			<input name="vacation_mode" id="vacation_mode_off" value="0" {if isset($smarty.post.vacation_mode)}{if !$smarty.post.vacation_mode} checked="checked"{/if}{else}{if $seller->vacation_mode==0} checked="checked"{/if}{/if} type="radio" />
                			<label for="vacation_mode_off" class="radioCheck">
                				<i class="color_danger"></i> {l s='No' mod='ets_marketplace'}
                			</label>
                			<a class="slide-button btn"></a>
                		</span>
                    </div>
                </div>
                <div class="form-group row enable_vacation_mode">
                    <label class="col-md-3 form-control-label"> {l s='Start date' mod='ets_marketplace'} </label>
                    <div class="col-md-9">
                        <div class="input-group ets_mp_datepicker">
                            <input class="form-control ets-mp-datetimepicker" readonly="true" name="date_vacation_start" value="{if isset($smarty.post.date_vacation_start)}{$smarty.post.date_vacation_start|escape:'html':'UTF-8'} {else}{$seller->date_vacation_start|escape:'html':'UTF-8'}{/if}" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="icon icon-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row enable_vacation_mode">
                    <label class="col-md-3 form-control-label"> {l s='End date' mod='ets_marketplace'} </label>
                    <div class="col-md-9">
                        <div class="input-group ets_mp_datepicker">
                            <input class="form-control ets-mp-datetimepicker" readonly="true" name="date_vacation_end" value="{if isset($smarty.post.date_vacation_end)}{$smarty.post.date_vacation_end|escape:'html':'UTF-8'} {else}{$seller->date_vacation_end|escape:'html':'UTF-8'}{/if}" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="icon icon-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row enable_vacation_mode">
                    <label class="col-md-3 form-control-label"> {l s='Vacation mode' mod='ets_marketplace'} </label>
                    <div class="col-md-9">
                        <select name="vacation_type" id="vacation_type" class="form-control">
                            <option value="show_notifications" {if isset($smarty.post.vacation_type)}{if $smarty.post.vacation_type=='show_notifications'} selected="selected"{/if}{else}{if $seller->vacation_type=='show_notifications'} selected="selected"{/if}{/if}>{l s='Show notifications' mod='ets_marketplace'}</option>
                            <option value="disable_product" {if isset($smarty.post.vacation_type)}{if $smarty.post.vacation_type=='disable_product'} selected="selected"{/if}{else}{if $seller->vacation_type=='disable_product'} selected="selected"{/if}{/if}>{l s='Disable products' mod='ets_marketplace'}</option>
                            <option value="disable_product_and_show_notifications" {if isset($smarty.post.vacation_type)}{if $smarty.post.vacation_type=='disable_product_and_show_notifications'} selected="selected"{/if}{else}{if $seller->vacation_type=='disable_product_and_show_notifications'} selected="selected"{/if}{/if}>{l s='Disable products and show notifications' mod='ets_marketplace'}</option>
                            <option value="disable_shopping" {if isset($smarty.post.vacation_type)}{if $smarty.post.vacation_type=='disable_shopping'} selected="selected"{/if}{else}{if $seller->vacation_type=='disable_shopping'} selected="selected"{/if}{/if}>{l s='Disable shopping feature' mod='ets_marketplace'}</option>
                            <option value="disable_shopping_and_show_notifications" {if isset($smarty.post.vacation_type)}{if $smarty.post.vacation_type=='disable_shopping_and_show_notifications'} selected="selected"{/if}{else}{if $seller->vacation_type=='disable_shopping_and_show_notifications'} selected="selected"{/if}{/if}>{l s='Disable shopping feature and show notifications' mod='ets_marketplace'}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row enable_vacation_mode show_notifications">
                    <label class="col-md-3 form-control-label required"> {l s='Notification' mod='ets_marketplace'} </label>
                    <div class="col-md-9">
                        {if $languages && count($languages)>1}
                            <div class="form-group">
                                {foreach from=$languages item='language'}
                                    <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang!=$id_lang_default} style="display:none;"{/if}>
                                        <div class="col-lg-10">
                                            {if isset($valueFieldPost)}
                                                {assign var='value_text' value=$valueFieldPost['vacation_notifications'][$language.id_lang]}
                                            {/if}
                                            <textarea class="form-control" name="vacation_notifications_{$language.id_lang|intval}">{if isset($value_text)}{$value_text|escape:'html':'UTF-8'}{elseif !isset($smarty.post.vacation_type)}{l s='This seller is currently on vacation. Add the products to your shopping cart and purchase when the seller is back.' mod='ets_marketplace'}{/if}</textarea>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="toggle_form">
                                            <button class="btn btn-default dropdown-toggle" type="button" tabindex="-1" data-toggle="dropdown">
                                            {$language.iso_code|escape:'html':'UTF-8'}
                                            <i class="icon-caret-down"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                {foreach from=$languages item='lang'}
                                                    <li>
                                                        <a class="hideOtherLanguage" href="#" tabindex="-1" data-id-lang="{$lang.id_lang|intval}">{$lang.name|escape:'html':'UTF-8'}</a>
                                                    </li>
                                                {/foreach}
                                            </ul>
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        {else}
                            {if isset($valueFieldPost)}
                                {assign var='value_text' value=$valueFieldPost['vacation_notifications'][$id_lang_default]}
                            {/if}
                            <textarea class="form-control" name="vacation_notifications_{$id_lang_default|intval}">{if isset($value_text)}{$value_text|escape:'html':'UTF-8'}{elseif !isset($smarty.post.vacation_type)}{l s='This seller is currently on vacation. Add the products to your shopping cart and purchase when the seller is back.' mod='ets_marketplace'}{/if}</textarea>
                        {/if}
                    </div>
                </div>  
                <div class="form-group row">
                    <div class="col-md-3"> </div>
                    <div class="col-md-9">
                        <input name="submitSaveVacationSeller" value="1" type="hidden" />
                        <button class="btn btn-primary form-control-submit float-xs-right" type="submit">
                            <i class="icon icon-save"></i> {l s='Save' mod='ets_marketplace'}
                        </button>
                    </div>
                </div>
                <div class="clearfix"></div>
            </section>
        </form>
    </section>
</div>