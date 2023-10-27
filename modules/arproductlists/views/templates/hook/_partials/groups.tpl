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

<div class="arproductlists-config-panel {if !empty($activeTab)}hidden{/if}" id="arproductlists-groups">
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-list"></i> {l s='Groups & lists' mod='arproductlists'}
        </div>
        <div class="form-wrapper">
            <div class="row">
                <div class="col-sm-6" id="arpl-groups">
                    <h2 class="text-center">{l s='Groups' mod='arproductlists'}</h2>
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#arpl-home" id="arpl-home-tab" data-general="1" data-product-context="0" data-category-context="0" data-toggle="tab">{l s='Home page' mod='arproductlists'}</a>
                        </li>
                        <li class="">
                            <a href="#arpl-category" id="arpl-category-tab" data-general="1" data-product-context="0" data-category-context="1" data-toggle="tab">{l s='Category page' mod='arproductlists'}</a>
                        </li>
                        <li class="">
                            <a href="#arpl-product" id="arpl-product-tab" data-general="1" data-product-context="1" data-category-context="0" data-toggle="tab">{l s='Product page' mod='arproductlists'}</a>
                        </li>
                        <li class="">
                            <a href="#arpl-cart" id="arpl-cart-tab" data-general="1" data-product-context="0" data-category-context="0" data-toggle="tab">{l s='Checkout page' mod='arproductlists'}</a>
                        </li>
                        <li class="">
                            <a href="#arpl-404" id="arpl-product-tab" data-general="1" data-product-context="0" data-category-context="0" data-toggle="tab">{l s='404 page' mod='arproductlists'}</a>
                        </li>
                        <li class="">
                            <a href="#arpl-thank-you" id="arpl-product-tab" data-general="1" data-product-context="0" data-category-context="0" data-toggle="tab">{l s='"Thank you" page' mod='arproductlists'}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="arpl-home">
                            {foreach $homeGroups as $hook => $groups}
                                <div class="arpl-sub-section" id="arpl-sub-section-{$hook|escape:'htmlall':'UTF-8'}" data-hook="{$hook|escape:'htmlall':'UTF-8'}">
                                    <div class="sub-section-head">
                                        <span>{l s='Hook' mod='arproductlists'}:</span> {$hook|escape:'htmlall':'UTF-8'}
                                        <div class="arpl-sub-section-actions">
                                            <button class="arpl-add-group" type="button" data-hook="{$hook|escape:'htmlall':'UTF-8'}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H224V80c0-8.84-7.16-16-16-16h-32c-8.84 0-16 7.16-16 16v144H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h144v144c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V288h144c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z" class=""></path></svg>
                                                {l s='add group' mod='arproductlists'}
                                            </button>
                                        </div>
                                    </div>
                                    {include file="./_group_items.tpl"}
                                </div>
                            {/foreach}
                        </div>
                        <div class="tab-pane" id="arpl-category">
                            {foreach $categoryGroups as $hook => $groups}
                                <div class="arpl-sub-section" id="arpl-sub-section-{$hook|escape:'htmlall':'UTF-8'}" data-hook="{$hook|escape:'htmlall':'UTF-8'}">
                                    <div class="sub-section-head">
                                        <span>{l s='Hook' mod='arproductlists'}:</span> {$hook|escape:'htmlall':'UTF-8'}
                                        <div class="arpl-sub-section-actions">
                                            <button class="arpl-add-group" type="button" data-hook="{$hook|escape:'htmlall':'UTF-8'}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H224V80c0-8.84-7.16-16-16-16h-32c-8.84 0-16 7.16-16 16v144H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h144v144c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V288h144c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z" class=""></path></svg>
                                                {l s='add group' mod='arproductlists'}
                                            </button>
                                        </div>
                                    </div>
                                    {include file="./_group_items.tpl"}
                                </div>
                            {/foreach}
                        </div>
                        <div class="tab-pane" id="arpl-product">
                            {foreach $productGroups as $hook => $groups}
                                <div class="arpl-sub-section" id="arpl-sub-section-{$hook|escape:'htmlall':'UTF-8'}" data-hook="{$hook|escape:'htmlall':'UTF-8'}">
                                    <div class="sub-section-head">
                                        <span>{l s='Hook' mod='arproductlists'}:</span> {$hook|escape:'htmlall':'UTF-8'}
                                        <div class="arpl-sub-section-actions">
                                            <button class="arpl-add-group" type="button" data-hook="{$hook|escape:'htmlall':'UTF-8'}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H224V80c0-8.84-7.16-16-16-16h-32c-8.84 0-16 7.16-16 16v144H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h144v144c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V288h144c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z" class=""></path></svg>
                                                {l s='add group' mod='arproductlists'}
                                            </button>
                                        </div>
                                    </div>
                                    {include file="./_group_items.tpl"}
                                </div>
                            {/foreach}
                        </div>
                        <div class="tab-pane" id="arpl-cart">
                            {foreach $cartGroups as $hook => $groups}
                                <div class="arpl-sub-section" id="arpl-sub-section-{$hook|escape:'htmlall':'UTF-8'}" data-hook="{$hook|escape:'htmlall':'UTF-8'}">
                                    <div class="sub-section-head">
                                        <span>{l s='Hook' mod='arproductlists'}:</span> {$hook|escape:'htmlall':'UTF-8'}
                                        <div class="arpl-sub-section-actions">
                                            <button class="arpl-add-group" type="button" data-hook="{$hook|escape:'htmlall':'UTF-8'}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H224V80c0-8.84-7.16-16-16-16h-32c-8.84 0-16 7.16-16 16v144H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h144v144c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V288h144c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z" class=""></path></svg>
                                                {l s='add group' mod='arproductlists'}
                                            </button>
                                        </div>
                                    </div>
                                    {include file="./_group_items.tpl"}
                                </div>
                            {/foreach}
                        </div>
                        <div class="tab-pane" id="arpl-404">
                            {foreach $page404Groups as $hook => $groups}
                                <div class="arpl-sub-section" id="arpl-sub-section-{$hook|escape:'htmlall':'UTF-8'}" data-hook="{$hook|escape:'htmlall':'UTF-8'}">
                                    <div class="sub-section-head">
                                        <span>{l s='Hook' mod='arproductlists'}:</span> {$hook|escape:'htmlall':'UTF-8'}
                                        <div class="arpl-sub-section-actions">
                                            <button class="arpl-add-group" type="button" data-hook="{$hook|escape:'htmlall':'UTF-8'}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H224V80c0-8.84-7.16-16-16-16h-32c-8.84 0-16 7.16-16 16v144H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h144v144c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V288h144c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z" class=""></path></svg>
                                                {l s='add group' mod='arproductlists'}
                                            </button>
                                        </div>
                                    </div>
                                    {include file="./_group_items.tpl"}
                                </div>
                            {/foreach}
                        </div>
                        <div class="tab-pane" id="arpl-thank-you">
                            {foreach $thankYouGroups as $hook => $groups}
                                <div class="arpl-sub-section" id="arpl-sub-section-{$hook|escape:'htmlall':'UTF-8'}" data-hook="{$hook|escape:'htmlall':'UTF-8'}">
                                    <div class="sub-section-head">
                                        <span>{l s='Hook' mod='arproductlists'}:</span> {$hook|escape:'htmlall':'UTF-8'}
                                        <div class="arpl-sub-section-actions">
                                            <button class="arpl-add-group" type="button" data-hook="{$hook|escape:'htmlall':'UTF-8'}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H224V80c0-8.84-7.16-16-16-16h-32c-8.84 0-16 7.16-16 16v144H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h144v144c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V288h144c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z" class=""></path></svg>
                                                {l s='add group' mod='arproductlists'}
                                            </button>
                                        </div>
                                    </div>
                                    {include file="./_group_items.tpl"}
                                </div>
                            {/foreach}
                        </div>
                    </div>
                    
                </div>
                <div class="col-sm-6" id="arpl-lists">
                    <h2 class="text-center">{l s='General lists' mod='arproductlists'}</h2>
                    <div class="arpl-list-container" id="arpl-list-container">
                        {include file="./_list_items.tpl"}
                    </div>
                    
                    {if $productContextLists}
                        <hr/>
                        <h2 class="text-center">{l s='Product context lists' mod='arproductlists'}</h2>
                        <div class="arpl-list-container arpl-list-disabled" id="arpl-list-product-context-container">
                            {include file="./_list_items.tpl" lists=$productContextLists}
                        </div>
                    {/if}
                    
                    {if $categoryContextLists}
                        <hr/>
                        <h2 class="text-center">{l s='Category context lists' mod='arproductlists'}</h2>
                        <div class="arpl-list-container arpl-list-disabled" id="arpl-list-category-context-container">
                            {include file="./_list_items.tpl" lists=$categoryContextLists}
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>