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

<form id="ets_mp_manager_shop_form" action="" method="post" enctype="multipart/form-data">
    <div class="ets_mp_close_popup" title="{l s='Close' mod='ets_marketplace'}">{l s='Close' mod='ets_marketplace'}</div>
     <div id="fieldset_0" class="panel">
         <div class="panel-heading">
            <i class="icon-info-sign"></i>
            {if $id_ets_mp_seller_manager}{l s='Edit permission' mod='ets_marketplace'}{else}{l s='Add new permission' mod='ets_marketplace'}{/if}
         </div>
         <div class="form-wrapper">
            {$html_form nofilter}
         </div>
         <div class="panel-footer">
            <span class="btn btn-secondary ets_mp_cancel_popup" title="{l s='Cancel' mod='ets_marketplace'}">{l s='Cancel' mod='ets_marketplace'}</span>
            <input type="hidden" name="submitSaveManagerShop" value="1"/>
            <input type="hidden" name="id_ets_mp_seller_manager" value="{$id_ets_mp_seller_manager|intval}" />
            <button name="submitSaveManagerShop" type="submit" class="btn btn-primary form-control-submit float-xs-right">{l s='Save' mod='ets_marketplace'}</button>
         </div>
     </div>
</form>