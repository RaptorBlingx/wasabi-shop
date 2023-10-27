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

<div class="arpl-featured-categories arpl-section" id="arpl-section-{$group->id|intval}-{$model->id|intval}">
    {if $title}
        <h2 class="h2 arpl-title {if $model->getList()->titleAlign == 'left'} arpl-text-left {elseif $model->getList()->titleAlign == 'right'} arpl-text-right {else} arpl-text-center {/if} products-section-title text-uppercase">{$title|escape:'htmlall':'UTF-8'}</h2>
    {/if}
    <ul class="arpl-category-list-view" id="arpl-section-{$group->id|intval}-{$model->id|intval}">
        {foreach from=$categories item="category"}
            <li class="arpl-category" data-id="{$category->id|intval}" id="arpl-cat-{$category->id|intval}">
                <a href="{$link->getCategoryLink($category->id, $category->link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$category->name|escape:'htmlall':'UTF-8'}" class="arpl-cat-img">
                    <img width="{$imgWidth|intval}" height="{$imgHeight|intval}" src="{$link->getCatImageLink($category->link_rewrite, $category->id, $imgType)|escape:'html'}" alt="{$category->name|escape:'htmlall':'UTF-8'}" />
                </a>
                {if $model->getList()->cat_title}
                    <h5 class="arpl-cat-title">
                        <a href="{$link->getCategoryLink($category->id, $category->link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$category->name|escape:'htmlall':'UTF-8'}">{$category->name|truncate:25:'...'|escape:'htmlall':'UTF-8'}</a>
                    </h5>
                {/if}
                {if $model->getList()->cat_desc}
                    <p class="arpl-cat-desc">
                        {$category->description|truncate:255:'...':TRUE|strip_tags:'UTF-8'|escape:'htmlall':'UTF-8'}
                    </p>
                {/if}
                <div class="arpl-buttons">
                    <a class="btn btn-primary" href="{$link->getCategoryLink($category->id, $category->link_rewrite)|escape:'htmlall':'UTF-8'}" title="{l s='Show products' mod='arproductlists'}">
                        <span>{l s='Show products' mod='arproductlists'}</span>
                    </a>
                </div>
            </li>
        {/foreach}
    </ul>
</div>