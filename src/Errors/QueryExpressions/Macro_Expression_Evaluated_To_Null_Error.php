<?php

namespace Haijin\Persistency\Errors\QueryExpressions;

use Haijin\Persistency\Errors\Persistency_Error;

/**
 * Error raised when a macro expression used in a query filter evaluates to null.
 * That usually happens because of a missing return statement.
 */
class Macro_Expression_Evaluated_To_Null_Error extends Persistency_Error
{
    /**
     * The name of the macro that evaluated to null.
     */
    protected $macro_name;

    /// Initializing

    /**
     * Initializes $this instance.
     *
     * @param string $error_message The error message.
     * @param string $macro_name The name of the macro that evaluated to null.
     */
    public function __construct($error_message, $macro_name)
    {
        parent::__construct( $error_message );

        $this->macro_name = $macro_name;
    }

    /// Accessing

    /**
     * Returns The name of the macro that evaluated to null.
     *
     * @return string The name of the macro that evaluated to null.
     */
    public function get_macro_name()
    {
        return $this->macro_name;
    }
}