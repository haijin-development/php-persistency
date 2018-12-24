<?php

namespace Haijin\Persistency\Errors\QueryExpressions;

use Haijin\Persistency\Errors\PersistencyError;

/**
 * Error raised when a query QueryBuilder encounters an unexpected expression.
 */
class UnexpectedExpressionError extends PersistencyError
{
    protected $expression;

    /// Initializing

    public function __construct($error_message, $expression)
    {
        parent::__construct( $error_message );

        $this->expression = $expression;
    }

    /// Accessing

    public function get_expression()
    {
        return $this->expression;
    }
}