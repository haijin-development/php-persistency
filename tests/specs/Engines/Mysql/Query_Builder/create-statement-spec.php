<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;

$spec->describe( "When evaluating a create statement in a MySql database", function() {

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

    $this->it( "gets the created id", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "Homer" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $id = $this->database->get_last_created_id();

        $this->expect( $id ) ->to() ->equal( 4 );

    });

    $this->it( "creates a record with constant values", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "Homer" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 4,
                "name" => "Homer",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a function", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->concat( "Ho", "mer" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 4,
                "name" => "Homer",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a nested function", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->concat( "Ho", $query->lower( "MER" ) ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 4,
                "name" => "Homer",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a unary function", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->lower( "HOMER" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 4,
                "name" => "homer",
                "last_name" => "Simpson"
            ],
        ]);

    });


    $this->it( "creates a record with a binary operator", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( 3 ) ->op( "+" ) ->value( 4 ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 4,
                "name" => "7",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a brackets", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->brackets(
                        $query->value( 3 ) ->op( "+" ) ->value( 4 )
                    )
                ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 4,
                "name" => "7",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a null value", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( null ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 4,
                "name" => null,
                "last_name" => "Simpson"
            ],
        ]);

    });

});