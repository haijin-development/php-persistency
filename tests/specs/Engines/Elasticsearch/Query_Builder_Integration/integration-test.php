<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->describe( "When building a Elasticsearch expression", function() {

    $this->let( "database", function() {

        $database = new Elasticsearch_Database();

        $database->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

        return $database;

    });

    $this->it( "builds a complete sql expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->filter(
                $query->bool(
                    $query->must(
                        $query->match( 'name', 'Lisa' ),
                        $query->match( 'last_name', 'Simpson' )
                    )
                )
            );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query
                    ->offset( 0 )
                    ->limit( 10 )
            );

        });

        $this->expect( $rows ) ->to() ->equal([
            [
                "name" => "Lisa",
                "last_name" => "Simpson",
                "_id" => 1
            ]
        ]);

    });

    $this->it( "builds a complete sql expression using macros", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->let( "matches_name", function($query) {
                return $query->field( 'name' ) ->match( 'Lisa' );
            });

            $query->let( "matches_last_name", function($query) {
                return $query->field( 'last_name' ) ->match( 'Simpson' );
            });

            $query->filter(
                $query->bool(
                    $query->must(
                        $query->matches_name,
                        $query->matches_last_name
                    )
                )
            );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query
                    ->offset( 0 )
                    ->limit( 10 )
            );

        });

        $this->expect( $rows ) ->to() ->equal([
            [
                "name" => "Lisa",
                "last_name" => "Simpson",
                "_id" => 1
            ]
        ]);

    });

});