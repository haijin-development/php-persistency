<?php

use Haijin\Persistency\Types_Converters\Integer_To_String;
use Haijin\Errors\Haijin_Error;

$spec->describe( "When converting values with a Integer_To_String", function() {

    $this->let( "type_converter", function() {
        return new Integer_To_String();
    });

    $this->describe( 'when converting to the database', function() {

        $this->it( "converts an integer to a string", function() {

            $this->expect( $this->type_converter->to_database( 123 ) )
                ->to() ->be( '===' ) ->than( '123' );

        });

        $this->it( "returns the string if it is an integer string", function() {

            $this->expect( $this->type_converter->to_database( '123' ) )
                ->to() ->be( '===' ) ->than( '123' );

        });

        $this->it( "raises an error for any other value", function() {

            $this->expect( function() {

                 $this->type_converter->to_database( 'abc' );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( 'Invalid integer value.' );
                }
            );

        });

    });

    $this->describe( 'when converting from the database', function() {

        $this->it( "converts a string to an integer", function() {

            $this->expect( $this->type_converter->from_database( '123' ) )
                ->to() ->be( '===' ) ->than( 123 );

        });

        $this->it( "returns the integer if it is an integer", function() {

            $this->expect( $this->type_converter->from_database( 123 ) )
                ->to() ->be( '===' ) ->than( 123 );

        });

        $this->it( "raises an error for any other value", function() {

            $this->expect( function() {

                 $this->type_converter->from_database( 1.1 );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( 'Invalid string value.' );
                }
            );

            $this->expect( function() {

                 $this->type_converter->from_database( '1.1' );

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