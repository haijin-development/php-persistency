<?php

namespace Haijin\Persistency\Statement_Compiler;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements\Delete_Statement;

/**
 * Object to build a Delete_Statement from an update statement definition closure.
 */
class Delete_Statement_Compiler extends Statement_Compiler
{
    /**
     * Returns the concrete statement instance.
     */
    protected function new_statement_expression()
    {
        return Create::object( Delete_Statement::class, 
            $this->context
        );
    }

    /// Accessing

    public function get_delete_expression()
    {
        return $this->statement_expression;
    }

    /// DSL

    public function filter($filter_expression)
    {
        $this->statement_expression->set_filter_expression(
            $this->new_filter_expression( $filter_expression )
        );

        return $this;
    }
}