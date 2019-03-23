<?php

namespace Haijin\Persistency\Types_Converters;

use Haijin\Errors\Haijin_Error;

class Json_To_String
{
    public function to_database($array)
    {
        if( is_string( $array ) ) {
            return $array;
        }

        if( ! is_array( $array ) && ! is_a( $array, \stdclass::class ) ) {
            throw new Haijin_Error( "Invalid json value." );
        }

        return json_encode( $array );
    }

    public function from_database($string)
    {
        if( is_array( $string ) || is_a( $string, \stdclass::class ) ) {
            return $string;
        }

        $value = json_decode( $string, true );

        if( ! is_string( $string ) || $value === null ) {
            throw new Haijin_Error( "Invalid json string value." );
        }

        return $value;
    }
}