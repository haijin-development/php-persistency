<?php

use Haijin\Persistency\Engines\Postgresql\Postgresql_Database;

$spec->describe( "When evaluating transactional statements in a Postgresql database", function() {

    $this->before_each( function() {

        $this->re_populate_postgres_tables();

    });

    $this->after_all( function() {

        $this->re_populate_postgres_tables();

    });

    $this->let( "database", function() {

        $database = new Postgresql_Database();

        $database->connect(
            "host=localhost port=5432 dbname=haijin-persistency user=haijin password=123456"
        );

        return $database;

    });

    $this->it( "commits a transaction", function() {

        $this->database->begin_transaction();

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "Marjorie" ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

            $query->filter(
                $query->field( "id" ) ->op( "=" ) ->value( 3 )
            );

        });

        $this->database->commit_transaction();

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "id" ) ->op( "=" ) ->value( 3 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 3,
                "name" => "Marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "rolls back a transaction", function() {

        $this->database->begin_transaction();

        $this->database->update( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "Marjorie" ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

            $query->filter(
                $query->field( "id" ) ->op( "=" ) ->value( 3 )
            );

        });

        $this->database->rollback_transaction();

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "id" ) ->op( "=" ) ->value( 3 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "commits a statement within a transaction", function() {

        $this->database->during_transaction_do( function($database) {

            $database->update( function($query) {

                $query->collection( "users" );

                $query->record(
                    $query->set( "name", $query->value( "Marjorie" ) ),
                    $query->set( "last_name", $query->value( "simpson" ) )
                );

                $query->filter(
                    $query->field( "id" ) ->op( "=" ) ->value( 3 )
                );

            });

        }, $this );


        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "id" ) ->op( "=" ) ->value( 3 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 3,
                "name" => "Marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "roll backs a statement within a transaction", function() {

        try {

            $this->database->during_transaction_do( function($database) {

                $database->update( function($query) {

                    $query->collection( "users" );

                    $query->record(
                        $query->set( "name", $query->value( "Marjorie" ) ),
                        $query->set( "last_name", $query->value( "simpson" ) )
                    );

                    $query->filter(
                        $query->field( "id" ) ->op( "=" ) ->value( 3 )
                    );

                });

                throw new \RuntimeException( "Error Processing Request" );

            }, $this );

        } catch( \RuntimeException $e ) {

        }

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "id" ) ->op( "=" ) ->value( 3 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "re raises an error within a transaction", function() {

        $re_raised_exception = false;
        try {

            $this->database->during_transaction_do( function($database) {

                $database->update( function($query) {

                    $query->collection( "users" );

                    $query->record(
                        $query->set( "name", $query->value( "Marjorie" ) ),
                        $query->set( "last_name", $query->value( "simpson" ) )
                    );

                    $query->filter(
                        $query->field( "id" ) ->op( "=" ) ->value( 3 )
                    );

                });

                throw new \RuntimeException( "Error Processing Request" );

            }, $this );

        } catch( \RuntimeException $e ) {
            $re_raised_exception = true;
        }

        $this->expect( $re_raised_exception ) ->to() ->be() ->true();

    });

});