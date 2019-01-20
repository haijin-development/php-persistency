<?php

use Haijin\Persistency\Engines\Sqlite\Sqlite_Database;

$spec->describe( "When building the order by statement of a sql expression", function() {

    $this->before_all( function() {

        $this->sort_users();

    });

    $this->after_all( function() {

        $this->re_populate_tables();

    });

    $this->let( "database", function() {

        $database = new Sqlite_Database();

        $database->connect( $this->sqlite_file );

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

        $this->sqlite->query( "delete from users where 1 = 1;" );

        $this->sqlite->query(
            "INSERT INTO users VALUES ( 1, 'Lisa', 'Simpson' );"
        );
        $this->sqlite->query(
            "INSERT INTO users VALUES ( 2, 'Marge', 'Bouvier' );"
        );
        $this->sqlite->query(
            "INSERT INTO users VALUES ( 3, 'Maggie', 'Simpson' );"
        );

    });

    $this->def( "re_populate_tables", function() {

        $this->clear_sqlite_tables();
        $this->populate_sqlite_tables();

    });

});