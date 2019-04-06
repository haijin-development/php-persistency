<?php

use Haijin\Persistency\Engines\Postgresql\Postgresql_Database;
use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;

$spec->describe( "When querying a Persistent_Collection stored in a Postgresql database", function() {

    $this->before_all( function() {

        $this->database = new Postgresql_Database();

        $this->database->connect(
            "host=localhost port=5432 dbname=haijin-persistency user=haijin password=123456"
        );

        Users_Collection::get()->set_database( $this->database );

        Users_Collection::do()->clear_all();

        Users_Collection::do()->create_from_attributes([
            "name" => "Lisa",
            "last_name" => "Simpson"
        ]);

        Users_Collection::do()->create_from_attributes([
            "name" => "Bart",
            "last_name" => "Simpson"
        ]);

        Users_Collection::do()->create_from_attributes([
            "name" => "Maggie",
            "last_name" => "Simpson"
        ]);

    });

    $this->after_all( function() {

        Users_Collection::do()->clear_all();

    });

    $this->describe( "when getting all the objects with ->all()", function() {

        $this->it( "gets all the objects in the collection", function() {

            $users = Users_Collection::get()->all();

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 1,
                    "get_name()" => "Lisa",
                    "get_last_name()" => "Simpson"
                ],
                [
                    "get_id()" => 2,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson"
                ],
                [
                    "get_id()" => 3,
                    "get_name()" => "Maggie",
                    "get_last_name()" => "Simpson"
                ]
            ]);

        });

        $this->it( "gets all the objects matching a query", function() {

            $users = Users_Collection::get()->all( function($query) {

                $query->filter(
                    $query->field( "id" ) ->op( ">" ) ->value( 1 )
                );

                $query->order_by(
                    $query->field( "id" ) ->desc()
                );

            });

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 3,
                    "get_name()" => "Maggie",
                    "get_last_name()" => "Simpson"
                ],
                [
                    "get_id()" => 2,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson"
                ]
            ]);

        });

        $this->it( "gets an empty collection if no objects matches a query", function() {

            $users = Users_Collection::get()->all( function($query) {

                $query->filter(
                    $query->field( "id" ) ->op( ">" ) ->value( 4 )
                );

                $query->order_by(
                    $query->field( "id" ) ->desc()
                );

            });

            $this->expect( $users ) ->to() ->be() ->exactly_like( [] );

        });

        $this->it( "gets all the objects matching a query with named parameters", function() {

            $users = Users_Collection::get()->all( function($query) {

                $query->filter(
                    $query->field( "id" ) ->op( ">" ) ->param( "id" )
                );

                $query->order_by(
                    $query->field( "id" ) ->desc()
                );

            }, [
                "id" => 1
            ]);

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 3,
                    "get_name()" => "Maggie",
                    "get_last_name()" => "Simpson"
                ],
                [
                    "get_id()" => 2,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson"
                ]
            ]);

        });

    });

    $this->describe( "when getting the first object with ->first()", function() {

        $this->it( "gets the first object", function() {

            $user = Users_Collection::get()->first();

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson"
            ]);

        });

        $this->it( "gets the first object matching a query", function() {

            $user = Users_Collection::get()->first( function($query) {

                $query->filter(
                    $query->field( "id" ) ->op( ">" ) ->value( "1" )
                );

                $query->order_by(
                    $query->field( "id" )
                );

            });

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 2,
                "get_name()" => "Bart",
                "get_last_name()" => "Simpson"
            ]);

        });

        $this->it( "gets the first object matching a query with params", function() {

            $user = Users_Collection::get()->first( function($query) {

                $query->filter(
                    $query->field( "id" ) ->op( ">" ) ->param( "id" )
                );

                $query->order_by(
                    $query->field( "id" )
                );

            }, [
                "id" => 1
            ]);

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 2,
                "get_name()" => "Bart",
                "get_last_name()" => "Simpson"
            ]);

        });

    });

    $this->describe( "when getting the last object with ->last()", function() {

        $this->it( "gets the last object", function() {

            $user = Users_Collection::get()->last();

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 3,
                "get_name()" => "Maggie",
                "get_last_name()" => "Simpson"
            ]);

        });

    });

});