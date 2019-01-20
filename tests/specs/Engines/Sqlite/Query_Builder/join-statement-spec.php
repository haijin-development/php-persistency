<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;

$spec->describe( "When building the join statement of a Sqlite expression", function() {

    $this->let( "database", function() {

        $database = new Sqlite_Database();

        $database->connect( $this->sqlite_file );

        return $database;

    });

    $this->it( "builds a join", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "id" ) ->as( "user_id" ),
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {
                    $query->proyect(
                        $query->field( "id" ) ->as( "address_id" ),
                        $query->field( "street_name" ),
                        $query->field( "street_number" )
                    );
                });

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "user_id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson",
                "address_id" => 10,
                "street_name" => "Evergreen",
                "street_number" => "742"
            ],
            [
                "user_id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson",
                "address_id" => 20,
                "street_name" => "Evergreen",
                "street_number" => "742"
            ],
            [
                "user_id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson",
                "address_id" => 30,
                "street_name" => "Evergreen",
                "street_number" => "742"
            ]
        ]);

    });

    $this->it( "builds an aliased join", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "id" ) ->as( "user_id" ),
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->as( "a" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {
                    $query->proyect(
                        $query->field( "id" ) ->as( "address_id" ),
                        $query->field( "street_name" ),
                        $query->field( "street_number" )
                    );
                });
        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "user_id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson",
                "address_id" => 10,
                "street_name" => "Evergreen",
                "street_number" => "742"
            ],
            [
                "user_id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson",
                "address_id" => 20,
                "street_name" => "Evergreen",
                "street_number" => "742"
            ],
            [
                "user_id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson",
                "address_id" => 30,
                "street_name" => "Evergreen",
                "street_number" => "742"
            ]
        ]);

    });

    $this->it( "builds multiple joins", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "id" ) ->as( "user_id" ),
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {
                    $query->proyect(
                        $query->field( "id" ) ->as( "address_1_id" ),
                        $query->field( "street_name" ) ->as( "street_name_1" ),
                        $query->field( "street_number" ) ->as( "street_number_1" )
                    );
                });

            $query->join( "address_2" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {
                    $query->proyect(
                        $query->field( "id" ) ->as( "address_2_id" ),
                        $query->field( "street_name" ) ->as( "street_name_2" ),
                        $query->field( "street_number" ) ->as( "street_number_2" )
                    );
                });

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "user_id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson",
                "address_1_id" => 10,
                "street_name_1" => "Evergreen",
                "street_number_1" => "742",
                "address_2_id" => 100,
                "street_name_2" => "Evergreen 742",
                "street_number_2" => ""
            ],
            [
                "user_id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson",
                "address_1_id" => 20,
                "street_name_1" => "Evergreen",
                "street_number_1" => "742",
                "address_2_id" => 200,
                "street_name_2" => "Evergreen 742",
                "street_number_2" => ""
            ],
            [
                "user_id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson",
                "address_1_id" => 30,
                "street_name_1" => "Evergreen",
                "street_number_1" => "742",
                "address_2_id" => 300,
                "street_name_2" => "Evergreen 742",
                "street_number_2" => ""
            ]
        ]);

    });

    $this->it( "builds join proyections", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {
                    $query->proyect(
                        $query->field( "street_name" ),
                        $query->field( "street_number" )
                    );
                });

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "name" => "Lisa",
                "last_name" => "Simpson",
                "street_name" => "Evergreen",
                "street_number" => "742"
            ],
            [
                "name" => "Bart",
                "last_name" => "Simpson",
                "street_name" => "Evergreen",
                "street_number" => "742"
            ],
            [
                "name" => "Maggie",
                "last_name" => "Simpson",
                "street_name" => "Evergreen",
                "street_number" => "742"
            ]
        ]);

    });

    $this->it( "builds macro expressions within join expressions", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "id" ) ->as( "user_id" ),
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {

                    $query->proyect(
                        $query->field( "id" ) ->as( "address_id" ),
                        $query->field( "street_name" ),
                        $query->field( "street_number" )
                    );

                    $query->let( "matches_street", function($query) {
                        return $query ->field( "id" ) ->op( "=" ) ->value( "10" );
                    });

                });

            $query->filter(
                $query ->matches_street
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "user_id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson",
                "address_id" => 10,
                "street_name" => "Evergreen",
                "street_number" => "742"
            ]
        ]);

    });

    $this->it( "builds nested joins", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "id" ) ->as( "user_id" ),
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {

                    $query->proyect(
                        $query->field( "id" ) ->as( "address_id" ),
                        $query->field( "street_name" ),
                        $query->field( "street_number" )
                    );
                    $query->join( "cities" ) ->from( "id_city" ) ->to( "id" )
                        ->eval( function($query) {

                            $query->proyect(
                                $query->field( "id" ) ->as( "city_id" ),
                                $query->field( "name" ) ->as( "city" )
                            );

                            $query->let( "matches_city", function($query) {
                                return $query ->field( "name" )
                                    ->op( "=" )
                                    ->value( "Springfield_" );
                            });

                        });
                });

            $query->filter(
                $query ->matches_city
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "user_id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson",
                "address_id" => 10,
                "street_name" => "Evergreen",
                "street_number" => "742",
                "city_id" => 2,
                "city" => "Springfield_"
            ]
        ]);

    });

});