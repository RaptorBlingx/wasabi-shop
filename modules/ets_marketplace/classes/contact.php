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
class Ets_mp_contact extends ObjectModel
{
    public $id_customer;
    public $id_seller;
    public $id_order;
    public $id_product;
    public $name;
    public $email;
    public $phone;
    public $title;
    public $id_message;
    public $attachment;
    public $attachment_name;
    public $message;
    public static $definition = array(
		'table' => 'ets_mp_seller_contact',
		'primary' => 'id_contact',
		'multilang' => false,
		'fields' => array(
			'id_customer' => array('type' => self::TYPE_INT),
            'id_seller' => array('type' => self::TYPE_INT),
            'id_order' => array('type'=>self::TYPE_INT),
            'id_product' => array('type'=>self::TYPE_INT),
            'name' => array('type'=>self::TYPE_STRING),
            'email' => array('type'=>self::TYPE_STRING),
            'phone' => array('type'=> self::TYPE_STRING),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
        $message_contact = Db::getInstance()->getRow('SELECT id_message,title,attachment,attachment_name,message FROM `'._DB_PREFIX_.'ets_mp_seller_contact_message` WHERE id_contact='.(int)$this->id.' AND attachment!="" ORDER BY id_message ASC');
        if(!$message_contact)
            $message_contact = Db::getInstance()->getRow('SELECT id_message,title,attachment,attachment_name,message FROM `'._DB_PREFIX_.'ets_mp_seller_contact_message` WHERE id_contact='.(int)$this->id.' ORDER BY id_message ASC');
        $this->title = isset($message_contact['title']) ? $message_contact['title']:'';
        $this->id_message = isset($message_contact['id_message']) ? $message_contact['id_message'] : 0;
        $this->attachment = isset($message_contact['attachment']) ? $message_contact['attachment'] : '';
        $this->attachment_name = isset($message_contact['attachment_name']) ? $message_contact['attachment_name'] : '';
        $this->message = isset($message_contact['message']) ? $message_contact['message'] :'';
	}
    public function delete()
    {
        if(parent::delete())
        {
            $messages = Db::getInstance()->executeS('SELECT id_message FROM `'._DB_PREFIX_.'ets_mp_seller_contact_message` WHERE id_contact='.(int)$this->id);
            if($messages)
            {
                foreach($messages as $message)
                {
                    $contact_message = new Ets_mp_contact_message($message['id_message']);
                        $contact_message->delete();
                }
            }
            return true;
        }
    }
    public function getTitle()
    {
        $sql = 'SELECT title FROM `'._DB_PREFIX_.'ets_mp_seller_contact_message` WHERE id_contact="'.(int)$this->id.'" ORDER BY id_message ASC';
        return Db::getInstance()->getValue($sql);
    }
    public function getMessages()
    {
        $messages = Db::getInstance()->executeS('SELECT cm.*,CONCAT(c.firstname," ",c.lastname) as customer_name,CONCAT(customer.firstname," ",customer.lastname) as seller_name, CONCAT(e.firstname," ",e.lastname) as employee_name,CONCAT(manager.firstname," ",manager.lastname) as manager_name FROM `'._DB_PREFIX_.'ets_mp_seller_contact_message` cm
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer = cm.id_customer)
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller` seller ON (seller.id_seller=cm.id_seller)
        LEFT JOIN `'._DB_PREFIX_.'customer` customer ON (seller.id_customer=customer.id_customer)
        LEFT JOIN `'._DB_PREFIX_.'customer` manager ON (manager.id_customer=cm.id_manager)
        LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee = cm.id_employee)
        WHERE id_contact="'.(int)$this->id.'" AND id_message !="'.(int)$this->id_message.'" ORDER BY date_add DESC');
        return $messages;
    }
    public function updateCustomerReaedMessage()
    {
        return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_mp_seller_contact_message` SET customer_read=1 WHERE id_contact="'.(int)$this->id.'"');
    }
    public static function getIDCustomerThreadByIDOrder($id_order)
    {
        return Db::getInstance()->getValue('SELECT id_customer_thread FROM `'._DB_PREFIX_.'customer_thread` WHERE id_order="'.(int)$id_order.'" ');
    }
    public static function getOrderMessages($filter='',$start=0,$limit=12,$order_by='',$total=false)
    {
        $sql2 = 'SELECT o.reference, contact.id_order,contact.id_contact,cm.message,cm.id_employee,cm.id_seller,cm.date_add,cm.customer_read,CONCAT(customer.firstname," ",customer.lastname) as seller_name,if(contact.id_customer!=0,CONCAT(c.firstname," ",c.lastname),contact.name) as customer_name, CONCAT(e.firstname," ",e.lastname) as employee_name
        FROM `'._DB_PREFIX_.'ets_mp_seller_contact` contact
        INNER JOIN `'._DB_PREFIX_.'ets_mp_seller_contact_message` cm ON (contact.id_contact=cm.id_contact)
        INNER JOIN (SELECT id_contact,max(id_message) as id_message_max FROM `'._DB_PREFIX_.'ets_mp_seller_contact_message` GROUP BY id_contact) cmmax ON (cmmax.id_message_max = cm.id_message AND cmmax.id_contact= contact.id_contact)
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer= contact.id_customer)
        LEFT JOIN `'._DB_PREFIX_.'ets_mp_seller` seller ON (seller.id_seller=cm.id_seller)
        LEFT JOIN `'._DB_PREFIX_.'customer` customer ON (customer.id_customer=seller.id_customer)
        LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee=cm.id_employee)
        LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.id_order=contact.id_order)
        WHERE contact.id_customer="'.(int)Context::getContext()->customer->id.'"';
        $sql = "SELECT * FROM ($sql2) as tb WHERE 1".($filter ? (string)$filter :'');
        if($total)
            return count(Db::getInstance()->executeS($sql));
        else
        {
            $sql .= ($order_by ? ' ORDER By '.pSQL($order_by):'');
            $sql .= ' LIMIT '.(int)$start.','.(int)$limit;
            return Db::getInstance()->executeS($sql);
        }
    }
 }