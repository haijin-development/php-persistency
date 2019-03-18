<?php

namespace Haijin\Persistency\Errors\Query_Expressions;

use Haijin\Persistency\Errors\Persistency_Error;

/**
 * Error raised when a query expression is invalid.
 */
class Invalid_Expression_Error extends Persistency_Error
{
    /**
     * The unexpected Expression.
     */
    protected $expression;

    /// Initializing

    /**
     * Initializes $this instance.
     *
     * @param string $error_message The error message.
     * @param Expression $expression The unexpected Expression.
     */
    public function __construct($error_message, $expression = null)
    {
        parent::__construct( $error_message );

        $this->expression = $expression;
    }

    /// Accessing

    /**
     * Returns the unexpected Expression.
     *
     * @return Expression The unexpected Expression.
     */
    public function get_expression()
    {
        return $this->expression;
    }
}