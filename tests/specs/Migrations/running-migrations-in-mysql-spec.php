<?php

use Haijin\Persistency\Migrations\Database_CLI;

$spec->describe( "When running migrations in mysql", function() {

    $this->before_each( function() {

        $this->drop_mysql_tables();
        $this->drop_postgresql_tables();
        $this->drop_sqlite_tables();
        $this->drop_elasticsearch_indices();

    });

    $this->it( "runs the migrations the first time", function() {

        $argv = [ '', 'migrate' ];

        $cli = new Database_CLI( $argv );

        $migrations = $cli->get_migrations_builder();

        require  __DIR__ . "/../../samples/migrations/one-mysql-migration-config.php";

        $cli->evaluate();

    });

    $this->it( "runs the migrations the seconds time", function() {

        $argv = [ '', 'migrate' ];

        $cli = new Database_CLI( $argv );

        $migrations = $cli->get_migrations_builder();

        require  __DIR__ . "/../../samples/migrations/one-mysql-migration-config.php";

        $cli->evaluate();


        $argv = [ '', 'migrate' ];

        $cli = new Database_CLI( $argv );

        $migrations = $cli->get_migrations_builder();

        require  __DIR__ . "/../../samples/migrations/many-mysql-migrations-config.php";

        $cli->evaluate();
    });

    $this->it( "raises an error if a migration id is repeated", function() {

        $argv = [ '', 'migrate' ];

        $cli = new Database_CLI( $argv );

        $migrations = $cli->get_migrations_builder();

        require  __DIR__ . "/../../samples/migrations/repeated-migration-ids-config.php";

        $this->expect( function() use($cli) {

            $cli->evaluate();

        }) ->to() ->raise(
            \RuntimeException::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->match(
                    "/^The migration in file ['].+[\/]migrations[\/]repeated[-]migration[-]ids[-]config[\/]02[-]create[-]products[.]php['] has a repeated unique id[:] [']1['][.]$/"
                );
            }

        );

    });

});