<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;
use Haijin\Persistency\Engines\Mysql\Mysql_Database;
use Haijin\Persistency\Engines\Postgresql\Postgresql_Database;
use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;

$migrations->configure( function($migrations) {

    $mysql_database = ( new Mysql_Database() )
        ->connect( '127.0.0.1', 'haijin', '123456', 'haijin-persistency' );

    $elastic_search_database = ( new Elasticsearch_Database() )
        ->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

    $postgressql_database = ( new Postgresql_Database() )
        ->connect(
            'host=localhost port=5432 dbname=haijin-persistency user=haijin password=123456'
        );

    $sqlite_database = ( new Sqlite_Database() )
        ->connect( 'test.db' );


    $migrations->database = $mysql_database;

    $migrations->table_name = 'app_migrations';

    $migrations->migrated_databases = [
        $mysql_database,
        $postgressql_database,
        $sqlite_database,
        $elastic_search_database
    ];

    $migrations->folder = __DIR__ . '/one-mysql-migration/';

});