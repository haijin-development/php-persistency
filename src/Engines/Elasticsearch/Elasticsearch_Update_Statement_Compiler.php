<?php

namespace Haijin\Persistency\Engines\Elasticsearch;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statement_Compiler\Update_Statement_Compiler;

class Elasticsearch_Update_Statement_Compiler extends Update_Statement_Compiler
{
    public function script($script_expression)
    {
        $this->statement_expression->set_script_expression(
            $this->new_script_expression( $script_expression )
        );

        return $this;
    }

    protected function new_script_expression($expression)
    {
        return Create::a( Elasticsearch_Script_Expression::class )->with(
            $this->context,
            $expression
        );
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