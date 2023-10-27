<?php
/**
 * Copyright (c) 2021. OrangePix  All rights reserved.
 * DISCLAIMER
 * Do not edit, modify or copy this file.
 * If you wish to customize it, contact us at info@orangepix.it
 *
 * @author    Carlos Batista <carlos.batista@orangepix.it> , Samuele Cisaro <samuele.cisaro@orangepix.it>
 * @copyright OrangePix Srl
 * @license   Do not edit, modify or copy this file
 *
 */

use OrangePix\Classes\HTMLBlock;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

class Opxblocks extends Module implements WidgetInterface
{
    protected $html;
    private $fields_list;
    private $fields_form;
    public $installed = 0;
    public $template_path = 'module:opxblocks/views/templates/hook/';

    public function __construct()
    {
        $this->name = 'opxblocks';
        $this->tab = 'AdminParentThemes';
        $this->version = '1.3.1';
        $this->author = 'OrangePix Srl';
        $this->need_instance = 0;
        $this->ps_versions_compliancy['min'] = '1.7.0';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('HTML Blocks');
        $this->description = $this->l('Custom HTML Blocks for Frontend');

        $this->updatePositions();
    }

    public function install()
    {
        parent::install();
        /* Run querys */
        include $this->local_path.'sql/install.php';
        $this->upgrade();

        return $this->registerHook('actionFrontControllerSetMedia') &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            $this->registerHook('displayHome');
    }

    /** set $this->installed = 0 to force upgrade
     * @return bool
     */
    public function upgrade()
    {
        Module::initUpgradeModule($this);
        Module::needUpgrade($this);
        $upgraded = $this->runUpgradeModule();
        return !empty($upgraded['success']);
    }

    public function uninstall()
    {
        /* Run querys */
        include $this->local_path.'sql/uninstall.php';
        return parent::uninstall();
    }

    public function getAllDisplayHooks()
    {
        return Hook::getHooks(false, true);
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet($this->name.'_front.css', $this->_path.'views/css/front.css');
    }

    public function hookActionAdminControllerSetMedia($params)
    {
    }

    public function validateFields()
    {
        $fields = ['name','hook'];
        foreach (Language::getIDs() as $id_lang) {
            $fields[] = 'content_'.$id_lang;
        }
        $inputs = Tools::getAllValues();
        $valid = true;
        foreach ($fields as $field) {
            $valid &= !empty($inputs[$field]);
        }
        return $valid;
    }


    /**
     * @return array|\HTMLBlock
     */
    protected function postValidation()
    {
        $this->_errors = [];
        $block = [];
        if (!$this->validateFields()) {
            return $block;
        }
        $block = new HTMLBlock(Tools::getValue('id_htmlblock', null));

        if (!$block->id) {
            $block->position = ($this->getLastPosition() + 1);
        }

        $block->active = (int) Tools::getValue('active');
        $block->name = Tools::getValue('name');
        $block->hook = Tools::getValue('hook');

        foreach (Language::getIDs(false) as $id_lang) {
            $block->content[$id_lang] = Tools::getValue('content_' . $id_lang);
        }

        $block->id_shop = Shop::getContextShopID();
        try {
            $block->save();
        } catch (PrestaShopException $e) {
            $this->_errors[] = $this->l('Error on Save. clean your HTML');
        }

        if (count($this->_errors)) {
            $this->html .= $this->displayError($this->_errors);
        } else {
            $this->html .= $this->displayConfirmation($this->l('Configuration successfully saved'));
        }

        return $block;
    }

