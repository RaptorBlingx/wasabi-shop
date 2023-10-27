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
window.addEventListener('load', function(){
    $('.tree-folder .tree').show();
    $('#arpl-category-restrictions2').addClass('full_loaded');
    $('#arpl-category-restrictions').addClass('full_loaded');
    arPL.init();
});
var arPL = {
    errorMessage: 'Operation error',
    initRelCat: function(){
        $('.arplr-relcat-content ul').sortable({
            handle: '.arpl-relcat-handle',
            axis: 'y',
            update: function(event, ui){
                var $container = $(ui.item.get(0)).parents('.arpl-relcat');
                arPL.relCat.reorder($container);
            }
        });
    },
    init: function(){
        $('.field_custom_css > div').append('<div id="ace-css-editor"></div>');
        setTimeout(function(){
            var editor  = new ace.edit('ace-css-editor');
            editor.session.setMode("ace/mode/css");
            editor.setTheme("ace/theme/chrome");
            editor.setValue($('#ARPLG_CUSTOM_CSS').val());
            editor.on('change', function(e){
                $('#ARPLG_CUSTOM_CSS').val(editor.getValue());
            });
        }, 100);
        
        $('.arpl-exclude-same-category-group .prestashop-switch input').change(function(){
            if ($('#arpl-list-form_list_exclude_same_category_on').is(':checked')){
                $('#arpl-list-form_list_same_category_only_off').trigger('click');
            }
        });

        $('.arpl-same-category-only-group .prestashop-switch input').change(function(){
            if ($('#arpl-list-form_list_same_category_only_on').is(':checked')){
                $('#arpl-list-form_list_exclude_same_category_off').trigger('click');
            }
        });
        
        $('.fancybox').fancybox();
        $('#arproductlists-config-tabs').addClass('active');
        $(".arplTabs a").click(function(e){
            e.preventDefault();
            $(".arplTabs .active").removeClass('active');
            $(this).addClass('active');
            $('#arpl-config .arproductlists-config-panel').addClass('hidden');
            $('#' + $(this).data('target')).removeClass('hidden');
            $('#arproductlistsActiveTab').remove();
            $('#arproductlistsActiveTab').val($(this).data('tab'));
            if ($(this).data('target') == 'arproductlists-relcat') {
                arPL.relCat.reload();
            }
            if ($(this).data('target') == 'arproductlists-rules') {
                arPL.rules.reload();
            }
        });
        $('#arpl-config').on('click', '.arpl-group-toggle', function(){
            $(this).parents('.arpl-group-container').toggleClass('open');
        });
        $('#arpl-config').on('click', '.arpl-group-title', function(){
            $(this).parents('.arpl-group-container').toggleClass('open');
        });
        $('#arpl-config').on('click', '.arpl-group-list-container .arpl-list-delete', function(){
            var id = $(this).data('id');
            arPL.group.removeList(id);
        });
        $('#arpl-config').on('click', '.arpl-group-delete', function(){
            var id = $(this).data('id');
            arPL.group.remove(id);
        });
        $('#arpl-config').on('click', '.arpl-add-group', function(){
            var hook = $(this).data('hook');
            arPL.group.create(hook);
        });
        $('#arpl-config').on('click', '.arpl-group-edit', function(){
            var id = $(this).data('id');
            arPL.group.edit(id);
        });
        $('#arpl-config').on('click', '.arpl-group-device li', function(){
            var id = $(this).parents('.arpl-group-device').data('id');
            var value = $(this).data('value');
            arPL.group.changeDevice(id, value);
        });
        $('#arpl-config').on('click', '.arpl-list-device li', function(){
            var id = $(this).parents('.arpl-list-device').data('id');
            var value = $(this).data('value');
            arPL.list.changeDevice(id, value);
        });
        $('#arpl-config').on('click', '.arpl-group-status', function(){
            var id = $(this).data('id');
            arPL.group.toggle(id);
        });
        $('#arpl-config').on('click', '.arpl-group-list-container .arpl-list-status', function(){
            var id = $(this).data('id');
            arPL.list.toggle(id);
        });
        $('#arpl-config').on('click', '.arpl-group-add-list', function(){
            var id = $(this).data('id');
            arPL.list.create(id);
            arPL.switchClass(id);
            arPL.switchOrder();
        });
        $('#arpl-config').on('click', '.arpl-relcat-status', function(){
            var id = $(this).data('id');
            arPL.relCat.toggle(id);
        });
        $('#arpl-config').on('click', '.arpl-relcat-delete', function(){
            var id = $(this).data('id');
            arPL.relCat.removeRel(id);
        });
        $('#arpl-config').on('click', '.arpl-rel-delete', function(){
            var id = $(this).data('id');
            arPL.relCat.remove(id);
        });
        $('#arpl-config').on('click', '.arpl-rel-add', function(){
            var id = $(this).data('id');
            arPL.relCat.edit(id);
        });
        $('#arpl-config').on('click', '.arpl-rel-toggle', function(){
            var id = $(this).data('id');
            $('#arpl-rel-' + id).toggleClass('active');
        });
        $('#arpl-config').on('click', '.arpl-group-list-container .arpl-list-edit', function(){
            var id = $(this).data('id');
            arPL.list.edit(id);
        });
        $('#arpl-config').on('click', '.prestashop-switch', function(){
            arPL.switchFields();
        });
        $('#arpl-config').on('click', '#arpl-list-form_class', function(){
            arPL.switchClass($('#arpl-list-form_id_group').val());
        });
        $('#arpl-config').on('click', '#arpl-list-form_list_orderBy', function(){
            arPL.switchOrder();
        });
        $('#arpl-config').on('click', '.arpl-product-delete', function(){
            $(this).parent().remove();
            arPL.updateProductList();
        });
        $('#arpl-config .owl-group').on('click', 'label', function(){
            arPL.switchFields();
        });
        $('#arpl-config').on('click', '.arpl-category-delete', function(){
            $(this).parent().remove();
            arPL.updateCategoryList();
        });
        $('#arpl-config').on('click', '.arpl-brand-delete', function(){
            $(this).parent().remove();
            arPL.updateBrandList();
        });
        $('#arpl-config').on('change', '.arpl-rule-condition-type select', function(){
            var val = $(this).val();
            var $el = $(this).parents('.arpl-rule-condition');
            arPL.rules.changeType(val, $el);
        });
        $('#arpl-config').on('change', '[name="id_feature"]', function(){
            var val = $(this).val();
            var $el = $(this).parents('.arpl-rule-condition');
            arPL.rules.changeFeature(val, $el);
        });
        $('#arpl-config').on('click', '.arpl-rule-add-group', function(){
            var $el = $(this).parents('.arpl-rule');
            arPL.rules.addGroup($el);
        });
        $('#arpl-config').on('click', '.arpl-condition-add', function(){
            var $el = $(this).parents('.arpl-rule-condition');
            arPL.rules.addCondition($el);
        });
        $('#arpl-config').on('click', '.arpl-condition-remove', function(){
            var $el = $(this).parents('.arpl-rule-condition');
            arPL.rules.removeCondition($el);
        });
        $('#arpl-config').on('click', '.arpl-condition-toggle', function(){
            var $el = $(this).parents('.arpl-rule-condition');
            arPL.rules.toggleCondition($el);
        });
        $('#arpl-config').on('click', '.arpl-rule-remove', function(){
            var $el = $(this).parents('.arpl-rule');
            arPL.rules.remove($el);
        });
        $('#arpl-config').on('click', '.arpl-rule-save', function(){
            var $el = $(this).parents('.arpl-rule');
            arPL.rules.save($el);
        });
        $('#arpl-config').on('click', '.arpl-rule-cancel', function(){
            var $el = $(this).parents('.arpl-rule');
            arPL.rules.cancel($el);
        });
        
        $('#arpl-config').on('click', '.arpl-rule-toggle', function(){
            $(this).parents('.arpl-rule').toggleClass('expanded');
        });
        $('#arpl-config').on('focus', '.arplr-rule-name input', function(){
            $(this).parents('.arpl-rule').addClass('expanded');
        });
        $('#arpl-groups .nav-tabs li a').click(function(){
            if ($(this).data('general')){
                $('#arpl-list-container').removeClass('arpl-list-disabled');
            }else{
                $('#arpl-list-container').addClass('arpl-list-disabled');
            }
            if ($(this).data('product-context')){
                $('#arpl-list-product-context-container').removeClass('arpl-list-disabled');
                $('.product-context-opt').removeClass('hidden');
            }else{
                $('#arpl-list-product-context-container').addClass('arpl-list-disabled');
                $('.product-context-opt').addClass('hidden');
            }
            if ($(this).data('category-context')){
                $('#arpl-list-category-context-container').removeClass('arpl-list-disabled');
            }else{
                $('#arpl-list-category-context-container').addClass('arpl-list-disabled');
            }
        });
        
        $('#arpl-list-container, #arpl-list-product-context-container, #arpl-list-category-context-container').sortable({
            connectWith: '.arpl-group-list-container-inner',
            handle: '.arpl-list-handle',
            update: function(event, ui){
                var el = ui.item.get(0);
                var $container = null;
                var product = 0;
                var category = 0;
                if ($(el).data('product-context')){
                    $container = $('#arpl-list-product-context-container');
                    product = 1;
                }else if($(el).data('category-context')){
                    $container = $('#arpl-list-category-context-container');
                    category = 1;
                }else{
                    $container = $('#arpl-list-container');
                }
                if ($(el).parents('.arpl-group-container').length){
                    var listId = $(el).data('list-id');
                    var group = $(el).parents('.arpl-group-container');
                    var groupId = $(group.get(0)).data('id');
                    arPL.group.addList(groupId, listId, el);
                    if ($(el).data('product-context')){
                        $('#arpl-list-product-context-container').append($(el).clone());
                    }else if($(el).data('category-context')){
                        $('#arpl-list-category-context-container').append($(el).clone());
                    }else{
                        $('#arpl-list-container').append($(el).clone());
                    }
                    arPL.list.reload($container, product, category);
                }else{
                    arPL.list.reorder($container);
                }
            }
        });
        arPL._initListContainer();
        arPL.switchFields();
        arPL.switchClass();
        arPL.switchOrder();
        $('.arpl-group-list').sortable({
            axis: 'y',
            handle: '.arpl-group-handle',
            update: function(event, ui){
                var hook = ui.item.parents('.arpl-sub-section').data('hook');
                arPL.group.reorder(hook);
            }
        });
        $('#arpl-product-container').sortable({
            axis: 'y',
            handle: '.arpl-product-handle',
            update: function(event, ui){
                arPL.updateProductList();
            }
        });
        $('#arpl-categories-container').sortable({
            axis: 'y',
            handle: '.arpl-category-handle',
            update: function(event, ui){
                arPL.updateCategoryList();
            }
        });
        $('#arpl-brands-container').sortable({
            axis: 'y',
            handle: '.arpl-brand-handle',
            update: function(event, ui){
                arPL.updateBrandList();
            }
        });
        $("#arpl-list-form_list_product").autocomplete({
            minLength: 1,
            source: function(request, response) {
                $.ajax({
                    url: arPL.list.ajaxUrl,
                    dataType: "json",
                    data: {
                        action : 'productSearch',
                        ajax : true,
                        q: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                if (!ui.item){
                    return false;
                }
                arPL.blockUI('.custom-products-group>div');
                $.ajax({
                    type: 'GET',
                    url: arPL.list.ajaxUrl,
                    dataType: 'json',
                    data: {
                        action : 'productForList',
                        ajax : true,
                        id: ui.item.id
                    },
                    success: function(data){
                        arPL.unblockUI('.custom-products-group>div');
                        $('#arpl-product-container').append(data.content);
                        $('#arpl-list-form_list_product').val('');
                        arPL.updateProductList();
                    }
                }).fail(function(){
                    arPL.unblockUI('.custom-products-group>div');
                    showErrorMessage(arPL.errorMessage);
                });
            },
            focus: function(event, ui) {
                $('.ui-autocomplete-row.selected').removeClass('selected');
                $('#arpl-ac-item-' + ui.item.id).addClass('selected');
            }
        }).data("ui-autocomplete")._renderItem = function(ul, item) {
            var a = $('<a>', {
                id: 'ui-id-' + item.id,
                class: ''
            });
            var li = $("<li>", {
                class: 'ui-autocomplete-row ui-menu-item',
                'data-id': item.id,
                id: 'arpl-ac-item-' + item.id
            });
            var image = $('<img>', {
                src: item.image
            });
            var name = $('<div>', {
                class: 'arpl-product-name'
            });
            name.text(item.label);
            
            var ref = $('<div>', {
                class: 'arpl-product-ref'
            });
            ref.text(item.ref);
            
            li.data("item.autocomplete", item)
            a.append(image);
            a.append(name);
            a.append(ref);
            a.appendTo(li);
            li.appendTo(ul);
            return li;
        };
        
        $("#arpl-list-form_list_categories").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: arPL.list.ajaxUrl,
                    dataType: "json",
                    data: {
                        action : 'categorySearch',
                        ajax : true,
                        q: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 3,
            select: function(event, ui) {
                if (!ui.item){
                    return false;
                }
                arPL.blockUI('.custom-categories-group>div');
                $.ajax({
                    type: 'GET',
                    url: arPL.list.ajaxUrl,
                    dataType: 'json',
                    data: {
                        action : 'categoryForList',
                        ajax : true,
                        id: ui.item.id
                    },
                    success: function(data){
                        arPL.unblockUI('.custom-categories-group>div');
                        $('#arpl-categories-container').append(data.content);
                        $('#arpl-list-form_list_categories').val('');
                        arPL.updateCategoryList();
                    }
                }).fail(function(){
                    arPL.unblockUI('.custom-categories-group>div');
                    showErrorMessage(arPL.errorMessage);
                });
            },
            focus: function(event, ui) {
                $('.ui-autocomplete-row.selected').removeClass('selected');
                $('#arpl-ac-item-' + ui.item.id).addClass('selected');
            }
        }).data("ui-autocomplete")._renderItem = function(ul, item) {
            var a = $('<a>', {
                id: 'ui-id-' + item.id,
                class: ''
            });
            var li = $("<li>", {
                class: 'ui-autocomplete-row ui-menu-item',
                'data-id': item.id,
                id: 'arpl-ac-item-' + item.id
            });
            var image = $('<img>', {
                src: item.image
            });
            var name = $('<div>', {
                class: 'arpl-category-name'
            });
            name.text(item.label);
            
            var ref = $('<div>', {
                class: 'arpl-product-ref'
            });
            
            ref.text('ID: ' + item.id);
            
            li.data("item.autocomplete", item);
            a.append(image);
            a.append(name);
            a.append(ref);
            a.appendTo(li);
            li.appendTo(ul);
            return li;
        };
        
        
        $("#arpl-list-form_list_brands").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: arPL.list.ajaxUrl,
                    dataType: "json",
                    data: {
                        action : 'brandSearch',
                        ajax : true,
                        q: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 3,
            select: function(event, ui) {
                if (!ui.item){
                    return false;
                }
                arPL.blockUI('.custom-brands-group>div');
                $.ajax({
                    type: 'GET',
                    url: arPL.list.ajaxUrl,
                    dataType: 'json',
                    data: {
                        action : 'brandForList',
                        ajax : true,
                        id: ui.item.id
                    },
                    success: function(data){
                        arPL.unblockUI('.custom-brands-group>div');
                        $('#arpl-brands-container').append(data.content);
                        $('#arpl-list-form_list_brands').val('');
                        arPL.updateBrandList();
                    }
                }).fail(function(){
                    arPL.unblockUI('.custom-brands-group>div');
                    showErrorMessage(arPL.errorMessage);
                });
            },
            focus: function(event, ui) {
                $('.ui-autocomplete-row.selected').removeClass('selected');
                $('#arpl-ac-item-' + ui.item.id).addClass('selected');
            }
        }).data("ui-autocomplete")._renderItem = function(ul, item) {
            var a = $('<a>', {
                id: 'ui-id-' + item.id,
                class: ''
            });
            var li = $("<li>", {
                class: 'ui-autocomplete-row ui-menu-item',
                'data-id': item.id,
                id: 'arpl-ac-item-' + item.id
            });
            var image = $('<img>', {
                src: item.image
            });
            var name = $('<div>', {
                class: 'arpl-category-name'
            });
            name.text(item.label);
            
            var ref = $('<div>', {
                class: 'arpl-product-ref'
            });
            
            ref.text('ID: ' + item.id);
            
            li.data("item.autocomplete", item);
            a.append(image);
            a.append(name);
            a.append(ref);
            a.appendTo(li);
            li.appendTo(ul);
            return li;
        };
    },
    updateOrderList: function(className){
        $.ajax({
            type: 'POST',
            url: arPL.list.ajaxUrl,
            dataType: 'json',
            data: {
                className: className,
                action : 'getOrder',
                controller : 'AdminArPlList',
                ajax : true
            },
            success: function(data){
                arPL.list.fillOrderFields(data, null, null);
            }
        }).fail(function(){
            showErrorMessage(arPL.errorMessage);
        });
    },
    updateBrandList: function(){
        var ids = [];
        $('#arpl-brands-container li').each(function(){
            ids.push($(this).data('id'));
        });
        $('#arpl-list-form_list_brand_ids').val(ids.join(','));
    },
    updateCategoryList: function(){
        var ids = [];
        $('#arpl-categories-container li').each(function(){
            ids.push($(this).data('id'));
        });
        $('#arpl-list-form_list_cat_ids').val(ids.join(','));
    },
    updateProductList: function(){
        var ids = [];
        $('#arpl-product-container li').each(function(){
            ids.push($(this).data('id'));
        });
        $('#arpl-list-form_list_ids').val(ids.join(','));
    },
    _initListContainer: function(){
        $('.arpl-group-list-container-inner').each(function(){
            if ($(this).hasClass('ui-sortable')){
                $(this).sortable('destroy');
            }
        });
        $('.arpl-group-list-container-inner').sortable({
            axis: 'y',
            handle: '.arpl-list-handle',
            update: function(event, ui){
                var el = ui.item.get(0);
                var group = $(el).parents('.arpl-group-container');
                var groupId = $(group.get(0)).data('id');
                arPL.group.reorderList(groupId);
            }
        });
    },
    switchFields: function(){
        if ($('#arpl-list-form_list_view:checked').val() == '1'){
            $('.slider-group').removeClass('hidden');
        }else{
            $('.slider-group').addClass('hidden');
        }
        if ($('#arpl-list-form_list_more_link_on').is(':checked')){
            $('.more-group').removeClass('hidden');
        }else{
            $('.more-group').addClass('hidden');
        }
        if ($('#arpl-list-form_list_current_category_on').is(':checked')) {
            $('.arpl-current-category-only').removeClass('hidden');
        } else {
            $('.arpl-current-category-only').addClass('hidden');
        }
        if ($('#arpl-list-form_list_current_category_only_on').is(':checked')) {
            $('.arpl-full-tree').addClass('hidden');
            $('#arpl-list-form_list_full_tree_off').prop('checked', true);
        } else {
            $('.arpl-full-tree').removeClass('hidden');
        }
        
        var val = $('#arpl-list-form_class').val();
        if ($('#arpl-list-form_list_view:checked').val() == '1'){
            switch(val){
                case 'ArPLViewedCategories':
                case 'ArPLCustomCategories':
                case 'ArPLProductCategories':
                case 'ArPLChildCategories':
                case 'ArPLRelatedCategories':
                case 'ArPLCustomBrands':
                case 'ArPLCategoryBrands':
                case 'ArPLMostWantedProductsBrands':
                case 'ArPLMostViewedProductsBrands':
                case 'ArPLCategoryChildCategories':
                case 'ArPLCategoryRelatedCategories':
                    $('#arpl-list-form .non-slider-group').addClass('hidden');
                    break;
            }
        }else if ($('#arpl-list-form_list_view:checked').val() == '2'){
            switch(val){
                case 'ArPLViewedCategories':
                case 'ArPLCustomCategories':
                case 'ArPLProductCategories':
                case 'ArPLChildCategories':
                case 'ArPLRelatedCategories':
                case 'ArPLCustomBrands':
                case 'ArPLCategoryBrands':
                case 'ArPLMostWantedProductsBrands':
                case 'ArPLMostViewedProductsBrands':
                case 'ArPLCategoryChildCategories':
                case 'ArPLCategoryRelatedCategories':
                    $('#arpl-list-form .non-slider-group').removeClass('hidden');
                    break;
            }
        }else{
            switch(val){
                case 'ArPLViewedCategories':
                case 'ArPLCustomCategories':
                case 'ArPLProductCategories':
                case 'ArPLChildCategories':
                case 'ArPLRelatedCategories':
                case 'ArPLCustomBrands':
                case 'ArPLCategoryBrands':
                case 'ArPLMostWantedProductsBrands':
                case 'ArPLMostViewedProductsBrands':
                case 'ArPLCategoryChildCategories':
                case 'ArPLCategoryRelatedCategories':
                    $('#arpl-list-form .non-slider-group').addClass('hidden');
                    break;
            }
        }
        switch(val) {
            case 'ArPLMostWantedProducts':
            case 'ArPLMostViewedProducts':
            case 'ArPLSameBrandProducts':
                $('#arpl-list-form .category-filter-group').removeClass('hidden');
                break;
            default:
                $('#arpl-list-form .category-filter-group').addClass('hidden');
        }
    },
    switchOrder: function(){
        var val = $('#arpl-list-form_list_orderBy').val();
        if (val == 'rand') {
            $('#arpl-list-form_list_orderWay').addClass('hidden');
        }else{
            $('#arpl-list-form_list_orderWay').removeClass('hidden');
        }
    },
    switchClass: function(id_group){
        var val = $('#arpl-list-form_class').val();
        $('#arpl-list-form .category-group').addClass('hidden');
        $('#arpl-list-form .limit-group').removeClass('hidden');
        $('#arpl-list-form .ajax-group').removeClass('hidden');
        $('#arpl-list-form .custom-products-group').addClass('hidden');
        $('#arpl-list-form .owl-group').removeClass('hidden');
        $('#arpl-list-form .non-slider-group').addClass('hidden');
        $('#arpl-list-form .thumb-group').addClass('hidden');
        $('#arpl-list-form .brand-thumb-group').addClass('hidden');
        $('#arpl-list-form .order-group').addClass('hidden');
        $('#arpl-list-form .custom-categories-group').addClass('hidden');
        $('#arpl-list-form .custom-brands-group').addClass('hidden');
        $('#arpl-list-form .cat-title-group').addClass('hidden');
        $('#arpl-list-form .cat-desc-group').addClass('hidden');
        $('#arpl-list-form .more-link-group').addClass('hidden');
        $('#arpl-list-form .manufacturer-group').addClass('hidden');
        $('#arpl-list-form .supplier-group').addClass('hidden');
        $('#arpl-list-form .days-group').addClass('hidden');
        $('#arpl-list-form .arpl-attr-group, #arpl-list-form .arpl-feature-group, #arpl-list-form .arpl-exclude-same-category-group, #arpl-list-form .arpl-same-category-only-group').addClass('hidden');
        arPL.switchFields();
        switch(val){
            case 'ArPLHomeFeatured':
            case 'ArPLCategory':
            case 'ArPLPriceDrop':
            case 'ArPLBestSellers':
            case 'ArPLNewProducts':
            case 'ArPLSameCategoryProducts':
            case 'ArPLSameBrandProducts':
            case 'ArPLSameReferenceProducts':
            case 'ArPLBrandProducts':
            case 'ArPLSupplierProducts':
            case 'ArPLWithThisProductAlsoBuy':
            case 'ArPLWithThisProductAlsoBuy':
            case 'ArPLRelatedProducts':
            case 'ArPLRuleProducts':
            case 'ArPLCategoryRelatedProducts':
            case 'ArPLSubcategoriesBestSellers':
            case 'ArPLSubcategoriesFeaturedProducts':
            case 'ArPLSubcategoriesNewProducts':
            case 'ArPLSubcategoriesProducts':
            case 'ArPLViewedProducts':
                $('#arpl-list-form .order-group').removeClass('hidden');
                break;
            case 'ArPLMostViewedProducts':
            case 'ArPLMostWantedProducts':
            case 'ArPLLastCartProducts':
            case 'ArPLMostBuyedProducts':
            case 'ArPLLastBuyedProducts':
                $('#arpl-list-form .days-group').removeClass('hidden');
                break;
        }
        switch(val){
            case 'ArPLHomeFeatured':
            case 'ArPLCategory':
            case 'ArPLPriceDrop':
            case 'ArPLBestSellers':
            case 'ArPLNewProducts':
            case 'ArPLBrandProducts':
            case 'ArPLSupplierProducts':
            case 'ArPLSameCategoryProducts':
            case 'ArPLMostViewedProducts':
            case 'ArPLMostWantedProducts':
            case 'ArPLLastCartProducts':
            case 'ArPLMostBuyedProducts':
            case 'ArPLLastBuyedProducts':
            case 'ArPLCustomProducts':
            case 'ArPLSubcategoriesBestSellers':
            case 'ArPLSubcategoriesFeaturedProducts':
            case 'ArPLSubcategoriesNewProducts':
            case 'ArPLSubcategoriesProducts':
                $('#arpl-list-form .more-link-group').removeClass('hidden');
                break;
        }
        switch(val){
            case 'ArPLCategory':
                $('#arpl-list-form .category-group').removeClass('hidden');
                break;
            case 'ArPLBrandProducts':
                $('#arpl-list-form .manufacturer-group').removeClass('hidden');
                break;
            case 'ArPLSupplierProducts':
                $('#arpl-list-form .supplier-group').removeClass('hidden');
                break;
            case 'ArPLCustomCategories':
                arPL.switchFields();
                $('#arpl-list-form .custom-categories-group').removeClass('hidden');
                $('#arpl-list-form .limit-group').addClass('hidden');
                $('#arpl-list-form .thumb-group').removeClass('hidden');
                $('#arpl-list-form .cat-title-group').removeClass('hidden');
                $('#arpl-list-form .cat-desc-group').removeClass('hidden');
                break;
            case 'ArPLCustomBrands':
                arPL.switchFields();
                $('#arpl-list-form .custom-brands-group').removeClass('hidden');
                $('#arpl-list-form .limit-group').addClass('hidden');
                $('#arpl-list-form .brand-thumb-group').removeClass('hidden');
                $('#arpl-list-form .cat-title-group').removeClass('hidden');
                break;
            case 'ArPLMostWantedProductsBrands':
            case 'ArPLMostViewedProductsBrands':
                $('#arpl-list-form .brand-thumb-group').removeClass('hidden');
                break;
            case 'ArPLRelatedCategories':
            case 'ArPLCategoryChildCategories':
            case 'ArPLCategoryRelatedCategories':
            case 'ArPLProductCategories':
            case 'ArPLViewedCategories':
                arPL.switchFields();
                $('#arpl-list-form .custom-categories-group').addClass('hidden');
                $('#arpl-list-form .limit-group').addClass('hidden');
                $('#arpl-list-form .thumb-group').removeClass('hidden');
                $('#arpl-list-form .cat-title-group').removeClass('hidden');
                $('#arpl-list-form .cat-desc-group').removeClass('hidden');
                break;
            case 'ArPLChildCategories':
                arPL.switchFields();
                $('#arpl-list-form .category-group').removeClass('hidden');
                $('#arpl-list-form .thumb-group').removeClass('hidden');
                $('#arpl-list-form .cat-title-group').removeClass('hidden');
                $('#arpl-list-form .cat-desc-group').removeClass('hidden');
                break;
            case 'ArPLPromotions':
            case 'ArPLPromotionsWithProduct':
                $('#arpl-list-form .ajax-group').addClass('hidden');
                $('#arpl-list-form .owl-group').addClass('hidden');
                $('#arpl-list-form .slider-group').addClass('hidden');
                break;
            case 'ArPLCustomProducts':
                $('#arpl-list-form .custom-products-group').removeClass('hidden');
                $('#arpl-list-form .limit-group').addClass('hidden');
                $('#arpl-list-form .order-group').addClass('hidden');
                break;
        }
        if (val == 'ArPLViewedProducts' || val == 'ArPLProductCategories' || val == 'ArPLSameAttrProducts' || val == 'ArPLSameFeatureProducts') {
            $('#arpl-list-form .category-restrictions2-group').removeClass('hidden');
            if (val == 'ArPLSameAttrProducts') {
                $('#arpl-list-form .category-restrictions-group').removeClass('hidden'); 
                $('#arpl-list-form .arpl-attr-group, #arpl-list-form .arpl-exclude-same-category-group, #arpl-list-form .arpl-same-category-only-group').removeClass('hidden');
            }
            if (val == 'ArPLSameFeatureProducts') {
                $('#arpl-list-form .category-restrictions-group').removeClass('hidden'); 
                $('#arpl-list-form .arpl-feature-group, #arpl-list-form .arpl-exclude-same-category-group, #arpl-list-form .arpl-same-category-only-group').removeClass('hidden');
            }
        } else {
            $('#arpl-list-form .category-restrictions2-group').addClass('hidden');
        }
        if (val == 'ArPLProductRelatedProducts' || val == 'ArPLCustomProducts' || val == 'ArPLCustomCategories') {
            $('#arpl-list-form .limit-group').addClass('hidden');
        } else {
            $('#arpl-list-form .limit-group').removeClass('hidden');
        }
        if (val == 'ArPLSubcategoriesBestSellers' || val == 'ArPLSubcategoriesNewProducts' || val == 'ArPLSubcategoriesProducts') {
            $('.arpl-current-category-group').removeClass('hidden');
        } else {
            $('.arpl-current-category-group').addClass('hidden');
        }
        if (arPL.list.createMode) {
            arPL.updateOrderList(val);
            $.ajax({
                type: 'GET',
                url: arPL.list.ajaxUrl,
                dataType: 'json',
                data: {
                    className: val,
                    action : 'getClassOptions',
                    ajax : true,
                    id_group: id_group
                },
                success: function(data){
                    arPL.toggleContextFields(data);
                }
            }).fail(function(){
                showErrorMessage(arPL.errorMessage);
            });
        }
    },
    toggleContextFields(data){
        if (data.isProductList) {
            $('.arpl-in-stock').removeClass('hidden');
        } else {
            $('.arpl-in-stock').addClass('hidden');
        }

        if (data.isCategoryPageHook || (data && data.model && data.model.class == 'ArPLSameAttrProducts') || (data && data.model && data.model.class == 'ArPLSameFeatureProducts')) {
            $('.category-restrictions-group').removeClass('hidden');
        } else {
            $('.category-restrictions-group').addClass('hidden');
        }
        if (data.isProductPageHook) {
            $('.product-update-group').removeClass('hidden');
        } else {
            $('.product-update-group').addClass('hidden');
        }
    },
    group: {
        ajaxUrl: null,
        prevOrder: null,
        create: function(hook){
            arPL.group.resetForm();
            $('#arpl-group-modal').modal('show');
            $('#arpl-group-form_hook').val(hook);
        },
        changeDevice: function(id, value){
            arPL.blockUI('#arpl-groups');
            $.ajax({
                type: 'POST',
                url: arPL.group.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'changeDevice',
                    controller : 'AdminArPlGroup',
                    ajax : true,
                    value: value,
                    id: id
                },
                success: function(data){
                    arPL.unblockUI('#arpl-groups');
                    $('#arpl-group-' + id).find('.arpl-group-device .current').html($('#arpl-group-' + id).find('.arpl-group-device [data-value="' + value + '"] .icon').html());
                    $('#arpl-group-' + id).find('.arpl-group-device ul .active').removeClass('active');
                    $('#arpl-group-' + id).find('.arpl-group-device [data-value="' + value + '"]').addClass('active');
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-groups');
                showErrorMessage(arPL.errorMessage);
            });
        },
        toggle: function(id){
            arPL.blockUI('#arpl-groups');
            $.ajax({
                type: 'POST',
                url: arPL.group.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'toggle',
                    controller : 'AdminArPlGroup',
                    ajax : true,
                    id: id
                },
                success: function(data){
                    arPL.unblockUI('#arpl-groups');
                    if (data.status){
                        $('#arpl-group-' + id + ' .arpl-group').addClass('active');
                    }else{
                        $('#arpl-group-' + id + ' .arpl-group').removeClass('active')
                    }
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-groups');
                showErrorMessage(arPL.errorMessage);
            });
        },
        remove: function(id){
            if (!confirm('Remove this group?')){
                return false;
            }
            arPL.blockUI('#arpl-groups');
            $.ajax({
                type: 'POST',
                url: arPL.group.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'remove',
                    controller : 'AdminArPlGroup',
                    ajax : true,
                    id: id
                },
                success: function(data){
                    arPL.unblockUI('#arpl-groups');
                    $('#arpl-group-' + id).remove();
                    arPL.group.reorder(data.model.hook);
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-groups');
                showErrorMessage(arPL.errorMessage);
            });
        },
        edit: function(id){
            arPL.blockUI('#arpl-groups');
            arPL.group.resetForm();
            $.ajax({
                type: 'POST',
                url: arPL.group.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'edit',
                    controller : 'AdminArPlGroup',
                    ajax : true,
                    id: id
                },
                success: function(data){
                    arPL.unblockUI('#arpl-groups');
                    arPL.populateForm('#arpl-group-form', data.model);
                    $('#arpl-group-modal').modal('show');
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-groups');
                showErrorMessage(arPL.errorMessage);
            });
        },
        reload: function(hook){
            arPL.blockUI('#arpl-groups');
            $.ajax({
                type: 'POST',
                url: arPL.group.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'reload',
                    controller : 'AdminArPlGroup',
                    ajax : true,
                    hook: hook
                },
                success: function(data){
                    arPL.unblockUI('#arpl-groups');
                    $('#arpl-sub-section-' + hook + ' .arpl-group-list').replaceWith(data.content);
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-groups');
                showErrorMessage(arPL.errorMessage);
            });
        },
        save: function(){
            arPL.blockUI('#arpl-groups');
            $.ajax({
                type: 'POST',
                url: arPL.group.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'saveGroup',
                    controller : 'AdminArPlGroup',
                    ajax : true,
                    id: $('#arpl-group-form_id').val(),
                    title: $('#arpl-group-form_title').val(),
                    type: $('#arpl-group-form_type').val(),
                    hook: $('#arpl-group-form_hook').val(),
                    id_shop: $('#arpl-group-form_id_shop').val()
                },
                success: function(data){
                    arPL.unblockUI('#arpl-groups');
                    $('#arpl-group-modal').modal('hide');
                    if (data.created){
                        $('#arpl-sub-section-' + data.model.hook + ' .arpl-group-list').append(data.content);
                        if (data.created){
                            $('#arpl-group-' + data.model.id).addClass('open');
                        }
                        arPL._initListContainer();
                    }else{
                        $('#arpl-group-' + data.model.id + ' .arpl-group-title').text(data.model.title);
                        $('#arpl-group-' + data.model.id + ' .arpl-group-title').append('<span class="shop-context">' + data.shop_name + '</span>');
                    }
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-groups');
                showErrorMessage(arPL.errorMessage);
            });
        },
        removeList: function(id){
            if (!confirm('Remove this item from group?')){
                return false;
            }
            arPL.blockUI('#arpl-group-'+id);
            $.ajax({
                type: 'POST',
                url: arPL.group.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'removeList',
                    controller : 'AdminArPlGroup',
                    ajax : true,
                    id: id
                },
                success: function(data){
                    arPL.unblockUI('#arpl-group-'+id);
                    var groupId = $('#arpl-list-rel-' + id).data('group-id');
                    $('#arpl-list-rel-' + id).remove();
                    arPL.group.reorderList(groupId);
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-group-'+id);
                showErrorMessage(arPL.errorMessage);
            });
            
        },
        addList: function(groupId, listId, el){
            arPL.blockUI('#arpl-group-' + groupId);
            $.ajax({
                type: 'POST',
                url: arPL.group.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'addToGroup',
                    controller : 'AdminArPlGroup',
                    ajax : true,
                    listId: listId,
                    groupId: groupId
                },
                success: function(data){
                    arPL.unblockUI('#arpl-group-' + groupId);
                    $(el).attr('data-rel-id', data.model.id);
                    $(el).attr('data-group-id', data.model.id_group);
                    $(el).attr('id', 'arpl-list-rel-' + data.model.id);
                    $(el).find('.arpl-list-delete').attr('data-id', data.model.id);
                    $(el).find('.arpl-list-edit').attr('data-id', data.model.id);
                    $(el).find('.arpl-list-status').attr('data-id', data.model.id);
                    arPL.group.reorderList(groupId);
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-group-' + groupId);
                showErrorMessage(arPL.errorMessage);
            });
            
        },
        reorder: function(hook){
            var positions = [];
            $('#arpl-sub-section-' + hook + ' .arpl-group-container').each(function(index){
                var order = index + 1;
                var id = $(this).data('id');
                positions.push(id + '_' + order);
            });
            arPL.blockUI('#arpl-groups');
            if (arPL.prevOrder != positions){
                $.ajax({
                    type: 'POST',
                    url: arPL.group.ajaxUrl,
                    dataType: 'json',
                    data: {
                        action : 'reorder',
                        controller : 'AdminArPlGroup',
                        ajax : true,
                        data: positions
                    },
                    success: function(data){
                        arPL.unblockUI('#arpl-groups');
                        arPL.prevOrder = positions;
                    }
                }).fail(function(){
                    arPL.unblockUI('#arpl-groups');
                    showErrorMessage(arPL.errorMessage);
                });
            }
        },
        reorderList: function(id){
            var positions = [];
            var proced = true;
            $('#arpl-group-'+id).find('.arpl-list-item').each(function(index){
                var order = index + 1;
                var id = $(this).data('rel-id');
                positions.push(id + '_' + order);
                if (typeof id == 'undefined'){
                    proced = false;
                }
            });
            arPL.blockUI('#arpl-group-'+id);
            if (arPL.prevOrder != positions && proced){
                $.ajax({
                    type: 'POST',
                    url: arPL.group.ajaxUrl,
                    dataType: 'json',
                    data: {
                        action : 'reorderList',
                        controller : 'AdminArPlGroup',
                        id: id,
                        ajax : true,
                        data: positions
                    },
                    success: function(data){
                        arPL.unblockUI('#arpl-group-'+id);
                        arPL.prevOrder = positions;
                    }
                }).fail(function(){
                    arPL.unblockUI('#arpl-group-'+id);
                    showErrorMessage(arPL.errorMessage);
                });
            }
        },
        resetForm: function(){
            arPL.resetForm('#arpl-group-form');
        },
    },
    list: {
        createMode: false,
        ajaxUrl: null,
        create: function(id_group){
            arPL.list.createMode = true;
            $('#arpl-list-form .list-class-group').removeClass('hidden');
            arPL.list.resetForm();
            $('#arpl-list-form_id_group').val(id_group);
            $('#arpl-list-modal').modal('show');
        },
        toggle: function(id){
            arPL.blockUI('#arpl-groups');
            $.ajax({
                type: 'POST',
                url: arPL.list.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'toggle',
                    controller : 'AdminArPlList',
                    ajax : true,
                    id: id
                },
                success: function(data){
                    arPL.unblockUI('#arpl-groups');
                    if (data.status){
                        $('#arpl-list-rel-' + id).addClass('active');
                    }else{
                        $('#arpl-list-rel-' + id).removeClass('active')
                    }
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-groups');
                showErrorMessage(arPL.errorMessage);
            });
        },
        edit: function(id){
            arPL.blockUI('#arpl-groups');
            arPL.list.createMode = false;
            arPL.list.resetForm();
            $('#arpl-list-form .list-class-group').addClass('hidden');
            $.ajax({
                type: 'POST',
                url: arPL.list.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'edit',
                    controller : 'AdminArPlList',
                    ajax : true,
                    id: id
                },
                success: function(data){
                    setTimeout(function(){
                        arPL.unblockUI('#arpl-groups');
                        arPL.populateForm('#arpl-list-form', data.model);
                        arPL.list.fillOrderFields(data.order, data.model.list.orderBy, data.model.list.orderWay);
                        $('#arpl-product-container').html('');
                        if (data.products) {
                            $.each(data.products, function(i){
                                $('#arpl-product-container').append(data.products[i]);
                            });
                        }
                        $('#arpl-categories-container').html('');
                        if (data.categories) {
                            $.each(data.categories, function(i){
                                $('#arpl-categories-container').append(data.categories[i]);
                            });
                        }
                        $('#arpl-brands-container').html('');
                        if (data.brands) {
                            $.each(data.brands, function(i){
                                $('#arpl-brands-container').append(data.brands[i]);
                            });
                        }
                        arPL.switchFields();
                        arPL.switchClass(data.model.id_group);
                        arPL.switchOrder();
                        $('#arpl-list-modal').modal('show');
                        $('#arpl-category-restrictions').tree('expandAll');
                        $('#collapse-all-arpl-category-restrictions').show();
                        $('#expand-all-arpl-category-restrictions').hide(); 
                        
                        $('#arpl-category-restrictions2').tree('expandAll');
                        $('#collapse-all-arpl-category-restrictions2').show();
                        $('#expand-all-arpl-category-restrictions2').hide();
                        arPL.blockUI('.category-restrictions-group');
                        arPL.blockUI('.category-restrictions2-group');
                        uncheckAllAssociatedCategories($('#arpl-category-restrictions'));
                        uncheckAllAssociatedCategories($('#arpl-category-restrictions2'));
                        setTimeout(function(){
                            
                            if (data.model.list.category_restrictions) {
                                $.each(data.model.list.category_restrictions, function(i){
                                    $('[name="list.category_restrictions[]"][value="' + data.model.list.category_restrictions[i] + '"]').prop('checked', true);
                                });
                            }
                            
                            if (data.model.list.category_restrictions) {
                                $.each(data.model.list.category_restrictions2, function(i){
                                    $('[name="list.category_restrictions2[]"][value="' + data.model.list.category_restrictions2[i] + '"]').prop('checked', true);
                                });
                            }
                            arPL.unblockUI('.category-restrictions-group');
                            arPL.unblockUI('.category-restrictions2-group');
                        }, 1000);
                        arPL.toggleContextFields(data);
                    }, 200);
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-groups');
                showErrorMessage(arPL.errorMessage);
            });
        },
        fillOrderFields: function(order, orderBy, orderWay){
            if (order == null) {
                return false;
            }
            $('#arpl-list-form_list_orderBy').html('');
            $('#arpl-list-form_list_orderWay').html('');
            $.each(order.orderBy, function(i){
                $('#arpl-list-form_list_orderBy').append('<option value="' + i + '">' + order.orderBy[i] + '</option>');
            });
            $.each(order.orderWay, function(i){
                $('#arpl-list-form_list_orderWay').append('<option value="' + i + '">' + order.orderWay[i] + '</option>');
            });
            if (orderBy){
                $('#arpl-list-form_list_orderBy').val(orderBy);
            }
            if (orderWay){
                $('#arpl-list-form_list_orderWay').val(orderWay);
            }
        },
        save: function(){
            arPL.blockUI('#arpl-list-form');
            $.ajax({
                type: 'POST',
                url: arPL.list.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'save',
                    controller : 'AdminArPlList',
                    ajax : true,
                    data: arPL._getFormData('#arpl-list-form', true),
                    id: $('#arpl-list-form_id').val()
                },
                success: function(data){
                    arPL.unblockUI('#arpl-list-form');
                    if (data.success){
                        $('#arpl-list-modal').modal('hide');
                        if ($('#arpl-list-rel-' + data.model.id).length){
                            $('#arpl-list-rel-' + data.model.id).replaceWith(data.content);
                        }else{
                            $('#arpl-group-' + data.model.id_group + ' .arpl-group-list-container-inner').append(data.content);
                        }
                    }else{
                        arPL.processErrors('#arpl-list-form', data);
                    }
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-list-form');
                showErrorMessage(arPL.errorMessage);
            });
        },
        reorder: function($container){
            arPL.blockUI($container);
            var positions = [];
            $($container).find('.arpl-list-item').each(function(index){
                var order = index + 1;
                var id = $(this).data('list-id');
                positions.push(id + '_' + order);
            });
            $.ajax({
                type: 'POST',
                url: arPL.list.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'reorder',
                    controller : 'AdminArPlList',
                    data: positions,
                    ajax : true,
                },
                success: function(data){
                    arPL.unblockUI($container);
                    $('#arpl-list-container').html(data.content);
                }
            }).fail(function(){
                arPL.unblockUI($container);
                showErrorMessage(arPL.errorMessage);
            });
        },
        reload: function($container, product, category){
            arPL.blockUI($container);
            $.ajax({
                type: 'POST',
                url: arPL.list.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'reload',
                    controller : 'AdminArPlList',
                    ajax : true,
                    product: product,
                    category: category
                },
                success: function(data){
                    arPL.unblockUI($container);
                    $($container).html(data.content);
                }
            }).fail(function(){
                arPL.unblockUI($container);
                showErrorMessage(arPL.errorMessage);
            });
        },
        changeDevice: function(id, value){
            arPL.blockUI('#arpl-groups');
            $.ajax({
                type: 'POST',
                url: arPL.list.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'changeDevice',
                    controller : 'AdminArPlList',
                    ajax : true,
                    value: value,
                    id: id
                },
                success: function(data){
                    arPL.unblockUI('#arpl-groups');
                    $('#arpl-list-rel-' + id).find('.arpl-list-device .current').html($('#arpl-list-rel-' + id).find('.arpl-list-device [data-value="' + value + '"] .icon').html());
                    $('#arpl-list-rel-' + id).find('.arpl-list-device ul .active').removeClass('active');
                    $('#arpl-list-rel-' + id).find('.arpl-list-device [data-value="' + value + '"]').addClass('active');
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-groups');
                showErrorMessage(arPL.errorMessage);
            });
        },
        resetForm: function(){
            arPL.resetForm('#arpl-list-form');
        }
    },
    relCat: {
        ajaxUrl: null,
        create: function(){
            $('#arpl-relcat-modal').modal('show');
            $('#arpl-relcat-form [type="radio"]').prop('checked', false);
            $('#arpl-relcat-form [type="checkbox"]').prop('checked', false);
            $('#arpl-relcat-form .tree-selected').removeClass('tree-selected');
            arPL.resetForm('#arpl-relcat-form');
        },
        save: function(){
            arPL.blockUI('#arpl-relcat');
            arPL.resetForm('#arpl-relcat-form');
            $.ajax({
                type: 'POST',
                url: arPL.relCat.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'save',
                    controller : 'AdminArPlRelCat',
                    ajax : true,
                    data: arPL._getFormData('#arpl-relcat-form', true)
                },
                success: function(data){
                    arPL.unblockUI('#arpl-relcat');
                    if (data.success){
                        $('#arpl-relcat-modal').modal('hide');
                        arPL.relCat.reload();
                    }else{
                        arPL.processErrors('#arpl-relcat-form', data);
                    }
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-relcat');
                showErrorMessage(arPL.errorMessage);
            });
        },
        edit: function(id){
            arPL.blockUI('#arpl-relcat');
            $.ajax({
                type: 'POST',
                url: arPL.relCat.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'edit',
                    controller : 'AdminArPlRelCat',
                    ajax : true,
                    id: id
                },
                success: function(data){
                    arPL.unblockUI('#arpl-relcat');
                    $('#arpl-relcat-form [type="radio"]').prop('checked', false);
                    $('#arpl-relcat-form [type="checkbox"]').prop('checked', false);
                    $('#arpl-relcat-form .tree-selected').removeClass('tree-selected');
                    arPL.resetForm('#arpl-relcat-form');
                    $('#arpl-relcat-source [value="' + id + '"]').prop('checked', true);
                    $('#arpl-relcat-source [value="' + id + '"]').parent().addClass('tree-selected');
                    $('#arpl-relcat-modal').modal('show');
                    $('#arpl-relcat-rels').tree('expandAll');$('#collapse-all-arpl-relcat-rels').show();$('#expand-all-arpl-relcat-rels').hide();
                    $('#arpl-relcat-source').tree('expandAll');$('#collapse-all-arpl-relcat-source').show();$('#expand-all-arpl-relcat-source').hide();
                    arPL.blockUI('#arpl-relcat-form');
                    setTimeout(function(){
                        $.each(data.rels, function(i){
                            $('#arpl-relcat-rels [value="' + data.rels[i] + '"]').prop('checked', true);
                        });
                        arPL.unblockUI('#arpl-relcat-form');
                    }, 1000);
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-relcat');
                showErrorMessage(arPL.errorMessage);
            });
        },
        reload: function(){
            arPL.blockUI('#arpl-relcat');
            $.ajax({
                type: 'POST',
                url: arPL.relCat.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'reload',
                    controller : 'AdminArPlRelCat',
                    ajax : true,
                },
                success: function(data){
                    arPL.unblockUI('#arpl-relcat');
                    $('#arpl-relcat').html(data.content);
                    arPL.initRelCat();
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-relcat');
                showErrorMessage(arPL.errorMessage);
            });
        },
        toggle: function(id){
            arPL.blockUI('#arpl-relcat');
            $.ajax({
                type: 'POST',
                url: arPL.relCat.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'toggle',
                    controller : 'AdminArPlRelCat',
                    ajax : true,
                    id: id
                },
                success: function(data){
                    arPL.unblockUI('#arpl-relcat');
                    if (data.status){
                        $('#arpl-relcat-' + id).addClass('active');
                    }else{
                        $('#arpl-relcat-' + id).removeClass('active')
                    }
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-relcat');
                showErrorMessage(arPL.errorMessage);
            });
        },
        removeRel: function(id){
            if (!confirm('Remove this item?')){
                return false;
            }
            arPL.blockUI('#arpl-relcat');
            $.ajax({
                type: 'POST',
                url: arPL.relCat.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'remove',
                    controller : 'AdminArPlRelCat',
                    ajax : true,
                    id: id
                },
                success: function(data){
                    arPL.unblockUI('#arpl-relcat');
                    $container = $('#arpl-relcat-' + id).parents('.arpl-relcat');
                    $('#arpl-relcat-' + id).remove();
                    
                    arPL.relCat.reorder($container);
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-relcat');
                showErrorMessage(arPL.errorMessage);
            });
        },
        remove: function(id){
            if (!confirm('Remove this item?')){
                return false;
            }
            arPL.blockUI('#arpl-relcat');
            $.ajax({
                type: 'POST',
                url: arPL.relCat.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'removeAll',
                    controller : 'AdminArPlRelCat',
                    ajax : true,
                    id: id
                },
                success: function(data){
                    arPL.unblockUI('#arpl-relcat');
                    $('#arpl-rel-' + id).parent().remove();
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-relcat');
                showErrorMessage(arPL.errorMessage);
            });
        },
        reorder: function($container){
            arPL.blockUI('#arpl-relcat');
            var positions = [];
            $($container).find('ul>li').each(function(index){
                var order = index + 1;
                var id = $(this).data('id');
                positions.push(id + '_' + order);
            });
            $.ajax({
                type: 'POST',
                url: arPL.relCat.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'reorder',
                    controller : 'AdminArPlRelCat',
                    data: positions,
                    ajax : true,
                },
                success: function(data){
                    arPL.unblockUI('#arpl-relcat');
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-relcat');
                showErrorMessage(arPL.errorMessage);
            });
        },
    },
    rules: {
        ajaxUrl: null,
        create: function(){
            var $clone = $('.arpl-rule-empty').clone();
            $clone.removeClass('arpl-rule-empty');
            $('#arpl-rules').append($clone);
            arPL.rules.changeType($clone.find('[name="type"]').val(), $clone.find('.arpl-rule-condition'));
            arPL.rules.loadRules();
        },
        cancel: function($rule){
            if ($rule.find('[name="rule_id"]').val()){
                var id = $rule.find('[name="rule_id"]').val();
                arPL.blockUI('#arpl-rules');
                $.ajax({
                    type: 'POST',
                    url: arPL.rules.ajaxUrl,
                    dataType: 'json',
                    data: {
                        action : 'reload',
                        controller : 'AdminArPlRules',
                        id: id,
                        ajax : true
                    },
                    success: function(data){
                        arPL.unblockUI('#arpl-rules');
                        $('#arpl-rule-' + id).replaceWith(data.content);
                    }
                }).fail(function(){
                    arPL.unblockUI('#arpl-rules');
                    showErrorMessage(arPL.errorMessage);
                });
            }else{
                $rule.remove();
            }
        },
        reload: function(){
            arPL.blockUI('#arpl-rules');
            $.ajax({
                type: 'POST',
                url: arPL.rules.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'reload',
                    controller : 'AdminArPlRules',
                    ajax : true
                },
                success: function(data){
                    arPL.unblockUI('#arpl-rules');
                    $('#arpl-rules').html(data.content);
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-rules');
                showErrorMessage(arPL.errorMessage);
            });
        },
        save: function($rule){
            var name = $rule.find('[name="rule_name"]').val();
            var id = $rule.find('[name="rule_id"]').val();
            var relRule = $rule.find('[name="rule_rel"]').val();
            var rule = {
                id: id,
                rel_rule: relRule,
                name: name,
                groups: []
            };
            
            var groups = [];
            $rule.find('.arpl-rule-group').each(function(){
                var group = {
                    op: $(this).find('.arpl-rule-group-op select').val()
                };
                var conditions = [];
                $(this).find('.arpl-rule-condition').each(function(){
                    var condition = {
                        type: $(this).find('[name="type"]').val(),
                        id_feature: $(this).find('[name="id_feature"]').val(),
                        id_feature_value: $(this).find('[name="id_feature_value"]').val(),
                        id_category: $(this).find('[name="id_category"]').val(),
                        id_manufacturer: $(this).find('[name="id_manufacturer"]').val(),
                        status: $(this).find('[name="condition_status"]').val(),
                        op: $(this).find('[name="op"]').val(),
                    };
                    conditions.push(condition);
                });
                group.conditions = conditions;
                groups.push(group);
            });
            rule.groups = groups;
            arPL.blockUI($rule);
            $.ajax({
                type: 'POST',
                url: arPL.rules.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'save',
                    controller : 'AdminArPlRules',
                    rule: rule,
                    ajax : true
                },
                success: function(data){
                    arPL.unblockUI($rule);
                    if (data.success){
                        $rule.find('[name="rule_id"]').val(data.id);
                        $rule.find('[name="rule_name"]').val(data.name);
                        arPL.rules.loadRules();
                        showSuccessMessage('Rule saved');
                    }else{
                        var errors = data.errors.join('');
                        showErrorMessage(errors);
                    }
                }
            }).fail(function(){
                arPL.unblockUI($rule);
                showErrorMessage(arPL.errorMessage);
            });
        },
        remove: function($el){
            if ($el.find('[name="rule_id"]').val()){
                if (confirm('Are you sure you want delete this rule?')) {
                    arPL.blockUI('#arpl-rules');
                    $.ajax({
                        type: 'POST',
                        url: arPL.rules.ajaxUrl,
                        dataType: 'json',
                        data: {
                            action : 'remove',
                            controller : 'AdminArPlRules',
                            id: $el.find('[name="rule_id"]').val(),
                            ajax : true
                        },
                        success: function(data){
                            arPL.unblockUI('#arpl-rules');
                            $el.remove();
                            arPL.rules.reload();
                        }
                    }).fail(function(){
                        arPL.unblockUI('#arpl-rules');
                        showErrorMessage(arPL.errorMessage);
                    });
                }
            }else{
                $el.remove();
            }
            
        },
        changeFeature: function(feature, $el){
            arPL.rules.loadFeatureValues(feature, $el);
        },
        changeType: function(type, $el){
            switch(type){
                case 'feature':
                    $el.find('.arpl-rule-type-feature').removeClass('hidden');
                    $el.find('.arpl-rule-type-category').addClass('hidden');
                    $el.find('.arpl-rule-type-manufacturer').addClass('hidden');
                    arPL.rules.loadFeatures($el);
                    break;
                case 'category':
                    $el.find('.arpl-rule-type-feature').addClass('hidden');
                    $el.find('.arpl-rule-type-category').removeClass('hidden');
                    $el.find('.arpl-rule-type-manufacturer').addClass('hidden');
                    arPL.rules.loadCategories($el);
                    break;
                case 'manufacturer':
                    $el.find('.arpl-rule-type-feature').addClass('hidden');
                    $el.find('.arpl-rule-type-category').addClass('hidden');
                    $el.find('.arpl-rule-type-manufacturer').removeClass('hidden');
                    arPL.rules.loadManufacturers($el);
                    break;
            }
        },
        loadRules: function(){
            arPL.blockUI('#arpl-rules');
            $.ajax({
                type: 'POST',
                url: arPL.rules.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'loadRules',
                    controller : 'AdminArPlRules',
                    ajax : true,
                },
                success: function(data){
                    arPL.unblockUI('#arpl-rules');
                    $('#arpl-rules .arpl-rule').each(function(){
                        var $select = $(this).find('[name="rule_rel"]');
                        var relRule = $select.val();
                        var id = $(this).find('[name="rule_id"]').val();
                        
                        $select.html('');
                        $select.append($('<option>', {
                            value: 0
                        }).text('-- Not linked --'));
                        $.each(data, function(i){
                            if (id != data[i].id_rule) {
                                $select.append($('<option>', {
                                    value: data[i].id_rule
                                }).text(data[i].name));
                            }
                        });
                        if (!relRule){
                            $select.val(0);
                        }else{
                            $select.val(relRule);
                        }
                    });
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-rules');
                showErrorMessage(arPL.errorMessage);
            });
        },
        loadFeatureValues: function(id, $el){
            arPL.blockUI('#arpl-rules');
            $.ajax({
                type: 'POST',
                url: arPL.rules.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'loadFeatureValues',
                    controller : 'AdminArPlRules',
                    feature: id,
                    ajax : true,
                },
                success: function(data){
                    arPL.unblockUI('#arpl-rules');
                    var $control = $el.find('[name="id_feature_value"]');
                    $control.html('');
                    $.each(data, function(i){
                        $control.append($('<option>', {
                            value: data[i].id_feature_value
                        }).text(data[i].value));
                    });
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-rules');
                showErrorMessage(arPL.errorMessage);
            });
        },
        loadFeatures: function($el){
            arPL.blockUI('#arpl-rules');
            $.ajax({
                type: 'POST',
                url: arPL.rules.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'loadFeatures',
                    controller : 'AdminArPlRules',
                    ajax : true,
                },
                success: function(data){
                    arPL.unblockUI('#arpl-rules');
                    var $control = $el.find('[name="id_feature"]');
                    $control.html('');
                    $.each(data, function(i){
                        $control.append($('<option>', {
                            value: data[i].id_feature
                        }).text(data[i].name));
                    });
                    arPL.rules.loadFeatureValues($el.find('[name="id_feature"]').val(), $el);
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-rules');
                showErrorMessage(arPL.errorMessage);
            });
        },
        loadCategories: function($el){
            arPL.blockUI('#arpl-rules');
            $.ajax({
                type: 'POST',
                url: arPL.rules.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'loadCategories',
                    controller : 'AdminArPlRules',
                    ajax : true
                },
                success: function(data){
                    arPL.unblockUI('#arpl-rules');
                    var $control = $el.find('[name="id_category"]');
                    $control.html('');
                    $.each(data, function(i){
                        $control.append($('<option>', {
                            value: data[i].id_category
                        }).text(data[i].name));
                    });
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-rules');
                showErrorMessage(arPL.errorMessage);
            });
        },
        loadManufacturers: function($el){
            arPL.blockUI('#arpl-rules');
            $.ajax({
                type: 'POST',
                url: arPL.rules.ajaxUrl,
                dataType: 'json',
                data: {
                    action : 'loadManufacturers',
                    controller : 'AdminArPlRules',
                    ajax : true
                },
                success: function(data){
                    arPL.unblockUI('#arpl-rules');
                    var $control = $el.find('[name="id_manufacturer"]');
                    $control.html('');
                    $.each(data, function(i){
                        $control.append($('<option>', {
                            value: data[i].id_manufacturer
                        }).text(data[i].name));
                    });
                }
            }).fail(function(){
                arPL.unblockUI('#arpl-rules');
                showErrorMessage(arPL.errorMessage);
            });
        },
        toggleCondition: function($el){
            $el.toggleClass('active');
            if ($el.hasClass('active')){
                $el.find('[name="condition_status"]').val(1);
            }else{
                $el.find('[name="condition_status"]').val(0);
            }
        },
        addCondition: function($el){
            var $condition = $('.arpl-rule-empty .arpl-rule-group .arpl-rule-condition').clone();
            arPL.rules.changeType('feature', $condition);
            $el.after($condition);
        },
        removeCondition: function($el){
            $group = $el.parents('.arpl-rule-group');
            $el.remove();
            if ($group.find('.arpl-rule-condition').length == 0) {
                $group.remove();
            }
        },
        addGroup: function($el){
            var $group = $('.arpl-rule-empty .arpl-rule-group').clone();
            var $condition = $group.find('.arpl-rule-condition');
            arPL.rules.changeType('feature', $condition);
            $el.find('.arplr-rule-content').append($group);
        }
    },
    blockUI: function(selector){
        $(selector).addClass('ar-blocked');
        $(selector).find('.ar-loading').remove();
        $(selector).append('<div class="ar-loading"><div class="ar-loading-inner">Loading...</div></div>');
    },
    unblockUI: function(selector){
        $(selector).find('.ar-loading').remove();
        $(selector).removeClass('ar-blocked');
    },
    processErrors: function(form, data){
        arPL.clearErrors();
        if (data.success == 0){
            $.each(data.errors, function(index){
                $(form + '_'+index).parents('.form-group').addClass('has-error');
                $(form + '_'+index).parents('.form-group').find('.errors').text(data.errors[index]);
                if ($(form + '_'+index).parents('.tab-pane').length){
                    var tabId = $(form + '_'+index).parents('.tab-pane').attr('id');
                    $('.nav-tabs li>a[href="#' + tabId + '"]').parent().addClass('has-error');
                }
            });
            showErrorMessage(arPL.errorMessage);
            return true;
        }
        return false;
    },
    _getFormData: function(form, all){
        var params = [];
        var selector = '';
        if (all){
            selector = form + ' input, ' + form + ' select, ' + form + ' textarea';
        }else{
            selector = form + ' [data-serializable="true"]'  
        }
        $(selector).each(function(){
            var val = $(this).val();
            if ($(this).attr('type') == 'checkbox'){
                val = $(this).is(':checked')? $(this).val() : 0;
            }
            if ($(this).attr('type') == 'radio') {
                var name = $(this).attr('name');
                val = $(form + ' ' + '[name="' + name + '"]:checked').val();
            }
            params.push({
                name: $(this).attr('name'),
                value: val
            });
        });
        return params;
    },
    clearErrors: function(form){
        $(form + ' .form-group.has-error').removeClass('has-error');
        $(form + ' .nav-tabs .has-error').removeClass('has-error');
    },
    resetForm: function(form){
        arPL.clearErrors(form);
        $(form + ' [data-default').each(function(){
            var attr = $(this).attr('data-default');
            if (typeof attr !== typeof undefined && attr !== false) {
                if ($(this).attr('type') == 'checkbox'){
                    if ($(this).data('default') == 1){
                        $(this).prop('checked', 'true');
                    }else{
                        $(this).removeProp('checked');
                    }
                }else{
                    $(this).val($(this).data('default'));
                }
            }
        });
    },
    populateForm: function(form, data){
        $.each(data, function(i){
            var fieldId = form + '_' + i;
            if (typeof data[i] == 'object'){
                if (data[i] != null){
                    $.each(data[i], function(id_lang){
                        if ($(fieldId + '_' + id_lang).length && $(fieldId + '_' + id_lang).prop('tagName') != 'DIV'){
                            if ($(fieldId + '_' + id_lang).attr('type') == 'radio'){
                                $(fieldId + '_' + id_lang + '[value="' + data[i][id_lang] + '"]').prop('checked', 'true');
                            }else{
                                $(fieldId + '_' + id_lang).val(data[i][id_lang]);
                            }
                        }else if($(form).find('[name="' + i + '.' + id_lang + '"]').length){
                            if ($(form).find('[name="' + i + '.' + id_lang + '"]').attr('type') == 'radio'){
                                if ($(fieldId + '_' + id_lang + '_on').length){
                                    if (data[i][id_lang]){
                                        $(fieldId + '_' + id_lang + '_on').prop('checked', 'true');
                                    }else{
                                        $(fieldId + '_' + id_lang + '_off').prop('checked', 'true');
                                    }
                                }else{
                                    $(form).find('[name="' + i + '.' + id_lang + '"][value="' + data[i][id_lang] + '"]').prop('checked', 'true');
                                    $(form).find('.tree-item-name.tree-selected').removeClass('tree-selected');
                                    $(form).find('[name="' + i + '.' + id_lang + '"][value="' + data[i][id_lang] + '"]').parents('.tree-item-name').addClass('tree-selected');
                                    $('#collapse-all-arpl-categories').show();
                                    $('#expand-all-arpl-categories').hide();
                                }
                            }
                        }
                    });
                }
            }else{
                if ($(fieldId).attr('type') == 'checkbox'){
                    if (data[i] == 1){
                        $(fieldId).prop('checked', 'true');
                    }else{
                        $(fieldId).removeProp('checked');
                    }
                }else{
                    $(fieldId).val(data[i]);
                }
            }
        });
    }
};