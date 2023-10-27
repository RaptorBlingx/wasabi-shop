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
include_once dirname(__FILE__).'/../../classes/ArProductList.php';
include_once dirname(__FILE__).'/../../classes/ArPLInstaller.php';
include_once dirname(__FILE__).'/../../classes/ArPLTools.php';

class AdminArPlListController extends ModuleAdminController
{
    public function __construct()
    {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();
        $this->meta_title = $this->l('Product lists');
    }
    
    public function ajaxProcessGetClassOptions()
    {
        $className = Tools::getValue('className');
        $model = new $className(null);
        $id_group = (int)Tools::getValue('id_group');
        $group = new ArProductListGroup($id_group);
        die(ArPLTools::jsonEncode(array(
            'isProductList' => (int)$model->isProductList(),
            'isCategoryList' => (int)$model->isCategoryList(),
            'isBrandList' => (int)$model->isBrandList(),
            'group' => $group,
            'isHomePageHook' => (int)in_array($group->hook, $this->module->getHomePageHooks()),
            'isProductPageHook' => (int)in_array($group->hook, $this->module->getProductPageHooks()),
            'isCategoryPageHook' => (int)in_array($group->hook, $this->module->getCategoryPageHooks()),
            'is404PageHook' => (int)in_array($group->hook, $this->module->get404PageHooks()),
            'isCartPageHook' => (int)in_array($group->hook, $this->module->getCartPageHooks()),
        )));
    }
    
    public function ajaxProcessUpdate()
    {
        $installer = new ArPLInstaller($this->module);
        $installer->installDefaultLists();
        die(ArPLTools::jsonEncode(array(
            'success' => 1
        )));
    }
    
    public function ajaxProcessGetOrder()
    {
        $className = Tools::getValue('className');
        $model = new $className(null);
        $order = $model->getOrderOptions();
        die(ArPLTools::jsonEncode($order));
    }
    
    public function ajaxProcessReorder()
    {
        $data = Tools::getValue('data');
        if (is_array($data) && !empty($data)) {
            foreach ($data as $item) {
                $k = explode('_', $item);
                Db::getInstance()->update(ArProductList::getTableName(false), array(
                    'position' => (int)$k[1]
                ), 'id_list = ' . (int)$k[0]);
            }
            $this->module->clearCache();
        }
        die(ArPLTools::jsonEncode(array()));
    }
    
    public function ajaxProcessReload()
    {
        $product = Tools::getValue('product');
        $category = Tools::getValue('category');
        $lists = ArProductList::getAll(Context::getContext()->language->id, $product, $category);
        die(ArPLTools::jsonEncode(array(
            'content' => $this->module->render('_partials/_list_items.tpl', array(
                'lists' => $lists
            ))
        )));
    }
    
