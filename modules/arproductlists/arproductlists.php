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

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__).'/classes/ArPLInstaller.php';
include_once dirname(__FILE__).'/classes/ArPLListAbstract.php';

include_once dirname(__FILE__).'/classes/ArPLGeneralConfig.php';
include_once dirname(__FILE__).'/classes/ArPLSliderConfig.php';
include_once dirname(__FILE__).'/classes/ArPLPromoConfig.php';
include_once dirname(__FILE__).'/classes/ArPLTabsConfig.php';
include_once dirname(__FILE__).'/classes/ArPLSectionConfig.php';
include_once dirname(__FILE__).'/classes/ArProductViews.php';
include_once dirname(__FILE__).'/classes/ArCategoryViews.php';

class ArProductLists extends Module
{
    const REMIND_TO_RATE = 259200; // 3 days
    const ADDONS_ID = 45796;
    const AUTHOR_ID = 675406;
    
    protected $html;
    protected $installer = null;
    protected $isMobile = null;


    protected $generalConfig;
    protected $sliderConfig;
    protected $promoConfig;
    protected $tabsConfig;
    protected $sectionConfig;

    protected static $langs = null;

    public function __construct()
    {
        $this->name = 'arproductlists';
        $this->tab = 'front_office_features';
        $this->version = '1.9.1';
        $this->author = 'Areama';
        $this->controllers = array('ajax');
        $this->need_instance = 0;
        $this->bootstrap = true;
        if ($this->is17() || $this->is8x()) {
            $this->ps_versions_compliancy = array(
                'min' => '1.7',
                'max' => _PS_VERSION_
            );
        }
        $this->module_key = 'c0ec7f0e5cfd57faca75169e9849109d';
        parent::__construct();

        $this->displayName = $this->l('Product and category slider PRO + related producs');
        $this->description = $this->l('Add responsive and mobile-friendly product and category sliders with related products anywhere to your site.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete all data?');
    }
    
    /**
     * @return ArPLSliderConfig
     */
    public function getSliderConfig()
    {
        if (empty($this->sliderConfig)) {
            $this->sliderConfig = new ArPLSliderConfig($this, 'arpls_');
        }
        if (!$this->sliderConfig->isLoaded()) {
            $this->sliderConfig->loadFromConfig();
        }
        return $this->sliderConfig;
    }
    
    public function renderGroups($groups, $product = null, $initSlider = false)
    {
        if (empty($this->generalConfig)) {
            $this->generalConfig = new ArPLGeneralConfig($this, 'arplg_');
        }
        if (!$this->generalConfig->isLoaded()) {
            $this->generalConfig->loadFromConfig();
        }
        
        if ($this->generalConfig->sandbox) {
            $ips = explode("\r\n", $this->generalConfig->allowed_ips);
            if (!in_array($this->generalConfig->getCurrentIP(), $ips)) {
                return null;
            }
        }
        $id_lang = Context::getContext()->language->id;
        $controllerId = $this->getControllerId();
        $categoryId = null;
        if ($controllerId == 'category') {
            $categoryId = $this->context->controller->getCategory()->id;
        }
        
        $content = '';
        foreach ($groups as $group) {
            $lists = array();
            $groupModel = new ArProductListGroup($group->id);
            foreach ($groupModel->getLists($id_lang, $this->isMobile()) as $list) {
                $model = new ArProductListRel($list['id_rel'], $id_lang);
                if (is_array($model->getList()->category_restrictions) && !empty($model->getList()->category_restrictions) && $controllerId == 'category' && !in_array($categoryId, $model->getList()->category_restrictions)) {
                    // nothing to do. Skip this list
                } else {
                    if ($model->getList()->ajax && !in_array($model->class, array('ArPLPromotions', 'ArPLPromotionsWithProduct')) && $groupModel->type == 'tabbed') {
                        $listContent = $this->render('_partials/_loader.tpl');
                        $ajax = true;
                    } else {
                        $listContent = $this->renderList($groupModel, $model, $product, $this->context->controller, $initSlider);
                        $ajax = false;
                    }
                    if ($listContent || $ajax) {
                        $lists[] = array(
                            'ajax' => $ajax,
                            'model' => $model,
                            'title' => $model->title,
                            'content' => $listContent
                        );
                    }
                }
            }
            if ($groupModel->type == 'tabbed') {
                $view = 'group_tabbed.tpl';
            } else {
                $view = 'group.tpl';
            }
            $content .= $this->render($view, array(
                'model' => $groupModel,
                'lists' => $lists
            ));
        }
        return $content;
    }
    
