<?php

namespace Haijin\Persistency\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Builders\QueryExpressionBuilder;
use Haijin\Tools\OrderedCollection;

class JoinExpression extends Expression
{
    protected $collection;
    protected $from_field;
    protected $to_field;
    protected $proyection;
    protected $joins;

    /// Initializing

    public function __construct($macro_expressions, $collection = null)
    {
        parent::__construct( $macro_expressions );

        $this->collection = $collection;
        $this->from_field = null;
        $this->to_field = null;
        $this->proyection = $this->new_proyection_expression();
        $this->joins = new OrderedCollection();
    }

    /// Accessing

    public function get_collection()
    {
        return $this->collection;
    }

    public function set_collection($collection)
    {
        $this->collection = $collection;
    }

    public function get_from_field()
    {
        return $this->from_field;
    }

    public function set_from_field($from_field)
    {
        $this->from_field = $from_field;
    }

    public function get_to_field()
    {
        return $this->to_field;
    }

    public function set_to_field($to_field)
    {
        $this->to_field = $to_field;
    }

    public function get_proyection()
    {
        return $this->proyection;
    }

    public function set_proyection($proyection)
    {
        $this->proyection = $proyection;
    }

    public function add_join($join_expression)
    {
        $this->joins->add( $join_expression );

        return $join_expression;
    }

    public function get_joins()
    {
        return $this->joins;
    }

    public function get_collection_name()
    {
        return $this->collection->get_collection_name();
    }

    public function get_referenced_name()
    {
        return $this->collection->get_referenced_name();
    }

    /// DSL

    public function from($from_field)
    {
        $this->set_from_field(
            $this->new_field_expression( $from_field )
        );

        return $this;
    }

    public function to($to_field)
    {
        $this->set_to_field(
            $this->new_field_expression( $to_field )
        );

        return $this;
    }

    public function eval($build_closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $join_query_builder = $this->new_query_expression_builder();

        $build_closure->call( $binding, $join_query_builder );

        $this->proyection = $join_query_builder->get_proyection();
        $this->join = $join_query_builder->get_join();

        $this->macro_expressions->merge_with(
            $join_query_builder->get_macro_expressions()
        );
    }

    /// Creating instances

    public function new_query_expression_builder()
    {
        return new QueryExpressionBuilder();
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_join_expression( $this );
    }
}
