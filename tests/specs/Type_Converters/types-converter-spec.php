<?php

use Haijin\Persistency\Types_Converters\Types_Converter;
use Haijin\Errors\Haijin_Error;

$spec->describe( "When converting values with a Types_Converter", function() {

    $this->let( "types_converter", function() {
        return new Types_Converter();
    });

    $this->describe( 'when converting to the database', function() {

        $this->it( "converts a null", function() {

            $value = $this->types_converter->convert_to_database( null );

            $this->expect( $value ) ->to() ->be() ->null();
        });

        $this->it( "converts a boolean", function() {

            $value = $this->types_converter->convert_to_database( true );

            $this->expect( $value ) ->to() ->equal( 1 );
        });

        $this->it( "converts a string", function() {

            $value = $this->types_converter->convert_to_database( 'a' );

            $this->expect( $value ) ->to() ->equal( 'a' );
        });

        $this->it( "converts an int", function() {

            $value = $this->types_converter->convert_to_database( 123 );

            $this->expect( $value ) ->to() ->equal( 123 );
        });

        $this->it( "converts a double", function() {

            $value = $this->types_converter->convert_to_database( 1.1 );

            $this->expect( $value ) ->to() ->equal( 1.1 );
        });

        $this->it( "converts a date time", function() {

            $value = $this->types_converter->convert_to_database(
                new DateTime( '2000-12-31' )
            );

            $this->expect( $value ) ->to() ->equal( '2000-12-31 00:00:00' );
        });

        $this->it( "converts a json", function() {

            $value = $this->types_converter->convert_to_database( [ 1 ] );

            $this->expect( $value ) ->to() ->equal( '[1]' );


            $value = $this->types_converter->convert_to_database( (object) [ 'a' => 1 ] );

            $this->expect( $value ) ->to() ->equal( '{"a":1}' );

        });

        $this->it( "raises an error for an unkown type", function() {

            $this->expect( function() {

                $this->types_converter->convert_to_database( new Custom() );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( "Unkown type converter: 'Custom'." );
                }
            );
        });

    });

    $this->describe( 'when converting from the database', function() {

        $this->it( "converts a null", function() {

            $value = $this->types_converter->convert_from_database( 'string', null );

            $this->expect( $value ) ->to() ->be() ->null();
        });

        $this->it( "converts a boolean", function() {

            $value = $this->types_converter->convert_from_database( 'boolean', '1' );

            $this->expect( $value ) ->to() ->equal( 1 );
        });

        $this->it( "converts a string", function() {

            $value = $this->types_converter->convert_from_database( 'string', 'a' );

            $this->expect( $value ) ->to() ->equal( 'a' );
        });

        $this->it( "converts an int", function() {

            $value = $this->types_converter->convert_from_database( 'integer', '123' );

            $this->expect( $value ) ->to() ->equal( 123 );
        });

        $this->it( "converts a double", function() {

            $value = $this->types_converter->convert_from_database( 'double', '1.1' );

            $this->expect( $value ) ->to() ->equal( 1.1 );
        });

        $this->it( "converts a date time", function() {

            $value = $this->types_converter->convert_from_database(
                'date_time',
                '2000-12-31 00:00:00'
            );

            $this->expect( $value ) ->to() ->equal( new DateTime( '2000-12-31' ) );
        });

        $this->it( "converts a json", function() {

            $value = $this->types_converter->convert_from_database( 'json', '{"a":1}' );

            $this->expect( $value ) ->to() ->equal( [ 'a' => 1 ] );

        });

        $this->it( "raises an error for an unkown type", function() {

            $this->expect( function() {

                $this->types_converter->convert_from_database( 'custom', new Custom() );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( "Unkown type converter: 'custom'." );
                }
            );
        });

    });

});

class Custom
{
}