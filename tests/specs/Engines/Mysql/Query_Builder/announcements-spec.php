<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;
use Haijin\Persistency\Announcements\About_To_Execute_Statement;

$spec->describe( "When a MySql database makes announcements events", function() {

    $this->let( "database", function() {

        $database = new Mysql_Database();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        return $database;

    });

    $this->it( "announces the sql string and parameters before a select execution", function() {

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

                $this->expect( $announcement->get_announcer_print_string() ) ->to()
                    ->equal( Mysql_Database::class );

                $this->expect( $announcement->get_sql() ) ->to()
                    ->equal( 'select users.* from users where users.name = ?;' );

                $this->expect( $announcement->get_parameters() ) ->to()
                    ->equal( [ 'Lisa' ] );
        });

    });

    $this->it( "announces the sql string and parameters before a create execution", function() {

        $this->expect( $this->database ) ->during( function() {

            $this->database->create( function($query) {

                $query->collection( "users" );

                $query->record(
                    $query->set( 'name', 'Lisa' )
                );

            });

        }) ->to() ->announce(

            About_To_Execute_Statement::class,

            function($announcement){

                $this->expect( $announcement->__toString() ) ->to()
                    ->equal( "Haijin\Persistency\Engines\Mysql\Mysql_Database about to execute: 'insert into users (name) values (?);' with parameters: '[\"Lisa\"]'" );

                $this->expect( $announcement->get_announcer_print_string() ) ->to()
                    ->equal( Mysql_Database::class );

                $this->expect( $announcement->get_sql() ) ->to()
                    ->equal( 'insert into users (name) values (?);' );

                $this->expect( $announcement->get_parameters() ) ->to()
                    ->equal( [ 'Lisa' ] );
        });

    });

    $this->it( "announces the sql string and parameters before an update execution", function() {

        $this->expect( $this->database ) ->during( function() {

            $this->database->update( function($query) {

                $query->collection( "users" );

                $query->record(
                    $query->set( 'name', 'Margaret' )
                );

                $query->filter(
                    $query->field( 'name', '=', 'Maggie' )
                );

            });

        }) ->to() ->announce(

            About_To_Execute_Statement::class,

            function($announcement){

                $this->expect( $announcement->__toString() ) ->to()
                    ->equal( "Haijin\Persistency\Engines\Mysql\Mysql_Database about to execute: 'update users set name = ? where users.name = ?;' with parameters: '[\"Margaret\",\"Maggie\"]'" );

                $this->expect( $announcement->get_announcer_print_string() ) ->to()
                    ->equal( Mysql_Database::class );

                $this->expect( $announcement->get_sql() ) ->to()
                    ->equal( 'update users set name = ? where users.name = ?;' );

                $this->expect( $announcement->get_parameters() ) ->to()
                    ->equal( [ 'Margaret', 'Maggie' ] );
        });

    });

    $this->it( "announces the sql string and parameters before a delete execution", function() {

        $this->expect( $this->database ) ->during( function() {

            $this->database->delete( function($query) {

                $query->collection( "users" );

                $query->filter(
                    $query->field( 'name', '=', 'Maggie' )
                );

            });

        }) ->to() ->announce(

            About_To_Execute_Statement::class,

            function($announcement){

                $this->expect( $announcement->__toString() ) ->to()
                    ->equal( "Haijin\Persistency\Engines\Mysql\Mysql_Database about to execute: 'delete from users where users.name = ?;' with parameters: '[\"Maggie\"]'" );

                $this->expect( $announcement->get_announcer_print_string() ) ->to()
                    ->equal( Mysql_Database::class );

                $this->expect( $announcement->get_sql() ) ->to()
                    ->equal( 'delete from users where users.name = ?;' );

                $this->expect( $announcement->get_parameters() ) ->to()
                    ->equal( [ 'Maggie' ] );
        });

    });

});