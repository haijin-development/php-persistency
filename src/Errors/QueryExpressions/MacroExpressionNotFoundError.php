<?php

namespace Haijin\Persistency\Errors\QueryExpressions;

use Haijin\Persistency\Errors\PersistencyError;

/**
 * Error raised when a macro expression is used in a query filter without being previously defined.
 */
class MacroExpressionNotFoundError extends PersistencyError
{
    /**
     * The name of the missing macro.
     */
    protected $macro_name;

    /// Initializing

    /**
     * Initializes $this instance.
     *
     * @param string $error_message The error message.
     * @param string $macro_name The name of the missing macro.
     */
    public function __construct($error_message, $macro_name)
    {
        parent::__construct( $error_message );

        $this->macro_name = $macro_name;
    }

    /// Accessing

    /**
     * Returns The name of the missing macro.
     *
     * @return string The name of the missing macro.
     */
    public function get_macro_name()
    {
        return $this->macro_name;
    }
}