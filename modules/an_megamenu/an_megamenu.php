<?php
/**
 * 2007-2017 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'an_megamenu/classes/AnMenu.php';
require_once _PS_MODULE_DIR_ . 'an_megamenu/classes/AnDropdown.php';

/**
 * Class an_megamenu
 */
class an_megamenu extends Module
{
    /**
     * Composition classes
     */
    protected $compositeClasses = array(
        'AnMegaMenuConfigurator',
        'AnMegaMenuMenuConfigurator',
        'AnMegaMenuHooks',
        'AnMegaMenuAjaxHandler',
        'AnMegaMenuDropDownConfigurator',
    );

    /**
     * Composition cache
     */
    protected static $compositeCache = array();

    /**
     * @var string
     */
    protected $bg_img_folder = 'views/img/images/';

    /**
     * @var string
     */
    protected $html = '';

    /**
     * @var string
     */
    protected $currentIndex;

    /**
     * an_megamenu constructor.
     */
    public function __construct()
    {
        $this->name = 'an_megamenu';
        $this->tab = 'front_office_features';
        $this->version = '1.0.11';
        $this->author = 'Anvanto';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->tr('Anvanto Mega Menu');
        $this->description = $this->tr('Anvanto Mega Menu');
        $this->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
    }

    /**
     * Installation
     *
     * @return bool
     */
    public function install()
    {
        $sql = include _PS_MODULE_DIR_ . 'an_megamenu/sql/install.php';
        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        $this->installSampleData();
        if (parent::install()) {
            foreach ($this->getMegaMenuHooks() as $hook) {
                if (!$this->registerHook($hook)) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Sample data
     *
     * @return bool
     */
    public function installSampleData()
    {
        $sample = include _PS_MODULE_DIR_ . 'an_megamenu/sql/sample.php';
        $db = Db::getInstance();
        $shops = Shop::getShops(true, true);
        $languages = Language::getLanguages(true, false, true);
        $current = $db->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'anmenu` ORDER BY `id_anmenu` DESC LIMIT 1');
        if ($current) {
            $id_an_menu = $current[0]['id_anmenu'];
//            return false;
        } else {
            $id_an_menu = 0;
        }
        $categories = Category::getNestedCategories();
        $products = Product::getProducts($this->context->language->id, 1, 5000, 'name', 'ASC');
        shuffle($products);

        foreach ($sample as $item) {
            foreach ($shops as $shop) {
                $randomCats = array();
                foreach ($categories as $category) {
                    if (isset($category['children'])) {
                        foreach ($category['children'] as $child) {
                            if (isset($child['children'])) {
                                if (sizeof($child['children']) >= 2) {
                                    if(array_key_exists($child['id_category'], $randomCats) && is_array($randomCats[$child['id_category']])) {
                                        $randomCats[$child['id_category']][] = $child['id_category'];
                                        foreach ($child['children'] as $cat) {
                                            $randomCats[$child['id_category']][] = $cat['id_category'];
                                        }
                                        $randomCats[$child['id_category']] = serialize($randomCats[$child['id_category']]);
                                    }
                                }
                            } else {
                                $randomCats[] = serialize(array($child['id_category']));
                            }
                        }
                    }
                }
                $manufacturers = Manufacturer::getManufacturers();
                $id_an_menu++;
                $sql = str_replace('AN_ID_SHOP', $shop['id_shop'], $item['item']);
                $sql = str_replace('ID_AN_MENU', $id_an_menu, $sql);
                $db->execute($sql);
                foreach ($languages as $language) {
                    $sql = str_replace('AN_ID_LANG', $language, $item['lang']);
                    $sql = str_replace('ID_AN_MENU', $id_an_menu, $sql);
                    $db->execute($sql);
                }
                if (isset($item['items'])) {
                    $current = $db->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'andropdown` ORDER BY `id_andropdown` DESC LIMIT 1');
                    if ($current) {
                        $id_an_dropdown = $current[0]['id_andropdown'];
                    } else {
                        $id_an_dropdown = 0;
                    }
                }
                foreach ($item['items'] as $child) {
                    $id_an_dropdown++;
                    $sql = str_replace('ID_AN_MENU', $id_an_menu, $child['item']);
                    $sql = str_replace('ID_AN_DROPDOWN', $id_an_dropdown, $sql);
                    if (strpos($sql, 'AN_RANDOM_CATEGORIES') !== false) {
                        $cat = array_pop($randomCats);
                        if ($cat) {
                            $sql = str_replace('AN_RANDOM_CATEGORIES', "'" . $cat . "'", $sql);
                        } else {
                            $sql = str_replace('AN_RANDOM_CATEGORIES', '"a:0:{}"', $sql);
                        }
                    }
                    if (strpos($sql, 'AN_RANDOM_PRODUCT') !== false) {
                        $product = array_pop($products);
                        if ($product) {
                            $sql = str_replace('AN_RANDOM_PRODUCT', "'" .  serialize(array($product['id_product'])) . "'", $sql);
                        } else {
                            $sql = str_replace('AN_RANDOM_PRODUCT', '"a:0:{}"', $sql);
                        }
                    }
                    if (strpos($sql, 'AN_MANUFACTURER') !== false) {
                        $manufacturer = array_pop($manufacturers);
                        if ($manufacturer) {
                            $sql = str_replace('AN_MANUFACTURER', "'" .  serialize(array($manufacturer['id_manufacturer'])) . "'", $sql);
                        } else {
                            $sql = str_replace('AN_MANUFACTURER', '"a:0:{}"', $sql);
                        }
                    }
                    $db->execute($sql);
                    foreach ($languages as $language) {
                        $sql = str_replace('AN_ID_LANG', $language, $child['lang']);
                        $sql = str_replace('ID_AN_DROPDOWN', $id_an_dropdown, $sql);
                        $db->execute($sql);
                    }
                }
            }
        }
    }

    /**
     * Uninstallation
     *
     * @return bool
     */
    public function uninstall()
    {
        $sql = include _PS_MODULE_DIR_ . 'an_megamenu/sql/uninstall.php';
        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return parent::uninstall();
    }

    /**
     * Translator
     *
     * @param $text
     * @param array $params
     * @param string $type
     * @return mixed
     */
    protected function tr($text, $params = array(), $type = 'Modules.AnMegaMenu.Admin')
    {
        return $this->getTranslator()->trans($text, $params, $type);
    }

    /**
     * @return mixed|void
     */
    public function __call($method, $args = array())
    {
        if (empty(static::$compositeCache)) {
            foreach ($this->compositeClasses as $class) {
                require_once _PS_MODULE_DIR_ . 'an_megamenu/composite/' . $class . '.php';
                static::$compositeCache[$class] = new $class();
            }
        }

        foreach (static::$compositeCache as $instance) {
            if (method_exists($instance, $method)) {
                return $instance->$method();
            }
        }

        $this->_clearCache('*');
    }

    /**
     * Get configuratino
     *
     * @return string
     */
    public function getContent()
    {
        $stores = Shop::getContextListShopID();
        if (count($stores) > 1) {
            //$this->errors[] = Tools::displayError($this->l('Please select shop!'));
            return Tools::displayError($this->l('Please select shop!'));
        }
        return $this->getConfiguration();
    }

    protected function getCMSCategories($recursive = false, $parent = 1, $id_lang = false, $id_shop = false)
    {
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
        $id_shop = ($id_shop !== false) ? $id_shop : Context::getContext()->shop->id;
        $join_shop = '';
        $where_shop = '';

        if (Tools::version_compare(_PS_VERSION_, '1.6.0.12', '>=') == true) {
            $join_shop = ' INNER JOIN `'._DB_PREFIX_.'cms_category_shop` cs
			ON (bcp.`id_cms_category` = cs.`id_cms_category`)';
            $where_shop = ' AND cs.`id_shop` = '.(int)$id_shop.' AND cl.`id_shop` = '.(int)$id_shop;
        }

        if ($recursive === false) {
            $sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
				FROM `'._DB_PREFIX_.'cms_category` bcp'.$join_shop.'
				INNER JOIN `'._DB_PREFIX_.'cms_category_lang` cl
				ON (bcp.`id_cms_category` = cl.`id_cms_category`)
				WHERE cl.`id_lang` = '.(int)$id_lang.'
				AND bcp.`id_parent` = '.(int)$parent.$where_shop;

            return Db::getInstance()->executeS($sql);
        } else {
            $sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
				FROM `'._DB_PREFIX_.'cms_category` bcp'.$join_shop.'
				INNER JOIN `'._DB_PREFIX_.'cms_category_lang` cl
				ON (bcp.`id_cms_category` = cl.`id_cms_category`)
				WHERE cl.`id_lang` = '.(int)$id_lang.'
				AND bcp.`id_parent` = '.(int)$parent.$where_shop;

            $results = Db::getInstance()->executeS($sql);
            foreach ($results as $result) {
                $sub_categories = $this->getCMSCategories(true, $result['id_cms_category'], (int)$id_lang);
                if ($sub_categories && count($sub_categories) > 0) {
                    $result['sub_categories'] = $sub_categories;
                }
                $categories[] = $result;
            }

            return isset($categories) ? $categories : false;
        }
    }

    protected function getCMSPages($id_cms_category = false, $id_shop = false, $id_lang = false)
    {
        $id_shop = ($id_shop !== false) ? (int)$id_shop : (int)Context::getContext()->shop->id;
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;

        $where_shop = '';
        if (Tools::version_compare(_PS_VERSION_, '1.6.0.12', '>=') == true) {
            $where_shop = ' AND cl.`id_shop` = '.(int)$id_shop;
        }

        $where = $id_cms_category ? 'c.`id_cms_category` = '.(int)$id_cms_category.' AND ' : '';
        $sql = 'SELECT c.`id_cms`, cl.`meta_title`, cl.`link_rewrite`
			FROM `'._DB_PREFIX_.'cms` c
			INNER JOIN `'._DB_PREFIX_.'cms_shop` cs
			ON (c.`id_cms` = cs.`id_cms`)
			INNER JOIN `'._DB_PREFIX_.'cms_lang` cl
			ON (c.`id_cms` = cl.`id_cms`)
			WHERE '.$where.' cs.`id_shop` = '.(int)$id_shop.'
			AND cl.`id_lang` = '.(int)$id_lang.$where_shop.'
			AND c.`active` = 1
			ORDER BY `position`';

        return Db::getInstance()->executeS($sql);
    }

    protected function getCMSCategory($id = false, $id_lang = false)
    {
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
        $join_shop = '';

        if (Tools::version_compare(_PS_VERSION_, '1.6.0.12', '>=') == true) {
            $join_shop = ' INNER JOIN `'._DB_PREFIX_.'cms_category_shop` cs
			ON (bcp.`id_cms_category` = cs.`id_cms_category`)';
        }

        $sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
            FROM `'._DB_PREFIX_.'cms_category` bcp'.$join_shop.'
            INNER JOIN `'._DB_PREFIX_.'cms_category_lang` cl
            ON (bcp.`id_cms_category` = cl.`id_cms_category`)
            WHERE cl.`id_lang` = '.(int)$id_lang.'
            AND bcp.`id_cms_category` = '.(int)$id;

        return Db::getInstance()->executeS($sql)[0];
    }

    protected function getCategoryForMenu($id_cms_category, $id_lang = false) {
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;

        $subCategories = $this->getCMSCategories(false, $id_cms_category, $id_lang);
        $subPages = $this->getCMSPages($id_cms_category);

        return array('subcategorues'=>$subCategories, 'subpages'=>$subPages);
    }

    protected function getCMSPagesForMenu($pagesArray = false, $id_shop = false, $id_lang = false)
    {
        if(!$pagesArray){return array();}
        $id_shop = ($id_shop !== false) ? (int)$id_shop : (int)Context::getContext()->shop->id;
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;

        $where_shop = '';
        if (Tools::version_compare(_PS_VERSION_, '1.6.0.12', '>=') == true) {
            $where_shop = ' AND cl.`id_shop` = '.(int)$id_shop;
        }

        $sql = 'SELECT c.`id_cms`, cl.`meta_title`, cl.`link_rewrite`
			FROM `'._DB_PREFIX_.'cms` c
			INNER JOIN `'._DB_PREFIX_.'cms_shop` cs
			ON (c.`id_cms` = cs.`id_cms`)
			INNER JOIN `'._DB_PREFIX_.'cms_lang` cl
			ON (c.`id_cms` = cl.`id_cms`)
			WHERE c.id_cms IN (' . implode(', ', $pagesArray) . ')
			AND cs.`id_shop` = '.(int)$id_shop.'
			AND cl.`id_lang` = '.(int)$id_lang.$where_shop.'
			AND c.`active` = 1
			ORDER BY `position`';

        return Db::getInstance()->executeS($sql);
    }
}
