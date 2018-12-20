<?php

namespace Haijin\Persistency\Errors\Connections;

use Haijin\Persistency\Errors\PersistencyError;

class NamedParameterNotFoundError extends PersistencyError
{
    protected $parameter_name;

    /// Initializing

    public function __construct($error_message, $parameter_name)
    {
        parent::__construct( $error_message );

        $this->parameter_name = $parameter_name;
    }

    /// Accessing

    public function get_parameter_name()
    {
        return $this->parameter_name;
    }
}