<?php

namespace Haijin\Persistency\Errors\QueryExpressions;

use Haijin\Persistency\Errors\PersistencyError;

/**
 * Error raised when a query Query_Builder encounters an unexpected expression.
 */
class UnexpectedExpressionError extends PersistencyError
{
    /**
     * The unexpected Query_Expression.
     */
    protected $expression;

    /// Initializing

    /**
     * Initializes $this instance.
     *
     * @param string $error_message The error message.
     * @param Query_Expression $expression The unexpected Query_Expression.
     */
    public function __construct($error_message, $expression)
    {
        parent::__construct( $error_message );

        $this->expression = $expression;
    }

    /// Accessing

    /**
     * Returns the unexpected Query_Expression.
     *
     * @return Query_Expression The unexpected Query_Expression.
     */
    public function get_expression()
    {
        return $this->expression;
    }
}