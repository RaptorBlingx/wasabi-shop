/**
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
 */
$(document).ready(function(){
    if($('#hook-display-after-carrier .ets-mp-delivery-options-list').length && $('#checkout-delivery-step.js-current-step').length)
    {
        if(!$('.delivery-options-list .delivery-options').prev('.ets-mp-delivery-options-list').length)
            $('.delivery-options-list .delivery-options').before('<div class="ets-mp-delivery-options-list">'+$('#hook-display-after-carrier .ets-mp-delivery-options-list').html()+'</div>');
        else
            $('.delivery-options-list .delivery-options').prev('.ets-mp-delivery-options-list').html($('#hook-display-after-carrier .ets-mp-delivery-options-list').html());
        $('#hook-display-after-carrier .ets-mp-delivery-options-list').remove();
        $('.delivery-options-list .delivery-options').hide();
        etsmp_changeDeliveryOptions();
    }
    $(document).on('click','#checkout-delivery-step .step-edit',function(){
        if($('#hook-display-after-carrier .ets-mp-delivery-options-list').length)
        {
            if(!$('.delivery-options-list .delivery-options').prev('.ets-mp-delivery-options-list').length)
                $('.delivery-options-list .delivery-options').before('<div class="ets-mp-delivery-options-list">'+$('#hook-display-after-carrier .ets-mp-delivery-options-list').html()+'</div>');
            else
                $('.delivery-options-list .delivery-options').prev('.ets-mp-delivery-options-list').html($('#hook-display-after-carrier .ets-mp-delivery-options-list').html());
            $('#hook-display-after-carrier .ets-mp-delivery-options-list').remove();
            $('.delivery-options-list .delivery-options').hide();
            etsmp_changeDeliveryOptions();
        }
    });
    if($('#hook-display-after-carrier .ets-mp-delivery-options-list').length && $('#form_ets_onepagecheckout').length)
    {
        
        if(!$('.delivery-options-list .delivery-options').prev('.ets-mp-delivery-options-list').length)
            $('.delivery-options-list .delivery-options').before('<div class="ets-mp-delivery-options-list">'+$('#hook-display-after-carrier .ets-mp-delivery-options-list').html()+'</div>');
        else
            $('.delivery-options-list .delivery-options').prev('.ets-mp-delivery-options-list').html($('#hook-display-after-carrier .ets-mp-delivery-options-list').html());
        $('#hook-display-after-carrier .ets-mp-delivery-options-list').remove();
        $('.delivery-options-list .delivery-options').hide();
        etsmp_changeDeliveryOptions();
    }
    $(document).on('hooksShippingLoaded',function(){
        if($('#hook-display-after-carrier .ets-mp-delivery-options-list').length)
        {
            
            if(!$('.delivery-options-list .delivery-options').prev('.ets-mp-delivery-options-list').length)
                $('.delivery-options-list .delivery-options').before('<div class="ets-mp-delivery-options-list">'+$('#hook-display-after-carrier .ets-mp-delivery-options-list').html()+'</div>');
            else
                $('.delivery-options-list .delivery-options').prev('.ets-mp-delivery-options-list').html($('#hook-display-after-carrier .ets-mp-delivery-options-list').html());
            $('#hook-display-after-carrier .ets-mp-delivery-options-list').remove();
            $('.delivery-options-list .delivery-options').hide();
            etsmp_changeDeliveryOptions();
        }
    });
    $(document).on('change','.ets-carriers-selected',function(){
        etsmp_changeDeliveryOptions();
    });
});
function etsmp_changeDeliveryOptions()
{
    if($('.ets-carriers-selected').length)
    {
        var $keys = [];
        var $index = 0;
        $('.ets-carriers-selected').each(function(){
            if(!etsmp_inArray($(this).val(),$keys))
            {
                $keys[$index] = $(this).val();
                $index++;
            }
            
        });
        var id_carriers = '';
        for(var i = 0; i < $keys.length; i++) {
            id_carriers +=$keys[i]+',';
        }
        $('.delivery-option input[value="'+id_carriers+'"]').click();
    }
}
function etsmp_inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}