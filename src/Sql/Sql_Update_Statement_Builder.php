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

    /// Building

    /**
     * Builds and returns a new SQL string.
     *
     * @param closure $expression_closure The closure to build the Query_Statement
     *      using a DSL.
     * @param object $binding Optional - An optional object to bind the evaluation of the
     *      $expression_closure.
     *
     * @return Query_Statement The built Query_Statement.
     */
    public function build( $expression_closure, $binding = null )
    {
        $create_statement = $this->new_create_statement_compiler()
            ->compile( $expression_closure, $binding );

        return $this->build_sql_from( $create_statement );
    }

    /// Visiting

    /**
     * Accepts a Query_Statement.
     */
    public function accept_update_statement($create_statement)
    {
        $sql = "update ";

        $sql .= $this->visit( $create_statement->get_collection_expression() );

        $sql .= " set ";

        $sql .= $this->visit( $create_statement->get_records_values_expression() );

        if( $create_statement->has_filter_expression() ) {

            $sql .= " where ";

            $sql .= $this->visit( $create_statement->get_filter_expression() );

        }

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