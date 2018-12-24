<?php

namespace Haijin\Persistency\Errors\QueryExpressions;

use Haijin\Persistency\Errors\PersistencyError;

/**
 * Error raised when a macro expression is used in a query filter without being previously defined.
 */
class MacroExpressionNotFoundError extends PersistencyError
{
    protected $macro_name;

    /// Initializing

    public function __construct($error_message, $macro_name)
    {
        parent::__construct( $error_message );

        $this->macro_name = $macro_name;
    }

    /// Accessing

    public function get_macro_name()
    {
        return $this->macro_name;
    }
}