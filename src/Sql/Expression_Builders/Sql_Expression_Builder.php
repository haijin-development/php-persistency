<?php

namespace Haijin\Persistency\Sql\Expression_Builders;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements_Visitors\Expression_Visitor;

/**
 * Base class for objects building SQL from a
 * \Haijin\Persistency\Statements\Expressions\Expression subclass.
 */
class Sql_Expression_Builder extends Expression_Visitor
{
    protected $collected_parameters;

    public function __construct($collected_parameters)
    {
        $this->collected_parameters = $collected_parameters;
    }

    public function get_collected_parameters()
    {
        return $this->collected_parameters;
    }

    /// Building

    public function build_sql_from($expression)
    {
        return $this->visit( $expression );
    }

    /// Appending sql

    protected function escape_sql($text)
    {
        return \addslashes( $text );
    }

    protected function value_to_sql($value)
    {
        if( is_string( $value ) ) {
            return "'" . $this->escape_sql( $value ) . "'";
        }

        return $this->escape_sql( (string) $value );
    }

    protected function expression_sql_from($expression, $expression_compiler = null)
    {
        if( $expression_compiler === null ) {
            $expression_compiler = $this->new_sql_expression_builder();
        }

        return $expression_compiler->build_sql_from( $expression );
    }

    protected function collect_expressions_sql($expressions)
    {
        $expression_compiler = $this->new_sql_expression_builder();

        return $expressions->collect(
            function($expression) use($expression_compiler){
                return $this->expression_sql_from( $expression, $expression_compiler );
            },
            $this
        );
    }

    protected function expressions_list($expressions)
    {
        return $this->collect_expressions_sql( $expressions )
            ->join_with( ", " );        
    }
}
