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
<div class="panel ets_mp-panel">
    <div class="ets_mp_close_popup" title="{l s='Close' mod='ets_marketplace'}">{l s='Close' mod='ets_marketplace'}</div>
    <div class="panel-heading">
        {l s='List of changed fields' mod='ets_marketplace'} - {l s='product name' mod='ets_marketplace'}: {$product_name|escape:'html':'UTF-8'}
    </div>
    <div class="table-responsive clearfix ets_mp_changeproduct">
        {if $items}
            <form method="post" action="">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{l s='Field name' mod='ets_marketplace'}</th>
                            <th>{l s='Language' mod='ets_marketplace'}</th>
                            <th>{l s='Old value' mod='ets_marketplace'}</th>
                            <th>{l s='New value' mod='ets_marketplace'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$items item='item'}
                            {if !is_array($item.old_values)}
                                <tr>
                                    <td class="text-center">
                                        {$item.name|escape:'html':'UTF-8'}
                                    </td>
                                    <td>--</td>
                                    <td>
                                        {$item.old_values nofilter}
                                    </td>
                                    <td>
                                        {$item.new_values nofilter}
                                    </td>
                                </tr>
                            {else}
                                {if count($item.old_values)>1}
                                    {foreach from=$item.old_values key='key' item='val'}
                                        <tr>
                                            {if $key==0}
                                                <td rowspan="{count($item.old_values)|intval}" class="text-center">{$item.name|escape:'html':'UTF-8'}</td>
                                            {/if}
                                            <td>{$item['languages'][$key]|escape:'html':'UTF-8'}</td>
                                            <td>{$item['old_values'][$key] nofilter}</td>
                                            <td>{$item['new_values'][$key] nofilter}</td>
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td class="text-center">{$item.name|escape:'html':'UTF-8'}</td>
                                        <td>{$item.languages.0|escape:'html':'UTF-8'}</td>
                                        <td>{$item.old_values.0 nofilter}</td>
                                        <td>{$item.new_values.0 nofilter}</td>
                                    </tr>
                                {/if}
                            {/if}
                        {/foreach}
                    </tbody>
                </table>
                <div class="panel-footer">
                    <input type="hidden" value="1" name="etsmpSubmitApproveChanged" />
                    <input name="id_product" value="{$id_product|intval}" type="hidden" />
                    <a class="btn btn-default btn-close-popup" href="{$link->getAdminLink('AdminMarketPlaceProducts')|escape:'html':'UTF-8'}">
                        <i class="process-icon-cancel"></i>
                        {l s='Cancel' mod='ets_marketplace'}
                    </a>
                    <button id="module_form_submit_btn" class="btn btn-default pull-right btn-approve-change" type="button" value="1" name="saveConfig" data-id_product="{$id_product|intval}">
                        <i class="process-icon-check ets_svg_process">
                            <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"/></svg>
                        </i>
                        {l s='Approve' mod='ets_marketplace'}
                    </button>
                    {if !$declined}
                    <button class="btn btn-default pull-right btn-decline-change-product">
                        <i class="process-icon-cancel ets_svg_process">
                            <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1440 893q0-161-87-295l-754 753q137 89 297 89 111 0 211.5-43.5t173.5-116.5 116-174.5 43-212.5zm-999 299l755-754q-135-91-300-91-148 0-273 73t-198 199-73 274q0 162 89 299zm1223-299q0 157-61 300t-163.5 246-245 164-298.5 61-298.5-61-245-164-163.5-246-61-300 61-299.5 163.5-245.5 245-164 298.5-61 298.5 61 245 164 163.5 245.5 61 299.5z"/></svg>

                        </i>
                        {l s='Decline' mod='ets_marketplace'}
                    </button>
                    {/if}
                </div>
            </form>
            {if !$declined}
                <div class="ets_mp_popup_child ets_mp_decline_product" style="display:none">
                    <div class="mp_pop_table_child ets_table">
                        <div class="ets_table-cell">
                            <form method="post" action="">
                                <div class="ets_mp_close_child" title="{l s='Close' mod='ets_marketplace'}">{l s='Close' mod='ets_marketplace'}</div>
                                <div class="form-wrapper">
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label">{l s='Status' mod='ets_marketplace'}:</label>
                                        <div class="col-lg-3">
                                            <span class="ets_pmn_status decline">{l s='Declined' mod='ets_marketplace'}</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label" for="reason_decline">{l s='Reason' mod='ets_marketplace'}</label>
                                        <div class="col-lg-9">
                                            <textarea class="" name="reason_decline" id="reason_decline"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <input name="id_product" value="{$id_product|intval}" type="hidden" />
                                    <input name="btnSubmitDeclineChangeProduct" type="hidden" value="1"/>
                                    <button class="btn btn-default btn-close-popup-child">
                                        <i class="process-icon-cancel"></i>
                                        {l s='Cancel' mod='ets_marketplace'}
                                    </button>
                                    <button class="btn btn-default pull-right" type="button" value="1" name="btnSubmitDeclineChangeProduct">
                                        <i class="process-icon-save"></i>
                                        {l s='Save' mod='ets_marketplace'}
                                    </button>
                                </div>
                            </form> 
                        </div>
                    </div>
                </div>
            {/if}
        {else}
            <div class="alert alert-warning">{l s='There is not any new change.' mod='ets_marketplace'}</div>
        {/if}
    </div>
</div>