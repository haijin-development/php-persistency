<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;

$spec->describe( "When evaluating a create statement in a Sqlite database", function() {

    $this->before_each( function() {

        $this->setup_sqlite();

    });

    $this->after_all( function() {

        $this->setup_sqlite();

    });

    $this->let( "database", function() {

        $database = new Sqlite_Database();

        $database->connect( $this->sqlite_file );

        return $database;

    });

    $this->it( "returns the created id", function() {

        $this->database->create( function($query) {

            $query->collection( "users_with_sequence" );

            $query->record(
                $query->set( "name", $query->value( "Lisa" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $id = $this->database->get_last_created_id();

        $this->expect( $id ) ->to() ->equal( 1 );

    });

    $this->it( "creates a record with constant values", function() {

        $this->database->create( function($query) {

            $query->collection( "users_with_sequence" );

            $query->record(
                $query->set( "name", $query->value( "Lisa" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users_with_sequence" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a function", function() {

        $this->database->create( function($query) {

            $query->collection( "users_with_sequence" );

            $query->record(
                $query->set( "name", $query->upper( "lisa" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users_with_sequence" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "LISA",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a nested function", function() {

        $this->database->create( function($query) {

            $query->collection( "users_with_sequence" );

            $query->record(
                $query->set( "name", $query->upper( $query->lower( "LiSa" ) ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users_with_sequence" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "LISA",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a unary function", function() {

        $this->database->create( function($query) {

            $query->collection( "users_with_sequence" );

            $query->record(
                $query->set( "name", $query->lower( "LISA" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users_with_sequence" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "lisa",
                "last_name" => "Simpson"
            ],
        ]);

    });


    $this->it( "creates a record with a binary operator", function() {

        $this->database->create( function($query) {

            $query->collection( "users_with_sequence" );

            $query->record(
                $query->set( "name", $query->value( 3 ) ->op( "+" ) ->value( 4 ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users_with_sequence" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "7",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a brackets", function() {

        $this->database->create( function($query) {

            $query->collection( "users_with_sequence" );

            $query->record(
                $query->set( "name", $query->brackets(
                        $query->value( 3 ) ->op( "+" ) ->value( 4 )
                    )
                ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users_with_sequence" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "7",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a null value", function() {

        $this->database->create( function($query) {

            $query->collection( "users_with_sequence" );

            $query->record(
                $query->set( "name", $query->value( null ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users_with_sequence" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => null,
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with named parameters", function() {

        $this->database->create( function($query) {

            $query->collection( "users_with_sequence" );

            $query->record(
                $query->set( "name", $query->param( "name" ) ),
                $query->set( "last_name", $query->param( "last_name" ) )
            );

        }, [
            "name" => "Homer",
            "last_name" => "Simpson"
        ]);

        $rows = $this->database->query( function($query) {

            $query->collection( "users_with_sequence" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Homer",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates records with a compiled statement", function() {

        $compiled_statement = $this->database->compile_create_statement( function($query) {

            $query->collection( "users_with_sequence" );

            $query->record(
                $query->set( "name", $query->param( "name" ) ),
                $query->set( "last_name", $query->param( "last_name" ) )
            );

        });

        $this->database->execute( $compiled_statement, [
            "name" => "Homer",
            "last_name" => "Simpson"
        ]);


        $this->database->execute( $compiled_statement, [
            "name" => "Marge",
            "last_name" => "Simpson"
        ]);

        $rows = $this->database->query( function($query) {

            $query->collection( "users_with_sequence" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 2,
                "name" => "Marge",
                "last_name" => "Simpson"
            ],
            [
                "id" => 1,
                "name" => "Homer",
                "last_name" => "Simpson"
            ],
        ]);

    });

});