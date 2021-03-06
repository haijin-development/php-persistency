<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;

$spec->describe( "When stablishing a connection to a Mysql database", function() {

    $this->it( "raises an error when the connection fails", function() {

        $database = new Sqlite_Database();

        $this->expect( function() use($database) {

            $database->connect( "inexistent-database.db", SQLITE3_OPEN_READWRITE );

        }) ->to() ->raise(
            \Haijin\Persistency\Errors\Connections\Connection_Failure_Error::class,
            function($error) use($database) {

                $this->expect( $error->getMessage() ) ->to() ->equal(
                    "Unable to open database: unable to open database file"
                );

                $this->expect( $error->get_database() ) ->to() ->be( "===" ) ->than( $database );

            }

        );

    });

    $this->it( "raises an error when the connection is not initialized", function() {

        $database = new Sqlite_Database();

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