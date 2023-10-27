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

/**
 * @property ArProductLists $module
 */
abstract class ArPLRendererAbstract
{
    public $module;
    public $controller = null;
    
    public function __construct($module, $controller = null)
    {
        $this->module = $module;
        $this->controller = $controller;
    }
    
    abstract public function render($view, $group, $model, $product);
    abstract public function renderCategoryList($view, $group, $model, $product);
    abstract public function renderPromotions($view, $groupModel, $model, $product);
}
