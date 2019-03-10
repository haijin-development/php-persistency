<?php

namespace Haijin\Persistency\Statement_Compiler;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements\Create_Statement;

/**
 * Object to build a Create_Statement from a create statement definition callable.
 */
class Create_Statement_Compiler extends Statement_Compiler
{
    /**
     * Returns the concrete statement instance.
     */
    protected function new_statement_expression()
    {
        return Create::object( Create_Statement::class, 
            $this->context
        );
    }

    /// Accessing

    public function get_create_expression()
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

    public function set($field_name, $value_expression)
    {
        return $this->new_field_value_expression( $field_name, $value_expression );
    }
}