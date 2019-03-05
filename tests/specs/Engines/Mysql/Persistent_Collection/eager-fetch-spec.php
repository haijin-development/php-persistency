<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

$spec->describe( "When mapping fields to another collections in a MySql database", function() {

    $this->before_all( function() {

        $this->database = new Mysql_Database();

        $this->database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        Users_Collection::get()->set_database( $this->database );
        Addresses_Collection::get()->set_database( $this->database );

    });

    $this->before_each( function() {

        Users_Collection::do()->clear_all();
        Addresses_Collection::do()->clear_all();

    });

    $this->after_all( function() {

        Users_Collection::do()->clear_all();
        Addresses_Collection::do()->clear_all();

    });

    $this->describe( "with a reference to an object in another collection", function() {

        $this->it( "eager fetchs to null", function() {

            Users_Collection::do()->create_from_attributes([
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]);

            $user = Users_Collection::get()->first( null, [
                'eager_fetch' => [
                    'address_id' => true
                ]
            ]);

            $this->expect( $user->get_address() ) ->to() ->be() ->null();

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson",
                "get_address()" => null,                
            ]);

        });

        $this->it( "eager fetchs to the objects", function() {

            $address = Addresses_Collection::do()->create_from_attributes([
                "street_1" => "Evergreen 742",
            ]);

            Users_Collection::do()->insert_record([
                "name" => "Lisa",
                "last_name" => "Simpson",
                "address_id" => $address->get_id()
            ]);

            $user = Users_Collection::get()->first( null, [
                'eager_fetch' => [
                    'address_id' => true
                ]
            ]);

            $this->expect( $user->get_address() ) ->to() ->be() ->a(
                \Address::class
            );

        });

    });

});