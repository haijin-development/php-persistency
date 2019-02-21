<?php

namespace Haijin\Persistency\Statement_Compiler;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements\Update_Statement;

/**
 * Object to build an Update_Statement from an update statement definition closure.
 */
class Update_Statement_Compiler extends Statement_Compiler
{
    /**
     * Returns the concrete statement instance.
     */
    protected function new_statement_expression()
    {
        return Create::a( Update_Statement::class )->with(
            $this->context
        );
    }

    /// Accessing

    public function get_update_expression()
    {
        return $this->statement_expression;
    }

    /// DSL

    /**
     * Defines the collection name of $this Create_Statement.
     * Returns a CollectionExpressionBuilder to allow further configuration of the
     * Collection_Expression.
     *
     * @param string $collection_name The name of the root collection to query for.
     *
     * @return CollectionExpressionBuilder Returns a CollectionExpressionBuilder to allow
     *      further configuration of the Collection_Expression.
     */
    public function collection($collection_name)
    {
        $collection  = $this->new_collection_expression( $collection_name );

        $this->context->set_current_collection( $collection );

        $this->statement_expression->set_collection_expression( $collection );

        return $this;
    }

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

    public function set($field_name, $value_expression)
    {
        return $this->new_field_value_expression( $field_name, $value_expression );
    }
}