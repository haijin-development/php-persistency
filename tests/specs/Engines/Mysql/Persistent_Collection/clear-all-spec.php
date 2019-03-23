<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

$spec->describe( "When deleting an object from a Persistent_Collection in a MySql database", function() {

    $this->before_all( function() {

        $this->database = new Mysql_Database();

        $this->database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        Users_Collection::get()->set_database( $this->database );

    });

    $this->before_each( function() {

        Users_Collection::do()->clear_all();

        Users_Collection::do()->create_from_attributes([
            "id" => 1,
            "name" => "Lisa",
            "last_name" => "Simpson",
        ]);

        Users_Collection::do()->create_from_attributes([
            "id" => 2,
            "name" => "Bart",
            "last_name" => "Simpson",
        ]);

        Users_Collection::do()->create_from_attributes([
            "id" => 3,
            "name" => "Maggie",
            "last_name" => "Simpson",
        ]);

    });

    $this->after_all( function() {

        Users_Collection::do()->clear_all();

    });

    $this->it( "deletes all objects", function() {

        $users = Users_Collection::do()->clear_all();

        $this->expect( $users ) ->to() ->be() ->exactly_like( [] );

    });

});