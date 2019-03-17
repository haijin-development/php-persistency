<?php

namespace Haijin\Persistency\Statement_Compiler;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements\Query_Statement;
use Haijin\Persistency\Errors\Query_Expressions\Macro_Expression_Evaluated_To_Null_Error;

/**
 * Object to build a Query_Statement from a query definition callable.
 */
class Query_Statement_Compiler extends Statement_Compiler
{
    /// Accessing

    /**
     * Returns the concrete statement instance.
     */
    protected function new_statement_expression()
    {
        return Create::object( Query_Statement::class,  $this->context );
    }

    public function get_query_statement()
    {
        return $this->statement_expression;
    }

    /// DSL

    /**
     * Defines the an alias for the collection.
     *
     * @param string $alias The alias for the collection.
     */
    public function as($alias)
    {
        $alias_expression = $this->new_alias_expression(
            $alias,
            $this->statement_expression->get_collection_expression()
        );

        $this->statement_expression->set_collection_expression( $alias_expression );

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
        $this->statement_expression->set_proyection_expression(
            $this->new_proyection_expression_with_all( $proyected_expressions )
        );
    }

    /**
     * Creates a Group_By_Expression with the grouping fields expressions of the query.
     *
     * @param array $fields_expressions Each parameter is a Field_Expression defining
     *      a query group by.
     */
    public function group_by(...$fields_expressions)
    {
        $this->statement_expression->set_group_by_expression(
            $this->new_group_by_expression_with_all( $fields_expressions )
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

                $join_expression = $this->_create_inner_join(
                    $from_collection,
                    $joined_collection_name
                );

            $this->statement_expression->add_join_expression( $join_expression );

            return $join_expression;
        });
    }

    public function inner_join($joined_collection_name)
    {
        return $this->join( $joined_collection_name );
    }

    public function left_join($joined_collection_name)
    {
        $expression_context = $this->new_expression_context(
            $this->get_macros_dictionary()
        );

        $from_collection = $this->get_context_collection();

        return $this->_with_expression_context_do(
            $expression_context,
            function() use($from_collection, $joined_collection_name) {

                $join_expression = $this->_create_left_outer_join(
                    $from_collection,
                    $joined_collection_name
                );

            $this->statement_expression->add_join_expression( $join_expression );

            return $join_expression;
        });
    }

    public function left_outer_join($joined_collection_name)
    {
        return $this->left_join( $joined_collection_name );
    }

    public function right_join($joined_collection_name)
    {
        $expression_context = $this->new_expression_context(
            $this->get_macros_dictionary()
        );

        $from_collection = $this->get_context_collection();

        return $this->_with_expression_context_do(
            $expression_context,
            function() use($from_collection, $joined_collection_name) {

                $join_expression = $this->_create_right_outer_join(
                    $from_collection,
                    $joined_collection_name
                );

            $this->statement_expression->add_join_expression( $join_expression );

            return $join_expression;
        });
    }

    public function right_outer_join($joined_collection_name)
    {
        return $this->right_join( $joined_collection_name );
    }

    public function full_join($joined_collection_name)
    {
        $expression_context = $this->new_expression_context(
            $this->get_macros_dictionary()
        );

        $from_collection = $this->get_context_collection();

        return $this->_with_expression_context_do(
            $expression_context,
            function() use($from_collection, $joined_collection_name) {

                $join_expression = $this->_create_full_outer_join(
                    $from_collection,
                    $joined_collection_name
                );

            $this->statement_expression->add_join_expression( $join_expression );

            return $join_expression;
        });
    }

    public function full_outer_join($joined_collection_name)
    {
        return $this->full_join( $joined_collection_name );
    }

    protected function _create_inner_join($from_collection, $joined_collection_name)
    {
        $joined_collection =
            $this->new_collection_expression( $joined_collection_name );

        $this->context->set_current_collection( $joined_collection );

        return $this->new_inner_join_expression(
            $from_collection,
            $joined_collection
        );
    }

    protected function _create_left_outer_join($from_collection, $joined_collection_name)
    {
        $joined_collection =
            $this->new_collection_expression( $joined_collection_name );

        $this->context->set_current_collection( $joined_collection );

        return $this->new_left_outer_join_expression(
            $from_collection,
            $joined_collection
        );
    }

    protected function _create_right_outer_join($from_collection, $joined_collection_name)
    {
        $joined_collection =
            $this->new_collection_expression( $joined_collection_name );

        $this->context->set_current_collection( $joined_collection );

        return $this->new_right_outer_join_expression(
            $from_collection,
            $joined_collection
        );
    }

    protected function _create_full_outer_join($from_collection, $joined_collection_name)
    {
        $joined_collection =
            $this->new_collection_expression( $joined_collection_name );

        $this->context->set_current_collection( $joined_collection );

        return $this->new_full_outer_join_expression(
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
        $this->statement_expression->set_filter_expression(
            $this->new_filter_expression( $filter_expression )
        );
    }

    /**
     * Creates a Filter_Expression with the having expression of the query.
     *
     * @param Filter_Expression $filter_expression The filter expression of the query.
     */
    public function having($filter_expression)
    {
        $this->statement_expression->set_having_expression(
            $this->new_having_expression( $filter_expression )
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

        $this->statement_expression->set_order_by_expression( $order_by );
    }

    /**
     * This is just a placerholder to improve DSL expressiveness, but the
     * $pagination_expression is already set to $this->statement_expression.
     */
    public function pagination($pagination_expression)
    {
        return;
    }

    /// Pagination DSL

    public function _pagination_expression()
    {
        if( ! $this->statement_expression->has_pagination_expression() ) {
            $this->statement_expression->set_pagination_expression(
                $this->new_pagination_expression()
            );
        }

        return $this->statement_expression->get_pagination_expression();
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

    /// Eager fetch

    public function eager_fetch($eager_fetch_spec)
    {
        $this->statement_expression->set_eager_fetch_spec( $eager_fetch_spec );
    }

    /// Macro expressions

    public function let($macro_name, $definition_callable)
    {
        $macro_expression = $definition_callable( $this );

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

    protected function _with_expression_context_do($expression_context, $callable)
    {
        $this->previous_expression_context = $this->context;

        $this->context = $expression_context;

        try {
            return $callable();
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