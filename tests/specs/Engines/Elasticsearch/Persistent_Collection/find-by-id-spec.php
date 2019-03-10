<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

$spec->describe( "When searching an object by its primary key in a Persistent_Collection in a Elasticsearch database", function() {

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

        $user = Elasticsearch_Users_Collection::do()->find_by_id( 2 );

        $this->expect( $user ) ->to() ->be() ->exactly_like([
            "get_id()" => 2,
            "get_name()" => "Bart",
            "get_last_name()" => "Simpson"
        ]);

    });
    
    $this->it( "returns null if it is absent", function() {

        $user = Elasticsearch_Users_Collection::do()->find_by_id( 4 );

        $this->expect( $user ) ->to() ->be() ->null();

    });

    $this->describe( "with find_by_id_if_absent", function() {

        $this->it( "returns the object if it is present", function() {

            $user = Elasticsearch_Users_Collection::do()->find_by_id_if_absent( 2, function() {
                return "absent";
            });

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 2,
                "get_name()" => "Bart",
                "get_last_name()" => "Simpson"
            ]);

        });

        $this->it( "evaluates a callable if it is absent", function() {

            $user = Elasticsearch_Users_Collection::do()->find_by_id_if_absent( 4, function() {
                return "absent";
            });

            $this->expect( $user ) ->to() ->equal( "absent" );

        });

    });

});