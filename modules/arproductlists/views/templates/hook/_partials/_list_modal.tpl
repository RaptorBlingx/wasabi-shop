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

<div class="modal fade" id="arpl-list-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="modal-title" style="font-size: 18px;" id="myModalLabel">{l s='List options' mod='arproductlists'}</div>
            </div>
            <form class="form-horizontal form" id="arpl-list-form" onsubmit="arPL.list.save(); return false;">
                <input type="hidden" id="arpl-list-form_id" value="" data-default="">
                <input type="hidden" id="arpl-list-form_id_group" name="id_group" value="" data-serializable="true" data-default="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-10">
                            <div class="form-group list-class-group">
                                <label class="control-label col-sm-4">{l s='Product list type' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="class" id="arpl-list-form_class" data-serializable="true" data-default="ArPLHomeFeatured">
                                        <option value="ArPLHomeFeatured">{l s='Home featured products' mod='arproductlists'}</option>
                                        <option value="ArPLCategory">{l s='Category' mod='arproductlists'}</option>
                                        <option value="ArPLViewedProducts">{l s='Viewed products' mod='arproductlists'}</option>
                                        <option value="ArPLBrandProducts">{l s='Brand products' mod='arproductlists'}</option>
                                        <option value="ArPLSupplierProducts">{l s='Supplier products' mod='arproductlists'}</option>
                                        <option value="ArPLPriceDrop">{l s='Price drop' mod='arproductlists'}</option>
                                        <option value="ArPLBestSellers">{l s='Best sellers' mod='arproductlists'}</option>
                                        <option value="ArPLNewProducts">{l s='New products' mod='arproductlists'}</option>
                                        <option value="ArPLCustomProducts">{l s='Custom products' mod='arproductlists'}</option>
                                        <option value="ArPLPromotions">{l s='Promotions' mod='arproductlists'}</option>
                                        <option value="ArPLMostViewedProducts">{l s='Most viewed products' mod='arproductlists'}</option>
                                        <option value="ArPLMostWantedProducts">{l s='Most wanted products' mod='arproductlists'}</option>
                                        <option value="ArPLLastCartProducts">{l s='Last added to cart products' mod='arproductlists'}</option>
                                        <option value="ArPLMostBuyedProducts">{l s='Most buyed products' mod='arproductlists'}</option>
                                        <option value="ArPLLastBuyedProducts">{l s='Last buyed products' mod='arproductlists'}</option>
                                        <option class="product-context-opt hidden" value="ArPLSameCategoryProducts">{l s='Same category products' mod='arproductlists'}</option>
                                        <option class="product-context-opt hidden" value="ArPLSameBrandProducts">{l s='Same brand products' mod='arproductlists'}</option>
                                        <option class="product-context-opt hidden" value="ArPLSameReferenceProducts">{l s='Same reference products' mod='arproductlists'}</option>
                                        <option class="product-context-opt hidden" value="ArPLWithThisProductAlsoBuy">{l s='With this product also buy' mod='arproductlists'}</option>
                                        <option class="product-context-opt hidden" value="ArPLPromotionsWithProduct">{l s='Promotions with this product' mod='arproductlists'}</option>
                                        <option class="product-context-opt hidden" value="ArPLRelatedProducts">{l s='Related products' mod='arproductlists'}</option>
                                        <option class="product-context-opt hidden" value="ArPLRelatedCategories">{l s='Related categories' mod='arproductlists'}</option>
                                        <option class="product-context-opt hidden" value="ArPLRuleProducts">{l s='Rule based products' mod='arproductlists'}</option>
                                        <option class="product-context-opt hidden" value="ArPLProductRelatedProducts">{l s='Related products' mod='arproductlists'}</option>
                                        <option class="product-context-opt hidden" value="ArPLProductCategories">{l s='Product categories' mod='arproductlists'}</option>
                                        <option class="product-context-opt hidden" value="ArPLSameAttrProducts">{l s='Same attribute products' mod='arproductlists'}</option>
                                        <option class="product-context-opt hidden" value="ArPLSameFeatureProducts">{l s='Same feature products' mod='arproductlists'}</option>
                                        <option class="category-context-opt hidden" value="ArPLCategoryChildCategories">{l s='Category child categories' mod='arproductlists'}</option>
                                        <option class="category-context-opt hidden" value="ArPLCategoryRelatedCategories">{l s='Category related categories' mod='arproductlists'}</option>
                                        <option class="category-context-opt hidden" value="ArPLCategoryRelatedProducts">{l s='Related categories products' mod='arproductlists'}</option>
                                        <option class="category-context-opt hidden" value="ArPLSubcategoriesFeaturedProducts">{l s='Subcategories featured products' mod='arproductlists'}</option>
                                        <option class="category-context-opt hidden" value="ArPLSubcategoriesNewProducts">{l s='Subcategories new products' mod='arproductlists'}</option>
                                        <option class="category-context-opt hidden" value="ArPLSubcategoriesProducts">{l s='Subcategories products' mod='arproductlists'}</option>
                                        <option class="category-context-opt hidden" value="ArPLSubcategoriesBestSellers">{l s='Best sellers from subcategories' mod='arproductlists'}</option>
                                        <option class="category-context-opt hidden" value="ArPLCategoryBrands">{l s='Category brands' mod='arproductlists'}</option>
                                        <option value="ArPLCustomCategories">{l s='Custom categories' mod='arproductlists'}</option>
                                        <option value="ArPLCustomBrands">{l s='Custom brands' mod='arproductlists'}</option>
                                        <option value="ArPLChildCategories">{l s='Child categories' mod='arproductlists'}</option>
                                        <option value="ArPLViewedCategories">{l s='Viewed categories' mod='arproductlists'}</option>
                                        <option value="ArPLProductsIPurchased">{l s='Products I purchased' mod='arproductlists'}</option>
                                        <option value="ArPLMostWantedProductsBrands">{l s='Brands of most wanted products' mod='arproductlists'}</option>
                                        <option value="ArPLMostViewedProductsBrands">{l s='Brands of viewed products' mod='arproductlists'}</option>
                                    </select>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">{l s='Title' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    {foreach $languages as $language}
                                        <div class="translatable-field row lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                            <div class="col-lg-10">
                                                <input name="title_{$language.id_lang|escape:'htmlall':'UTF-8'}" id="arpl-list-form_title_{$language.id_lang|escape:'htmlall':'UTF-8'}" 
                                                       data-lang="{$language.id_lang|escape:'htmlall':'UTF-8'}" value="" data-serializable="true" data-default="" class="arpl-list-form_title arcontactus-control" type="text" />
                                            </div>
                                            <div class="col-lg-2">
                                                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                                            {$language.iso_code|escape:'htmlall':'UTF-8'}
                                                            <i class="icon-caret-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                            {foreach from=$languages item=language}
                                                            <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'htmlall':'UTF-8'});" data-lang="{$language.id_lang|intval}" tabindex="-1">{$language.name|escape:'htmlall':'UTF-8'}</a></li>
                                                            {/foreach}
                                                    </ul>
                                            </div>
                                        </div>
                                    {/foreach}
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group title-group">
                                <label class="control-label col-sm-4">{l s='Title align' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="list.titleAlign" id="arpl-list-form_list_titleAlign" data-serializable="true" data-default="center">
                                        <option value="left">{l s='Left' mod='arproductlists'}</option>
                                        <option value="right">{l s='Right' mod='arproductlists'}</option>
                                        <option value="center">{l s='Center' mod='arproductlists'}</option>
                                    </select>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="days-group">
                                <div class="form-group">
                                    <label class="control-label col-sm-4 required">{l s='Calculate data for X days' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <input type="text" data-serializable="true" class="form-control" name="list.days" data-default="1" id="arpl-list-form_list_days" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ajax-group">
                                <label class="control-label col-sm-4">{l s='AJAX loading' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="list.ajax" id="arpl-list-form_list_ajax_on" value="1" checked="checked">
                                        <label for="arpl-list-form_list_ajax_on">Yes</label>
                                        <input type="radio" name="list.ajax" id="arpl-list-form_list_ajax_off" value="0">
                                        <label for="arpl-list-form_list_ajax_off">No</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                    <p class="help-block">
                                        {l s='Applicable only on tabbed group view' mod='arproductlists'}
                                    </p>
                                </div>
                            </div>
                            <div class="form-group product-update-group">
                                <label class="control-label col-sm-4">{l s='Update list on product update' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="list.product_update" id="arpl-list-form_list_product_update_on" value="1" checked="checked">
                                        <label for="arpl-list-form_list_product_update_on">Yes</label>
                                        <input type="radio" name="list.product_update" id="arpl-list-form_list_product_update_off" value="0">
                                        <label for="arpl-list-form_list_product_update_off">No</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                    <p class="help-block">
                                        {l s='Refresh list if product updated (eg. product combination changed). Please use this option for product-sensetive lists only.' mod='arproductlists'}
                                    </p>
                                </div>
                            </div>
                            <div class="form-group cat-title-group">
                                <label class="control-label col-sm-4">{l s='Show title' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="list.cat_title" id="arpl-list-form_list_cat_title_on" value="1" checked="checked">
                                        <label for="arpl-list-form_list_cat_title_on">Yes</label>
                                        <input type="radio" name="list.cat_title" id="arpl-list-form_list_cat_title_off" value="0">
                                        <label for="arpl-list-form_list_cat_title_off">No</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group cat-desc-group">
                                <label class="control-label col-sm-4">{l s='Show description' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="list.cat_desc" id="arpl-list-form_list_cat_desc_on" value="1" checked="checked">
                                        <label for="arpl-list-form_list_cat_desc_on">Yes</label>
                                        <input type="radio" name="list.cat_desc" id="arpl-list-form_list_cat_desc_off" value="0">
                                        <label for="arpl-list-form_list_cat_desc_off">No</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group thumb-group">
                                <label class="control-label col-sm-4 required">{l s='Image thumbnail size' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="list.thumb_size" id="arpl-list-form_list_thumb_size" data-serializable="true" data-default="">
                                        {foreach $imgTypes as $type}
                                            <option value="{$type.id_image_type|intval}">{$type.name|escape:'htmlall':'UTF-8'} ({$type.width|intval}x{$type.height|intval})</option>
                                        {/foreach}
                                    </select>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group brand-thumb-group">
                                <label class="control-label col-sm-4 required">{l s='Image thumbnail size' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="list.brand_thumb_size" id="arpl-list-form_list_brand_thumb_size" data-serializable="true" data-default="">
                                        {foreach $brandImgTypes as $type}
                                            <option value="{$type.id_image_type|intval}">{$type.name|escape:'htmlall':'UTF-8'} ({$type.width|intval}x{$type.height|intval})</option>
                                        {/foreach}
                                    </select>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group owl-group">
                                <label class="control-label col-sm-4">{l s='View type' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <label>
                                        <input type="radio" name="list.view" id="arpl-list-form_list_view" value="1" checked="checked">
                                        <img src="{$path|escape:'htmlall':'UTF-8'}/views/img/slider.png" title="Slider" />
                                    </label>
                                    <br/>
                                    <label>
                                        <input type="radio" name="list.view" id="arpl-list-form_list_view" value="2">
                                        <img src="{$path|escape:'htmlall':'UTF-8'}/views/img/tiles.png" title="Standard list" />
                                    </label>
                                    <br/>
                                    <label>
                                        <input type="radio" name="list.view" id="arpl-list-form_list_view" value="3">
                                        <img src="{$path|escape:'htmlall':'UTF-8'}/views/img/list.png" title="Compact list" />
                                    </label>
                                </div>
                            </div>
                            <div class="slider-group">
                                <div class="form-group">
                                    <label class="control-label col-sm-4">{l s='Slide items' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <input type="text" data-serializable="true" class="form-control" name="list.slide_by" data-default="1" id="arpl-list-form_list_slide_by" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">{l s='Enable touch swipe' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="list.drag" id="arpl-list-form_list_drag_on" value="1" checked="checked">
                                            <label for="arpl-list-form_list_drag_on">Yes</label>
                                            <input type="radio" name="list.drag" id="arpl-list-form_list_drag_off" value="0">
                                            <label for="arpl-list-form_list_drag_off">No</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">{l s='Enable controls' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="list.controls" id="arpl-list-form_list_controls_on" value="1" checked="checked">
                                            <label for="arpl-list-form_list_controls_on">Yes</label>
                                            <input type="radio" name="list.controls" id="arpl-list-form_list_controls_off" value="0">
                                            <label for="arpl-list-form_list_controls_off">No</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">{l s='Enable dots' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="list.dots" id="arpl-list-form_list_dots_on" value="1" checked="checked">
                                            <label for="arpl-list-form_list_dots_on">Yes</label>
                                            <input type="radio" name="list.dots" id="arpl-list-form_list_dots_off" value="0">
                                            <label for="arpl-list-form_list_dots_off">No</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">{l s='Center' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="list.center" id="arpl-list-form_list_center_on" value="1" checked="checked">
                                            <label for="arpl-list-form_list_center_on">Yes</label>
                                            <input type="radio" name="list.center" id="arpl-list-form_list_center_off" value="0">
                                            <label for="arpl-list-form_list_center_off">No</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">{l s='Infinity loop' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="list.loop" id="arpl-list-form_list_loop_on" value="1" checked="checked">
                                            <label for="arpl-list-form_list_loop_on">Yes</label>
                                            <input type="radio" name="list.loop" id="arpl-list-form_list_loop_off" value="0">
                                            <label for="arpl-list-form_list_loop_off">No</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">{l s='Autoplay' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="list.autoplay" id="arpl-list-form_list_autoplay_on" value="1" checked="checked">
                                            <label for="arpl-list-form_list_autoplay_on">Yes</label>
                                            <input type="radio" name="list.autoplay" id="arpl-list-form_list_autoplay_off" value="0">
                                            <label for="arpl-list-form_list_autoplay_off">No</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">{l s='Autoplay timeout' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <input type="text" data-serializable="true" class="form-control" name="list.autoplayTimeout" data-default="3000" id="arpl-list-form_list_autoplayTimeout" />
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label col-sm-4">{l s='Responsive base element' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="list.responsiveBaseElement" id="arpl-list-form_list_responsiveBaseElement" data-serializable="true" data-default="parent">
                                            <option value="window">{l s='Window' mod='arproductlists'}</option>
                                            <option value="parent">{l s='Parent element' mod='arproductlists'}</option>
                                        </select>
                                        <div class="errors"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">{l s='Custom responsive breakdowns' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" name="list.responsiveBreakdowns" rows="6" id="arpl-list-form_list_responsiveBreakdowns" data-serializable="true" data-default=""></textarea>
                                        <div class="errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group arpl-attr-group">
                                <label class="control-label col-sm-4">{l s='Attribute' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="list.attribute_group" id="arpl-list-form_list_attribute_group" data-serializable="true" data-default="">
                                        {foreach $attributeGroups as $attrGroup}
                                            <option value="{$attrGroup.id_attribute_group|intval}">{$attrGroup.name|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group arpl-feature-group">
                                <label class="control-label col-sm-4">{l s='Feature' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="list.id_feature" id="arpl-list-form_list_id_feature" data-serializable="true" data-default="">
                                        {foreach $features as $feature}
                                            <option value="{$feature.id_feature|intval}">{$feature.name|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group arpl-exclude-same-category-group">
                                <label class="control-label col-sm-4">{l s='Exclude products from same category' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <span class="switch prestashop-switch fixed-width-lg">
                                        <input type="radio" name="list.exclude_same_category" id="arpl-list-form_list_exclude_same_category_on" value="1" checked="checked">
                                        <label for="arpl-list-form_list_exclude_same_category_on">Yes</label>
                                        <input type="radio" name="list.exclude_same_category" id="arpl-list-form_list_exclude_same_category_off" value="0">
                                        <label for="arpl-list-form_list_exclude_same_category_off">No</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group arpl-same-category-only-group">
                                <label class="control-label col-sm-4">{l s='Same category products only' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <span class="switch prestashop-switch fixed-width-lg">
                                        <input type="radio" name="list.same_category_only" id="arpl-list-form_list_same_category_only_on" value="1" checked="checked">
                                        <label for="arpl-list-form_list_same_category_only_on">Yes</label>
                                        <input type="radio" name="list.same_category_only" id="arpl-list-form_list_same_category_only_off" value="0">
                                        <label for="arpl-list-form_list_same_category_only_off">No</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group arpl-in-stock">
                                <label class="control-label col-sm-4">{l s='In-stock products only' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <span class="switch prestashop-switch fixed-width-lg">
                                        <input type="radio" name="list.instock" id="arpl-list-form_list_instock_on" value="1" checked="checked">
                                        <label for="arpl-list-form_list_instock_on">Yes</label>
                                        <input type="radio" name="list.instock" id="arpl-list-form_list_instock_off" value="0">
                                        <label for="arpl-list-form_list_instock_off">No</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                </div>
                            </div>
                            <div class="arpl-current-category-group">
                                <div class="form-group arpl-current-category">
                                    <label class="control-label col-sm-4">{l s='Including current category' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="list.current_category" id="arpl-list-form_list_current_category_on" value="1" checked="checked">
                                            <label for="arpl-list-form_list_current_category_on">Yes</label>
                                            <input type="radio" name="list.current_category" id="arpl-list-form_list_current_category_off" value="0">
                                            <label for="arpl-list-form_list_current_category_off">No</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group arpl-current-category-only">
                                    <label class="control-label col-sm-4">{l s='Current category only' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="list.current_category_only" id="arpl-list-form_list_current_category_only_on" value="1" checked="checked">
                                            <label for="arpl-list-form_list_current_category_only_on">Yes</label>
                                            <input type="radio" name="list.current_category_only" id="arpl-list-form_list_current_category_only_off" value="0">
                                            <label for="arpl-list-form_list_current_category_only_off">No</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group arpl-full-tree">
                                    <label class="control-label col-sm-4">{l s='Including sub-sub categories (full category tree)' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="list.full_tree" id="arpl-list-form_list_full_tree_on" value="1" checked="checked">
                                            <label for="arpl-list-form_list_full_tree_on">Yes</label>
                                            <input type="radio" name="list.full_tree" id="arpl-list-form_list_full_tree_off" value="0">
                                            <label for="arpl-list-form_list_full_tree_off">No</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group non-slider-group">
                                <label class="control-label col-sm-4">{l s='Items in row (large screen)' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="list.grid" id="arpl-list-form_list_grid" data-serializable="true" data-default="6">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group non-slider-group">
                                <label class="control-label col-sm-4">{l s='Items in row (medium screen)' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="list.grid_md" id="arpl-list-form_list_grid_md" data-serializable="true" data-default="3">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group non-slider-group">
                                <label class="control-label col-sm-4">{l s='Items in row (small screen)' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="list.grid_sm" id="arpl-list-form_list_grid_sm" data-serializable="true" data-default="2">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group category-group">
                                <label class="control-label required col-sm-4">{l s='Category' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    {$categoriesTree nofilter}
                                    <div id="arpl-list-form_list_category"></div>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group category-restrictions-group">
                                <label class="control-label required col-sm-4">{l s='Show list in categories' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    {$categoriesCheckboxTree nofilter}
                                    <div id="arpl-list-form_list_category_restrictions"></div>
                                    <p class="help-block">
                                        {l s='Applicable only for category context' mod='arproductlists'}
                                    </p>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group category-restrictions2-group">
                                <label class="control-label required col-sm-4">{l s='Show products that belongs to categories' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    {$categoriesCheckboxTree2 nofilter}
                                    <div id="arpl-list-form_list_category_restrictions2"></div>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group manufacturer-group">
                                <label class="control-label required col-sm-4">{l s='Manufacturer' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="list.id_manufacturer" id="arpl-list-form_list_id_manufacturer" data-serializable="true" data-default="center">
                                        {foreach $manufacturers as $manufacturer}
                                            <option value="{$manufacturer.id_manufacturer|intval}">{$manufacturer.name|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group supplier-group">
                                <label class="control-label required col-sm-4">{l s='Supplier' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="list.id_supplier" id="arpl-list-form_list_id_supplier" data-serializable="true" data-default="center">
                                        {foreach $suppliers as $supplier}
                                            <option value="{$supplier.id_supplier|intval}">{$supplier.name|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group custom-products-group">
                                <label class="control-label required col-sm-4">{l s='Products' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <input type="text" placeholder="Enter product ID or reference" class="form-control" id="arpl-list-form_list_product" data-default="">
                                    <input type="hidden" value="" class="form-control" id="arpl-list-form_list_ids" name="list.ids" data-serializable="true" data-default="">
                                    <ul id="arpl-product-container">
                                        
                                    </ul>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group custom-categories-group">
                                <label class="control-label required col-sm-4">{l s='Categories' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <input type="text" placeholder="Enter category ID or name" class="form-control" id="arpl-list-form_list_categories" data-default="">
                                    <input type="hidden" value="" class="form-control" id="arpl-list-form_list_cat_ids" name="list.cat_ids" data-serializable="true" data-default="">
                                    <ul id="arpl-categories-container">
                                        
                                    </ul>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group custom-brands-group">
                                <label class="control-label required col-sm-4">{l s='Brands' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <input type="text" placeholder="Enter manufacturer ID or name" class="form-control" id="arpl-list-form_list_brands" data-default="">
                                    <input type="hidden" value="" class="form-control" id="arpl-list-form_list_brand_ids" name="list.brand_ids" data-serializable="true" data-default="">
                                    <ul id="arpl-brands-container">
                                        
                                    </ul>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="more-link-group">
                                <div class="form-group">
                                    <label class="control-label col-sm-4">{l s='Show more link' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="list.more_link" id="arpl-list-form_list_more_link_on" value="1" checked="checked">
                                            <label for="arpl-list-form_list_more_link_on">Yes</label>
                                            <input type="radio" name="list.more_link" id="arpl-list-form_list_more_link_off" value="0">
                                            <label for="arpl-list-form_list_more_link_off">No</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group more-group">
                                    <label class="control-label col-sm-4">{l s='More link' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        {foreach $languages as $language}
                                            <div class="translatable-field row lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                                <div class="col-lg-10">
                                                    <input name="more_link_{$language.id_lang|escape:'htmlall':'UTF-8'}" id="arpl-list-form_more_link_{$language.id_lang|escape:'htmlall':'UTF-8'}" 
                                                           data-lang="{$language.id_lang|escape:'htmlall':'UTF-8'}" value="" data-serializable="true" data-default="" class="arpl-list-form_more_link arcontactus-control" type="text" />
                                                </div>
                                                <div class="col-lg-2">
                                                        <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                                                {$language.iso_code|escape:'htmlall':'UTF-8'}
                                                                <i class="icon-caret-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                                {foreach from=$languages item=language}
                                                                <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'htmlall':'UTF-8'});" data-lang="{$language.id_lang|intval}" tabindex="-1">{$language.name|escape:'htmlall':'UTF-8'}</a></li>
                                                                {/foreach}
                                                        </ul>
                                                </div>
                                            </div>
                                        {/foreach}
                                        <div class="errors"></div>
                                    </div>
                                </div>
                                <div class="form-group more-group">
                                    <label class="control-label col-sm-4">{l s='Custom more link url' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="arpl-list-form_list_more_url" name="list.more_url" data-serializable="true" data-default="">
                                        <p class="help-block">
                                            {l s='You can override standard link with your one. Token {lang} will be replaced to current language.' mod='arproductlists'}
                                        </p>
                                        <div class="errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group limit-group">
                                <label class="control-label required col-sm-4">{l s='Limit items count' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="arpl-list-form_list_limit" name="list.limit" data-serializable="true" data-default="">
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="order-group">
                                <div class="form-group">
                                    <label class="control-label col-sm-4">{l s='Sort order' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <select class="form-control" name="list.orderBy" id="arpl-list-form_list_orderBy" data-serializable="true" data-default="">

                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <select class="form-control" name="list.orderWay" id="arpl-list-form_list_orderWay" data-serializable="true" data-default="">

                                                </select>
                                            </div>
                                        </div>

                                        <div class="errors"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">{l s='Display sort control' mod='arproductlists'}</label>
                                    <div class="col-sm-8">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="list.sortorder" id="arpl-list-form_list_sortorder_on" value="1" checked="checked">
                                            <label for="arpl-list-form_list_sortorder_on">{l s='Yes' mod='arproductlists'}</label>
                                            <input type="radio" name="list.sortorder" id="arpl-list-form_list_sortorder_off" value="0">
                                            <label for="arpl-list-form_list_sortorder_off">{l s='No' mod='arproductlists'}</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">{l s='Use shop context filter' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <span class="switch prestashop-switch fixed-width-lg">
                                        <input type="radio" name="list.shop_filter" id="arpl-list-form_list_shop_filter_on" value="1" checked="checked">
                                        <label for="arpl-list-form_list_shop_filter_on">Yes</label>
                                        <input type="radio" name="list.shop_filter" id="arpl-list-form_list_shop_filter_off" value="0">
                                        <label for="arpl-list-form_list_shop_filter_off">No</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group category-filter-group">
                                <label class="control-label col-sm-4">{l s='Use category context filter' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <span class="switch prestashop-switch fixed-width-lg">
                                        <input type="radio" name="list.category_filter" id="arpl-list-form_list_category_filter_on" value="1" checked="checked">
                                        <label for="arpl-list-form_list_category_filter_on">Yes</label>
                                        <input type="radio" name="list.category_filter" id="arpl-list-form_list_category_filter_off" value="0">
                                        <label for="arpl-list-form_list_category_filter_off">No</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">{l s='Custom JS code' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" name="list.custom_js" rows="6" id="arpl-list-form_list_custom_js" data-serializable="true" data-default=""></textarea>
                                    <div class="errors"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal">{l s='Close' mod='arproductlists'}</button>
                    <button class="btn btn-success" type="submit">{l s='Save' mod='arproductlists'}</button>
                </div>
            </form>
        </div>
    </div>
</div>