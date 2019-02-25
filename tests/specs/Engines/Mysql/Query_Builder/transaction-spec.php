<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;

$spec->describe( "When evaluating transactional statements in a MySql database", function() {

    $this->before_each( function() {

        $this->clear_mysql_tables();

    });

    $this->after_all( function() {

        $this->clear_mysql_tables();

    });

    $this->let( "database", function() {

        $database = new Mysql_Database();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        return $database;

    });

    $this->it( "commits a transaction", function() {

        $this->database->begin_transaction();

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "Margaret" ) ),
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
                "name" => "Margaret",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "rolls back a transaction", function() {

        $this->database->begin_transaction();

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "name", $query->value( "Margaret" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
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
                    $query->set( "name", $query->value( "Margaret" ) ),
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
                "name" => "Margaret",
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
                        $query->set( "name", $query->value( "Margaret" ) ),
                        $query->set( "last_name", $query->value( "simpson" ) )
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
                        $query->set( "name", $query->value( "Margaret" ) ),
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