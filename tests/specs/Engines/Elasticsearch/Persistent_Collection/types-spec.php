<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

$spec->describe( "When mapping fields with types in a Persistent_Collection stored in a Elasticsearch database", function() {

    $this->before_all( function() {

        $this->database = new Elasticsearch_Database();

        $this->database->connect( [ '127.0.0.1:9200' ] );

        Elasticsearch_Types_Collection::get()->set_database( $this->database );

    });

    $this->before_each( function() {

        Elasticsearch_Types_Collection::do()->clear_all();

    });

    $this->after_all( function() {

        Elasticsearch_Types_Collection::do()->clear_all();

    });

    $this->it( "makes no convertions if not type is defined", function() {

        Elasticsearch_Types_Collection::do()->create_from_attributes([
            "id" => 1,
            "no_type_field" => "no type"
        ]);

        $record = Elasticsearch_Types_Collection::get()->first();

        $this->expect( $record->no_type_field ) ->to() ->be( "===" ) ->than( "no type" );

    });

    $this->it( "converts to string", function() {

        Elasticsearch_Types_Collection::do()->create_from_attributes([
            "id" => 1,
            "string_field" => "123"
        ]);

        $record = Elasticsearch_Types_Collection::get()->first();

        $this->expect( $record->string_field ) ->to() ->be( "===" ) ->than( "123" );

    });

    $this->it( "converts to integer", function() {

        Elasticsearch_Types_Collection::do()->create_from_attributes([
            "id" => 1,
            "integer_field" => 123
        ]);

        $record = Elasticsearch_Types_Collection::get()->first();

        $this->expect( $record->integer_field ) ->to() ->be( "===" ) ->than( 123 );

    });

    $this->it( "converts to double", function() {

        Elasticsearch_Types_Collection::do()->create_from_attributes([
            "id" => 1,
            "double_field" => 123.0
        ]);

        $record = Elasticsearch_Types_Collection::get()->first();

        $this->expect( $record->double_field ) ->to() ->be( "===" ) ->than( 123.0 );

    });

    $this->it( "converts to boolean", function() {

        Elasticsearch_Types_Collection::do()->create_from_attributes([
            "id" => 1,
            "boolean_field" => true
        ]);

        $record = Elasticsearch_Types_Collection::get()->last();

        $this->expect( $record->boolean_field ) ->to() ->be() ->true();


        Elasticsearch_Types_Collection::do()->create_from_attributes([
            "id" => 1,
            "boolean_field" => false
        ]);

        $record = Elasticsearch_Types_Collection::get()->last();

        $this->expect( $record->boolean_field ) ->to() ->be() ->false();
    });

    $this->it( "converts to date", function() {

        $date = new \DateTime( "2010-01-31" );

        Elasticsearch_Types_Collection::do()->create_from_attributes([
            "id" => 1,
            "date_field" => $date
        ]);

        $record = Elasticsearch_Types_Collection::get()->first();

        $this->expect( $record->date_field ) ->to() ->equal( new \DateTime( "2010-01-31" ) );

    });

    $this->it( "converts to time", function() {

        $time = new \DateTime( "13:45:12" );

        Elasticsearch_Types_Collection::do()->create_from_attributes([
            "id" => 1,
            "time_field" => $time
        ]);

        $record = Elasticsearch_Types_Collection::get()->first();

        $this->expect( $record->time_field ) ->to() ->equal( new \DateTime( "13:45:12" ) );

    });

    $this->it( "converts to date_time", function() {

        $date_time = new \DateTime( "2010-01-31 13:45:12" );

        Elasticsearch_Types_Collection::do()->create_from_attributes([
            "id" => 1,
            "date_time_field" => $date_time
        ]);

        $record = Elasticsearch_Types_Collection::get()->first();

        $this->expect( $record->date_time_field )
            ->to() ->equal( new \DateTime( "2010-01-31 13:45:12" ) );

    });

    $this->it( "converts to json", function() {

        $json_array = [ 1, 2, 3 ];

        Elasticsearch_Types_Collection::do()->create_from_attributes([
            "id" => 1,
            "json_field" => $json_array
        ]);

        $record = Elasticsearch_Types_Collection::get()->first();

        $this->expect( $record->json_field )->to() ->equal( [ 1, 2, 3 ] );

    });

});