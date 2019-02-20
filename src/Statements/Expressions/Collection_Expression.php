<?php

namespace Haijin\Persistency\Statements\Expressions;

class Collection_Expression extends Expression
{
    protected $collection_name;

    /// Initializing

    public function __construct($expression_context, $collection_name = null)
    {
        parent::__construct( $expression_context );

        $this->collection_name = $collection_name;
    }

    /// Accessing

    public function get_collection_name()
    {
        return $this->collection_name;
    }

    public function set_collection_name($collection_name)
    {
        $this->collection_name = $collection_name;
    }

    public function get_referenced_name()
    {
        return $this->collection_name;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_collection_expression( $this );
    }
}
