<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

$spec->describe( "When instantiating objects from a Sqlite database", function() {

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

    $this->let( "users_collection", function() {
        return new Persistent_Collection();
    });

    $this->describe( "with a class name", function() {

        $this->it( "instantiates the objects of that class", function() {

            $db = $this->database;

            $this->users_collection->define( function($collection) use($db) {

                $collection->database = $db;

                $collection->collection_name = "users";

                $collection->instantiate_objects_with = User::class;

                $collection->field_mappings = function($mapping) {

                    $mapping->field( "id" )
                        ->read_with( "get_id()" )
                        ->write_with( "set_id()" );

                    $mapping->field( "name" )
                        ->read_with( "get_name()" )
                        ->write_with( "set_name()" );

                    $mapping->field( "last_name" )
                        ->read_with( "get_last_name()" )
                        ->write_with( "set_last_name()" );
                };

            });

            $users = $this->users_collection->all();

            $this->expect( $users[ 0 ] ) ->to() ->be() ->a( User::class );
            $this->expect( $users[ 1 ] ) ->to() ->be() ->a( User::class );
            $this->expect( $users[ 2 ] ) ->to() ->be() ->a( User::class );

        });

    });


    $this->describe( "with a closure", function() {

        $this->it( "instantiates the objects evaluating the closure", function() {

            $db = $this->database;
            $spec = $this;

            $this->users_collection->define( function($collection) use($spec, $db) {

                $collection->database = $db;

                $collection->collection_name = "users";

                $collection->instantiate_objects_with =
                                        function($mapped_record, $raw_record) use($spec) {

                    $spec->expect( $mapped_record ) ->to() ->be() ->array();

                    $spec->expect( $mapped_record ) ->to() ->be() ->exactly_like([
                        "id" => function($value) {},
                     ]);

                    $spec->expect( $raw_record ) ->to() ->be() ->array();

                    $spec->expect( $raw_record ) ->to() ->be() ->exactly_like([
                        "id" => function($value) {},
                        "name" => function($value) {},
                        "last_name" => function($value) {}
                     ]);

                    return new User( $raw_record[ "name" ], $raw_record["last_name"] );

                };

                $collection->field_mappings = function($mapping) {

                    $mapping->field( "id" )
                        ->read_with( "get_id()" )
                        ->write_with( "set_id()" );

                };

            });

            $users = $this->users_collection->all();

            $this->expect( $users[ 0 ] ) ->to() ->be() ->a( User::class );
            $this->expect( $users[ 1 ] ) ->to() ->be() ->a( User::class );
            $this->expect( $users[ 2 ] ) ->to() ->be() ->a( User::class );

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

    });

    $this->describe( "with null", function() {

        $this->it( "instantiates an associative array of the mapped records", function() {

            $db = $this->database;

            $this->users_collection->define( function($collection) use($db) {

                $collection->database = $db;

                $collection->collection_name = "users";

                $collection->instantiate_objects_with = null;

                $collection->field_mappings = function($mapping) {

                    $mapping->field( "id" );
                    $mapping->field( "name" );
                };

            });

            $users = $this->users_collection->all();

            $this->expect( $users[ 0 ] ) ->to() ->be() ->array();
            $this->expect( $users[ 1 ] ) ->to() ->be() ->array();
            $this->expect( $users[ 2 ] ) ->to() ->be() ->array();

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "id" => 1,
                    "name" => "Lisa",
                ],
                [
                    "id" => 2,
                    "name" => "Bart",
                ],
                [
                    "id" => 3,
                    "name" => "Maggie",
                ]
            ]);

        });

    });

});