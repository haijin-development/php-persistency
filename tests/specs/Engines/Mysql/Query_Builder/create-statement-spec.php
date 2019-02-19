<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;

$spec->describe( "When evaluating a create statement in a MySql database", function() {

    $this->before_each( function() {

        $this->re_populate_mysql_tables();

    });

    $this->after_all( function() {

        $this->re_populate_mysql_tables();

    });

    $this->let( "database", function() {

        $database = new Mysql_Database();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        return $database;

    });

    $this->it( "creates a record with constant values", function() {

        $this->database->create_one( function($query) {

            $query->collection( "users" );

            $query->record(
                $query->field( "name" ) ->value( "Homer" ),
                $query->field( "last_name" ) ->value( "Simpson" )
            );

        });

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 4,
                "name" => "Homer",
                "last_name" => "Simpson"
            ],
        ]);

    });

});