<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;

$spec->describe( "When inspecting a statement of a MySql expression", function() {

    $this->let( "database", function() {

        $database = new Mysql_Database();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        return $database;

    });

    $this->it( "inspects a query statement", function() {

        $this->inspected_was_called = false;

        $this->database->inspect_query_with( function($sql, $params) {

            $this->inspected_was_called = true;

            $this->expect( $sql ) ->to()
                ->equal( "select users_read_only.* from users_read_only where users_read_only.name = ? and users_read_only.last_name = ?;" );

            $this->expect( $params ) ->to() ->equal( ['Lisa', 'Simpson' ] );

        }, $this );

        $rows = $this->database->query( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query ->field( 'name' ) ->op( '=' ) ->value( 'Lisa' )
                ->and()
                ->field( 'last_name' ) ->op( '=' ) ->param( 'ln' )
            );
        }, [
            'parameters' => [ 'ln' => 'Simpson' ]
        ]);

        $this->expect( $this->inspected_was_called ) ->to() ->be() ->true();

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                "id" => 1,
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]
        ]);

    });

});