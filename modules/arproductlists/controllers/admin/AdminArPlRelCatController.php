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

include_once dirname(__FILE__).'/../../classes/ArProductListRelCat.php';
include_once dirname(__FILE__).'/../../classes/ArPLTools.php';

class AdminArPlRelCatController extends ModuleAdminController
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
    
    public function ajaxProcessReload()
    {
        $data = ArProductListRelCat::getMainCategories();
        die(ArPLTools::jsonEncode(array(
            'content' => $this->module->render('_partials/_relcat.tpl', array(
                'data' => $data
            )),
            'data' => $data
        )));
    }
    
    public function ajaxProcessEdit()
    {
        $id = Tools::getValue('id');
        $rels = ArProductListRelCat::getRelatedCategoriesStatic($id, true, false);
        die(ArPLTools::jsonEncode(array(
            'success' => 1,
            'id' => $id,
            'rels' => $rels
        )));
    }
    
    public function ajaxProcessReloadOne()
    {
    }
    
    public function ajaxProcessSave()
    {
        $data = Tools::getValue('data');
        $id_cat = 0;
        $id_rel = array();
        foreach ($data as $item) {
            if (isset($item['name']) && isset($item['value']) && $item['name'] == 'relcat.source') {
                $id_cat = (int)$item['value'];
            } elseif (isset($item['name']) && isset($item['value']) && $item['name'] == 'relcat.rels[]' && (int)$item['value']) {
                if ((int)$item['value'] != $id_cat) {
                    $id_rel[] = (int)$item['value'];
                }
            }
        }
        $errors = array();
        if (empty($id_cat)) {
            $errors['source'] = $this->module->l('Please choose category');
        }
        if (empty($id_rel)) {
            $errors['rels'] = $this->module->l('Please choose related categories');
        }
        if ($errors) {
            die(ArPLTools::jsonEncode(array(
                'success' => 0,
                'errors' => $errors
            )));
        }
        Db::getInstance()->delete(ArProductListRelCat::getTableName(false), 'id_cat = ' . (int)$id_cat);
        foreach ($id_rel as $k => $id) {
            $model = new ArProductListRelCat();
            $model->id_cat = (int)$id_cat;
            $model->id_rel = (int)$id;
            $model->id_shop = Context::getContext()->shop->id;
            $model->status = 1;
            $model->position = $k + 1;
            $model->save();
        }
        die(ArPLTools::jsonEncode(array(
            'success' => 1
        )));
    }
    
    public function ajaxProcessRemoveAll()
    {
        $id = Tools::getValue('id');
        $removed = Db::getInstance()->delete(ArProductListRelCat::getTableName(false), 'id_cat = ' . (int)$id);
        die(ArPLTools::jsonEncode(array(
            'success' => (int)$removed
        )));
    }
    
    public function ajaxProcessRemove()
    {
        $id = Tools::getValue('id');
        $removed = Db::getInstance()->delete(ArProductListRelCat::getTableName(false), 'id_relcat = ' . (int)$id);
        die(ArPLTools::jsonEncode(array(
            'success' => (int)$removed
        )));
    }
    
    public function ajaxProcessReorder()
    {
        $data = Tools::getValue('data');
        if (is_array($data) && !empty($data)) {
            foreach ($data as $item) {
                $k = explode('_', $item);
                Db::getInstance()->update(ArProductListRelCat::getTableName(false), array(
                    'position' => (int)$k[1]
                ), 'id_relcat = ' . (int)$k[0]);
            }
            $this->module->clearCache();
        }
        die(ArPLTools::jsonEncode(array()));
    }
    
    public function ajaxProcessToggle()
    {
        $id = Tools::getValue('id');
        $model = new ArProductListRelCat($id);
        $model->status = $model->status? 0 : 1;
        $model->save();
        die(ArPLTools::jsonEncode(array(
            'status' => $model->status
        )));
    }
}
