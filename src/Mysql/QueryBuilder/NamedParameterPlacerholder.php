<?php

namespace Haijin\Persistency\Mysql\QueryBuilder;

class NamedParameterPlacerholder
{
    protected $parameter_name;

    /// Initializing

    public function __construct($parameter_name)
    {
        $this->parameter_name = $parameter_name;
    }

    /// Accessing

    public function get_parameter_name()
    {
        return $this->parameter_name;
    }
}