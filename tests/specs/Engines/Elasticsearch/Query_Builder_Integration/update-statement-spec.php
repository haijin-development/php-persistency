<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->xdescribe( "When evaluating an update statement in a Elasticsearch database", function() {

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

        $database->connect( [ '127.0.0.1:9200' ] );

        return $database;

    });

    $this->it( "updates a record with constant values", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "Marjorie" ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

            $query->filter(
                $query->match( $query->field( "name" ), $query ->value( "Maggie" ) )
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
            ],
            [
                "id" => 3,
                "name" => "Marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->xit( "updates a record with a function", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->concat( "Mar", "jorie" ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Maggie" )
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
            ],
            [
                "id" => 3,
                "name" => "Marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->xit( "updates many records", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( null ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

            $query->filter(
                $query->field( "id" ) ->op( ">" ) ->value( "1" )
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
                "name" => null,
                "last_name" => "simpson"
            ],
            [
                "id" => 3,
                "name" => null,
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->xit( "updates a record with named parameters", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->param( "name" ) ),
                $query->set( "last_name", $query->param( "last_name" ) )
            );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Maggie" )
            );

        }, [
            "name" => "Margaret",
            "last_name" => "simpson"
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
            ],
            [
                "id" => 3,
                "name" => "Margaret",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->xit( "updates a record with compiled statements", function() {

        $compiled_statement = $this->database->compile_update_statement( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->param( "name" ) ),
                $query->set( "last_name", $query->param( "last_name" ) )
            );

            $query->filter(
                $query->field( "id" ) ->op( "=" ) ->value( 3 )
            );

        });

        $this->database->execute( $compiled_statement, [
            "name" => "Margaret",
            "last_name" => "simpson"
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
            ],
            [
                "id" => 3,
                "name" => "Margaret",
                "last_name" => "simpson"
            ],
        ]);


        $this->database->execute( $compiled_statement, [
            "name" => "margaret",
            "last_name" => "simpson"
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
            ],
            [
                "id" => 3,
                "name" => "margaret",
                "last_name" => "simpson"
            ],
        ]);

    });

});