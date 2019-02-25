<?php

namespace Haijin\Persistency\Statement_Compiler;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Errors\Query_Expressions\Macro_Expression_Evaluated_To_Null_Error;
use Haijin\Persistency\Statements\Expressions\Expressions_Factory_Trait;
use Haijin\Persistency\Statements\Expressions\Expressions_DSL_Trait;

/**
 * Object to build a Create_Statement from a create statment definition closure.
 */
abstract class Statement_Compiler
{
    use Expressions_Factory_Trait;
    use Expressions_DSL_Trait;

    /**
     * A Dictionary with the macro expressions defined in the scope of $this->$statement_expression.
     */
    protected $expression_context;

    /**
     * A Dictionary with the macro expressions defined in the scope of $this->$statement_expression.
     */
    protected $statement_expression;

    /// Initializing

    /**
     * Initializes $this instance.
     *
     * @param Expression_Context $expression_context Optional - The Expression_Context of the
     *      Statement_Expression being built. If none is given a new Expression_Context is created.
     */
    public function __construct($expression_context = null)
    {
        if( $expression_context === null ) {
            $expression_context = $this->new_expression_context();
        }

        $this->context = $expression_context;

        $this->statement_expression = $this->new_statement_expression();
    }

    /**
     * Returns the concrete statement instance.
     */
    abstract protected function new_statement_expression();

    /// Accessing

    /**
     * Returns the Expression_Context of the Statement_Expression.
     *
     * @return Expression_Context The Expression_Context of the Statement_Expression.
     */
    public function get_context()
    {
        return $this->context;
    }

    /**
     * Returns the Dictionary with the macro definitions of the Statement_Expression.
     *
     * @return Dictionary The Dictionary with the macro definitions of the Statement_Expression.
     */
    public function get_macros_dictionary()
    {
        return $this->get_context()->get_macros_dictionary();
    }

    public function get_context_collection()
    {
        return $this->get_context()->get_current_collection();
    }

    /// Building expression

    /**
     * Builds and returns a new Statement_Expression.
     *
     * @param closure $expression_closure The closure to build the Statement_Expression
     *      using a DSL.
     * @param object $binding Optional - An optional object to bind the evaluation of the
     *      $expression_closure.
     *
     * @return Statement_Expression The built Statement_Expression.
     */
    public function build( $expression_closure, $binding = null )
    {
        $this->statement_expression = $this->new_statement_expression();

        $this->eval( $expression_closure, $binding );

        return $this->statement_expression;
    }

    /**
     * Evaluates the given $expression_closure with the current $this->statement_expression.
     * This method allows to build the Statement_Expression in different times instead of all
     * at once.
     *
     * @param closure $expression_closure The closure to build the Statement_Expression
     *      using a DSL.
     * @param object $binding Optional - An optional object to bind the evaluation of the
     *      $expression_closure.
     *
     * @return Statement_Expression The current $this->statement_expression.
     */
    public function eval($expression_closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        // temporary using $this instead of $binding because of an error
        $binding = $this;

        $expression_closure->call( $binding, $this );

        return $this->statement_expression;
    }

    /// Macro expressions

    public function let($macro_name, $definition_closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $macro_expression = $definition_closure->call( $binding, $this );

        if( $macro_expression === null ) {
            $this->_raise_macro_expression_evaluated_to_null_error( $macro_name );
        }

        $this->get_macros_dictionary()[ $macro_name ] = $macro_expression;
    }

    /**
     * Assumes that the attribute is a macro expressions. Searches for a defined macro
     * expression with that name and returns its evaluation. If none is found raises
     * an error.
     */
    public function __get($macro_name)
    {
        return $this->get_macros_dictionary()->at( $macro_name, $this );
    }

    /// Helper methods

    protected function _with_expression_context_do($expression_context, $closure)
    {
        $this->previous_expression_context = $this->context;

        $this->context = $expression_context;

        try {
            return $closure->call( $this );
        } finally {
            $this->context = $this->previous_expression_context;
        }
    }

    /// Raising errors

    protected function _raise_macro_expression_evaluated_to_null_error($macro_name)
    {
        throw Create::a( Macro_Expression_Evaluated_To_Null_Error::class )->with(
            "The macro expression '{$macro_name}' evaluated to null. Probably it is missing the return statement.",
            $macro_name
        );
    }
}