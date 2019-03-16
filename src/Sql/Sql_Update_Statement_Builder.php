<?php

namespace Haijin\Persistency\Sql;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Filter_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Filter_Builder;

class Sql_Update_Statement_Builder extends Sql_Expression_Builder
{
    public function __construct()
    {
        parent::__construct( new Ordered_Collection() );
    }

    /// Visiting

    /**
     * Accepts a Query_Statement.
     */
    public function accept_update_statement($update_statement)
    {
        $this->validate_statement( $update_statement );

        $sql = "update ";

        $sql .= $this->visit( $update_statement->get_collection_expression() );

        $sql .= " set ";

        $sql .= $this->visit( $update_statement->get_records_values_expression() );

        if( $update_statement->has_filter_expression() ) {

            $sql .= " where ";

            $sql .= $this->visit( $update_statement->get_filter_expression() );

        }

        $sql .= ";";

        return $sql;
    }

    /// Validating

    protected function validate_statement($update_statement)
    {
        if( $update_statement->get_collection_expression() === null ) {
            $this->raise_invalid_expression(
                "The update statement is missing the \$query->collection(...) expression.",
                $update_statement
            );
        }

        if( $update_statement->get_records_values_expression() === null ) {
            $this->raise_invalid_expression(
                "The update statement is missing the \$query->record(...) expression.",
                $update_statement
            );
        }
    }

    /**
     * Accepts a Collection_Expression.
     */
    public function accept_collection_expression($collection_expression)
    {
        return $collection_expression->get_collection_name();
    }

    /**
     * Accepts a Records_Values_Expression.
     */
    public function accept_record_values_expression($record_values_expression)
    {
        $attribute_updates = [];

        foreach( $record_values_expression->get_field_values() as $field_value ) {
            $attribute_updates[] =
                $field_value->get_field_name() .
                " = " .
                $this->new_sql_expression_builder()->build_sql_from(
                    $field_value->get_value_expression()
                );
        }

        return join( ", ", $attribute_updates );
    }

    /**
     * Accepts a Filter_Expression.
     */
    public function accept_filter_expression($filter_expression)
    {
        return $this->new_sql_filter_builder()
            ->build_sql_from( $filter_expression );
    }

    //// Creating instances

    protected function new_sql_expression_builder()
    {
        return Create::object(
            Sql_Expression_In_Filter_Builder::class,
            $this->collected_parameters
        );
    }

    protected function new_sql_filter_builder()
    {
        return Create::object(
            Sql_Filter_Builder::class,
            $this->collected_parameters
        );
    }   
}