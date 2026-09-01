<?php

use function Cake\Core\env;

/*
 * Local configuration file to provide any overrides to your app.php configuration.
 * Copy and save this file as app_local.php and make changes as required.
 * Note: It is not recommended to commit files with credentials such as app_local.php
 * into source code version control.
 */
return [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', '__SALT__'),
    ],

    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * See app.php for more configuration options.
     */
    'Datasources' => [
        /*
         * The application runs on MariaDB — the `db` service in
         * docker-compose.yml, published on the host as 127.0.0.1:3306.
         *
         * These defaults match the development credentials in
         * docker-compose.yml, so `bin/cake` works from the host with the stack
         * up. Inside the container DATABASE_URL is set and overrides them.
         */
        'default' => [
            'host' => '127.0.0.1',
            'port' => '3306',

            'username' => 'jussiflow',
            'password' => 'jussiflow',

            'database' => 'jussiflow',

            'encoding' => 'utf8mb4',
            'timezone' => 'UTC',

            /*
             * You can use a DSN string to set the entire configuration
             */
            'url' => env('DATABASE_URL', null),
        ],

        /*
         * The test connection is used during the test suite.
         *
         * It stays on SQLite on purpose: `composer test` then needs no database
         * server and is safe to run repeatedly. Money is stored as integer
         * cents, so SQLite's loose numeric typing costs nothing here.
         */
        'test' => [
            'url' => env('DATABASE_TEST_URL', 'sqlite://127.0.0.1/tmp/tests.sqlite'),
        ],
    ],

    /*
     * Email configuration.
     *
     * Host and credential configuration in case you are using SmtpTransport
     *
     * See app.php for more configuration options.
     */
    'EmailTransport' => [
        'default' => [
            'host' => 'localhost',
            'port' => 25,
            'username' => null,
            'password' => null,
            'client' => null,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],
];
