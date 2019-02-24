<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

$spec->describe( "When updating an object in a Persistent_Collection in a MySql database", function() {

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

    $this->it( "updates the object", function() {

        $user = Users_Collection::get()->find_by_id( 3 );

        $user->set_name( "Margaret" );

        Users_Collection::do()->update( $user );

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
                "get_name()" => "Margaret",
                "get_last_name()" => "Simpson"
            ]
        ]);

    });

    $this->it( "updates the object from its its attributes", function() {

        $user = Users_Collection::get()->find_by_id( 3 );

        Users_Collection::do()->update_from_attributes( $user, [
            "name" => "Margaret"
        ]);

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
                "get_name()" => "Margaret",
                "get_last_name()" => "Simpson"
            ]
        ]);

    });
    
    $this->it( "updates all the objects matching a filter", function() {

        Users_Collection::do()->update_all( function($query) {

            $query->record(
                $query->set( "last_name", $query->field( "last_name" )->lower() )
            );

            $query->filter(

                $query->field( "id") ->op( "<=" ) ->value( 2 )

            );

        });

        $users = Users_Collection::get()->all();

        $this->expect( $users ) ->to() ->be() ->exactly_like([
            [
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "simpson"
            ],
            [
                "get_id()" => 2,
                "get_name()" => "Bart",
                "get_last_name()" => "simpson"
            ],
            [
                "get_id()" => 3,
                "get_name()" => "Maggie",
                "get_last_name()" => "Simpson"
            ]
        ]);

    });

    $this->it( "updates all the objects matching a filter with parameters", function() {

        Users_Collection::do()->update_all( function($query) {

            $query->record(
                $query->set( "last_name", $query->param( "last_name" ) )
            );

            $query->filter(

                $query->field( "id") ->op( "<=" ) ->param( "id" )

            );

        }, [ "last_name" => "simpson", "id" => 2 ] );

        $users = Users_Collection::get()->all();

        $this->expect( $users ) ->to() ->be() ->exactly_like([
            [
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "simpson"
            ],
            [
                "get_id()" => 2,
                "get_name()" => "Bart",
                "get_last_name()" => "simpson"
            ],
            [
                "get_id()" => 3,
                "get_name()" => "Maggie",
                "get_last_name()" => "Simpson"
            ]
        ]);

    });

});