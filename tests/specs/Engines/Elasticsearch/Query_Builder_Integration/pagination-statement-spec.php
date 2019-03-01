<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->describe( "When building the pagination statement of a Elasticsearch expression", function() {

    $this->let( "database", function() {

        $database = new Elasticsearch_Database();

        $database->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

        return $database;

    });

    $this->it( "raises an error if only the offset is defined", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->order_by(
                $query->field( 'id' )
            );

            $query->pagination(
                $query->offset( 2 )
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

    $this->it( "builds the limit statement", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->order_by(
                $query->field( 'id' )
            );

            $query->pagination(
                $query->limit( 1 )
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

    $this->it( "builds the limit and offset statement", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->order_by(
                $query->field( 'id' )
            );

            $query->pagination(
                $query
                    ->offset( 1 )
                    ->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "raises an error if only the page is defined", function() {

        $this->expect( function() {

            $this->database->query( function($query) {

                $query->collection( "users_read_only" );

                $query->order_by(
                    $query->field( 'id' )
                );

                $query->pagination(
                    $query->page( 1 )
                );

            });

        }) ->to() ->be() ->raise(
            \Haijin\Persistency\Errors\Query_Expressions\Missing_Page_Size_Expression_Error::class,
            function($error) {

                $this->expect( $error->getMessage() ) ->to() ->equal(
                    "The 'page' expression must have a 'page_size' expression as well. Please define a '\$query->page_size(\$n)' expression."
                );
            }
        );

    });

    $this->it( "raises an error if only the page_size is defined", function() {

        $this->expect( function() {

            $this->database->query( function($query) {

                $query->collection( "users_read_only" );

                $query->pagination(
                    $query->page_size( 10 )
                );

            });

        }) ->to() ->be() ->raise(
            \Haijin\Persistency\Errors\Query_Expressions\Missing_Page_Number_Expression_Error::class,
            function($error) {

                $this->expect( $error->getMessage() ) ->to() ->equal(
                    "The 'page_size' expression must have a 'page' expression as well. Please define a '\$query->page(\$n)' expression."
                );
            }
        );

    });

    $this->it( "builds the page and size statements", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->order_by(
                $query->field( 'id' )
            );

            $query->pagination(
                $query
                    ->page( 1 )
                    ->page_size( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson"
            ]
        ]);

    });

});