<?php

namespace Haijin\Persistency\Sql;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Filter_Builder;

class Sql_Delete_Statement_Builder extends Sql_Expression_Builder
{
    public function __construct()
    {
        parent::__construct( new Ordered_Collection() );
    }

    /// Visiting

    /**
     * Accepts a Query_Statement.
     */
    public function accept_delete_statement($delete_statement)
    {
        $this->validate_statement( $delete_statement );

        $sql = "delete from ";

        $sql .= $this->visit( $delete_statement->get_collection_expression() );

        if( $delete_statement->has_filter_expression() ) {

            $sql .= " where ";

            $sql .= $this->visit( $delete_statement->get_filter_expression() );

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

    /// Validating

    protected function validate_statement($delete_statement)
    {
        if( $delete_statement->get_collection_expression() === null ) {
            $this->raise_invalid_expression(
                "The delete statement is missing the \$query->collection(...) expression.",
                $delete_statement
            );
        }
    }

    //// Creating instances

    protected function new_sql_filter_builder()
    {
        return Create::object(
            Sql_Filter_Builder::class,
            $this->collected_parameters
        );
    }   
}