<?php

use Haijin\Persistency\Sql\Sql_Query_Statement_Builder;

$spec->describe( "When building the pagination statement of a sql expression", function() {

    $this->let( "query_builder", function() {
        return new Sql_Query_Statement_Builder();
    });

    $this->it( "builds the offset statement", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->pagination(
                $query->offset( 10 )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users offset 10;"
        );

    });

    $this->it( "builds the limit statement", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->pagination(
                $query->limit( 10 )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users limit 10;"
        );

    });

    $this->it( "builds the limit and offset statement", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->pagination(
                $query
                    ->offset( 1 )
                    ->limit( 10 )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users limit 10 offset 1;"
        );

    });

    $this->it( "builds the page statement", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->pagination(
                $query
                    ->page( 3 )
                    ->page_size( 10 )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users limit 10 offset 30;"
        );

    });

});