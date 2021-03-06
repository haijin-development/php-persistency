<?php

namespace Haijin\Persistency\Engines\Elasticsearch\Statements_Compiler;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statement_Compiler\Update_Statement_Compiler;
use Haijin\Persistency\Engines\Elasticsearch\Statements\Elasticsearch_Update_Statement;

class Elasticsearch_Update_Statement_Compiler extends Update_Statement_Compiler
{
    public function script($script)
    {
        $this->statement_expression->set_script( $script );

        return $this;
    }
}