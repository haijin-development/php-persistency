<?php

namespace Haijin\Persistency\QueryBuilder\Builders;

use Haijin\Tools\Dictionary;
use Haijin\Persistency\Errors\MacroExpressionNotFoundError;
use Haijin\Persistency\Errors\MacroExpressionEvaluatedToNullError;

/**
 * This object holds all the necessary context any Expression needs.
 */
class ExpressionContext
{
    /**
     * A Dictionary with the macro definitions defined in the scope of a QueryExpression.
     */
    protected $macros_dictionary;

    /**
     * The CollectionExpression in which context a expression is stated.
     */
    protected $current_collection;

    // Initializing

    /**
     * Initializes $this instance.
     *
     * @param Dictionary $macros_dictionary A Dictionary with the macro definitions defined in
     *          the scope of a QueryExpression.
     * @param CollectionExpression $current_collection The CollectionExpression in which context
     *          a expression is stated.
     */
    public function __construct($macros_dictionary, $current_collection = null)
    {
        $this->macros_dictionary = $macros_dictionary;
        $this->current_collection = $current_collection;
    }

    /// Accessing

    /**
     * Returns the Dictionary with the macros defined in the scope of the Expression.
     *
     * @return Dictionary The Dictionary with the macros defined in the scope of the Expression.
     */
    public function get_macros_dictionary()
    {
        return $this->macros_dictionary;
    }

    /**
     * Returns the CollectionExpression in which context a Expression was stated.
     *
     * @return CollectionExpression The CollectionExpression in which context a
     *      Expression was stated.
     */
    public function get_current_collection()
    {
        return $this->current_collection;
    }

    /**
     * Sets the CollectionExpression in which context a Expression was stated.
     *
     * @param CollectionExpression The CollectionExpression in which context a
     *      Expression was stated.
     */
    public function set_current_collection($collection_expression)
    {
        $this->current_collection = $collection_expression;
    }

    /// Adding

    /**
     * Merges the macro definitions from the given $another_macros_dictionary into
     * $this->macros_dictionary.
     *
     * @param Dictionary $another_macros_dictionary A Dictionary with macro definitions. 
     */
    public function add_macro_definitions_from($another_macros_dictionary)
    {
        $this->macros_dictionary->merge_with( $another_macros_dictionary );
    }
}