    public function ajaxProcessEdit()
    {
        $id = Tools::getValue('id');
        $model = new ArProductListRel($id);
        $model->getList();
        $products = array();
        $categories = array();
        $brands = array();
        $id_lang = Context::getContext()->language->id;
        if ($model->class == 'ArPLCustomProducts' && $model->getList()->ids) {
            $id_lang = Context::getContext()->language->id;
            if ($this->module->is16()) {
                $imageType = ImageType::getFormatedName('small');
            } elseif ($this->module->is17() || $this->module->is8x()) {
                $imageType = ImageType::getFormattedName('small');
            }
            foreach ($model->getList()->ids as $id) {
                $product = new Product($id, true, $id_lang);
                $image = Image::getCover($product->id);
                $products[] = $this->module->render('_partials/_product.tpl', array(
                    'product' => $product,
                    'price' => Tools::displayPrice($product->getPrice()),
                    'image' => Context::getContext()->link->getImageLink($product->link_rewrite, $image['id_image'], $imageType)
                ));
            }
        } elseif ($model->class == 'ArPLCustomCategories' && $model->getList()->cat_ids) {
            if ($this->module->is16()) {
                $imageType = ImageType::getFormatedName('small');
            } elseif ($this->module->is17() || $this->module->is8x()) {
                $imageType = ImageType::getFormattedName('small');
            }
            foreach ($model->getList()->cat_ids as $id) {
                $category = new Category($id, $id_lang);
                $categories[] = $this->module->render('_partials/_category.tpl', array(
                    'category' => $category,
                    'image' => Context::getContext()->link->getCatImageLink($category->link_rewrite, $category->id, $imageType)
                ));
            }
        } elseif ($model->class == 'ArPLCustomBrands' && $model->getList()->brand_ids) {
            $id_lang = Context::getContext()->language->id;
            if ($this->module->is16()) {
                $imageType = ImageType::getFormatedName('small');
            } elseif ($this->module->is17() || $this->module->is8x()) {
                $imageType = ImageType::getFormattedName('small');
            }
            foreach ($model->getList()->brand_ids as $id) {
                $brand = new Manufacturer($id, $id_lang);
                $brands[] = $this->module->render('_partials/_brand.tpl', array(
                    'brand' => $brand,
                    'image' => $this->getManufacturerImage($brand->id)
                ));
            }
        }
        $group = new ArProductListGroup($model->id_group);
        die(ArPLTools::jsonEncode(array(
            'success' => 1,
            'model' => $model,
            'isHomePageHook' => (int)in_array($group->hook, $this->module->getHomePageHooks()),
            'isProductPageHook' => (int)in_array($group->hook, $this->module->getProductPageHooks()),
            'isCategoryPageHook' => (int)in_array($group->hook, $this->module->getCategoryPageHooks()),
            'is404PageHook' => (int)in_array($group->hook, $this->module->get404PageHooks()),
            'isCartPageHook' => (int)in_array($group->hook, $this->module->getCartPageHooks()),
            'order' => $model->getList()->getOrderOptions(),
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'isProductList' => (int)$model->getList()->isProductList(),
            'isCategoryList' => (int)$model->getList()->isCategoryList(),
            'isBrandList' => (int)$model->getList()->isBrandList(),
        )));
    }
    
    public function fetchFilters()
    {
        return array(
            array(
                array(
                    'list.custom_js'
                ), 'safe'
            ),
            array(
                array(
                    'class',
                    'title_\d+',
                    'more_link_\d+',
                    'list.more_url',
                    'list.ids',
                    'list.category_restrictions',
                    'list.category_restrictions2',
                    'list.cat_ids',
                    'list.brand_ids',
                    'list.responsiveBaseElement',
                    'list.responsiveBreakdowns',
                    'list.titleAlign',
                    'list.orderBy',
                    'list.orderWay'
                ), 'pSQL'
            ),
            array(
                array(
                    'id_group',
                    'list.ajax',
                    'list.limit',
                    'list.view',
                    'list.controls',
                    'list.dots',
                    'list.center',
                    'list.loop',
                    'list.drag',
                    'list.category',
                    'list.autoplay',
                    'list.autoplayTimeout',
                    'list.instock',
                    'list.grid',
                    'list.grid_md',
                    'list.grid_sm',
                    'list.thumb_size',
                    'list.brand_thumb_size',
                    'list.cat_title',
                    'list.cat_desc',
                    'list.more_link',
                    'list.id_manufacturer',
                    'list.id_supplier',
                    'list.slide_by',
                    'list.days',
                    'list.sortorder',
                    'list.attribute_group',
                    'list.exclude_same_category',
                    'list.same_category_only',
                    'list.product_update',
                    'list.shop_filter',
                    'list.category_filter',
                    'list.id_feature',
                    'list.current_category',
                    'list.current_category_only',
                    'list.full_tree'
                ), 'int'
            )
        );
    }
    
    public function getFilterType($attribute)
    {
        foreach ($this->fetchFilters() as $filter) {
            if (in_array($attribute, $filter[0])) {
                return $filter[1];
            }
        }
        return false;
    }
    
