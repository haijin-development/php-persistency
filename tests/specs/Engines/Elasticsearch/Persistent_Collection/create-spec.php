<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;
use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;
use Haijin\Errors\Haijin_Error;

$spec->describe( "When creating an object in a Persistent_Collection in a Elasticsearch database", function() {

    $this->before_all( function() {

        $this->database = new Elasticsearch_Database();

        $this->database->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

        Elasticsearch_Users_Collection::get()->set_database( $this->database );

    });

    $this->before_each( function() {

        Elasticsearch_Users_Collection::do()->clear_all();

    });

    $this->after_all( function() {

        Elasticsearch_Users_Collection::do()->clear_all();

    });

    $this->describe( "from an object", function() {

        $this->it( "creates the object with a given id", function() {

            $user = new User();
            $user->set_id( 7 );
            $user->set_name( "Lisa" );
            $user->set_last_name( "Simpson" );

            Elasticsearch_Users_Collection::do()->create( $user );

            $user = new User();
            $user->set_id( 8 );
            $user->set_name( "Bart" );
            $user->set_last_name( "Simpson" );

            Elasticsearch_Users_Collection::do()->create( $user );

            $users = Elasticsearch_Users_Collection::get()->all();

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 7,
                    "get_name()" => "Lisa",
                    "get_last_name()" => "Simpson"
                ],
                [
                    "get_id()" => 8,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson"
                ]
            ]);

        });

        $this->it( "creates the object with missing fields", function() {

            $user = new User();
            $user->set_id( 7 );
            $user->set_name( "Lisa" );

            Elasticsearch_Users_Collection::do()->create( $user );

            $user = new User();
            $user->set_id( 8 );
            $user->set_name( "Bart" );

            Elasticsearch_Users_Collection::do()->create( $user );

            $users = Elasticsearch_Users_Collection::get()->all();

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 7,
                    "get_name()" => "Lisa",
                    "get_last_name()" => null
                ],
                [
                    "get_id()" => 8,
                    "get_name()" => "Bart",
                    "get_last_name()" => null
                ]
            ]);

        });

        $this->it( "raises an error if the object has no id", function() {

            $this->expect( function() {

                $user = new User();
                $user->set_name( "Lisa" );
                $user->set_last_name( "Simpson" );

                Elasticsearch_Users_Collection::do()->create( $user );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to() ->equal( "Must assign an _id." );
            });

        });

    });

    $this->describe( "from attributes", function() {

        $this->it( "creates the object with a given id", function() {

            Elasticsearch_Users_Collection::do()->create_from_attributes([
                "id" => 7,
                "name" => "Lisa",
                "last_name" => "Simpson",
            ]);

            Elasticsearch_Users_Collection::do()->create_from_attributes([
                "id" => 8,
                "name" => "Bart",
                "last_name" => "Simpson",
            ]);

            $users = Elasticsearch_Users_Collection::get()->all();

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 7,
                    "get_name()" => "Lisa",
                    "get_last_name()" => "Simpson"
                ],
                [
                    "get_id()" => 8,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson"
                ]
            ]);

        });

        $this->it( "creates the object with missing fields", function() {

            Elasticsearch_Users_Collection::do()->create_from_attributes([
                "id" => 7,
                "name" => "Lisa"
            ]);

            Elasticsearch_Users_Collection::do()->create_from_attributes([
                "id" => 8,
                "name" => "Bart"
            ]);

            $users = Elasticsearch_Users_Collection::get()->all();

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 7,
                    "get_name()" => "Lisa",
                    "get_last_name()" => null
                ],
                [
                    "get_id()" => 8,
                    "get_name()" => "Bart",
                    "get_last_name()" => null
                ]
            ]);

        });

        $this->it( "creates the object with no id", function() {

            $this->expect( function() {

                Elasticsearch_Users_Collection::do()->create_from_attributes([
                    "name" => "Lisa",
                    "last_name" => "Simpson",
                ]);

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to() ->equal( "Must assign an _id." );
            });

        });

    });
    
});