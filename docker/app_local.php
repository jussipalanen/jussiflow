<?php
declare(strict_types=1);

/**
 * Container configuration for JussiFlow.
 *
 * The normal config/app_local.php is gitignored and therefore absent from the
 * image, so this env-driven equivalent is copied into place by the entrypoint.
 * Everything here comes from the environment — see docker-compose.yml.
 */

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Sqlite;

use function Cake\Core\env;

return [
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    'Security' => [
        'salt' => env('SECURITY_SALT'),
    ],

    'Datasources' => [
        // The application runs on MariaDB (the `db` service in
        // docker-compose.yml). These keys are the fallback; in practice
        // docker-compose.yml sets DATABASE_URL, and the DSN below wins.
        'default' => [
            'className' => Connection::class,
            'driver' => Mysql::class,
            'host' => env('DB_HOST', 'db'),
            'port' => env('DB_PORT', '3306'),
            'username' => env('DB_USER', 'jussiflow'),
            'password' => env('DB_PASSWORD', 'jussiflow'),
            'database' => env('DB_NAME', 'jussiflow'),
            'encoding' => 'utf8mb4',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
            // A DSN in DATABASE_URL, if set, overrides the keys above.
            'url' => env('DATABASE_URL', null),
        ],

        // Tests stay on SQLite deliberately: `composer test` then needs no
        // database server, and the suite is safe to run repeatedly. Money is
        // stored as integer cents, so SQLite's loose numeric typing costs
        // nothing here.
        'test' => [
            'className' => Connection::class,
            'driver' => Sqlite::class,
            'database' => env('SQLITE_TEST_DATABASE', ROOT . DS . 'tmp' . DS . 'tests.sqlite'),
            'timezone' => 'UTC',
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
            'url' => env('DATABASE_TEST_URL', null),
        ],
    ],

    'EmailTransport' => [
        'default' => [
            'host' => env('EMAIL_HOST', 'localhost'),
            'port' => (int)env('EMAIL_PORT', '25'),
            'username' => env('EMAIL_USERNAME', null),
            'password' => env('EMAIL_PASSWORD', null),
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],
];
