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
    protected $macro_expressions;

    /// Initializing

    public function __construct($macro_expressions)
    {
        $this->macro_expressions = $macro_expressions;
    }

    /// Evaluating

    public function receive($method_name, $parameters)
    {
        return $this->$method_name( ...$parameters );
    }
}