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
 * Class Ets_MarketPlaceMessagesModuleFrontController
 * @property \Ets_mp_seller $seller
 * @property \Ets_marketplace $module
 */
class Ets_MarketPlaceMessagesModuleFrontController extends ModuleFrontController
{
    public $seller;
    public $_errors= array();
    public $_success ='';
    public function __construct()
	{
		parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
        if($this->context->cookie->_success)
        {
            $this->_success = $this->context->cookie->_success;
            $this->context->cookie->_success='';
        }
	}
    public function postProcess()
    {
        parent::postProcess();
        if(!$this->context->customer->isLogged() || !($this->seller = $this->module->_getSeller(true)) )
            Tools::redirect($this->context->link->getModuleLink($this->module->name,'myseller'));
        if(!$this->module->_checkPermissionPage($this->seller))
            die($this->module->l('You do not have permission','messages'));
        if(Tools::isSubmit('downloadfile') && ($id_contact = (int)Tools::getValue('id_contact')) && Validate::isUnsignedId($id_contact))
        {
            $this->seller->downloadFileContact($id_contact);
        }
        if(Tools::isSubmit('del') && ($id_contact = (int)Tools::getValue('id_contact')) && Validate::isUnsignedId($id_contact))
        {
            if(Ets_mp_seller::checkSellerByIDContact($id_contact,$this->seller->id))
            {
                $contact = new Ets_mp_contact($id_contact);
                if($contact->delete())
                {
                    $this->context->cookie->_success = $this->module->l('Deleted successfully','messages');
                    Tools::redirect($this->context->link->getModuleLink($this->module->name,'messages',array('list'=>1)));
                }
                else
                    $this->_errors[] = $this->module->l('Delete message failed','messages');
                
            }
            else
                $this->_errors[] = $this->module->l('You do not have permission to delete this message','messages');
        }
        if(Tools::isSubmit('submitMessage') && ($id_order = (int)Tools::getValue('id_order')) && Validate::isUnsignedId($id_order) && ($order = new Order($id_order)) && Validate::isLoadedObject($order) )
        {
            if(!$this->seller->checkHasOrder($id_order) || !Validate::isLoadedObject($order))
                die($this->module->l('You do not have permission','messages'));
            $id_customer = (int)Tools::getValue('id_customer');
            $customer = new Customer($id_customer);
            if (!Validate::isLoadedObject($customer)) {
                $this->_errors[] = $this->module->l('The customer is invalid.','messages');
            } elseif (!$message = Tools::getValue('message'))
                $this->_errors[] = $this->module->l('The message cannot be blank.','messages');
            elseif(!Validate::isCleanHtml($message))
                $this->_errors[] = $this->module->l('The message is not valid','messages');
            elseif(Tools::strlen($message) > 1600)
                $this->_errors[] = $this->module->l('Message is too large. It must be between 0 and 1600 chars.','messages');
            else {
                /* Get message rules and and check fields validity */
                $rules = call_user_func(array('Message', 'getValidationRules'), 'Message');
                foreach ($rules['required'] as $field) {
                    if (($value = Tools::getValue($field)) == false && (string) $value != '0') {
                        $val = Tools::getValue('id_message');
                        if (!$val || $field != 'passwd') {
                            $this->_errors[] = $field.' '.$this->module->l('is required','messages');
                        }
                    }
                }
                foreach ($rules['size'] as $field => $maxLength) {
                    $field_value = Tools::getValue($field);
                    if ($field_value && Tools::strlen($field_value) > $maxLength) {
                        $this->_errors[] = $field.' '.$this->module->l('is too long','messages').' '. $maxLength;
                    }
                }
                foreach ($rules['validate'] as $field => $function) {
                    $field_value = Tools::getValue($field);
                    if ($field_value) {
                        if (!Validate::$function(htmlentities($field_value, ENT_COMPAT, 'UTF-8'))) {
                            $this->_errors[] = $field. ' '.$this->module->l(' is not valid','messages');
                        }
                    }
                    unset($function);
                }
                if (!count($this->_errors)) {
                    //check if a thread already exist
                    $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($customer->email, $order->id);
                    if (!$id_customer_thread) {
                        $customer_thread = new CustomerThread();
                        $customer_thread->id_contact = 0;
                        $customer_thread->id_customer = (int) $order->id_customer;
                        $customer_thread->id_shop = (int) $this->context->shop->id;
                        $customer_thread->id_order = (int) $order->id;
                        $customer_thread->id_lang = (int) $this->context->language->id;
                        $customer_thread->email = $customer->email;
                        $customer_thread->status = 'open';
                        $customer_thread->token = Tools::passwdGen(12);
                        $customer_thread->add();
                    } else {
                        $customer_thread = new CustomerThread((int) $id_customer_thread);
                    }
                    $customer_message = new CustomerMessage();
                    $customer_message->id_customer_thread = $customer_thread->id;
                    $customer_message->id_employee = 1;
                    $customer_message->message = $message;
                    $visibility = (int)Tools::getValue('visibility');
                    $customer_message->private = $visibility;
                    $add = true;
                    if (!$customer_message->add()) {
                    {
                        $add = false;
                        $this->_errors[] = $this->module->l('An error occurred while saving the message.','messages');
                    }
                    } elseif ($customer_message->private) {
                        $this->_success = $this->module->l('Message sent successfully.','messages');
                    } else {
                        $message = $customer_message->message;
                        if (Configuration::get('PS_MAIL_TYPE', null, null, $order->id_shop) != Mail::TYPE_TEXT) {
                            $message = Tools::nl2br($customer_message->message);
                        }
                        $varsTpl = array(
                            '{lastname}' => $customer->lastname,
                            '{firstname}' => $customer->firstname,
                            '{id_order}' => $order->id,
                            '{order_name}' => $order->getUniqReference(),
                            '{message}' => $message,
                        );
                        $subjects = array(
                            'translation' => $this->module->l('New message regarding your order','messages'),
                            'origin'=> 'New message regarding your order',
                            'specific' => 'messages'
                        );
                        if (
                            !Ets_marketplace::sendMail('order_merchant_comment',$varsTpl,$customer->email,$subjects,$customer->firstname.' '.$customer->lastname)
                        ) {
                            $this->_errors[] = $this->module->l('An error occurred while sending an email to the customer.','messages');
                        }
                    }
                    if($add)
                    {
                        Ets_mp_contact_message::addMessageToseller($this->seller->id_customer,$customer_message->id);
                    }
                    
                }
            }
        }
        if(Tools::isSubmit('submitMessage') && ($id_contact = (int)Tools::getValue('id_contact')) && Validate::isUnsignedId($id_contact))
        {
            $contact = new Ets_mp_contact($id_contact);
            if(!Validate::isLoadedObject($contact) || $contact->id_seller!= $this->seller->id)
                die($this->module->l('You do not have permission','messages'));
            if (!$message = Tools::getValue('message'))
                $this->_errors[] = $this->module->l('The message cannot be blank.','messages');
            if($message && !Validate::isCleanHtml($message))
                $this->_errors[] = $this->module->l('Message is not valid','messages');
            if(!$this->_errors)
            {
                $contact_message = new Ets_mp_contact_message();
                $contact_message->id_seller = (int)$this->seller->id;
                $contact_message->id_manager = $this->seller->id_customer!= $this->context->customer->id ? (int)$this->context->customer->id:0;
                $contact_message->message = $message;
                $contact_message->id_contact = $id_contact;
                $contact_message->read= 1;
                if($contact_message->add())
                {
                    $this->_success = $this->module->l('Message was sent successfully','messages');
                    if(Configuration::get('ETS_MP_EMAIL_NEW_CONTACT'))
                    {
                        if($contact->id_customer)
                        {
                            $customer = new Customer($contact->id_customer);
                            $customer_email = $customer->email;
                            $customer_name = $customer->firstname.' '.$customer->lastname;
                        }
                        else
                        {
                            $customer_email =$contact->email;
                            $customer_name = $contact->name;
                        }
                        if($customer_email)
                        {
                            $this->context->smarty->assign(
                                array(
                                    'message' => $message,  
                                )
                            );
                            $template_vars = array(
                                '{content_message}' => $this->module->displayTpl('content_message.tpl'),
                                '{customer_name}' => $customer_name,
                                '{seller_name}' => $this->seller->id_customer== $this->context->customer->id ? $this->seller->seller_name: $this->context->customer->firstname.' '.$this->context->customer->lastname,
                                '{message_title}' => $contact->getTitle(),
                                '{link_reply}' => $this->context->link->getModuleLink($this->module->name,'contactseller',array('viewmessage'=>1,'id_contact'=> $id_contact)),
                            );
                            $subjects = array(
                                'translation' => $this->module->l('A new contact message from','messages'),
                                'origin'=> 'A new contact message from',
                                'specific' => 'messages'
                            );
                            Ets_marketplace::sendMail('seller_reply',$template_vars,$customer_email,$customer_name);
                        }
                    }
                }    
                else
                    $this->_errors[] = $this->module->l('An error occurred while saving the message.','messages');
            }    
        }
    }
    public function initContent()
	{
		parent::initContent();
        if(!Configuration::get('ETS_MP_ENABLE_CONTACT_SHOP'))
            Tools::redirectLink($this->context->link->getPageLink('PageNotFound'));
        $this->context->smarty->assign(
            array(
                '_errors' =>$this->_errors ? $this->module->displayError($this->_errors):'',
                '_success' => $this->_success ? $this->module->displayConfirmation($this->_success):'',
            )
        );
        $this->context->smarty->assign(
            array(
                'path' => $this->module->getBreadCrumb(),
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false,
                'html_content' => $this->_initContent(),
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/messages.tpl');      
        else        
            $this->setTemplate('messages_16.tpl'); 
    }
    public function _initContent()
    {
        if(Tools::isSubmit('viewmessage') && ($id_order=Tools::getValue('id_order')) && Validate::isUnsignedId($id_order))
        {
            $order = new Order($id_order);
            if(!$this->seller->checkHasOrder($id_order) || !Validate::isLoadedObject($order))
                die($this->module->l('You do not have permission','messages'));
            $id_customer_thread = (int)Ets_mp_contact::getIDCustomerThreadByIDOrder($order->id);
            Ets_mp_contact_message::updateReadByIDCustomerThread($id_customer_thread);
            $messages = Ets_mp_contact_message::getCustomerMessagesOrder($order->id_customer,$order->id,10);
            $this->context->smarty->assign(
                array(
                    'order'=>$order,
                    'messages'=> $messages,
                    'ets_mp_customer' => new Customer($order->id_customer),
                    'customer_thread_message' => CustomerThread::getCustomerMessages($order->id_customer, null, $order->id),
                    'orderMessages' => OrderMessage::getOrderMessages($order->id_lang),
                )            
            );
            
            return $this->module->displayTpl('order_messages.tpl');
        }
        if(Tools::isSubmit('viewmessage') && ($id_contact= (int)Tools::getValue('id_contact')) && Validate::isUnsignedId($id_contact))
        {
            $contact = new Ets_mp_contact($id_contact);
            if(!Validate::isLoadedObject($contact) || $contact->id_seller!=$this->seller->id)
                die($this->module->l('You do not have permission','messages'));                      
            if($contact->id_customer)
            {
                $customer = new Customer($contact->id_customer);
                $contact->name = $customer->firstname.' '.$customer->lastname;
                $contact->email = $customer->email;
            }
            if($contact)
            {
                Ets_mp_contact_message::updateReadByIDContact($id_contact);
                $messages = $contact->getMessages();
                if($contact->id_product)
                {
                    $id_image = Ets_mp_product::getImageByIDProduct($contact->id_product,true);
                    if(!$id_image)
                        $id_image = Ets_mp_product::getImageByIDProduct($contact->id_product);
                    $product = new Product($contact->id_product,false,$this->context->language->id);
                    if($this->module->is17)
                        $type_image = ImageType::getFormattedName('small');
                    else
                        $type_image = ImageType::getFormatedName('small');
                    $this->context->smarty->assign(
                        array(
                            'product' =>$product,
                            'link_image' => $id_image ? $this->context->link->getImageLink($product->link_rewrite,$id_image,$type_image):'',
                        )
                    );
                }
                if($contact->id_order)
                {
                    $this->context->smarty->assign(
                        array(
                            'order_message' => new Order($contact->id_order),
                        )
                    );
                }
                $this->context->smarty->assign(
                    array(
                        'contact' => $contact,
                        'messages' => $messages,
                        'seller_page' => true, 
                    )
                );
                return $this->module->displayTpl('shop/message.tpl');
            }
            
        }
        $fields_list = array(
            'reference'=>array(
                'title' => $this->module->l('Order ref','orders'),
                'type'=> 'text',
                'sort' => true,
                'filter' => true,
            ),
            'author'=> array(
                'title'=> $this->module->l('Contact name','messages'),
                'type'=> 'text',
                'sort' => false,
                'filter' => true,
                'strip_tag'=>false,
            ),
            'message' => array(
                'title'=> $this->module->l('Message','messages'),
                'type'=> 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag'=>false,
            ),
            'date_add' => array(
                'title' => $this->module->l('Date','messages'),
                'type' => 'date',
                'sort' => true,
                'filter' => true
            ),
        );
        $show_resset = false;
        $filter = "";
        if(($reference = trim(Tools::getValue('reference'))) || $reference!='')
        {
            if(Validate::isReference($reference))
                $filter .= ' AND reference LIKE "%'.pSQL($reference).'%"';
            $show_resset=true;
        }
        if(($message = trim(Tools::getValue('message'))) || $message!='')
        {
            if(Validate::isCleanHtml($message))
                $filter .=' AND message LIKE "%'.pSQL($message).'%"';
            $show_resset=true;
        }
        if(($author = trim(Tools::getValue('author'))) || $author!='')
        {
            if(Validate::isCleanHtml($author))
                $filter .=' AND (seller_min_name LIKE "%'.pSQL($author).'%" || (customer_min_name LIKE "%'.pSQL($author).'%" && id_employee_min=0) || employee_min_name LIKE "%'.pSQL($author).'%"  )';
            $show_resset=true;
        }
        if(($date_add_min = trim(Tools::getValue('date_add_min'))) || $date_add_min!='')
        {
            if(Validate::isDate($date_add_min))
                $filter .=' AND date_add >= "'.pSQL($date_add_min).' 00:00:00"';
            $show_resset = true;
        }
        if(($date_add_max = trim(Tools::getValue('date_add_max'))) || $date_add_max!='')
        {
            if(Validate::isDate($date_add_max))
                $filter .=' AND date_add <= "'.pSQL($date_add_max).' 23:59:59"';
            $show_resset = true;
        }
        $sort = "";
        $sort_type=Tools::getValue('sort_type','desc');
        $sort_value =Tools::getValue('sort','date_add'); 
        if($sort_value)
        {
            switch ($sort_value) {
                case 'date_add':
                    $sort .='date_add';
                    break;
                case 'reference':
                    $sort .='reference';
                    break;
                case 'message':
                    $sort .='message';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                    $sort .= ' '.trim($sort_type); 
        }
        $page = (int)Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $totalRecords = (int) $this->module->_getOrderMessages($filter,0,0,'',true);
        $paggination = new Ets_mp_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url =$this->context->link->getModuleLink($this->module->name,'messages',array('list'=>true, 'page'=>'_page_')).$this->module->getFilterParams($fields_list,'ms_message');
        $paggination->limit =  (int)Tools::getValue('paginator_message_select_limit',10);
        $paggination->name ='message';
        $paggination->num_links =5;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $messages = $this->module->_getOrderMessages($filter, $start,$paggination->limit,$sort,false);
        if($messages)
        {
            foreach($messages as &$message)
            {               
                if(!$message['id_contact'])
                {
                    $message['child_view_url'] = $this->context->link->getModuleLink($this->module->name,'messages',array('viewmessage'=>1,'id_order'=>$message['id_order']));
                    $message['view_order_url'] = $this->context->link->getModuleLink($this->module->name,'orders',array('id_order'=>$message['id_order']));
                    $message['action_edit'] = true;
                }
                else
                {
                    $message['child_view_url'] = $this->context->link->getModuleLink($this->module->name,'messages',array('viewmessage'=>1,'id_contact'=>$message['id_contact']));
                    $message['view_order_url'] = '';
                    $message['action_edit'] = true;
                }
                if(Tools::strlen($message['message'])>135)
                {
                    $message['message'] = Tools::substr($message['message'],0,135).'...';
                } 
                if($message['id_employee_min'])
                {
                    if($message['manager_min_name'])
                        $message['author'] = $message['manager_min_name'].' ('.$this->module->l('Seller manager','messages').')';
                    elseif($message['seller_min_name'])
                        $message['author'] = $message['seller_min_name'].' ('.$this->module->l('Seller','messages').')';
                    else
                        $message['author'] = $message['employee_min_name'].' ('.$this->module->l('Admin','messages').')';
                }
                else
                    $message['author'] = $message['customer_name'].' ('.$this->module->l('Customer','messages').')';; 
                
                if($message['id_employee'] || $message['id_seller'])
                {
                    $message['read']=1; 
                    if($message['manager_name'])
                        $message['author_message'] = $message['manager_name'].' ('.$this->module->l('Seller manager','messages').')';
                    elseif($message['seller_name'])
                        $message['author_message'] = $message['seller_name'].' ('.$this->module->l('Seller','messages').')';
                    else
                        $message['author_message'] = $message['employee_name'].' ('.$this->module->l('Admin','messages').')';
                }
                else
                    $message['author_message'] = $message['customer_name']; 
                if(!$message['read'])
                    $message['read']=0;
                $message['message'] = Module::getInstanceByName('ets_marketplace')->displayText($message['title_contact'],'b')
                .Module::getInstanceByName('ets_marketplace')->displayText('','br')
                .$message['message']
                .Module::getInstanceByName('ets_marketplace')->displayText('','br')
                .($message['author_message'] ? Module::getInstanceByName('ets_marketplace')->displayText($this->module->l('Last replied by: ','messages').$message['author_message'],'b') : '');
            }
        }
        $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','messages');
        $paggination->style_links = $this->module->l('links','messages');
        $paggination->style_results = $this->module->l('results','messages');
        $listData = array(
            'name' => 'ms_message',
            'actions' => array('view','vieworder','delete'),
            'currentIndex' => $this->context->link->getModuleLink($this->module->name,'messages',array('list'=>1)).($paggination->limit!=10 ? '&paginator_message_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getModuleLink($this->module->name,'messages',array('list'=>1)),
            'identifier' => 'id_contact',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->module->l('Messages','messages'),
            'fields_list' => $fields_list,
            'field_values' => $messages,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list,'ms_message'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'show_add_new'=> false,
            'sort_type' => $sort_type,
        );            
        return $this->module->renderList($listData);
    }
    
 }