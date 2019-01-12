<?php

use Haijin\Persistency\Mysql\MysqlDatabase;

$spec->describe( "When building the collection statement of a MySql expression", function() {

    $this->let( "database", function() {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        return $database;
    });

    $this->it( "builds the collection name", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

        });

        $this->expect( $rows ) ->to() ->be() ->like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
            [
                "id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson"
            ],
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson"
            ]
        ]);

    });

    $this->it( "builds the collection name with an alias", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" ) ->as( "c" );

        });

        $this->expect( $rows ) ->to() ->be() ->like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ],
            [
                "id" => 2,
                "name" => "Bart",
                "last_name" => "Simpson"
            ],
            [
                "id" => 3,
                "name" => "Maggie",
                "last_name" => "Simpson"
            ]
        ]);

    });

});