    public function renderList(ArProductListGroup $group, ArProductListRel $model, $product = null, $controller = null, $initSlider = false)
    {
        $viewPrefix = '';
        if ($this->is17() || $this->is8x()) {
            include_once dirname(__FILE__).'/classes/ArPLRenderer17.php';
            $renderer = new ArPLRenderer17($this, $controller);
        } else {
            $viewPrefix = 'ps16/';
            include_once dirname(__FILE__).'/classes/ArPLRenderer.php';
            $renderer = new ArPLRenderer($this, $controller);
        }
        if (($model->getList() instanceof ArPLPromotions) || ($model->getList() instanceof ArPLPromotionsWithProduct)) {
            return $renderer->renderPromotions($viewPrefix.'promo-list.tpl', $group, $model, $product);
        } else {
            if ($model->getList()->isProductList()) {
                return $renderer->render($viewPrefix.'list.tpl', $group, $model, $product, $initSlider);
            } elseif ($model->getList()->isCategoryList()) {
                if ($model->getList()->isCompact()) {
                    return $renderer->renderCategoryList($viewPrefix.'category-list-compact.tpl', $group, $model, $product, $initSlider);
                } else {
                    return $renderer->renderCategoryList($viewPrefix.'category-list.tpl', $group, $model, $product, $initSlider);
                }
            } elseif ($model->getList()->isBrandList()) {
                if ($model->getList()->isCompact()) {
                    return $renderer->renderBrandList($viewPrefix.'brand-list-compact.tpl', $group, $model, $product, $initSlider);
                } else {
                    return $renderer->renderBrandList($viewPrefix.'brand-list.tpl', $group, $model, $product, $initSlider);
                }
            }
        }
    }
    
    public function addViewedCategory($idCategory)
    {
        $arr = array();
        if (isset($this->context->cookie->arpl_viewed_cat)) {
            $arr = explode(',', $this->context->cookie->arpl_viewed_cat);
        }

        if (!in_array($idCategory, $arr)) {
            $arr[] = $idCategory;

            $this->context->cookie->arpl_viewed_cat = trim(implode(',', $arr), ',');
        }
        ArCategoryViews::viewCategory($idCategory);
    }
    
    public function addViewedProduct($idProduct)
    {
        $arr = array();
        if (isset($this->context->cookie->arpl_viewed)) {
            $arr = explode(',', $this->context->cookie->arpl_viewed);
        }

        if (!in_array($idProduct, $arr)) {
            $arr[] = $idProduct;

            $this->context->cookie->arpl_viewed = trim(implode(',', $arr), ',');
        }
        ArProductViews::viewProduct($idProduct);
    }
    
    public function hookDisplayCartModalContent($params)
    {
        $groups = ArProductListGroup::getByHook('displayCartModalContent', Context::getContext()->shop->id, $this->isMobile());
        $product = null;
        if (isset($params['product']) && $params['product']) {
            if (is_object($params['product'])) {
                $product = $params['product'];
            } else if (is_array($params['product']) && isset($params['product']['id_product'])) {
                $product = new Product($params['product']['id_product'], false, Context::getContext()->language->id);
            }
        }
        $this->addViewedProduct($product->id);
        return $this->renderGroups($groups, $product, true);
    }
    
    public function hookDisplayCartModalFooter($params)
    {
        $groups = ArProductListGroup::getByHook('displayCartModalFooter', Context::getContext()->shop->id, $this->isMobile());
        $product = null;
        if (isset($params['product']) && $params['product']) {
            if (is_object($params['product'])) {
                $product = $params['product'];
            } else if (is_array($params['product']) && isset($params['product']['id_product'])) {
                $product = new Product($params['product']['id_product'], false, Context::getContext()->language->id);
            }
        }
        $this->addViewedProduct($product->id);
        return $this->renderGroups($groups, $product, true);
    }
    
