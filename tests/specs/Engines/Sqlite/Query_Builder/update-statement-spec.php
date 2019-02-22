<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;

$spec->describe( "When evaluating an update statement in a SQlite database", function() {

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

    $this->it( "updates a record with constant values", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "Marjorie" ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

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
            ],
            [
                "id" => 3,
                "name" => "Marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates a record with a function", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->lower( "Marjorie" ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

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
            ],
            [
                "id" => 3,
                "name" => "marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates a record with a nested function", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->lower( $query->upper( "Marjorie" ) ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

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
            ],
            [
                "id" => 3,
                "name" => "marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates a record with a unary function", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "MARJORIE" )->lower() ),
                $query->set( "last_name", $query->value( "Simpson" )->lower() )
            );

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
            ],
            [
                "id" => 3,
                "name" => "marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });


    $this->it( "updates a record with a binary operator", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( 3 ) ->op( "+" ) ->value( 4 ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

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
            ],
            [
                "id" => 3,
                "name" => 7,
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates a record with a brackets", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->brackets(
                        $query->value( 3 ) ->op( "+" ) ->value( 4 )
                    )
                ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

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
            ],
            [
                "id" => 3,
                "name" => 7,
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates a record with a null value", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( null ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

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
            ],
            [
                "id" => 3,
                "name" => null,
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates many records", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( null ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

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
            ],
            [
                "id" => 2,
                "name" => null,
                "last_name" => "simpson"
            ],
            [
                "id" => 3,
                "name" => null,
                "last_name" => "simpson"
            ],
        ]);

    });

});