<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;
use Haijin\Persistency\Announcements\About_To_Execute_Statement;

$spec->describe( "When a MySql database makes announcements events", function() {

    $this->let( "database", function() {

        $database = new Mysql_Database();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        return $database;

    });

    $this->it( "announces the sql string and parameters before execution", function() {

        $this->expect( $this->database ) ->during( function() {

            $this->database->query( function($query) {

                $query->collection( "users" );

                $query->filter(
                    $query->field( 'name' ) ->op( '=' ) ->value( 'Lisa' )
                );

            });

        }) ->to() ->announce(

            About_To_Execute_Statement::class,

            function($announcement){

                $this->expect( $announcement->__toString() ) ->to()
                    ->equal( "Haijin\Persistency\Engines\Mysql\Mysql_Database about to execute: 'select users.* from users where users.name = ?;' with parameters: '[\"Lisa\"]'" );

                $this->expect( $announcement->get_database_class() ) ->to()
                    ->equal( Mysql_Database::class );

                $this->expect( $announcement->get_sql() ) ->to()
                    ->equal( 'select users.* from users where users.name = ?;' );

                $this->expect( $announcement->get_parameters() ) ->to()
                    ->equal( [ 'Lisa' ] );
        });

    });

});