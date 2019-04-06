<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;
use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;

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

        $this->it( "resolves the reference to null", function() {

            Users_Collection::do()->create_from_attributes([
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]);

            $user = Users_Collection::get()->last();

            $this->expect( $user->get_address() ) ->to() ->be() ->null();

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson",
                "get_address()" => null,                
            ]);

        });

        $this->it( "resolves the reference to an object by its id", function() {

            $address = Addresses_Collection::do()->create_from_attributes([
                "street_1" => "Evergreen 742",
            ]);

            Users_Collection::do()->insert_record([
                "name" => "Lisa",
                "last_name" => "Simpson",
                "address_id" => $address->get_id()
            ]);

            $user = Users_Collection::get()->last();

            $this->expect( $user->get_address() ) ->to() ->be() ->a(
                \Haijin\Persistency\Persistent_Collection\Reference_Proxies\Object_In_Collection_Proxy::class
            );


            $address = $user->get_address()->resolve_reference();

            $this->expect( $address ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_street_1()" => 'Evergreen 742',
                "get_street_2()" => null,
                "get_city()" => null
            ]);

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson",
                "get_address()" => [
                    "get_id()" => 1,
                    "get_street_1()" => 'Evergreen 742',
                    "get_street_2()" => null,
                    "get_city()" => null
                ]
            ]);

        });

        $this->it( "resolves the reference on the first message received", function() {

            $address = Addresses_Collection::do()->create_from_attributes([
                "street_1" => "Evergreen 742",
            ]);

            Users_Collection::do()->insert_record([
                "name" => "Lisa",
                "last_name" => "Simpson",
                "address_id" => $address->get_id()
            ]);

            $user = Users_Collection::get()->last();

            $this->expect( $user->get_address() ) ->to() ->be() ->a(
                \Haijin\Persistency\Persistent_Collection\Reference_Proxies\Object_In_Collection_Proxy::class
            );

            $user->get_address()->get_id();

            $this->expect( $user->get_address() ) ->to() ->be() ->a(
                \Address::class
            );

        });

    });

    $this->describe( "with a reference from an object in another collection", function() {

        $this->it( "resolves the reference to null", function() {

            Users_Collection::do()->create_from_attributes([
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]);

            $user = Users_Collection::get()->last();

            $address = $user->get_address_2()->resolve_reference();

            $this->expect( $address ) ->to() ->be() ->null();

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson",
                "get_address()" => null,
                "get_address_2()" => null,
            ]);

        });

        $this->it( "resolves the reference to an object", function() {

            $user = Users_Collection::do()->create_from_attributes([
                'name' => 'Lisa',
                'last_name' => 'Simpson'
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "user_id" => $user->get_id(),
                "street_1" => "Evergreen 742",
            ]);

            $user = Users_Collection::get()->last();

            $address = $user->get_address_2()->resolve_reference();

            $this->expect( $address ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_street_1()" => 'Evergreen 742',
                "get_street_2()" => null,
                "get_city()" => null
            ]);

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson",
                "get_address()" => null,
                "get_address_2()" => [
                    "get_id()" => 1,
                    "get_street_1()" => 'Evergreen 742',
                    "get_street_2()" => null,
                    "get_city()" => null
                ]
            ]);

        });

        $this->it( "resolves the reference on the first message received", function() {

            $user = Users_Collection::do()->create_from_attributes([
                'name' => 'Lisa',
                'last_name' => 'Simpson'
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "user_id" => $user->get_id(),
                "street_1" => "Evergreen 742",
            ]);

            $user = Users_Collection::get()->last();

            $this->expect( $user->get_address_2() ) ->to() ->be() ->a(
                \Haijin\Persistency\Persistent_Collection\Reference_Proxies\Object_From_Collection_Proxy::class
            );

            $user->get_address_2()->get_id();

            $this->expect( $user->get_address_2() ) ->to() ->be() ->a(
                \Address::class
            );

        });

    });

    $this->describe( "with a reference from an array in another collection", function() {

        $this->it( "resolves the reference to []", function() {

            Users_Collection::do()->create_from_attributes([
                'name' => 'Lisa',
                'last_name' => 'Simpson'
            ]);

            $user = Users_Collection::get()->last();

            $address = $user->get_all_addresses()->resolve_reference();

            $this->expect( $address->is_empty() ) ->to() ->be() ->true();

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson",
                "get_all_addresses()" => function($ordered_colleciton) {
                    $this->expect( $ordered_colleciton->to_array() )
                        ->to() ->equal( [] );
                },
            ]);

        });

        $this->it( "resolves the reference to an array", function() {

            $user = Users_Collection::do()->create_from_attributes([
                'name' => 'Lisa',
                'last_name' => 'Simpson'
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "user_id" => $user->get_id(),
                "street_1" => "Evergreen 742",
            ]);

            $user = Users_Collection::get()->last();

            $addresses = $user->get_all_addresses()->resolve_reference();

            $this->expect( $addresses->to_array() ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 1,
                    "get_street_1()" => 'Evergreen 742',
                    "get_street_2()" => null,
                    "get_city()" => null
                ]
            ]);

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson",
                "get_all_addresses()" => function($ordered_colleciton) {
                    $this->expect( $ordered_colleciton->to_array() )
                        ->to() ->be() ->exactly_like([
                            [
                                "get_id()" => 1,
                                "get_street_1()" => 'Evergreen 742',
                                "get_street_2()" => null,
                                "get_city()" => null
                            ]
                        ]);
                }
            ]);

        });

        $this->it( "resolves the reference on the first message received", function() {

            $user = Users_Collection::do()->create_from_attributes([
                'name' => 'Lisa',
                'last_name' => 'Simpson'
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "user_id" => $user->get_id(),
                "street_1" => "Evergreen 742",
            ]);

            $user = Users_Collection::get()->last();

            $this->expect( $user->get_all_addresses() ) ->to() ->be() ->a(
                \Haijin\Persistency\Persistent_Collection\Reference_Proxies\Array_From_Collection_Proxy::class
            );

            $user->get_all_addresses()->to_array();

            $this->expect( $user->get_all_addresses() ) ->to() ->be() ->a(
                \Haijin\Ordered_Collection::class
            );

        });

    });

    $this->describe( "with a reference through a middle table to an array in another collection", function() {

        $this->it( "resolves the reference to []", function() {

            Users_Collection::do()->create_from_attributes([
                'name' => 'Lisa',
                'last_name' => 'Simpson'
            ]);

            $user = Users_Collection::get()->last();

            $address = $user->get_all_indirect_addresses()->resolve_reference();

            $this->expect( $address->is_empty() ) ->to() ->be() ->true();

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson",
                "get_all_indirect_addresses()" => function($ordered_colleciton) {
                    $this->expect( $ordered_colleciton->to_array() )
                        ->to() ->equal( [] );
                },
            ]);

        });

        $this->it( "resolves the reference to an array", function() {

            $user = Users_Collection::do()->create_from_attributes([
                'name' => 'Lisa',
                'last_name' => 'Simpson'
            ]);

            $address = Addresses_Collection::do()->create_from_attributes([
                "street_1" => "Evergreen 742",
            ]);

            $this->database->create( function($query) use($user, $address){

                $query->collection( "users_addresses" );

                $query->record(
                    $query->set( "user_id", $query->value( $user->get_id() ) ),
                    $query->set( "address_id", $query->value( $address->get_id() ) )
                );

            });

            $user = Users_Collection::get()->last();

            $addresses = $user->get_all_indirect_addresses()->resolve_reference();

            $this->expect( $addresses->to_array() ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 1,
                    "get_street_1()" => 'Evergreen 742',
                    "get_street_2()" => null,
                    "get_city()" => null
                ]
            ]);

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson",
                "get_all_indirect_addresses()" => function($ordered_colleciton) {
                    $this->expect( $ordered_colleciton->to_array() )
                        ->to() ->be() ->exactly_like([
                            [
                                "get_id()" => 1,
                                "get_street_1()" => 'Evergreen 742',
                                "get_street_2()" => null,
                                "get_city()" => null
                            ]
                        ]);
                }
            ]);

        });

        $this->it( "resolves the reference on the first message received", function() {

            $user = Users_Collection::do()->create_from_attributes([
                'name' => 'Lisa',
                'last_name' => 'Simpson'
            ]);

            $address = Addresses_Collection::do()->create_from_attributes([
                "street_1" => "Evergreen 742",
            ]);

            $this->database->create( function($query) use($user, $address){

                $query->collection( "users_addresses" );

                $query->record(
                    $query->set( "user_id", $query->value( $user->get_id() ) ),
                    $query->set( "address_id", $query->value( $address->get_id() ) )
                );

            });

            $user = Users_Collection::get()->last();

            $this->expect( $user->get_all_indirect_addresses() ) ->to() ->be() ->a(
                \Haijin\Persistency\Persistent_Collection\Reference_Proxies\Array_Through_Collection_Proxy::class
            );

            $user->get_all_indirect_addresses()->to_array();

            $this->expect( $user->get_all_indirect_addresses() ) ->to() ->be() ->a(
                \Haijin\Ordered_Collection::class
            );

        });

    });

});