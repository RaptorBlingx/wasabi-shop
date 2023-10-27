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

include_once dirname(__FILE__).'/ArPLRendererAbstract.php';

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class ArPLRenderer17 extends ArPLRendererAbstract
{
    
    public function render($view, $group, $model, $product, $initSlider = false)
    {
        $model->getList()->setProduct($product);
        $products = $model->getProductList();
        
        if (empty($products)) {
            return null;
        }
        $sliderConfig = $this->module->getSliderConfig();
        $ajax = false;
        if ($model->getList()->ajax && !in_array($model->class, array('ArPLPromotions', 'ArPLPromotionsWithProduct')) && $group->type == 'tabbed') {
            $ajax = true;
        }
        $params = array(
            'group' => $group,
            'ajax' => $ajax,
            'model' => $model,
            'title' => ($group->type == 'tabbed')? null : $model->title,
            'products' => $this->getProductsForTemplate($products),
            'link' => Context::getContext()->link,
            'pageNumber' => 0,
            'sliderConfig' => $sliderConfig,
            'initSlider' => $initSlider,
            'contentOnly' => (int)Tools::getValue('arPLContentOnly')
        );
        if ($this->controller) {
            $params['customer'] = $this->controller->getTemplateVarCustomer();
            $params['currency'] = $this->controller->getTemplateVarCurrency();
        }
        return $this->module->render($view, $params);
    }
    
    public function renderBrandList($view, $group, $model, $product, $initSlider = false)
    {
        $model->getList()->setProduct($product);
        $brands = $model->getBrandsList();
        if (empty($brands)) {
            return null;
        }
        $sliderConfig = $this->module->getSliderConfig();
        $ajax = false;
        if ($model->getList()->ajax && $group->type == 'tabbed') {
            $ajax = true;
        }
        $image_types = ImageType::getImagesTypes('manufacturers');
        $imgType = null;
        $imgWidth = null;
        $imgHeight = null;
        foreach ($image_types as $image_type) {
            if ($image_type['id_image_type'] == $model->getList()->brand_thumb_size) {
                $imgType = $image_type['name'];
                $imgWidth = $image_type['width'];
                $imgHeight = $image_type['height'];
            }
        }
        $params = array(
            'group' => $group,
            'imgType' => $imgType,
            'imgWidth' => $imgWidth,
            'imgHeight' => $imgHeight,
            'ajax' => $ajax,
            'model' => $model,
            'title' => ($group->type == 'tabbed')? null : $model->title,
            'brands' => $brands,
            'pageNumber' => 0,
            'link' => Context::getContext()->link,
            'sliderConfig' => $sliderConfig,
            'initSlider' => $initSlider,
            'contentOnly' => (int)Tools::getValue('arPLContentOnly')
        );
        
        if ($this->controller) {
            $params['customer'] = $this->controller->getTemplateVarCustomer();
            $params['currency'] = $this->controller->getTemplateVarCurrency();
        }
        
        return $this->module->render($view, $params);
    }
    
    public function renderCategoryList($view, $group, $model, $product, $initSlider = false)
    {
        $model->getList()->setProduct($product);
        $categories = $model->getCategoriesList();
        if (empty($categories)) {
            return null;
        }
        $sliderConfig = $this->module->getSliderConfig();
        $ajax = false;
        if ($model->getList()->ajax && $group->type == 'tabbed') {
            $ajax = true;
        }
        $image_types = ImageType::getImagesTypes('categories');
        $imgType = null;
        $imgWidth = null;
        $imgHeight = null;
        foreach ($image_types as $image_type) {
            if ($image_type['id_image_type'] == $model->getList()->thumb_size) {
                $imgType = $image_type['name'];
                $imgWidth = $image_type['width'];
                $imgHeight = $image_type['height'];
            }
        }
        
        $params = array(
            'group' => $group,
            'imgType' => $imgType,
            'imgWidth' => $imgWidth,
            'imgHeight' => $imgHeight,
            'ajax' => $ajax,
            'model' => $model,
            'title' => ($group->type == 'tabbed')? null : $model->title,
            'categories' => $categories,
            'pageNumber' => 0,
            'link' => Context::getContext()->link,
            'sliderConfig' => $sliderConfig,
            'initSlider' => $initSlider,
            'contentOnly' => (int)Tools::getValue('arPLContentOnly')
        );
        
        if ($this->controller) {
            $params['customer'] = $this->controller->getTemplateVarCustomer();
            $params['currency'] = $this->controller->getTemplateVarCurrency();
        }
        
        return $this->module->render($view, $params);
    }
    
    protected function getFactory()
    {
        return new ProductPresenterFactory(Context::getContext(), new TaxConfiguration());
    }

    protected function getProductPresentationSettings()
    {
        return $this->getFactory()->getPresentationSettings();
    }

    protected function getProductPresenter()
    {
        return $this->getFactory()->getPresenter();
    }
    
    protected function getProductsForTemplate($products)
    {
        $context = Context::getContext();
        
        if (empty($products)) {
            return null;
        }
        $assembler = new ProductAssembler($context);

        $presenter = $this->getProductPresenter();

        $products_for_template = [];

        foreach ($products as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $this->getProductPresentationSettings(),
                $assembler->assembleProduct($rawProduct),
                $context->language
            );
        }
        return $products_for_template;
    }


    public function renderPromotions($view, $groupModel, $model, $product, $initSlider = false)
    {
        $data = array();
        $model->getList()->setProduct($product);
        $list = $model->getProductList();
        if (empty($list)) {
            return null;
        }
        $sliderConfig = $this->module->getSliderConfig();
        foreach ($list as $k => $promo) {
            $groupNumber = 0;
            $test = array();
            foreach ($promo['groups'] as $kk => $group) {
                $groupNumber ++;
                $data[$k]['cart_rule'] = $promo['cart_rule'];
                $data[$k]['groups'][$kk] = $this->getProductsForTemplate($group);
                foreach ($group as $product) {
                    $test[$groupNumber][] = $product['id_product'];
                }
            }
            $a = array();
            if (isset($test[2])) {
                foreach ($test[1] as $v) {
                    foreach ($test[2] as $vv) {
                        $oldPrice = $this->getOldPrice(array($v, $vv));
                        $actualPrice = $this->getActualPrice(array($v, $vv), $promo);
                        $a[] = array(
                            'key' => implode('-', array($v, $vv)),
                            'ids' => array($v, $vv),
                            'oldPrice' => Tools::displayPrice($oldPrice),
                            'actualPrice' => Tools::displayPrice($actualPrice),
                            'save'=> Tools::displayPrice($oldPrice - $actualPrice)
                        );
                    }
                }
            }
            $data[$k]['prices'] = $a;
        }
        
        $params = array(
            'groupModel' => $groupModel,
            'model' => $model,
            'title' => ($groupModel->type == 'tabbed')? null : $model->title,
            'list' => $data,
            'static_token' => Tools::getToken(false),
            'pageNumber' => 0,
            'sliderConfig' => $sliderConfig,
            'initSlider' => $initSlider,
            'contentOnly' => (int)Tools::getValue('arPLContentOnly')
        );
        
        if ($this->controller) {
            $params['customer'] = $this->controller->getTemplateVarCustomer();
            $params['currency'] = $this->controller->getTemplateVarCurrency();
        }
        
        return $this->module->render($view, $params);
    }
    
    public function getActualPrice($ids, $cartRule)
    {
        $oldPrice = $this->getOldPrice($ids);
        return $oldPrice - ($oldPrice * $cartRule['cart_rule']['reduction_percent'] / 100);
    }
    
    public function getOldPrice($ids)
    {
        $price = 0;
        foreach ($ids as $id) {
            $price += Product::getPriceStatic($id, true, null, 2, '.', false, true);
        }
        return $price;
    }
}
