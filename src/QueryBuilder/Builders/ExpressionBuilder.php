<?php

namespace Haijin\Persistency\QueryBuilder\Builders;

use Haijin\Persistency\QueryBuilder\ExpressionsFactoryTrait;
use Haijin\Persistency\QueryBuilder\ExpressionsDSLTrait;

/**
 * An object to build and return expressions using the DSL.
 */
class ExpressionBuilder
{
    use ExpressionsFactoryTrait;
    use ExpressionsDSLTrait;

    /**
     * A dictionary with the macro expressions defined in the query.
     */
    protected $expression_context;

    /// Initializing

    public function __construct($expression_context)
    {
        $this->context = $expression_context;
    }

    /// Evaluating

    public function receive($method_name, $parameters)
    {
        return $this->$method_name( ...$parameters );
    }
}