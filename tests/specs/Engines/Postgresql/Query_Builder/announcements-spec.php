<?php

use Haijin\Persistency\Engines\Postgresql\Postgresql_Database;
use Haijin\Persistency\Announcements\About_To_Execute_Statement;

$spec->describe( "When a Postgresql database makes announcements events", function() {

    $this->let( "database", function() {

        $database = new Postgresql_Database();

        $database->connect(
            "host=localhost port=5432 dbname=haijin-persistency user=haijin password=123456"
        );

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
                    ->equal( "Haijin\Persistency\Engines\Postgresql\Postgresql_Database about to execute: 'select users.* from users where users.name = $1;' with parameters: '[\"Lisa\"]'" );

                $this->expect( $announcement->get_database_class() ) ->to()
                    ->equal( Postgresql_Database::class );

                $this->expect( $announcement->get_sql() ) ->to()
                    ->equal( 'select users.* from users where users.name = $1;' );

                $this->expect( $announcement->get_parameters() ) ->to()
                    ->equal( [ 'Lisa' ] );
        });

    });

});