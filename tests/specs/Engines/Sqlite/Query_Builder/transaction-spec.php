<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;

$spec->describe( "When evaluating transactional statements in a Sqlite database", function() {

    $this->before_each( function() {

        $this->clear_sqlite_tables();

    });

    $this->after_all( function() {

        $this->clear_sqlite_tables();

    });

    $this->let( "database", function() {

        $database = new Sqlite_Database();

        $database->connect( $this->sqlite_file );

        return $database;

    });

    $this->it( "commits a transaction", function() {

        $this->database->begin_transaction();

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "Marjorie" ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

        });

        $this->database->commit_transaction();

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "rolls back a transaction", function() {

        $this->database->begin_transaction();

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "Marjorie" ) ),
                $query->set( "last_name", $query->value( "simpson" ) )
            );

        });

        $this->database->rollback_transaction();

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like( [] );

    });

    $this->it( "commits a statement within a transaction", function() {

        $this->database->during_transaction_do( function($database) {

            $database->create( function($query) {

                $query->collection( "users" );

                $query->record(
                    $query->set( "name", $query->value( "Marjorie" ) ),
                    $query->set( "last_name", $query->value( "simpson" ) )
                );

            });

        }, $this );


        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Marjorie",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "roll backs a statement within a transaction", function() {

        try {

            $this->database->during_transaction_do( function($database) {

                $database->create( function($query) {

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

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like( [] );

    });

    $this->it( "re raises an error within a transaction", function() {

        $re_raised_exception = false;
        try {

            $this->database->during_transaction_do( function($database) {

                $database->create( function($query) {

                    $query->collection( "users" );

                    $query->record(
                        $query->set( "name", $query->value( "Marjorie" ) ),
                        $query->set( "last_name", $query->value( "simpson" ) )
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