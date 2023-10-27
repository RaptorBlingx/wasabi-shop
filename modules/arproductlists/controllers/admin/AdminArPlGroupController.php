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

class AdminArPlGroupController extends ModuleAdminController
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
    
    public function ajaxProcessReorderList()
    {
        $data = Tools::getValue('data');
        if (is_array($data) && !empty($data)) {
            foreach ($data as $item) {
                $k = explode('_', $item);
                Db::getInstance()->update(ArProductListRel::getTableName(false), array(
                    'position' => (int)$k[1]
                ), 'id_rel = ' . (int)$k[0]);
            }
            $this->module->clearCache();
        }
        die(ArPLTools::jsonEncode(array()));
    }
    
    public function ajaxProcessRemoveList()
    {
        $id = Tools::getValue('id');
        if ($removed = Db::getInstance()->delete(ArProductListRel::getTableName(false), 'id_rel = ' . (int)$id)) {
            Db::getInstance()->delete(ArProductListRel::getTableName(false) . '_lang', 'id_rel = ' . (int)$id);
        }
        die(ArPLTools::jsonEncode(array(
            'success' => (int)$removed
        )));
    }
    
    public function ajaxProcessAddToGroup()
    {
        $listId = Tools::getValue('listId');
        $groupId = Tools::getValue('groupId');
        $id_lang = Context::getContext()->language->id;
        $list = new ArProductList($listId, $id_lang);
        $model = new ArProductListRel();
        $model->id_list = (int)$listId;
        $model->id_group = (int)$groupId;
        $model->position = 0;
        $model->class = pSQL($list->class);
        $model->data = $list->data; /* Escape will break functionality */
        $model->title = pSQL($list->title);
        $model->status = 1;
        $model->save();
        die(ArPLTools::jsonEncode(array(
            'model' => $model
        )));
    }
    
    public function ajaxProcessReorder()
    {
        $data = Tools::getValue('data');
        if (is_array($data) && !empty($data)) {
            foreach ($data as $item) {
                $k = explode('_', $item);
                Db::getInstance()->update(ArProductListGroup::getTableName(false), array(
                    'position' => (int)$k[1]
                ), 'id_group = ' . (int)$k[0]);
            }
            $this->module->clearCache();
        }
        die(ArPLTools::jsonEncode(array()));
    }
    
    public function ajaxProcessSaveGroup()
    {
        $id = Tools::getValue('id');
        $title = Tools::getValue('title');
        $type = Tools::getValue('type');
        $hook = Tools::getValue('hook');
        $id_shop = Tools::getValue('id_shop');
        $created = 0;
        if ($id) {
            $model = new ArProductListGroup($id);
        } else {
            $model = new ArProductListGroup();
            $created = 1;
        }
        
        $model->type = pSQL($type);
        $model->hook = pSQL($hook);
        $model->status = 1;
        $model->position = (int)(ArProductListGroup::getHookPosition($hook) + 1);
        $model->id_shop = (int)$id_shop;
        if (empty($title)) {
            $model->title = 'Group ' . ((int)$model->position);
        } else {
            $model->title = pSQL($title);
        }
        $model->save();
        die(ArPLTools::jsonEncode(array(
            'model' => $model,
            'shop_name' => $model->getShopName(),
            'created' => $created,
            'content' => $this->module->render('_partials/_group_item.tpl', array(
                'group' => $model,
                'id_lang' => Context::getContext()->language->id
            ))
        )));
    }
    
    public function ajaxProcessReload()
    {
        $hook = Tools::getValue('hook');
        die(ArPLTools::jsonEncode(array(
            'content' => $this->module->render('_partials/_group_items.tpl', array(
                'groups' => ArProductListGroup::getByHook($hook, Context::getContext()->shop->id),
                'id_lang' => Context::getContext()->language->id
            ))
        )));
    }
    
    public function ajaxProcessRemove()
    {
        $id = Tools::getValue('id');
        $model = new ArProductListGroup($id);
        
        $sql = 'DELETE FROM `' . ArProductListRel::getTableName() . '` WHERE `id_group` = ' . (int)$id;
        Db::getInstance()->execute($sql);
        
        $sql = 'DELETE FROM `' . ArProductListGroup::getTableName() . '` WHERE `id_group` = ' . (int)$id;
        Db::getInstance()->execute($sql);
        
        die(ArPLTools::jsonEncode(array(
            'success' => 1,
            'model' => $model
        )));
    }
    
    public function ajaxProcessEdit()
    {
        $id = Tools::getValue('id');
        $model = new ArProductListGroup($id);
        die(ArPLTools::jsonEncode(array(
            'success' => 1,
            'model' => $model
        )));
    }
    
    public function ajaxProcessToggle()
    {
        $id = Tools::getValue('id');
        $model = new ArProductListGroup($id);
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
        $model = new ArProductListGroup($id);
        $model->device = (int)$value;
        $model->save();
        die(ArPLTools::jsonEncode(array(
            'device' => $model->device
        )));
    }
}
