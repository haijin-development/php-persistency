<?php

use Haijin\Persistency\Engines\Postgresql\Postgresql_Database;

$spec->describe( "When building the order by statement of a sql expression", function() {

    $this->before_all( function() {

        $this->sort_users();

    });

    $this->after_all( function() {

        $this->re_populate_postgres_tables();

    });

    $this->let( "database", function() {

        $database = new Postgresql_Database();

        $database->connect(
            "host=localhost port=5432 dbname=haijin-persistency user=haijin password=123456"
        );

        return $database;

    });

    $this->it( "builds the order by fields", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "last_name" ),
                $query->field( "name" )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 2,
                "name" => "Marge",
                "last_name" => "Bouvier"
            ],
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds the desc expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson"
            ],
            [
                "id" => 2,
                "name" => "Marge",
                "last_name" => "Bouvier"
            ],
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds the asc expression", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "name" ) ->asc()
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson"
            ],
            [
                "id" => 2,
                "name" => "Marge",
                "last_name" => "Bouvier"
            ]
        ]);

    });

    $this->def( "sort_users", function() {

        pg_query( $this->postgresql, "TRUNCATE users;" );
        pg_query( $this->postgresql, "INSERT INTO users VALUES ( 1, 'Lisa', 'Simpson' );" );
        pg_query( $this->postgresql, "INSERT INTO users VALUES ( 2, 'Marge', 'Bouvier' );" );
        pg_query( $this->postgresql, "INSERT INTO users VALUES ( 3, 'Maggie', 'Simpson' );" );

    });

});