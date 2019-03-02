<?php

use Haijin\Persistency\Sql\Sql_Query_Statement_Builder;

$spec->describe( "When building the filter statement of a sql expression", function() {

    $this->let( "query_builder", function() {
        return new Sql_Query_Statement_Builder();
    });

    $this->it( "builds a relative field expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where users.name = 'Lisa';"
        );

    });

    $this->it( "builds an absolute field expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" ) ->as( "u" );

            $query->filter(
                $query ->field( "u.name" ) ->op( "=" ) ->value( "Lisa" )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select u.* from users as u where u.name = 'Lisa';"
        );

    });

    $this->it( "builds a constant value expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where users.name = 'Lisa';"
        );

    });

    $this->it( "builds a function with values", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->f(1, 2) ->op( "=" ) ->value(3)
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where f(1, 2) = 3;"
        );

    });

    $this->it( "builds a function with values expressions", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->f( $query->value(1), $query->value(2) ) ->op( "=" ) ->value(3)
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where f(1, 2) = 3;"
        );

    });

    $this->it( "builds a nested function expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->f( $query->g(1), $query->h(2) ) ->op( "=" ) ->value(3)
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where f(g(1), h(2)) = 3;"
        );

    });

    $this->it( "builds a binary opetor expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                ( $query->value( 1 ) ->op( "+" ) ->value( 2 ) ) ->op( "=" ) ->value(3)
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where 1 + 2 = 3;"
        );

    });

    $this->it( "builds a is null expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->value( 1 ) ->is_null()
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where 1 is null;"
        );

    });

    $this->it( "builds a is not null expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->value( 1 ) ->is_not_null()
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where 1 is not null;"
        );

    });

    $this->it( "builds a message send expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "name" ) ->uppercase() ->is_not_null()
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where uppercase(users.name) is not null;"
        );

    });

    $this->it( "builds a message send with parameters expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "name" ) ->match( '%lisa%' )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where match(users.name, '%lisa%');"
        );

    });

    $this->it( "builds a brackets expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->brackets( $query->value( 1 ) )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where (1);"
        );

    });

    $this->it( "builds an and expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->value( 1 ) ->and() ->value( 1 )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where 1 and 1;"
        );

    });

    $this->it( "builds an or expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->value( 1 ) ->or() ->value( 1 )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where 1 or 1;"
        );

    });

});