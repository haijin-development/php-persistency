<?php

namespace Haijin\Persistency\QueryBuilder\Builders;

use Haijin\Tools\Dictionary;
use Haijin\Persistency\Errors\MacroExpressionNotFoundError;
use Haijin\Persistency\Errors\MacroExpressionEvaluatedToNullError;

/**
 * This object holds all the necesary context any Expression needs.
 */
class ExpressionContext
{
    public function __construct($macro_expressions, $current_collection = null)
    {
        $this->macro_expressions = $macro_expressions;
        $this->current_collection = $current_collection;
    }

    /**
     * A dictionary with the macro definitions.
     */
    protected $macro_expressions;

    /**
     * The collection in which context the expression is stated.
     */
    protected $current_collection;

    /// Accessing

    public function get_macros_dictionary()
    {
        return $this->macro_expressions;
    }

    public function get_current_collection()
    {
        return $this->current_collection;
    }

    public function set_current_collection($collection_expression)
    {
        $this->current_collection = $collection_expression;
    }

    /// Expression context

    public function add_to_macro_expressions($another_macro_expressions)
    {
        $this->macro_expressions->merge_with( $another_macro_expressions );
    }
}