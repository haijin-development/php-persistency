<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

$spec->describe( "When mapping fields to another collections in a Sqlite database", function() {

    $this->before_all( function() {

        $this->database = new Sqlite_Database();

        $this->database->connect( $this->sqlite_file );

        Users_Collection::get()->set_database( $this->database );
        Addresses_Collection::get()->set_database( $this->database );

    });

    $this->before_each( function() {

        Users_Collection::do()->clear_all();
        Addresses_Collection::do()->clear_all();
        $this->database->clear_all( 'users_addresses' );

    });

    $this->after_each( function() {

        $this->database->inspect_query_with( null );

    });

    $this->after_all( function() {

        Users_Collection::do()->clear_all();
        Addresses_Collection::do()->clear_all();
        $this->database->clear_all( 'users_addresses' );

    });

    $this->describe( "with a reference to an object in another collection", function() {

        $this->let( "populate_users_and_addresses", function() {

            Addresses_Collection::do()->create_from_attributes([
                "id" => 1,
                "street_1" => "Evergreen 742",
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "id" => 2,
                "street_1" => "Evergreen 742",
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "id" => 3,
                "street_1" => "Evergreen 742",
            ]);

            Users_Collection::do()->insert_record([
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson",
                "address_id" => 1
            ]);

            Users_Collection::do()->insert_record([
                "id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson",
                "address_id" => 2
            ]);

            Users_Collection::do()->insert_record([
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson",
                "address_id" => 3
            ]);

        });

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

            $this->populate_users_and_addresses;

            $users = Users_Collection::get()->all( function(){}, [
                'eager_fetch' => [
                    'address_id' => true
                ]
            ]);

            $this->expect( $users[ 0 ]->get_address() ) ->to() ->be() ->a(
                \Address::class
            );
            $this->expect( $users[ 1 ]->get_address() ) ->to() ->be() ->a(
                \Address::class
            );
            $this->expect( $users[ 2 ]->get_address() ) ->to() ->be() ->a(
                \Address::class
            );

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 1,
                    "get_name()" => "Lisa",
                    "get_last_name()" => "Simpson",
                    "get_address()" => [
                        "get_id()" => 1,
                        "get_street_1()" => "Evergreen 742"
                    ],
                ],
                [
                    "get_id()" => 2,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson",
                    "get_address()" => [
                        "get_id()" => 2,
                        "get_street_1()" => "Evergreen 742"
                    ],
                ],
                [
                    "get_id()" => 3,
                    "get_name()" => "Maggie",
                    "get_last_name()" => "Simpson",
                    "get_address()" => [
                        "get_id()" => 3,
                        "get_street_1()" => "Evergreen 742"
                    ],
                ]
            ]);

        });

        $this->it( "uses two queries to fetch the objects, 1 for all users and 1 for all addresses", function() {

            $this->populate_users_and_addresses;

            $this->queries = [];

            $this->database->inspect_query_with( function($sql, $params) {

                $this->queries[] = $sql;

            }, $this );

            $user = Users_Collection::get()->all( function(){}, [
                'eager_fetch' => [
                    'address_id' => true
                ]
            ]);

            $this->expect( $this->queries ) ->to() ->equal([
                "select users.* from users;",
                "select addresses.* from addresses where addresses.id in (1, 2, 3);"
            ]);

        });

    });

    $this->describe( "with a reference from an object in another collection", function() {

        $this->let( "populate_users_and_addresses", function() {

            Users_Collection::do()->insert_record([
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson",
            ]);

            Users_Collection::do()->insert_record([
                "id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson",
            ]);

            Users_Collection::do()->insert_record([
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson",
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "id" => 1,
                "user_id" => 1,
                "street_1" => "Evergreen 742",
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "id" => 2,
                "user_id" => 2,
                "street_1" => "Evergreen 742",
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "id" => 3,
                "user_id" => 3,
                "street_1" => "Evergreen 742",
            ]);

        });

        $this->it( "eager fetches to null", function() {

            Users_Collection::do()->create_from_attributes([
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]);

            $user = Users_Collection::get()->first( function(){}, [
                'eager_fetch' => [
                    'address_2' => true
                ]
            ]);

            $this->expect( $user->get_address_2() ) ->to() ->be() ->null();

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson",
                "get_address_2()" => null,
            ]);

        });

        $this->it( "eager fetches the references to another objects", function() {

            $this->populate_users_and_addresses;

            $users = Users_Collection::get()->all( function(){}, [
                'eager_fetch' => [
                    'address_2' => true
                ]
            ]);

            $this->expect( $users[ 0 ]->get_address_2() ) ->to() ->be() ->a(
                \Address::class
            );
            $this->expect( $users[ 1 ]->get_address_2() ) ->to() ->be() ->a(
                \Address::class
            );
            $this->expect( $users[ 2 ]->get_address_2() ) ->to() ->be() ->a(
                \Address::class
            );

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 1,
                    "get_name()" => "Lisa",
                    "get_last_name()" => "Simpson",
                    "get_address_2()" => [
                        "get_id()" => 1,
                        "get_street_1()" => "Evergreen 742"
                    ],
                ],
                [
                    "get_id()" => 2,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson",
                    "get_address_2()" => [
                        "get_id()" => 2,
                        "get_street_1()" => "Evergreen 742"
                    ],
                ],
                [
                    "get_id()" => 3,
                    "get_name()" => "Maggie",
                    "get_last_name()" => "Simpson",
                    "get_address_2()" => [
                        "get_id()" => 3,
                        "get_street_1()" => "Evergreen 742"
                    ],
                ]
            ]);

        });

        $this->it( "uses two queries to fetch the objects, 1 for all users and 1 for all addresses", function() {

            $this->populate_users_and_addresses;

            $this->queries = [];

            $this->database->inspect_query_with( function($sql, $params) {

                $this->queries[] = $sql;

            }, $this );

            $users = Users_Collection::get()->all( function(){}, [
                'eager_fetch' => [
                    'address_2' => true
                ]
            ]);

            $this->expect( $this->queries ) ->to() ->equal([
                "select users.* from users;",
                "select addresses.* from addresses where addresses.user_id in (1, 2, 3);"
            ]);

        });

    });

    $this->describe( "with a reference from an array in another collection", function() {

        $this->let( "populate_users_and_addresses", function() {

            Users_Collection::do()->insert_record([
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson",
            ]);

            Users_Collection::do()->insert_record([
                "id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson",
            ]);

            Users_Collection::do()->insert_record([
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson",
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "id" => 1,
                "user_id" => 1,
                "street_1" => "Evergreen 742",
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "id" => 2,
                "user_id" => 2,
                "street_1" => "Evergreen 742",
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "id" => 3,
                "user_id" => 3,
                "street_1" => "Evergreen 742",
            ]);

        });

        $this->it( "eager fetches the reference to []", function() {

            Users_Collection::do()->create_from_attributes([
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]);

            $user = Users_Collection::get()->first( function(){}, [
                'eager_fetch' => [
                    'all_addresses' => true
                ]
            ]);

            $this->expect( $user->get_all_addresses() ) ->to() ->be() ->a(
                \Haijin\Ordered_Collection::class
            );

            $this->expect( $user->get_all_addresses()->to_array() ) ->to() ->equal( [] );

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson",
                "get_all_addresses()" => [],
            ]);

        });

        $this->it( "eager fetches the reference to an array", function() {

            $this->populate_users_and_addresses;

            $users = Users_Collection::get()->all( function(){}, [
                'eager_fetch' => [
                    'all_addresses' => true
                ]
            ]);

            $this->expect( $users[ 0 ]->get_all_addresses() ) ->to() ->be() ->a(
                \Haijin\Ordered_Collection::class
            );
            $this->expect( $users[ 1 ]->get_all_addresses() ) ->to() ->be() ->a(
                \Haijin\Ordered_Collection::class
            );
            $this->expect( $users[ 2 ]->get_all_addresses() ) ->to() ->be() ->a(
                \Haijin\Ordered_Collection::class
            );

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 1,
                    "get_name()" => "Lisa",
                    "get_last_name()" => "Simpson",
                    "get_all_addresses()" => [
                        "to_array()" => [
                            [
                                "get_id()" => 1,
                                "get_street_1()" => "Evergreen 742"
                            ]
                        ]
                    ],
                ],
                [
                    "get_id()" => 2,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson",
                    "get_all_addresses()" => [
                        "to_array()" => [
                            [
                                "get_id()" => 2,
                                "get_street_1()" => "Evergreen 742"
                            ]
                        ]
                    ],
                ],
                [
                    "get_id()" => 3,
                    "get_name()" => "Maggie",
                    "get_last_name()" => "Simpson",
                    "get_all_addresses()" => [
                        "to_array()" => [
                            [
                                "get_id()" => 3,
                                "get_street_1()" => "Evergreen 742"
                            ]
                        ]
                    ],
                ]
            ]);

        });

        $this->it( "uses two queries to fetch the objects, 1 for all users and 1 for all addresses", function() {

            $this->populate_users_and_addresses;

            $this->queries = [];

            $this->database->inspect_query_with( function($sql, $params) {

                $this->queries[] = $sql;

            }, $this );

            $users = Users_Collection::get()->all( function(){}, [
                'eager_fetch' => [
                    'all_addresses' => true
                ]
            ]);

            $this->expect( $this->queries ) ->to() ->equal([
                "select users.* from users;",
                "select addresses.* from addresses where addresses.user_id in (1, 2, 3);"
            ]);

        });

    });

    $this->describe( "with a reference through a middle table to an array in another collection", function() {

        $this->let( "populate_users_and_addresses", function() {

            Users_Collection::do()->insert_record([
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson",
            ]);

            Users_Collection::do()->insert_record([
                "id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson",
            ]);

            Users_Collection::do()->insert_record([
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson",
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "id" => 1,
                "street_1" => "Evergreen 742",
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "id" => 2,
                "street_1" => "Evergreen 742",
            ]);

            Addresses_Collection::do()->create_from_attributes([
                "id" => 3,
                "street_1" => "Evergreen 742",
            ]);

            $compiled_query = $this->database->compile( function($compiler) {

                $compiler->create( function($query) {

                    $query->collection( "users_addresses" );

                    $query->record(
                        $query->set( "user_id", $query->param( "user_id" ) ),
                        $query->set( "address_id", $query->param( "address_id" ) )
                    );

                });

            });

            $this->database->execute( $compiled_query, [ 'parameters' => [
                    'user_id' => 1,
                    'address_id' => 1
                ]
            ]);
            $this->database->execute( $compiled_query, [ 'parameters' => [
                    'user_id' => 2,
                    'address_id' => 2
                ]
            ]);
            $this->database->execute( $compiled_query, [ 'parameters' => [
                    'user_id' => 3,
                    'address_id' => 3
                ]
            ]);

        });

        $this->it( "eager fetches the reference to []", function() {

            Users_Collection::do()->create_from_attributes([
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]);

            $user = Users_Collection::get()->first( function(){}, [
                'eager_fetch' => [
                    'all_indirect_addresses' => true
                ]
            ]);

            $this->expect( $user->get_all_indirect_addresses() ) ->to() ->be() ->a(
                \Haijin\Ordered_Collection::class
            );

            $this->expect( $user->get_all_indirect_addresses()->to_array() ) ->to() ->equal( [] );

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                "get_id()" => 1,
                "get_name()" => "Lisa",
                "get_last_name()" => "Simpson",
                "get_all_indirect_addresses()" => [],
            ]);

        });

        $this->it( "eager fetches the reference to an array", function() {

            $this->populate_users_and_addresses;

            $users = Users_Collection::get()->all( function(){}, [
                'eager_fetch' => [
                    'all_indirect_addresses' => true
                ]
            ]);

            $this->expect( $users[ 0 ]->get_all_indirect_addresses() ) ->to() ->be() ->a(
                \Haijin\Ordered_Collection::class
            );
            $this->expect( $users[ 1 ]->get_all_indirect_addresses() ) ->to() ->be() ->a(
                \Haijin\Ordered_Collection::class
            );
            $this->expect( $users[ 2 ]->get_all_indirect_addresses() ) ->to() ->be() ->a(
                \Haijin\Ordered_Collection::class
            );

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 1,
                    "get_name()" => "Lisa",
                    "get_last_name()" => "Simpson",
                    "get_all_indirect_addresses()" => [
                        "to_array()" => [
                            [
                                "get_id()" => 1,
                                "get_street_1()" => "Evergreen 742"
                            ]
                        ]
                    ],
                ],
                [
                    "get_id()" => 2,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson",
                    "get_all_indirect_addresses()" => [
                        "to_array()" => [
                            [
                                "get_id()" => 2,
                                "get_street_1()" => "Evergreen 742"
                            ]
                        ]
                    ],
                ],
                [
                    "get_id()" => 3,
                    "get_name()" => "Maggie",
                    "get_last_name()" => "Simpson",
                    "get_all_indirect_addresses()" => [
                        "to_array()" => [
                            [
                                "get_id()" => 3,
                                "get_street_1()" => "Evergreen 742"
                            ]
                        ]
                    ],
                ]
            ]);

        });

        $this->it( "uses two queries to fetch the objects, 1 for all users and 1 for all addresses", function() {

            $this->populate_users_and_addresses;

            $this->queries = [];

            $this->database->inspect_query_with( function($sql, $params) {

                $this->queries[] = $sql;

            }, $this );

            $users = Users_Collection::get()->all( function(){}, [
                'eager_fetch' => [
                    'all_indirect_addresses' => true
                ]
            ]);

            $this->expect( $this->queries ) ->to() ->equal([
                "select users.* from users;",
                "select addresses.* ".
                "from addresses join users_addresses on addresses.id = users_addresses.address_id ".
                "join users on users_addresses.user_id = users.id " .
                "where users.id in (1, 2, 3);"
            ]);

        });

    });

});