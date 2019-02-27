<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Filter_Builder;
use Haijin\Persistency\Statement_Compiler\Query_Statement_Compiler;

$spec->describe( "When building the collection statement of a Elasticsearch expression", function() {

    $this->let( "query_compiler", function() {

        return new Query_Statement_Compiler();

    });

    $this->let( "filter_builder", function() {

        return new Elasticsearch_Filter_Builder();

    });

    $this->it( "builds a single leaf query clause", function() {

        $compiled_query = $this->query_compiler->build( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->term( $query->field( 'name' ), $this->value( 'Lisa' ) )
            );

        });

        $query = $this->filter_builder->visit( $compiled_query->get_filter_expression() );

        $this->expect( $query ) ->to() ->be() ->exactly_like([
            'query' => [
                'term' => [
                    'name' => 'Lisa'
                ]
            ]
        ]);

    });

    $this->it( "builds a single leaf query clause with literals", function() {

        $compiled_query = $this->query_compiler->build( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->term( 'name', 'Lisa' )
            );

        });

        $query = $this->filter_builder->visit( $compiled_query->get_filter_expression() );

        $this->expect( $query ) ->to() ->be() ->exactly_like([
            'query' => [
                'term' => [
                    'name' => 'Lisa'
                ]
            ]
        ]);

    });

    $this->it( "builds mutliple leaf query clauses", function() {

        $compiled_query = $this->query_compiler->build( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->bool(
                    $query->should(
                        $query->term( $query->field( 'name' ), 'Lisa' ),
                        $query->term( $query->field( 'last_name' ), 'Simpson' )
                    )
                )
            );

        });

        $query = $this->filter_builder->visit( $compiled_query->get_filter_expression() );

        $this->expect( $query ) ->to() ->be() ->exactly_like([
            'query' => [
                'bool' => [
                    'should' => [
                        [
                            'term' => [
                                'name' => 'Lisa'
                            ]
                        ],
                        [
                            'term' => [
                                'last_name' => 'Simpson'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

    });

    $this->it( "builds a compound query clause", function() {

        $compiled_query = $this->query_compiler->build( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->bool(
                    $query->should(
                        $query->term( $query->field( 'name' ), 'Lisa' ),
                        $query->term( $query->field( 'last_name' ), 'Simpson' )
                    )
                )
            );

        });

        $query = $this->filter_builder->visit( $compiled_query->get_filter_expression() );

        $this->expect( $query ) ->to() ->be() ->exactly_like([
            'query' => [
                'bool' => [
                    'should' => [
                        [
                            'term' => [
                                'name' => 'Lisa'
                            ]
                        ],
                        [
                            'term' => [
                                'last_name' => 'Simpson'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

    });

});