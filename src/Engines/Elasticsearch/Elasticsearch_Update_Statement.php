<?php

namespace Haijin\Persistency\Engines\Elasticsearch;

use Haijin\Persistency\Statements\Update_Statement;

class Elasticsearch_Update_Statement extends Update_Statement
{
    protected $script_expression;

    /// Initializing

    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->script = null;
    }

    /// Accessing

    public function set_script_expression($script_expression)
    {
        $this->script_expression = $script_expression;
    }

    public function get_script_expression()
    {
        return $this->script_expression;
    }

    /// Asking

    public function has_script_expression()
    {
        return $this->script_expression !== null;
    }
}