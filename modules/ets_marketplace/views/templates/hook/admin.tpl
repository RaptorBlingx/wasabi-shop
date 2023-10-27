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
<script>
    var confim_delete_logo = '{l s='Do you want to delete this logo' mod='ets_marketplace' js=1}';
    var Close_text = '{l s='Close' mod='ets_marketplace' js=1}';
    var Status_text = '{l s='Status' mod='ets_marketplace' js=1}';
    var Declined_text ='{l s='Declined' mod='ets_marketplace' js=1}';
    var Reason_text ='{l s='Reason' mod='ets_marketplace' js=1}';
    var Cancel_text ='{l s='Cancel' mod='ets_marketplace' js=1}';
    var Save_text = '{l s='Save' mod='ets_marketplace' js=1}';
</script>
<script type="text/javascript" src="{$ets_mp_module_dir|escape:'html':'UTF-8'}views/js/admin.js"></script>
<script type="text/javascript">
    {if isset($ets_link_search_seller)}
        var ets_link_search_seller ='{$ets_link_search_seller nofilter}';
    {/if}
</script>
{$ets_mp_sidebar nofilter}
<div class="etsmp-left-panel col-lg-12">
    {if isset($smarty.get.controller) && $smarty.get.controller!='AdminMarketPlaceDashboard'}
    <nav  class="breadcrumb hidden-sm-down">
      <ol>
          <li>
            <a href="{$link->getAdminLink('AdminMarketPlaceDashboard')|escape:'html':'UTF-8'}">
                <span ><i class="icon fa-home"></i></span>
            </a>
          </li>
          {if $smarty.get.controller=='AdminMarketPlacePayments' || $smarty.get.controller=='AdminMarketPlaceCommissionsUsage' || $smarty.get.controller=='AdminMarketPlaceSettingsGeneral' || $smarty.get.controller=='AdminMarketPlaceCronJob'}
              <li>
                <span >{l s='Setting' mod='ets_marketplace'}</span>
              </li>
          {/if}
          {if $smarty.get.controller=='AdminMarketPlaceReport' || $smarty.get.controller=='AdminMarketPlaceShopGroups' || $smarty.get.controller=='AdminMarketPlaceCategory'}
              <li>
                <span >{l s='Shops' mod='ets_marketplace'}</span>
              </li>  
          {/if}
          <li>
                {if $smarty.get.controller=='AdminMarketPlacePayments'}
                    <span >{l s='Payment method' mod='ets_marketplace'}</span>
                {elseif $smarty.get.controller=='AdminMarketPlaceCommissionsUsage'}
                    <span >{l s='Commission' mod='ets_marketplace'}</span>
                {elseif $smarty.get.controller=='AdminMarketPlaceCronJob'}
                    <span >{l s='Cronjob' mod='ets_marketplace'}</span>
                {elseif $smarty.get.controller=='AdminMarketPlaceOrders'}
                    <span >{l s='Orders' mod='ets_marketplace'}</span>
                {elseif $smarty.get.controller=='AdminMarketPlaceProducts'}
                    <span >{if isset($product_class)}<a href="{$link->getAdminLink('AdminMarketPlaceProducts')|escape:'html':'UTF-8'}">{/if}{l s='Products' mod='ets_marketplace'}{if isset($product_class)}</a>{/if}</span>
                {elseif $smarty.get.controller=='AdminMarketPlaceCommissions'}
                    <span >{l s='Commissions' mod='ets_marketplace'}</span>
                {elseif $smarty.get.controller=='AdminMarketPlaceBillings'}
                    <span >{l s='Membership' mod='ets_marketplace'}</span>
                {elseif $smarty.get.controller=='AdminMarketPlaceWithdrawals'}
                    <span >{l s='Withdrawals' mod='ets_marketplace'}</span>
                {elseif $smarty.get.controller=='AdminMarketPlaceRegistrations'}
                    <span >{l s='Applications' mod='ets_marketplace'}</span>
                {elseif $smarty.get.controller=='AdminMarketPlaceSellers'}
                    <span >{l s='Shops' mod='ets_marketplace'}</span>
                {elseif $smarty.get.controller=='AdminMarketPlaceReport'}
                    <span >{l s='Reports' mod='ets_marketplace'}</span>
                {elseif $smarty.get.controller=='AdminMarketPlaceShopGroups'}
                    <span >{l s='Shop groups' mod='ets_marketplace'}</span>
                {elseif $smarty.get.controller=='AdminMarketPlaceRatings'}
                    <span >{l s='Ratings' mod='ets_marketplace'}</span>
                {elseif $smarty.get.controller=='AdminMarketPlaceCategory'}
                    <span>{l s='Shop categories' mod='ets_marketplace'}</span>
                {else}
                    <span >{l s='General' mod='ets_marketplace'}</span>
                {/if}
          </li>
          {if $smarty.get.controller=='AdminMarketPlaceProducts' && isset($product_class)}
                <li>{$product_class->name|escape:'html':'UTF-8'}</li>
          {/if}
      </ol>
    </nav>
    {/if}
{if isset($tabActive)}
        <div class="etsws-panel">
            <style>
                #commission_usage_form .panel-heading{
                    display:none;
                }
            </style>
            <div class="title-content">
                <h1>{l s='Commission' mod='ets_marketplace'} </h1>
            </div>
            <div class="ets-ws-admin__subtabs">
                    <ul class="subtab-list">
                        <li class="{if $tabActive=='commission_rate'}active{/if}">
                            <a href="{$link->getAdminLink('AdminMarketPlaceCommissionsUsage')|escape:'html':'UTF-8'}&tabActive=commission_rate" title="">
                                <i class="ets_svg_icon ets_svg_fill_bluegray ets_svg_fill_hover_blue">
                                    <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1362 1185q0 153-99.5 263.5t-258.5 136.5v175q0 14-9 23t-23 9h-135q-13 0-22.5-9.5t-9.5-22.5v-175q-66-9-127.5-31t-101.5-44.5-74-48-46.5-37.5-17.5-18q-17-21-2-41l103-135q7-10 23-12 15-2 24 9l2 2q113 99 243 125 37 8 74 8 81 0 142.5-43t61.5-122q0-28-15-53t-33.5-42-58.5-37.5-66-32-80-32.5q-39-16-61.5-25t-61.5-26.5-62.5-31-56.5-35.5-53.5-42.5-43.5-49-35.5-58-21-66.5-8.5-78q0-138 98-242t255-134v-180q0-13 9.5-22.5t22.5-9.5h135q14 0 23 9t9 23v176q57 6 110.5 23t87 33.5 63.5 37.5 39 29 15 14q17 18 5 38l-81 146q-8 15-23 16-14 3-27-7-3-3-14.5-12t-39-26.5-58.5-32-74.5-26-85.5-11.5q-95 0-155 43t-60 111q0 26 8.5 48t29.5 41.5 39.5 33 56 31 60.5 27 70 27.5q53 20 81 31.5t76 35 75.5 42.5 62 50 53 63.5 31.5 76.5 13 94z"/></svg>
                                </i>
                                {l s='Commission rate' mod='ets_marketplace'}
                            </a>
                        </li>
                        <li class="{if $tabActive=='commission_usage'}active{/if}">
                            <a href="{$link->getAdminLink('AdminMarketPlaceCommissionsUsage')|escape:'html':'UTF-8'}&tabActive=commission_usage" title="">
                                <i class="ets_svg_icon ets_svg_fill_bluegray ets_svg_fill_hover_blue">
                                    <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg>
                                </i>
                                {l s='Usage settings' mod='ets_marketplace'}
                            </a>
                        </li>
                        <li class="{if $tabActive=='payment_settings'}active{/if}">
                            <a href="{$link->getAdminLink('AdminMarketPlaceCommissionsUsage')|escape:'html':'UTF-8'}&tabActive=payment_settings" title="">
                                <i class="ets_svg_icon ets_svg_fill_bluegray ets_svg_fill_hover_blue">
                                    <svg class="w_16 h_14" width="16" height="14" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1824 128q66 0 113 47t47 113v1216q0 66-47 113t-113 47h-1600q-66 0-113-47t-47-113v-1216q0-66 47-113t113-47h1600zm-1600 128q-13 0-22.5 9.5t-9.5 22.5v224h1664v-224q0-13-9.5-22.5t-22.5-9.5h-1600zm1600 1280q13 0 22.5-9.5t9.5-22.5v-608h-1664v608q0 13 9.5 22.5t22.5 9.5h1600zm-1504-128v-128h256v128h-256zm384 0v-128h384v128h-384z"/></svg>
                                </i>
                                {l s='Withdrawal methods' mod='ets_marketplace'}
                            </a>
                        </li>
                    </ul>
            </div>
        {/if}
{$ets_mp_body_html nofilter}
{if isset($tabActive)}
        </div>
    {/if}
</div>
<div class="clearfix"></div>