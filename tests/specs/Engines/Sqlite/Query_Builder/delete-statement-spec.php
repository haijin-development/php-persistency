<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;

$spec->describe( "When evaluating a delete statement in a MySql database", function() {

    $this->before_each( function() {

        $this->re_populate_sqlite_tables();

    });

    $this->after_all( function() {

        $this->re_populate_sqlite_tables();

    });

    $this->let( "database", function() {

        $database = new Sqlite_Database();

        $database->connect( $this->sqlite_file );

        return $database;

    });

    $this->it( "deletes a record with constant values", function() {

        $this->database->delete( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Maggie" )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
            [
                "id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "deletes many records", function() {

        $this->database->delete( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "id" ) ->op( ">" ) ->value( "1" )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

});