    public function fetchData($data)
    {
        $result = array();
        $categoryRestrictions = array();
        $categoryRestrictions2 = array();
        foreach ($data as $param) {
            if (isset($param['name']) && $param['name'] && isset($param['value'])) {
                $attribute = trim($param['name']);
                if (strpos($attribute, 'list.') === 0) {
                    if ($attribute == 'list.ids') {
                        if (!empty($param['value'])) {
                            $values = explode(',', $param['value']);
                        } else {
                            $values = array();
                        }
                        $result['list']['ids'] = $values;
                    } elseif ($attribute == 'list.cat_ids') {
                        if (!empty($param['value'])) {
                            $values = explode(',', $param['value']);
                        } else {
                            $values = array();
                        }
                        $result['list']['cat_ids'] = $values;
                    } elseif ($attribute == 'list.brand_ids') {
                        if (!empty($param['value'])) {
                            $values = explode(',', $param['value']);
                        } else {
                            $values = array();
                        }
                        $result['list']['brand_ids'] = $values;
                    } elseif ($attribute == 'list.category_restrictions[]') {
                        if (!empty($param['value'])) {
                            $categoryRestrictions[] = trim(pSQL($param['value']));
                        }
                    } elseif ($attribute == 'list.category_restrictions2[]') {
                        if (!empty($param['value'])) {
                            $categoryRestrictions2[] = trim(pSQL($param['value']));
                        }
                    } else {
                        if ($filter = $this->getFilterType($attribute)) {
                            $attribute = str_replace('list.', '', $attribute);
                            if ($filter == 'pSQL') {
                                $result['list'][$attribute] = trim(pSQL($param['value']));
                            } elseif ($filter == 'int') {
                                $result['list'][$attribute] = (int)$param['value'];
                            } elseif ($filter == 'safe') {
                                $result['list'][$attribute] = $param['value'];
                            }
                        }
                    }
                } else {
                    if ($filter = $this->getFilterType($attribute)) {
                        if ($filter == 'pSQL') {
                            $result[$attribute] = trim(pSQL($param['value']));
                        } elseif ($filter == 'int') {
                            $result[$attribute] = (int)$param['value'];
                        }
                    } elseif (preg_match('/title_(\d+)/is', $attribute, $mathes)) {
                        $result['title'][$mathes[1]] = pSQL($param['value']);
                    } elseif (preg_match('/more_link_(\d+)/is', $attribute, $mathes)) {
                        $result['more_link'][$mathes[1]] = pSQL($param['value']);
                    }
                }
            }
        }
        if ($categoryRestrictions) {
            $result['list']['category_restrictions'] = $categoryRestrictions;
        }
        if ($categoryRestrictions2) {
            $result['list']['category_restrictions2'] = $categoryRestrictions2;
        }
        return $result;
    }
    
    public function assignData($model, $data)
    {
        foreach ($data as $k => $v) {
            if ($k == 'list') {
                $model->data = ArPLTools::jsonEncode($v);
            } else {
                if (property_exists($model, $k)) {
                    $model->$k = $v;
                }
            }
        }
    }
    
    public function validateResponsiveBreakdowns($model)
    {
        $data = ArPLTools::jsonDecode($model->data);
        if (!$data->responsiveBreakdowns) {
            return true;
        }
        $breakdowns = explode('\n', $data->responsiveBreakdowns);
        foreach ($breakdowns as $breakdown) {
            if (!preg_match('{^\d+:\d+$}is', $breakdown)) {
                return false;
            }
        }
        $data->responsiveBreakdowns = implode("\n", $breakdowns);
        $model->data = ArPLTools::jsonEncode($data);
        return true;
    }
    
