<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;
use Haijin\Persistency\Announcements\About_To_Create_Object;
use Haijin\Persistency\Announcements\About_To_Update_Object;
use Haijin\Persistency\Announcements\About_To_Delete_Object;
use Haijin\Persistency\Announcements\Object_Created;
use Haijin\Persistency\Announcements\Object_Updated;
use Haijin\Persistency\Announcements\Object_Deleted;
use Haijin\Persistency\Announcements\Object_Creation_Canceled;
use Haijin\Persistency\Announcements\Object_Update_Canceled;
use Haijin\Persistency\Announcements\Object_Deletion_Canceled;

$spec->describe( "When canceling a create, update or delete in a Persisten_Collection on a Mysql database using its announcements", function() {

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

    $this->after_each( function() {

        Users_Collection::get()->drop_all_announcements_to( $this );

    });

    $this->after_all( function() {

        Users_Collection::get()->drop_all_announcements_to( $this );

    });

    $this->describe( "when canceling the creation of an object", function() {

        $this->it( "cancels the creation of an object", function() {

            Users_Collection::get()->when(
                About_To_Create_Object::class,
                $this,
                function($announcement) {
                    $announcement->cancel( "Cancelation reason" );
            });

            Users_Collection::do()->create_from_attributes([
                'name' => 'Lisa'
            ]);

            $this->expect( Users_Collection::get()->count() ) ->to() ->equal( 1 );

        });

        $this->it( "announces that it canceled the creation of an object", function() {

            Users_Collection::get()->when(
                About_To_Create_Object::class,
                $this,
                function($announcement) {
                    $announcement->cancel( "Cancelation reason" );
            });

            $this->expect( Users_Collection::get() ) ->during( function() {

                Users_Collection::do()->create_from_attributes([
                    'name' => 'Lisa'
                ]);

            }) ->to() ->announce(

                Object_Creation_Canceled::class,

                function($announcement){

                    $this->expect( $announcement->__toString() ) ->to()
                        ->equal( "Users_Persistent_Collection canceled the creation of an object User." );

                    $this->expect( $announcement->get_object() ) ->to() ->be()
                        ->exactly_like([
                            'get_id()' => null,
                            'get_name()' => 'Lisa'
                        ]);

                    $this->expect( $announcement->get_cancelation_reasons() ) ->to()
                        ->be() ->exactly_like([
                            'Cancelation reason'
                        ]);

            });

        });

    });

    $this->describe( "when canceling the update of an object", function() {

        $this->it( "cancels the update of an object", function() {

            Users_Collection::get()->when(
                About_To_Update_Object::class,
                $this,
                function($announcement) {
                    $announcement->cancel( "Cancelation reason" );
            });

            Users_Collection::do()->update_from_attributes( $this->user, [
                'name' => 'Margaret'
            ]);

            $this->expect( Users_Collection::get()->first() ) ->to() ->be()
                ->exactly_like([
                    'get_id()' => 1,
                    'get_name()' => 'Maggie'
                ]);

        });

        $this->it( "announces that it canceled the update of an object", function() {

            Users_Collection::get()->when(
                About_To_Update_Object::class,
                $this,
                function($announcement) {
                    $announcement->cancel( "Cancelation reason" );
            });

            $this->expect( Users_Collection::get() ) ->during( function() {

                Users_Collection::do()->update_from_attributes( $this->user, [
                    'name' => 'Maggie'
                ]);

            }) ->to() ->announce(

                Object_Update_Canceled::class,

                function($announcement){

                    $this->expect( $announcement->__toString() ) ->to()
                        ->equal( "Users_Persistent_Collection canceled the update of an object User." );

                    $this->expect( $announcement->get_object() ) ->to() ->be()
                        ->exactly_like([
                            'get_id()' => 1,
                            'get_name()' => 'Maggie'
                        ]);

                    $this->expect( $announcement->get_cancelation_reasons() ) ->to()
                        ->be() ->exactly_like([
                            'Cancelation reason'
                        ]);

            });

        });

    });

    $this->describe( "when canceling the deletion of an object", function() {

        $this->it( "cancels the deletion of an object", function() {

            Users_Collection::get()->when(
                About_To_Delete_Object::class,
                $this,
                function($announcement) {
                    $announcement->cancel( "Cancelation reason" );
            });

            Users_Collection::do()->delete( $this->user );

            $this->expect( Users_Collection::get()->first() ) ->to() ->be()
                ->exactly_like([
                    'get_id()' => 1,
                    'get_name()' => 'Maggie'
                ]);

        });

        $this->it( "announces that it canceled the deletion of an object", function() {

            Users_Collection::get()->when(
                About_To_Delete_Object::class,
                $this,
                function($announcement) {
                    $announcement->cancel( "Cancelation reason" );
            });

            $this->expect( Users_Collection::get() ) ->during( function() {

                Users_Collection::do()->delete( $this->user );

            }) ->to() ->announce(

                Object_Deletion_Canceled::class,

                function($announcement){

                    $this->expect( $announcement->__toString() ) ->to()
                        ->equal( "Users_Persistent_Collection canceled the deletion of an object User." );

                    $this->expect( $announcement->get_object() ) ->to() ->be()
                        ->exactly_like([
                            'get_id()' => 1,
                            'get_name()' => 'Maggie'
                        ]);

                    $this->expect( $announcement->get_cancelation_reasons() ) ->to()
                        ->be() ->exactly_like([
                            'Cancelation reason'
                        ]);

            });

        });

    });

});