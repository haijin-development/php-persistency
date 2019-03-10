<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;
use Haijin\Persistency\Errors\Persistency_Error;

$spec->describe( "When finding an object by its matching fields in a Persistent_Collection in a Elasticsearch database", function() {

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
            "last_name" => "Simpson",
        ]);

        Elasticsearch_Users_Collection::do()->create_from_attributes([
            "id" => 2,
            "name" => "Bart",
            "last_name" => "Simpson",
        ]);

        Elasticsearch_Users_Collection::do()->create_from_attributes([
            "id" => 3,
            "name" => "Maggie",
            "last_name" => "Simpson",
        ]);

    });

    $this->after_all( function() {

        Elasticsearch_Users_Collection::do()->clear_all();

    });

    $this->it( "returns the object if it is present", function() {

        $user = Elasticsearch_Users_Collection::do()->find_by([
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

        $user = Elasticsearch_Users_Collection::do()->find_by([
            "name" => "Marge",
            "last_name" => "Simpson"
        ]);

        $this->expect( $user ) ->to() ->be() ->null();

    });

    $this->it( "raises an error if it finds more than one object", function() {

        $this->expect( function() {

            Elasticsearch_Users_Collection::do()->find_by([
                "last_name" => "Simpson"
            ]);

        }) ->to() ->raise(
            Persistency_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->equal( "Expected one record, found 3." );
            }
        );

    });

    $this->describe( "with find_by_if_absent", function() {

        $this->it( "returns the object if it is present", function() {

            $user = Elasticsearch_Users_Collection::do()->find_by_if_absent([
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

            $user = Elasticsearch_Users_Collection::do()->find_by_if_absent([
                "name" => "Marge",
                "last_name" => "Simpson"
            ], function() {
                return "absent";
            });

            $this->expect( $user ) ->to() ->equal( "absent" );

        });

        $this->it( "raises an error if it finds more than one object", function() {

            $this->expect( function() {

                Elasticsearch_Users_Collection::do()->find_by_if_absent([
                    "last_name" => "Simpson"
                ], function() {
                return "absent";
            });

            }) ->to() ->raise(
                Persistency_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to() ->equal( "Expected one record, found 3." );
                }
            );

        });

    });

});