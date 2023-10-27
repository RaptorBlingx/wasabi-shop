<?php
/**
 * 2007-2020 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

use Orangepix\Opxcatalogseo\Form;
use \Orangepix\Opxcatalogseo\Configurations;
use Symfony\Component\Form\FormBuilderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class Opxcatalogseo extends Module
{

  protected $config_output;

  public function __construct()
  {
    $this->name = 'opxcatalogseo';
    $this->tab = 'administration';
    $this->version = '2.1.0';
    $this->author = 'OrangePix Srl';
    $this->need_instance = 0;

    /**
     * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
     */
    $this->bootstrap = true;

    parent::__construct();

    $this->displayName = $this->l('Catalog SEO manager');
    $this->description = $this->l('Manage SEO fields for new categories and products');

    $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
  }

  public function install()
  {
    include __DIR__ . '/sql/install.php';
    return
      parent::install() &&
      $this->registerHook('filterCategoryContent') &&
      $this->registerHook('actionCategoryFormBuilderModifier') &&
      $this->registerHook('actionBeforeUpdateCategoryFormHandler') &&
      $this->registerHook('actionBeforeCreateCategoryFormHandler') &&
      $this->registerHook('actionAfterCreateCategoryFormHandler') &&
      $this->registerHook('actionBeforeUpdateManufacturerFormHandler') &&
      $this->registerHook('actionBeforeCreateManufacturerFormHandler') &&
      $this->registerHook('actionCategoryUpdate') &&
      $this->registerHook('actionCategoryAdd') &&
      $this->registerHook('actionObjectManufacturerUpdateAfter') &&
      $this->registerHook('actionObjectManufacturerAddAfter') &&
      $this->registerHook('actionAdminSaveBefore');
  }

  public function getContent()
  {
    if (!Configuration::hasKey(Configurations::H1_OVERRIDE_CATEGORY_NAME) ) {
      Configuration::updateValue(Configurations::H1_OVERRIDE_CATEGORY_NAME, true);
    }
    
    $this->config_output = '';

    if (Tools::isSubmit('success')) {
      $this->config_output .= $this->displayConfirmation($this->l('Updated successfully'));
    }

    $this->handleAjax();
    
    $this->postValidation();

    if ($this->isConfigOk()) {
      $this->config_output .= $this->context->smarty->fetch(__DIR__ . '/views/templates/admin/configure.tpl');
    }

    // display any message, then the form
    return $this->config_output . $this->displayForm();
  }

  public function displayForm()
  {
    return (new Form($this))->displayForm();
  }

  public function hookFilterCategoryContent($params)
  {
    $category = &$params['object'];
    $id = $category['id'];
    if ($opx_category = OpxCatalogseoCategory::findByIdCategory($id)) {
      $id_lang = $this->context->language->id;
      $category['h1'] = $opx_category->h1[$id_lang];
      if (Configuration::get(Configurations::H1_OVERRIDE_CATEGORY_NAME)) {
        $category['name'] = $opx_category->h1[$id_lang];
      }
    }
    return $params;
  }

  public function hookActionCategoryFormBuilderModifier($params)
  {
    /** @var FormBuilderInterface $formBuilder */
    $formBuilder = $params['form_builder'];
    $formBuilder->add('h1', TranslatableType::class, [
        'type' => TextType::class,
        'label' => 'H1',
        'required' => false,
    ]);

    if (array_key_exists('id', $params) && $id = $params['id']){
      $opx_category = OpxCatalogseoCategory::findByIdCategory($id);
      $params['data']['h1'] = $opx_category ? $opx_category->h1 : null;
    }

    $formBuilder->setData($params['data']);
  }

  public function hookActionAdminSaveBefore($params)
  {
    $this->updateProductFields($params);
  }

  public function hookActionCategoryUpdate($params)
  {
    /** @var Category $category  */
    $category = $params['category'];
    $this->updateCategory($category);
  }

  public function hookActionCategoryAdd($params)
  {
    /** @var Category $category  */
    $category = $params['category'];
    $this->updateCategory($category);
  }

  public function hookActionObjectManufacturerUpdateAfter($params)
  {
    /** @var Manufacturer $manufacturer  */
    $manufacturer = $params['object'];
    $this->updateManufacturer($manufacturer);
  }

  public function hookActionObjectManufacturerAddAfter($params)
  {
    /** @var Manufacturer $manufacturer  */
    $manufacturer = $params['manufacturer'];
    $this->updateManufacturer($manufacturer);
  }

  public function hookActionBeforeCreateCategoryFormHandler($params)
  {
    $this->updateCategoryFields($params);
  }


  public function hookActionAfterCreateCategoryFormHandler($params)
  {
    $this->updateCategoryFields($params);
  }

  public function hookActionBeforeUpdateCategoryFormHandler($params)
  {
    $this->updateCategoryFields($params);
  }

  public function hookActionBeforeCreateManufacturerFormHandler($params)
  {
    $this->updateManufacturerFields($params);
  }

  public function hookActionBeforeUpdateManufacturerFormHandler($params)
  {
    $this->updateManufacturerFields($params);
  }

  
  public function replacePlaceholders(string $string, array $data = [], string|int $id_lang = 0)
  {
    if ($id_lang == 0) {
      $id_lang = Configuration::get('PS_LANG_DEFAULT');
    }

    $category = array_key_exists('category', $data) ? $data['category'] : null;
    $product = array_key_exists('product', $data) ? $data['product'] : null;
    $manufacturer = array_key_exists('manufacturer', $data) ? $data['manufacturer'] : null;

    $replaces = [
      '{category}' => $category,
      '{product}' => $product,
      '{manufacturer}' => $manufacturer,
      '{shop}' => $this->context->shop->name,
    ];

    array_walk($replaces, function(&$item, $key, $id_lang){
      if (is_array($item)) {
        $item = array_key_exists($id_lang, $item) ? $item[$id_lang] : current($item);
      }
    }, $id_lang);

    $newString = strtr($string, $replaces);

    // remove extra spaces
    $newString = trim(preg_replace('/\s+/', ' ', $newString));

    return $newString;
  }

  private function postValidation()
  {
    if (!Tools::isSubmit('submit' . $this->name)) {
      return;
    }
    // this part is executed only when the form is submitted
    // retrieve the value set by the user
    
    Configuration::updateValue(Configurations::H1_OVERRIDE_CATEGORY_NAME, (bool)Tools::getValue(Configurations::H1_OVERRIDE_CATEGORY_NAME));

    $hasErrors = false;

    $required_fields = [
      Configurations::CATEGORY_TITLE             => $this->l('Category page title'),
      Configurations::CATEGORY_DESCRIPTION       => $this->l('Category meta description'),
      Configurations::PRODUCT_TITLE              => $this->l('Product page title'),
      Configurations::PRODUCT_DESCRIPTION        => $this->l('Product meta description'),
      Configurations::MANUFACTURER_TITLE         => $this->l('Manufacturer page title'),
      Configurations::MANUFACTURER_DESCRIPTION   => $this->l('Manufacturer page description')
    ];

    $langs = $this->context->controller->getLanguages();
    foreach ($langs as $lang) {
      foreach ($required_fields as $key => $label) {
        $kLang = "{$key}_$lang[id_lang]";
        if (empty(Tools::getValue($kLang))) {
          $msg = sprintf($this->l('`%s` can\'t be blank.' ), $label);
          if (count($langs) > 1) {
            $msg .= " " . sprintf($this->l('Check %s language'), $lang['name']);
          }
          $this->config_output .= $this->displayError($msg);
          $hasErrors = true;
        }
        else {
          Configuration::updateValue($key, [$lang['id_lang'] => Tools::getValue($kLang)]);
        }
      }
    }

    if (!$hasErrors) {
      $this->config_output .= $this->displayConfirmation($this->l('Settings updated'));
    }
  }

  public function getConfigValues($config_fields)
  {
    $langs = $this->context->controller->getLanguages();
    return array_reduce($config_fields, function($prev, $curr) use($langs) {
      $key = $curr['name'];
      $type = $curr['type'];
      if (!array_key_exists('lang', $curr) || !$curr['lang']) {
        $prev[$key] = Tools::getValue($key, Configuration::get($key));
      }
      else {
        foreach ($langs as $lang) {
          $kLang = "{$key}_$lang[id_lang]";
          $prev[$key][$lang['id_lang']] = Tools::getValue($kLang, Configuration::get($key, $lang['id_lang']));
        }
      }
      $type == 'switch' && ($prev[$key] = (int) $prev[$key]);
      return $prev;
    }, []);
  }

  private function updateCategoryFields(&$params)
  {
    $formData = &$params['form_data'];
    $aDescriptionFormat = Configuration::getConfigInMultipleLangs(Configurations::CATEGORY_DESCRIPTION); 
    $aTitleFormat = Configuration::getConfigInMultipleLangs(Configurations::CATEGORY_TITLE); 
    $this->updateFormFields($formData, $aTitleFormat, $aDescriptionFormat, 'category');

    if (array_key_exists('id', $params) &&  $id = $params['id']) {
      $opx_category = OpxCatalogseoCategory::findByIdCategory($id);
      if (null === $opx_category) {
        $opx_category = new OpxCatalogseoCategory();
        $opx_category->id_category = $id;
      }
      $opx_category->h1 = $formData['h1'];

      $opx_category->save();

    }

  }

  private function updateManufacturerFields(&$params)
  {
    $formData = &$params['form_data'];
    $aDescriptionFormat = Configuration::getConfigInMultipleLangs(Configurations::MANUFACTURER_DESCRIPTION); 
    $aTitleFormat = Configuration::getConfigInMultipleLangs(Configurations::MANUFACTURER_TITLE); 
    $this->updateFormFields($formData, $aTitleFormat, $aDescriptionFormat, 'manufacturer');
  }

  private function updateFormFields(&$formData, array $aTitleFormat, array $aDescriptionFormat, string $type)
  {
    $langs = $this->context->controller->getLanguages();
    foreach ($langs as $lang) {
      $descriptionFormat = $aDescriptionFormat[$lang['id_lang']];
      $titleFormat = $aTitleFormat[$lang['id_lang']];
      $metaDescription = &$formData['meta_description'][$lang['id_lang']];
      $metaTitle = &$formData['meta_title'][$lang['id_lang']];
      
      if (!empty($descriptionFormat) && empty($metaDescription)) {
        $metaDescription = $this->replacePlaceholders($descriptionFormat, [$type => $formData['name']], $lang['id_lang'] );
      }

      if (!empty($titleFormat) && empty($metaTitle)) {
        $metaTitle = $this->replacePlaceholders($titleFormat, [$type => $formData['name']], $lang['id_lang']);
      }
    }
  }

  private function updatePostValue(string $key, string $translated, int|string $id_lang)
  {
    $kLang = "{$key}_$id_lang";
    if (empty($_POST[$kLang]) && !empty($translated)) {
      $_POST[$kLang] = $translated;
    }
  }

  private function getMultilangValuesFromPost($key)
  {
    $langs = $this->context->controller->getLanguages();
    $output = [];
    foreach ($langs as $lang) {
      $kLang = "{$key}_$lang[id_lang]";
      $output[$lang['id_lang']] = $_POST[$kLang];
    }

    return $output;
  }

  private function updateProductFields(&$params)
  {
    if(!$params['controller'] instanceof AdminProductsController) {
      return;
    }
    $aTitleFormat = Configuration::getConfigInMultipleLangs(Configurations::PRODUCT_TITLE);
    $aDescriptionFormat = Configuration::getConfigInMultipleLangs(Configurations::PRODUCT_DESCRIPTION);
    $names = $this->getMultilangValuesFromPost('name');
    $category = new Category($_POST['id_category_default']);
    $categoryNames = $category->name;
    $replaceData = ['category' => $categoryNames, 'product' => $names];
    $langs = $this->context->controller->getLanguages();
    foreach ($langs as $lang) {
      $id_lang = $lang['id_lang'];
      $value = $this->replacePlaceholders($aTitleFormat[$id_lang], $replaceData, $id_lang);
      $this->updatePostValue('meta_title', $value, $id_lang);
      $value = $this->replacePlaceholders($aDescriptionFormat[$id_lang], $replaceData, $id_lang);
      $this->updatePostValue('meta_description', $value, $id_lang);
    }
  }

  private function handleAjax()
  {
    if (!Tools::isSubmit('ajax')) {
      return;
    }


    if (Tools::isSubmit('ocsChkAll')) {
      $category = $product = $manufacturer = true;
    }
    else {
      $category = Tools::isSubmit('ocsChkCategory');
      $product = Tools::isSubmit('ocsChkProduct');
      $manufacturer = Tools::isSubmit('ocsChkManufacturer');
    }

    try {
      if (Tools::isSubmit('ocsUpAll')) {
        return $this->updateAll($category, $product, $manufacturer);
      }
      elseif (Tools::isSubmit('ocsFill')) {
        return $this->fillMissings($category, $product, $manufacturer);
      }
    } catch (\Throwable $th) {
      ob_end_clean();
      die(json_encode(['status' => false, 'message' => $th->getMessage()]));
    }
  }

  private function updateAll(bool $category, bool $product, bool $manufacturer)
  {
    $response = ['status' => true, 'message' => 'All updated'];
    ob_start();

    if ($category) {
      $this->updateAllCategories();
    }

    if ($product) {
      $this->updateAllProducts();
    }

    if ($manufacturer) {
      $this->updateAllManufacturers();
    }

    ob_end_clean();
    die(json_encode($response));
  }

  private function fillMissings(bool $category, bool $product, bool $manufacturer)
  {
    $response = ['status' => true, 'message' => 'Missing fields filled'];
    ob_start();
    
    if ($category) {
      $this->updateAllCategories(true);
    }

    if ($product) {
      $this->updateAllProducts(true);
    }

    if ($manufacturer) {
      $this->updateAllManufacturers(true);
    }

    ob_end_clean();
    die(json_encode($response));
  }

  public function updateAllCategories($fill = false)
  {
    $langs = $this->context->controller->getLanguages();
    $descriptionStrings = Configuration::getConfigInMultipleLangs(Configurations::CATEGORY_DESCRIPTION);
    $titleStrings = Configuration::getConfigInMultipleLangs(Configurations::CATEGORY_TITLE);
    foreach ($langs as $lang) {
      $description = $descriptionStrings[$lang['id_lang']];
      $title = $titleStrings[$lang['id_lang']];
      Db::getInstance()->update('category_lang', [
        'meta_description' => [
          'type' => 'sql',
          'value' => $this->getReplaceStatement($description, 'category', $fill ? 'meta_description': false)
        ],
        'meta_title' => [
          'type' => 'sql',
          'value' => $this->getReplaceStatement($title, 'category', $fill ? 'meta_title' : false)
        ],
      ], "id_lang = $lang[id_lang]");
    }
  }

  public function updateAllManufacturers($fill = false)
  {
    $langs = $this->context->controller->getLanguages();
    $descriptionStrings = Configuration::getConfigInMultipleLangs(Configurations::MANUFACTURER_DESCRIPTION);
    $titleStrings = Configuration::getConfigInMultipleLangs(Configurations::MANUFACTURER_TITLE);
    foreach ($langs as $lang) {
      $description = $descriptionStrings[$lang['id_lang']];
      $title = $titleStrings[$lang['id_lang']];
      $sqlName = (new DbQuery())->select('name')->from('manufacturer', 'm')->where(sprintf('m.id_manufacturer = `%smanufacturer_lang`.id_manufacturer', _DB_PREFIX_))->__toString();
      Db::getInstance()->update('manufacturer_lang', [
        'meta_description' => [
          'type' => 'sql',
          'value' => $this->getReplaceStatement($description, ['manufacturer', "($sqlName)"], $fill ? 'meta_description': false)
        ],
        'meta_title' => [
          'type' => 'sql',
          'value' => $this->getReplaceStatement($title, ['manufacturer', "($sqlName)"], $fill ? 'meta_title' : false)
        ],
      ], "id_lang = $lang[id_lang]");
    }
  }
  
  public function updateAllProducts(bool $fill = false)
  {
    $langs = $this->context->controller->getLanguages();
    $descriptionStrings = Configuration::getConfigInMultipleLangs(Configurations::PRODUCT_DESCRIPTION);
    $titleStrings = Configuration::getConfigInMultipleLangs(Configurations::PRODUCT_TITLE);
    foreach ($langs as $lang) {
      $description = $descriptionStrings[$lang['id_lang']];
      $title = $titleStrings[$lang['id_lang']];
      Db::getInstance()->update('product_lang', [
        'meta_description' => [
          'type' => 'sql',
          'value' => sprintf("COALESCE((%s), meta_description)", $this->getProductReplaceStatement($description, $lang['id_lang'], $fill ? 'meta_description': false))
        ],
        'meta_title' => [
          'type' => 'sql',
          'value' => sprintf("COALESCE((%s), meta_title)",$this->getProductReplaceStatement($title, $lang['id_lang'], $fill ? 'meta_title' : false))
        ],
      ], "id_lang = $lang[id_lang]");
    }
  }

  /**
   * @var string|array $placeholder `value | [value, key]`
   */
  private function getReplaceStatement(string $string, string|array $placeholder, string|bool $fill = false)
  {
    $nameK = is_array($placeholder) && count($placeholder) == 2 ? $placeholder[1] : 'name';
    $nameV = is_array($placeholder) ? $placeholder[0] : $placeholder;
    $shop_name = $this->context->shop->name;
    $replace = sprintf("REPLACE('%s', '{%s}', %s)", $string, $nameV, $nameK);
    $replace = sprintf("REPLACE(%s, '{shop}', '%s')", $replace, $shop_name);
    if ($fill) {
      $replace = $this->transformToFillOnly($replace, $fill);
    }
    return $replace;
  }

  private function transformToFillOnly(string $replace_statement, string $field)
  {
    return "IF(TRIM($field) = '' OR ISNULL($field), $replace_statement, $field)";
  }

  private function getProductReplaceStatement(string $string, $id_lang, string|bool $fill = false)
  {
    $replace = $this->getReplaceStatement($string, 'product');
    $sql = $this->getCategoryNameQuery($id_lang);
    $replace = sprintf("REPLACE(%s, '{category}', (%s))", $replace, $sql);
    if ($fill) {
      $replace = $this->transformToFillOnly($replace, $fill);
    }
    return $replace;
  }

  private function getCategoryNameQuery($id_lang)
  {
    $sql = (new DbQuery())
    ->select('cl.name')
    ->from('product', 'p')
    ->innerJoin('category_lang', 'cl', 'p.id_category_default = cl.id_category AND id_lang = '.$id_lang)
    ->where('p.id_product = {prefix}product_lang.id_product')->__toString();

    return str_replace('{prefix}', _DB_PREFIX_, $sql);
  }

  public function isConfigOk()
  {
    $langs = $this->context->controller->getLanguages();
    $return = true;
    Configuration::loadConfiguration();
    foreach ($langs as $lang) {
      $id_lang = $lang['id_lang'];
      $configs = Configuration::getMultiple([
        Configurations::CATEGORY_DESCRIPTION,
        Configurations::CATEGORY_TITLE,
        Configurations::PRODUCT_TITLE,
        Configurations::PRODUCT_DESCRIPTION,
        Configurations::MANUFACTURER_TITLE,
        Configurations::MANUFACTURER_DESCRIPTION
      ], $id_lang);
      $return &= array_reduce($configs, fn($prev, $curr) => $prev && !empty($curr), true);
      if (!$return) break;
    }

    return $return;
  }

  private function updateCategory(Category $category)
  {
    $aDescriptionFormat = Configuration::getConfigInMultipleLangs(Configurations::CATEGORY_DESCRIPTION); 
    $aTitleFormat = Configuration::getConfigInMultipleLangs(Configurations::CATEGORY_TITLE);
    $this->updateEntitySEO($category, 'category', $aTitleFormat, $aDescriptionFormat);
  }

  private function updateManufacturer(Manufacturer $manufacturer)
  {
    $aDescriptionFormat = Configuration::getConfigInMultipleLangs(Configurations::MANUFACTURER_DESCRIPTION); 
    $aTitleFormat = Configuration::getConfigInMultipleLangs(Configurations::MANUFACTURER_TITLE);
    $this->updateEntitySEO($manufacturer, 'manufacturer', $aTitleFormat, $aDescriptionFormat);
  }

  private function updateEntitySEO(Category|Manufacturer $entity, string $entity_name, array $aTitleFormat, array $aDescriptionFormat)
  {
    $langs = $this->context->controller->getLanguages();
    $edit = false;
    foreach ($langs as $lang) {
      $descriptionFormat = $aDescriptionFormat[$lang['id_lang']];
      $titleFormat = $aTitleFormat[$lang['id_lang']];

      if (!empty($descriptionFormat) && property_exists($entity, 'meta_description') && empty($entity->meta_description[$lang['id_lang']])) {
        $entity->meta_description[$lang['id_lang']] = $this->replacePlaceholders($descriptionFormat, [$entity_name => $entity->name], $lang['id_lang'] );
        $edit = true;
      }

      if (!empty($titleFormat) && property_exists($entity, 'meta_title') && empty($entity->meta_title[$lang['id_lang']])) {
        $entity->meta_title[$lang['id_lang']] = $this->replacePlaceholders($titleFormat, [$entity_name => $entity->name], $lang['id_lang']);
        $edit = true;
      }
      
    }
    if ($edit) {
      $entity->save();
    }
  }
  
}
