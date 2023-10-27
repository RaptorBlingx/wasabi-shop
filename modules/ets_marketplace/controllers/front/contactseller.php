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
 * Class Ets_MarketPlaceContactsellerModuleFrontController
 * @property \Ets_marketplace $module;
 * @property \Ets_mp_seller $seller
 */
class Ets_MarketPlaceContactsellerModuleFrontController extends ModuleFrontController
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
        if(!$this->context->customer->isLogged())
            Tools::redirectLink($this->context->link->getPageLink('authentication',null,null,array('back'=> $this->context->link->getModuleLink($this->module->name,'contactseller'))));
        if(Tools::isSubmit('downloadfile') && ($id_contact = (int)Tools::getValue('id_contact')) && Validate::isUnsignedId($id_contact))
        {
            if(!(($contact = new Ets_mp_contact($id_contact)) && Validate::isLoadedObject($contact) && $contact->id_customer== $this->context->customer->id))
                die($this->module->l('You do not have permission to download this attachment','contactseller'));
            else
            {
                if($contact->attachment)
                {
                    $filepath =_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_attachment/'.$contact->attachment;
                    if(file_exists($filepath)){
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/octet-stream');
                        header('Content-Disposition: attachment; filename="'. ($contact->attachment_name ? : $contact->attachment).'"');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($filepath));
                        flush(); // Flush system output buffer
                        readfile($filepath);
                        exit;
                    }
                    else
                        die($this->module->l('File attachment is null','contactseller').$filepath);
                }
                else
                    die($this->module->l('File attachment is null','contactseller'));
            }
        }
        if(Tools::isSubmit('del') && ($id_contact = (int)Tools::getValue('id_contact')) && Validate::isUnsignedId($id_contact))
        {
            if(($contact = new Ets_mp_contact($id_contact)) && Validate::isLoadedObject($contact) && $contact->id_customer== $this->context->customer->id )
            {
                if($contact->delete())
                {
                    $this->context->cookie->_success = $this->module->l('Deleted successfully','contactseller');
                    Tools::redirect($this->context->link->getModuleLink($this->module->name,'contactseller',array('list'=>1)));
                }
                else
                    $this->_errors[] = $this->module->l('Delete message failed','contactseller');
            }
            else
                $this->_errors[] = $this->module->l('You do not have permission to delete this message','contactseller');
        }
        if(Tools::isSubmit('submitMessage') && ($id_contact = (int)Tools::getValue('id_contact')) && Validate::isUnsignedId($id_contact))
        {
            $contact = new Ets_mp_contact($id_contact);
            if(!Validate::isLoadedObject($contact) || $contact->id_customer!=$this->context->customer->id)
                die($this->module->l('You do not have permission to reply this contact','contactseller'));
            if (!($message = Tools::getValue('message')))
                $this->_errors[] = $this->module->l('The message cannot be blank.','contactseller');
            if($message && !Validate::isCleanHtml($message))
                $this->_errors[] = $this->module->l('Message is not valid','contactseller');
            elseif(Tools::strlen($message) >300)
                $this->_errors[] = $this->module->l('Message can not be longer than 300 characters','contactseller');
            if(!$this->_errors)
            {
                $contact_message = new Ets_mp_contact_message();
                $contact_message->id_customer = (int)$this->context->customer->id;
                $contact_message->message = $message;
                $contact_message->id_contact = (int)$id_contact;
                $contact_message->customer_read = 1;
                if($contact_message->add())
                {
                    if(Configuration::get('ETS_MP_EMAIL_NEW_CONTACT'))
                    {
                        $seller_contact= new Ets_mp_seller((int)$contact->id_seller);
                        if($seller_contact->seller_email)
                        {
                            $this->context->smarty->assign(
                                array(
                                    'message' => $message, 
                                    'id_contact' => $id_contact,   
                                )
                            );
                            $template_vars = array(
                                '{content_message}' => $this->module->displayTpl('content_message.tpl'),
                                '{seller_name}' => $seller_contact->seller_name,
                                '{customer_name}' => $this->context->customer->firstname.' '.$this->context->customer->lastname,
                                '{message_title}' => $contact->title,
                                '{link_reply}' => $this->context->link->getModuleLink($this->module->name,'contactseller',array('viewmessage'=>1,'id_contact'=> $id_contact)),
                            );
                            Mail::Send(
                    			$this->context->language->id,
                    			'customer_reply',
                    			($this->module->getTextLang('A new contact message from',$seller_contact->id_language,'contactseller') ? : $this->module->l('A new contact message from','contactseller')).' '.$this->context->customer->firstname.' '.$this->context->customer->lastname,
                    			$template_vars,
                    			$seller_contact->seller_email,
                    			$seller_contact->seller_name,
                    			null,
                    			null,
                    			null,
                    			null,
                    			dirname(__FILE__).'/../../mails/',
                    			null,
                    			$this->context->shop->id
                    		);
                        }
                        
                    }
                    $this->_success = $this->module->l('Message was sent successfully.','contactseller');
                } 
                else
                    $this->_errors[] = $this->module->l('An error occurred while saving the message.','contactseller');
            }    
        }
        elseif(Tools::isSubmit('submitNewMessage'))
        {
            $ETS_MP_CONTACT_FIELDS = Configuration::get('ETS_MP_CONTACT_FIELDS') ? explode(',',Configuration::get('ETS_MP_CONTACT_FIELDS')):array();
            $ETS_MP_CONTACT_FIELDS_VALIDATE = Configuration::get('ETS_MP_CONTACT_FIELDS_VALIDATE') ? explode(',',Configuration::get('ETS_MP_CONTACT_FIELDS_VALIDATE')):array();
            $id_seller = (int)Tools::getValue('id_seller');
            $id_product = (int)Tools::getValue('id_product');
            if(!($title = Tools::getValue('title')))
                $this->_errors[] = $this->module->l('Title is required','contactseller');
            if(Tools::strlen($title)>100)
                $this->_errors[] = $this->module->l('Title cannot be longer than 100 characters','contactseller');
            if(!($message = Tools::getValue('message')))
                $this->_errors[] = $this->module->l('Message is required','contactseller');
            if(!($email = Tools::getValue('email')))
                $this->_errors[] = $this->module->l('Email is required','contactseller');
            if(Tools::strlen($message) >300)
                $this->_errors[] = $this->module->l('Message cannot be longer than 300 characters','contactseller');
            if(!($name = Tools::getValue('name')) && in_array('name',$ETS_MP_CONTACT_FIELDS) && in_array('name',$ETS_MP_CONTACT_FIELDS_VALIDATE))
            {
                $this->_errors[] = $this->module->l('Name is required','contactseller');
            }
            if(!($phone = Tools::getValue('phone')) && in_array('phone',$ETS_MP_CONTACT_FIELDS) && in_array('phone',$ETS_MP_CONTACT_FIELDS_VALIDATE))
            {
                $this->_errors[] = $this->module->l('Phone is required','contactseller');
            }
            if(!($reference = Tools::getValue('reference')) && $this->context->customer->isLogged() && in_array('reference',$ETS_MP_CONTACT_FIELDS) && in_array('reference',$ETS_MP_CONTACT_FIELDS_VALIDATE))
            {
                $this->_errors[] = $this->module->l('Order reference is required','contactseller');
            }
            if(in_array('attachment',$ETS_MP_CONTACT_FIELDS) && in_array('attachment',$ETS_MP_CONTACT_FIELDS_VALIDATE) && (!isset($_FILES['attachment']) || (isset($_FILES['attachment']) && !$_FILES['attachment']['name'])))
            {
                $this->_errors[] = $this->module->l('Attached file is required','contactseller');
            }
            if($title && !Validate::isCleanHtml($title))
                $this->_errors[] = $this->module->l('Title is not valid','contactseller');
            if($message && !Validate::isCleanHtml($message))
                $this->_errors[] = $this->module->l('Message is not valid','contactseller');
            if($email && !Validate::isEmail($email))
                $this->_errors[] = $this->module->l('Email is not valid','contactseller');
            $id_order=0;
            if($reference && !Validate::isReference($reference))
                $this->_errors[] = $this->module->l('Reference is not valid','contactseller');
            if($name && !Validate::isName($name))
                $this->_errors[] = $this->module->l('Name is not valid','contactseller');
            if($id_seller)
            {
                $seller = new Ets_mp_seller($id_seller,$this->context->language->id);
            }elseif($id_product)
            {
                if($id_customer = (int)Ets_mp_seller::getIDCustomerSellerByIdProduct($id_contact))
                {
                    $seller = Ets_mp_seller::_getSellerByIdCustomer($id_customer,$this->context->language->id);
                }
            }
            if(!(isset($seller) && Validate::isLoadedObject($seller)))
                $this->_errors[] = $this->shop->l('Shop contact is not valid','contactseller');
            elseif($phone && !Validate::isPhoneNumber($phone))
                $this->_errors[] = $this->module->l('Phone is not valid','contactseller');
            elseif($reference && $this->context->customer->isLogged())
            {
                if(!$id_order = (int)$seller->getIDOrderByReferenceIDCustomer($this->context->customer->id,$reference))
                    $this->_errors[] = $this->module->l('Order reference does not exist','contactseller');
            }
            $attachment ='';
            $attachment_name ='';
            $g_recaptcha_response = Tools::getValue('g-recaptcha-response');
            if(Configuration::get('ETS_MP_ENABLE_CAPTCHA') && Tools::isSubmit('g-recaptcha-response') && Validate::isCleanHtml($g_recaptcha_response))
            {
                if(!$g_recaptcha_response)
                {
                    $this->_errors[] = $this->module->l('reCAPTCHA is invalid','contactseller');
                }
                else
                {
                    $recaptcha = $g_recaptcha_response ? $g_recaptcha_response : false;
                    if ($recaptcha) {
                        $response = json_decode(Tools::file_get_contents($this->module->link_capcha), true);
                        if ($response['success'] == false) {
                            $this->_errors[] = $this->module->l('reCAPTCHA is invalid','contactseller');
                        }
                    }
                }
                
            }
            if(isset($_FILES['attachment']) && isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'])
            {
                
                if(!Validate::isFileName(str_replace(array(' ','(',')','!','@','#','+'),'_',$_FILES['attachment']['name'])))
                    $this->_errors[] = '"'.$_FILES['attachment']['name'].'" '.$this->module->l('file name is not valid','contactseller');
                else
                {
                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES['attachment']['name'], '.'), 1));
                    if(!is_dir(_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_attachment/'))
                    {
                        @mkdir(_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_attachment/',0777,true);
                        @copy(dirname(__FILE__).'/index.php', _PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_attachment/index.php');
                    }
                    $target_file = _PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_attachment/';
                    $file_name = Tools::strtolower(Tools::passwdGen(12, 'NO_NUMERIC'));
                    if(!file_exists($target_file.$file_name))
                        $file_name = $file_name;
                    else
                        $file_name = time().'_'.$file_name;
                    $target_file .=$file_name; 
                    if(file_exists($target_file))
                        $this->_errors[] = $this->module->l('Attachment already exists. Try to rename the file then reupload');
                    if(!in_array($type, array('jpg', 'gif', 'jpeg', 'png','zip','rar','pdf')))
                    {
                        $this->_errors[] = $this->module->l('Attachment is not valid','contactseller');
                    }
                    $max_sizefile = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');
                    if($_FILES['attachment']['size'] > $max_sizefile*1024*1024)
                        $this->_errors[] = $this->module->l('Sorry, your file is too large.','contactseller');
                    if(!$this->_errors)
                    {
                        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) 
                        {
                            $attachment = $file_name;
                            $attachment_name = $_FILES['attachment']['name'];
  
                        } else {
                            $this->_errors[] = $this->module->l('Sorry, there was an error occurred while uploading your file.','contactseller');
                        }
                    }
                }
                
            }
            if(!$this->_errors)
            {
                if(!$this->context->customer->isLogged() && !Configuration::get('ETS_MP_ALLOW_GUEST_CONTACT_SHOP'))
                {
                    die(
                        json_encode(
                            array(
                                'display_form_login' => true,
                            )
                        )
                    );
                }
                $contact = new Ets_mp_contact();
                $contact->id_customer = (int)$this->context->customer->id;
                $contact->id_seller = (int)$id_seller;
                $contact->id_product = (int)$id_product;
                $contact->id_order = (int)$id_order;
                $contact->name = $name;
                $contact->email = $email;
                $contact->phone = $phone;
                if($contact->add() && ($id_contact = $contact->id))
                {
                   $contact_message = new Ets_mp_contact_message();
                   $contact_message->id_contact = (int)$id_contact;
                   $contact_message->id_customer = 0;
                   $contact_message->id_seller = 0;
                   $contact_message->id_employee = 0;
                   $contact_message->title = $title;
                   $contact_message->message = $message;
                   $contact_message->attachment = $attachment;
                   $contact_message->attachment_name = $attachment_name;
                   $contact_message->customer_read = 1;
                   if($contact_message->add())   
                   {
                        if(Configuration::get('ETS_MP_EMAIL_NEW_CONTACT'))
                        {
                            $seller_contact= new Ets_mp_seller((int)$id_seller);
                            $product_link = Tools::getValue('product_link');
                            if($seller_contact->seller_email)
                            {
                                $this->context->smarty->assign(
                                    array(
                                        'title' => $title,
                                        'product_link' => Validate::isCleanHtml($product_link) ? $product_link :'s',
                                        'reference' => $reference,
                                        'attachment' => $attachment,
                                        'attachment_name' => $attachment_name,
                                        'email' => $email,
                                        'name' => $name,
                                        'phone' => $phone,
                                        'message' => $contact_message->message,  
                                        'id_contact' => $id_contact,
                                        'link'=>  $this->context->link  
                                    )
                                );
                                $template_vars = array(
                                    '{content_message}' => $this->module->displayTpl('content_message.tpl'),
                                    '{seller_name}' => $seller_contact->seller_name,
                                    '{seller_shop_name}' => $seller_contact->shop_name[$seller_contact->id_language],
                                    '{link_reply}' => $this->context->link->getModuleLink($this->module->name,'contactseller',array('viewmessage'=>1,'id_contact'=> $id_contact)),
                                    '{customer_name}' => $this->context->customer->firstname.' '.$this->context->customer->lastname,
                                );
                                Mail::Send(
                        			$seller_contact->id_language,
                        			'customer_contact',
                        			($this->module->getTextLang('A new contact message from',$seller_contact->id_language,'contactseller') ? : $this->module->l('A new contact message from','contactseller') ).' '.$this->context->customer->firstname.' '.$this->context->customer->lastname,
                        			$template_vars,
                        			$seller_contact->seller_email,
                        			$seller_contact->seller_name,
                        			null,
                        			null,
                        			null,
                        			null,
                        			dirname(__FILE__).'/../../mails/',
                        			null,
                        			$this->context->shop->id
                        		);
                            }
                            $template_vars = array(
                                '{title}' => $contact->title,
                                '{customer_name}' => $name ? : $this->context->customer->firstname .' '.$this->context->customer->lastname,
                                '{shop_name}' => $seller_contact->shop_name[$this->context->language->id],
                            );
                            $subjects = array(
                                'translation' => $this->module->l('Your message was sent successfully'),
                                'origin'=> 'Your message was sent successfully',
                                'specific'=>'contactseller'
                            );
                            Ets_marketplace::sendMail('new_contact_customer',$template_vars,$email ? : $this->context->customer->email,$subjects,$name ? : $this->context->customer->firstname .' '.$this->context->customer->lastname);
                        }
                        die(
                            json_encode(
                                array(
                                    'success' => $this->module->l('Message was sent successfully.','contactseller'),
                                )
                            )
                        );
                   }
                   elseif(isset($target_file) && file_exists($target_file))
                        @unlink($target_file);     
                }
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($this->_errors),
                        )
                    )
                );
            }
        }
        if(Tools::isSubmit('submitLoginCustomerContact'))
        {
            if(!Tools::getValue('email'))
                $this->_errors[] = $this->module->l('Email is required','contactseller');
            elseif(Tools::getValue('email') && !Validate::isEmail(Tools::getValue('email')))
                $this->_errors[] = $this->module->l('Email is not valid','contactseller');
            if(!Tools::getValue('password'))
                $this->_errors[] = $this->module->l('Password is required','contactseller');
            if(Tools::getValue('password') && !Validate::isPasswd(Tools::getValue('password')))
                $this->_errors[] = $this->module->l('Password is not valid','contactseller');
            if(!$this->_errors)
            {
                if($id_customer= Customer::customerExists(Tools::getValue('email')))
                {
                    $customer = new Customer($id_customer);
                    if($customer->getByEmail(Tools::getValue('email'),Tools::getValue('password')))
                    {
                        if ($this->module->is17)
                            $this->context->updateCustomer($customer);
                        else
                            $this->module->updateContext($customer);
                        die(
                            json_encode(
                                array(
                                    'success' => $this->module->l('Logged in successfully','contactseller'),
                                    'email' => $customer->email,
                                    'name' =>$customer->firstname.' '.$customer->lastname,
                                )
                            )
                        );
                    }
                    else
                        $this->_errors[] = $this->module->l('Authentication failed.','contactseller');
                }
                else
                    $this->_errors[] = $this->module->l('Authentication failed.','contactseller');
                
            }
            if($this->_errors)
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($this->_errors),
                        )
                    )
                );
            } 
        }
        if(Tools::isSubmit('submitCustomerContact'))
        {
            if(Tools::getValue('email') && Validate::isEmail(Tools::getValue('email')) && ($id_customer= Customer::customerExists(Tools::getValue('email'))))
            {
                $this->_errors[] = $this->module->l('The email is already used, please choose another one or sign in','contactseller');   
            }
            else
            {
                if(!($firstname =  Tools::getValue('firstname')))
                    $this->_errors[] = $this->module->l('First name is required','contactseller');
                elseif($firstname && !Validate::isName($firstname))
                    $this->_errors[] = $this->module->l('First name is not valid','contactseller');
                if(!($lastname= Tools::getValue('lastname')))
                    $this->_errors[] = $this->module->l('Last name is required','contactseller');
                elseif($lastname && !Validate::isName($lastname))
                    $this->_errors[] = $this->module->l('Last name is not valid','contactseller');
                if(!($email = Tools::getValue('email')))
                    $this->_errors[] = $this->module->l('Email is required','contactseller');
                elseif($email && !Validate::isEmail($email))
                    $this->_errors[] = $this->module->l('Email is not valid','contactseller');
                if(!($password = Tools::getValue('password')))
                    $this->_errors[] = $this->module->l('Password is required','contactseller');
                if($password && !Validate::isPasswd($password))
                    $this->_errors[] = $this->module->l('Password is not valid','contactseller');
                if(!$this->_errors)
                {
                    $customer = new Customer();
        			$customer->id_shop = (int)$this->context->shop->id;
        			$customer->lastname = $lastname;
        			$customer->firstname = $firstname;
        			$customer->email = $email;
        			$passwdGen = $password;
        			$customer->passwd = md5(_COOKIE_KEY_.$passwdGen);
        			if ($customer->save())
        			{
        				if ($this->module->is17)
                        {
                            $this->context->updateCustomer($customer);
                            Hook::exec('actionAuthentication', array('customer' => $this->context->customer));
                            CartRule::autoRemoveFromCart($this->context);
                            CartRule::autoAddToCart($this->context);
                        }
                        else
        				    $this->module->updateContext($customer);
                        
        			}
                    die(
                        json_encode(
                            array(
                                'success' => $this->module->l('Create customer successfully','contactseller'),
                                'email' => $customer->email,
                                'name' =>$customer->firstname.' '.$customer->lastname,
                            )
                        )
                    );
                }
            }
            if($this->_errors)
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($this->_errors),
                        )
                    )
                );
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
                '_success' =>  $this->_success ? $this->module->displayConfirmation($this->_success):'',
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
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/contact.tpl');      
        else        
            $this->setTemplate('contact_16.tpl'); 
    }
    public function _initContent()
    {
        if(Tools::isSubmit('viewmessage') && ($id_contact= Tools::getValue('id_contact')) && Validate::isUnsignedId($id_contact))
        {
            if(!$this->context->customer->isLogged())
                Tools::redirectLink($this->context->link->getPageLink('authentication',null,null,array('back'=> $this->context->link->getModuleLink($this->module->name,'contactseller',array('viewmessage'=>1,'id_contact'=>$id_contact)))));
            $contact = new Ets_mp_contact($id_contact);
            if(!Validate::isLoadedObject($contact) || $contact->id_customer!= $this->context->customer->id)
                Tools::redirect($this->context->link->getModuleLink($this->module->name,'contactseller'));
            if($contact)
            {
                $messages = $contact->getMessages();
                $contact->updateCustomerReaedMessage();
                if($contact->id_product)
                {
                    $id_image = Ets_mp_product::getImageByIDProduct($contact->id_product,1);
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
                    )
                );
                return $this->module->displayTpl('shop/message.tpl');
            }
            else
                return $this->module->displayWarning($this->module->l('Contact message not found','contactseller'));
        }
        if(($id_seller = Tools::getValue('id_seller')) && Validate::isUnsignedId($id_seller))
        {
            $seller = new Ets_mp_seller($id_seller,$this->context->language->id);
        }elseif(($id_product = (int)Tools::getValue('id_product')) && Validate::isUnsignedId($id_product))
        {
            if($id_customer = (int)Ets_mp_seller::getIDCustomerSellerByIdProduct($id_product))
            {
                $seller = Ets_mp_seller::_getSellerByIdCustomer($id_customer,$this->context->language->id);
            }
            $this->context->smarty->assign(
                array(
                    'product_link' => $this->context->link->getProductLink($id_product),
                    'id_product' => $id_product,
                    'product_class' => new Product($id_product,false,$this->context->language->id),
                )
            );
        }
        if(isset($seller))
        {
            if($seller && $seller->id)
            {
                if($this->context->customer->isLogged())
                {
                    $this->context->smarty->assign(
                        array(
                            'order_references' => $seller->getListOrderByIDCustomer($this->context->customer->id),
                        )
                    );
                }
                if(Configuration::get('ETS_MP_ENABLE_CAPTCHA') && Configuration::get('ETS_MP_ENABLE_CAPTCHA_FOR'))
                {
                    $captcha_for = explode(',',Configuration::get('ETS_MP_ENABLE_CAPTCHA_FOR'));
                    if(in_array('shop_contact',$captcha_for) && (!$this->context->customer->isLogged() || !Configuration::get('ETS_MP_NO_CAPTCHA_IS_LOGIN')))
                        $is_captcha = true;
                }
                $this->context->smarty->assign(
                    array(
                        'seller' => $seller,
                        'seller_link' => $this->module->getShopLink(array('id_seller'=>$seller->id)),
                        'max_sizefile' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
                        'logged' => $this->context->customer->isLogged(),
                        'is_captcha' => isset($is_captcha) ? $is_captcha:false,
                        'contact_customer' => $this->context->customer,
                        'ETS_MP_ENABLE_CAPTCHA_TYPE' => Configuration::get('ETS_MP_ENABLE_CAPTCHA_TYPE'),
                        'ETS_MP_ENABLE_CAPTCHA_SITE_KEY2' => Configuration::get('ETS_MP_ENABLE_CAPTCHA_SITE_KEY2'),
                        'ETS_MP_ENABLE_CAPTCHA_SECRET_KEY2' => Configuration::get('ETS_MP_ENABLE_CAPTCHA_SECRET_KEY2'),
                        'ETS_MP_ENABLE_CAPTCHA_SITE_KEY3' => Configuration::get('ETS_MP_ENABLE_CAPTCHA_SITE_KEY3'),
                        'ETS_MP_ENABLE_CAPTCHA_SECRET_KEY3' => Configuration::get('ETS_MP_ENABLE_CAPTCHA_SECRET_KEY3'),
                        'ETS_MP_CONTACT_FIELDS' => Configuration::get('ETS_MP_CONTACT_FIELDS') ? explode(',',Configuration::get('ETS_MP_CONTACT_FIELDS')):array(),
                        'ETS_MP_CONTACT_FIELDS_VALIDATE' => Configuration::get('ETS_MP_CONTACT_FIELDS_VALIDATE') ? explode(',',Configuration::get('ETS_MP_CONTACT_FIELDS_VALIDATE')):array(),
                    )
                );
                return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/contact.tpl');
            }
            else
                return $this->module->displayWarning($this->module->l('Shop not found','contactseller'));
            
        }
        return $this->displayListMessage();
    }
    public function displayListMessage()
    {
        $fields_list = array(
            'reference'=>array(
                'title' => $this->module->l('Order reference','orders'),
                'type'=> 'text',
                'sort' => true,
                'filter' => true,
            ),
            'author'=> array(
                'title'=> $this->module->l('Customer','contactseller'),
                'type'=> 'text',
                'sort' => false,
                'filter' => true,
                'strip_tag'=>false,
            ),
            'message' => array(
                'title'=> $this->module->l('Messages','contactseller'),
                'type'=> 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag'=>false,
            ),
            'date_add' => array(
                'title' => $this->module->l('Date time','contactseller'),
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
                $filter .=' AND (seller_name LIKE "%'.pSQL($author).'%" || (customer_name LIKE "%'.pSQL($author).'%" && id_employee=0 AND id_seller=0) || employee_name LIKE "%'.pSQL($author).'%"  )';
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
        $sort_value = Tools::getValue('sort','date_add');
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
        $totalRecords = (int)Ets_mp_contact::getOrderMessages($filter,0,0,'',true);
        $paggination = new Ets_mp_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url =$this->context->link->getModuleLink($this->module->name,'contactseller',array('list'=>true, 'page'=>'_page_')).$this->module->getFilterParams($fields_list,'ms_message');
        $paggination->limit =  (int)Tools::getValue('paginator_contact_select_limit',10);
        $paggination->name ='contact';
        $paggination->num_links =5;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $messages = Ets_mp_contact::getOrderMessages($filter, $start,$paggination->limit,$sort,false);
        if($messages)
        {
            foreach($messages as &$message)
            {
                $message['child_view_url'] = $this->context->link->getModuleLink($this->module->name,'contactseller',array('viewmessage'=>1,'id_contact'=>$message['id_contact']));
                $message['view_order_url'] = '';
                $message['action_edit'] = true;
                if(Tools::strlen($message['message'])>135)
                    $message['message'] = Tools::substr($message['message'],0,135).'...';  
                $message['read'] = (int)$message['customer_read']; 
                if($message['id_employee'])
                {
                    
                    if($message['seller_name'])
                        $message['author'] = $message['seller_name'].' ('.$this->module->l('Seller','contactseller').')';
                    else
                        $message['author'] = $message['employee_name'].' ('.$this->module->l('Admin','contactseller').')';
                }
                else
                    $message['author'] = $message['customer_name'];               
            }
        }
        $paggination->text =  $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)','contactseller');
        $paggination->style_links = $this->module->l('links','contactseller');
        $paggination->style_results = $this->module->l('results','contactseller');
        $listData = array(
            'name' => 'ms_message',
            'actions' => array('view','vieworder','delete'),
            'currentIndex' => $this->context->link->getModuleLink($this->module->name,'contactseller',array('list'=>1)).($paggination->limit!=10 ? '&paginator_contact_select_limit='.$paggination->limit:''),
            'postIndex' => $this->context->link->getModuleLink($this->module->name,'contactseller',array('list'=>1)),
            'identifier' => 'id_contact',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->module->l('Contact shop','contactseller'),
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