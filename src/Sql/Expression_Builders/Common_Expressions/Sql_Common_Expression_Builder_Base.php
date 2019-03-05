<?php

namespace Haijin\Persistency\Sql\Expression_Builders\Common_Expressions;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_Builder;

/**
 * A builder of general expressions used in different query parts.
 */
class Sql_Common_Expression_Builder_Base extends Sql_Expression_Builder
{
    protected $function_handlers;

    /// Initializing

    public function __construct($collected_parameters)
    {
        parent::__construct( $collected_parameters );

        $this->function_handlers = [];

        $this->definition();
    }

    /// Defining

    public function definition()
    {
        $this->function_handlers[ 'is_null' ] = function($function_call_expression) {

            $receiver = $function_call_expression->get_parameters()[0];

            return $this->expression_sql_from( $receiver ) . " is null";

        };

        $this->function_handlers[ 'is_not_null' ] = function($function_call_expression) {

            $receiver = $function_call_expression->get_parameters()[0];

            return $this->expression_sql_from( $receiver ) . " is not null";

        };

        $this->function_handlers[ 'desc' ] = function($function_call_expression) {

            $receiver = $function_call_expression->get_parameters()[0];

            return $this->expression_sql_from( $receiver ) . " desc";

        };

        $this->function_handlers[ 'asc' ] = function($function_call_expression) {

            $receiver = $function_call_expression->get_parameters()[0];

            return $this->expression_sql_from( $receiver ) . " asc";

        };

        $this->function_handlers[ 'in' ] = function($function_call_expression) {

            $sql = $this->visit( $function_call_expression->get_parameters()[0] );

            $sql .= " in (";

            $ids = [];
            foreach( $function_call_expression->get_parameters()[ 1 ]->get_value() as $id ) {
                $ids[] = $this->value_to_sql( $id );
            }

            $sql .= join( ", ", $ids );
            $sql .= ")";

            return $sql;

        };

        $this->function_handlers[ '_default' ] = function($function_call_expression) {

            $sql = $this->escape_sql( $function_call_expression->get_function_name() );
            $sql .= "(";

            $sql .= $this->expressions_list(
                $function_call_expression->get_parameters()
            );

            $sql .= ")";

            return $sql;

        };
    }

    /// Visiting

    /**
     * Accepts a Count_Expression.
     */
    public function accept_count_expression($count_expression)
    {
        return "count(*)";
    }

    /**
     * Accepts an All_Fields_Expression.
     */
    public function accept_all_fields_expression($all_fields_expression)
    {
        return $all_fields_expression->get_context_collection()
                    ->get_referenced_name() . ".*";
    }

    /**
     * Accepts a Field_Expression.
     */
    public function accept_field_expression($field_expression)
    {
        $field = '';

        if( $field_expression->is_relative() ) {
            $field .= $field_expression->get_context_collection()
                        ->get_referenced_name() . ".";
        }

        $field .= $this->escape_sql( $field_expression->get_field_name() );

        return $field;
    }

    /**
     * Accepts a Value_Expression.
     */
    public function accept_value_expression($value_expression)
    {
        return $this->value_to_sql( $value_expression->get_value() );
    }

    /**
     * Accepts a Alias_Expression.
     */
    public function accept_alias_expression($alias_expression)
    {
        return $alias_expression->get_alias();
    }

    /**
     * Accepts an Ignore_Expression.
     */
    public function accept_ignore_expression($ignore_expression)
    {
        return '';
    }

    /**
     * Accepts a Function_Call_Expression.
     */
    public function accept_function_call_expression($function_call_expression)
    {
        $function_name = $function_call_expression->get_function_name();

        if( isset( $this->function_handlers[ $function_name ] ) ) {

            $handler = $this->function_handlers[ $function_name ];

            return $handler->call( $this, $function_call_expression );
        }

        $handler = $this->function_handlers[ '_default' ];

        return $handler->call( $this, $function_call_expression );
    }

    /**
     * Accepts a Binary_Operator_Expression.
     */
    public function accept_binary_operator_expression($binary_operator_expression)
    {
        $parameter_1 = $binary_operator_expression->get_parameter_1();
        $parameter_2 = $binary_operator_expression->get_parameter_2();

        if( $parameter_1->is_ignore_expression() ) {
            return $this->visit( $parameter_2 );
        }
        if( $parameter_2->is_ignore_expression() ) {
            return $this->visit( $parameter_1 );
        }

        $sql = $this->visit( $parameter_1 );

        $sql .= " ";

        $sql .= $this->escape_sql( $binary_operator_expression->get_operator_symbol() );

        $sql .= " ";

        $sql .= $this->visit( $parameter_2 );

        return $sql;
    }

    /**
     * Accepts a Brackets_Expression.
     */
    public function accept_brackets_expression($brackets_expression)
    {
        return "(" . $this->visit( $brackets_expression->get_expression() ) . ")";
    }

    protected function new_sql_expression_builder()
    {
        return Create::object(
            get_class( $this ),
            $this->collected_parameters
        );
    }
}