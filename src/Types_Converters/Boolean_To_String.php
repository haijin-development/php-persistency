<?php

namespace Haijin\Persistency\Types_Converters;

use Haijin\Errors\Haijin_Error;

class Boolean_To_String
{
    public function to_database($boolean)
    {
        if( $boolean === '1' || $boolean === '0' ) {
            return $boolean;
        }

        if( $boolean === true ) {
            return '1';
        }

        if( $boolean === false ) {
            return '0';
        }

        throw new Haijin_Error( "Invalid boolean value." );
    }

    public function from_database($string)
    {
        if( $string === true || $string === false ) {
            return $string;
        }

        if( $string === "1" || $string === "t" ) {
            return true;
        }

        if( $string === "0" || $string === "f" ) {
            return false;
        }

        throw new Haijin_Error( "Invalid string value." );
    }
}