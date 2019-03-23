<?php

use Haijin\Persistency\Errors\Query_Expressions\Invalid_Expression_Error;
use Haijin\Persistency\Sql\Sql_Delete_Statement_Builder;

$spec->describe( "When building a delete statement of a sql expression", function() {

    $this->let( "query_builder", function() {
        return new Sql_Delete_Statement_Builder();
    });

    $this->it( "builds the statement", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

        });

        $this->expect( $sql ) ->to() ->equal(
            "delete from users;"
        );

    });

    $this->it( "raises an error if the collection is not defined", function() {

        $this->expect( function() {

            $this->query_builder->build( function($query) {

                $query->record(
                    $query->set( 'name', 'Lisa' )
                );

            });

        }) ->to() ->raise(
            Invalid_Expression_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->equal(
                    'The delete statement is missing the $query->collection(...) expression.'
                );
            }
        );

    });

});