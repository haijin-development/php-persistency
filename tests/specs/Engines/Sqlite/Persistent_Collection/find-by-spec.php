<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

$spec->describe( "When searching an object in a Persistent_Collection in a Sqlite database", function() {

    $this->before_all( function() {

        $this->database = new Sqlite_Database();

        $this->database->connect( $this->sqlite_file );

        Users_Collection::get()->set_database( $this->database );

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

    $this->it( "returns the object if it is present", function() {

        $user = Users_Collection::do()->find_by([
            "name" => "Bart",
            "last_name" => "Simpson"
        ]);

        $this->expect( $user ) ->to() ->be() ->exactly_like([
            "get_id()" => 2,
            "get_name()" => "Bart",
            "get_last_name()" => "Simpson"
        ]);

    });
    
    $this->it( "returns null if it is absent", function() {

        $user = Users_Collection::do()->find_by([
            "name" => "Marge",
            "last_name" => "Simpson"
        ]);

        $this->expect( $user ) ->to() ->be() ->null();

    });

    $this->describe( "with find_by_if_absent", function() {

        $this->it( "returns the object if it is present", function() {

            $user = Users_Collection::do()->find_by_if_absent([
                "name" => "Bart",
                "last_name" => "Simpson"
            ], function() {
                return "absent";
            });

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 2,
                "get_name()" => "Bart",
                "get_last_name()" => "Simpson"
            ]);

        });

        $this->it( "evaluates a callable if it is absent", function() {

            $user = Users_Collection::do()->find_by_if_absent([
                "name" => "Marge",
                "last_name" => "Simpson"
            ], function() {
                return "absent";
            });

            $this->expect( $user ) ->to() ->equal( "absent" );

        });

    });

});