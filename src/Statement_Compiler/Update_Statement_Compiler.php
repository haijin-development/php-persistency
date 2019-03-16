<?php

namespace Haijin\Persistency\Statement_Compiler;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements\Update_Statement;

/**
 * Object to build an Update_Statement from an update statement definition callable.
 */
class Update_Statement_Compiler extends Statement_Compiler
{
    /**
     * Returns the concrete statement instance.
     */
    protected function new_statement_expression()
    {
        return Create::object( Update_Statement::class, 
            $this->context
        );
    }

    /// Accessing

    public function get_update_expression()
    {
        return $this->statement_expression;
    }

    /// DSL

    public function record(...$attribute_values)
    {
        $records_values_expression  = $this->new_record_values_expression( $attribute_values );

        $this->statement_expression->set_records_values_expression( $records_values_expression );

        return $this;
    }

    public function filter($filter_expression)
    {
        $this->statement_expression->set_filter_expression(
            $this->new_filter_expression( $filter_expression )
        );

        return $this;
    }

    public function set($field_name, $value_or_expression)
    {
        return $this->new_field_value_expression(
            $field_name,
            $this->_value_to_expression( $value_or_expression )
        );
    }
}