<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;
use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;

$spec->describe( "When asking if exists a record with a given id in a Sqlite collection", function() {

    $this->before_all( function() {

        $this->database = new Sqlite_Database();

        $this->database->connect( $this->sqlite_file );

        Users_Collection::get()->set_database( $this->database );

        Users_Collection::do()->clear_all();

        Users_Collection::do()->create_from_attributes([
            "name" => "Lisa",
            "last_name" => "Simpson"
        ]);

    });

    $this->after_all( function() {

        Users_Collection::do()->clear_all();

    });

    $this->it( "returns true if the record exists", function() {

        $exists = Users_Collection::get()->exists_with_id( 1 );

        $this->expect( $exists ) ->to() ->be() ->true();

    });

    $this->it( "returns false if the record does not exist", function() {

        $exists = Users_Collection::get()->exists_with_id( 2 );

        $this->expect( $exists ) ->to() ->be() ->false();

    });

});