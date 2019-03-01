<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->describe( "When building the proyection statement of a Elasticsearch expression", function() {

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

            $query->order_by(
                $query->field( 'id' )
            );

        });

        $this->expect( $rows ) ->to() ->equal([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson",
                "_id" => 1,
            ],
            [
                "id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson",
                "_id" => 2,
            ],
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson",
                "_id" => 3,
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

            $query->order_by(
                $query->field( 'id' )
            );

        });

        $this->expect( $rows ) ->to() ->equal([
            [
                "name" => "Lisa",
                "last_name" => "Simpson",
                "_id" => 1,
            ],
            [
                "name" => "Bart",
                "last_name" => "Simpson",
                "_id" => 2,
            ],
            [
                "name" => "Maggie",
                "last_name" => "Simpson",
                "_id" => 3,
            ]
        ]);

    });

});