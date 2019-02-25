<?php

namespace Haijin\Persistency\Types_Converters;

class Integer_To_String
{
    public function to_database($integer)
    {
        return (string) $integer;
    }

    public function from_database($string)
    {
        return (int) $string;
    }
}