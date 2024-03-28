<?php
include_once __DIR__ . "/../../../config/config.inc.php";

$_DB_PREFIX_ = 'wa_'; // _DB_PREFIX_;
$_DB_NAME_ = 'wasabi'; // _DB_NAME_;
$sql = [];
$exists = Db::getInstance()->getValue(<<<SQL
SELECT 1
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = '{$_DB_PREFIX_}customer'
AND table_schema = '$_DB_NAME_'
AND column_name = 'developer_status'
SQL);
if (! $exists) {
    $sql[] = <<<SQL
      ALTER TABLE `{$_DB_PREFIX_}customer` ADD `developer_status` varchar(255);
    SQL;
}

foreach ($sql as $query) {
    Db::getInstance()->execute($query);
}
