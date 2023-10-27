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
    $(document ).ajaxComplete(function( event, xhr, settings ) {
        if(xhr.responseText && xhr.responseText.indexOf("cart_detailed")>=0)
        {
            setTimeout(function(){ets_mpDislayShopNameInShoppingCart();},500);
        }
    });
    ets_mpDislayShopNameInShoppingCart();
    if($('#cart_summary .shop_seller_link').length)
    {
        $('#cart_summary .shop_seller_link').each(function(){
           $(this).closest('tr.cart_item').find('.cart_description .product-name').after($(this).html()); 
        });
        $('#cart_summary .shop_seller_link').remove();
    }
    if($('#cart-summary-product-list .shop_seller_link').length)
    {
        $('#cart-summary-product-list .shop_seller_link').each(function(){
           $(this).parent().find('.product-name').append('<br/>'+$(this).html()); 
        });
        $('#cart-summary-product-list .shop_seller_link').remove();
    }
});
function ets_mpDislayShopNameInShoppingCart()
{
    if($('#list-sellers-in-cart .product').length)
    {
        $('#list-sellers-in-cart .product').each(function(){
           var id_product = $(this).data('id-product');
           if($('.cart-item .remove-from-cart[data-id-product="'+id_product+'"]').length)
           {
               $('.cart-item .remove-from-cart[data-id-product="'+id_product+'"]').closest('li.cart-item').find('.product-line-info > a').after('<br />'+ $('#list-sellers-in-cart .product[data-id-product="'+id_product+'"] .shop_seller_link').html()) 
           } 
        });
    }
}