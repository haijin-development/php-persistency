<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;
use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;

$spec->describe( "When asking if exists a record with a condition in a MySql collection", function() {

    $this->before_all( function() {

        $this->database = new Mysql_Database();

        $this->database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

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

        $exists = Users_Collection::get()->exists( function($query) {

            $query->filter(
                $query ->field( 'name', '=', 'Lisa' )
                ->and()
                ->field( 'last_name', '=', 'Simpson' )
            );

        });

        $this->expect( $exists ) ->to() ->be() ->true();

    });

    $this->it( "returns false if the record does not exist", function() {

        $exists = Users_Collection::get()->exists( function($query) {

            $query->filter(
                $query ->field( 'name', '=', 'Bart' )
                ->and()
                ->field( 'last_name', '=', 'Simpson' )
            );

        });

        $this->expect( $exists ) ->to() ->be() ->false();

    });

});