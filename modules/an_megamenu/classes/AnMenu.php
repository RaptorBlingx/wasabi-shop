<?php
/**
 * 2007-2017 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class AnMenu extends ObjectModel
{
    /**
     * @var
     */
    public $id;
    /**
     * @var
     */
    public $id_anmenu;
    /**
     * @var int
     */
    public $id_shop;
    /**
     * @var
     */
    public $name;
    /**
     * @var int
     */
    public $active = 1;
    /**
     * @var int
     */
    public $position;
    /**
     * @var
     */
    public $link;
    /**
     * @var
     */
    public $label;
    /**
     * @var string
     */
    public $label_color = '#17cf00';
    /**
     * @var int
     */
    public $drop_column = 0;
    /**
     * @var string
     */
    public $drop_bgcolor = '#ffffff';
    /**
     * @var
     */
    public $drop_bgimage;
    /**
     * @var string
     */
    public $bgimage_position = 'right bottom';
    /**
     * @var int
     */
    public $position_x = 0;
    /**
     * @var int
     */
    public $position_y = 0;

    /**
     * @var array
     */
    public static $definition = array(
        'table' => 'anmenu',
        'primary' => 'id_anmenu',
        'multilang' => true,
        'multilang_shop' => false,
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'label_color' => array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 32),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'position' => array('type' => self::TYPE_INT),
            'drop_column' => array('type' => self::TYPE_INT),
            'drop_bgcolor' => array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 32),
            'drop_bgimage' => array('type' => self::TYPE_STRING, 'size' => 128),
            'bgimage_position' => array('type' => self::TYPE_STRING, 'size' => 50),
            'position_x' => array('type' => self::TYPE_INT),
            'position_y' => array('type' => self::TYPE_INT),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 254),
            'link' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isUrlOrEmpty', 'size' => 254),
            'label' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
        ),
    );

    /**
     * AnMenu constructor.
     * @param null $id_anmenu
     * @param null $id_lang
     */
    public function __construct($id_anmenu = null, $id_lang = null)
    {
        parent::__construct($id_anmenu, $id_lang);

        if (!$this->id_shop) {
            $this->id_shop = Context::getContext()->shop->id;
        }

        if (!$this->position) {
            $this->position = 1 + $this->getMaxPosition();
        }

        if (!$id_anmenu && Context::getContext()->language->is_rtl) {
            $this->bgimage_position = 'left bottom';
        }
    }

    /**
     * @param $id_lang
     * @param bool $active
     * @return mixed
     */
    public static function getList($id_lang, $active = true)
    {
        $id_lang = is_null($id_lang) ? Context::getContext()->language->id : (int)$id_lang;
        $id_shop = Context::getContext()->shop->id;

        $query = 'SELECT *
            FROM `' . _DB_PREFIX_ . 'anmenu` m
            LEFT JOIN `' . _DB_PREFIX_ . 'anmenu_lang` ml ON m.`id_anmenu` = ml.`id_anmenu`
            WHERE m.`id_shop` = ' . (int)$id_shop . '
            AND `id_lang` = ' . (int)$id_lang . '
            ' . ($active ? 'AND `active` = 1' : '') . '
            GROUP BY m.`id_anmenu`
            ORDER BY m.`position` ASC';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return $result;
    }

    /**
     * @return int
     */
    public function getMaxPosition()
    {
        $id_shop = Context::getContext()->shop->id;
        $query = 'SELECT MAX(m.`position`)
            FROM `' . _DB_PREFIX_ . 'anmenu` m
            WHERE m.`id_shop` = ' . (int)$id_shop;

        return (int)Db::getInstance()->getValue($query);
    }

    /**
     * @param $id_anmenu
     * @param $position
     */
    public static function updatePosition($id_anmenu, $position)
    {
        $query = 'UPDATE `' . _DB_PREFIX_ . 'anmenu`
			SET `position` = ' . (int)$position . '
			WHERE `id_anmenu` = ' . (int)$id_anmenu;

        Db::getInstance()->execute($query);
    }
}
