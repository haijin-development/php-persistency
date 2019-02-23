<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;

$spec->describe( "When evaluating an update statement in a MySql database", function() {

    $this->before_each( function() {

        $this->re_populate_mysql_tables();

    });

    $this->after_all( function() {

        $this->re_populate_mysql_tables();

    });

    $this->let( "database", function() {

        $database = new Mysql_Database();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

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
                $query->set( "name", $query->concat( "Mar", "jorie" ) ),
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

    $this->it( "updates a record with a nested function", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->concat( "Mar", $query->lower( "JORIE" ) ) ),
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

    $this->it( "updates a record with named parameters", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->param( "name" ) ),
                $query->set( "last_name", $query->param( "last_name" ) )
            );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Maggie" )
            );

        }, [
            "name" => "Margaret",
            "last_name" => "simpson"
        ]);

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
                "name" => "Margaret",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates a record with compiled statements", function() {

        $compiled_statement = $this->database->compile_update_statement( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->param( "name" ) ),
                $query->set( "last_name", $query->param( "last_name" ) )
            );

            $query->filter(
                $query->field( "id" ) ->op( "=" ) ->value( 3 )
            );

        });

        $this->database->execute( $compiled_statement, [
            "name" => "Margaret",
            "last_name" => "simpson"
        ]);

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
                "name" => "Margaret",
                "last_name" => "simpson"
            ],
        ]);


        $this->database->execute( $compiled_statement, [
            "name" => "margaret",
            "last_name" => "simpson"
        ]);

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
                "name" => "margaret",
                "last_name" => "simpson"
            ],
        ]);

    });

});