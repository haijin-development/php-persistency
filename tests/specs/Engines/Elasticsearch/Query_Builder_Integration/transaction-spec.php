<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;
use Haijin\Errors\Haijin_Error;

$spec->describe( "When evaluating transactional statements in a Elasticsearch database", function() {

    $this->before_each( function() {

        $this->clear_elasticsearch_indices();

    });

    $this->after_all( function() {

        $this->clear_elasticsearch_indices();

    });

    $this->let( "database", function() {

        $database = new Elasticsearch_Database();

        $database->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

        return $database;

    });

    $this->it( "commits a transaction", function() {

        $this->database->begin_transaction();

        $this->database->create( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->set( "_id", $query->value( 1 ) ),
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
                "_id" => 1,
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
                $query->set( "_id", $query->value( 1 ) ),
                $query->set( "name", $query->value( "Margaret" ) ),
                $query->set( "last_name", $query->value( "Simpson" ) )
            );

        });

        $this->database->rollback_transaction();

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "_id" => 1,
                "name" => "Margaret",
                "last_name" => "Simpson"
            ],
        ]);

    });

    $this->it( "commits a statement within a transaction", function() {

        $this->database->during_transaction_do( function($database) {

            $database->create( function($query) {

                $query->collection( "users" );

                $query->record(
                    $query->set( "_id", $query->value( 1 ) ),
                    $query->set( "name", $query->value( "Margaret" ) ),
                    $query->set( "last_name", $query->value( "simpson" ) )
                );

            });

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "_id" => 1,
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
                        $query->set( "_id", $query->value( 1 ) ),
                        $query->set( "name", $query->value( "Margaret" ) ),
                        $query->set( "last_name", $query->value( "simpson" ) )
                    );

                });

                throw new Haijin_Error( "Error Processing Request" );

            });

        } catch( \RuntimeException $e ) {

        }

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "_id" => 1,
                "name" => "Margaret",
                "last_name" => "simpson"
            ],
        ]);

    });

    $this->it( "re raises an error within a transaction", function() {

        $re_raised_exception = false;
        try {

            $this->database->during_transaction_do( function($database) {

                $database->create( function($query) {

                    $query->collection( "users" );

                    $query->record(
                        $query->set( "_id", $query->value( 1 ) ),
                        $query->set( "name", $query->value( "Margaret" ) ),
                        $query->set( "last_name", $query->value( "simpson" ) )
                    );

                });

                throw new Haijin_Error( "Error Processing Request" );

            });

        } catch( \RuntimeException $e ) {
            $re_raised_exception = true;
        }

        $this->expect( $re_raised_exception ) ->to() ->be() ->true();

    });

});