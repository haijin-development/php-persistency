<?php

namespace Haijin\Persistency\Types_Converters;

class Boolean_To_String
{
    public function to_database($boolean)
    {
        if( $boolean === true ) {
            return "1";
        }

        if( $boolean === false ) {
            return "0";
        }

        throw new \RuntimeException( "Invalid boolean value" );
    }

    public function from_database($string)
    {
        if( $string === "1" || $string === "t" ) {
            return true;
        }

        if( $string === "0" || $string === "f" ) {
            return false;
        }

        throw new \RuntimeException( "Invalid string value" );
    }
}