    public function getContent()
    {
        if (version_compare($this->version, $this->getRegisterModuleVersion(), '>')) {
            return $this->displayWarning('This Module need to be Updated');
        }

        if (Tools::isSubmit('savehtmlblock') || Tools::isSubmit('savemessage')) {
            $this->postValidation();
        }elseif (Tools::isSubmit('updatehtmlblock')) {
            $block = new HTMLBlock((int) Tools::getValue('id_htmlblock', null));
            $helper = $this->initBlockForm($block);
            return $this->html . $helper->generateForm($this->fields_form);
        }

        if ((Tools::isSubmit('statushtmlblock') || Tools::isSubmit('statusmessage'))
            && Tools::isSubmit('id_htmlblock')
        ) {
            $block = new HTMLBlock((int) Tools::getValue('id_htmlblock'));
            $block->active = !$block->active;
            $block->update();
        }

        if (Tools::isSubmit('deletehtmlblock') || Tools::isSubmit('deletemessage')) {
            $block = new HTMLBlock((int) Tools::getValue('id_htmlblock'));

            if (Validate::isLoadedObject($block)) {
                $block->delete();
                $this->html .= $this->displayConfirmation($this->l('Successful deletion'));
            }
        }

        if (Tools::isSubmit('submitBulkenablehtmlblock') && !empty(Tools::getValue('htmlblockBox'))) {
            $this->submitBulkEnableHtmlBlock();
        }
        if (Tools::isSubmit('submitBulkdisablehtmlblock') && !empty(Tools::getValue('htmlblockBox'))) {
            $this->submitBulkDisableHtmlBlock();
        }
        if (Tools::isSubmit('submitBulkdeletehtmlblock') && !empty(Tools::getValue('htmlblockBox'))) {
            $this->submitBulkDeleteHtmlBlock();
        }

        if (Tools::isSubmit('saveandstay')){
            Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name
                                 . '&updatehtmlblock=&id_htmlblock='.Tools::getValue('id_htmlblock', null).'&token=' . Tools::getAdminTokenLite('AdminModules'));
        }

        // Block list
        $blocks = $this->getBlockList();
        $helpers = $this->initBlockList($blocks);
        $buttons = $this->getButtons();
        $this->html .= $buttons;
        $this->html .= $helpers->generateList($blocks, $this->fields_list);

