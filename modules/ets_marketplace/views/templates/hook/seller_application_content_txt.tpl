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
{l s='Seller email' mod='ets_marketplace'}: {$seller_email|escape:'html':'UTF-8'}
{if $submit_fields}
    {foreach $submit_fields item='submit_field'}
        {if $submit_field!='seller_email' && isset($seller_fields[$submit_field]) && isset($submit_values[$submit_field])}
            {$seller_fields[$submit_field]|escape:'html':'UTF-8'}: {$submit_values[$submit_field]|escape:'html':'UTF-8'}
        {/if}
    {/foreach}
{/if}