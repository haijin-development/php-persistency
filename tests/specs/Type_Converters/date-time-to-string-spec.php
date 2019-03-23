<?php

use Haijin\Persistency\Types_Converters\DateTime_To_String;
use Haijin\Errors\Haijin_Error;

$spec->describe( "When converting values with a DateTime_To_String", function() {

    $this->let( "type_converter", function() {
        return new DateTime_To_String();
    });

    $this->describe( 'when converting to the database', function() {

        $this->it( "converts a datetime to a string", function() {

            $datetime = new \DateTime( '2000-12-31' );

            $this->expect( $this->type_converter->to_database( $datetime ) )
                ->to() ->be( '===' ) ->than( '2000-12-31 00:00:00' );

        });

        $this->it( "return the string if is a string", function() {

            $datetime = '2000-12-31';

            $this->expect( $this->type_converter->to_database( $datetime ) )
                ->to() ->be( '===' ) ->than( '2000-12-31' );

        });

        $this->it( "raises an error if it is not a datetime", function() {

            $this->expect( function() {

                 $this->type_converter->to_database( 0 );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( 'Invalid DateTime value.' );
                }
            );

        });

    });

    $this->describe( 'when converting from the database', function() {

        $this->it( "converts a date string to a datetime", function() {

            $this->expect( $this->type_converter->from_database( '2000-12-31' ) )
                ->to() ->equal( new \DateTime( '2000-12-31' ) );

        });

        $this->it( "converts a datetime string to a datetime", function() {

            $this->expect( $this->type_converter->from_database( '2000-12-31' ) )
                ->to() ->equal( new \DateTime( '2000-12-31 00:00:00' ) );

        });

        $this->it( "returns the datatime if it is a datetime", function() {

            $this->expect(
                $this->type_converter->from_database( new \DateTime( '2000-12-31' ) )
            ) ->to() ->equal( new \DateTime( '2000-12-31 00:00:00' ) );

        });

        $this->it( "raises an error if the string is not a valid datetime string", function() {

            $this->expect( function() {

                 $this->type_converter->from_database( 123 );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {
                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( 'Invalid datetime string value.' );
                }
            );

        });

    });

});