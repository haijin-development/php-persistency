<?php

use Haijin\Persistency\Migrations\Database_CLI;
use Haijin\Errors\Haijin_Error;
use Haijin\Persistency\Errors\Connections\Database_Query_Error;

$spec->describe( "When running migrations in postgresql", function() {

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

        $cli = new Database_CLI();

        $migrations = $cli->get_migrations_builder();

        require  "tests/samples/migrations/postgresql/one-postgresql-migration-config.php";

        $cli->migrate_command();

    });

    $this->it( "runs the migrations the second time", function() {

        $cli = new Database_CLI();

        $migrations = $cli->get_migrations_builder();

        require  "tests/samples/migrations/postgresql/one-postgresql-migration-config.php";

        $cli->migrate_command();

        $cli->migrate_command();
    });

    $this->it( "raises an error if a migration id is repeated", function() {

        $cli = new Database_CLI();

        $migrations = $cli->get_migrations_builder();

        require  "tests/samples/migrations/postgresql/repeated-migration-ids-config.php";

        $this->expect( function() use($cli) {

            $cli->migrate_command();

        }) ->to() ->raise(
            Haijin_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->match(
                    "/^The migration in file ['].+[\/]migrations[\/]postgresql[\/]repeated[-]migration[-]ids[-]config[\/]02[-]create[-]products[.]php['] has a repeated unique id[:] [']1['][.]$/"
                );
            }

        );

    });

    $this->it( "raises an error with an invalid migration", function() {

        $cli = new Database_CLI();

        $migrations = $cli->get_migrations_builder();

        require  "tests/samples/migrations/mysql/invalid-migration-config.php";

        $this->expect( function() use($cli) {

            $cli->migrate_command();

        }) ->to() ->raise(
            Database_Query_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->equal(
                    "You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'invalid script' at line 1"
                );
            }

        );

    });

});