<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],
	
	  'Notification' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_Notif', '127.0.0.1'),
            'port' => env('DB_PORT_Notif', '3306'),
            'database' => env('DB_DATABASE_Notif', 'forge'),
            'username' => env('DB_USERNAME_Notif', 'forge'),
            'password' => env('DB_PASSWORD_Notif', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
	
	'oraPRODh' => [
	    'driver' => 'oracle',
	    'host' => env('DB_HOST_PRODh', 'localhost'),
	    'port' => env('DB_PORT_PRODh', '1521'),
	    'database' => env('DB_DATABASE_PRODh', 'xe'),
	    'username' => env('DB_USERNAME_PRODh', 'system'),
	    'password' => env('DB_PASSWORD_PRODh', 'oracle'),
	    'service_name' => env('DB_SERVICE_NAME', ''),
	    'charset' => 'AL32UTF8',
	    'prefix' => '',
	],
	
	'oraCENh' => [
	    'driver' => 'oracle',
	    'host' => env('DB_HOST_CENh', 'localhost'),
	    'port' => env('DB_PORT_CENh', '1521'),
	    'database' => env('DB_DATABASE_CENh', 'xe'),
	    'username' => env('DB_USERNAME_CENh', 'system'),
	    'password' => env('DB_PASSWORD_CENh', 'oracle'),
	    'service_name' => env('DB_SERVICE_NAME', ''),
	    'charset' => 'AL32UTF8',
	    'prefix' => '',
	],
	
	'oraCENe' => [
	    'driver' => 'oracle',
	    'host' => env('DB_HOST_CENe', 'localhost'),
	    'port' => env('DB_PORT_CENe', '1521'),
	    'database' => env('DB_DATABASE_CENe', 'xe'),
	    'username' => env('DB_USERNAME_CENe', 'system'),
	    'password' => env('DB_PASSWORD_CENe', 'oracle'),
	    'service_name' => env('DB_SERVICE_NAME', ''),
	    'charset' => 'AL32UTF8',
	    'prefix' => '',
	],

    'oraCEBh' => [
	    'driver' => 'oracle',
	    'host' => env('DB_HOST_CEBh', 'localhost'),
	    'port' => env('DB_PORT_CEBh', '1521'),
	    'database' => env('DB_DATABASE_CEBh', 'xe'),
	    'username' => env('DB_USERNAME_CEBh', 'system'),
	    'password' => env('DB_PASSWORD_CEBh', 'oracle'),
	    'service_name' => env('DB_SERVICE_NAME', ''),
	    'charset' => 'AL32UTF8',
	    'prefix' => '',
	],

    'oraTARe' => [
	    'driver' => 'oracle',
	    'host' => env('DB_HOST_TARe', 'localhost'),
	    'port' => env('DB_PORT_TARe', '1521'),
	    'database' => env('DB_DATABASE_TARe', 'xe'),
	    'username' => env('DB_USERNAME_TARe', 'system'),
	    'password' => env('DB_PASSWORD_TARe', 'oracle'),
	    'service_name' => env('DB_SERVICE_NAME', ''),
	    'charset' => 'AL32UTF8',
	    'prefix' => '',
	],
	
	'oraSMBh' => [
	    'driver' => 'oracle',
	    'host' => env('DB_HOST_SMBh', 'localhost'),
	    'port' => env('DB_PORT_SMBh', '1521'),
	    'database' => env('DB_DATABASE_SMBh', 'xe'),
	    'username' => env('DB_USERNAME_SMBh', 'system'),
	    'password' => env('DB_PASSWORD_SMBh', 'oracle'),
	    'service_name' => env('DB_SERVICE_NAME', ''),
	    'charset' => 'AL32UTF8',
	    'prefix' => '',
	],
	
	'oraSMBe' => [
	    'driver' => 'oracle',
	    'host' => env('DB_HOST_SMBe', 'localhost'),
	    'port' => env('DB_PORT_SMBe', '1521'),
	    'database' => env('DB_DATABASE_SMBe', 'xe'),
	    'username' => env('DB_USERNAME_SMBe', 'system'),
	    'password' => env('DB_PASSWORD_SMBe', 'oracle'),
	    'service_name' => env('DB_SERVICE_NAME', ''),
	    'charset' => 'AL32UTF8',
	    'prefix' => '',
	],
	
	'oraTARh' => [
	    'driver' => 'oracle',
	    'host' => env('DB_HOST_TARe', 'localhost'),
	    'port' => env('DB_PORT_TARe', '1521'),
	    'database' => env('DB_DATABASE_TARe', 'xe'),
	    'username' => env('DB_USERNAME_TARe', 'system'),
	    'password' => env('DB_PASSWORD_TARe', 'oracle'),
	    'service_name' => env('DB_SERVICE_NAME', ''),
	    'charset' => 'AL32UTF8',
	    'prefix' => '',
	],
	
	'oraTESTh' => [
	    'driver' => 'oracle',
	    'host' => env('DB_HOST_TESTh', 'localhost'),
	    'port' => env('DB_PORT_TESTh', '1521'),
	    'database' => env('DB_DATABASE_TESTh', 'xe'),
	    'username' => env('DB_USERNAME_TESTh', 'system'),
	    'password' => env('DB_PASSWORD_TESTh', 'oracle'),
	    'service_name' => env('DB_SERVICE_NAME', ''),
	    'charset' => 'AL32UTF8',
	    'prefix' => '',
	],
	
	'oraTESTe' => [
	    'driver' => 'oracle',
	    'host' => env('DB_HOST_TESTe', 'localhost'),
	    'port' => env('DB_PORT_TESTe', '1521'),
	    'database' => env('DB_DATABASE_TESTe', 'xe'),
	    'username' => env('DB_USERNAME_TESTe', 'system'),
	    'password' => env('DB_PASSWORD_TESTe', 'oracle'),
	    'service_name' => env('DB_SERVICE_NAME', ''),
	    'charset' => 'AL32UTF8',
	    'prefix' => '',
	],
	
	'oraPRODe' => [
	    'driver' => 'oracle',
	    'host' => env('DB_HOST_PRODe', 'localhost'),
	    'port' => env('DB_PORT_PRODe', '1521'),
	    'database' => env('DB_DATABASE_PRODe', 'xe'),
	    'username' => env('DB_USERNAME_PRODe', 'system'),
	    'password' => env('DB_PASSWORD_PRODe', 'oracle'),
	    'service_name' => env('DB_SERVICE_NAME', ''),
	    'charset' => 'AL32UTF8',
	    'prefix' => '',
	],
	
	
	'ErosDump' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_EDump', '127.0.0.1'),
            'port' => env('DB_PORT_EDump', '3306'),
            'database' => env('DB_DATABASE_EDump', 'forge'),
            'username' => env('DB_USERNAME_EDump', 'forge'),
            'password' => env('DB_PASSWORD_EDump', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
	
	 'sqlCMS' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_sqlCMS', '127.0.0.1'),
            'port' => env('DB_PORT_sqlCMS', '3306'),
            'database' => env('DB_DATABASE_sqlCMS', 'forge'),
            'username' => env('DB_USERNAME_sqlCMS', 'forge'),
            'password' => env('DB_PASSWORD_sqlCMS', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
	
	 'HRIS' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_HRIS', '127.0.0.1'),
            'port' => env('DB_PORT_HRIS', '3306'),
            'database' => env('DB_DATABASE_HRIS', 'forge'),
            'username' => env('DB_USERNAME_HRIS', 'forge'),
            'password' => env('DB_PASSWORD_HRIS', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],    
	
	 'CMS' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_CMS', '127.0.0.1'),
            'port' => env('DB_PORT_CMS', '3306'),
            'database' => env('DB_DATABASE_CMS', 'forge'),
            'username' => env('DB_USERNAME_CMS', 'forge'),
            'password' => env('DB_PASSWORD_CMS', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

	'Queuing' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_Que', '127.0.0.1'),
            'port' => env('DB_PORT_Que', '3306'),
            'database' => env('DB_DATABASE_Que', 'forge'),
            'username' => env('DB_USERNAME_Que', 'forge'),
            'password' => env('DB_PASSWORD_Que', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
	
	'DnCMS' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_DnCMS', '127.0.0.1'),
            'port' => env('DB_PORT_DnCMS', '3306'),
            'database' => env('DB_DATABASE_DnCMS', 'forge'),
            'username' => env('DB_USERNAME_DnCMS', 'forge'),
            'password' => env('DB_PASSWORD_DnCMS', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
	
	'Eros' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_2', '127.0.0.1'),
            'port' => env('DB_PORT_2', '3306'),
            'database' => env('DB_DATABASE_2', 'forge'),
            'username' => env('DB_USERNAME_2', 'forge'),
            'password' => env('DB_PASSWORD_2', ''),
            'unix_socket' => env('DB_SOCKET_2', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
	
	'hclab' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_3', '127.0.0.1'),
            'port' => env('DB_PORT_3', '3306'),
            'database' => env('DB_DATABASE_3', 'forge'),
            'username' => env('DB_USERNAME_3', 'forge'),
            'password' => env('DB_PASSWORD_3', ''),
            'unix_socket' => env('DB_SOCKET_3', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
	
	'Audit' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_Audit', '127.0.0.1'),
            'port' => env('DB_PORT_Audit', '3306'),
            'database' => env('DB_DATABASE_Audit', 'forge'),
            'username' => env('DB_USERNAME_Audit', 'forge'),
            'password' => env('DB_PASSWORD_Audit', ''),
            'unix_socket' => env('DB_SOCKET_Audit', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],    
	
	'Zennya' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_Zennya', '127.0.0.1'),
            'port' => env('DB_PORT_Zennya', '3306'),
            'database' => env('DB_DATABASE_Zennya', 'forge'),
            'username' => env('DB_USERNAME_Zennya', 'forge'),
            'password' => env('DB_PASSWORD_Zennya', ''),
            'unix_socket' => env('DB_SOCKET_Zennya', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'BizBox' => [
            'driver' => 'sqlsrv',
	   // 'odbc_driver'   => '{ODBC Driver 13 for SQL Server}',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_BZ', 'localhost'),
            'port' => env('DB_PORT_BZ', '1433'),
            'database' => env('DB_DATABASE_BZ', 'forge'),
            'username' => env('DB_USERNAME_BZ', 'forge'),
            'password' => env('DB_PASSWORD_BZ', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],
	
	  'WWW' => [
		/*
		   'driver'    => 'mysql',
		   'host'      => env('TUNNELER_LOCAL_ADDRESS'),
		    'port'      => env('TUNNELER_LOCAL_PORT'),
		    'database'  => env('DB_DATABASE'),
		    'username'  => env('DB_USERNAME'),
		    'password'  => env('DB_PASSWORD'),
		    'charset'   => env('DB_CHARSET', 'utf8'),
		    'collation' => env('DB_COLLATION', 'utf8_unicode_ci'),
		    'prefix'    => env('DB_PREFIX', ''),
		    'timezone'  => env('DB_TIMEZONE', '+00:00'),
		    'strict'    => env('DB_STRICT_MODE', false),
	  */
	  
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
             'host'      => env('TUNNELER_LOCAL_ADDRESS'),
            'port'      => env('TUNNELER_LOCAL_PORT'),
             'database'  => env('DB_DATABASE_WEB'),
		    'username'  => env('DB_USERNAME_WEB'),
		    'password'  => env('DB_PASSWORD_WEB'),
            //'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
           'strict'    => env('DB_STRICT_MODE', false),
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
