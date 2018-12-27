<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Builders\QueryExpressionBuilder;
use Haijin\Persistency\QueryBuilder\Visitors\AbstractQueryExpressionVisitor;
use Haijin\Persistency\QueryBuilder\Visitors\QueryVisitorTrait;
use Haijin\Persistency\Factory\Factory;
use Haijin\Tools\OrderedCollection;

class SqlBuilder extends AbstractQueryExpressionVisitor
{
    use QueryVisitorTrait;
    use SqlBuilderTrait;

    /// Building

    /**
     * Builds and returns a new SQL string.
     *
     * @param closure $expression_closure The closure to build the QueryExpression
     *      using a DSL.
     * @param object $binding Optional - An optional object to bind the evaluation of the
     *      $expression_closure.
     *
     * @return QueryExpression The built QueryExpression.
     */
    public function build( $expression_closure, $binding = null )
    {
        $query_expression = $this->new_query_expression_builder()
            ->build( $expression_closure, $binding );

        return $this->build_sql_from( $query_expression );
    }

    /// Visiting

    /**
     * Accepts a QueryExpression.
     */
    public function accept_query_expression($query_expression)
    {
        $sql = "";

        $sql .= $this->nested_proyections_sql_from( $query_expression );

        $sql .= " ";

        $sql .= $this->visit( $query_expression->get_collection() );

        $sql .= $this->join_expressions_sql_from( $query_expression );

        if( $query_expression->has_filter() ) {
            $sql .= " ";
            $sql .= $this->visit( $query_expression->get_filter() );
        }

        if( $query_expression->has_order_by() ) {
            $sql .= " ";
            $sql .= $this->visit( $query_expression->get_order_by() );
        }

        if( $query_expression->has_pagination() ) {
            $sql .= " ";
            $sql .= $this->visit( $query_expression->get_pagination() );
        }

        $sql .= ";";

        return $sql;
    }

    public function nested_proyections_sql_from($expression)
    {
        return "select " . $this->get_nested_proyections_sql_from( $expression );
    }

    public function get_nested_proyections_sql_from($expression)
    {
        $proyected_fields = new OrderedCollection();

        $proyected_fields[] = $this->proyected_fields_from( $expression );

        $expression->joins_do( function($join_expression) use($proyected_fields) {
            $sql_builder = $this->new_sql_builder( $join_expression );

            $proyected_fields[] =
                $sql_builder->get_nested_proyections_sql_from( $join_expression );

        }, $this);

        return $proyected_fields->join_with( ", " );
    }

    public function proyected_fields_from($expression)
    {
        $proyection_builder = $this->new_sql_proyection_builder();

        return $proyection_builder->proyections_from(
            $expression->get_proyection()
        );
    }

    /**
     * Accepts a CollectionExpression.
     */
    public function accept_collection_expression($collection_expression)
    {
        return $this->new_sql_collection_builder()
            ->build_sql_from( $collection_expression );
    }

    /**
     * Accepts a JoinExpression.
     */
    public function accept_join_expression($join_expression)
    {
        return $this->new_sql_join_builder()
            ->build_sql_from( $join_expression );
    }

    protected function join_expressions_sql_from($query_expression)
    {
        if( ! $query_expression->has_joins() ) {
            return "";
        }

        $joins = new OrderedCollection();

        $query_expression->joins_do( function($join_expression) use($joins) {

            $join_expression->get_nested_joins()->each_do( function($join_expression) use($joins) {

                $joins[] = $this->visit( $join_expression );

            }, $this );

        }, $this );

        return " " . $joins->join_with( " " );
    }

    /**
     * Accepts a FilterExpression.
     */
    public function accept_filter_expression($filter_expression)
    {
        return $this->new_sql_filter_builder()
            ->build_sql_from( $filter_expression );
    }

    /**
     * Accepts a OrderByExpression.
     */
    public function accept_order_by_expression($order_by_expression)
    {
        return $this->new_sql_order_by_builder()
            ->build_sql_from( $order_by_expression );
    }

    /**
     * Accepts a PaginationExpression.
     */
    public function accept_pagination_expression($pagination_expression)
    {
        return $this->new_sql_pagination_builder()
            ->build_sql_from( $pagination_expression );
    }

    /**
     * Accepts a AliasExpression. The alias at this DSL level is for the CollectionExpression.
     */
    public function accept_alias_expression($alias_expression)
    {
        $sql = $this->visit( $alias_expression->get_aliased_expression() );
        $sql .= " as ";
        $sql .= $this->escape_sql( $alias_expression->get_alias() );

        return $sql;
    }

    /// Creating instances

    //// Query expression

    protected function new_query_expression_builder()
    {
        return Factory::new( QueryExpressionBuilder::class );
    }

    //// Sql builders

    protected function new_sql_builder()
    {
        return Factory::new( self::class );
    }

    protected function new_sql_proyection_builder()
    {
        return Factory::new( SqlProyectionBuilder::class );
    }

    protected function new_sql_collection_builder()
    {
        return Factory::new( SqlCollectionBuilder::class );
    }

    protected function new_sql_join_builder()
    {
        return Factory::new( SqlJoinBuilder::class );
    }

    protected function new_sql_order_by_builder()
    {
        return Factory::new( SqlOrderByBuilder::class );
    }

    protected function new_sql_pagination_builder()
    {
        return Factory::new( SqlPaginationBuilder::class );
    }

    protected function new_sql_filter_builder()
    {
        return Factory::new( SqlFilterBuilder::class );
    }   
}