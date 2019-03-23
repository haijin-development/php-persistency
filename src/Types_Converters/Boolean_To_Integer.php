<?php

namespace Haijin\Persistency\Types_Converters;

use Haijin\Errors\Haijin_Error;

class Boolean_To_Integer
{
    public function to_database($boolean)
    {
        if( $boolean === 1 || $boolean === 0 ) {
            return $boolean;
        }

        if( $boolean === true ) {
            return 1;
        }

        if( $boolean === false ) {
            return 0;
        }

        throw new Haijin_Error( "Invalid boolean value." );
    }

    public function from_database($integer)
    {
        if( $integer === true || $integer === false ) {
            return $integer;
        }

        if( $integer === 1 ) {
            return true;
        }

        if( $integer === 0 ) {
            return false;
        }

        throw new Haijin_Error( "Invalid integer value." );
    }
}