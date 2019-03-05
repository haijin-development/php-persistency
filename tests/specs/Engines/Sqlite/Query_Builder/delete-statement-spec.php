<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;

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
            'parameters' => [
                "name" => "Maggie"
            ]
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
            'parameters' => [
                "name" => "Maggie"
            ]
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
            'parameters' => [
                "name" => "Bart"
            ]
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

});