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
{if $ets_mp_tag}
<{$ets_mp_tag|escape:'html':'UTF-8'}
    {if $ets_mp_class} class="{$ets_mp_class|escape:'html':'UTF-8'}"{/if}
    {if $ets_mp_id} id="{$ets_mp_id|escape:'html':'UTF-8'}"{/if}
    {if $ets_mp_rel} rel="{$ets_mp_rel|escape:'html':'UTF-8'}"{/if}
    {if $ets_mp_type} type="{$ets_mp_type|escape:'html':'UTF-8'}"{/if}
    {if $ets_mp_data_id_product} data-id_product="{$ets_mp_data_id_product|escape:'html':'UTF-8'}"{/if}
    {if $ets_mp_value} value="{$ets_mp_value|escape:'html':'UTF-8'}"{/if}
    {if $ets_mp_href} href="{$ets_mp_href nofilter}"{/if}{if $ets_mp_tag=='a' && $ets_mp_blank} target="_blank"{/if}
    {if $ets_mp_tag=='img' && $ets_mp_src} src="{$ets_mp_src nofilter}"{/if}
    {if $ets_mp_name} name="{$ets_mp_name|escape:'html':'UTF-8'}"{/if}
    {if $ets_mp_attr_datas}
        {foreach from=$ets_mp_attr_datas item='data'}
            {$data.name|escape:'html':'UTF-8'}="{$data.value|escape:'html':'UTF-8'}"
        {/foreach}
    {/if}
    {if $ets_mp_tag=='img' || $ets_mp_tag=='br' || $ets_mp_tag=='input'} /{/if}
    
>
    {/if}{if $ets_mp_tag && $ets_mp_tag!='img' && $ets_mp_tag!='input' && $ets_mp_tag!='br' && !is_null($ets_mp_content)}{$ets_mp_content nofilter}{/if}{if $ets_mp_tag && $ets_mp_tag!='img' && $ets_mp_tag!='input' && $ets_mp_tag!='br'}</{$ets_mp_tag|escape:'html':'UTF-8'}>{/if}