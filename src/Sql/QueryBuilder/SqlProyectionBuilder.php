<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Visitors\QueryExpressionVisitor;
use Haijin\Tools\OrderedCollection;

class SqlProyectionBuilder extends QueryExpressionVisitor
{
    use SqlBuilderTrait;

    protected $collection;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    /// Visiting

    /**
     * Accepts a ProyectionExpression.
     */
    public function accept_proyection_expression($proyection_expression)
    {
        if( $proyection_expression->is_empty() ) {
            return "select " . $this->collection->get_referenced_name() . ".*";
        }

        return "select " . $this->expressions_list(
                $proyection_expression->get_proyected_expressions()
            );
    }

    public function proyections_from($proyection_expression)
    {
        if( $proyection_expression->is_empty() ) {
            return $this->collection->get_referenced_name() . ".*";
        }

        return $this->expressions_list(
            $proyection_expression->get_proyected_expressions()
        );
    }

    /// Creating instances

    protected function new_sql_expression_builder($collection_expression)
    {
        return new SqlExpressionBuilder( $collection_expression, false );
    }
}