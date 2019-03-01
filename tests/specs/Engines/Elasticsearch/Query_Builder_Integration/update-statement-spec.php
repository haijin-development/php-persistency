<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->describe( "When evaluating an update statement in a Elasticsearch database", function() {

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

    $this->it( "updates a record with a script values", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->script([
                "lang" => "painless",
                "source" => "ctx._source.name = 'Margaret'"
            ]);

            $query->filter(
                $query->match( "name", "Maggie" )
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
                "name" => "Margaret",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "updates all records with a script", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->script([
                "lang" => "painless",
                "source" => "ctx._source.name = ctx._source.name.toUpperCase()"
            ]);

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
                "name" => "LISA",
                "last_name" => "Simpson"
            ],
            [
                "id" => 2,
                "name" => "BART",
                "last_name" => "Simpson"
            ],
            [
                "id" => 3,
                "name" => "MAGGIE",
                "last_name" => "Simpson"
            ],
        ]);

    });

});