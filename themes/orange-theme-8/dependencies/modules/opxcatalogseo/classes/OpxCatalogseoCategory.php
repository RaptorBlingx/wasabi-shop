<?php

class OpxCatalogseoCategory extends ObjectModel
{
  /** @var int */
  public $id_opxcatalogseo_category;

  /** @var int */
  public $id_category;

  /** @var string */
  public $h1;

  public $date_add;
  public $date_upd;

  public static $definition = [
    'table' => 'opxcatalogseo_category',
    'primary' => 'id_opxcatalogseo_category',
    'multilang' => true,
    'fields' => array(
      'id_category'  => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
      'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
      'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],

      // Language fields
      'h1' => [
        'type' => self::TYPE_STRING,
        'lang' => true,
        'validate' => 'isGenericName',
        'size' => 180
      ],
    )
  ];

  /**
   * @param integer $id_category
   * @return OpxCatalogseoCategory|null
   */
  public static function findByIdCategory(int $id_category)
  {
    $primary = self::$definition['primary'];
    $table = _DB_PREFIX_.self::$definition['table'];
    $id = Db::getInstance()->getValue("SELECT `$primary` FROM `$table` WHERE `id_category` = $id_category");
    return $id ? new self($id) : null;
  }
}
