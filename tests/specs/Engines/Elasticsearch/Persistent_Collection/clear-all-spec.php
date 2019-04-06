<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;
use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;

$spec->describe( "When deleting an object from a Persistent_Collection in a Elasticsearch database", function() {

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

    $this->it( "deletes all objects object", function() {

        $users = Elasticsearch_Users_Collection::get()->clear_all();

        $this->expect( $users ) ->to() ->be() ->exactly_like( [] );

    });

});