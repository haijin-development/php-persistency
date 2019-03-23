<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;
use Haijin\Errors\Haijin_Error;
use Haijin\Persistency\Errors\Connections\Named_Parameter_Not_Found_Error;

$spec->describe( "When evaluating a delete statement in a MySql database", function() {

    $this->before_each( function() {

        $this->clear_sqlite_tables();

        $this->sqlite->query(
            "INSERT INTO users VALUES ( 1, 'Lisa', 'Simpson', null );"
        );
        $this->sqlite->query(
            "INSERT INTO users VALUES ( 2, 'Bart', 'Simpson', null );"
        );
        $this->sqlite->query(
            "INSERT INTO users VALUES ( 3, 'Maggie', 'Simpson', null );"
        );

    });

    $this->after_all( function() {

        $this->clear_sqlite_tables();

    });

    $this->let( "database", function() {

        $database = new Sqlite_Database();

        $database->connect( $this->sqlite_file );

        return $database;

    });

    $this->it( "deletes a record with constant values", function() {

        $this->database->delete( function($query) {

            $query->collection( "users" );

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
            ]
        ]);

    });

    $this->it( "deletes many records", function() {

        $this->database->delete( function($query) {

            $query->collection( "users" );

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
            ]
        ]);

    });

    $this->it( "deletes a record with parameters", function() {

        $this->database->delete( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->param( "name" )
            );

        }, [
            "name" => "Maggie"
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
            ]
        ]);

    });

    $this->it( "deletes a record with a compiled query", function() {

        $compiled_query = $this->database->compile( function($compiler) {

            $compiler->delete( function($query) {

                $query->collection( "users" );

                $query->filter(
                    $query->field( "name" ) ->op( "=" ) ->param( "name" )
                );

            });

        });

        $this->database->execute( $compiled_query, [
            "name" => "Maggie"
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
            ]
        ]);


        $this->database->execute( $compiled_query, [
            "name" => "Bart"
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
            ]
        ]);
    });

    $this->it( "raises an error with missing parameters", function() {

        $this->expect( function() {

            $compiled_query = $this->database->compile( function($compiler) {

                $compiler->delete( function($query) {

                    $query->collection( "users" );

                    $query->filter(
                        $query->field( "name" ) ->op( "=" ) ->param( "name" )
                    );

                });

            });

            $this->database->execute( $compiled_query, [] );

        }) ->to() ->raise(
            Named_Parameter_Not_Found_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->equal( 
                    "The query named parameter 'name' was not found."
                );
            }
        );
    });

    $this->it( "raises an error with invalid parameters", function() {

        $this->expect( function() {

            $compiled_query = $this->database->compile( function($compiler) {

                $compiler->delete( function($query) {

                    $query->collection( "users" );

                    $query->filter(
                        $query->field( "name" ) ->op( "=" ) ->param( "name" )
                    );

                });

            });

            $this->database->execute( $compiled_query, "not-an-array" );

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