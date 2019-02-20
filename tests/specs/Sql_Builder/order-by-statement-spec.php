<?php

use Haijin\Persistency\Sql\Sql_Query_Statement_Builder;

$spec->describe( "When building the order by statement of a sql expression", function() {

    $this->let( "query_builder", function() {
        return new Sql_Query_Statement_Builder();
    });

    $this->it( "builds the order by fields", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users order by name, last_name;"
        );

    });

    $this->it( "builds the desc expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "name" ) ->desc()
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users order by name desc;"
        );

    });

    $this->it( "builds the asc expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "name" ) ->asc()
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users order by name asc;"
        );

    });

    $this->it( "builds aliased fields", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ) ->as( "n" )
            );

            $query->order_by(
                $query->field( "n" )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.name as n from users order by n;"
        );

    });

});