    public function ajaxProcessSave()
    {
        $id = Tools::getValue('id');
        $data = Tools::getValue('data');
        $res = $this->fetchData($data);
        $errors = array();
        if ($id) {
            $model = new ArProductListRel($id);
        } else {
            $model = new ArProductListRel();
            $model->position = ArProductListRel::getPosition($res['id_group']) + 1;
        }
        $this->assignData($model, $res);
        if (!$model->getList()->validate()) {
            $errors = $model->getList()->getErrors();
        }
        if (!$this->validateResponsiveBreakdowns($model)) {
            $errors['list_responsiveBreakdowns'] = $this->module->l('Wrong breakdowns');
        }
        if (!empty($errors)) {
            die(ArPLTools::jsonEncode(array(
                'success' => 0,
                'errors' => $errors
            )));
        }
        $model->status = 1;
        die(ArPLTools::jsonEncode(array(
            'success' => (int)$model->save(),
            'model' => $model,
            'content' => $this->module->render('_partials/_list_item.tpl', array(
                'list' => $model->toArray(),
                'group' => new ArProductListGroup($model->id_group)
            ))
        )));
    }
    
    public function ajaxProcessProductSearch()
    {
        $q = Tools::getValue('q');
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        $sql = 'SELECT p.id_product, p.reference, pl.name, pl.link_rewrite FROM `' . _DB_PREFIX_ . 'product` p '
                . 'LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON pl.id_product = p.id_product '
                . 'WHERE (pl.name LIKE "%' . pSQL($q) . '%" OR p.reference LIKE "%' . pSQL($q) . '%") AND p.active = 1 AND pl.id_lang = ' . (int)$id_lang . ' AND pl.id_shop = ' . (int)$id_shop . ' LIMIT 10';
        $res = array();
        if ($rows = Db::getInstance()->executeS($sql)) {
            if ($this->module->is16()) {
                $imageType = ImageType::getFormatedName('small');
            } elseif ($this->module->is17() || $this->module->is8x()) {
                $imageType = ImageType::getFormattedName('small');
            }
            foreach ($rows as $row) {
                $image = Image::getCover($row['id_product']);
                $res[] = array(
                    'id' => $row['id_product'],
                    'label' => $row['name'],
                    'ref' => $row['reference'],
                    'image' => Context::getContext()->link->getImageLink($row['link_rewrite'], $image['id_image'], $imageType)
                );
            }
        }
        die(ArPLTools::jsonEncode($res));
    }
    
    public function ajaxProcessCategorySearch()
    {
        $q = Tools::getValue('q');
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        $sql = 'SELECT c.id_category, cl.name, cl.link_rewrite FROM `' . _DB_PREFIX_ . 'category` c '
                . 'LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON cl.id_category = c.id_category '
                . 'WHERE (cl.name LIKE "%' . pSQL($q) . '%" OR c.id_category LIKE "%' . pSQL($q) . '%") AND c.active = 1 AND cl.id_lang = ' . (int)$id_lang . ' AND cl.id_shop = ' . (int)$id_shop . ' LIMIT 10';
        $res = array();
        if ($rows = Db::getInstance()->executeS($sql)) {
            if ($this->module->is16()) {
                $imageType = ImageType::getFormatedName('small');
            } elseif ($this->module->is17() || $this->module->is8x()) {
                $imageType = ImageType::getFormattedName('small');
            }
            foreach ($rows as $row) {
                $res[] = array(
                    'id' => $row['id_category'],
                    'label' => $row['name'],
                    'image' => Context::getContext()->link->getCatImageLink($row['link_rewrite'], $row['id_category'], $imageType)
                );
            }
        }
        die(ArPLTools::jsonEncode($res));
    }
    
