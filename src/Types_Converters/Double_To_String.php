<?php

namespace Haijin\Persistency\Types_Converters;

use Haijin\Errors\Haijin_Error;

class Double_To_String
{
    public function to_database($double)
    {
        if( (string)(double) $double === $double ) {
            return $double;
        }

        if( ! is_double( $double ) ) {
            throw new Haijin_Error( "Invalid double value." );
        }

        return (string) $double;
    }

    public function from_database($string)
    {
        if( is_double( $string ) ) {
            return $string;
        }

        if( ! is_string( $string ) || ! is_numeric( $string ) ) {
            throw new Haijin_Error( "Invalid string value." );
        }

        return (double) $string;
    }
}