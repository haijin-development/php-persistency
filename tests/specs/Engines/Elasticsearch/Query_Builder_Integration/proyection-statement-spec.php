<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->xdescribe( "When building the proyection statement of a Elasticsearch expression", function() {

    $this->let( "database", function() {

        $database = new Elasticsearch_Database();

        $database->connect( [ '127.0.0.1:9200' ] );

        return $database;

    });

    $this->it( "builds the select all statement", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->proyect(
                $query->all()
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

    $this->it( "builds the select fields statement", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
            [
                "name" => "Bart",
                "last_name" => "Simpson"
            ],
            [
                "name" => "Maggie",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds aliased fields statements", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->proyect(
                $query->field( "name" ) ->as( "n" ),
                $query->field( "last_name" ) ->as( "ln" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "n" => "Lisa",
                "ln" => "Simpson"
            ],
            [
                "n" => "Bart",
                "ln" => "Simpson"
            ],
            [
                "n" => "Maggie",
                "ln" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds constant values statements", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->proyect(
                $query ->value( 1 ),
                $query ->value( "2" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                1 => 1,
                2 => "2"
            ],
            [
                1 => 1,
                2 => "2"
            ],
            [
                1 => 1,
                2 => "2"
            ]
        ]);

    });

    $this->it( "builds aliased constant values statements", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->proyect(
                $query->value( 1 ) ->as( "v1" ),
                $query->value( "2" ) ->as( "v2" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "v1" => 1,
                "v2" => "2"
            ],
            [
                "v1" => 1,
                "v2" => "2"
            ],
            [
                "v1" => 1,
                "v2" => "2"
            ]
        ]);

    });

    $this->it( "builds a function with values statements", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->proyect(
                $query->concat( "1", "0" ) ->as( "s" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "s" => "10"
            ],
            [
                "s" => "10"
            ],
            [
                "s" => "10"
            ]
        ]);

    });

    $this->it( "builds a function with value expressions statements", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->proyect(
                $query->concat(
                    $query->value( "1" ),
                    $query->value( "0" )
                ) ->as( "s" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "s" => "10"
            ],
            [
                "s" => "10"
            ],
            [
                "s" => "10"
            ]
        ]);

    });

    $this->it( "builds a nested function statement", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->proyect(
                $query->concat(
                    $query->upper( "a" ),
                    $query->lower( "A" )
                ) ->as( "s" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "s" => "Aa"
            ],
            [
                "s" => "Aa"
            ],
            [
                "s" => "Aa"
            ]
        ]);

    });

    $this->it( "builds a binary operator statement", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->proyect(
                $query->brackets(
                    $query->value( 1 ) ->op( "+" ) ->value( 2 )
                ) ->as( "n" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "n" => 3
            ],
            [
                "n" => 3
            ],
            [
                "n" => 3
            ]
        ]);

    });

});