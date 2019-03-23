<?php

use Haijin\Persistency\Types_Converters\Boolean_To_String;
use Haijin\Errors\Haijin_Error;

$spec->describe( "When converting values with a Boolean_To_String", function() {

    $this->let( "type_converter", function() {
        return new Boolean_To_String();
    });

    $this->describe( 'when converting to the database', function() {

        $this->it( "converts true to '1'", function() {

            $this->expect( $this->type_converter->to_database( true ) )
                ->to() ->be( '===' ) ->than( '1' );

        });

        $this->it( "converts false to '0'", function() {

            $this->expect( $this->type_converter->to_database( false ) )
                ->to() ->be( '===' ) ->than( '0' );

        });

        $this->it( "converts true to true", function() {

            $this->expect( $this->type_converter->to_database( '1' ) )
                ->to() ->be( '===' ) ->than( '1' );

        });

        $this->it( "converts false to false", function() {

            $this->expect( $this->type_converter->to_database( '0' ) )
                ->to() ->be( '===' ) ->than( '0' );

        });

        $this->it( "raises an error for any other value", function() {

            $this->expect( function() {

                 $this->type_converter->to_database( 0 );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( 'Invalid boolean value.' );
                }
            );

        });

    });

    $this->describe( 'when converting from the database', function() {

        $this->it( "converts '1' to true", function() {

            $this->expect( $this->type_converter->from_database( '1' ) )
                ->to() ->be( '===' ) ->than( true );

        });

        $this->it( "converts 't' to true", function() {

            $this->expect( $this->type_converter->from_database( 't' ) )
                ->to() ->be( '===' ) ->than( true );

        });

        $this->it( "converts '0' to false", function() {

            $this->expect( $this->type_converter->from_database( '0' ) )
                ->to() ->be( '===' ) ->than( false );

        });

        $this->it( "converts 'f' to false", function() {

            $this->expect( $this->type_converter->from_database( 'f' ) )
                ->to() ->be( '===' ) ->than( false );

        });

        $this->it( "converts true to true", function() {

            $this->expect( $this->type_converter->from_database( true ) )
                ->to() ->be( '===' ) ->than( true );

        });

        $this->it( "converts false to false", function() {

            $this->expect( $this->type_converter->from_database( false ) )
                ->to() ->be( '===' ) ->than( false );

        });

        $this->it( "raises an error for any other value", function() {

            $this->expect( function() {

                 $this->type_converter->from_database( 123 );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( 'Invalid string value.' );
                }
            );

        });

    });

});