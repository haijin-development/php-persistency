<?php

namespace Haijin\Persistency\Engines\Elasticsearch;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statement_Compiler\Update_Statement_Compiler;

class Elasticsearch_Update_Statement_Compiler extends Update_Statement_Compiler
{
    public function script($script)
    {
        $this->statement_expression->set_script( $script );

        return $this;
    }

    /**
     * Returns the concrete statement instance.
     */
    protected function new_statement_expression()
    {
        return Create::a( Elasticsearch_Update_Statement::class )->with(
            $this->context
        );
    }
}