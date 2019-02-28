<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->describe( "When building the filter statement of a MySql expression", function() {

    $this->let( "database", function() {

        $database = new Elasticsearch_Database();

        $database->connect( [ '127.0.0.1:9200' ] );

        return $database;

    });

    $this->it( "builds a relative field expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->match( $query->field( "name" ), $query ->value( "Lisa" ) )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds a constant value expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->match( $query->field( "name" ), $query ->value( "Lisa" ) )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

});