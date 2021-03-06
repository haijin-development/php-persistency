<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->describe( "When evaluating a delete statement in a Elasticsearch database", function() {

    $this->before_each( function() {

        $this->clear_elasticsearch_indices();

        $this->elasticsearch->index([
            'index' => 'users',
            'type' => 'users',
            'id' => 1,
            'body' => [
                'id' => 1,
                'name' => 'Lisa',
                'last_name' => 'Simpson'
            ],
            'refresh' => true
        ]);

        $this->elasticsearch->index([
            'index' => 'users',
            'type' => 'users',
            'id' => 2,
            'body' => [
                'id' => 2,
                'name' => 'Bart',
                'last_name' => 'Simpson'
            ],
            'refresh' => true
        ]);

        $this->elasticsearch->index([
            'index' => 'users',
            'type' => 'users',
            'id' => 3,
            'body' => [
                'id' => 3,
                'name' => 'Maggie',
                'last_name' => 'Simpson'
            ],
            'refresh' => true
        ]);

    });

    $this->after_all( function() {

        $this->clear_elasticsearch_indices();

    });

    $this->let( "database", function() {

        $database = new Elasticsearch_Database();

        $database->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

        return $database;

    });

    $this->it( "deletes a record with constant values", function() {

        $this->database->delete( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "name" ) ->match( "Maggie" )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
            ]
        ]);

    });

    $this->it( "deletes many records", function() {

        $this->database->delete( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "last_name" ) ->match( "Simpson" )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like( [] );

    });

    $this->it( "deletes a record with parameters", function() {

        $this->database->delete( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->match(
                    $query->field( "name" ),
                    $query->param( "name" )
                )
            );

        }, [
            'parameters' => [
                "name" => "Maggie"
            ]
        ]);

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
            ]
        ]);

    });

    $this->it( "deletes a record with a compiled query", function() {

        $compiled_query = $this->database->compile( function($compiler) {

            $compiler->delete( function($query) {

                $query->collection( "users" );

                $query->filter(
                    $query->match(
                        $query->field( "name" ),
                        $query->param( "name" )
                    )
                );

            });

        });

        $this->database->execute( $compiled_query, [
            'parameters' => [
                "name" => "Maggie"
            ]
        ]);

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
            ]
        ]);


        $this->database->execute( $compiled_query, [
            'parameters' => [
                "name" => "Bart"
            ]
        ]);

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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