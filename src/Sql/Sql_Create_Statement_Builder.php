<?php

namespace Haijin\Persistency\Sql;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statement_Compiler\Create_Statement_Compiler;
use Haijin\Persistency\Statements_Visitors\Abstract_Query_Expression_Visitor;
use Haijin\Persistency\Statements_Visitors\Query_Visitor_Trait;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_In_Filter_Builder;
use Haijin\Ordered_Collection;

class Sql_Create_Statement_Builder extends Abstract_Query_Expression_Visitor
{
    use Query_Visitor_Trait;
    use Sql_Builder_Trait;

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
            ->build( $expression_closure, $binding );

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

    protected function new_create_statement_compiler()
    {
        return Create::object( Create_Statement_Compiler::class );
    }

    protected function new_sql_expression_builder()
    {
        return Create::object( Sql_Expression_In_Filter_Builder::class );
    }
}