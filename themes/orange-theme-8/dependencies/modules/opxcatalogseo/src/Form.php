<?php

namespace Orangepix\Opxcatalogseo;

use HelperForm;
use AdminController;
use Tools;
use Configuration;

class Form
{
  /**
   * @var \Opxcatalogseo
   */
  private $module;

  /**
   * @var array
   */
  private $fields;

  private $translator;

  /**
   * @var \Context
   */
  private $context;

  public function __construct($module)
  {
    $this->module= $module;
    $this->context = \Context::getContext();
    $this->translator = $this->context->getTranslator();
    $this->setFields();
  }

  private function setFields()
  {
    $this->fields = [
      [
        'type' => 'text',
        'label' => $this->module->l('Category page title', 'form'),
        'desc' => $this->module->l('Use {category} placeholder for category name and {shop} for the shop\'s name', 'form'),
        'name' => Configurations::CATEGORY_TITLE,
        'required' => true,
        'lang' => true,
      ],
      [
        'type' => 'textarea',
        'label' => $this->module->l('Category meta description', 'form'),
        'desc' => $this->module->l('Use {category} placeholder for category name and {shop} for the shop\'s name', 'form'),
        'name' => Configurations::CATEGORY_DESCRIPTION,
        'required' => true,
        'lang' => true,
      ],
      [
        'type' => 'text',
        'label' => $this->module->l('Product page title', 'form'),
        'desc' => $this->module->l('Use {product} placeholder for product name, {category} for category name and {shop} for the shop\'s name', 'form'),
        'name' => Configurations::PRODUCT_TITLE,
        'required' => true,
        'lang' => true,
      ],
      [
        'type' => 'textarea',
        'label' => $this->module->l('Product meta description', 'form'),
        'desc' => $this->module->l('Use {product} placeholder for product name, {category} for category name and {shop} for the shop\'s name', 'form'),
        'name' => Configurations::PRODUCT_DESCRIPTION,
        'required' => true,
        'lang' => true,
      ],
      [
        'type' => 'text',
        'label' => $this->module->l('Manufacturer page title', 'form'),
        'desc' => $this->module->l('Use {manufacturer} placeholder for manufacturer name and {shop} for the shop\'s name', 'form'),
        'name' => Configurations::MANUFACTURER_TITLE,
        'required' => true,
        'lang' => true,
      ],
      [
        'type' => 'textarea',
        'label' => $this->module->l('Manufacturer meta description', 'form'),
        'desc' => $this->module->l('Use {manufacturer} placeholder for manufacturer name and {shop} for the shop\'s name', 'form'),
        'name' => Configurations::MANUFACTURER_DESCRIPTION,
        'required' => true,
        'lang' => true,
      ],
      [

        'type' => 'switch',
        'label' => $this->module->l('Replace category name var with H1 value', 'form'),
        'desc' => $this->module->l('Recommended in most cases', 'form'),
        'hint' => $this->module->l('In category page, smarty var $category.name value is replaced with $cateogory.h1 (new param from module). Disable this option if you are able to customize your theme files and you want more flexibility', 'form'),
        'name' => Configurations::H1_OVERRIDE_CATEGORY_NAME,
        'is_bool' => true,
        'values' => array(
          [
            'id' => Configurations::H1_OVERRIDE_CATEGORY_NAME.'_on',
            'value' => 1,
          ],
          [
            'id' => Configurations::H1_OVERRIDE_CATEGORY_NAME.'_off',
            'value' => 0,
          ],
        )
      ]

    ];
  }

  /**
   * Builds the configuration form
   * @return string HTML code
   */
  public function displayForm()
  {
    // Init Fields form array
    $form = [
      'form' => [
        'legend' => [
          'title' => $this->module->l('Settings', 'form'),
        ],
        'input' => $this->fields,
        'submit' => [
          'title' => $this->module->getTranslator()->trans('Save', [], 'Admin.Global'),
          'class' => 'btn btn-default pull-right',
        ],
      ],
    ];

    $helper = new HelperForm();

    // Module, token and currentIndex
    $helper->table = 'module';
    $helper->name_controller = $this->module->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->module->name]);
    $helper->submit_action = 'submit' . $this->module->name;

    // Default language
    $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

    // Load current value into the form
    $helper->fields_value = $this->module->getConfigValues($this->fields);

    $helper->tpl_vars = array(  
      'languages' => $this->context->controller->getLanguages(),
      'id_language' => $this->context->language->id,
    );

    return $helper->generateForm([$form]);
  }


}
