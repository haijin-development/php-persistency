<?php

namespace Haijin\Persistency\Statements\Expressions;

class Count_Expression extends Expression
{
    use Expression_Trait;

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_count_expression( $this );
    }
}
