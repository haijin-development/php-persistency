<?php

namespace Haijin\Persistency\Types_Converters;

class DateTime_To_String
{
    public function to_database($datetime)
    {
        return $datetime->format( 'Y-m-d H:i:s');
    }

    public function from_database($string)
    {
        return new \DateTime( $string );
    }
}