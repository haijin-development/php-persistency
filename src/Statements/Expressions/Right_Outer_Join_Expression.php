<?php

namespace Haijin\Persistency\Statements\Expressions;

class Right_Outer_Join_Expression extends Join_Expression
{
    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_right_outer_join_expression( $this );
    }
}
