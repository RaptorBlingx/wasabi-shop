<?php
/**
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class AmazzingFilterAjaxModuleFrontController extends ModuleFrontControllerCore
{
    public function initContent()
    {
        $this->module->defineSettings();
        switch (Tools::getValue('action')) {
            case 'getFilteredProducts':
                if ($params = $this->module->parseStr(Tools::getValue('params'))) {
                    $params['ajax'] = 1;
                    exit(json_encode($this->module->prepareAjaxResponse($params)));
                }
                break;
            case 'SaveMyFilters':
                $this->module->ajaxSaveCustomerFilters();
                break;
        }
        /*
        } else if (Tools::getValue('action') == 'getRedirectLink') {
            $link_params = Tools::getValue('link_params');
            $id_category = $link_params['id_category'];
            unset($link_params['id_category']);
            $link = new Link();
            $url = $link->getCategoryLink($id_category);
            $params = strpos($url, '?') ? '&' : '?';
            $params .= http_build_query($link_params);
            $ret = ['url' => $url.$params];
            exit(json_encode($ret));
        }
        */
    }
}
