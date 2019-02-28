<?php

use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database;

$spec->xdescribe( "When building a Elasticsearch expression", function() {

    $this->let( "database", function() {

        $database = new Elasticsearch_Database();

        $database->connect( [ '127.0.0.1:9200' ] );

        return $database;

    });

    $this->it( "builds a complete sql expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {
                    $query->proyect(
                        $query->concat(
                            $query->field( "street_name" ), " ", $query->field( "street_number" )
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
                $query->field( "users_read_only.last_name" ) ->desc(),
                $query->field( "users_read_only.name" ) ->desc(),
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

            $query->collection( "users_read_only" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {

                    $query->proyect(
                        $query->concat(
                            $query->field( "street_name" ), " ", $query->field( "street_number" )
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
                $query->field( "users_read_only.last_name" ) ->desc(),
                $query->field( "users_read_only.name" ) ->desc(),
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