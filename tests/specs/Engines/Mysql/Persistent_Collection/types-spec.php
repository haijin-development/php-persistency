<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;
use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;

$spec->describe( "When mapping fields with types in a Persistent_Collection stored in a MySql database", function() {

    $this->before_all( function() {

        $this->database = new Mysql_Database();

        $this->database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        Types_Collection::get()->set_database( $this->database );

    });

    $this->before_each( function() {

        Types_Collection::do()->clear_all();

    });

    $this->after_all( function() {

        Types_Collection::do()->clear_all();

    });

    $this->it( "makes no convertions if not type is defined", function() {

        Types_Collection::do()->create_from_attributes([
            "no_type_field" => "no type"
        ]);

        $record = Types_Collection::get()->first();

        $this->expect( $record->no_type_field ) ->to() ->be( "===" ) ->than( "no type" );

    });

    $this->it( "converts to string", function() {

        Types_Collection::do()->create_from_attributes([
            "string_field" => "123"
        ]);

        $record = Types_Collection::get()->first();

        $this->expect( $record->string_field ) ->to() ->be( "===" ) ->than( "123" );

    });

    $this->it( "converts to integer", function() {

        Types_Collection::do()->create_from_attributes([
            "integer_field" => 123
        ]);

        $record = Types_Collection::get()->first();

        $this->expect( $record->integer_field ) ->to() ->be( "===" ) ->than( 123 );

    });

    $this->it( "converts to double", function() {

        Types_Collection::do()->create_from_attributes([
            "double_field" => 123.0
        ]);

        $record = Types_Collection::get()->first();

        $this->expect( $record->double_field ) ->to() ->be( "===" ) ->than( 123.0 );

    });

    $this->it( "converts to boolean", function() {

        Types_Collection::do()->create_from_attributes([
            "boolean_field" => true
        ]);

        $record = Types_Collection::get()->last();

        $this->expect( $record->boolean_field ) ->to() ->be() ->true();


        Types_Collection::do()->create_from_attributes([
            "boolean_field" => false
        ]);

        $record = Types_Collection::get()->last();

        $this->expect( $record->boolean_field ) ->to() ->be() ->false();
    });

    $this->it( "converts to date", function() {

        $date = new \DateTime( "2010-01-31" );

        Types_Collection::do()->create_from_attributes([
            "date_field" => $date
        ]);

        $record = Types_Collection::get()->first();

        $this->expect( $record->date_field ) ->to() ->equal( new \DateTime( "2010-01-31" ) );

    });

    $this->it( "converts to time", function() {

        $time = new \DateTime( "13:45:12" );

        Types_Collection::do()->create_from_attributes([
            "time_field" => $time
        ]);

        $record = Types_Collection::get()->first();

        $this->expect( $record->time_field ) ->to() ->equal( new \DateTime( "13:45:12" ) );

    });

    $this->it( "converts to date_time", function() {

        $date_time = new \DateTime( "2010-01-31 13:45:12" );

        Types_Collection::do()->create_from_attributes([
            "date_time_field" => $date_time
        ]);

        $record = Types_Collection::get()->first();

        $this->expect( $record->date_time_field )
            ->to() ->equal( new \DateTime( "2010-01-31 13:45:12" ) );

    });

    $this->it( "converts to json", function() {

        $json_array = [ 1, 2, 3 ];

        Types_Collection::do()->create_from_attributes([
            "json_field" => $json_array
        ]);

        $record = Types_Collection::get()->first();

        $this->expect( $record->json_field )->to() ->equal( [ 1, 2, 3 ] );

    });

});