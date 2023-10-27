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
class Ets_mp_contact_message extends ObjectModel
{
    public $id_contact;
    public $id_customer;
    public $id_seller;
    public $id_manager;
    public $id_employee;
    public $title;
    public $message;  
    public $read;
    public $customer_read;
    public $attachment; 
    public $attachment_name;
    public $date_add;
    public static $definition = array(
		'table' => 'ets_mp_seller_contact_message',
		'primary' => 'id_message',
		'multilang' => false,
		'fields' => array(
            'id_contact' => array('type' => self::TYPE_INT),
			'id_customer' => array('type' => self::TYPE_INT),
            'id_seller' => array('type' => self::TYPE_INT),
            'id_manager' => array('type' => self::TYPE_INT),
            'id_employee' => array('type'=>self::TYPE_INT),
            'title' => array('type'=>self::TYPE_STRING),
            'message' => array('type'=>self::TYPE_STRING),
            'attachment' => array('type'=>self::TYPE_STRING),
            'attachment_name' => array('type'=>self::TYPE_STRING),
            'read' => array('type'=> self::TYPE_INT),
            'customer_read' =>array('type'=> self::TYPE_INT),
            'date_add' => array('type'=> self::TYPE_DATE)
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public function delete()
    {
        if(parent::delete())
        {
            if($this->attachment && file_exists(_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_attachment/'.$this->attachment))
                @unlink(_PS_ETS_MARKETPLACE_UPLOAD_DIR_.'mp_attachment/'.$this->attachment);
                
        }
    }
    public static function getCustomerMessagesOrder($id_customer, $id_order,$limit=2) {
        $sql = 'SELECT cm.*, c.`firstname` AS cfirstname, c.`lastname` AS clastname,
                e.`firstname` AS efirstname, e.`lastname` AS elastname
			FROM `' . _DB_PREFIX_ . 'customer_thread` ct
			LEFT JOIN `' . _DB_PREFIX_ . 'customer_message` cm
				ON ct.id_customer_thread = cm.id_customer_thread
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c 
                ON ct.`id_customer` = c.`id_customer`
            LEFT OUTER JOIN `' . _DB_PREFIX_ . 'employee` e 
                ON e.`id_employee` = cm.`id_employee`
			WHERE ct.id_customer = ' . (int) $id_customer .
            ' AND ct.`id_order` = ' . (int) $id_order . '
            GROUP BY cm.id_customer_message
		 	ORDER BY cm.date_add DESC'.($limit ? ' LIMIT '.(int)$limit :'');
        $messages = Db::getInstance()->executeS($sql);
        if($messages)
        {
            foreach($messages as &$message)
            {
                $sql = 'SELECT *,CONCAT(customer.firstname," ",customer.lastname) as seller_name,CONCAT(manager.firstname," ",manager.lastname) as manager_name FROM `'._DB_PREFIX_.'ets_mp_seller_customer_message` scm
                INNER JOIN `'._DB_PREFIX_.'customer` customer ON (customer.id_customer=scm.id_customer)
                INNER JOIN `'._DB_PREFIX_.'ets_mp_seller` s ON (s.id_customer=customer.id_customer)
                LEFT JOIN `'._DB_PREFIX_.'customer` manager ON (manager.id_customer= scm.id_manager)
                WHERE scm.id_customer_message = "'.(int)$message['id_customer_message'].'"';
                if($seller = Db::getInstance()->getRow($sql))
                {
                    if($seller['manager_name'])
                    {
                        $message['efirstname'] = $seller['manager_name'];
                        $message['elastname']='(Seller manager)';
                    }
                    else
                    {
                        $message['efirstname'] = $seller['seller_name'];
                        $message['elastname']='(Seller)';
                    }

                }
            }
        }
        return $messages;
    }
    public static function updateReadByIDCustomerThread($id_customer_thread)
    {
        return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customer_message` SET `read`=1 WHERE id_customer_thread ="'.(int)$id_customer_thread.'"');
    }
    public static function updateReadByIDContact($id_contact)
    {
        return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_mp_seller_contact_message` SET `read`=1 WHERE id_contact="'.(int)$id_contact.'"');
    }
    public static function addMessageToseller($id_seller_customer,$id_customer_message)
    {
        return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_mp_seller_customer_message`(id_customer,id_manager,id_customer_message) VALUES("'.(int)$id_seller_customer.'","'.(Context::getContext()->customer->id!= $id_seller_customer ? Context::getContext()->customer->id:0).'","'.(int)$id_customer_message.'")');
    }
 }