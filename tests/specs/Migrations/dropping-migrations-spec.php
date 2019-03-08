<?php

use Haijin\Persistency\Migrations\Database_CLI;

$spec->describe( "When dropping migrations", function() {

    $this->it( "drops the migrations with no tables", function() {

        $this->drop_mysql_tables();
        $this->drop_postgresql_tables();
        $this->drop_sqlite_tables();
        $this->drop_elasticsearch_indices();

        $argv = [ '', 'drop' ];

        $cli = new Database_CLI( $argv );

        $cli->config_in_file(
            __DIR__ . "/../../samples/migrations/no-migrations-config.php"
        );

        $cli->evaluate();

    });

    $this->it( "drops the migrations with tables", function() {

        $this->drop_mysql_tables();
        $this->drop_postgresql_tables();
        $this->drop_sqlite_tables();
        $this->drop_elasticsearch_indices();

        $this->create_mysql_tables();
        $this->create_postgresql_tables();
        $this->create_sqlite_tables();
        $this->create_elasticsearch_indices();

        $argv = [ '', 'drop' ];

        $cli = new Database_CLI( $argv );

        $cli->config_in_file(
            __DIR__ . "/../../samples/migrations/no-migrations-config.php"
        );

        $cli->evaluate();

    });

});