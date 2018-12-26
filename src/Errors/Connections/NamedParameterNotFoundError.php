<?php

namespace Haijin\Persistency\Errors\Connections;

use Haijin\Persistency\Errors\PersistencyError;

/**
 * Error raised when the query expected a named parameter that is missing in the given 
 * $named_parameters.
 */
class NamedParameterNotFoundError extends PersistencyError
{
    /**
     * The name of the missing paramter.
     */
    protected $parameter_name;

    /// Initializing

    /**
     * Initializes $this instance.
     *
     * @param string $error_message The error message.
     * @param string $parameter_name The name of the missing parameter.
     */
    public function __construct($error_message, $parameter_name)
    {
        parent::__construct( $error_message );

        $this->parameter_name = $parameter_name;
    }

    /// Accessing

    /**
     * Returns the name of the missing parameter.
     *
     * @return string The name of the missing parameter.
     */
    public function get_parameter_name()
    {
        return $this->parameter_name;
    }
}