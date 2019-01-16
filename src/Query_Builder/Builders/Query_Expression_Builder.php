<?php

namespace Haijin\Persistency\Query_Builder\Builders;

use Haijin\Persistency\Errors\QueryExpressions\Macro_Expression_Evaluated_To_Null_Error;
use Haijin\Persistency\Query_Builder\Expressions_Factory_Trait;
use Haijin\Persistency\Query_Builder\Expressions_DSL_Trait;

/**
 * Object to build a Query_Expression from a query definition closure.
 */
class Query_Expression_Builder
{
    use Expressions_Factory_Trait;
    use Expressions_DSL_Trait;

    /**
     * The Query_Expression being built.
     */
    protected $query_expression;

    /**
     * A Dictionary with the macro expressions defined in the scope of $this->$query_expression.
     */
    protected $expression_context;

    /// Initializing

    /**
     * Initializes $this instance.
     *
     * @param Expression_Context $expression_context Optional - The Expression_Context of the
     *      Query_Expression being built. If none is given a new Expression_Context is created.
     */
    public function __construct($expression_context = null)
    {
        if( $expression_context === null ) {
            $expression_context = $this->new_expression_context();
        }
        $this->context = $expression_context;

        $this->query_expression = $this->new_query_expression();
    }

    /// Accessing

    /**
     * Returns the Expression_Context of the Query_Expression.
     *
     * @return Expression_Context The Expression_Context of the Query_Expression.
     */
    public function get_context()
    {
        return $this->context;
    }

    /**
     * Returns the Dictionary with the macro definitions of the Query_Expression.
     *
     * @return Dictionary The Dictionary with the macro definitions of the Query_Expression.
     */
    public function get_macros_dictionary()
    {
        return $this->get_context()->get_macros_dictionary();
    }

    public function get_context_collection()
    {
        return $this->get_context()->get_current_collection();
    }

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

    /// Building expression

    /**
     * Builds and returns a new Query_Expression.
     *
     * @param closure $expression_closure The closure to build the Query_Expression
     *      using a DSL.
     * @param object $binding Optional - An optional object to bind the evaluation of the
     *      $expression_closure.
     *
     * @return Query_Expression The built Query_Expression.
     */
    public function build( $expression_closure, $binding = null )
    {
        $this->query_expression = $this->new_query_expression();

        $this->eval( $expression_closure, $binding );

        return $this->query_expression;
    }

    /**
     * Evaluates the given $expression_closure with the current $this->query_expression.
     * This method allows to build the Query_Expression in different times instead of all
     * at once.
     *
     * @param closure $expression_closure The closure to build the Query_Expression
     *      using a DSL.
     * @param object $binding Optional - An optional object to bind the evaluation of the
     *      $expression_closure.
     *
     * @return Query_Expression The current $this->query_expression.
     */
    public function eval( $expression_closure, $binding = null )
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $expression_closure->call( $binding, $this );

        return $this->query_expression;
    }

    /**
     * Defines the collection name of $this Query_Expression.
     * Returns a CollectionExpressionBuilder to allow further configuration of the
     * Collection_Expression.
     *
     * @param string $collection_name The name of the root collection to query for.
     *
     * @return CollectionExpressionBuilder Returns a CollectionExpressionBuilder to allow
     *      further configuration of the Collection_Expression.
     */
    public function collection($collection_name)
    {
        $collection  = $this->new_collection_expression( $collection_name );

        $this->context->set_current_collection( $collection );

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
        $alias_expression = $this->new_alias_expression(
            $alias,
            $this->query_expression->get_collection()
        );

        $this->query_expression->set_collection( $alias_expression );

        return $alias_expression;
    }

    /**
     * Creates a Proyection_Expression with the proyected expressions of the query.
     *
     * @param array $proyected_expressions Each parameter is an expression defining
     *      a query proyection.
     */
    public function proyect(...$proyected_expressions)
    {
        $this->query_expression->set_proyection(
            $this->new_proyection_expression_with_all( $proyected_expressions )
        );
    }

    public function join($joined_collection_name)
    {
        $expression_context = $this->new_expression_context(
            $this->get_macros_dictionary()
        );

        $from_collection = $this->get_context_collection();

        return $this->_with_expression_context_do(
            $expression_context,
            function() use($from_collection, $joined_collection_name) {

                $join_expression = $this->_create_join(
                    $from_collection,
                    $joined_collection_name
                );

            $this->query_expression->add_join( $join_expression );

            return $join_expression;
        });
    }

    protected function _create_join($from_collection, $joined_collection_name)
    {
        $joined_collection =
            $this->new_collection_expression( $joined_collection_name );

        $this->context->set_current_collection( $joined_collection );

        return $this->new_join_expression(
            $from_collection,
            $joined_collection
        );
    }

    /**
     * Creates a Filter_Expression with the filter expression of the query.
     *
     * @param Filter_Expression $filter_expression The filter expression of the query.
     */
    public function filter($filter_expression)
    {
        $this->query_expression->set_filter(
            $this->new_filter_expression( $filter_expression )
        );
    }

    /**
     * Creates an Order_By_Expression with the proyected expressions of the query.
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

    public function limit($limit)
    {
        $this->_pagination_expression()->set_limit( $limit );

        return $this;
    }

    public function page($page_number)
    {
        $this->_pagination_expression()->set_page_number( $page_number );

        return $this;
    }

    public function page_size($page_size)
    {
        $this->_pagination_expression()->set_page_size( $page_size );

        return $this;
    }

    /// Macro expressions

    public function let($macro_name, $definition_closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $macro_expression = $definition_closure->call( $binding, $this );

        if( $macro_expression === null ) {
            $this->_raise_macro_expression_evaluated_to_null_error( $macro_name );
        }

        $this->get_macros_dictionary()[ $macro_name ] = $macro_expression;
    }

    /**
     * Assumes that the attribute is a macro expressions. Searches for a defined macro
     * expression with that name and returns its evaluation. If none is found raises
     * an error.
     */
    public function __get($macro_name)
    {
        return $this->get_macros_dictionary()->at( $macro_name, $this );
    }

    /// Helper methods

    protected function _with_expression_context_do($expression_context, $closure)
    {
        $this->previous_expression_context = $this->context;

        $this->context = $expression_context;

        try {
            return $closure->call( $this );
        } finally {
            $this->context = $this->previous_expression_context;
        }
    }

    /// Raising errors

    protected function _raise_macro_expression_evaluated_to_null_error($macro_name)
    {
        throw new Macro_Expression_Evaluated_To_Null_Error(
            "The macro expression '{$macro_name}' evaluated to null. Probably it is missing the return statement.",
            $macro_name
        );
    }
}