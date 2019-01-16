<?php

namespace Haijin\Persistency\Query_Builder;

use Haijin\Persistency\Query_Builder\Builders\Query_Expression_Builder;
use Haijin\Ordered_Collection;

class Join_Expression extends Expression
{
    protected $from_collection;
    protected $to_collection;
    protected $from_field;
    protected $to_field;
    protected $proyection;
    protected $joins;

    /// Initializing

    public function __construct($expression_context, $from_collection, $to_collection)
    {
        parent::__construct( $expression_context );

        $this->from_collection = $from_collection;
        $this->to_collection = $to_collection;
        $this->from_field = null;
        $this->to_field = null;
        $this->proyection = $this->new_proyection_expression();
        $this->joins = new Ordered_Collection();
    }

    /// Accessing

    public function get_from_collection()
    {
        return $this->from_collection;
    }

    public function get_to_collection()
    {
        return $this->to_collection;
    }

    public function set_to_collection($to_collection)
    {
        $this->to_collection = $to_collection;
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

    public function get_nested_joins()
    {
        $joins = Ordered_Collection::with( $this );

        $this->joins->each_do( function($each_nested_join) use($joins) {
            $joins->add_all( $each_nested_join->get_nested_joins() );
        });

        return $joins;
    }

    public function get_collection_name()
    {
        return $this->to_collection->get_collection_name();
    }

    public function get_referenced_name()
    {
        return $this->to_collection->get_referenced_name();
    }

    /// Iterating

    public function joins_do($closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        return $this->joins->each_do( $closure, $binding );
    }

    /// DSL

    /**
     * Defines the an alias for the joined collection $this->to_collection.
     *
     * @param string $alias The alias for the collection.
     */
    public function as($alias)
    {
        $alias_expression = $this->new_alias_expression(
            $alias,
            $this->to_collection
        );

        $this->to_collection = $alias_expression;

        $new_context = $this->new_expression_context(
            $this->get_macros_dictionary(),
            $this->to_collection
        );

        $this->update_expression_context_to( $new_context );

        return $this;
    }

    protected function update_expression_context_to($new_context)
    {
        $this->to_collection->set_context( $new_context );
        $this->set_context( $new_context );
        $this->proyection->set_context( $new_context );
    }

    public function from($from_field)
    {
        $from_field_expression = $this->new_field_expression( $from_field );
        $from_field_expression->set_context(
            $this->new_expression_context(
                $this->get_macros_dictionary(),
                $this->from_collection
            )
        );

        $this->set_from_field(
            $from_field_expression
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

        $join_query_builder = $this->new_query_expression_builder( $this->context );

        $build_closure->call( $binding, $join_query_builder );

        $this->proyection = $join_query_builder->get_proyection();

        $this->joins = $join_query_builder->get_joins();

        $this->context->add_macro_definitions_from(
            $join_query_builder->get_macros_dictionary()
        );
    }

    /// Creating instances

    public function new_query_expression_builder($expression_context = null)
    {
        return new Query_Expression_Builder( $expression_context );
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_join_expression( $this );
    }
}
