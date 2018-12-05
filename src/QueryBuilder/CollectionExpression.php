<?php

namespace Haijin\Persistency\QueryBuilder;

class CollectionExpression extends Expression
{
    protected $collection_name;

    /// Initializing

    public function __construct($macro_expressions, $collection_name = null)
    {
        parent::__construct( $macro_expressions );

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