        return $this->html;
    }

    protected function getButton($text, $href = null, $js = null,  $icon = null, $color = null)
    {
        if (empty($color)) {
            $color = 'primary';
        }
        $this->context->smarty->assign(compact('text', 'icon', 'color', 'href', 'js'));
        return $this->context->smarty->fetch('module:opxblocks/views/templates/admin/button.tpl');
    }

    protected function getButtons()
    {
        $link = AdminController::$currentIndex . '&configure=' . $this->name
        . '&updatehtmlblock&token=' . Tools::getAdminTokenLite('AdminModules');
        $addBtn = $this->getButton('Aggiungi blocco', $link, null, 'add_circle_outline');
        $btns = [$addBtn];
        $this->context->smarty->assign(compact('btns'));
        return $this->context->smarty->fetch('module:opxblocks/views/templates/admin/buttons.tpl');
    }

    /**
     * @param \HTMLBlock $block
     *
     * @return \HelperForm
     */
    protected function initBlockForm($block)
    {
        $hooks = $this->getAllDisplayHooks();

        $this->fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('HTML Block'),
                'icon' => 'icon-text'
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'id_htmlblock',
                    'default_value' => 0,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Enabled'),
                    'name' => 'active',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'enable_on',
                            'value' => 1,
                            'label' => $this->l('Yes')],
                        [
                            'id' => 'enable_off',
                            'value' => 0,
                            'label' => $this->l('No')],
                    ]
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Block Name'),
                    'name' => 'name',
                    'lang' => false,
                    'cols' => 6,
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Hook'),
                    'name' => 'hook',
                    'col' => 6,
                    'desc' => $this->l('Insert on this hook'),
                    'default_value' => 'displayHome',
                    'options' => [
                        'id' => 'name',
                        'name' => 'name',
                        'query' => $hooks,
                    ]
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('HTML'),
                    'name' => 'content',
                    'lang' => true,
                    'class' => 'rte',
                    'autoload_rte' => true,
                    'desc' => $this->l('Inserted iframe or javascript code will be removed'),
                ],
            ],
            'submit' => [
                'name' => 'submitHTMLBlock',
                'title' => $this->l('Save')
            ],
        ];

        if ((int)Tools::getValue('id_htmlblock', 0)){
            $this->fields_form[0]['form']['buttons'] = [
                'saveandstay' => [
                    'name' => 'saveandstay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-preview',
                    'title' => $this->l('Save and Stay')
                ]
            ];
        }

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = 'op_htmlblocks';
        $helper->table = 'htmlblock';
        $helper->identifier = 'id_htmlblock';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->toolbar_scroll = true;
        $helper->title = $this->displayName;
        $helper->submit_action = 'savehtmlblock';
        $helper->show_cancel_button = true;
        $helper->default_form_language = $this->context->language->id;
        $helper->back_url = AdminController::$currentIndex . '&configure=' . $this->name
            . '&token=' . Tools::getAdminTokenLite('AdminModules');
        $helper->toolbar_btn = [
            'cancel' =>
            [
                'href' => AdminController::$currentIndex . '&configure=' . $this->name
                . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Cancel')
            ]
        ];

        $helper->fields_value = [
            'active' => (is_object($block) ? $block->active : $block['active']),
            'id_htmlblock' => (is_object($block) ? $block->id : $block['id_htmlblock']),
            'name' => (is_object($block) ? $block->name : $block['name']),
            'hook' => (is_object($block) ? $block->hook : $block['hook']),
            'content' => (is_object($block) ? $block->content : $block['content']),
        ];

        foreach (Language::getLanguages(false) as $language) {
            $helper->languages[] = [
                'id_lang' => $language['id_lang'],
                'iso_code' => $language['iso_code'],
                'name' => $language['name'],
                'is_default' => ($this->context->language->id == $language['id_lang'] ? 1 : 0)
            ];
        }

        return $helper;
    }

    protected function initBlockList($blocks)
    {
        $this->fields_list = [
            'position' => [
                'title' => $this->l('Position'),
                'filter_key' => 'position',
                'align' => 'left',
                'position' => 'position',
                'search' => false
            ],
            'name' => [
                'title' => $this->l('Block Name'),
                'width' => 140,
                'type' => 'text',
                'search' => true
            ],
            'hook' => [
                'title' => $this->l('Hook'),
                'width' => 140,
                'type' => 'text',
                'search' => true
            ],
            'active' => [
                'title' => $this->l('Enabled'),
                'width' => 140,
                'type' => 'bool',
                'align' => 'center',
                'active' => 'status',
                'filter_key' => 'active',
            ]
        ];

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->name_controller = 'op_htmlblocks';
        $helper->simple_header = false;
        $helper->identifier = 'id_htmlblock';
        $helper->position_identifier = 'id_htmlblock';
        $helper->orderBy = 'position';
        $helper->orderWay = 'ASC';
        $helper->actions = ['edit', 'delete'];
        $helper->show_toolbar = true;
        $helper->listTotal = count($blocks);
        $helper->module = $this;
        $helper->bulk_actions = ['delete' => ['text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')],
                                      'enable' => ['text' => $this->l('Enable selected')],
                                      'disable' => ['text' => $this->l('Disable selected')]
                                ];

        $helper->toolbar_btn['new'] = [
            'href' => AdminController::$currentIndex . '&configure=' . $this->name
            . '&updatehtmlblock&token=' . Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add Block')
        ];

        $helper->title = $this->l('HTML Blocks');
        $helper->table = 'htmlblock';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        return $helper;
    }

    protected function getBlockList()
    {
        if (Tools::isSubmit('submitResethtmlblock') || Tools::isSubmit('submitReset')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name]));
        }
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        $has_filters = Tools::isSubmit('submitFilter');
        $filter_name = Tools::getValue('htmlblockFilter_name', null);
        $filter_hook = Tools::getValue('htmlblockFilter_hook', null);
        $filter_active = Tools::getValue('htmlblockFilter_active', null);
        $where = [];

        if ($has_filters && $filter_name) {
            $where[] = 'hb.name LIKE "%' .pSQL($filter_name) . '%"';
        }
        if ($has_filters && $filter_hook) {
            $where[] = 'hb.hook LIKE "%' . pSQL($filter_hook) . '%"';
        }
        if ($has_filters && $filter_active != null) {
            $where[] = 'hb.active = ' . (int)$filter_active;
        }

        $sql = 'SELECT *
			FROM ' . _DB_PREFIX_ . 'htmlblock hb
			WHERE 1 AND hb.id_shop = ' . (int) $id_shop .
            ($has_filters ? ' AND ('.implode(' AND ', $where).')' : '').
            ' ORDER BY hb.position;';

        $blocks = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);
        if (empty($blocks)) {
            return [];
        }

        return $blocks;
    }

    protected function clearCache()
    {
        $this->_clearCache('block.tpl');
    }


    /**
     * @param       $hookName
     * @param array $configuration
     *
     * @return mixed
     */
    public function renderWidget($hookName, array $configuration)
    {
        $blocks = $this->getWidgetVariables($hookName, $configuration);
        $template = 'block.tpl';
        $html = '';
        if (empty($blocks)) {
            return '';
        } else {
            $this->clearCache();
            foreach ($blocks as $block) {
                $this->context->smarty->assign('content', $block['content'], true);
                $html .= $this->context->smarty->fetch($this->template_path.$template);
            }
        }
        return $html;
    }

    /**
     * @param       $hookName
     * @param array $configuration
     *
     * @return mixed
     */
    public function getWidgetVariables($hookName, array $configuration)
    {
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;
        $sql = 'SELECT  hb.id_htmlblock, hb.hook, hbl.content  
                FROM ' . _DB_PREFIX_ . 'htmlblock hb
                INNER JOIN ' . _DB_PREFIX_ . 'htmlblock_lang hbl
                    ON hb.id_htmlblock = hbl.id_htmlblock AND hbl.id_lang = ' . (int) $id_lang . '
                WHERE hb.id_shop = ' . (int) $id_shop . ' AND hb.active = 1
                AND hb.hook = "' . $hookName.'"
                ORDER BY hb.position ASC';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Update the block positions in database (order of visualization)
     *
     * @return string
     */
    public function updatePositions()
    {
        if (!Tools::isSubmit('htmlblock') || !Tools::getIsset('ajax') || !(Tools::isSubmit('action') && Tools::getValue('action') == 'updatePositions')) {
            return;
        }
        $positions = Tools::getValue('htmlblock');
        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);
            if (count($pos) > 3) {
                $resp = Db::getInstance()->update('htmlblock', ['position' => $position], 'id_htmlblock ='.(int)$pos[2], 0, false, false, true);
            }
            $resp +=0;
        }
    }

    public function getLastPosition()
    {
        return (int)Db::getInstance()->getValue('SELECT MAX(position) FROM `'._DB_PREFIX_.'htmlblock`;');
    }

    public function getRegisterModuleVersion()
    {
        return Db::getInstance()->getValue('SELECT `version` FROM `'._DB_PREFIX_.'module` WHERE `name` = "'.$this->name.'";');
    }

    public function submitBulkEnableHtmlBlock()
    {
        $id_blocks = Tools::getValue('htmlblockBox');
        Db::getInstance()->update('htmlblock', ['active' => 1], 'id_htmlblock in('.implode(',', $id_blocks).')', 0, false, false, true);
    }

    public function submitBulkDisableHtmlBlock()
    {
        $id_blocks = Tools::getValue('htmlblockBox');
        Db::getInstance()->update('htmlblock', ['active' => 0], 'id_htmlblock in('.implode(',', $id_blocks).')', 0, false, false, true);
    }

    public function submitBulkDeleteHtmlBlock()
    {
        $id_blocks = Tools::getValue('htmlblockBox');
        Db::getInstance()->delete('htmlblock', 'id_htmlblock in('.implode(',', $id_blocks).')', 0, false, true);
    }
}
