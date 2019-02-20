<?php

namespace Haijin\Persistency\Statement_Compiler;

use Haijin\Persistency\Statements\Expressions\Expressions_Factory_Trait;
use Haijin\Persistency\Statements\Expressions\Expressions_DSL_Trait;

/**
 * An object to build and return Haijin\Persistency\Statements\Expressions using the DSL.
 * Its protocol is divided in the traits:
 *
 *      - Expressions_Factory_Trait: has all the methods to create instances of
 *          concrete Expression subclasses.
 *
 *      - Expressions_DSL_Trait: has the DSL methods common to most expressions.
 */
class Expression_Compiler
{
    use Expressions_Factory_Trait;
    use Expressions_DSL_Trait;

    /**
     * The expression context of the top most expression being built.
     */
    protected $expression_context;

    /// Initializing

    /**
     * Initializes $this instance.
     *
     * @param Expression_Context $expression_context The Expression_Context of the top most
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
     *      $expression_compiler->$method_name( ...$parameters );
     *
     * but it is more expressive and intention revealing.
     */
    public function receive($method_name, $parameters)
    {
        return $this->$method_name( ...$parameters );
    }
}