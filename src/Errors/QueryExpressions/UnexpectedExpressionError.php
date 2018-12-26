<?php

namespace Haijin\Persistency\Errors\QueryExpressions;

use Haijin\Persistency\Errors\PersistencyError;

/**
 * Error raised when a query QueryBuilder encounters an unexpected expression.
 */
class UnexpectedExpressionError extends PersistencyError
{
    /**
     * The unexpected QueryExpression.
     */
    protected $expression;

    /// Initializing

    /**
     * Initializes $this instance.
     *
     * @param string $error_message The error message.
     * @param QueryExpression $expression The unexpected QueryExpression.
     */
    public function __construct($error_message, $expression)
    {
        parent::__construct( $error_message );

        $this->expression = $expression;
    }

    /// Accessing

    /**
     * Returns the unexpected QueryExpression.
     *
     * @return QueryExpression The unexpected QueryExpression.
     */
    public function get_expression()
    {
        return $this->expression;
    }
}