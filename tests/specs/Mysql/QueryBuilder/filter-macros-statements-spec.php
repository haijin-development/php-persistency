<?php

use Haijin\Persistency\Mysql\MysqlDatabase;

$spec->describe( "When using macros in the filter statement of a Mysql expression", function() {

    $this->let( "database", function() {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        return $database;
    });

    $this->it( "resolves the macro expression when referenced", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->let( "matches_name", function($query) { return $query
                ->field( "name" ) ->op( "=" ) ->value( "Lisa" );
            });

            $query->filter( $query
                ->matches_name
            );
        });

        $this->expect( $rows ) ->to() ->equal([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "combines macro expression with a logical operand", function() {

        $rows = $this->database->query( function($query) {

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

        $this->expect( $rows ) ->to() ->equal([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "raises an error if the macro expression is missing the return statement", function() {

        $this->expect( function() {

            $this->database->query( function($query) {

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