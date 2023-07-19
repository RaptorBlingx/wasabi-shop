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
<div class="alert alert-info" style="margin-top: 10px;">
    {l s='You have ' mod='ets_marketplace'}&nbsp;{$commission_total_balance|escape:'html':'UTF-8'}&nbsp;{l s=' in your balance. It can be converted into voucher code.' mod='ets_marketplace'}&nbsp;<a href="{$link->getModuleLink('ets_marketplace','voucher')|escape:'html':'UTF-8'}">{l s='Convert now' mod='ets_marketplace'}</a>
</div>