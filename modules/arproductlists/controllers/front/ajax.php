<?php
/**
* 2012-2019 Areama
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
*  @author    Areama <support@areama.net>
*  @copyright 2019 Areama
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Areama
*/

include_once dirname(__FILE__).'/../../classes/ArProductListGroup.php';
include_once dirname(__FILE__).'/../../classes/ArProductListRel.php';
include_once dirname(__FILE__).'/../../classes/ArPLTools.php';

use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;

/**
 * @property ArProductLists $module
 */
class ArProductListsAjaxModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    
    /**
    * @see FrontController::initContent()
    */
    public function initContent()
    {
        $action = Tools::getValue('action');
        $return = array();
        if ('showModal' == $action) {
            $return = $this->showModal();
        } elseif ('loadTab' == $action) {
            $id = Tools::getValue('id');
            $groupId = Tools::getValue('group_id');
            $productId = Tools::getValue('product_id');
            $return = $this->loadTab($groupId, $id, $productId);
        }
        die(ArPLTools::jsonEncode($return));
    }
    
    public function loadTab($groupId, $id, $productId)
    {
        $id_lang = Context::getContext()->language->id;
        $model = new ArProductListRel($id, $id_lang);
        $groupModel = new ArProductListGroup($groupId);
        $product = null;
        if ($productId) {
            $product = new Product($productId, false, $id_lang);
        }
        $listContent = $this->module->renderList($groupModel, $model, $product, $this);
        return array(
            'model' => $model,
            'title' => $model->title,
            'responsiveBreakdowns' => $model->getList()->getResponsiveBreakdowns(),
            'content' => $listContent
        );
    }
    
    public function showModal()
    {
        $id_lang = Context::getContext()->language->id;
        $ids = Tools::getValue('ids');
        $cartRuleId = Tools::getValue('cartRuleId');
        $cartRule = new CartRule($cartRuleId, $id_lang);
        $cart = Context::getContext()->cart;
        $products = array();
        $data = (new CartPresenter)->present($cart);
        foreach ($data['products'] as $product) {
            if (in_array($product['id_product'], $ids)) {
                $products[] = $product;
            }
        }
        return array(
            'content' => $this->module->render('modal.tpl', array(
                'products' => $products,
                'cartRule' => $cartRule,
                'cart' => $data,
                'cart_url' => $this->getCartSummaryURL()
            ))
        );
    }
    
    private function getCartSummaryURL()
    {
        return $this->context->link->getPageLink(
            'cart',
            null,
            $this->context->language->id,
            array(
                'action' => 'show'
            ),
            false,
            null,
            true
        );
    }
}
