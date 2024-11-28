<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'sqlite'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        //primary/main database "usep-tbc"
        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

        // Obrero Campus Database Connection
        'es_obrero' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_ES_OBRERO', '172.16.210.4'),
            'port' => env('DB_PORT_ES_OBRERO', '1433'),
            'database' => env('DB_DATABASE_ES_OBRERO', 'Princetech_Solutions'),
            'username' => env('DB_USERNAME_ES_OBRERO', 'useptextblast'),
            'password' => env('DB_PASSWORD_ES_OBRERO', 'US3Pt3xtb@st'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            // Uncomment and configure if needed for encryption
            // 'encrypt' => env('DB_ENCRYPT_ES_OBRERO', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE_ES_OBRERO', 'false'),
        ],

        // Mintal Campus Database Connection
        'es_mintal' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_ES_MINTAL', '10.10.11.1'),
            'port' => env('DB_PORT_ES_MINTAL', '1433'),
            'database' => env('DB_DATABASE_ES_MINTAL', 'es_mintal'),
            'username' => env('DB_USERNAME_ES_MINTAL', 'useptextblast'),
            'password' => env('DB_PASSWORD_ES_MINTAL', 'US3Pt3xtb@st'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            // Uncomment and configure if needed for encryption
            // 'encrypt' => env('DB_ENCRYPT_ES_MINTAL', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE_ES_MINTAL', 'false'),
        ],

        // Tagum Campus Database Connection
        'es_tagum' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_ES_TAGUM', '10.10.10.1'),
            'port' => env('DB_PORT_ES_TAGUM', '1433'),
            'database' => env('DB_DATABASE_ES_TAGUM', 'es_tagum'),
            'username' => env('DB_USERNAME_ES_TAGUM', 'useptextblast'),
            'password' => env('DB_PASSWORD_ES_TAGUM', 'US3Pt3xtb@st'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            // Uncomment and configure if needed for encryption
            // 'encrypt' => env('DB_ENCRYPT_ES_TAGUM', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE_ES_TAGUM', 'false'),
        ],

        // Mabini Campus Database Connection
        'es_mabini' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST_ES_MABINI', '10.10.12.1'),
            'port' => env('DB_PORT_ES_MABINI', '1433'),
            'database' => env('DB_DATABASE_ES_MABINI', 'es_mabini'),
            'username' => env('DB_USERNAME_ES_MABINI', 'useptextblast'),
            'password' => env('DB_PASSWORD_ES_MABINI', 'US3Pt3xtb@st'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            // Uncomment and configure if needed for encryption
            // 'encrypt' => env('DB_ENCRYPT_ES_MABINI', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE_ES_MABINI', 'false'),
        ],
        
        'mysql_hris' => [
            'driver' => 'mysql',
            'host' => '172.16.210.15',
            'port' => '3306',
            'database' => 'hris',
            'username' => 'useptextblast',
            'password' => 'US3Pt3xtb@st',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