    public function ajaxProcessBrandSearch()
    {
        $q = Tools::getValue('q');
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        $sql = 'SELECT m.id_manufacturer, m.name FROM `' . _DB_PREFIX_ . 'manufacturer` m '
                . 'LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer_lang` ml ON ml.id_manufacturer = m.id_manufacturer '
                . 'LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` ms ON ms.id_manufacturer = m.id_manufacturer '
                . 'WHERE (m.name LIKE "%' . pSQL($q) . '%" OR m.id_manufacturer LIKE "%' . pSQL($q) . '%") AND m.active = 1 AND ml.id_lang = ' . (int)$id_lang . ' AND ms.id_shop = ' . (int)$id_shop . ' LIMIT 10';
        $res = array();
        if ($rows = Db::getInstance()->executeS($sql)) {
            foreach ($rows as $row) {
                $res[] = array(
                    'id' => $row['id_manufacturer'],
                    'label' => $row['name'],
                    'image' => $this->getManufacturerImage($row['id_manufacturer'])
                );
            }
        }
        die(ArPLTools::jsonEncode($res));
    }
    
    public function getManufacturerImage($id)
    {
        if ($this->module->is16()) {
            $imageType = ImageType::getFormatedName('small');
        } elseif ($this->module->is17() || $this->module->is8x()) {
            $imageType = ImageType::getFormattedName('small');
        }
        if ($this->module->is17() || $this->module->is8x()) {
            return Context::getContext()->link->getManufacturerImageLink($id, $imageType);
        } else {
            $img = (!file_exists(_PS_MANU_IMG_DIR_.$id.'-'.ImageType::getFormatedName('medium').'.jpg')) ? Context::getContext()->language->iso_code.'-default' : $id;
            return _THEME_MANU_DIR_ . $img . '-' . ImageType::getFormatedName('medium') . '.jpg';
        }
    }
    
    public function ajaxProcessBrandForList()
    {
        $id = Tools::getValue('id');
        $id_lang = Context::getContext()->language->id;
        $brand = new Manufacturer($id, $id_lang);
        die(ArPLTools::jsonEncode(array(
            'content' => $this->module->render('_partials/_brand.tpl', array(
                'brand' => $brand,
                'image' => $this->getManufacturerImage($brand->id)
            ))
        )));
    }
    
    public function ajaxProcessCategoryForList()
    {
        $id = Tools::getValue('id');
        $id_lang = Context::getContext()->language->id;
        $category = new Category($id, $id_lang);
        if ($this->module->is16()) {
            $imageType = ImageType::getFormatedName('small');
        } elseif ($this->module->is17() || $this->module->is8x()) {
            $imageType = ImageType::getFormattedName('small');
        }
        die(ArPLTools::jsonEncode(array(
            'content' => $this->module->render('_partials/_category.tpl', array(
                'category' => $category,
                'image' => Context::getContext()->link->getCatImageLink($category->link_rewrite, $category->id, $imageType)
            ))
        )));
    }
    
    public function ajaxProcessProductForList()
    {
        $id = Tools::getValue('id');
        $id_lang = Context::getContext()->language->id;
        $product = new Product($id, true, $id_lang);
        if ($this->module->is16()) {
            $imageType = ImageType::getFormatedName('small');
        } elseif ($this->module->is17() || $this->module->is8x()) {
            $imageType = ImageType::getFormattedName('small');
        }
        $image = Image::getCover($product->id);
        die(ArPLTools::jsonEncode(array(
            'content' => $this->module->render('_partials/_product.tpl', array(
                'product' => $product,
                'price' => Tools::displayPrice($product->getPrice()),
                'image' => Context::getContext()->link->getImageLink($product->link_rewrite, $image['id_image'], $imageType)
            ))
        )));
    }
    
    public function ajaxProcessToggle()
    {
        $id = Tools::getValue('id');
        $model = new ArProductListRel($id);
        $model->status = $model->status? 0 : 1;
        $model->save();
        die(ArPLTools::jsonEncode(array(
            'status' => $model->status
        )));
    }
    
    public function ajaxProcessChangeDevice()
    {
        $id = Tools::getValue('id');
        $value = Tools::getValue('value');
        $model = new ArProductListRel($id);
        $model->device = (int)$value;
        $model->save();
        die(ArPLTools::jsonEncode(array(
            'device' => $model->device
        )));
    }
}
