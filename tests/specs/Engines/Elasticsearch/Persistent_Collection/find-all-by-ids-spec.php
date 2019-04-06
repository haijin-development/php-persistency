<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;
use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;

$spec->describe( "When searching ids in Persistent_Collection stored in a Elasticsearch database", function() {

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

    $this->it( "finds the ids in the collection", function() {

        $users = Elasticsearch_Users_Collection::get()->find_all_by_ids([ 1, 3 ]);

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

        $users = Elasticsearch_Users_Collection::get()->find_all_by_ids([ 4, 5 ]);

        $this->expect( $users ) ->to() ->be() ->exactly_like( [] );

    });

});