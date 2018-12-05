<?php

namespace Haijin\Persistency\QueryBuilder\Builders;

use Haijin\Persistency\QueryBuilder\ExpressionsFactoryTrait;
use Haijin\Persistency\QueryBuilder\ExpressionsDSLTrait;

class QueryExpressionBuilder
{
    use ExpressionsFactoryTrait;
    use ExpressionsDSLTrait;

    /**
     * The QueryExpression being built.
     */
    protected $query_expression;

    /**
     * A dictionary with the macro expressions defined in the query.
     */
    protected $macro_expressions;

    /// Initializing

    public function __construct()
    {
        $this->query_expression = $this->new_query_expression();
        $this->macro_expressions = new MacroExpressionsDictionary();
    }

    /// Accessing

    public function get_query_expression()
    {
        return $this->query_expression;
    }

    public function get_collection()
    {
        return $this->query_expression->get_collection();
    }

    public function get_proyection()
    {
        return $this->query_expression->get_proyection();
    }

    public function get_filter()
    {
        return $this->query_expression->get_filter();
    }

    public function get_joins()
    {
        return $this->query_expression->get_joins();
    }

    public function get_order_by()
    {
        return $this->query_expression->get_order_by();
    }

    public function get_pagination()
    {
        return $this->query_expression->get_pagination();
    }

    public function get_macro_expressions()
    {
        return $this->macro_expressions;
    }

    /// Building expression

    /**
     * Builds and returns a new QueryExpression.
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
        if( $binding === null ) {
            $binding = $this;
        }

        $this->query_expression = $this->new_query_expression();

        $expression_closure->call( $binding, $this );

        return $this->query_expression;
    }

    /**
     * Defines the collection name of $this QueryExpression.
     * Returns a CollectionExpressionBuilder to allow further configuration of the
     * CollectionExpression.
     *
     * @param string $collection_name The name of the root collection to query for.
     *
     * @return CollectionExpressionBuilder Returns a CollectionExpressionBuilder to allow
     *      further configuration of the CollectionExpression.
     */
    public function collection($collection_name)
    {
        $collection  = $this->new_collection_expression( $collection_name );

        $this->query_expression->set_collection( $collection );

        return $this;
    }

    /**
     * Defines the an alias for the collection.
     *
     * @param string $alias The alias for the collection.
     */
    public function as($alias)
    {
        $alias_expression  = $this->new_alias_expression(
            $alias,
            $this->query_expression->get_collection()
        );

        $this->query_expression->set_collection( $alias_expression );

        return $alias_expression;
    }

    /**
     * Creates a ProyectionExpression with the proyected expressions of the query.
     *
     * @param array $proyected_expressions Each parameter is an expression defining
     *      a query proyection.
     */
    public function proyect(...$proyected_expressions)
    {
        $proyection = $this->new_proyection_expression();
        $proyection->add_all( $proyected_expressions );

        $this->query_expression->set_proyection( $proyection );
    }

    public function join($joined_collection_name)
    {
        $joined_collection = $this->new_collection_expression( $joined_collection_name );

        $join_expression = $this->new_join_expression( $joined_collection );

        $this->query_expression->add_join( $join_expression );

        return $join_expression;
    }

    /**
     * Creates a FilterExpression with the filter expression of the query.
     *
     * @param FilterExpression $filter_expression The filter expression of the query.
     */
    public function filter($filter_expression)
    {
        $this->query_expression->set_filter(
            $this->new_filter_expression( $filter_expression )
        );
    }

    /**
     * Creates an OrderByExpression with the proyected expressions of the query.
     *
     * @param array $proyected_expressions Each parameter is an expression defining
     *      an ordering expression.
     */
    public function order_by(...$order_by_expressions)
    {
        $order_by = $this->new_order_by_expression();
        $order_by->add_all( $order_by_expressions );

        $this->query_expression->set_order_by( $order_by );
    }

    /**
     * This is just a placerholder to improve DSL expressiveness, but the
     * $pagination_expression is already set to $this->query_expression.
     */
    public function pagination($pagination_expression)
    {
        return;
    }

    /// Pagination DSL

    public function _pagination_expression()
    {
        if( ! $this->query_expression->has_pagination() ) {
            $this->query_expression->set_pagination(
                $this->new_pagination_expression()
            );
        }

        return $this->query_expression->get_pagination();
    }

    public function offset($offset)
    {
        $this->_pagination_expression()->set_offset( $offset );

        return $this;
    }

    public function limit($length)
    {
        $this->_pagination_expression()->set_length( $length );

        return $this;
    }

    public function page($page)
    {
        $this->_pagination_expression()->set_page( $page );

        return $this;
    }

    public function page_size($length)
    {
        $this->_pagination_expression()->set_length( $length );

        return $this;
    }

    /// Macro expressions

    public function let($macro_expression_name, $definition_closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $this->macro_expressions->define(
            $macro_expression_name,
            $definition_closure->bindTo( $binding )
        );
    }

    /**
     * Assumes that the attribute is a macro expressions. Searches for a defined macro
     * expression with that name and returns its evaluation. If none is found raises
     * an error.
     */
    public function __get($attribute_name)
    {
        return $this->macro_expressions->at( $attribute_name, $this );
    }
}