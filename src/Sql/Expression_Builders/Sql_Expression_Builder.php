<?php

namespace Haijin\Persistency\Sql\Expression_Builders;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements_Visitors\Expression_Visitor;
use Haijin\Persistency\Errors\Query_Expressions\Invalid_Expression_Error;


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

        $collected_expressions = [];

        foreach( $expressions as $expression ) {

            if( ! $expression->is_ignore_expression() ) {

                $collected_expressions[] =
                    $this->expression_sql_from( $expression, $expression_compiler );

            }

        }

        return $collected_expressions;
    }

    protected function expressions_list($expressions)
    {
        return join( ', ', $this->collect_expressions_sql( $expressions ) );        
    }

    /// Visiting

    public function accept_raw_expression($raw_expression)
    {
        return $raw_expression->get_value();
    }

    /// Raising errors

    protected function raise_invalid_expression_error($message)
    {
        throw new Invalid_Expression_Error( $message, $this );
    }
}
