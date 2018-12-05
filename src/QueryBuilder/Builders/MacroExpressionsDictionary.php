<?php

namespace Haijin\Persistency\QueryBuilder\Builders;

use Haijin\Tools\Dictionary;
use Haijin\Persistency\Errors\MacroExpressionNotFoundError;
use Haijin\Persistency\Errors\MacroExpressionEvaluatedToNullError;

class MacroExpressionsDictionary
{
    /**
     * A dictionary with the macro expressions closures defined in this query.
     */
    protected $macro_expression_closures;

    /**
     * A dictionary with the resolved macro expressions in the query.
     * This Dictionary is populated lazily.
     */
    protected $macro_expressions;

    /// Initializing

    public function __construct()
    {
        $this->macro_expression_closures = new Dictionary();
        $this->macro_expressions = new Dictionary();
    }

    /// Accessing

    public function get_macro_expression_closures()
    {
        return $this->macro_expression_closures;
    }

    public function define($macro_expression_name, $macro_expression_closure)
    {
        $this->macro_expression_closures[ $macro_expression_name ] = $macro_expression_closure;
    }

    public function at($macro_expression_name, $query_builder)
    {
        $macro_expression =
            $this->macro_expressions->at_if_absent( $macro_expression_name, null );

        if( $macro_expression !== null ) {
            return $macro_expression;
        }

        $macro_closure = $this->macro_expression_closures->at_if_absent(
            $macro_expression_name,
            function() use($macro_expression_name) {
                $this->raise_macro_expression_not_found_error( $macro_expression_name );
            }, $this);

        $macro_expression = $macro_closure( $query_builder );

        if( $macro_expression === null ) {
            $this->raise_macro_expression_evaluated_to_null_error($macro_expression_name);
        }

        $this->macro_expressions[$macro_expression_name] = $macro_expression;

        return $macro_expression;
    }

    /// Merging

    public function merge_with($another_macro_expressions)
    {
        $this->macro_expression_closures->merge_with(
            $another_macro_expressions->get_macro_expression_closures()
        );
    }

    /// Errors

    protected function raise_macro_expression_not_found_error($macro_expression_name)
    {
        throw new MacroExpressionNotFoundError(
            "Macro expression named '{$macro_expression_name}' not found.",
            $macro_expression_name
        );
    }

    protected function raise_macro_expression_evaluated_to_null_error($macro_expression_name)
    {
        throw new MacroExpressionEvaluatedToNullError( "The macro expression '{$macro_expression_name}' evaluated to null. Probably it is missing the return statement." );
    }
}