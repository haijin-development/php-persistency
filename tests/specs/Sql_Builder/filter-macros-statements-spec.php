<?php

use Haijin\Persistency\Sql\Query_Builder\Sql_Builder;

$spec->describe( "When using macros in the filter statement of a sql expression", function() {

    $this->let( "query_builder", function() {
        return new Sql_Builder();
    });

    $this->it( "resolves the macro expression when referenced", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->let( "matches_name", function($query) { return $query
                ->field( "name" ) ->op( "=" ) ->value( "Lisa" );
            });

            $query->filter( $query
                ->matches_name
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where users.name = 'Lisa';"
        );

    });

    $this->it( "combines macro expression with a logical operand", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->let( "matches_name", function($query) { return $query
                ->field( "name" ) ->op( "=" ) ->value( "Lisa" );
            });

            $query->let( "matches_last_name", function($query) { return $query
                ->field( "last_name" ) ->op( "=" ) ->value( "Simpson" );
            });

            $query->filter(
                $query ->matches_name ->and() ->matches_last_name
            );

        });

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where users.name = 'Lisa' and users.last_name = 'Simpson';"
        );

    });

    $this->it( "raises an error if the macro expression is missing the return statement", function() {

        $this->expect( function() {

            $this->query_builder->build( function($query) {

                $query->collection( "users" );

                $query->let( "matches_name", function($query) { $query
                    ->field( "name" ) ->op( "=" ) ->value( "Lisa" );
                });

                $query->filter( $query
                    ->matches_name
                );

            });

        }) ->to() ->raise(
            \Haijin\Persistency\Errors\QueryExpressions\MacroExpressionEvaluatedToNullError::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->equal(
                    "The macro expression 'matches_name' evaluated to null. Probably it is missing the return statement."
                );
            }
        );

    });

});