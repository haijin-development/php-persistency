<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;
use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;

$spec->describe( "When searching ids in Persistent_Collection stored in a Sqlite database", function() {

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

    $this->it( "finds the ids in the collection", function() {

        $users = Users_Collection::get()->find_all_by_ids([ 1, 3 ]);

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

    $this->it( "returns an empty set if no id is found", function() {

        $users = Users_Collection::get()->find_all_by_ids([ 4, 5 ]);

        $this->expect( $users ) ->to() ->be() ->exactly_like( [] );

    });

});