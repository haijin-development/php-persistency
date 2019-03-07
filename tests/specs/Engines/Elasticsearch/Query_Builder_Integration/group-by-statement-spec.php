<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->describe( "When building the group by statement of a sql expression", function() {

    $this->before_all( function() {

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
                'name' => 'Marge',
                'last_name' => 'Bouvier'
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

        $this->clear_postgresql_tables();

    });

    $this->let( "database", function() {

        $database = new Elasticsearch_Database();

        $database->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

        return $database;

    });

    $this->it( "builds the group by fields", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->group_by(
                $query->last_names(
                    $query->terms( 'field', 'last_name' )
                )
            );

            $query->order_by(
                $query->field( 'last_name' )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            'last_names' => [
                'doc_count_error_upper_bound' => 0,
                'sum_other_doc_count' => 0,
                'buckets' => [
                    0 => [
                        'key' => "simpson",
                        'doc_count' => 2
                    ],
                    1 => [
                        'key' => "bouvier",
                        'doc_count' => 1
                    ]
                ]
            ]
        ]);

    });

});