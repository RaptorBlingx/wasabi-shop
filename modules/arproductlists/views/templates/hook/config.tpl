{*
* 2019 Areama
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@areama.net so we can send you a copy immediately.
*
*
*  @author Areama <contact@areama.net>
*  @copyright  2019 Areama
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Areama
*}

<link rel="stylesheet" href="{$moduleUrl|escape:'htmlall':'UTF-8'}views/css/admin.css" type="text/css" media="all" />
<script type="text/javascript" src="{$moduleUrl|escape:'htmlall':'UTF-8'}views/js/admin.js"></script>
<script type="text/javascript" src="{$moduleUrl|escape:'htmlall':'UTF-8'}views/js/ace/src-min/ace.js"></script>
<div class="row" id="arpl-config">
    {if $max_input_vars < 3000}
        <div class="alert alert-warning">
            {l s='Value of variable max_input_vars is too small! Please increase it to 5000.' mod='arproductlists'}
            {l s='Current value is' mod='arproductlists'} {$max_input_vars|intval}
        </div>
    {/if}
    <div class="col-lg-2 col-md-3">
        <div class="list-group arplTabs">
            <a class="list-group-item {if empty($activeTab)}active{/if}" data-tab="1" data-target="arproductlists-groups" href="#">
                <i class="icon-list"></i> {l s='Groups & lists' mod='arproductlists'}
            </a>
            <a class="list-group-item {if $activeTab == 'ArPLGeneralConfig'}active{/if}" data-target="arproductlists-general" href="#">
                <i class="icon-cog"></i> {l s='General settings' mod='arproductlists'}
            </a>
            <a class="list-group-item {if $activeTab == 'ArPLSliderConfig'}active{/if}" data-target="arproductlists-slider" href="#">
                <i class="icon-cog"></i> {l s='Slider settings' mod='arproductlists'}
            </a>
            <a class="list-group-item {if $activeTab == 'ArPLPromoConfig'}active{/if}" data-target="arproductlists-promo" href="#">
                <i class="icon-cog"></i> {l s='Promo section settings' mod='arproductlists'}
            </a>
            <a class="list-group-item {if $activeTab == 'ArPLTabsConfig'}active{/if}" data-target="arproductlists-tabs" href="#">
                <i class="icon-cog"></i> {l s='Tabbed view settings' mod='arproductlists'}
            </a>
            <a class="list-group-item {if $activeTab == 'ArPLSectionConfig'}active{/if}" data-target="arproductlists-section" href="#">
                <i class="icon-cog"></i> {l s='Section view settings' mod='arproductlists'}
            </a>
            <a class="list-group-item {if $activeTab == 'ArPLRelCatConfig'}active{/if}" data-target="arproductlists-relcat" href="#">
                <i class="icon-cog"></i> {l s='Related categories' mod='arproductlists'}
            </a>
            <a class="list-group-item {if $activeTab == 'ArPLRulesConfig'}active{/if}" data-target="arproductlists-rules" href="#">
                <i class="icon-cog"></i> {l s='Related products rules' mod='arproductlists'}
            </a>
            <a class="list-group-item" data-tab="10" data-target="arproductlists-about" href="#">
                <i class="icon-info"></i> {l s='About' mod='arproductlists'}
            </a>
        </div>
    </div>
    <div class="col-lg-10 col-md-9" id="arproductlists-config-tabs">
        {include file="./_partials/general.tpl"}
        {include file="./_partials/slider.tpl"}
        {include file="./_partials/groups.tpl"}
        {include file="./_partials/promo.tpl"}
        {include file="./_partials/tabs.tpl"}
        {include file="./_partials/section.tpl"}
        {include file="./_partials/relcat.tpl"}
        {include file="./_partials/rules.tpl"}
        {include file="./_partials/about.tpl"}
    </div>
    {include file="./_partials/_group_modal.tpl"}
    {include file="./_partials/_list_modal.tpl"}
</div>
<script type="text/javascript">
    window.addEventListener('load', function(){
        arPL.group.ajaxUrl = '{$ajaxUrl.group nofilter}';
        arPL.list.ajaxUrl = '{$ajaxUrl.list nofilter}';
        arPL.relCat.ajaxUrl = '{$ajaxUrl.relCat nofilter}';
        arPL.rules.ajaxUrl = '{$ajaxUrl.rules nofilter}';
        $('#arpl-categories').tree('expandAll');
    });
</script>