<?php

namespace Haijin\Persistency\Sql;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements_Visitors\Abstract_Query_Expression_Visitor;
use Haijin\Persistency\Statements_Visitors\Query_Visitor_Trait;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_In_Filter_Builder;
use Haijin\Ordered_Collection;

class Sql_Delete_Statement_Builder extends Abstract_Query_Expression_Visitor
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
    public function accept_delete_statement($create_statement)
    {
        $sql = "delete from ";

        $sql .= $this->visit( $create_statement->get_collection_expression() );

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
     * Accepts a Filter_Expression.
     */
    public function accept_filter_expression($filter_expression)
    {
        return $this->new_sql_filter_builder()
            ->build_sql_from( $filter_expression );
    }

    //// Creating instances

    protected function new_sql_filter_builder()
    {
        return Create::object( Sql_Filter_Builder::class );
    }   
}