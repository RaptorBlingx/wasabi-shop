<?php
  /**
   * Copyright (c) 2021. OrangePix  All rights reserved.
   * DISCLAIMER
   * Do not edit, modify or copy this file.
   * If you wish to customize it, contact us at info@orangepix.it
   *
   * @author    Davide Mazzonetto <davide.mazzonetto@orangepix.it> 
   * @license   Do not edit, modify or copy this file
   * @copyright OrangePix Srl
   */

/**
 * @autor Carlos Batista <carlos.batista@orangepix.it>
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use OrangePix\Classes\Op_HomeSlide;

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

class Opxslider extends Module implements WidgetInterface
{
    protected $_html = '';
    protected $default_width = 779;
    protected $default_speed = 5000;
    protected $default_pause_on_hover = 1;
    protected $default_wrap = 1;
    protected $templateFile;

    public function __construct()
    {
        $this->name = 'opxslider';
        $this->tab = 'front_office_features';
        $this->version = '2.2.2';
        $this->author = 'OrangePix Srl';
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->getTranslator()->trans('Image slider', array(), 'Modules.Opxslider.Admin');
        $this->description = $this->getTranslator()->trans('Slider con supporto immagini mobile', array(), 'Modules.Opxslider.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7.6.0', 'max' => _PS_VERSION_);

        $this->templateFile = 'module:opxslider/views/templates/hook/slider.tpl';
    }

    /**
     * @see Module::install()
     */
    public function install()
    {
        /* Adds Module */
        if (parent::install() &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayHome') &&
            $this->registerHook('actionShopDataDuplication')
        ) {
            $shops = Shop::getContextListShopID();
            $shop_groups_list = array();

            /* Setup each shop */
            foreach ($shops as $shop_id) {
                $shop_group_id = (int)Shop::getGroupFromShop($shop_id, true);

                if (!in_array($shop_group_id, $shop_groups_list)) {
                    $shop_groups_list[] = $shop_group_id;
                }

                /* Sets up configuration */
                $res = Configuration::updateValue('OP_HOMESLIDER_SPEED', $this->default_speed, false, $shop_group_id, $shop_id);
                $res &= Configuration::updateValue('OP_HOMESLIDER_PAUSE_ON_HOVER', $this->default_pause_on_hover, false, $shop_group_id, $shop_id);
                $res &= Configuration::updateValue('OP_HOMESLIDER_WRAP', $this->default_wrap, false, $shop_group_id, $shop_id);
            }

            /* Sets up Shop Group configuration */
            if (count($shop_groups_list)) {
                foreach ($shop_groups_list as $shop_group_id) {
                    $res &= Configuration::updateValue('OP_HOMESLIDER_SPEED', $this->default_speed, false, $shop_group_id);
                    $res &= Configuration::updateValue('OP_HOMESLIDER_PAUSE_ON_HOVER', $this->default_pause_on_hover, false, $shop_group_id);
                    $res &= Configuration::updateValue('OP_HOMESLIDER_WRAP', $this->default_wrap, false, $shop_group_id);
                }
            }

            /* Sets up Global configuration */
            $res &= Configuration::updateValue('OP_HOMESLIDER_SPEED', $this->default_speed);
            $res &= Configuration::updateValue('OP_HOMESLIDER_PAUSE_ON_HOVER', $this->default_pause_on_hover);
            $res &= Configuration::updateValue('OP_HOMESLIDER_WRAP', $this->default_wrap);

            /* Creates tables */
            $res &= $this->createTables();

            /* Adds samples */
            if ($res) {
                $this->installSamples();
            }

            return (bool)$res;
        }

        return false;
    }

    /**
     * Adds samples
     */
    protected function installSamples()
    {
        $languages = Language::getLanguages(false);
        for ($i = 1; $i <= 3; ++$i) {
            $slide = new Op_HomeSlide();
            $slide->position = $i;
            $slide->active = 1;
            foreach ($languages as $language) {
                $slide->title[$language['id_lang']] = 'Sample '.$i;
                $slide->description[$language['id_lang']] = '<h3>EXCEPTEUR OCCAECAT</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin tristique in tortor et dignissim. Quisque non tempor leo. Maecenas egestas sem elit</p>';
                $slide->legend[$language['id_lang']] = 'sample-'.$i;
                $slide->url[$language['id_lang']] = '#';
                $slide->image[$language['id_lang']] = 'sample-'.$i.'.jpg';
            }
            $slide->add();
        }
    }

    /**
     * @see Module::uninstall()
     */
    public function uninstall()
    {
        /* Deletes Module */
        if (parent::uninstall()) {
            /* Deletes tables */
            $res = $this->deleteTables();

            /* Unsets configuration */
            $res &= Configuration::deleteByName('OP_HOMESLIDER_SPEED');
            $res &= Configuration::deleteByName('OP_HOMESLIDER_PAUSE_ON_HOVER');
            $res &= Configuration::deleteByName('OP_HOMESLIDER_WRAP');

            return (bool)$res;
        }

        return false;
    }

    /**
     * Creates tables
     */
    protected function createTables()
    {
        /* Slides */
        $res = (bool)Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opxslider` (
                `id_opxslider_slides` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_opxslider_slides`, `id_shop`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');

        /* Slides configuration */
        $res &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opxslider_slides` (
              `id_opxslider_slides` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `position` int(10) unsigned NOT NULL DEFAULT \'0\',
              `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
              PRIMARY KEY (`id_opxslider_slides`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');

        /* Slides lang configuration */
        $res &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opxslider_slides_lang` (
              `id_opxslider_slides` int(10) unsigned NOT NULL,
              `id_lang` int(10) unsigned NOT NULL,
              `title` varchar(255) NOT NULL,
              `description` text NOT NULL,
              `legend` varchar(255) NOT NULL,
              `url` varchar(255) NOT NULL,
              `cta` varchar(255) NOT NULL,
              `image` varchar(255) NOT NULL,
              `image_mobile` varchar(255) NOT NULL,
              PRIMARY KEY (`id_opxslider_slides`,`id_lang`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');

        return $res;
    }

    /**
     * deletes tables
     */
    protected function deleteTables()
    {
        $slides = $this->getSlides();
        foreach ($slides as $slide) {
            $to_del = new Op_HomeSlide($slide['id_slide']);
            $to_del->delete();
        }

        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS `'._DB_PREFIX_.'opxslider`, `'._DB_PREFIX_.'opxslider_slides`, `'._DB_PREFIX_.'opxslider_slides_lang`;
        ');
    }

    public function getContent()
    {
        $this->_html .= $this->headerHTML();

        /* Validate & process */
        if (Tools::isSubmit('submitSlide') || Tools::isSubmit('delete_id_slide') ||
            Tools::isSubmit('submitSlider') ||
            Tools::isSubmit('changeStatus')
        ) {
            if ($this->_postValidation()) {
                $this->_postProcess();
                $this->_html .= $this->renderForm();
                $this->_html .= $this->renderList();
            } else {
                $this->_html .= $this->renderAddForm();
            }

            $this->clearCache();
        } elseif (Tools::isSubmit('addSlide') || (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide')))) {
            if (Tools::isSubmit('addSlide')) {
                $mode = 'add';
            } else {
                $mode = 'edit';
            }

            if ($mode == 'add') {
                if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL) {
                    $this->_html .= $this->renderAddForm();
                } else {
                    $this->_html .= $this->getShopContextError(null, $mode);
                }
            } else {
                $associated_shop_ids = Op_HomeSlide::getAssociatedIdsShop((int) Tools::getValue('id_slide'));
                $context_shop_id     = (int) Shop::getContextShopID();

                if ($associated_shop_ids === false) {
                    $this->_html .= $this->getShopAssociationError((int)Tools::getValue('id_slide'));
                } elseif (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL && in_array($context_shop_id, $associated_shop_ids)) {
                    if (count($associated_shop_ids) > 1) {
                        $this->_html = $this->getSharedSlideWarning();
                    }
                    $this->_html .= $this->renderAddForm();
                } else {
                    $shops_name_list = array();
                    foreach ($associated_shop_ids as $shop_id) {
                        $associated_shop = new Shop((int)$shop_id);
                        $shops_name_list[] = $associated_shop->name;
                    }
                    $this->_html .= $this->getShopContextError($shops_name_list, $mode);
                }
            }
        } else {
            $this->_html .= $this->getWarningMultishopHtml().$this->getCurrentShopInfoMsg().$this->renderForm();

            if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL) {
                $this->_html .= $this->renderList();
            }
        }

        return $this->_html;
    }

    protected function _postValidation()
    {
        $errors = array();

        /* Validation for Slider configuration */
        if (Tools::isSubmit('submitSlider')) {
            if (!Validate::isInt(Tools::getValue('OP_HOMESLIDER_SPEED'))) {
                $errors[] = $this->getTranslator()->trans('Invalid values', array(), 'Modules.Opxslider.Admin');
            }
        } elseif (Tools::isSubmit('changeStatus')) {
            if (!Validate::isInt(Tools::getValue('id_slide'))) {
                $errors[] = $this->getTranslator()->trans('Invalid slide', array(), 'Modules.Opxslider.Admin');
            }
        } elseif (Tools::isSubmit('submitSlide')) {
            /* Checks state (active) */
            if (!Validate::isInt(Tools::getValue('active_slide')) || (Tools::getValue('active_slide') != 0 && Tools::getValue('active_slide') != 1)) {
                $errors[] = $this->getTranslator()->trans('Invalid slide state.', array(), 'Modules.Opxslider.Admin');
            }
            /* If edit : checks id_slide */
            if (Tools::isSubmit('id_slide')) {
                if (!Validate::isInt(Tools::getValue('id_slide')) && !$this->slideExists(Tools::getValue('id_slide'))) {
                    $errors[] = $this->getTranslator()->trans('Invalid slide ID', array(), 'Modules.Opxslider.Admin');
                }
            }
            /* Checks title/url/legend/description/image */
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                if (Tools::strlen(Tools::getValue('title_' . $language['id_lang'])) > 255) {
                    $errors[] = $this->getTranslator()->trans('The title is too long.', array(), 'Modules.Opxslider.Admin');
                }
                if (Tools::strlen(Tools::getValue('legend_' . $language['id_lang'])) > 255) {
                    $errors[] = $this->getTranslator()->trans('The caption is too long.', array(), 'Modules.Opxslider.Admin');
                }
                if (Tools::strlen(Tools::getValue('cta_' . $language['id_lang'])) > 255) {
                    $errors[] = $this->getTranslator()->trans('The CTA is too long.', array(), 'Modules.Opxslider.Admin');
                }
                if (Tools::strlen(Tools::getValue('url_' . $language['id_lang'])) > 255) {
                    $errors[] = $this->getTranslator()->trans('The URL is too long.', array(), 'Modules.Opxslider.Admin');
                }
                if (Tools::strlen(Tools::getValue('description_' . $language['id_lang'])) > 4000) {
                    $errors[] = $this->getTranslator()->trans('The description is too long.', array(), 'Modules.Opxslider.Admin');
                }
                if (Tools::strlen(Tools::getValue('url_' . $language['id_lang'])) > 0 && !Validate::isUrl(Tools::getValue('url_' . $language['id_lang']))) {
                    $errors[] = $this->getTranslator()->trans('The URL format is not correct.', array(), 'Modules.Opxslider.Admin');
                }
                if (Tools::getValue('image_' . $language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_' . $language['id_lang']))) {
                    $errors[] = $this->getTranslator()->trans('Invalid filename.', array(), 'Modules.Opxslider.Admin');
                }
                if (Tools::getValue('image_old_' . $language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_old_' . $language['id_lang']))) {
                    $errors[] = $this->getTranslator()->trans('Invalid filename.', array(), 'Modules.Opxslider.Admin');
                }
                if (Tools::getValue('image_mobile_' . $language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_mobile_' . $language['id_lang']))) {
                    $errors[] = $this->getTranslator()->trans('Invalid filename.', array(), 'Modules.Opxslider.Admin');
                }
                if (Tools::getValue('image_mobile_old_' . $language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_mobile_old_' . $language['id_lang']))) {
                    $errors[] = $this->getTranslator()->trans('Invalid filename.', array(), 'Modules.Opxslider.Admin');
                }
            }

            /* Checks title/url/legend/description for default lang */
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            if (Tools::strlen(Tools::getValue('url_' . $id_lang_default)) == 0) {
                $errors[] = $this->getTranslator()->trans('The URL is not set.', array(), 'Modules.Opxslider.Admin');
            }
            if (!Tools::isSubmit('has_picture') && (!isset($_FILES['image_' . $id_lang_default]) || empty($_FILES['image_' . $id_lang_default]['tmp_name']))) {
                $errors[] = $this->getTranslator()->trans('The image is not set.', array(), 'Modules.Opxslider.Admin');
            }
            if (Tools::getValue('image_old_'.$id_lang_default) && !Validate::isFileName(Tools::getValue('image_old_'.$id_lang_default))) {
                $errors[] = $this->getTranslator()->trans('The image is not set.', array(), 'Modules.Opxslider.Admin');
            }
        } elseif (Tools::isSubmit('delete_id_slide') && (!Validate::isInt(Tools::getValue('delete_id_slide')) || !$this->slideExists((int)Tools::getValue('delete_id_slide')))) {
            $errors[] = $this->getTranslator()->trans('Invalid slide ID', array(), 'Modules.Opxslider.Admin');
        }

        /* Display errors if needed */
        if (count($errors)) {
            $this->_html .= $this->displayError(implode('<br />', $errors));

            return false;
        }

        /* Returns if validation is ok */

        return true;
    }

    protected function _postProcess()
    {
        $errors = array();
        $shop_context = Shop::getContext();

        /* Processes Slider */
        if (Tools::isSubmit('submitSlider')) {
            $shop_groups_list = array();
            $shops = Shop::getContextListShopID();

            foreach ($shops as $shop_id) {
                $shop_group_id = (int)Shop::getGroupFromShop($shop_id, true);

                if (!in_array($shop_group_id, $shop_groups_list)) {
                    $shop_groups_list[] = $shop_group_id;
                }

                $res = Configuration::updateValue('OP_HOMESLIDER_SPEED', (int)Tools::getValue('OP_HOMESLIDER_SPEED'), false, $shop_group_id, $shop_id);
                $res &= Configuration::updateValue('OP_HOMESLIDER_PAUSE_ON_HOVER', (int)Tools::getValue('OP_HOMESLIDER_PAUSE_ON_HOVER'), false, $shop_group_id, $shop_id);
                $res &= Configuration::updateValue('OP_HOMESLIDER_WRAP', (int)Tools::getValue('OP_HOMESLIDER_WRAP'), false, $shop_group_id, $shop_id);
            }

            /* Update global shop context if needed*/
            switch ($shop_context) {
                case Shop::CONTEXT_ALL:
                    $res &= Configuration::updateValue('OP_HOMESLIDER_SPEED', (int)Tools::getValue('OP_HOMESLIDER_SPEED'));
                    $res &= Configuration::updateValue('OP_HOMESLIDER_PAUSE_ON_HOVER', (int)Tools::getValue('OP_HOMESLIDER_PAUSE_ON_HOVER'));
                    $res &= Configuration::updateValue('OP_HOMESLIDER_WRAP', (int)Tools::getValue('OP_HOMESLIDER_WRAP'));
                    if (count($shop_groups_list)) {
                        foreach ($shop_groups_list as $shop_group_id) {
                            $res &= Configuration::updateValue('OP_HOMESLIDER_SPEED', (int)Tools::getValue('OP_HOMESLIDER_SPEED'), false, $shop_group_id);
                            $res &= Configuration::updateValue('OP_HOMESLIDER_PAUSE_ON_HOVER', (int)Tools::getValue('OP_HOMESLIDER_PAUSE_ON_HOVER'), false, $shop_group_id);
                            $res &= Configuration::updateValue('OP_HOMESLIDER_WRAP', (int)Tools::getValue('OP_HOMESLIDER_WRAP'), false, $shop_group_id);
                        }
                    }
                    break;
                case Shop::CONTEXT_GROUP:
                    if (count($shop_groups_list)) {
                        foreach ($shop_groups_list as $shop_group_id) {
                            $res &= Configuration::updateValue('OP_HOMESLIDER_SPEED', (int)Tools::getValue('OP_HOMESLIDER_SPEED'), false, $shop_group_id);
                            $res &= Configuration::updateValue('OP_HOMESLIDER_PAUSE_ON_HOVER', (int)Tools::getValue('OP_HOMESLIDER_PAUSE_ON_HOVER'), false, $shop_group_id);
                            $res &= Configuration::updateValue('OP_HOMESLIDER_WRAP', (int)Tools::getValue('OP_HOMESLIDER_WRAP'), false, $shop_group_id);
                        }
                    }
                    break;
            }

            $this->clearCache();

            if (!$res) {
                $errors[] = $this->displayError($this->getTranslator()->trans('The configuration could not be updated.', array(), 'Modules.Opxslider.Admin'));
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=6&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
            }
        } elseif (Tools::isSubmit('changeStatus') && Tools::isSubmit('id_slide')) {
            $slide = new Op_HomeSlide((int)Tools::getValue('id_slide'));
            if ($slide->active == 0) {
                $slide->active = 1;
            } else {
                $slide->active = 0;
            }
            $res = $slide->update();
            $this->clearCache();
            $this->_html .= ($res ? $this->displayConfirmation($this->getTranslator()->trans('Configuration updated', array(), 'Admin.Notifications.Success')) : $this->displayError($this->getTranslator()->trans('The configuration could not be updated.', array(), 'Modules.Opxslider.Admin')));
        } elseif (Tools::isSubmit('submitSlide')) {
            /* Sets ID if needed */
            if (Tools::getValue('id_slide')) {
                $slide = new Op_HomeSlide((int)Tools::getValue('id_slide'));
                if (!Validate::isLoadedObject($slide)) {
                    $this->_html .= $this->displayError($this->getTranslator()->trans('Invalid slide ID', array(), 'Modules.Opxslider.Admin'));
                    return false;
                }
            } else {
                $slide = new Op_HomeSlide();
                /* Sets position */
                $slide->position = (int)$this->getNextPosition();
            }
            /* Sets active */
            $slide->active = (int)Tools::getValue('active_slide');

            /* Sets each langue fields */
            $languages = Language::getLanguages(false);

            foreach ($languages as $language) {
                $slide->title[$language['id_lang']] = Tools::getValue('title_'.$language['id_lang']);
                $slide->url[$language['id_lang']] = Tools::getValue('url_'.$language['id_lang']);
                $slide->legend[$language['id_lang']] = Tools::getValue('legend_'.$language['id_lang']);
                $slide->cta[$language['id_lang']] = Tools::getValue('cta_'.$language['id_lang']);
                $slide->description[$language['id_lang']] = Tools::getValue('description_'.$language['id_lang']);

                /* Uploads image and sets slide */
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES['image_'.$language['id_lang']]['name'], '.'), 1));
				
                if (isset($_FILES['image_'.$language['id_lang']]) &&
                    isset($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
                    !empty($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
                    !empty($imagesize = @getimagesize($_FILES['image_'.$language['id_lang']]['tmp_name'])) &&
                    in_array(
                        Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)),
                        array(
                            'jpg',
                            'gif',
                            'jpeg',
                            'png'
                        )
                    ) &&
                    in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                ) {
                    $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                    $salt = sha1(microtime());
                    if ($error = ImageManager::validateUpload($_FILES['image_'.$language['id_lang']])) {
                        $errors[] = $error;
                    } elseif (!$temp_name || !move_uploaded_file($_FILES['image_'.$language['id_lang']]['tmp_name'], $temp_name)) {
                        return false;
                    } elseif (!ImageManager::resize($temp_name, __DIR__ . '/views/img/' .$salt.'_'. $_FILES['image_'. $language['id_lang']]['name'], null, null, $type)) {
                        $errors[] = $this->displayError($this->getTranslator()->trans('An error occurred during the image upload process.', array(), 'Admin.Notifications.Error'));
                    }
                    if (isset($temp_name)) {
                        @unlink($temp_name);
                    }
                    $slide->image[$language['id_lang']] = $salt.'_'.$_FILES['image_'.$language['id_lang']]['name'];
                } elseif (Tools::getValue('image_old_'.$language['id_lang']) != '') {
                    $slide->image[$language['id_lang']] = Tools::getValue('image_old_' . $language['id_lang']);
                }

                /* Upload image for mobile */
                $type_mobile = Tools::strtolower(Tools::substr(strrchr($_FILES['image_mobile_'.$language['id_lang']]['name'], '.'), 1));
                if (isset($_FILES['image_mobile_'.$language['id_lang']]) &&
                    isset($_FILES['image_mobile_'.$language['id_lang']]['tmp_name']) &&
                    !empty($_FILES['image_mobile_'.$language['id_lang']]['tmp_name']) &&
                    !empty($imagesize_mobile = @getimagesize($_FILES['image_mobile_'.$language['id_lang']]['tmp_name'])) &&
                    in_array(
                        Tools::strtolower(Tools::substr(strrchr($imagesize_mobile['mime'], '/'), 1)),
                        array(
                            'jpg',
                            'gif',
                            'jpeg',
                            'png'
                        )
                    ) &&
                    in_array($type_mobile, array('jpg', 'gif', 'jpeg', 'png'))
                ) {
                    $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                    $salt = sha1(microtime());
                    if ($error = ImageManager::validateUpload($_FILES['image_mobile_'.$language['id_lang']])) {
                        $errors[] = $error;
                    } elseif (!$temp_name || !move_uploaded_file($_FILES['image_mobile_'.$language['id_lang']]['tmp_name'], $temp_name)) {
                        return false;
                    }
                    if (isset($temp_name)) {
                        @copy($temp_name, __DIR__ . '/views/img/' .$salt.'_'. $_FILES['image_mobile_'. $language['id_lang']]['name']);
                        @unlink($temp_name);
                    }
                    $slide->image_mobile[$language['id_lang']] = $salt.'_'.$_FILES['image_mobile_'.$language['id_lang']]['name'];
                } elseif (Tools::getValue('image_mobile_old_'.$language['id_lang']) != '') {
                    $slide->image_mobile[$language['id_lang']] = Tools::getValue('image_mobile_old_' . $language['id_lang']);
                }
            }

            /* Processes if no errors  */
            if (!$errors) {
                /* Adds */
                if (!Tools::getValue('id_slide')) {
                    if (!$slide->add()) {
                        $errors[] = $this->displayError($this->getTranslator()->trans('The slide could not be added.', array(), 'Modules.Opxslider.Admin'));
                    }
                } elseif (!$slide->update()) {
                    $errors[] = $this->displayError($this->getTranslator()->trans('The slide could not be updated.', array(), 'Modules.Opxslider.Admin'));
                }
                $this->clearCache();
            }
        } elseif (Tools::isSubmit('delete_id_slide')) {
            $slide = new Op_HomeSlide((int)Tools::getValue('delete_id_slide'));
            $res = $slide->delete();
            $this->clearCache();
            if (!$res) {
                $this->_html .= $this->displayError('Could not delete.');
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=1&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
            }
        }

        /* Display errors if needed */
        if (count($errors)) {
            $this->_html .= $this->displayError(implode('<br />', $errors));
        } elseif (Tools::isSubmit('submitSlide') && Tools::getValue('id_slide')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=4&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
        } elseif (Tools::isSubmit('submitSlide')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=3&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
        }
    }

    public function hookdisplayHeader($params)
    {
        $this->context->controller->registerStylesheet('modules-homeslider', 'modules/'.$this->name.'/views/css/opxslider.css', ['media' => 'all', 'priority' => 150]); 
        $this->context->controller->registerJavascript('modules-homeslider', 'modules/'.$this->name.'/views/js/opxslider.js', ['position' => 'bottom', 'priority' => 150]);
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        $this->clearCache();
        if (!$this->isCached($this->templateFile, $this->getCacheId())) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($this->templateFile, $this->getCacheId());
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $slides = $this->getSlides(true);
        if (is_array($slides)) {
            foreach ($slides as &$slide) {
                $slide['sizes'] = @getimagesize((__DIR__ . DIRECTORY_SEPARATOR . 'views/img' . DIRECTORY_SEPARATOR . $slide['image']));
                if (isset($slide['sizes'][3]) && $slide['sizes'][3]) {
                    $slide['size'] = $slide['sizes'][3];
                }
            }
        }

        $config = $this->getConfigFieldsValues();

        return [
            'homeslider' => [
                'speed' => $config['OP_HOMESLIDER_SPEED'],
                'pause' => $config['OP_HOMESLIDER_PAUSE_ON_HOVER'] ? 'hover' : '',
                'wrap' => $config['OP_HOMESLIDER_WRAP'] ? 'true' : 'false',
                'slides' => $slides,
            ],
        ];
    }

    protected function updateUrl($link)
    {
        if (substr($link, 0, 7) !== "http://" && substr($link, 0, 8) !== "https://") {
            $link = "http://" . $link;
        }

        return $link;
    }

    public function clearCache()
    {
        $this->_clearCache($this->templateFile);
    }

    public function hookActionShopDataDuplication($params)
    {
        Db::getInstance()->execute(
            '
            INSERT IGNORE INTO '._DB_PREFIX_.'opxslider (id_opxslider_slides, id_shop)
            SELECT id_opxslider_slides, '.(int)$params['new_id_shop'].'
            FROM '._DB_PREFIX_.'opxslider
            WHERE id_shop = '.(int)$params['old_id_shop']
        );
        $this->clearCache();
    }

    public function headerHTML()
    {
        if (Tools::getValue('controller') != 'AdminModules' && Tools::getValue('configure') != $this->name) {
            return;
        }

        $this->context->controller->addJqueryUI('ui.sortable');
        /* Style & js for fieldset 'slides configuration' */
        $html = '<script type="text/javascript">
            $(function() {
                var $mySlides = $("#slides");
                $mySlides.sortable({
                    opacity: 0.6,
                    cursor: "move",
                    update: function() {
                        var order = $(this).sortable("serialize") + "&action=updateSlidesPosition";
                        $.post("'.$this->context->shop->physical_uri.$this->context->shop->virtual_uri.'modules/'.$this->name.'/ajax_'.$this->name.'.php?secure_key='.$this->secure_key.'", order);
                        }
                    });
                $mySlides.hover(function() {
                    $(this).css("cursor","move");
                    },
                    function() {
                    $(this).css("cursor","auto");
                });
            });
        </script>';

        return $html;
    }

    public function getNextPosition()
    {
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            '
            SELECT MAX(hss.`position`) AS `next_position`
            FROM `'._DB_PREFIX_.'opxslider_slides` hss, `'._DB_PREFIX_.'opxslider` hs
            WHERE hss.`id_opxslider_slides` = hs.`id_opxslider_slides` AND hs.`id_shop` = '.(int)$this->context->shop->id
        );

        return (++$row['next_position']);
    }

    public function getSlides($active = null)
    {
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;

        $slides = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
            SELECT hs.`id_opxslider_slides` as id_slide, hss.`position`, hss.`active`, hssl.`title`,
            hssl.`url`, hssl.`cta`, hssl.`legend`, hssl.`description`, hssl.`image`, hssl.`image_mobile`
            FROM '._DB_PREFIX_.'opxslider hs
            LEFT JOIN '._DB_PREFIX_.'opxslider_slides hss ON (hs.id_opxslider_slides = hss.id_opxslider_slides)
            LEFT JOIN '._DB_PREFIX_.'opxslider_slides_lang hssl ON (hss.id_opxslider_slides = hssl.id_opxslider_slides)
            WHERE id_shop = '.(int)$id_shop.'
            AND hssl.id_lang = '.(int)$id_lang.
            ($active ? ' AND hss.`active` = 1' : ' ').'
            ORDER BY hss.position'
        );

        foreach ($slides as &$slide) {
            $slide['image_url'] = $this->context->link->getMediaLink(_MODULE_DIR_.'opxslider/views/img/'.$slide['image']);
            $slide['image_mobile_url'] = empty($slide['image_mobile']) ? '' : $this->context->link->getMediaLink(_MODULE_DIR_.'opxslider/views/img/'.$slide['image_mobile']);
        }

        return $slides;
    }

    public function getAllImagesBySlidesId($id_slides, $active = null, $id_shop = null)
    {
        $this->context = Context::getContext();
        $images = array();

        if (!isset($id_shop)) {
            $id_shop = $this->context->shop->id;
        }

        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
            SELECT hssl.`image`, hssl.`id_lang`
            FROM '._DB_PREFIX_.'opxslider hs
            LEFT JOIN '._DB_PREFIX_.'opxslider_slides hss ON (hs.id_opxslider_slides = hss.id_opxslider_slides)
            LEFT JOIN '._DB_PREFIX_.'opxslider_slides_lang hssl ON (hss.id_opxslider_slides = hssl.id_opxslider_slides)
            WHERE hs.`id_opxslider_slides` = '.(int)$id_slides.' AND hs.`id_shop` = '.(int)$id_shop.
            ($active ? ' AND hss.`active` = 1' : ' ')
        );

        foreach ($results as $result) {
            $images[$result['id_lang']] = $result['image'];
        }

        return $images;
    }

    public function displayStatus($id_slide, $active)
    {
        $title = ((int)$active == 0 ? $this->getTranslator()->trans('Disabled', array(), 'Admin.Global') : $this->getTranslator()->trans('Enabled', array(), 'Admin.Global'));
        $icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
        $class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
        $html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
            '&configure='.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules').
                '&changeStatus&id_slide='.(int)$id_slide.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';

        return $html;
    }

    public function slideExists($id_slide)
    {
        $req = 'SELECT hs.`id_opxslider_slides` as id_slide
                FROM `'._DB_PREFIX_.'opxslider` hs
                WHERE hs.`id_opxslider_slides` = '.(int)$id_slide;
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);

        return ($row);
    }

    public function renderList()
    {
        $slides = $this->getSlides();
        foreach ($slides as $key => $slide) {
            $slides[$key]['status'] = $this->displayStatus($slide['id_slide'], $slide['active']);
            $associated_shop_ids = Op_HomeSlide::getAssociatedIdsShop((int) $slide['id_slide']);
            if ($associated_shop_ids && count($associated_shop_ids) > 1) {
                $slides[$key]['is_shared'] = true;
            } else {
                $slides[$key]['is_shared'] = false;
            }
        }

        $this->context->smarty->assign(
            array(
                'link' => $this->context->link,
                'slides' => $slides,
                'image_baseurl' => $this->_path.'views/img/'
            )
        );

        return $this->display(__FILE__, 'list.tpl');
    }

    public function renderAddForm()
    {
        //Image Thumbnail
        $image_size = 0;
        if (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide'))) {
            $slide = new Op_HomeSlide((int)Tools::getValue('id_slide'));
            $image = $this->local_path.'views/img/'.$slide->image_mobile[(int)Configuration::get('PS_LANG_DEFAULT')];
            $image_url = array();
            foreach (Language::getIDs() as $lang) {
                $image_path = $this->_path.'views/img/'.$slide->image_mobile[$lang];
                $image_url[$lang] = '<img src="' . $image_path . '" alt="" class="imgm img-thumbnail" width="300" height="auto" />';
            }
            $image_size = file_exists($image) ? filesize($image) / 1000 : false;
        }


        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->getTranslator()->trans('Slide information', array(), 'Modules.Opxslider.Admin'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'file_lang',
                        'label' => $this->getTranslator()->trans('Image', array(), 'Admin.Global'),
                        'name' => 'image',
                        'required' => true,
                        'lang' => true,
                        'desc' => $this->getTranslator()->trans('Maximum image size: %s.', array(ini_get('upload_max_filesize')), 'Admin.Global')
                    ),
                    array(
                        'type' => 'file_lang',
                        'label' => $this->getTranslator()->trans('Image mobile', array(), 'Admin.Global'),
                        'name' => 'image_mobile',
                        'required' => false,
                        'display_image' => $image_size ? true : false,
                        'image' => $image_size ? $image_url : null,
                        'lang' => true,
                        'desc' => $this->getTranslator()->trans('Maximum image size: %s.', array(ini_get('upload_max_filesize')), 'Admin.Global')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('Title', array(), 'Admin.Global'),
                        'name' => 'title',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('Target URL', array(), 'Modules.Opxslider.Admin'),
                        'name' => 'url',
                        'required' => true,
                        'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('Caption', array(), 'Modules.Opxslider.Admin'),
                        'name' => 'legend',
                        'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('Call to action', array(), 'Modules.Opxslider.Admin'),
                        'name' => 'cta',
                        'lang' => true,
                        'desc' => $this->getTranslator()->trans('Leave empty to hide button', array(), 'Modules.Opxslider.Admin')
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->getTranslator()->trans('Description', array(), 'Admin.Global'),
                        'name' => 'description',
                        'autoload_rte' => true,
                        'lang' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->getTranslator()->trans('Enabled', array(), 'Admin.Global'),
                        'name' => 'active_slide',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->getTranslator()->trans('Yes', array(), 'Admin.Global')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->getTranslator()->trans('No', array(), 'Admin.Global')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->getTranslator()->trans('Save', array(), 'Admin.Actions'),
                )
            ),
        );

        if (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide'))) {
            $slide = new Op_HomeSlide((int)Tools::getValue('id_slide'));
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_slide');
            $fields_form['form']['img'] = $slide->image;
            /*$fields_form['form']['img'][] = $slide->image_mobile[(int)Configuration::get('PS_LANG_DEFAULT')];*/

            $has_picture = true;

            foreach (Language::getLanguages(false) as $lang) {
                if (!isset($slide->image[$lang['id_lang']])) {
                    $has_picture &= false;
                }
            }

            if ($has_picture) {
                $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'has_picture');
            }
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSlide';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'fields_value' => $this->getAddFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => $this->_path.'views/img/'
        );

        $helper->override_folder = '/';

        $languages = Language::getLanguages(false);

        if (count($languages) > 1) {
            return $this->getMultiLanguageInfoMsg() . $helper->generateForm(array($fields_form));
        } else {
            return $helper->generateForm(array($fields_form));
        }
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->getTranslator()->trans('Settings', array(), 'Admin.Global'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('Speed', array(), 'Modules.Opxslider.Admin'),
                        'name' => 'OP_HOMESLIDER_SPEED',
                        'suffix' => 'milliseconds',
                        'class' => 'fixed-width-sm',
                        'desc' => $this->getTranslator()->trans('The duration of the transition between two slides.', array(), 'Modules.Opxslider.Admin')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->getTranslator()->trans('Pause on hover', array(), 'Modules.Opxslider.Admin'),
                        'name' => 'OP_HOMESLIDER_PAUSE_ON_HOVER',
                        'desc' => $this->getTranslator()->trans('Stop sliding when the mouse cursor is over the slideshow.', array(), 'Modules.Opxslider.Admin'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->getTranslator()->trans('Enabled', array(), 'Admin.Global')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->getTranslator()->trans('Disabled', array(), 'Admin.Global')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->getTranslator()->trans('Loop forever', array(), 'Modules.Opxslider.Admin'),
                        'name' => 'OP_HOMESLIDER_WRAP',
                        'desc' => $this->getTranslator()->trans('Loop or stop after the last slide.', array(), 'Modules.Opxslider.Admin'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->getTranslator()->trans('Enabled', array(), 'Admin.Global')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->getTranslator()->trans('Disabled', array(), 'Admin.Global')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->getTranslator()->trans('Save', array(), 'Admin.Actions'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSlider';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        $id_shop_group = Shop::getContextShopGroupID();
        $id_shop = Shop::getContextShopID();

        return array(
            'OP_HOMESLIDER_SPEED' => Tools::getValue('OP_HOMESLIDER_SPEED', Configuration::get('OP_HOMESLIDER_SPEED', null, $id_shop_group, $id_shop)),
            'OP_HOMESLIDER_PAUSE_ON_HOVER' => Tools::getValue('OP_HOMESLIDER_PAUSE_ON_HOVER', Configuration::get('OP_HOMESLIDER_PAUSE_ON_HOVER', null, $id_shop_group, $id_shop)),
            'OP_HOMESLIDER_WRAP' => Tools::getValue('OP_HOMESLIDER_WRAP', Configuration::get('OP_HOMESLIDER_WRAP', null, $id_shop_group, $id_shop)),
        );
    }

    public function getAddFieldsValues()
    {
        $fields = array();

        if (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide'))) {
            $slide = new Op_HomeSlide((int)Tools::getValue('id_slide'));
            $fields['id_slide'] = (int)Tools::getValue('id_slide', $slide->id);
        } else {
            $slide = new Op_HomeSlide();
        }

        $fields['active_slide'] = Tools::getValue('active_slide', $slide->active);
        $fields['has_picture'] = true;

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $fields['image'][$lang['id_lang']] = Tools::getValue('image_'.(int)$lang['id_lang']);
            $fields['image_mobile'][$lang['id_lang']] = Tools::getValue('image_mobile_'.(int)$lang['id_lang']);
            $fields['title'][$lang['id_lang']] = Tools::getValue('title_'.(int)$lang['id_lang'], $slide->title[$lang['id_lang']] ?? '');
            $fields['url'][$lang['id_lang']] = Tools::getValue('url_'.(int)$lang['id_lang'], $slide->url[$lang['id_lang']] ?? '');
            $fields['legend'][$lang['id_lang']] = Tools::getValue('legend_'.(int)$lang['id_lang'], $slide->legend[$lang['id_lang']] ?? '');
            $fields['cta'][$lang['id_lang']] = Tools::getValue('cta_'.(int)$lang['id_lang'], $slide->cta[$lang['id_lang']] ?? '');
            $fields['description'][$lang['id_lang']] = Tools::getValue('description_'.(int)$lang['id_lang'], $slide->description[$lang['id_lang']] ?? '');
        }

        return $fields;
    }

    protected function getMultiLanguageInfoMsg()
    {
        return '<p class="alert alert-warning">'.
                    $this->getTranslator()->trans('Since multiple languages are activated on your shop, please mind to upload your image for each one of them', array(), 'Modules.Opxslider.Admin').
                '</p>';
    }

    protected function getWarningMultishopHtml()
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            return '<p class="alert alert-warning">' .
            $this->getTranslator()->trans('You cannot manage slides items from a "All Shops" or a "Group Shop" context, select directly the shop you want to edit', array(), 'Modules.Opxslider.Admin') .
            '</p>';
        } else {
            return '';
        }
    }

    protected function getShopContextError($shop_contextualized_name, $mode)
    {
        if (is_array($shop_contextualized_name)) {
            $shop_contextualized_name = implode('<br/>', $shop_contextualized_name);
        }

        if ($mode == 'edit') {
            return '<p class="alert alert-danger">' .
            $this->trans('You can only edit this slide from the shop(s) context: %s', array($shop_contextualized_name), 'Modules.Opxslider.Admin') .
            '</p>';
        } else {
            return '<p class="alert alert-danger">' .
            $this->trans('You cannot add slides from a "All Shops" or a "Group Shop" context', array(), 'Modules.Opxslider.Admin') .
            '</p>';
        }
    }

    protected function getShopAssociationError($id_slide)
    {
        return '<p class="alert alert-danger">'.
                        $this->trans('Unable to get slide shop association information (id_slide: %d)', array((int)$id_slide), 'Modules.Opxslider.Admin') .
                '</p>';
    }


    protected function getCurrentShopInfoMsg()
    {
        $shop_info = null;

        if (Shop::isFeatureActive()) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                $shop_info = $this->trans('The modifications will be applied to shop: %s', array($this->context->shop->name), 'Modules.Opxslider.Admin');
            } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
                $shop_info = $this->trans('The modifications will be applied to this group: %s', array(Shop::getContextShopGroup()->name), 'Modules.Opxslider.Admin');
            } else {
                $shop_info = $this->trans('The modifications will be applied to all shops and shop groups', array(), 'Modules.Opxslider.Admin');
            }

            return '<div class="alert alert-info">'.
                        $shop_info.
                    '</div>';
        } else {
            return '';
        }
    }

    protected function getSharedSlideWarning()
    {
        return '<p class="alert alert-warning">'.
                    $this->trans('This slide is shared with other shops! All shops associated to this slide will apply modifications made here', array(), 'Modules.Opxslider.Admin').
                '</p>';
    }
}
