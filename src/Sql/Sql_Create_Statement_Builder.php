<?php

namespace Haijin\Persistency\Sql;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;
use Haijin\Persistency\Statement_Compiler\Create_Statement_Compiler;
use Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Filter_Builder;

class Sql_Create_Statement_Builder extends Sql_Statement_Builder
{
    /// Initializing

    public function __construct()
    {
        parent::__construct( new Ordered_Collection() );
    }

    /// Visiting

    /**
     * Accepts a Query_Statement.
     */
    public function accept_create_statement($create_statement)
    {
        $this->validate_statement( $create_statement );

        $sql = "insert into ";

        $sql .= $this->visit( $create_statement->get_collection_expression() );

        $sql .= " ";

        $sql .= $this->visit( $create_statement->get_records_values_expression() );

        $sql .= ";";

        return $sql;
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
        $attribute_names = [];
        $attribute_values = [];

        foreach( $record_values_expression->get_field_values() as $field_value ) {
            $attribute_names[] = $field_value->get_field_name();

            $attribute_values[] = $this->new_sql_expression_builder()->build_sql_from(
                    $field_value->get_value_expression()
                );
        }

        return  "(" . 
                join( ", ", $attribute_names ) .
                ") values (" . 
                join( ", ", $attribute_values ) .
                ")";
    }

    /// Validating

    protected function validate_statement($create_statement)
    {
        if( ! $create_statement->has_collection_expression() ) {
            $this->raise_invalid_expression_error(
                "The create statement is missing the \$query->collection(...) expression.",
                $create_statement
            );
        }

        if( $create_statement->get_records_values_expression() === null ) {
            $this->raise_invalid_expression_error(
                "The create statement is missing the \$query->record(...) expression.",
                $create_statement
            );
        }
    }

    /// Creating instances

    protected function new_statement_compiler()
    {
        return Create::object( Create_Statement_Compiler::class );
    }

    protected function new_sql_expression_builder()
    {
        return Create::object(
            Sql_Expression_In_Filter_Builder::class,
            $this->collected_parameters
        );
    }
}