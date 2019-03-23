<?php

use Haijin\Persistency\Engines\Postgresql\Postgresql_Database;
use Haijin\Errors\Haijin_Error;
use Haijin\Persistency\Errors\Connections\Named_Parameter_Not_Found_Error;

$spec->describe( "When evaluating an update statement in a Postgresql database", function() {

    $this->before_each( function() {

        $this->clear_postgresql_tables();

        pg_query(
            $this->postgresql, 
            "INSERT INTO users VALUES ( 1, 'Lisa', 'Simpson' );"
        );
        pg_query(
            $this->postgresql, 
            "INSERT INTO users VALUES ( 2, 'Bart', 'Simpson' );"
        );
        pg_query(
            $this->postgresql, 
            "INSERT INTO users VALUES ( 3, 'Maggie', 'Simpson' );"
        );

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

    $this->it( "updates a record with constant values", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "Marjorie" ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Maggie" )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
                "name" => "Marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates a record with a function", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->concat( "Mar", "jorie" ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Maggie" )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
                "name" => "Marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates a record with a nested function", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->concat( "Mar", $query->lower( "JORIE" ) ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Maggie" )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
                "name" => "Marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates a record with a unary function", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "MARJORIE" )->lower() ),
                $query->set( "last_name", $query->value( "Simpson" )->lower() )
            );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Maggie" )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
                "name" => "marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });


    $this->it( "updates a record with a binary operator", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( 3 ) ->op( "+" ) ->value( 4 ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Maggie" )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
                "name" => 7,
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates a record with a brackets", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->brackets(
                        $query->value( 3 ) ->op( "+" ) ->value( 4 )
                    )
                ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Maggie" )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
                "name" => 7,
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates a record with a null value", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( null ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Maggie" )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
                "name" => null,
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates many records", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( null ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

            $query->filter(
                $query->field( "id" ) ->op( ">" ) ->value( "1" )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
                "name" => null,
                "last_name" => "simpson"
            ],
            [
                "id" => 3,
                "name" => null,
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates a record with parameters", function() {

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->param( "name" ) ),
                $query->set( "last_name", $query->param( "last_name" ) )
            );

            $query->filter(
                $query->field( "id" ) ->op( "=" ) ->value( 3 )
            );

        }, [
            "name" => "Margaret",
            "last_name" => "simpson"
        ]);

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
                "name" => "Margaret",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "updates a record with a compiled statement", function() {

        $compiled_statement = $this->database->compile( function($compiler) {

            $compiler->update( function($query) {

                $query->collection( "users" );

                $query->record(
                    $query->set( "name", $query->param( "name" ) ),
                    $query->set( "last_name", $query->param( "last_name" ) )
                );

                $query->filter(
                    $query->field( "id" ) ->op( "=" ) ->value( 3 )
                );

            });

        });

        $this->database->execute( $compiled_statement, [
            "name" => "Margaret",
            "last_name" => "simpson"
        ]);

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
                "name" => "Margaret",
                "last_name" => "simpson"
            ],
        ]);



        $this->database->execute( $compiled_statement, [
            "name" => "margaret",
            "last_name" => "simpson"
        ]);

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" )
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
                "name" => "margaret",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "raises an error with missing parameters", function() {

        $this->expect( function() {

            $compiled_statement = $this->database->compile( function($compiler) {

                $compiler->update( function($query) {

                    $query->collection( "users" );

                    $query->record(
                        $query->set( "name", $query->param( "name" ) ),
                        $query->set( "last_name", $query->param( "last_name" ) )
                    );

                    $query->filter(
                        $query->field( "id" ) ->op( "=" ) ->value( 3 )
                    );

                });

            });

            $this->database->execute( $compiled_statement, [
                "name" => "Margaret"
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

                $compiler->update( function($query) {

                    $query->collection( "users" );

                    $query->record(
                        $query->set( "name", $query->param( "name" ) ),
                        $query->set( "last_name", $query->param( "last_name" ) )
                    );

                    $query->filter(
                        $query->field( "id" ) ->op( "=" ) ->value( 3 )
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