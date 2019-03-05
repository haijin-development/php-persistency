<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

$spec->describe( "When accessing object values", function() {

    $this->before_all( function() {

        $this->database = new Mysql_Database();

        $this->database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        Users_Collection::get()->set_database( $this->database );

    });

    $this->before_each( function() {

        Users_Collection::do()->clear_all();

    });

    $this->after_all( function() {

        Users_Collection::do()->clear_all();

    });

    $this->let( "users_collection", function() {
        return new Persistent_Collection();
    });

    $this->describe( "with functions", function() {

        $this->it( "writes and reads the value from the object", function() {

            $db = $this->database;

            $this->users_collection->define( function($collection) use($db) {

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

            $user = new User();

            $user->set_id( 1 );
            $user->set_name( "Margaret" );
            $user->set_last_name( "Simpson" );

            $this->users_collection->create( $user );

            $user = $this->users_collection->last();

            $this->expect( $user ) ->to() ->be() ->a( User::class );

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Margaret",
                "get_last_name()" => "Simpson"
            ]);

        });

    });

    $this->describe( "with properties", function() {

        $this->it( "writes and reads the value from the object", function() {

            $db = $this->database;

            $this->users_collection->define( function($collection) use($db) {

                $collection->database = $db;

                $collection->collection_name = "users";

                $collection->instantiate_objects_with = \stdclass::class;

                $collection->field_mappings = function($mapping) {

                    $mapping->field( "id" ) ->is_primary_key()
                        ->read_with( "->id" )
                        ->write_with( "->id" );

                    $mapping->field( "name" )
                        ->read_with( "->name" )
                        ->write_with( "->name" );

                    $mapping->field( "last_name" )
                        ->read_with( "->last_name" )
                        ->write_with( "->last_name" );
                };

            });

            $user = new \stdclass;

            $user->id = 1;
            $user->name = "Margaret";
            $user->last_name =  "Simpson";

            $this->users_collection->create( $user );

            $user = $this->users_collection->last();

            $this->expect( $user ) ->to() ->be() ->a( \stdclass::class );

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "id" => 1,
                "name" => "Margaret",
                "last_name" => "Simpson"
            ]);

        });

    });

    $this->describe( "with arrays", function() {

        $this->it( "writes and reads the value from the object", function() {

            $db = $this->database;

            $this->users_collection->define( function($collection) use($db) {

                $collection->database = $db;

                $collection->collection_name = "users";

                $collection->instantiate_objects_with = function(){
                    return [];
                };

                $collection->field_mappings = function($mapping) {

                    $mapping->field( "id" ) ->is_primary_key()
                        ->read_with( "[id]" )
                        ->write_with( "[id]" );

                    $mapping->field( "name" )
                        ->read_with( "[name]" )
                        ->write_with( "[name]" );

                    $mapping->field( "last_name" )
                        ->read_with( "[last_name]" )
                        ->write_with( "[last_name]" );
                };

            });

            $user = [
                "id" => 1,
                "name" => "Margaret",
                "last_name" => "Simpson"
            ];

            $this->users_collection->create( $user );

            $user = $this->users_collection->last();

            $this->expect( $user ) ->to() ->be() ->array();

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "id" => 1,
                "name" => "Margaret",
                "last_name" => "Simpson"
            ]);

        });

    });

    $this->describe( "with closures", function() {

        $this->it( "writes and reads the value from the object", function() {

            $db = $this->database;
            $spec = $this;

            $this->users_collection->define( function($collection) use($db, $spec) {

                $collection->database = $db;

                $collection->collection_name = "users";

                $collection->instantiate_objects_with = User::class;

                $collection->field_mappings = function($mapping) use($spec) {

                    $mapping->field( "id" ) ->is_primary_key()

                        ->read_with( function($object) {
                            return $object->get_id();
                        })

                        ->write_with( function($object, $mapped_record, $raw_record) use($spec) {
                            $object->set_id( $mapped_record[ "id" ] );

                            $spec->expect( $mapped_record ) ->to() ->be() ->exactly_like([
                                "id" => 1,
                                "name" => "Margaret",
                                "last_name" => "Simpson"
                            ]);

                            $spec->expect( $raw_record ) ->to() ->be() ->exactly_like([
                                "id" => 1,
                                "name" => "Margaret",
                                "last_name" => "Simpson",
                                "address_id" => null
                            ]);

                        });

                    $mapping->field( "name" )

                        ->read_with( function($object) {
                            return $object->get_name();
                        })

                        ->write_with( function($object, $mapped_record, $raw_record) {
                            $object->set_name( strtolower( ( $mapped_record[ "name" ] ) ) );
                        });

                    $mapping->field( "last_name" )

                        ->read_with( function($object) {
                            return $object->get_last_name();
                        })

                        ->write_with( function($object, $mapped_record, $raw_record) {
                            $object->set_last_name(
                                strtoupper( ( $mapped_record[ "last_name" ] ) )
                            );
                        });

                };

            });

            $user = new User();

            $user->set_id( 1 );
            $user->set_name( "Margaret" );
            $user->set_last_name( "Simpson" );

            $this->users_collection->create( $user );

            $user = $this->users_collection->last();

            $this->expect( $user ) ->to() ->be() ->a( User::class );

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "margaret",
                "get_last_name()" => "SIMPSON"
            ]);

        });

    });

    $this->describe( "when disabling the field with ->write_to_database( false ) ", function() {

        $this->it( "does not write the value to the database", function() {

            $db = $this->database;

            $this->users_collection->define( function($collection) use($db) {

                $collection->database = $db;

                $collection->collection_name = "users";

                $collection->instantiate_objects_with = User::class;

                $collection->field_mappings = function($mapping) {

                    $mapping->field( "id" ) ->is_primary_key()
                        ->read_with( "get_id()" )
                        ->write_with( "set_id()" );

                    $mapping->field( "name" )
                        ->write_to_database( false )
                        ->read_with( "get_name()" )
                        ->write_with( "set_name()" );

                    $mapping->field( "last_name" )
                        ->read_with( "get_last_name()" )
                        ->write_with( "set_last_name()" );
                };

            });

            $user = new User();

            $user->set_id( 1 );
            $user->set_name( "Margaret" );
            $user->set_last_name( "Simpson" );

            $this->users_collection->create( $user );

            $user = $this->users_collection->last();

            $this->expect( $user ) ->to() ->be() ->a( User::class );

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => null,
                "get_last_name()" => "Simpson"
            ]);

        });

    });

});