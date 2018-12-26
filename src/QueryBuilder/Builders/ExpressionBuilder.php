<?php

namespace Haijin\Persistency\QueryBuilder\Builders;

use Haijin\Persistency\QueryBuilder\ExpressionsFactoryTrait;
use Haijin\Persistency\QueryBuilder\ExpressionsDSLTrait;

/**
 * An object to build and return Haijin\Persistency\QueryBuilder\Expressions using the DSL.
 * Its protocol is divided in the traits:
 *
 *      - ExpressionsFactoryTrait: has all the methods to create instances of
 *          concrete Expression subclasses.
 *
 *      - ExpressionsDSLTrait: has the DSL methods common to most expressions.
 */
class ExpressionBuilder
{
    use ExpressionsFactoryTrait;
    use ExpressionsDSLTrait;

    /**
     * The expression context of the top most expression being built.
     */
    protected $expression_context;

    /// Initializing

    /**
     * Initializes $this instance.
     *
     * @param ExpressionContext $expression_context The ExpressionContext of the top most
     *      Expression being built.
     */
    public function __construct($expression_context)
    {
        $this->context = $expression_context;
    }

    /// Evaluating

    /**
     * Sends the $method_name with the given $parameters to $this instance.
     * It would be the same as doing
     *
     *      $expression_builder->$method_name( ...$parameters );
     *
     * but it is more expressive and intention revealing.
     */
    public function receive($method_name, $parameters)
    {
        return $this->$method_name( ...$parameters );
    }
}