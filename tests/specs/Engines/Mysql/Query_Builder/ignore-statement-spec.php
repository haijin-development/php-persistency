<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;

$spec->describe( "When using the ignore statement in a Mysql expression", function() {

    $this->let( "database", function() {

        $database = new Mysql_Database();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        return $database;

    });

    $this->it( "ignores if no other expression is given", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->ignore()
            );

            $query->order_by(
                $query->field( 'id' )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
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

    $this->it( "ignores an expression at the left of a binary operator", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->ignore() ->and() ->value( false )
            );

            $query->order_by(
                $query->field( 'id' )
            );

        });

        $this->expect( $rows ) ->to() ->equal( [] );

    });

    $this->it( "ignores an expression at the right of a binary operator", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->value( true ) ->and() ->ignore()
            );

            $query->order_by(
                $query->field( 'id' )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
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

    $this->it( "ignores an expression at the right and left of a binary operator", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->ignore() ->and() ->ignore()
            );

            $query->order_by(
                $query->field( 'id' )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
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

    $this->it( "ignores a parameter in a function", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->field( 'name' )
                    ->op( "=" )
                    ->concat( 'Li', $query->ignore(), 'sa' )
            );

            $query->order_by(
                $query->field( 'id' )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

});