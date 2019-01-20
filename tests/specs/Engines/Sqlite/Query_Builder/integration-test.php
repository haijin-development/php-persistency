<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;

$spec->describe( "When building a MySql expression", function() {

    $this->let( "database", function() {

        $database = new Sqlite_Database();

        $database->connect( $this->sqlite_file );

        return $database;

    });

    $this->it( "builds a complete sql expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {
                    $query->proyect(
                        $query->brackets(

                            $query->field( "street_name" ) ->op( "||" )
                            ->value( " " ) ->op( "||" ) ->field( "street_number" )

                        ) ->as( "address" )
                    );
                });


            $query->filter(
                $query->brackets(
                    $query->brackets(
                        $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
                    )
                    ->and()
                    ->brackets(
                        $query ->field( "last_name" ) ->op( "=" ) ->value( "Simpson" )
                    )
                )
                ->or()
                ->brackets(
                    $query ->field( "address_1.street_name" ) ->op( "like" )
                        ->value( "%Evergreen%" )
                )
            );

            $query->order_by(
                $query->field( "users.last_name" ) ->desc(),
                $query->field( "users.name" ) ->desc(),
                $query->field( "address" ) ->desc()
            );

            $query->pagination(
                $query
                    ->offset( 0 )
                    ->limit( 10 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "name" => "Maggie",
                "last_name" => "Simpson"
            ],
            [
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
            [
                "name" => "Bart",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds a complete sql expression using macros", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {

                    $query->proyect(
                        $query->brackets(

                            $query->field( "street_name" ) ->op( "||" )
                            ->value( " " ) ->op( "||" ) ->field( "street_number" )

                        ) ->as( "address" )
                    );

                    $query->let( "matches_address", function($query) { return
                        $query->brackets(
                            $query ->field( "street_name" ) ->op( "like" ) ->value( "%Evergreen%" )
                        );
                    });
            });

            $query->let( "matches_name", function($query) { return
                $query->brackets(
                    $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
                );
            });

            $query->let( "matches_last_name", function($query) { return
                $query->brackets(
                    $query ->field( "last_name" ) ->op( "=" ) ->value( "Simpson" )
                );
            });

            $query->filter(
                $query->brackets( $query
                    ->matches_name ->and() ->matches_last_name
                )
                ->or()
                ->matches_address
            );

            $query->order_by(
                $query->field( "users.last_name" ) ->desc(),
                $query->field( "users.name" ) ->desc(),
                $query->field( "address" ) ->desc()
            );

            $query->pagination(
                $query
                    ->offset( 0 )
                    ->limit( 10 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "name" => "Maggie",
                "last_name" => "Simpson"
            ],
            [
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
            [
                "name" => "Bart",
                "last_name" => "Simpson"
            ]
        ]);

    });

});