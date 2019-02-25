<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

$spec->describe( "When counting objects in a Persistent_Collection stored in a Sqlite database", function() {

    $this->before_all( function() {

        $this->database = new Sqlite_Database();

        $this->database->connect( $this->sqlite_file );

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

    $this->it( "counts all the objects in the collection", function() {

        $count = Users_Collection::get()->count();

        $this->expect( $count ) ->to() ->equal( 3 );

    });

    $this->it( "counts all the objects matching a query", function() {

        $count = Users_Collection::get()->count( function($query) {

            $query->filter(
                $query->field( "id" ) ->op( ">" ) ->value( 1 )
            );

        });

        $this->expect( $count ) ->to() ->equal( 2 );

    });

    $this->it( "counts 0 if no objects matches a query", function() {

        $count = Users_Collection::get()->count( function($query) {

            $query->filter(
                $query->field( "id" ) ->op( ">" ) ->value( 4 )
            );

        });

        $this->expect( $count ) ->to() ->equal( 0 );

    });

    $this->it( "counts the objects matching a query with named parameters", function() {

        $count = Users_Collection::get()->count( function($query) {

            $query->filter(
                $query->field( "id" ) ->op( "=" ) ->param( "id" )
            );

        }, [
            "id" => 1
        ]);

        $this->expect( $count ) ->to() ->equal( 1 );

    });

});