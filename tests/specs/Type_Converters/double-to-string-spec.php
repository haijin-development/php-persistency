<?php

use Haijin\Persistency\Types_Converters\Double_To_String;
use Haijin\Errors\Haijin_Error;

$spec->describe( "When converting values with a Double_To_String", function() {

    $this->let( "type_converter", function() {
        return new Double_To_String();
    });

    $this->describe( 'when converting to the database', function() {

        $this->it( "converts a double to a string", function() {

            $this->expect( $this->type_converter->to_database( 1.1 ) )
                ->to() ->be( '===' ) ->than( '1.1' );

        });

        $this->it( "returns the string if it is a string", function() {

            $this->expect( $this->type_converter->to_database( '1.1' ) )
                ->to() ->be( '===' ) ->than( '1.1' );

        });

        $this->it( "raises an error for any other value", function() {

            $this->expect( function() {

                 $this->type_converter->to_database( 'abc' );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( 'Invalid double value.' );
                }
            );

        });

    });

    $this->describe( 'when converting from the database', function() {

        $this->it( "converts a string to a double", function() {

            $this->expect( $this->type_converter->from_database( '1.1' ) )
                ->to() ->be( '===' ) ->than( 1.1 );

        });

        $this->it( "returns the double if it is a double", function() {

            $this->expect( $this->type_converter->from_database( 1.1 ) )
                ->to() ->be( '===' ) ->than( 1.1 );

        });

        $this->it( "raises an error for any other value", function() {

            $this->expect( function() {

                 $this->type_converter->from_database( 'a' );

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