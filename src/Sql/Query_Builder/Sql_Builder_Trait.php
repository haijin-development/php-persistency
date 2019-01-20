<?php

namespace Haijin\Persistency\Sql\Query_Builder;

use Haijin\Persistency\Factory\Factory;
use Haijin\Persistency\Errors\QueryExpressions\Unexpected_Expression_Error;

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

    protected function expression_sql_from($expression, $expression_builder = null)
    {
        if( $expression_builder === null ) {
            $expression_builder = $this->new_sql_expression_builder();
        }

        return $expression_builder->build_sql_from( $expression );
    }

    protected function collect_expressions_sql($expressions)
    {
        $expression_builder = $this->new_sql_expression_builder();

        return $expressions->collect(
            function($expression) use($expression_builder){
                return $this->expression_sql_from( $expression, $expression_builder );
            },
            $this
        );
    }

    protected function expressions_list($expressions)
    {
        return $this->collect_expressions_sql( $expressions )
            ->join_with( ", " );        
    }

    protected function raise_unexpected_expression_error($expression)
    {
        $expression_name = get_class( $expression );

        throw Create::an( Unexpected_Expression_Error::class )->with(
            "Unexpected {$expression_name}",
            $expression
        );
    }
}
