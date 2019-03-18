<?php

namespace Haijin\Persistency\Statements\Expressions;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statement_Compiler\Query_Statement_Compiler;
use Haijin\Ordered_Collection;

abstract class Join_Expression extends Expression
{
    protected $from_collection;
    protected $to_collection;
    protected $from_field;
    protected $to_field;
    protected $proyection_expression;
    protected $join_expressions;

    /// Initializing

    public function __construct($expression_context, $from_collection, $to_collection)
    {
        parent::__construct( $expression_context );

        $this->from_collection = $from_collection;
        $this->to_collection = $to_collection;
        $this->from_field = null;
        $this->to_field = null;
        $this->proyection_expression = $this->new_proyection_expression();
        $this->join_expressions = new Ordered_Collection();
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

    public function get_proyection_expression()
    {
        return $this->proyection_expression;
    }

    public function set_proyection_expression($proyection_expression)
    {
        $this->proyection_expression = $proyection_expression;
    }

    public function add_join_expression($join_expression)
    {
        $this->join_expressions->add( $join_expression );

        return $join_expression;
    }

    public function get_join_expressions()
    {
        return $this->join_expressions;
    }

    public function get_nested_join_expressions()
    {
        $joins = Ordered_Collection::with( $this );

        $this->join_expressions->each_do( function($each_nested_join) use($joins) {
            $joins->add_all( $each_nested_join->get_nested_join_expressions() );
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

    public function join_expressions_do($callable)
    {
        return $this->join_expressions->each_do( $callable );
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
        $this->proyection_expression->set_context( $new_context );

        if( $this->from_field !== null ) {
            $this->from( $this->from_field->get_field_name() );
        }

        if( $this->to_field !== null ) {
            $this->to( $this->to_field->get_field_name() );            
        }
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

    public function eval($build_callable)
    {
        $join_query_builder = $this->new_query_statement_compiler( $this->context );

        $build_callable( $join_query_builder );

        $this->proyection_expression =
            $join_query_builder->get_query_statement()->get_proyection_expression();

        $this->join_expressions =
            $join_query_builder->get_query_statement()->get_join_expressions();

        $this->context->add_macro_definitions_from(
            $join_query_builder->get_macros_dictionary()
        );
    }

    /// Creating instances

    public function new_query_statement_compiler($expression_context = null)
    {
        return Create::object( Query_Statement_Compiler::class,  $expression_context );
    }
}
