<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;
use Haijin\Persistency\Announcements\About_To_Create_Object;
use Haijin\Persistency\Announcements\About_To_Update_Object;
use Haijin\Persistency\Announcements\About_To_Delete_Object;
use Haijin\Persistency\Announcements\Object_Created;
use Haijin\Persistency\Announcements\Object_Updated;
use Haijin\Persistency\Announcements\Object_Deleted;

$spec->describe( "When a Persisten_Collection on a Mysql database makes announcements", function() {

    $this->before_all( function() {

        $this->database = new Mysql_Database();

        $this->database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        Users_Collection::get()->set_database( $this->database );

    });

    $this->before_each( function() {

        Users_Collection::get()->clear_all();

        $this->user = Users_Collection::do()->create_from_attributes([
            'name' => 'Maggie'
        ]);

    });

    $this->describe( "when creating objects", function() {

        $this->it( "announces that is about to create an object", function() {

            $this->expect( Users_Collection::get() ) ->during( function() {

                Users_Collection::do()->create_from_attributes([
                    'name' => 'Lisa'
                ]);

            }) ->to() ->announce(

                About_To_Create_Object::class,

                function($announcement){

                    $this->expect( $announcement->__toString() ) ->to()
                        ->equal( "Users_Persistent_Collection about to create an object User." );

                    $this->expect( $announcement->get_object() ) ->to() ->be() ->exactly_like([
                        'get_id()' => null,
                        'get_name()' => 'Lisa'
                    ]);

            });

        });

        $this->it( "announces that it created an object", function() {

            $this->expect( Users_Collection::get() ) ->during( function() {

                Users_Collection::do()->create_from_attributes([
                    'name' => 'Lisa'
                ]);

            }) ->to() ->announce(

                Object_Created::class,

                function($announcement){

                    $this->expect( $announcement->__toString() ) ->to()
                        ->equal( "Users_Persistent_Collection created an object User." );

                    $this->expect( $announcement->get_object() ) ->to() ->be() ->exactly_like([
                        'get_id()' => 2,
                        'get_name()' => 'Lisa'
                    ]);

            });

        });

    });

    $this->describe( "when updating objects", function() {

        $this->it( "announces that is about to update an object", function() {

            $this->expect( Users_Collection::get() ) ->during( function() {

                Users_Collection::do()->update_from_attributes( $this->user, [
                    'name' => 'Margaret'
                ]);

            }) ->to() ->announce(

                About_To_Update_Object::class,

                function($announcement){

                    $this->expect( $announcement->__toString() ) ->to()
                        ->equal( "Users_Persistent_Collection about to update an object User." );

                    $this->expect( $announcement->get_object() ) ->to() ->be() ->exactly_like([
                        'get_id()' => 1,
                        'get_name()' => 'Margaret'
                    ]);

            });

        });

        $this->it( "announces that it updated an object", function() {

            $this->expect( Users_Collection::get() ) ->during( function() {

                Users_Collection::do()->update_from_attributes( $this->user, [
                    'name' => 'Margaret'
                ]);

            }) ->to() ->announce(

                Object_Updated::class,

                function($announcement){

                    $this->expect( $announcement->__toString() ) ->to()
                        ->equal( "Users_Persistent_Collection updated an object User." );

                    $this->expect( $announcement->get_object() ) ->to() ->be() ->exactly_like([
                        'get_id()' => 1,
                        'get_name()' => 'Margaret'
                    ]);

            });

        });

    });

    $this->describe( "when deleting objects", function() {

        $this->it( "announces that is about to delete an object", function() {

            $this->expect( Users_Collection::get() ) ->during( function() {

                Users_Collection::do()->delete( $this->user );

            }) ->to() ->announce(

                About_To_Delete_Object::class,

                function($announcement){

                    $this->expect( $announcement->__toString() ) ->to()
                        ->equal( "Users_Persistent_Collection about to delete an object User." );

                    $this->expect( $announcement->get_object() ) ->to() ->be() ->exactly_like([
                        'get_id()' => 1,
                        'get_name()' => 'Maggie'
                    ]);

            });

        });

        $this->it( "announces that it deleted an object", function() {

            $this->expect( Users_Collection::get() ) ->during( function() {

                Users_Collection::do()->delete( $this->user );

            }) ->to() ->announce(

                Object_Deleted::class,

                function($announcement){

                    $this->expect( $announcement->__toString() ) ->to()
                        ->equal( "Users_Persistent_Collection deleted an object User." );

                    $this->expect( $announcement->get_object() ) ->to() ->be() ->exactly_like([
                        'get_id()' => 1,
                        'get_name()' => 'Maggie'
                    ]);

            });

        });

    });

});