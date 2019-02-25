<?php

namespace Haijin\Persistency\Types_Converters;

class Double_To_String
{
    public function to_database($double)
    {
        return (string) $double;
    }

    public function from_database($string)
    {
        return (double) $string;
    }
}