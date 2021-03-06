<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

$spec->describe( "When updating an object in a Persistent_Collection in a Elasticsearch database", function() {

    $this->before_all( function() {

        $this->database = new Elasticsearch_Database();

        $this->database->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

        Elasticsearch_Users_Collection::get()->set_database( $this->database );

    });

    $this->before_each( function() {

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

    $this->it( "updates the object", function() {

        $user = Elasticsearch_Users_Collection::get()->find_by_id( 3 );

        $user->set_name( "Margaret" );

        Elasticsearch_Users_Collection::do()->update( $user );

        $users = Elasticsearch_Users_Collection::get()->all();

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

        $user = Elasticsearch_Users_Collection::get()->find_by_id( 3 );

        Elasticsearch_Users_Collection::do()->update_from_attributes( $user, [
            "name" => "Margaret"
        ]);

        $users = Elasticsearch_Users_Collection::get()->all();

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

});