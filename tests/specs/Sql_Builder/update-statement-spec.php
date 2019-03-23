<?php

use Haijin\Persistency\Errors\Query_Expressions\Invalid_Expression_Error;
use Haijin\Persistency\Sql\Sql_Update_Statement_Builder;

$spec->describe( "When building an update statement of a sql expression", function() {

    $this->let( "query_builder", function() {
        return new Sql_Update_Statement_Builder();
    });

    $this->it( "builds the statement", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( 'name', 'Lisa' )
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "update users set name = 'Lisa';"
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
                    'The update statement is missing the $query->collection(...) expression.'
                );
            }
        );

    });

    $this->it( "raises an error if the record is not defined", function() {

        $this->expect( function() {

            $this->query_builder->build( function($query) {

                $query->collection( "users" );

            });

        }) ->to() ->raise(
            Invalid_Expression_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->equal(
                    'The update statement is missing the $query->record(...) expression.'
                );
            }
        );

    });

});