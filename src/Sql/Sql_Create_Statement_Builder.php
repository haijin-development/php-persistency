<?php

namespace Haijin\Persistency\Sql;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Filter_Builder;

class Sql_Create_Statement_Builder extends Sql_Expression_Builder
{
    public function __construct()
    {
        parent::__construct( new Ordered_Collection() );
    }

    /// Building

    /**
     * Builds and returns a new SQL string.
     *
     * @param callable $expression_callable The callable to build the Query_Statement
     *      using a DSL.
     *
     * @return Query_Statement The built Query_Statement.
     */
    public function build($expression_callable)
    {
        $create_statement = $this->new_create_statement_compiler()
            ->compile( $expression_callable );

        return $this->build_sql_from( $create_statement );
    }

    /// Visiting

    /**
     * Accepts a Query_Statement.
     */
    public function accept_create_statement($create_statement)
    {
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

    //// Query expression

    protected function new_sql_expression_builder()
    {
        return Create::object(
            Sql_Expression_In_Filter_Builder::class,
            $this->collected_parameters
        );
    }
}