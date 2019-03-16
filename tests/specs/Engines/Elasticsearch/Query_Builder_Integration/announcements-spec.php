<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;
use Haijin\Persistency\Announcements\About_To_Execute_Statement;

$spec->describe( "When a Elasticsearch database makes announcements events", function() {

    $this->let( "database", function() {

        $database = new Elasticsearch_Database();

        $database->connect( function($handle) {
            $handle->setHosts([ '127.0.0.1:9200' ]);
        });

        return $database;

    });

    $this->it( "announces the sql string and parameters before a select execution", function() {

        $this->expect( $this->database ) ->during( function() {

            $this->database->query( function($query) {

                $query->collection( "users" );

                $query->filter(
                    $query->field( "name" ) ->match( "Lisa" )
                );

            });

        }) ->to() ->announce(

            About_To_Execute_Statement::class,

            function($announcement){

                $this->expect( $announcement->__toString() ) ->to()
                    ->equal( 'Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database about to execute: \'search\' with parameters: \'{"index":"users","type":"users","body":{"query":{"match":{"name":"Lisa"}}},"sort":[]}\'' );

                $this->expect( $announcement->get_database_class() ) ->to()
                    ->equal( Elasticsearch_Database::class );

                $this->expect( $announcement->get_endpoint() ) ->to()
                    ->equal( 'search' );

                $this->expect( $announcement->get_parameters() ) ->to() ->be()
                    ->exactly_like([
                        "index" => "users",
                        "type" => "users",
                        "body" => [
                            "query" => [
                                "match" => [
                                    "name" => "Lisa"
                                ]
                            ]
                        ],
                        "sort" => []
                    ]);
        });

    });

    $this->it( "announces the sql string and parameters before a create execution", function() {

        $this->expect( $this->database ) ->during( function() {

            $this->database->create( function($query) {

                $query->collection( "users" );

                $query->record(
                    $query->set( "name" ,  "Lisa" )
                );

            });

        }) ->to() ->announce(

            About_To_Execute_Statement::class,

            function($announcement){

                $this->expect( $announcement->__toString() ) ->to()
                    ->equal( 'Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database about to execute: \'search\' with parameters: \'{"index":"users","type":"users","body":{"query":{"match":{"name":"Lisa"}}},"sort":[]}\'' );

                $this->expect( $announcement->get_database_class() ) ->to()
                    ->equal( Elasticsearch_Database::class );

                $this->expect( $announcement->get_endpoint() ) ->to()
                    ->equal( 'search' );

                $this->expect( $announcement->get_parameters() ) ->to() ->be()
                    ->exactly_like([
                        "index" => "users",
                        "type" => "users",
                        "body" => [
                            "query" => [
                                "match" => [
                                    "name" => "Lisa"
                                ]
                            ]
                        ],
                        "sort" => []
                    ]);
        });

    });

});