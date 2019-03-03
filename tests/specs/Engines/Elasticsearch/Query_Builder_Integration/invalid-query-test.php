<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->describe( "When building an invalid Elasticsearch query", function() {

    $this->let( "database", function() {

        $database = new Elasticsearch_Database();

        $database->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

        return $database;

    });

    $this->it( "raises an error when the query has an error", function() {

        $this->expect( function() {

            $this->database->query( function($query) {
                $query->collection( "non_existing_index" );
            });

        }) ->to() ->raise(
            \Elasticsearch\Common\Exceptions\Missing404Exception::class,
            function($error) {

                $this->expect( $error->getMessage() ) ->to() ->equal(
                    '{"error":{"root_cause":[{"type":"index_not_found_exception","reason":"no such index","resource.type":"index_or_alias","resource.id":"non_existing_index","index_uuid":"_na_","index":"non_existing_index"}],"type":"index_not_found_exception","reason":"no such index","resource.type":"index_or_alias","resource.id":"non_existing_index","index_uuid":"_na_","index":"non_existing_index"},"status":404}'
                );

        });
    });

});