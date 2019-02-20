<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;

$spec->describe( "When building an invalid Sqlite query", function() {

    $this->before_all( function() {

        $this->error_reporting = error_reporting();

        error_reporting( E_ERROR | E_PARSE | E_NOTICE );

    });

    $this->after_all( function() {

        error_reporting( $this->error_reporting );

    });

    $this->let( "database", function() {

        $database = new Sqlite_Database();

        $database->connect( $this->sqlite_file );

        return $database;

    });

    $this->it( "raises an error when the query has an error", function() {

        $this->expect( function() {

            $this->database->query( function($query) {
                $query->collection( "non_existing_table" );
            });

        }) ->to() ->raise(
            \Haijin\Persistency\Errors\Connections\Database_Query_Error::class,
            function($error) {

                $this->expect( $error->getMessage() ) ->to() ->equal(
                    "no such table: non_existing_table"
                );

                $this->expect( $error->get_database() ) ->to() ->be( "===" )
                    ->than( $this->database );
        });
    });

});