    public function hookDisplayFooterProduct($params)
    {
        $groups = ArProductListGroup::getByHook('displayFooterProduct', Context::getContext()->shop->id, $this->isMobile());
        $product = $this->context->controller->getProduct();
        if ($product) {
            $this->addViewedProduct($product->id);
        }
        return $this->renderGroups($groups, $product);
    }
    
    public function hookDisplayProductAdditionalInfo($params)
    {
        $groups = ArProductListGroup::getByHook('displayProductAdditionalInfo', Context::getContext()->shop->id, $this->isMobile());
        $product = $this->context->controller->getProduct();
        return $this->renderGroups($groups, $product) . $this->render('product_additional_info.tpl', array(
            'ipa' => $params['product']->id_product_attribute,
            'id' => $params['product']->id
        ));
    }
    
    public function hookDisplayLeftColumn($params)
    {
        if ($this->getControllerId() == 'product') {
            $product = $this->context->controller->getProduct();
            $groups = ArProductListGroup::getByHook('displayLeftColumn', Context::getContext()->shop->id, $this->isMobile());
            return $this->renderGroups($groups, $product);
        } elseif ($this->getControllerId() == 'category') {
            $groups = ArProductListGroup::getByHook('displayLeftColumn', Context::getContext()->shop->id, $this->isMobile());
            return $this->renderGroups($groups);
        }
    }
    
    public function hookDisplayReassurance($params)
    {
        if ($this->getControllerId() == 'product') {
            $product = $this->context->controller->getProduct();
            $groups = ArProductListGroup::getByHook('displayReassurance', Context::getContext()->shop->id, $this->isMobile());
            return $this->renderGroups($groups, $product);
        }
    }
    
    public function hookArProductPageHook1($params)
    {
        if ($this->getControllerId() == 'product') {
            $product = $this->context->controller->getProduct();
            $groups = ArProductListGroup::getByHook('arProductPageHook1', Context::getContext()->shop->id, $this->isMobile());
            return $this->renderGroups($groups, $product);
        }
    }
    
    public function hookArProductPageHook2($params)
    {
        if ($this->getControllerId() == 'product') {
            $product = $this->context->controller->getProduct();
            $groups = ArProductListGroup::getByHook('arProductPageHook2', Context::getContext()->shop->id, $this->isMobile());
            return $this->renderGroups($groups, $product);
        }
    }
    
    public function hookArProductPageHook3($params)
    {
        if ($this->getControllerId() == 'product') {
            $product = $this->context->controller->getProduct();
            $groups = ArProductListGroup::getByHook('arProductPageHook3', Context::getContext()->shop->id, $this->isMobile());
            return $this->renderGroups($groups, $product);
        }
    }
    
