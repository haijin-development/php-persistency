<?php

namespace Haijin\Persistency\Types_Converters;

class Json_To_String
{
    public function to_database($array)
    {
        return json_encode( $array );
    }

    public function from_database($string)
    {
        return json_decode( $string );
    }
}