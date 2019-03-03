<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->describe( "When ignoring expressions a Elasticsearch expression", function() {

    $this->let( "database", function() {

        $database = new Elasticsearch_Database();

        $database->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

        return $database;

    });

    $this->it( "ignores a filter expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->ignore()
            );

            $query->order_by(
                $query ->field( 'id' )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
            [
                "id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson"
            ],
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "ignores a function parameter", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->bool(
                    $query->must(
                        $query ->ignore(),
                        $query->field( "name" ) ->match( "Lisa" ),
                        $query ->ignore()
                    )
                )
            );

            $query->order_by(
                $query ->field( 'id' )
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