<?php
/**
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*  INFO: This override is required for proper auto-indexation on saving products natively
*/

class AdminProductsController extends AdminProductsControllerCore
{
    public function processAdd()
    {
        $ret = parent::processAdd();
        Hook::exec('actionIndexProduct', ['product' => $this->object->id]);
        return $ret;
    }

    public function processUpdate()
    {
        $ret = parent::processUpdate();
        Hook::exec('actionIndexProduct', ['product' => $this->object->id]);
        return $ret;
    }
}
