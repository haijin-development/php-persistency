<?php

namespace Haijin\Persistency\Types_Converters;

use Haijin\Errors\Haijin_Error;

class DateTime_To_String
{
    public function to_database($datetime)
    {
        if( is_string( $datetime ) ) {
            return $datetime;
        }

        if( ! is_a( $datetime, \DateTime::class ) ) {
            throw new Haijin_Error( "Invalid DateTime value." );
        }

        return $datetime->format( 'Y-m-d H:i:s');
    }

    public function from_database($string)
    {
        if( is_a( $string, \DateTime::class ) ) {
            return $string;
        }

        try {
            return new \DateTime( $string );
        } catch( \Exception $e ) {
            throw new Haijin_Error( 'Invalid datetime string value.' );
        }
    }
}