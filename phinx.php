<?php
$parameters = require(__DIR__ . '/app/config/parameters.php');
$parameters = $parameters['parameters'];
$db = [
    'adapter' => 'mysql',
    'host' => $parameters['database_host'],
    'name' => $parameters['database_name'],
    'user' => $parameters['database_user'],
    'pass' => $parameters['database_password'],
    'port' => $parameters['database_port'],
    'charset' => 'utf8',
];
return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => $db,
        'development' => $db,
    ],
    'version_order' => 'creation'
];
