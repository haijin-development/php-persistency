<?php

namespace Haijin\Persistency\Types_Converters;

class Boolean_To_Integer
{
    public function to_database($boolean)
    {
        if( $boolean === true ) {
            return 1;
        }

        if( $boolean === false ) {
            return 0;
        }

        throw new \RuntimeException( "Invalid boolean value" );
    }

    public function from_database($integer)
    {
        if( $integer === 1 ) {
            return true;
        }

        if( $integer === 0 ) {
            return false;
        }

        throw new \RuntimeException( "Invalid string value" );
    }
}