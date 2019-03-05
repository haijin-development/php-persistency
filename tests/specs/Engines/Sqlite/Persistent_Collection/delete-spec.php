<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

$spec->describe( "When deleting an object from a Persistent_Collection in a Sqlite database", function() {

    $this->before_all( function() {

        $this->database = new Sqlite_Database();

        $this->database->connect( $this->sqlite_file );

        Users_Collection::get()->set_database( $this->database );

    });

    $this->before_each( function() {

        Users_Collection::do()->clear_all();

        Users_Collection::do()->create_from_attributes([
            "id" => 1,
            "name" => "Lisa",
            "last_name" => "Simpson",
        ]);

        Users_Collection::do()->create_from_attributes([
            "id" => 2,
            "name" => "Bart",
            "last_name" => "Simpson",
        ]);

        Users_Collection::do()->create_from_attributes([
            "id" => 3,
            "name" => "Maggie",
            "last_name" => "Simpson",
        ]);

    });

    $this->after_all( function() {

        Users_Collection::do()->clear_all();

    });

    $this->it( "deletes the object", function() {

        $user = Users_Collection::get()->find_by_id( 2 );

        Users_Collection::do()->delete( $user );

        $users = Users_Collection::get()->all();

        $this->expect( $users ) ->to() ->be() ->exactly_like([
            [
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson"
            ],
            [
                "get_id()" => 3,
                "get_name()" => "Maggie",
                "get_last_name()" => "Simpson"
            ]
        ]);

    });

    $this->it( "deletes all objects matching a filter from the collection", function() {

        $users = Users_Collection::do()->delete_all( function($query) {

            $query->filter(
                $query->field( "id" ) ->op( "<=" ) ->value( 2 )
            );

        });

        $users = Users_Collection::get()->all();

        $this->expect( $users ) ->to() ->be() ->exactly_like([
            [
                "get_id()" => 3,
                "get_name()" => "Maggie",
                "get_last_name()" => "Simpson"
            ]
        ]);

    });

    $this->it( "deletes all objects matching a filter from the collection", function() {

        $users = Users_Collection::do()->delete_all( function($query) {

            $query->filter(
                $query->field( "id" ) ->op( "<=" ) ->param( "id" )
            );

        }, [
            'parameters' => [
                "id" => 2
            ]
        ]);

        $users = Users_Collection::get()->all();

        $this->expect( $users ) ->to() ->be() ->exactly_like([
            [
                "get_id()" => 3,
                "get_name()" => "Maggie",
                "get_last_name()" => "Simpson"
            ]
        ]);

    });

    $this->it( "removes all objects from the collection", function() {

        $users = Users_Collection::do()->clear_all();

        $users = Users_Collection::get()->all();

        $this->expect( $users ) ->to() ->be() ->exactly_like( [] );

    });

});