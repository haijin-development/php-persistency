<?php

use Haijin\Persistency\Engines\Postgresql\Postgresql_Database;
use Haijin\Errors\Haijin_Error;
use Haijin\Persistency\Errors\Connections\Named_Parameter_Not_Found_Error;

$spec->describe( "When evaluating a create statement in a Postgresql database", function() {

    $this->before_each( function() {

        $this->clear_postgresql_tables();

    });

    $this->after_all( function() {

        $this->clear_postgresql_tables();

    });

    $this->let( "database", function() {

        $database = new Postgresql_Database();

        $database->connect(
            "host=localhost port=5432 dbname=haijin-persistency user=haijin password=123456"
        );

        return $database;

    });

    $this->it( "returns the created id", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "Lisa" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $id = $this->database->get_last_created_id();

        $this->expect( $id ) ->to() ->equal( 1 );

    });

    $this->it( "creates a record with constant values", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "Lisa" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a function", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->concat( "Li", "sa" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a nested function", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->concat( "Li", $query->lower( "SA" ) ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a unary function", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->lower( "LISA" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "lisa",
                "last_name" => "Simpson"
            ],
        ]);

    });


    $this->it( "creates a record with a binary operator", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( 3 ) ->op( "+" ) ->value( 4 ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "7",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a brackets", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->brackets(
                        $query->value( 3 ) ->op( "+" ) ->value( 4 )
                    )
                ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "7",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with a null value", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( null ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => null,
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates a record with named parameters", function() {

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->param( "name" ) ),
                $query->set( "last_name", $query->param( "last_name" ) )
            );

        }, [
            "name" => "Homer",
            "last_name" => "Simpson"
        ]);

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Homer",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "creates records with a compiled statement", function() {

        $compiled_statement = $this->database->compile( function($compiler) {

            $compiler->create( function($query) {

                $query->collection( "users" );

                $query->record(
                    $query->set( "name", $query->param( "name" ) ),
                    $query->set( "last_name", $query->param( "last_name" ) )
                );

            });

        });

        $this->database->execute( $compiled_statement, [
            "name" => "Homer",
            "last_name" => "Simpson"
        ]);

        $this->database->execute( $compiled_statement, [
            "name" => "Marge",
            "last_name" => "Simpson"
        ]);

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 2,
                "name" => "Marge",
                "last_name" => "Simpson"
            ],
            [
                "id" => 1,
                "name" => "Homer",
                "last_name" => "Simpson"
            ],
        ]);

    });


    $this->it( "raises an error with missing parameters", function() {

        $this->expect( function() {

            $compiled_statement = $this->database->compile( function($compiler) {

                $compiler->create( function($query) {

                    $query->collection( "users" );

                    $query->record(
                        $query->set( "name", $query->param( "name" ) ),
                        $query->set( "last_name", $query->param( "last_name" ) )
                    );

                });

            });

            $this->database->execute( $compiled_statement, [
                "name" => "Homer"
            ]);

        }) ->to() ->raise(
            Named_Parameter_Not_Found_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->equal( 
                    "The query named parameter 'last_name' was not found."
                );
            }
        );
    });

    $this->it( "raises an error with invalid parameters", function() {

        $this->expect( function() {

            $compiled_statement = $this->database->compile( function($compiler) {

                $compiler->create( function($query) {

                    $query->collection( "users" );

                    $query->record(
                        $query->set( "name", $query->param( "name" ) ),
                        $query->set( "last_name", $query->param( "last_name" ) )
                    );

                });

            });

            $this->database->execute( $compiled_statement, '' );

        }) ->to() ->raise(
            Haijin_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->equal( 
                    "Expected named parameters to be an associative array."
                );
            }
        );
    });

});