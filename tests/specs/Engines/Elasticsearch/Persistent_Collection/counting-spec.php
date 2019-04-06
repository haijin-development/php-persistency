<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;
use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;

$spec->describe( "When counting objects in a Persistent_Collection stored in a Elasticsearch database", function() {

    $this->before_all( function() {

        $this->database = new Elasticsearch_Database();

        $this->database->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

        Elasticsearch_Users_Collection::get()->set_database( $this->database );

        Elasticsearch_Users_Collection::do()->clear_all();

        Elasticsearch_Users_Collection::do()->create_from_attributes([
            "_id" => 1,
            "name" => "Lisa",
            "last_name" => "Simpson"
        ]);

        Elasticsearch_Users_Collection::do()->create_from_attributes([
            "_id" => 2,
            "name" => "Bart",
            "last_name" => "Simpson"
        ]);

        Elasticsearch_Users_Collection::do()->create_from_attributes([
            "_id" => 3,
            "name" => "Maggie",
            "last_name" => "Simpson"
        ]);

    });

    $this->after_all( function() {

        Elasticsearch_Users_Collection::do()->clear_all();

    });

    $this->it( "counts all the objects in the collection", function() {

        $count = Elasticsearch_Users_Collection::get()->count();

        $this->expect( $count ) ->to() ->equal( 3 );

    });

    $this->it( "counts all the objects matching a query", function() {

        $count = Elasticsearch_Users_Collection::get()->count( function($query) {

            $query->filter(
                    $query->range(
                        $query->id( 'gt', 1 )
                    )
            );

        });

        $this->expect( $count ) ->to() ->equal( 2 );

    });

    $this->it( "counts 0 if no objects matches a query", function() {

        $count = Elasticsearch_Users_Collection::get()->count( function($query) {

            $query->filter(
                    $query->range(
                        $query->id( 'gt', 4 )
                    )
            );

        });

        $this->expect( $count ) ->to() ->equal( 0 );

    });

    $this->it( "counts the objects matching a query with named parameters", function() {

        $count = Elasticsearch_Users_Collection::get()->count( function($query) {

            $query->filter(
                    $query->range(
                        $query->id( 'gt', $query->param( 'id' ) )
                    )
            );

        }, [ 'id' => 1 ]);

        $this->expect( $count ) ->to() ->equal( 2 );

    });

});