    public function hookDisplayShoppingCartFooter($params)
    {
        $groups = ArProductListGroup::getByHook('displayShoppingCartFooter', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookDisplayShoppingCart($params)
    {
        $groups = ArProductListGroup::getByHook('displayShoppingCart', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArCartPageHook1($params)
    {
        $groups = ArProductListGroup::getByHook('arCartPageHook1', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArCartPageHook2($params)
    {
        $groups = ArProductListGroup::getByHook('arCartPageHook2', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArCartPageHook3($params)
    {
        $groups = ArProductListGroup::getByHook('arCartPageHook3', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookAr404PageHook1($params)
    {
        $groups = ArProductListGroup::getByHook('ar404PageHook1', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookAr404PageHook2($params)
    {
        $groups = ArProductListGroup::getByHook('ar404PageHook2', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookAr404PageHook3($params)
    {
        $groups = ArProductListGroup::getByHook('ar404PageHook3', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    
    public function hookDisplayHome($params)
    {
        $groups = ArProductListGroup::getByHook('displayHome', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookDisplayHomeAfter($params)
    {
        $groups = ArProductListGroup::getByHook('displayHomeAfter', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookDisplayHomeTop($params)
    {
        $groups = ArProductListGroup::getByHook('displayHomeTop', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArHomePageHook1($params)
    {
        $groups = ArProductListGroup::getByHook('arHomePageHook1', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArHomePageHook2($params)
    {
        $groups = ArProductListGroup::getByHook('arHomePageHook2', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArHomePageHook3($params)
    {
        $groups = ArProductListGroup::getByHook('arHomePageHook3', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookDisplayHeaderCategory($params)
    {
        if ($this->getControllerId() != 'category') {
            return null;
        }
        $groups = ArProductListGroup::getByHook('displayHeaderCategory', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookDisplayFooterCategory($params)
    {
        if ($this->getControllerId() != 'category') {
            return null;
        }
        $groups = ArProductListGroup::getByHook('displayFooterCategory', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArCategoryPageHook1($params)
    {
        if ($this->getControllerId() != 'category') {
            return null;
        }
        $groups = ArProductListGroup::getByHook('arCategoryPageHook1', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArCategoryPageHook2($params)
    {
        if ($this->getControllerId() != 'category') {
            return null;
        }
        $groups = ArProductListGroup::getByHook('arCategoryPageHook2', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArCategoryPageHook3($params)
    {
        if ($this->getControllerId() != 'category') {
            return null;
        }
        $groups = ArProductListGroup::getByHook('arCategoryPageHook3', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArCategoryPageHook4($params)
    {
        if ($this->getControllerId() != 'category') {
            return null;
        }
        $groups = ArProductListGroup::getByHook('arCategoryPageHook4', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArCategoryPageHook5($params)
    {
        if ($this->getControllerId() != 'category') {
            return null;
        }
        $groups = ArProductListGroup::getByHook('arCategoryPageHook5', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArCategoryPageHook6($params)
    {
        if ($this->getControllerId() != 'category') {
            return null;
        }
        $groups = ArProductListGroup::getByHook('arCategoryPageHook6', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArCategoryPageHook7($params)
    {
        if ($this->getControllerId() != 'category') {
            return null;
        }
        $groups = ArProductListGroup::getByHook('arCategoryPageHook7', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArCategoryPageHook8($params)
    {
        if ($this->getControllerId() != 'category') {
            return null;
        }
        $groups = ArProductListGroup::getByHook('arCategoryPageHook8', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArCategoryPageHook9($params)
    {
        if ($this->getControllerId() != 'category') {
            return null;
        }
        $groups = ArProductListGroup::getByHook('arCategoryPageHook9', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArCategoryPageHook10($params)
    {
        if ($this->getControllerId() != 'category') {
            return null;
        }
        $groups = ArProductListGroup::getByHook('arCategoryPageHook10', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookDisplayNotFound($params)
    {
        $groups = ArProductListGroup::getByHook('displayNotFound', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookDisplayOrderConfirmation($params)
    {
        $groups = ArProductListGroup::getByHook('displayOrderConfirmation', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookDisplayOrderConfirmation1($params)
    {
        $groups = ArProductListGroup::getByHook('displayOrderConfirmation1', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookDisplayOrderConfirmation2($params)
    {
        $groups = ArProductListGroup::getByHook('displayOrderConfirmation2', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArThankYouPageHook1($params)
    {
        $groups = ArProductListGroup::getByHook('arThankYouPageHook1', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }
    
    public function hookArThankYouPageHook2($params)
    {
        $groups = ArProductListGroup::getByHook('arThankYouPageHook2', Context::getContext()->shop->id, $this->isMobile());
        return $this->renderGroups($groups);
    }


    public function initConfig($load = true)
    {
        $this->generalConfig = new ArPLGeneralConfig($this, 'arplg_');
        if ($load) {
            $this->generalConfig->loadFromConfig();
        }
        
        $this->sliderConfig = new ArPLSliderConfig($this, 'arpls_');
        if ($load) {
            $this->sliderConfig->loadFromConfig();
        }
        
        $this->promoConfig = new ArPLPromoConfig($this, 'arplp_');
        if ($load) {
            $this->promoConfig->loadFromConfig();
        }
        
        $this->tabsConfig = new ArPLTabsConfig($this, 'arplt_');
        if ($load) {
            $this->tabsConfig->loadFromConfig();
        }
        
        $this->sectionConfig = new ArPLSectionConfig($this, 'arpls_');
        if ($load) {
            $this->sectionConfig->loadFromConfig();
        }
    }
    
    public function getControllerId()
    {
        if ($this->context->controller instanceof ProductControllerCore || $this->context->controller instanceof ProductController) {
            return 'product';
        }
        if ($this->context->controller instanceof CategoryControllerCore || $this->context->controller instanceof CategoryController) {
            return 'category';
        }
        return null;
    }
    
    public function hookDisplayHeader($params)
    {
        $this->context->controller->addJS(($this->_path).'views/js/owl.carousel.min.js');
        $this->context->controller->addJS(($this->_path).'views/js/scripts.js');
        $this->context->controller->addCSS(($this->_path).'views/css/owl.carousel.min.css', 'all');
        $this->context->controller->addCSS(($this->_path).'views/css/owl.theme.default.min.css', 'all');
        $this->context->controller->addCSS(($this->_path).'views/css/styles.css', 'all');
        if ($this->is16()) {
            $this->context->controller->addCSS(($this->_path).'views/css/styles16.css', 'all');
        }
        $this->context->controller->addCSS(($this->_path).'views/css/generated.min.css', 'all', null, false);
        $productId = 0;
        $categoryId = 0;
        $ipa = 0;
        if ($this->getControllerId() == 'product') {
            $product = $this->context->controller->getProduct();
            $productId = $product->id;
            $ipa = Tools::getValue('id_product_attribute');
        }
        if ($this->getControllerId() == 'category') {
            $category = $this->context->controller->getCategory();
            $categoryId = $category->id;
            $this->addViewedCategory($categoryId);
        }
        return $this->render('head.tpl', array(
            'currentProduct' => $productId,
            'currentCategory' => $categoryId,
            'currentIPA' => $ipa,
            'ps17' => $this->is17(),
            'ps8x' => $this->is8x(),
            'ajaxUrl' => Context::getContext()->link->getModuleLink($this->name, 'ajax'),
            'cartUrl' => Context::getContext()->link->getPageLink('cart')
        ));
    }
    
    /**
     *
     * @return ArPLInstaller
     */
    protected function getInstaller()
    {
        if (!$this->installer) {
            $this->installer = new ArPLInstaller($this);
        }
        return $this->installer;
    }


    public function install()
    {
        return parent::install() && $this->getInstaller()->install();
    }
    
    public function uninstall()
    {
        return parent::uninstall() && $this->getInstaller()->uninstall();
    }
    
    public function getForms()
    {
        return array(
            $this->generalConfig,
            $this->sliderConfig,
            $this->promoConfig,
            $this->tabsConfig,
            $this->sectionConfig
        );
    }
    
    public function getContent()
    {
        $this->initConfig();
        
        if ($this->isSubmit()) {
            if ($this->postValidate()) {
                $this->postProcess();
            }
        }
        
        $this->html .= $this->renderForm();
        return $this->html;
    }
    
    public function isSubmit()
    {
        foreach ($this->getAllowedSubmits() as $submit) {
            if (Tools::isSubmit($submit)) {
                return true;
            }
        }
    }
    
    public function getAllowedSubmits()
    {
        $submits = array();
        foreach ($this->getForms() as $model) {
            $submits[] = get_class($model);
        }
        return $submits;
    }
    
    public function postProcess()
    {
        foreach ($this->getForms() as $model) {
            if (Tools::isSubmit(get_class($model))) {
                $model->populate();
                if ($model->saveToConfig()) {
                    $this->generateCSS();
                    $this->html .= $this->displayConfirmation($this->l('Settings updated'));
                } else {
                    $this->postValidate();
                }
            }
        }
    }
    
    public function generateCSS()
    {
        $content = $this->render('styles.tpl', array(
            'generalConfig' => $this->generalConfig,
            'sliderConfig' => $this->sliderConfig,
            'promoConfig' => $this->promoConfig,
            'sectionConfig' => $this->sectionConfig,
            'tabsConfig' => $this->tabsConfig
        ));
        $content = preg_replace('/\s+/is', ' ', $content);
        $content = str_replace(array('; }'), '}', $content);
        $content = str_replace(array('{ '), '{', $content);
        
        if (is_writable($this->getPath(true) . 'views/css/generated.min.css')) {
            file_put_contents($this->getPath(true) . 'views/css/generated.min.css', $content);
            Configuration::updateValue('arpl_css_generated', time());
        } else {
            $this->html .= $this->displayError($this->getPath(true) . 'views/css/generated.min.css file is not writeable!');
        }
    }


    public function postValidate()
    {
        foreach ($this->getForms() as $model) {
            if (Tools::isSubmit(get_class($model))) {
                $model->loadFromConfig();
                $model->populate();
                if (!$model->validate()) {
                    foreach ($model->getErrors() as $errors) {
                        foreach ($errors as $error) {
                            $this->html .= $this->displayError($error);
                        }
                    }
                    return false;
                }
                return true;
            }
        }
    }
    
    public function renderForm()
    {
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->addJqueryUI('ui.draggable');
        $this->context->controller->addJqueryUI('ui.autocomplete');
        
        $this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='
            .$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'path' => $this->getPath(),
        );
        $helper->base_folder =  dirname(__FILE__);
        
        $helper->base_tpl = '/views/templates/admin/arproductlists/helpers/form/form.tpl';
        
        $categoriesHelper = new HelperTreeCategories('arpl-categories');
        $categoriesHelper->setUseCheckBox(false);
        $categoriesHelper->setInputName('list.category');
        
        $categoriesCheckboxTree = new HelperTreeCategories('arpl-category-restrictions');
        $categoriesCheckboxTree->setUseCheckBox(true);
        $categoriesCheckboxTree->setInputName('list.category_restrictions');
        
        $relCategoriesHelper = new HelperTreeCategories('arpl-relcat-source');
        $relCategoriesHelper->setUseCheckBox(false);
        $relCategoriesHelper->setInputName('relcat.source');
        
        $relRelatedCategoriesTree = new HelperTreeCategories('arpl-relcat-rels');
        $relRelatedCategoriesTree->setUseCheckBox(true);
        $relRelatedCategoriesTree->setInputName('relcat.rels');
        
        $categoriesCheckboxTree2 = new HelperTreeCategories('arpl-category-restrictions2');
        $categoriesCheckboxTree2->setUseCheckBox(true);
        $categoriesCheckboxTree2->setInputName('list.category_restrictions2');
        
        $id_lang = Context::getContext()->language->id;
        
        $this->smarty->assign(array(
            'form' => $helper,
            'id_lang' => $id_lang,
            'moduleUrl' => $this->getModuleBaseUrl(),
            'activeTab' => $this->getActiveTab(),
            'name' => $this->displayName,
            'version' => $this->version,
            'moduleId' => self::ADDONS_ID,
            'authorId' => self::AUTHOR_ID,
            'imgTypes' => ImageType::getImagesTypes('categories'),
            'brandImgTypes' => ImageType::getImagesTypes('manufacturers'),
            'lists' => ArProductList::getAll($id_lang, 0, 0),
            'manufacturers' => Manufacturer::getManufacturers(),
            'suppliers' => Supplier::getSuppliers(),
            'productContextLists' => ArProductList::getAll($id_lang, 1),
            'categoryContextLists' => ArProductList::getAll($id_lang, false, 1),
            'homeGroups' => $this->getGroups($this->getHomePageHooks(), 0, false, 0),
            'categoryGroups' => $this->getGroups($this->getCategoryPageHooks(), 0, false, 0),
            'productGroups' => $this->getGroups($this->getProductPageHooks(), 0, false, 0),
            'page404Groups' => $this->getGroups($this->get404PageHooks(), 0, false, 0),
            'cartGroups' => $this->getGroups($this->getCartPageHooks(), 0, false, 0),
            'thankYouGroups' => $this->getGroups($this->getThankYouPageHooks(), 0, false, 0),
            'languages' => $this->context->controller->getLanguages(),
            'defaultFormLanguage' => (int)(Configuration::get('PS_LANG_DEFAULT')),
            'ajaxUrl' => array(
                'group' => $this->context->link->getAdminLink('AdminArPlGroup'),
                'list' => $this->context->link->getAdminLink('AdminArPlList'),
                'relCat' => $this->context->link->getAdminLink('AdminArPlRelCat'),
                'rules' => $this->context->link->getAdminLink('AdminArPlRules'),
            ),
            'attributeGroups' => AttributeGroup::getAttributesGroups($id_lang),
            'features' => Feature::getFeatures($id_lang),
            'shops' => Shop::getShops(),
            'categoriesTree' => $categoriesHelper->render(),
            'categoriesCheckboxTree' => $categoriesCheckboxTree->render(),
            'categoriesCheckboxTree2' => $categoriesCheckboxTree2->render(),
            'relCategoriesTree' => $relCategoriesHelper->render(),
            'relRelatedCategoriesTree' => $relRelatedCategoriesTree->render(),
            'generalConfigForm' => array($this->getForm($this->generalConfig)),
            'sliderConfigForm' => array($this->getForm($this->sliderConfig)),
            'promoConfigForm' => array($this->getForm($this->promoConfig)),
            'tabsConfigForm' => array($this->getForm($this->tabsConfig)),
            'sectionConfigForm' => array($this->getForm($this->sectionConfig)),
            'path' => $this->getPath(),
            'max_input_vars' => ini_get('max_input_vars')
        ));
        return $this->display(__FILE__, 'config.tpl');
    }
    
    public function getHomePageHooks()
    {
        return array(
            'displayHome',
            'displayHomeTop',
            'displayHomeAfter',
            'arHomePageHook1',
            'arHomePageHook2',
            'arHomePageHook3'
        );
    }
    
    public function getCategoryPageHooks()
    {
        $hooks = array();
        if ($this->is178() || $this->is8x()) {
            $hooks[] = 'displayHeaderCategory';
        }
        if ($this->is177() || $this->is8x()) {
            $hooks[] = 'displayFooterCategory';
        }
        return array_merge($hooks, array(
            'arCategoryPageHook1',
            'arCategoryPageHook2',
            'arCategoryPageHook3',
            'arCategoryPageHook4',
            'arCategoryPageHook5',
            'arCategoryPageHook6',
            'arCategoryPageHook7',
            'arCategoryPageHook8',
            'arCategoryPageHook9',
            'arCategoryPageHook10',
            'displayLeftColumn'
        ));
    }
    
    public function getProductPageHooks()
    {
        $hooks = array(
            'displayReassurance',
            'displayProductAdditionalInfo',
            'displayFooterProduct',
            'displayLeftColumn'
        );
        if ($this->is178() || $this->is8x()) {
            $hooks[] = 'displayCartModalContent';
            $hooks[] = 'displayCartModalFooter';
        }
        $hooks[] = 'arProductPageHook1';
        $hooks[] = 'arProductPageHook2';
        $hooks[] = 'arProductPageHook3';
        return $hooks;
    }
    
    public function get404PageHooks()
    {
        return array(
            'displayNotFound',
            'ar404PageHook1',
            'ar404PageHook2',
            'ar404PageHook3'
        );
    }
    
    public function getCartPageHooks()
    {
        return array(
            'displayShoppingCartFooter',
            'displayShoppingCart',
            'arCartPageHook1',
            'arCartPageHook2',
            'arCartPageHook3'
        );
    }
    
    
    public function getThankYouPageHooks()
    {
        return array(
            'displayOrderConfirmation',
            'displayOrderConfirmation1',
            'displayOrderConfirmation2',
            'arThankYouPageHook1',
            'arThankYouPageHook2'
        );
    }
    
    public function getGroups($hooks, $device, $activeOnly = true, $id_shop = null)
    {
        $result = array();
        foreach ($hooks as $hook) {
            if ($id_shop === null) {
                $id_shop = Context::getContext()->shop->id;
            }
            $result[$hook] = ArProductListGroup::getByHook($hook, $id_shop, $device, $activeOnly);
        }
        return $result;
    }
    
    public function getLanguages($active = true, $id_shop = false)
    {
        if (self::$langs === null) {
            $languages = Language::getLanguages($active, $id_shop);
            self::$langs = array();
            if ($languages) {
                foreach ($languages as $language) {
                    self::$langs[] = $language['id_lang'];
                }
            }
        }
        return self::$langs;
    }
    
    public function render($file, $params = array())
    {
        $this->smarty->assign($params);
        return $this->display(__FILE__, $file);
    }
    
    public function getActiveTab()
    {
        foreach ($this->getForms() as $model) {
            if (Tools::isSubmit(get_class($model))) {
                return get_class($model);
            }
        }
        if (Tools::getValue('activeTab')) {
            return Tools::getValue('activeTab');
        }
        return null;
    }
    
    public function getActiveSubTab()
    {
        foreach ($this->getForms() as $model) {
            if (Tools::isSubmit(get_class($model))) {
                return get_class($model);
            }
        }
        return null;
    }
    
    public function getFormConfigs()
    {
        $configs = array();
        foreach ($this->getForms() as $form) {
            $configs[] = $this->getForm($form);
        }
        return $configs;
    }
    
    public function getForm($model)
    {
        $model->populate();
        $model->validate(false);
        $config = $model->getFormHelperConfig();
        return array(
            'form' => array(
                'name' => get_class($model),
                'legend' => array(
                    'title' => $model->getFormTitle(),
                    'icon' => $model->getFormIcon()
                ),
                'input' => $config,
                'submit' => array(
                    'name' => get_class($model),
                    'class' => $this->is15()? 'button' : null,
                    'title' => $this->l('Save'),
                )
            )
        );
    }
    
    public function getAjaxUrl()
    {
        return null;
    }
    
    public function getUploadPath()
    {
        $path = dirname(__FILE__) . '/uploads/';
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return $path;
    }
    
    public function getUploadsUrl()
    {
        return $this->getModuleBaseUrl() . 'uploads/';
    }
    
    public function getConfigFieldsValues()
    {
        $values = array();
        foreach ($this->getForms() as $model) {
            $model->loadFromConfig();
            $model->populate();
            foreach ($model->getAttributes() as $attr => $value) {
                $values[$model->getConfigAttribueName($attr)] = $value;
            }
        }
        return $values;
    }
    
    public function toLinkRewrite($str)
    {
        return Tools::link_rewrite($str);
    }
    
    public function clearCache()
    {
    }
    
    public function is15()
    {
        if ((version_compare(_PS_VERSION_, '1.5.0', '>=') === true)
                && (version_compare(_PS_VERSION_, '1.6.0', '<') === true)) {
            return true;
        }
        return false;
    }
    
    public function is16()
    {
        if ((version_compare(_PS_VERSION_, '1.6.0', '>=') === true)
                && (version_compare(_PS_VERSION_, '1.7.0', '<') === true)) {
            return true;
        }
        return false;
    }
    
    public function is17()
    {
        return $this->isVersion('1.7.0');
    }
    
    public function is174()
    {
        return $this->isVersion('1.7.4');
    }
    
    public function is176()
    {
        return $this->isVersion('1.7.6');
    }
    
    public function is177()
    {
        return $this->isVersion('1.7.7');
    }
    
    public function is178()
    {
        return $this->isVersion('1.7.8');
    }
    
    public function is8x()
    {
        return $this->isVersion('8.0.0', '9.0.0');
    }
    
    public function isVersion($min, $max = '1.8.0')
    {
        if ((version_compare(_PS_VERSION_, $min, '>=') === true)
                && (version_compare(_PS_VERSION_, $max, '<') === true)) {
            return true;
        }
        return false;
    }
    
    public function getPath($abs = false)
    {
        if ($abs) {
            return _PS_MODULE_DIR_ . $this->name . '/';
        }
        return $this->_path;
    }
    
    public function isWritable($dir)
    {
        return is_writable($this->getPath(true) . $dir);
    }
    
    public function isDirectoryExists($dir)
    {
        return is_dir($this->getPath(true) . $dir);
    }
    
    public function createDirectory($dir)
    {
        return mkdir($this->getPath(true) . $dir, 0777, true);
    }
    
    public function getModuleBaseUrl($id_shop = null)
    {
        return self::getShopDomainSsl($id_shop, true, true).__PS_BASE_URI__ . 'modules/' . $this->name . '/';
    }
    
    public static function getShopDomainSsl($id_shop = null, $http = false, $entities = false)
    {
        if ($id_shop == null) {
            $id_shop = Context::getContext()->shop->id;
        }
        if (!$domain = ShopUrl::getMainShopDomainSSL($id_shop)) {
            $domain = Tools::getHttpHost();
        }
        if ($entities) {
            $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
        }
        if ($http) {
            $domain = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$domain;
        }
        return $domain;
    }
    
    public function isMobile()
    {
        if ($this->isMobile === null) {
            $this->isMobile = Context::getContext()->getMobileDetect()->isMobile() || Context::getContext()->getMobileDetect()->isTablet();
        }
        if ($this->isMobile) {
            return 1;
        } else {
            return 2;
        }
    }
    
    public function smartyAssign($var)
    {
        $this->context->smarty->assign($var);
    }
}
