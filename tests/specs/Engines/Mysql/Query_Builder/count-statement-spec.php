<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;

$spec->describe( "When building the count statement of a Mysql expression", function() {

    $this->let( "database", function() {

        $database = new Mysql_Database();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        return $database;

    });

    $this->it( "counts all the records", function() {

        $count = $this->database->count( function($query) {

            $query->collection( "users_read_only" );

        });

        $this->expect( $count ) ->to() ->equal( 3 );

    });

    $this->it( "counts the filtered records", function() {

        $count = $this->database->count( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->field( 'name', '=', 'Lisa' )
            );

        });

        $this->expect( $count ) ->to() ->equal( 1 );

    });

    $this->it( "counts the filtered records", function() {

        $count = $this->database->count( function($query) {

            $query->collection( "users_read_only" );

            $query->proyect(
                $query->field( 'last_name' ) ->distinct()
            );

            $query->filter(
                $query->field( 'name', '=', 'Lisa' )
            );

        });

        $this->expect( $count ) ->to() ->equal( 1 );

    });

});