<?php

namespace Haijin\Persistency\Types_Converters;

use Haijin\Errors\Haijin_Error;

class Integer_To_String
{
    public function to_database($integer)
    {
        if( (string)(int) $integer === $integer ) {
            return $integer;
        }

        if( ! is_int( $integer) ) {
            throw new Haijin_Error( "Invalid integer value." );
        }

        return (string) $integer;
    }

    public function from_database($string)
    {
        if( is_int( $string ) ) {
            return $string;
        }

        $value = (int) $string;

        if( ! is_string( $string ) ||
            ! is_numeric( $string ) ||
            $string !== (string) $value
          ) {
            throw new Haijin_Error( "Invalid string value." );
        }

        return $value;
    }
}