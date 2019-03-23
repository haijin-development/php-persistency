<?php

use Haijin\Persistency\Migrations\Database_CLI;
use Haijin\Errors\Haijin_Error;

$spec->describe( "When running migrations in mysql", function() {

    $this->before_all( function() {
        ob_start();
    });

    $this->before_each( function() {

        $this->drop_mysql_tables();
        $this->drop_postgresql_tables();
        $this->drop_sqlite_tables();
        $this->drop_elasticsearch_indices();

    });

    $this->after_all( function() {
        ob_get_clean();

        $this->setup_mysql();
        $this->setup_postgresql();
        $this->setup_sqlite();
        $this->setup_elasticsearch();

    });

    $this->it( "runs the migrations the first time", function() {

        $argv = [ '', 'migrate' ];

        $cli = new Database_CLI( $argv );

        $migrations = $cli->get_migrations_builder();

        require  "tests/samples/migrations/mysql/one-mysql-migration-config.php";

        $cli->evaluate();

    });

    $this->it( "runs the migrations the second time", function() {

        $argv = [ '', 'migrate' ];

        $cli = new Database_CLI( $argv );

        $migrations = $cli->get_migrations_builder();

        require  "tests/samples/migrations/mysql/one-mysql-migration-config.php";

        $cli->evaluate();


        $argv = [ '', 'migrate' ];

        $cli = new Database_CLI( $argv );

        $migrations = $cli->get_migrations_builder();

        require  "tests/samples/migrations/mysql/many-mysql-migrations-config.php";

        $cli->evaluate();
    });

    $this->it( "raises an error if a migration id is repeated", function() {

        $argv = [ '', 'migrate' ];

        $cli = new Database_CLI( $argv );

        $migrations = $cli->get_migrations_builder();

        require  "tests/samples/migrations/mysql/repeated-migration-ids-config.php";

        $this->expect( function() use($cli) {

            $cli->evaluate();

        }) ->to() ->raise(
            Haijin_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->match(
                    "/^The migration in file ['].+[\/]migrations[\/]mysql[\/]repeated[-]migration[-]ids[-]config[\/]02[-]create[-]products[.]php['] has a repeated unique id[:] [']1['][.]$/"
                );
            }

        );

    });

});