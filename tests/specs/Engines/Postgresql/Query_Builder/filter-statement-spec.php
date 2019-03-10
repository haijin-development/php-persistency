<?php

use Haijin\Persistency\Engines\Postgresql\Postgresql_Database;

$spec->describe( "When building the filter statement of a Postgresql expression", function() {

    $this->let( "database", function() {

        $database = new Postgresql_Database();

        $database->connect(
            "host=localhost port=5432 dbname=haijin-persistency user=haijin password=123456"
        );

        return $database;

    });

    $this->it( "builds a relative field expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds an absolute field expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" ) ->as( "u" );

            $query->filter(
                $query ->field( "u.name" ) ->op( "=" ) ->value( "Lisa" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds a constant value expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds a function with values", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->field( "name" ) ->op( "=" ) ->concat( "Lis", "a" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds a nested function expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->field( "name" )
                ->op( "=" )
                ->concat( $query->value( "Lis" ), $query->lower("A") )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds a binary opetor expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->field( "id" )
                ->op( "=" )
                ->brackets( $query->value( 1 ) ->op( "+" ) ->value( 2 ) )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds a is null expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->field( "id" ) ->is_null()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([]);

    });

    $this->it( "builds a is not null expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->field( "id" ) ->is_not_null()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
            [
                "id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson"
            ],
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds a unary function expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->field( "name" )->upper() ->op( "=" ) ->value( "LISA" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds a brackets expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->field( "id" )
                ->op( "=" )
                ->value( 3 )
                ->op( "*" )
                ->brackets(
                    $query->value( 2 )
                    ->op( "-" )
                    ->value( 1 )
                )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds an and expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->field( "last_name" ) ->op( "=" ) ->value( "Simpson" )
                ->and()
                ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds an or expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Lisa" )
                ->or()
                ->field( "name" ) ->op( "=" ) ->value( "Maggie" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds named parameters", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Lisa" )
                ->or()
                ->field( "name" ) ->op( "=" ) ->param( "name" )
            );
        },
        [
            "name" => "Maggie"
        ]);

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds named parameters", function() {

        $this->expect( function() {

            $this->database->query( function($query) {

                $query->collection( "users_read_only" );

                $query->filter(
                    $query->field( "name" ) ->op( "=" ) ->value( "Lisa" )
                    ->or()
                    ->field( "name" ) ->op( "=" ) ->param( "name" )
                );

            });

        }) ->to() ->raise(
            \Haijin\Persistency\Errors\Connections\Named_Parameter_Not_Found_Error::class,
            function($error) {

                $this->expect( $error->getMessage() ) ->to() ->equal(
                    "The query named parameter 'name' was not found."
                );

                $this->expect( $error->get_parameter_name() ) ->to() ->equal(
                    "name"
                );
        });

    });

    $this->describe( "when compiling the statement once and evaluating it many times", function() {

        $this->it( "get the results for each evaluation", function() {

            $compiled_statement = $this->database->compile( function($compiler) {

                $compiler->query( function($query) {

                    $query->collection( "users_read_only" );

                    $query->filter(
                        $query ->field( "name" ) ->op( "=" ) ->param( "name" )
                    );

                });

            });

            $rows = $this->database->execute( $compiled_statement, [
                "name" => "Lisa"
            ]);

            $this->expect( $rows ) ->to() ->be() ->exactly_like([
                [
                    "id" => 1,
                    "name" => "Lisa",
                    "last_name" => "Simpson"
                ]
            ]);

            $rows = $this->database->execute( $compiled_statement, [
                "name" => "Maggie"
            ]);

            $this->expect( $rows ) ->to() ->be() ->exactly_like([
                [
                    "id" => 3,
                    "name" => "Maggie",
                    "last_name" => "Simpson"
                ]
            ]);

        });

    });

});