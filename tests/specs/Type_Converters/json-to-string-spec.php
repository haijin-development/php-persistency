<?php

use Haijin\Persistency\Types_Converters\Json_To_String;
use Haijin\Errors\Haijin_Error;

$spec->describe( "When converting values with a Json_To_String", function() {

    $this->let( "type_converter", function() {
        return new Json_To_String();
    });

    $this->describe( 'when converting to the database', function() {

        $this->it( "converts an array to a json string", function() {

            $this->expect( $this->type_converter->to_database( [ 'a' => 123 ] ) )
                ->to() ->be( '===' ) ->than( '{"a":123}' );

        });

        $this->it( "converts a stdclass to a json string", function() {

            $this->expect( $this->type_converter->to_database( (object)[ 'a' => 123 ] ) )
                ->to() ->be( '===' ) ->than( '{"a":123}' );

        });

        $this->it( "returns a string if it is a string", function() {

            $this->expect( $this->type_converter->to_database( '{"a":123}' ) )
                ->to() ->be( '===' ) ->than( '{"a":123}' );

        });

        $this->it( "raises an error for any other value", function() {

            $this->expect( function() {

                 $this->type_converter->to_database( null );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( 'Invalid json value.' );
                }
            );

        });

    });

    $this->describe( 'when converting from the database', function() {

        $this->it( "converts a json string to an array", function() {

            $this->expect( $this->type_converter->from_database( '{"a":123}' ) )
                ->to() ->be( '===' ) ->than( [ 'a' => 123 ] );

        });

        $this->it( "returns an array if it is an array", function() {

            $this->expect( $this->type_converter->from_database( [ 'a' => 123 ] ) )
                ->to() ->be( '===' ) ->than( [ 'a' => 123 ] );

        });

        $this->it( "returns a stdclass if it is a stdclass", function() {

            $this->expect( $this->type_converter->from_database( (object)[ 'a' => 123 ] ) )
                ->to() ->be( '==' ) ->than( (object) [ 'a' => 123 ] );

        });

        $this->it( "raises an error for any other value", function() {

            $this->expect( function() {

                 $this->type_converter->from_database( null );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( 'Invalid json string value.' );
                }
            );

            $this->expect( function() {

                 $this->type_converter->from_database( '{ 123' );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( 'Invalid json string value.' );
                }
            );

        });

    });

});