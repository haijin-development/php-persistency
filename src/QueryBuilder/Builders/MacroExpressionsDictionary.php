<?php

namespace Haijin\Persistency\QueryBuilder\Builders;

use Haijin\Tools\Dictionary;
use Haijin\Persistency\Errors\MacroExpressionNotFoundError;
use Haijin\Persistency\Errors\MacroExpressionEvaluatedToNullError;

class MacroExpressionsDictionary
{
    /**
     * A dictionary with the resolved macro expressions in the query.
     */
    protected $macros;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct()
    {
        $this->macros = new Dictionary();
    }

    /// Accessing

    /**
     * Initializes $this instance.
     */
    public function get_macros()
    {
        return $this->macros;
    }

    public function set($macro_name, $macros)
    {
        $this->macros[ $macro_name ] = $macros;
    }

    public function at($macro_name, $query_builder)
    {
        return $this->macros->at_if_absent(
            $macro_name,
            function() use($macro_name) {
                $this->raise_macro_expression_not_found_error( $macro_name );
            }, $this);
    }

    /// Merging

    public function merge_with($another_macros_dictionary)
    {
        $this->macros->merge_with(
            $another_macros_dictionary->get_macros()
        );
    }

    /// Errors

    protected function raise_macro_expression_not_found_error($macro_name)
    {
        throw new MacroExpressionNotFoundError(
            "Macro expression named '{$macro_name}' not found.",
            $macro_name
        );
    }

    protected function raise_macro_expression_evaluated_to_null_error($macro_name)
    {
        throw new MacroExpressionEvaluatedToNullError( "The macro expression '{$macro_name}' evaluated to null. Probably it is missing the return statement." );
    }
}