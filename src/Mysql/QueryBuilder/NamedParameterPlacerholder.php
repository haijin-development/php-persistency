<?php

namespace Haijin\Persistency\Mysql\QueryBuilder;

/**
 * In many database servers support parametrized values in their queries, meaning that a parameter
 * placeholder is used instead of the actual value. Then the actual values are provided during the
 * execution of the query.
 * This class models such parameter placeholders.
 */
class NamedParameterPlacerholder
{
    /**
     * The name of the parameter.
     */
    protected $parameter_name;

    /// Initializing

    /**
     * Initializes $this intance.
     *
     * @param string $parameter_name The name of the parameter.
     */
    public function __construct($parameter_name)
    {
        $this->parameter_name = $parameter_name;
    }

    /// Accessing

    /**
     * Returns the name the parameter.
     *
     * @return string The name of the parameter.
     */
    public function get_parameter_name()
    {
        return $this->parameter_name;
    }
}