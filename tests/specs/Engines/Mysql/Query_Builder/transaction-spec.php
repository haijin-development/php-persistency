<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;

$spec->describe( "When evaluating transactional statements in a MySql database", function() {

    $this->before_each( function() {

        $this->re_populate_mysql_tables();

    });

    $this->after_all( function() {

        $this->re_populate_mysql_tables();

    });

    $this->let( "database", function() {

        $database = new Mysql_Database();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

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