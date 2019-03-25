<?php

use Haijin\Persistency\Migrations\Database_CLI;

$spec->describe( "When dropping migrations", function() {

    $this->before_all( function() {
        ob_start();
    });

    $this->after_all( function() {
        ob_get_clean();
    });

    $this->it( "drops the migrations with no tables", function() {

        $this->drop_mysql_tables();
        $this->drop_postgresql_tables();
        $this->drop_sqlite_tables();
        $this->drop_elasticsearch_indices();

        $cli = new Database_CLI();

        $migrations = $cli->get_migrations_builder();

        require "tests/samples/migrations/mysql/no-migrations-config.php";

        $cli->drop_command();

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

        $cli = new Database_CLI();

        $migrations = $cli->get_migrations_builder();

        require  "tests/samples/migrations/mysql/no-migrations-config.php";

        $cli->drop_command();

    });

});