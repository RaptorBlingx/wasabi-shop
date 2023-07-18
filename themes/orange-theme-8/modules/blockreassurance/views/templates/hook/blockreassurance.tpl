{**
* Copyright since 2007 PrestaShop SA and Contributors
* PrestaShop is an International Registered Trademark & Property of PrestaShop SA
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.md.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* @author PrestaShop SA and Contributors <contact@prestashop.com>
    * @copyright Since 2007 PrestaShop SA and Contributors
    * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
    *}
    {if $elements}
    <section class="bg-primary py-4 section-customer-reassurance">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                {foreach from=$elements item=element}
                <div class="col-4 col-md-4 col-xl section-customer-reassurance-column py-2 text-center" >
                    <img src="{$element.image}" alt="{$element.text|escape:'quotes'}" class="d-inline-block">
                    <h4 class="d-block text-white">{$element.text}</h4>
                </div>
                {/foreach}
            </div>
        </div>
    </section>
    {/if}