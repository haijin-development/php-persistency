<?php

use Haijin\Persistency\Engines\Elasticsearch\Query_Builder\Elasticsearch_Filter_Builder;
use Haijin\Persistency\Statement_Compiler\Query_Statement_Compiler;

$spec->describe( "When building a multi_match statement", function() {

    $this->let( "query_compiler", function() {

        return new Query_Statement_Compiler();

    });

    $this->let( "filter_builder", function() {

        return new Elasticsearch_Filter_Builder();

    });

    $this->it( "builds the statement with two parameters", function() {

        $compiled_query = $this->query_compiler->compile( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->multi_match([
                    "query" => "this is a test",
                    "fields" => [ "subject", "message" ]
                ])
            );

        });

        $query = $this->filter_builder->visit( $compiled_query->get_filter_expression() );

        $this->expect( $query ) ->to() ->be() ->exactly_like([
            'multi_match' => [
                "query" => "this is a test",
                "fields" => [ "subject", "message" ]
            ]
        ]);

        $this->expect( json_encode( $query ) ) ->to()
            ->equal( '{"multi_match":{"query":"this is a test","fields":["subject","message"]}}' );

    });

});