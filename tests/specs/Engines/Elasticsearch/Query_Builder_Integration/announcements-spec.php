<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;
use Haijin\Persistency\Announcements\About_To_Execute_Statement;

$spec->describe( "When a Elasticsearch database makes announcements", function() {

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

                $this->expect( $announcement->get_announcer_print_string() ) ->to()
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
                    $query->set( "_id" ,  1 ),
                    $query->set( "name" ,  "Lisa" )
                );

            });

        }) ->to() ->announce(

            About_To_Execute_Statement::class,

            function($announcement){

                $this->expect( $announcement->__toString() ) ->to()
                    ->equal( 'Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database about to execute: \'index\' with parameters: \'{"index":"users","type":"users","id":1,"body":{"name":"Lisa"},"refresh":true}\'' );

                $this->expect( $announcement->get_announcer_print_string() ) ->to()
                    ->equal( Elasticsearch_Database::class );

                $this->expect( $announcement->get_endpoint() ) ->to()
                    ->equal( 'index' );

                $this->expect( $announcement->get_parameters() ) ->to() ->be()
                    ->exactly_like([
                        "index" => "users",
                        "type" => "users",
                        "id" => 1,
                        "body" => [
                            "name" => "Lisa"
                        ],
                        "refresh" => true
                    ]);
        });

    });

    $this->it( "announces the sql string and parameters before an update execution", function() {

        $this->expect( $this->database ) ->during( function() {

            $this->database->update( function($query) {

                $query->collection( "users" );

                $query->script([
                    "lang" => "painless",
                    "source" => "ctx._source.name = 'Margaret'"
                ]);

                $query->filter(
                    $query->match( "name", "Maggie" )
                );

            });

        }) ->to() ->announce(

            About_To_Execute_Statement::class,

            function($announcement){

                $this->expect( $announcement->__toString() ) ->to()
                    ->equal( 'Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database about to execute: \'updateByQuery\' with parameters: \'{"index":"users","type":"users","body":{"query":{"match":{"name":"Maggie"}},"script":{"lang":"painless","source":"ctx._source.name = \'Margaret\'"}},"refresh":true}\'' );

                $this->expect( $announcement->get_announcer_print_string() ) ->to()
                    ->equal( Elasticsearch_Database::class );

                $this->expect( $announcement->get_endpoint() ) ->to()
                    ->equal( 'updateByQuery' );

                $this->expect( $announcement->get_parameters() ) ->to() ->be()
                    ->exactly_like([
                        "index" => "users",
                        "type" => "users",
                        "body" => [
                            "query" => [
                                "match" => [
                                    "name" => "Maggie"
                                ]
                            ]
                        ],
                        "refresh" => true
                    ]);
        });

    });

    $this->it( "announces the sql string and parameters before a delete execution", function() {

        $this->expect( $this->database ) ->during( function() {

            $this->database->delete( function($query) {

                $query->collection( "users" );

                $query->filter(
                    $query->field( "name" ) ->match( "Maggie" )
                );

            });

        }) ->to() ->announce(

            About_To_Execute_Statement::class,

            function($announcement){

                $this->expect( $announcement->__toString() ) ->to()
                    ->equal( 'Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database about to execute: \'deleteByQuery\' with parameters: \'{"index":"users","type":"users","body":{"query":{"match":{"name":"Maggie"}}},"refresh":true}\'' );

                $this->expect( $announcement->get_announcer_print_string() ) ->to()
                    ->equal( Elasticsearch_Database::class );

                $this->expect( $announcement->get_endpoint() ) ->to()
                    ->equal( 'deleteByQuery' );

                $this->expect( $announcement->get_parameters() ) ->to() ->be()
                    ->exactly_like([
                        "index" => "users",
                        "type" => "users",
                        "body" => [
                            "query" => [
                                "match" => [
                                    "name" => "Maggie"
                                ]
                            ]
                        ],
                        "refresh" => true
                    ]);
        });

    });

});