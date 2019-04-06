<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;
use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;

$spec->describe( "When querying a Mysql Persistent_Collection using with", function() {

    $this->before_all( function() {

        $this->database = new Mysql_Database();

        $this->database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        Users_2_Collection::get()->set_database( $this->database );
        Addresses_2_Collection::get()->set_database( $this->database );

        Users_2_Collection::do()->clear_all();

        $lisa = Users_2_Collection::do()->create_from_attributes([
            "name" => "Lisa",
            "last_name" => "Simpson"
        ]);

        $bart = Users_2_Collection::do()->create_from_attributes([
            "name" => "Bart",
            "last_name" => "Simpson"
        ]);

        $maggie = Users_2_Collection::do()->create_from_attributes([
            "name" => "Maggie",
            "last_name" => "Simpson"
        ]);

        Addresses_2_Collection::do()->create_from_attributes([
            "user_id" => $lisa,
            "street_1" => "Evergreen"
        ]);

        Addresses_2_Collection::do()->create_from_attributes([
            "user_id" => $bart,
            "street_1" => "Evergreen"
        ]);

        Addresses_2_Collection::do()->create_from_attributes([
            "user_id" => $maggie,
            "street_1" => "Evergreen 742"
        ]);

    });

    $this->after_all( function() {

        Users_2_Collection::do()->clear_all();

    });

    $this->describe( "joins with another collection", function() {

        $this->it( "gets all the objects in the collection", function() {

            $users = Users_2_Collection::get()->all( function($query) {

                $query->with( 'address_2' ) ->as( 'address' ) ->eval( function($query) {

                    $query->proyect( $query->ignore() );

                    $query->let( 'matches_address', function($query) {
                        return $query->field( 'street_1', '=', 'Evergreen' );
                    });

                });

                $query->filter(
                    $query->matches_address
                );

            });

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 1,
                    "get_name()" => "Lisa",
                    "get_last_name()" => "Simpson"
                ],
                [
                    "get_id()" => 2,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson"
                ]
            ]);

        });

    });

});