<?php

use Haijin\Persistency\Engines\Postgresql\Postgresql_Database;

$spec->describe( "When stablishing a connection to a Postgresql database", function() {

    $this->before_all( function() {

        $this->error_reporting = error_reporting();

        error_reporting( E_ERROR | E_PARSE | E_NOTICE );

    });

    $this->after_all( function() {

        error_reporting( $this->error_reporting );

    });

    $this->it( "raises an error when the connection fails", function() {

        $database = new Postgresql_Database();

        $this->expect( function() use($database) {

            $database->connect(
                "host=localhost port=5432 dbname=haijin-persistency user=haijin password=123"
            );

        }) ->to() ->raise(
            \Haijin\Persistency\Errors\Connections\Connection_Failure_Error::class,
            function($error) use($database) {

                $this->expect( $error->getMessage() ) ->to() ->equal(
                    "Connection failed."
                );

                $this->expect( $error->get_database() ) ->to() ->be( "===" ) ->than( $database );

            }

        );

    });

    $this->it( "raises an error when the connection is not initialized", function() {

        $database = new Postgresql_Database();

        $this->expect( function() use($database) {

            $database->query( function($query) {
                $query->collection( "users_read_only" );
            });

        }) ->to() ->raise(
            \Haijin\Persistency\Errors\Connections\Uninitialized_Connection_Error::class,
            function($error) use($database) {

                $this->expect( $error->getMessage() ) ->to() ->equal(
                    'The connection handle has not being initialized. Initialize it with \'->connect($hostname, $user, $password, $database)\' first.'
                );

                $this->expect( $error->get_database() ) ->to() ->be( "===" ) ->than( $database );

            }

        );

    });

});