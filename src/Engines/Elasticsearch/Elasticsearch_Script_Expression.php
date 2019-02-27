<?php

namespace Haijin\Persistency\Engines\Elasticsearch;

use Haijin\Persistency\Statements\Expressions\Expression;

class Elasticsearch_Script_Expression extends Expression
{
    protected $inner_expression;

    /// Initializing

    public function __construct($expression_context, $inner_expression = null)
    {
        parent::__construct( $expression_context );

        $this->inner_expression = $inner_expression;
    }

    /// Accessing

    /**
     * Returns the matching inner_expression.
     */
    public function get_inner_expression()
    {
        return $this->inner_expression;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_script_expression( $this );
    }
}
