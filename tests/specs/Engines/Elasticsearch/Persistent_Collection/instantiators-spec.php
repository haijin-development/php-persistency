<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;
use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;

$spec->describe( "When instantiating objects from a Elasticsearch database", function() {

    $this->before_all( function() {

        $this->database = new Elasticsearch_Database();

        $this->database->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

        Elasticsearch_Users_Collection::get()->set_database( $this->database );

        Elasticsearch_Users_Collection::do()->clear_all();

        Elasticsearch_Users_Collection::do()->create_from_attributes([
            "id" => 1,
            "name" => "Lisa",
            "last_name" => "Simpson"
        ]);

        Elasticsearch_Users_Collection::do()->create_from_attributes([
            "id" => 2,
            "name" => "Bart",
            "last_name" => "Simpson"
        ]);

        Elasticsearch_Users_Collection::do()->create_from_attributes([
            "id" => 3,
            "name" => "Maggie",
            "last_name" => "Simpson"
        ]);

    });

    $this->after_all( function() {

        Elasticsearch_Users_Collection::do()->clear_all();

    });

    $this->let( "Elasticsearch_Users_Collection", function() {
        return new Sql_Persistent_Collection();
    });

    $this->describe( "with a class name", function() {

        $this->it( "instantiates the objects of that class", function() {

            $db = $this->database;

            $this->Elasticsearch_Users_Collection->define( function($collection) use($db) {

                $collection->database = $db;

                $collection->collection_name = "users";

                $collection->instantiate_objects_with = User::class;

                $collection->field_mappings = function($mapping) {

                    $mapping->field( "id" ) ->is_primary_key()
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

            $users = $this->Elasticsearch_Users_Collection->all();

            $this->expect( $users[ 0 ] ) ->to() ->be() ->a( User::class );
            $this->expect( $users[ 1 ] ) ->to() ->be() ->a( User::class );
            $this->expect( $users[ 2 ] ) ->to() ->be() ->a( User::class );

        });

    });


    $this->describe( "with a callable", function() {

        $this->it( "instantiates the objects evaluating the callable", function() {

            $db = $this->database;
            $spec = $this;

            $this->Elasticsearch_Users_Collection->define( function($collection) use($spec, $db) {

                $collection->database = $db;

                $collection->collection_name = "users";

                $collection->instantiate_objects_with =
                                                function($raw_record) use($spec) {

                    $spec->expect( $raw_record ) ->to() ->be() ->array();

                    $spec->expect( $raw_record ) ->to() ->be() ->exactly_like([
                        "_id" => function($value) {},
                        "id" => function($value) {},
                        "name" => function($value) {},
                        "last_name" => function($value) {}
                     ]);

                    return new User( $raw_record[ "name" ], $raw_record["last_name"] );

                };

                $collection->field_mappings = function($mapping) {

                    $mapping->field( "id" ) ->is_primary_key()
                        ->read_with( "get_id()" )
                        ->write_with( "set_id()" );

                };

            });

            $users = $this->Elasticsearch_Users_Collection->all();

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

            $this->Elasticsearch_Users_Collection->define( function($collection) use($db) {

                $collection->database = $db;

                $collection->collection_name = "users";

                $collection->instantiate_objects_with = null;

                $collection->field_mappings = function($mapping) {

                    $mapping->field( "id" ) ->is_primary_key();
                    $mapping->field( "name" );
                };

            });

            $users = $this->Elasticsearch_Users_Collection->all();

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