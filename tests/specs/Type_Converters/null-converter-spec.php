<?php

use Haijin\Persistency\Types_Converters\Null_Converter;
use Haijin\Errors\Haijin_Error;

$spec->describe( "When converting values with a Null_Converter", function() {

    $this->let( "type_converter", function() {
        return new Null_Converter();
    });

    $this->describe( 'when converting to the database', function() {

        $this->it( "returns the same value", function() {

            $this->expect( $this->type_converter->to_database( 123 ) )
                ->to() ->be( '===' ) ->than( 123 );

        });

    });

    $this->describe( 'when converting from the database', function() {

        $this->it( "returns the same value", function() {

            $this->expect( $this->type_converter->from_database( 123 ) )
                ->to() ->be( '===' ) ->than( 123 );

        });

    });

});