<?php

namespace Haijin\Persistency\Engines\Elasticsearch;

use Haijin\Persistency\Statements\Update_Statement;

class Elasticsearch_Update_Statement extends Update_Statement
{
    protected $script;

    /// Initializing

    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->script = null;
    }

    /// Accessing

    public function set_script($script)
    {
        $this->script = $script;
    }

    public function get_script()
    {
        return $this->script;
    }

    /// Asking

    public function has_script()
    {
        return $this->script !== null;
    }
}