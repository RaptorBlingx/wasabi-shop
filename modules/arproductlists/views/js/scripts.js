/*
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
*/

var arPL = {
    currentProduct: null,
    currentIPA: null,
    currentCategory: null,
    ajaxTabs: [],
    ps: 17,
    init: function(){
        jQuery('.ar-pl-promotions').on('click', '.ar-pl-promo-total-add-to-cart', function(){
            var promoId = jQuery(this).data('promo-id');
            arPL.addToCart(promoId);
        });
        jQuery('.arpl-group-tabbed').on('click', '.arpl-tab-ajax', function(){
            var id = jQuery(this).data('id');
            var group_id = jQuery(this).data('group');
            arPL.loadAjaxTab(group_id, id, 0);
        });
        jQuery('.arpl-group-tabbed').on('click', '.arpl-tab-header a', function(){
            var $this = $(this);
            setTimeout(function(){
                $this.get(0).scrollIntoView({
                    block: "start",
                    behavior: "smooth"
                });
            }, 100);
        });
        jQuery('.arpl-tab-header a').click(function(e){
            jQuery(this).parents('.arpl-tab-content').find('.arpl-tab-header a.active').removeClass('active');
        });
        if (arPL.ps == 16){
            arPL.init16();
        }
    },
    init16: function(){
        jQuery('.arpl-group-tabbed').on('click', '.nav-link', function(){
            jQuery('.arpl-group-tabbed .nav-link.active').removeClass('active');
        });
    },
    scanAndUpdate: function(){
        jQuery('.arpl-refresh-on-product-update').each(function(){
            var group_id = jQuery(this).data('group-id');
            var list_id = jQuery(this).data('list-id');
            arPL.loadAjaxTab(group_id, list_id, 1);
        });
    },
    initOWLSlider: function(group_id, id, list, breakdowns){
        jQuery('#arpl-section-' + group_id + '-' + id + '-carousel').arplOwlCarousel({
            loop: list.loop,
            margin: 0,
            nav: list.controls,
            dots: list.dots,
            responsiveClass: 'arpl-responsive',
            responsiveBaseElement: list.responsiveBaseElement == 'window'? window : jQuery('#arpl-section-' + group_id + '-' + id + '-carousel').parent(),
            center: list.center,
            lazyLoad: false,
            autoplay: list.autoplay,
            autoplayTimeout: list.autoplayTimeout,
            autoplayHoverPause: true,
            mouseDrag: list.drag,
            touchDrag: list.drag,
            navText: [
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path fill="currentColor" d="M238.475 475.535l7.071-7.07c4.686-4.686 4.686-12.284 0-16.971L50.053 256 245.546 60.506c4.686-4.686 4.686-12.284 0-16.971l-7.071-7.07c-4.686-4.686-12.284-4.686-16.97 0L10.454 247.515c-4.686 4.686-4.686 12.284 0 16.971l211.051 211.05c4.686 4.686 12.284 4.686 16.97-.001z" class=""></path></svg>',
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path fill="currentColor" d="M17.525 36.465l-7.071 7.07c-4.686 4.686-4.686 12.284 0 16.971L205.947 256 10.454 451.494c-4.686 4.686-4.686 12.284 0 16.971l7.071 7.07c4.686 4.686 12.284 4.686 16.97 0l211.051-211.05c4.686-4.686 4.686-12.284 0-16.971L34.495 36.465c-4.686-4.687-12.284-4.687-16.97 0z" class=""></path></svg>'
            ],
            responsive:breakdowns
        });
    },
    loadAjaxTab: function(group_id, id, contentOnly){
        if (!contentOnly && arPL.ajaxTabs.indexOf(id) !== -1){
            return false;
        }
        jQuery('#arpl-tab-' + id).addClass('arpl-ajax-loading');
        jQuery('#arpl-section-' + group_id + '-' + id).addClass('arpl-ajax-loading');
        var $loader = jQuery('<div>', {
            class: 'load-container load7'
        });
        var $loaderContent = jQuery('<div>', {
            class: 'loader'
        });
        $loader.append($loaderContent);
        jQuery('#arpl-section-' + group_id + '-' + id).append($loader);
        jQuery.ajax({
            type: 'POST',
            url: arplAjaxURL,
            dataType: 'json',
            data: {
                action: 'loadTab',
                ajax : true,
                id: id,
                group_id: group_id,
                product_id: arPL.currentProduct,
                category_id: arPL.currentCategory,
                id_product_attribute: arPL.currentIPA,
                arPLContentOnly: contentOnly,
                sortOrder: jQuery('#arpl-section-' + group_id + '-' + id + ' .arpl-sort').val()
            },
            success: function(data){
                if (jQuery('#arpl-tab-' + id).length){
                    jQuery('#arpl-tab-' + id + ' .arpl-content').replaceWith(data.content);
                    if (data.model.list.view == 1) {
                        arPL.initOWLSlider(group_id, id, data.model.list, data.responsiveBreakdowns);
                    }
                    jQuery('#arpl-tab-' + id).removeClass('arpl-ajax-loading').addClass('arpl-ajax-loaded');
                } else if (jQuery('#arpl-section-' + group_id + '-' + id).length) {
                    if (data.content == null) {
                        jQuery('#arpl-section-' + group_id + '-' + id).hide();
                    } else {
                        jQuery('#arpl-section-' + group_id + '-' + id).show();
                        jQuery('#arpl-section-' + group_id + '-' + id + ' .arpl-content').replaceWith(data.content);
                        if (data.model.list.view == 1) {
                            arPL.initOWLSlider(group_id, id, data.model.list, data.responsiveBreakdowns);
                        }
                    }
                    jQuery('#arpl-section-' + group_id + '-' + id).removeClass('arpl-ajax-loading');
                }
                if ((arPL.ps == 17 || arPL.ps == 8) && arPL.currentCategory == 0) {
                    prestashop.emit('updateProductList', {});
                }
                jQuery('#arpl-section-' + group_id + '-' + id + ' .load-container').remove();
                arPL.ajaxTabs.push(id);
            }
        }).fail(function(){
            jQuery('#arpl-tab-' + id).removeClass('arpl-ajax-loading').addClass('arpl-ajax-loaded').addClass('arpl-ajax-error');
            jQuery('#arpl-section-' + group_id + '-' + id).removeClass('arpl-ajax-loading');
            console.log('fail');
        });
    },
    showModal: function(promoId, ids){
        jQuery.ajax({
            type: 'POST',
            url: arplAjaxURL,
            dataType: 'json',
            data: {
                action: 'showModal',
                ajax : true,
                ids: ids,
                cartRuleId: promoId
            },
            success: function(data){
                jQuery('#blockcart-modal').remove();
                jQuery('body').append(data.content);
                jQuery('#blockcart-modal').modal('show');
            }
        }).fail(function(){
            console.log('fail');
        });
    },
    addToCart: function(promoId){
        var token = jQuery('#arpl-promo-token-' + promoId).val();
        var ids = [];
        $('#arpl-promo-' + promoId).find('.ar-pl-promo-product.active').each(function(){
            ids.push($(this).data('id'));
        });
        $('#arpl-promo-' + promoId).find('.ar-pl-promo-total-add-to-cart').prop('disabled', 'disabled');
        $('#arpl-promo-' + promoId).find('.ar-pl-promo-total-add-to-cart').addClass('arpl-loading');
        var idsClone = ids.slice(0);
        var lastId = idsClone.pop();
        jQuery.each(ids, function(index){
            jQuery.ajax({
                type: 'POST',
                url: arplCartURL,
                async: false,
                dataType: 'json',
                data: {
                    controller: 'cart',
                    token: token,
                    action: 'update',
                    ajax : true,
                    qty: 1,
                    add: 1,
                    id_product: this,
                    id_customization: 0
                },
                success: function(data){
                    if (data.id_product == lastId){
                        if (arPL.ps == 17 || arPL.ps == 8){
                            prestashop.emit('updateCart', {reason:data});
                        } else {
                            document.location.assign(arplCartURL);
                        }
                        arPL.showModal(promoId, ids);
                        $('#arpl-promo-' + promoId).find('.ar-pl-promo-total-add-to-cart').removeAttr('disabled');
                        $('#arpl-promo-' + promoId).find('.ar-pl-promo-total-add-to-cart').removeClass('arpl-loading');
                    }
                }
            }).fail(function(){
                console.log('fail');
            });
        });
    }
};