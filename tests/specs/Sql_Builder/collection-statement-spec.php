<?php

use Haijin\Persistency\Sql\Query_Builder\Sql_Builder;

$spec->describe( "When building the collection statement of a sql expression", function() {

    $this->let( "query_builder", function() {
        return new Sql_Builder();
    });

    $this->it( "builds the collection name", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users;"
        );

    });

    $this->it( "builds the collection name with an alias", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" ) ->as( "c" );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select c.* from users as c;"
        );

    });

});