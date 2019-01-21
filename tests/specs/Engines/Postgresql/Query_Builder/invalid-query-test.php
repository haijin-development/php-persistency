<?php

use Haijin\Persistency\Engines\Postgresql\Postgresql_Database;

$spec->describe( "When building an invalid Postgresql query", function() {

    $this->let( "database", function() {

        $database = new Postgresql_Database();

        $database->connect(
            "host=localhost port=5432 dbname=haijin-persistency user=haijin password=123456"
        );

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

                $expected_message = "ERROR:  relation \"non_existing_table\" does not exist
LINE 1: select non_existing_table.* from non_existing_table;
                                         ^";

                $this->expect( $error->getMessage() ) ->to() ->equal( $expected_message );

                $this->expect( $error->get_database() ) ->to() ->be( "===" )
                    ->than( $this->database );
        });
    });

});