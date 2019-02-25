<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;
use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

$spec->describe( "When creating an object in a Persistent_Collection in a Sqlite database", function() {

    $this->before_all( function() {

        $this->database = new Sqlite_Database();

        $this->database->connect( $this->sqlite_file );

        Users_Collection::get()->set_database( $this->database );

    });

    $this->before_each( function() {

        Users_Collection::do()->clear_all();

    });

    $this->after_all( function() {

        Users_Collection::do()->clear_all();

    });

    $this->describe( "from an object", function() {

        $this->it( "creates the object with a given id", function() {

            $user = new User();
            $user->set_id( 7 );
            $user->set_name( "Lisa" );
            $user->set_last_name( "Simpson" );

            Users_Collection::do()->create( $user );

            $user = new User();
            $user->set_id( 8 );
            $user->set_name( "Bart" );
            $user->set_last_name( "Simpson" );

            Users_Collection::do()->create( $user );

            $users = Users_Collection::get()->all();

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 7,
                    "get_name()" => "Lisa",
                    "get_last_name()" => "Simpson"
                ],
                [
                    "get_id()" => 8,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson"
                ]
            ]);

        });

        $this->it( "creates the object with missing fields", function() {

            $user = new User();
            $user->set_id( 7 );
            $user->set_name( "Lisa" );

            Users_Collection::do()->create( $user );

            $user = new User();
            $user->set_id( 8 );
            $user->set_name( "Bart" );

            Users_Collection::do()->create( $user );

            $users = Users_Collection::get()->all();

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 7,
                    "get_name()" => "Lisa",
                    "get_last_name()" => null
                ],
                [
                    "get_id()" => 8,
                    "get_name()" => "Bart",
                    "get_last_name()" => null
                ]
            ]);

        });

        $this->it( "creates the object with no id", function() {

            $user = new User();
            $user->set_name( "Lisa" );
            $user->set_last_name( "Simpson" );

            Users_Collection::do()->create( $user );

            $user = new User();
            $user->set_name( "Bart" );
            $user->set_last_name( "Simpson" );

            Users_Collection::do()->create( $user );

            $users = Users_Collection::get()->all();

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

        $this->it( "fills in the created id into the object", function() {

            $user = new User();
            $user->set_name( "Lisa" );
            $user->set_last_name( "Simpson" );

            Users_Collection::do()->create( $user );

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                    "get_id()" => 1,
                    "get_name()" => "Lisa",
                    "get_last_name()" => "Simpson"
            ]);


            $user = new User();
            $user->set_name( "Bart" );
            $user->set_last_name( "Simpson" );

            Users_Collection::do()->create( $user );

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                    "get_id()" => 2,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson"
            ]);

        });

    });

    $this->describe( "from attributes", function() {

        $this->it( "creates the object with a given id", function() {

            Users_Collection::do()->create_from_attributes([
                "id" => 7,
                "name" => "Lisa",
                "last_name" => "Simpson",
            ]);

            Users_Collection::do()->create_from_attributes([
                "id" => 8,
                "name" => "Bart",
                "last_name" => "Simpson",
            ]);

            $users = Users_Collection::get()->all();

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 7,
                    "get_name()" => "Lisa",
                    "get_last_name()" => "Simpson"
                ],
                [
                    "get_id()" => 8,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson"
                ]
            ]);

        });

        $this->it( "creates the object with missing fields", function() {

            Users_Collection::do()->create_from_attributes([
                "id" => 7,
                "name" => "Lisa"
            ]);

            Users_Collection::do()->create_from_attributes([
                "id" => 8,
                "name" => "Bart"
            ]);

            $users = Users_Collection::get()->all();

            $this->expect( $users ) ->to() ->be() ->exactly_like([
                [
                    "get_id()" => 7,
                    "get_name()" => "Lisa",
                    "get_last_name()" => null
                ],
                [
                    "get_id()" => 8,
                    "get_name()" => "Bart",
                    "get_last_name()" => null
                ]
            ]);

        });

        $this->it( "creates the object with no id", function() {

            Users_Collection::do()->create_from_attributes([
                "name" => "Lisa",
                "last_name" => "Simpson",
            ]);

            Users_Collection::do()->create_from_attributes([
                "name" => "Bart",
                "last_name" => "Simpson",
            ]);

            $users = Users_Collection::get()->all();

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

        $this->it( "fills in the created id into the object", function() {

            $user = Users_Collection::do()->create_from_attributes([
                "name" => "Lisa",
                "last_name" => "Simpson",
            ]);

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                    "get_id()" => 1,
                    "get_name()" => "Lisa",
                    "get_last_name()" => "Simpson"
            ]);

            $user = Users_Collection::do()->create_from_attributes([
                "name" => "Bart",
                "last_name" => "Simpson",
            ]);

            $this->expect( $user ) ->to() ->be() ->exactly_like([
                    "get_id()" => 2,
                    "get_name()" => "Bart",
                    "get_last_name()" => "Simpson"
            ]);

        });

    });
    
});