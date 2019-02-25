<?php

namespace Haijin\Persistency\Types_Converters;

class Null_Converter
{
    public function to_database($value)
    {
        return $value;
    }

    public function from_database($value)
    {
        return $value;
    }
}