<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_'))
    	exit;
/**
 * Class AdminMarketPlaceProductsController
 * @property \Ets_marketplace $module
 */
class AdminMarketPlaceProductsController extends ModuleAdminController
{
    public function __construct()
    {
       parent::__construct();
       $this->context= Context::getContext();
       $this->bootstrap = true;
    }
    public function postProcess()
    {
        parent::postProcess();
        if(Tools::isSubmit('submitDeclineProductSeller'))
            $this->_submitDeclineProductSeller();
        if(Tools::isSubmit('etsmpSubmitApproveChanged') && ($id_product = (int)Tools::getValue('id_product')))
        {
            $this->_submitApproveChangedProduct($id_product);
        }
        if(Tools::isSubmit('btnSubmitDeclineChangeProduct') && ($id_product = (int)Tools::getValue('id_product')))
        {
            $this->_submitDecliceChangedProduct($id_product);
        }
        if(Tools::isSubmit('btnSubmitAssignProduct'))
        {
            $this->_submitAssignProduct();
        }
        if(Tools::isSubmit('del') && ($id_product = Tools::getValue('id_product')) && Validate::isUnsignedId($id_product))
        {
            $product = new Product($id_product);
            if($product->delete())
            {
                $this->context->cookie->success_message = $this->l('Deleted product successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceProducts'));
            }
            else
                $this->module->_errors[] = $this->l('An error occurred while deleting the product');
        }
        if(Tools::isSubmit('editmp_products') && ($id_product = (int)Tools::getValue('id_product')) && Validate::isUnsignedId($id_product))
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$id_product)));
        $bulk_action = Tools::getValue('bulk_action');
        if($bulk_action && ($id_products = Tools::getValue('bulk_action_selected_products')) && Ets_marketplace::validateArray($id_products,'isInt'))
        {
            $id_product = $id_products[0];
            $errors = array();
            switch ($bulk_action) {
              case 'activate_all':
                    $product = new Product($id_product);
                    if(Validate::isLoadedObject($product) && $product->active==0)
                    {
                        if(($id_seller = Ets_mp_product::getSellerByIdProduct($id_product)) && ($seller = new Ets_mp_seller($id_seller)) && Validate::isLoadedObject($seller) && $seller->vacation_mode && Tools::strpos($seller->vacation_type,'disable_product')!==false)
                            $errors[] = sprintf($this->l('You do not have permission to enable product(#%d)'),$id_product);
                        else
                        {
                            $product->active=1;
                            if(!$product->update())
                                $errors[] = sprintf($this->l('An error occurred while saving the product(#%d)'),$id_product);
                        }
                    }elseif(!Validate::isLoadedObject($product))
                        $errors[] = sprintf($this->l('Product(#%d) is not valid'),$id_product);
                    $this->context->cookie->success_message = $this->l('Product(s) successfully activated.');
                break;
              case 'deactivate_all':
                    $product = new Product($id_product);
                    if(Validate::isLoadedObject($product) &&  $product->active)
                    {
                        
                        if(($id_seller = Ets_mp_product::getSellerByIdProduct($id_product)) && ($seller = new Ets_mp_seller($id_seller)) && Validate::isLoadedObject($seller) && $seller->vacation_mode && Tools::strpos($seller->vacation_type,'disable_product')!==false)
                            $errors[] = sprintf($this->l('You do not have permission to enable product(#%d)'),$id_product);
                        else
                        {
                            $product->active=0;
                            if(!$product->update())
                                $errors[] = sprintf($this->l('An error occurred while saving the product(#%d)'),$id_product);
                        }
                        
                    }
                    elseif(!Validate::isLoadedObject($product))
                        $errors[] = sprintf($this->l('Product(#%d) is not valid'),$id_product);
                    $this->context->cookie->success_message = $this->l('Product(s) successfully deactivated.');
              break;
              case 'duplicate_all':
                Ets_mp_defines::getInstance()->processDuplicate($id_product,$errors);
                if($errors)
                {
                    $errors[0] = sprintf($this->l('An error occurred while duplicating the product(#%d) : %s'),$id_product,$errors[0]);
                }
                $this->context->cookie->success_message = $this->l('Product(s) successfully duplicated.');
              break;
              case 'delete_all':
                $product = new Product($id_product);
                if(Validate::isLoadedObject($product))
                {
                    if(!$product->delete())
                        $errors[] = sprintf($this->l('An error occurred while deleting the product(#%d)'),$id_product);
                }
                else
                    $errors[] = sprintf($this->l('Product(#%d) is not valid'),$id_product);
                $this->context->cookie->success_message = $this->l('Product(s) successfully deleted.');
              break;
            } 
            if($errors)
            {
                $this->context->cookie->success_message='';
                die(
                    json_encode(
                        array(
                            'error' => $errors[0],
                        )
                    )
                );
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'result' => 'ok',
                        )
                    )
                );
            }
        }
    }
    public function initContent()
    {
        parent::initContent();
        if($this->ajax)
            $this->renderList();
    }
    public function renderList()
    {
        $this->module->getContent();
        if($id_product = (int)Tools::getValue('id_product'))
        {
            $product_class = new Product($id_product,false,$this->context->language->id);
            $this->context->smarty->assign(
                array(
                    'product_class' => $product_class,
                )
            );
        }
        $this->context->smarty->assign(
            array(
                'ets_mp_body_html'=> $this->_renderProducts(),
                'ets_link_search_seller' => $this->context->link->getAdminLink('AdminMarketPlaceAjax'),
            )
        );
        $html ='';
        if($this->context->cookie->success_message)
        {
            $html .= $this->module->displayConfirmation($this->context->cookie->success_message);
            $this->context->cookie->success_message ='';
        }
        if($this->module->_errors)
            $html .= $this->module->displayError($this->module->_errors);
        return $html.$this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'admin.tpl');
    }
    public function _renderProducts()
    {
        if(Tools::isSubmit('change_enabled') && ($id_product = (int)Tools::getValue('id_product')) && Validate::isUnsignedId($id_product))
        {
            $product = new Product($id_product);
            $active= (int)Tools::getValue('change_enabled');
            $product->active = $active ? 1:0;
            $errors = '';
            if(($id_seller = Ets_mp_product::getSellerByIdProduct($id_product)) && ($seller = new Ets_mp_seller($id_seller)) && Validate::isLoadedObject($seller) && $seller->vacation_mode && Tools::strpos($seller->vacation_type,'disable_product')!==false)
            {
                $errors = $this->l('You do not have permission to enable this product');
            }
            if(!$errors)
            {
                if($product->update())
                {
                    Ets_mp_product::updateStatus($product->id,$product->active,true);
                    if($active)
                    {
                        die(
                            json_encode(
                                array(
                                    'href' => $this->context->link->getAdminLink('AdminMarketPlaceProducts').'&id_product='.$product->id.'&change_enabled=0&field=active',
                                    'title' => $this->l('Click to disable'),
                                    'success' => $this->l('Updated successfully'),
                                    'enabled' => 1,
                                )
                            )  
                        );
                    }
                    else
                    {
                        die(
                            json_encode(
                                array(
                                    'href' => $this->context->link->getAdminLink('AdminMarketPlaceProducts').'&id_product='.$product->id.'&change_enabled=1&field=active',
                                    'title' => $this->l('Click to enable'),
                                    'success' => $this->l('Updated successfully'),
                                    'enabled' => 0,
                                )
                            )  
                        );
                    }
                }
                else
                {
                    $errors = $this->l('An error occurred while saving the product');
                }
            }
            if($errors)
            {
                die(
                    json_encode(
                        array(
                            'errors' => $errors,
                        )
                    )
                );
            }
            
        }
        if(Tools::isSubmit('viewchanged') && ($id_product = (int)Tools::getValue('id_product')) && Validate::isLoadedObject(new Product($id_product)))
        {
            if(Tools::isSubmit('ajax'))
            {
                die(
                    json_encode(
                        array(
                            'form_html' => $this->displayProductChanged($id_product),
                        )
                    )
                );
            }
            return $this->displayProductChanged($id_product);
        }
        $fields_list = array(
            'input_box' => array(
                'title' => '',
                'width' => 40,
                'type' => 'text',
                'strip_tag'=> false,
            ),
            'id_product' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'class'=>'text-center'
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'type'=>'text',
                'sort' => false,
                'filter' => false,
                'strip_tag'=> false,
            ),
            'name' => array(
                'title' => $this->l('Product name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag'=> false,
            ),
            'price' => array(
                'title' => $this->l('Price'),
                'type' => 'int',
                'sort' => true,
                'filter' => true,
                'class'=>'text-center'
            ),
            'quantity' => array(
                'title' => $this->l('Quantity'),
                'type' => 'int',
                'sort' => true,
                'filter' => true,
                'class'=>'text-center'
            ),
            'shop_name' => array(
                'title' => $this->l('Shop name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
            ),
            'total_reported' => array(
                'title' => $this->l('Reported'),
                'type' => 'int',
                'sort' => true,
                'filter' => true,
                'class'=>'text-center'
            ),
            'wait_change' => array(
                'title' => $this->l('Last updated'),
                'type'=> 'select',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'active',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'active' => 1,
                            'title' => $this->l('Yes')
                        ),
                        1 => array(
                            'active' => 0,
                            'title' => $this->l('No')
                        ),
                    )
                ),
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'type' => 'active',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'active',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'active' => -2,
                            'title' => $this->l('Declined')
                        ),
                        1 => array(
                            'active' => -1,
                            'title' => $this->l('Pending')
                        ),
                        2 => array(
                            'active' => 1,
                            'title' => $this->l('Enabled')
                        ),
                        3 => array(
                            'active' => 0,
                            'title' => $this->l('Disabled')
                        )
                    )
                ),
                'class'=>'text-center'
            ),
            'date_add' => array(
                'title' => $this->l('Date added'),
                'type' => 'date',
                'sort' => true,
                'filter' => true,
                'strip_tag'=>false,
            ),
        );
        if(!(Configuration::get('ETS_MP_SELLER_PRODUCT_APPROVE_REQUIRED') && Configuration::get('ETS_MP_EDIT_PRODUCT_APPROVE_REQUIRED')))
            unset($fields_list['wait_change']);
        //Filter
        $show_resset = false;
        $filter = "";
        if(($id_product= Tools::getValue('id_product')) && !Tools::isSubmit('del'))
        {
            if(Validate::isUnsignedId($id_product))
                $filter .= ' AND p.id_product="'.(int)$id_product.'"';
            $show_resset = true;
        }
        if(($name = Tools::getValue('name')) || $name!='')
        {
            if(Validate::isCleanHtml($name))
                $filter .=' AND pl.name LIKE "%'.pSQL($name).'%"';
            $show_resset = true;
        }
        if(($reference = trim(Tools::getValue('reference'))) || $reference!='')
        {
            if(Validate::isCleanHtml($reference))
                $filter .=' AND p.reference LIKE "%'.pSQL($reference).'%"';
            $show_resset = true;
        }
        if(($default_category = trim(Tools::getValue('default_category'))) || $default_category!='')
        {
            if(Validate::isCleanHtml($default_category))
                $filter .=' AND cl.name LIKE "%'.pSQL($default_category).'%"';
            $show_resset = true;
        }
        if(($price_min = trim(Tools::getValue('price_min'))) || $price_min!='')
        {
            if(Validate::isFloat($price_min))
                $filter .= ' AND product_shop.price >= "'.(float)Tools::getValue('price_min').'"';
            $show_resset = true;
        }
        if(($price_max = trim(Tools::getValue('price_max'))) || $price_max!='')
        {
            if(Validate::isFloat($price_max))
                $filter .= ' AND product_shop.price <= "'.(float)$price_max.'"';
            $show_resset = true;
        }
        if(($active= trim(Tools::getValue('active'))) || $active!='')
        {
            if(Validate::isInt($active))
            {
                if($active==1)
                    $filter .= ' AND product_shop.active="1"';
                elseif($active==0)
                    $filter .= ' AND product_shop.active="0" AND sp.approved="1"';
                elseif($active==-1)
                    $filter .= ' AND product_shop.active="0" AND sp.approved="0"';
                elseif($active==-2)
                    $filter .= ' AND sp.approved="-2"';
            }
            $show_resset=true;
        }
        if(($quantity_min = trim(Tools::getValue('quantity_min'))) || $quantity_min!='')
        {
            if(Validate::isInt($quantity_min))
                $filter .=' AND stock.quantity >="'.(int)$quantity_min.'"';
            $show_resset = true;
        }
        if(($quantity_max = trim(Tools::getValue('quantity_max'))) || $quantity_max!='')
        {
            if(Validate::isInt($quantity_max))
                $filter .=' AND stock.quantity <= "'.(int)$quantity_max.'"';
            $show_resset= true;
        }
        if(($shop_name = trim(Tools::getValue('shop_name'))) || $shop_name!='')
        {
            if(Validate::isCleanHtml($shop_name))
                $filter .= ' AND seller_lang.shop_name like "%'.pSQL($shop_name).'%"';
            $show_resset = true;
        }
        if(($date_add_min = trim(Tools::getValue('date_add_min'))) || $date_add_min !='')
        {
            if(Validate::isDate($date_add_min))
                $filter .=' AND p.date_add >="'.pSQL($date_add_min).' 00:00:00"';
            $show_resset =true;
        }
        if(($date_add_max =trim(Tools::getValue('date_add_max'))) || $date_add_max!='')
        {
            if(Validate::isDate($date_add_max))
                $filter .=' AND p.date_add <="'.pSQL($date_add_max).' 23:59:59"';
            $show_resset = true;
        }
        if(($total_reported_min = trim(Tools::getValue('total_reported_min'))) || $total_reported_min!='')
        {
            if(Validate::isInt($total_reported_min))
                $filter .=' AND seller_report.total_reported >= '.(int)$total_reported_min;
            $show_resset=true;
        } 
        if(($total_reported_max = trim(Tools::getValue('total_reported_max'))) || $total_reported_max!='')
        {
            if(Validate::isInt($total_reported_max))
                $filter .=' AND seller_report.total_reported <= '.(int)$total_reported_max;
            $show_resset=true;
        }
        if(($wait_change = trim(Tools::getValue('wait_change'))) || $wait_change!='')
        {
                if(Validate::isInt($wait_change))
                    $filter .= $wait_change ? ' AND mp.id_product is not null': ' AND mp.id_product is null';
                $show_resset = true;
        }
        $sort = "";
        $sort_type=Tools::getValue('sort_type','desc');
        $sort_value = Tools::getValue('sort','id_product');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'id_product':
                    $sort .='p.id_product';
                    break;
                case 'name':
                    $sort .='pl.name';
                    break;
                case 'reference':
                    $sort .= 'p.reference';
                    break;
                case 'default_category':
                    $sort .= 'pl.name';
                    break;
                case 'price':
                    $sort .= 'product_shop.price';
                    break;
                case 'active':
                    $sort .='p.active';
                    break;
                case 'shop_name':
                    $sort .='seller_lang.shop_name';
                    break;
                case 'quantity':
                    $sort .='quantity';
                    break;
                case 'date_add':
                    $sort .='p.date_add';
                    break;
                case 'total_reported':
                    $sort .='seller_report.total_reported';
                    break;
                
            }
            if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                $sort .= ' '.trim($sort_type);  
        }
        //Paggination
        $page = Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $totalRecords = (int)Ets_mp_product::getSellerProducts($filter,0,0,'',true);
        $paggination = new Ets_mp_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = $this->context->link->getAdminLink('AdminMarketPlaceProducts').'&page=_page_'.$this->module->getFilterParams($fields_list,'mp_products') ;
        $paggination->limit =  (int)Tools::getValue('paginator_product_select_limit',20);
        $paggination->name ='product';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $products = Ets_mp_product::getSellerProducts($filter,$page,$paggination->limit,$sort,false);
        if($products)
        {
            if(version_compare(_PS_VERSION_, '1.7', '>='))
                $type_image= ImageType::getFormattedName('home');
            else
                $type_image= ImageType::getFormatedName('home');
            foreach($products as &$product)
            {
                $product['child_view_url'] = $this->context->link->getProductLink($product['id_product']);
                $product['price'] = Tools::displayPrice($product['price']);
                if(!$product['id_image'])
                    $product['id_image'] = Ets_mp_product::getImageByIDProduct($product['id_product']);
                if($product['id_image'])
                {
                    $product['image'] = Module::getInstanceByName('ets_marketplace')->displayText(Module::getInstanceByName('ets_marketplace')->displayText('','img','width_80','','','',$this->context->link->getImageLink($product['link_rewrite'],$product['id_image'],$type_image)),'a','','',$this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$product['id_product'])));
                }
                else
                    $product['image']='';
                $product['name'] = Module::getInstanceByName('ets_marketplace')->displayText($product['name'],'a','','',$this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$product['id_product'])));
                if($product['id_seller_product'])
                {
                    if($product['id_seller'])
                    {
                        $product['shop_name'] = Module::getInstanceByName('ets_marketplace')->displayText($product['shop_name'],'a','','',$this->module->getShopLink(array('id_seller'=>$product['id_seller'])),'_blank');
                        if($product['vacation_mode'])
                            $product['shop_name'] .= Module::getInstanceByName('ets_marketplace')->displayText($this->l('Vacation'),'p','seller_vacation');
                    }
                    else
                    {
                        $product['shop_name']= Module::getInstanceByName('ets_marketplace')->displayText($this->l('Shop deleted'),'span','deleted_shop row_deleted');
                    }
                }
                else
                {
                    $product['shop_name']='--';
                }
                if($product['approved']==-2)
                {
                    $product['active']=-2;
                }
                elseif(!$product['active'] && !$product['approved'] && $product['id_seller_product'])
                    $product['active']=-1;
                $product['input_box'] = Module::getInstanceByName('ets_marketplace')->displayText('','input','','bulk_action_selected_products-'.$product['id_product'],'','','','bulk_action_selected_products[]',$product['id_product'],'checkbox');
                if($product['wait_change'])
                {
                    $product['wait_change'] = Module::getInstanceByName('ets_marketplace')->displayText($this->l('Approve'),'button','btn btn-default btn-approve-change','','','','','','','',(int)$product['id_product']);
                    if($product['decline_change']!=0)
                        $product['wait_change'] .= Module::getInstanceByName('ets_marketplace')->displayText($this->l('Decline'),'button','btn btn-default btn-decline-change','','','','','','','',(int)$product['id_product']);
                    $attr_datas = array(
                        array(
                            'name'=> 'data-href',
                            'value' => $this->context->link->getAdminLink('AdminMarketPlaceProducts').'&viewchanged&id_product='.(int)$product['id_product'],
                        ),
                        array(
                            'name' => 'data-id_product',
                            'value' => $product['id_product'],
                        )
                    );
                    $product['wait_change'] .= Module::getInstanceByName('ets_marketplace')->displayText($this->l('View changes'),'button','btn btn-default btn-view-change-product','',null,false,null,null,null,null,null,null,$attr_datas);
                    if($product['date_submited'])
                    {
                        $time_submit =strtotime(date('Y-m-d H:i:s'))- strtotime($product['date_submited']);
                        if(($month = floor($time_submit/(3600*24*30))))
                            $product['wait_change'] = Module::getInstanceByName('ets_marketplace')->displayText(sprintf($this->l('Submited: %s month(s) ago'),$month),'p','ets_mp_date_submited').$product['wait_change'];
                        elseif(($day = floor($time_submit/(3600*24))))
                            $product['wait_change'] = Module::getInstanceByName('ets_marketplace')->displayText(sprintf($this->l('Submited: %s day(s) ago'),$day),'p','ets_mp_date_submited').$product['wait_change'];
                        elseif(($hour = floor($time_submit/3600)))
                            $product['wait_change'] = Module::getInstanceByName('ets_marketplace')->displayText(sprintf($this->l('Submited: %s hour(s) ago'),$hour),'p','ets_mp_date_submited').$product['wait_change'];
                        elseif(($minute =floor($time_submit/60)))
                            $product['wait_change'] = Module::getInstanceByName('ets_marketplace')->displayText(sprintf($this->l('Submited: %s minute(s) ago'),$minute),'p','ets_mp_date_submited').$product['wait_change'];
                        else
                            $product['wait_change'] = Module::getInstanceByName('ets_marketplace')->displayText(sprintf($this->l('Submited: %s seconds ago'),$time_submit),'p','ets_mp_date_submited').$product['wait_change'];
                    }
                }
                else
                    $product['wait_change']='';
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $listData = array(
            'name' => 'mp_products',
            'actions' => array('view','edit','delete','assign'),
            'icon' => 'icon-products',
            'currentIndex' => $this->context->link->getAdminLink('AdminMarketPlaceProducts').($paggination->limit!=20 ? '&paginator_product_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getAdminLink('AdminMarketPlaceProducts'),
            'identifier' => 'id_product',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->l('Products'),
            'fields_list' => $fields_list,
            'field_values' => $products,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list,'mp_products'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'show_add_new'=> false,
            'view_new_tab' => true,
            'sort_type' => $sort_type,
        );            
        return $this->_renderFormBulkProduct().$this->module->renderList($listData).$this->renderFormPopupAssignProduct();
    }
    public function _renderFormBulkProduct()
    {
        $this->context->smarty->assign(
            array(
                'has_delete_product' => true,
                'is_admin'=> true,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/product/product_bulk.tpl');
    }
    public function displayProductChanged($id_product)
    {
        $mpPrdouct = Ets_mp_product::getMpProductByIdProduct($id_product);
        $items = array();
        $product = new Product($id_product);
        if(Validate::isLoadedObject($mpPrdouct) && $fileds_changed = $mpPrdouct->filed_change)
        {
            $fileds_changed = explode(',',$fileds_changed);
            $product_fields = Ets_mp_defines::getInstance()->getFieldChangeProduct(); 
            $languages = Language::getLanguages(false);
            if($product_fields)
            {
                foreach($product_fields as $field)
                {
                    $fieldID = $field['id']=='wholesale_price' || $field['id']=='unit_price' ? 'price':$field['id'];
                    if($fieldID!='image' && in_array($fieldID,$fileds_changed))
                    {
                        if(isset($field['lang']) && $field['lang'])
                        {
                            $old_values = $product->{$field['id']};
                            $new_values = $mpPrdouct->{$field['id']};
                            $item = array(
                                'id'=>$field['id'],
                                'name' => $field['name'],
                                'old_values' => array(),
                                'new_values' => array(),
                                'languages' => array(),
                            );
                            foreach($languages as $language)
                            {
                                if(isset($old_values[$language['id_lang']]) && isset($new_values[$language['id_lang']],$new_values[$language['id_lang']])!==0)
                                {
                                    $item['old_values'][] = $old_values[$language['id_lang']];
                                    $item['new_values'][] = $new_values[$language['id_lang']];
                                    $item['languages'][] = $language['name'];
                                }
                            }
                            if($item['old_values'] || $item['new_values'])
                                $items[] = $item;
                        }
                        else
                        {
                            $old_value = $product->{$field['id']};
                            $new_value = $mpPrdouct->{$field['id']};
                            if($old_value != $new_value)
                            {
                                $item = array(
                                    'id'=>$field['id'],
                                    'name' => $field['name'],
                                    'old_values' => $field['id']=='price' ? Tools::displayPrice($old_value) : $old_value,
                                    'new_values' => $field['id']=='price' ? Tools::displayPrice($new_value) : $new_value,
                                );
                                $items[] = $item;
                            }
                            if($field['id']=='price' && $product->id_tax_rules_group!= $mpPrdouct->id_tax_rules_group)
                            {
                                if($product->id_tax_rules_group)
                                {
                                    $taxRulesGroup = new TaxRulesGroup($product->id_tax_rules_group,$this->context->language->id);
                                    $old_value = $taxRulesGroup->name;
                                }
                                else
                                    $old_value = $this->l('No tax');
                                if($mpPrdouct->id_tax_rules_group)
                                {
                                    $taxRulesGroup = new TaxRulesGroup($mpPrdouct->id_tax_rules_group,$this->context->language->id);
                                    $new_value = $taxRulesGroup->name;
                                }
                                else
                                    $new_value = $this->l('No tax');
                                $item = array(
                                    'id'=>'id_tax_rules_group',
                                    'name' => $this->l('Tax'),
                                    'old_values' => $old_value,
                                    'new_values' => $new_value,
                                );
                                $items[] = $item;
                            }
                        }
                    }  
                }
            }
        }
        if($mpImages = Ets_mp_product::getAllNewImageProductChange($id_product))
        {
            $images = Ets_mp_product::getAllOldImageProductChange($id_product);
            $mpImages = array_merge($mpImages,$images);
            $this->context->smarty->assign(
                array(
                    'new_images' => $mpImages,
                    'old_images' => $images ,
                    'link_rewrite' => $product->link_rewrite[$this->context->language->id],
                    'link' => $this->context->link,
                )
            );
            $item = array(
                'id'=>'image',
                'name' => $this->l('Image'),
                'old_values' => $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/product/product_old_images.tpl'),
                'new_values' => $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/product/product_new_images.tpl'),
            );
            $items[] = $item;
        }
        $this->context->smarty->assign(
            array(
                'items' => $items,
                'id_product' => $id_product,
                'declined' => $mpPrdouct->status==0 ? true : false,
                'product_name' => isset($product->name[$this->context->language->id]) ? $product->name[$this->context->language->id] : $product->name[Configuration::get('PS_LANG_DEFAULT')],
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/product/product_changed.tpl');
    }
    public function _submitDecliceChangedProduct($id_product)
    {
        $mpProduct = Ets_mp_product::getMpProductByIdProduct($id_product);
        $errors = '';
        if($mpProduct && Validate::isLoadedObject($mpProduct))
        {
            $reason_decline = Tools::getValue('reason_decline');
            if($reason_decline && !Validate::isCleanHtml($reason_decline))
                $errors = $this->l('Reason is not valid');
            else
            {
                $mpProduct->status=0;
                $mpProduct->decline = $reason_decline;
                if($mpProduct->adminUpdateStatus())
                {
                    if(Configuration::get('ETS_MP_EMAIL_SELLER_PRODUCT_UPDATE_APPROVED_OR_DECLINED'))
                    {
                        $id_seller = Ets_mp_product::getSellerByIdProduct($id_product);
                        if($id_seller && Validate::isUnsignedId($id_seller) && ($seller = new Ets_mp_seller($id_seller)) && Validate::isLoadedObject($seller))
                        {
                            $product = new Product($id_product);
                            $data = array(
                                '{seller_name}' => $seller->seller_name,
                                '{product_link}' => $this->context->link->getProductLink($product),
                                '{product_name}' => $product->name[$this->context->language->id],
                                '{reason}' => $reason_decline ? sprintf($this->l('Reason: %s'),nl2br($reason_decline)):'',
                                '{reason_txt}' => $reason_decline ? sprintf($this->l('Reason: %s'),$reason_decline):'',
                            );
                            $subjects = array(
                                'translation' => $this->l('Your product update was declined'),
                                'origin'=> 'Your product update was declined',
                                'specific'=>false
                            );
                            Ets_marketplace::sendMail('to_seller_when_product_update_is_declined',$data,$seller->seller_email,$subjects,$seller->seller_name);
                        }
                    }
                    die(
                        json_encode(
                            array(
                                'success' => $this->l('Declined edit product successfully'),
                                'id_product' => $id_product,
                            )
                        )
                    );
                }
                else
                    $errors = $this->l('An error occurred while saving the status');
            }
        }
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $errors,
                    )
                )
            );
        }
    }
    public function _submitApproveChangedProduct($id_product)
    {
        $mpProduct = Ets_mp_product::getMpProductByIdProduct($id_product);
        $product = new Product($id_product);
        $errors = '';
        if($mpProduct && Validate::isLoadedObject($mpProduct) && $product && Validate::isLoadedObject($product))
        {
            if(($fields_changed = $mpProduct->filed_change))
            {
                $fields_changed = explode(',',$fields_changed);
                foreach($fields_changed as $field)
                {
                    if(isset($product->{$field}) && isset($mpProduct->{$field}))
                        $product->{$field} = $mpProduct->{$field};
                    if($field=='price')
                    {
                        $product->id_tax_rules_group = $mpProduct->id_tax_rules_group;
                        $product->unit_price = $mpProduct->unit_price;
                        $product->wholesale_price = $mpProduct->wholesale_price;
                        $product->unit_price_ratio = $mpProduct->unit_price_ratio;
                    }
                }
                if(!$product->update())
                    $errors = $this->l('An error occurred while saving the product');
                else
                    $mpProduct->delete();
            }
            else
                $mpProduct->delete();
            
        }
        Ets_mp_product::approveChangedImageProduct($id_product,$errors);
        if($errors)
        {
            if(Tools::isSubmit('ajax'))
            {
                die(
                    json_encode(
                        array(
                            'errors' => $errors,
                        )
                    )
                );
            }
            else
                $this->module->_errors[] = $errors;
            
        }
        else
        {

            if(Configuration::get('ETS_MP_EMAIL_SELLER_PRODUCT_UPDATE_APPROVED_OR_DECLINED'))
            {
                $id_seller = Ets_mp_product::getSellerByIdProduct($id_product);
                if($id_seller && Validate::isUnsignedId($id_seller) && ($seller = new Ets_mp_seller($id_seller)) && Validate::isLoadedObject($seller))
                {
                    $data = array(
                        '{seller_name}' => $seller->seller_name,
                        '{product_link}' => $this->context->link->getProductLink($product),
                        '{product_name}' => $product->name[$this->context->language->id],
                    );
                    $subjects = array(
                        'translation' => $this->l('Your product update was approved'),
                        'origin'=> 'Your product update was approved',
                        'specific'=>false
                    );
                    Ets_marketplace::sendMail('to_seller_when_product_update_is_approved',$data,$seller->seller_email,$subjects,$seller->seller_name);
                }
            }
            if(Tools::isSubmit('ajax'))
            {
                die(
                    json_encode(
                        array(
                            'success' => $this->l('Updated successfull'),
                            'product_price' => Tools::displayPrice($product->price),
                            'product_name' => $product->name[$this->context->language->id],
                            'date_add' => Tools::displayDate($product->date_add,null,true),
                        )
                    )
                );
            }
            else
            {
                $this->context->cookie->success_message = $this->l('Updated successfull');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketPlaceProducts'));
            }
            
        }
    }
    public function _submitDeclineProductSeller()
    {
        $errors = array();
        $id_product = (int)Tools::getValue('product_id');
        $reason = Tools::getValue('reason');
        if($reason && !Validate::isCleanHtml($reason))
            $errors = $this->l('Reason is not valid');
        elseif($id_product && Validate::isUnsignedId($id_product) && ($product = new Product($id_product)) && Validate::isLoadedObject($product) && $product->active==0 && ($id_seller = (int)Ets_mp_product::getSellerByIdProduct($id_product)))
        {
            Ets_mp_product::declineProductSeller($id_product,$reason);
            if(Configuration::get('ETS_MP_EMAIL_SELLER_PRODUCT_APPROVED_OR_DECLINED'))
            {
                $seller = new Ets_mp_seller($id_seller);
                $data = array(
                    '{seller_name}' => $seller->seller_name,
                    '{product_link}' => $this->context->link->getProductLink($product),
                    '{product_name}' => $product->name[$this->context->language->id],
                    '{product_ID}' => $product->id,
                    '{reason}' => $this->module->displayText(sprintf($this->l('Reason: %s'),nl2br($reason)),'p'),
                    '{reason_txt}' => $reason,
                );
                $subjects = array(
                    'translation' => $this->l('Your product is declined'),
                    'origin'=> 'Your product is declined',
                    'specific'=>false
                );
                Ets_marketplace::sendMail('to_seller_product_declined',$data,$seller->seller_email,$subjects,$seller->seller_name);
            }
            die(
                json_encode(
                    array(
                        'success' => $this->l('Declined successfully'),
                        'id_product'=>$id_product,
                    )
                )
            );
        }
        else
            $errors = $this->l('Product is not valid');
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $errors,
                    )
                )
            );
        }
    }
    public function renderFormPopupAssignProduct()
    {
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/product/assign_product.tpl');
    }
    public function _submitAssignProduct()
    {
        $errors = '';
        $id_customer_seller = (int)Tools::getValue('id_customer_seller');
        $id_product = Tools::getValue('id_product');
        if($id_product)
            $id_product = array_map('intval',explode(',',$id_product));
        if(!$id_customer_seller)
            $errors = $this->l('Seller is required');
        elseif(!($seller = Ets_mp_seller::_getSellerByIdCustomer($id_customer_seller,$this->context->language->id)))
            $errors = $this->l('Seller is not valid');
        elseif(!$id_product)
            $errors = $this->l('Product is required');
        if($errors)
        {
           die(
                json_encode(
                    array(
                        'errors' => $errors,
                    )
                )
           );
        }
        elseif(isset($seller) && $seller)
        {
            $products = array();
            foreach($id_product as $id)
            {
                if($id && !Ets_mp_product::getProductSellerByIDProduct($id))
                {
                    if(Ets_mp_product::addProductSeller($id,$seller->id_customer,1))
                    {
                        $products[] = array(
                            'id_product' => $id,
                            'shop_name' => $seller->shop_name,
                            'shop_link' => $this->module->getShopLink(array('id_seller'=>$seller->id)),
                        );
                    }
                }
            }
            die(
                json_encode(
                    array(
                        'success' => $this->l('Assign product successfully'),
                        'products' => $products,
                    )
                )
            );
        }

    }
}