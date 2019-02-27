<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->describe( "When evaluating a create statement in a Elasticsearch database", function() {

    $this->before_each( function() {

        $this->clear_elasticsearch_indices();

    });

    $this->after_all( function() {

        $this->clear_elasticsearch_indices();

    });

    $this->let( "database", function() {

        $database = new Elasticsearch_Database();

        $database->connect( [ '127.0.0.1:9200' ] );

        return $database;

    });

    $this->it( "returns the created id", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "_id", $query->value( 1 ) ),
                $query->set( "name", $query->value( "Lisa" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $id = $this->database->get_last_created_id();

        $this->expect( $id ) ->to() ->equal( 1 );

    });

    $this->it( "creates a record with constant values", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "_id", $query->value( 1 ) ),
                $query->set( "name", $query->value( "Lisa" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "_id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
        ]);

    });

});