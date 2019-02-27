<?php

namespace Haijin\Persistency\Sql;

use Haijin\Instantiator\Create;

trait Sql_Builder_Trait
{
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
