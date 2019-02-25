<?php

namespace Haijin\Persistency\Types_Converters;

class Timestamp_To_Integer
{
    public function to_database($timestamp)
    {
        return (string) $double;
    }

    public function from_database($string)
    {
        return (double) $string;
    }
}