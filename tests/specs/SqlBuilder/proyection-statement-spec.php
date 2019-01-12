<?php

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;

$spec->describe( "When building the proyection statement of a sql expression", function() {

    $this->let( "query_builder", function() {
        return new SqlBuilder();
    });

    $this->it( "builds the select all statement", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->all()
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users;"
        );

    });

    $this->it( "builds the select fields statement", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.name, users.last_name from users;"
        );

    });

    $this->it( "builds aliased fields statements", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ) ->as( "n" ),
                $query->field( "last_name" ) ->as( "ln" )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.name as n, users.last_name as ln from users;"
        );

    });

    $this->it( "builds constant values statements", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query ->value( 1 ),
                $query ->value( "2" )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select 1, '2' from users;"
        );

    });

    $this->it( "builds aliased constant values statements", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->value( 1 ) ->as( "v1" ),
                $query->value( "2" ) ->as( "v2" )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select 1 as v1, '2' as v2 from users;"
        );

    });

    $this->it( "builds a function with values statements", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->f( 1, 2 )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select f(1, 2) from users;"
        );

    });

    $this->it( "builds a function with value expressions statements", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->f(
                    $query->value( 1 ),
                    $query->value( 2 )
                )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select f(1, 2) from users;"
        );

    });

    $this->it( "builds a nested function statement", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->f(
                    $query->g( 1 ),
                    $query->h( 2 )
                )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select f(g(1), h(2)) from users;"
        );

    });

    $this->it( "builds a binary operator statement", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->value( 1 ) ->op( "+" ) ->value( 2 )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select 1 + 2 from users;"
        );

    });

});