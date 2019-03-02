<?php

use Haijin\Persistency\Engines\Elasticsearch\Query_Builder\Elasticsearch_Filter_Builder;
use Haijin\Persistency\Statement_Compiler\Query_Statement_Compiler;

$spec->describe( "When building a match statement", function() {

    $this->let( "query_compiler", function() {

        return new Query_Statement_Compiler();

    });

    $this->let( "filter_builder", function() {

        return new Elasticsearch_Filter_Builder();

    });

    $this->it( "builds the statement with two parameters", function() {

        $compiled_query = $this->query_compiler->build( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->match( "message", "this is a test" )
            );

        });

        $query = $this->filter_builder->visit( $compiled_query->get_filter_expression() );

        $this->expect( $query ) ->to() ->be() ->exactly_like([
            'match' => [
                "message" => "this is a test"
            ]
        ]);

        $this->expect( json_encode( $query ) ) ->to()
            ->equal( '{"match":{"message":"this is a test"}}' );

    });

    $this->it( "builds the statement with two parameters", function() {

        $compiled_query = $this->query_compiler->build( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->match(
                    $query->message([
                        "query" => "this is a test",
                        "operator" => "and"
                    ])
                )
            );

        });

        $query = $this->filter_builder->visit( $compiled_query->get_filter_expression() );

        $this->expect( $query ) ->to() ->be() ->exactly_like([
            'match' => [
                "message" => [
                    "query" => "this is a test",
                    "operator" => "and"
                ]
            ]
        ]);

        $this->expect( json_encode( $query ) ) ->to()
            ->equal( '{"match":{"message":{"query":"this is a test","operator":"and"}}}' );

    });

});