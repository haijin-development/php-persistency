<?php

use Haijin\Persistency\Engines\Elasticsearch\Query_Builder\Elasticsearch_Filter_Builder;
use Haijin\Persistency\Statement_Compiler\Query_Statement_Compiler;

$spec->describe( "When building a match_all statement", function() {

    $this->let( "query_compiler", function() {

        return new Query_Statement_Compiler();

    });

    $this->let( "filter_builder", function() {

        return new Elasticsearch_Filter_Builder();

    });

    $this->it( "builds the statement with no parameters", function() {

        $compiled_query = $this->query_compiler->build( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->match_all()
            );

        });

        $query = $this->filter_builder->visit( $compiled_query->get_filter_expression() );

        $this->expect( $query ) ->to() ->be() ->exactly_like([
            'match_all' => []
        ]);

        $this->expect( json_encode( $query ) ) ->to() ->equal( '{"match_all":{}}' );

    });

    $this->it( "builds the statement with parameters", function() {

        $compiled_query = $this->query_compiler->build( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->match_all( "boost", 1.2 )
            );

        });

        $query = $this->filter_builder->visit( $compiled_query->get_filter_expression() );

        $this->expect( $query ) ->to() ->be() ->exactly_like([
            'match_all' => [
                "boost" => 1.2
            ]
        ]);

        $this->expect( json_encode( $query ) )
            ->to() ->equal( '{"match_all":{"boost":1.2}}' );

    });

});