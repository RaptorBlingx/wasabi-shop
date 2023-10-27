<?php
/**
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

function upgrade_module_3_2_3($module_obj)
{
    Media::clearCache();
    foreach ($module_obj->all_shop_ids as $id_shop) {
        $general_settings = $module_obj->getSavedSettings($id_shop, 'general');
        $module_obj->saveSettings('general', $general_settings, [$id_shop]); // autofill more_f
    }
    $module_obj->log('add', 'auto-upgrade applied for v3.2.3');
    return true;
}
