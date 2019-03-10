<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->describe( "When inspecting a statement of a Elasticsearch expression", function() {

    $this->let( "database", function() {

        $database = new Elasticsearch_Database();

        $database->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

        return $database;

    });

    $this->it( "inspects a query statement", function() {

        $this->inspected_was_called = false;

        $this->database->inspect_query_with( function($json_params) {

            $this->inspected_was_called = true;

            $this->expect( $json_params ) ->to() ->be() ->exactly_like([
                "index" => "users_read_only",
                "type" => "users_read_only",
                "body" => [
                    "query" => [
                        "bool" => [
                            "must" => [
                                [
                                    "match" => [ "name" => "Lisa" ]
                                ],
                                [
                                    "match" => [ "last_name" => "Simpson" ]
                                ],
                            ]
                        ]
                    ]
                ],
                "sort" => []
            ]);

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->bool(
                    $query->must(
                        $query->match( "name", "Lisa" ),
                        $query->match( "last_name", $query->param( 'ln') )
                    )
                )
            );

        }, [
            'parameters' => [
                'ln' => 'Simpson'
            ]
        ]);

        $this->expect( $this->inspected_was_called ) ->to() ->be() ->true();

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

});