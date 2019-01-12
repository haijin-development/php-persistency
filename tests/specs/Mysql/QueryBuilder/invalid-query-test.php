<?php

use Haijin\Persistency\Mysql\MysqlDatabase;

$spec->describe( "When building an invalid Mysql query", function() {

    $this->let( "database", function() {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        return $database;
    });

    $this->it( "raises an error when the query has an error", function() {

        $this->expect( function() {

            $this->database->query( function($query) {
                $query->collection( "non_existing_table" );
            });

        }) ->to() ->raise(
            \Haijin\Persistency\Errors\Connections\DatabaseQueryError::class,
            function($error) {

                $this->expect( $error->getMessage() ) ->to() ->equal(
                    "Table 'haijin-persistency.non_existing_table' doesn't exist"
                );

                $this->expect( $error->get_database() ) ->to() ->be( "===" )
                    ->than( $this->database );
        });
    });

});