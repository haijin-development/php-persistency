<?php

namespace Haijin\Persistency\Statement_Compiler;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Errors\Query_Expressions\Macro_Expression_Evaluated_To_Null_Error;
use Haijin\Persistency\Statements\Expressions\Expressions_Factory_Trait;
use Haijin\Persistency\Statements\Expressions\Expressions_DSL_Trait;

/**
 * Object to build a Create_Statement from a create statment definition callable.
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
     * Compiles and returns a new Statement_Expression.
     *
     * @param callable $expression_callable The callable to build the Statement_Expression
     *      using a DSL.
     *
     * @return Statement_Expression The built Statement_Expression.
     */
    public function compile($expression_callable)
    {
        $this->statement_expression = $this->new_statement_expression();

        $this->eval( $expression_callable );

        return $this->statement_expression;
    }

    /**
     * Evaluates the given $expression_callable with the current $this->statement_expression.
     * This method allows to build the Statement_Expression in different times instead of all
     * at once.
     *
     * @param callable $expression_callable The callable to build the Statement_Expression
     *      using a DSL.
     *
     * @return Statement_Expression The current $this->statement_expression.
     */
    public function eval($expression_callable)
    {
        $expression_callable( $this );

        return $this->statement_expression;
    }

    /// DSL

    /**
     * Defines the collection name of $this Create_Statement.
     * Returns a CollectionExpressionBuilder to allow further configuration of the
     * Collection_Expression.
     *
     * @param string $collection_name The name of the root collection to query for.
     *
     * @return CollectionExpressionBuilder Returns $this object to allow
     *      further configuration of the Collection_Expression.
     */
    public function collection($collection_name)
    {
        $collection  = $this->new_collection_expression( $collection_name );

        $this->context->set_current_collection( $collection );

        $this->statement_expression->set_collection_expression( $collection );

        return $this;
    }

    /**
     * Defines the statement extra parameters.
     *
     * This parameters may be used by each particular database engine to configure
     * a query in particular.
     *
     * @param object $extra_parameters Any object passed as extra parameters..
     *
     * @return CollectionExpressionBuilder Returns $this object to allow
     *      further configuration of the Collection_Expression.
     */
    public function extra_parameters($extra_parameters)
    {
        $this->statement_expression->set_extra_parameters( $extra_parameters );

        return $this;
    }

    /// Macro expressions

    public function let($macro_name, $definition_callable)
    {
        $macro_expression = $definition_callable( $this );

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

    protected function _with_expression_context_do($expression_context, $callable)
    {
        $this->previous_expression_context = $this->context;

        $this->context = $expression_context;

        try {
            return $callable();
        } finally {
            $this->context = $this->previous_expression_context;
        }
    }

    /// Raising errors

    protected function _raise_macro_expression_evaluated_to_null_error($macro_name)
    {
        throw new Macro_Expression_Evaluated_To_Null_Error(
            "The macro expression '{$macro_name}' evaluated to null. Probably it is missing the return statement.",
            $macro_name
        );
    }
}