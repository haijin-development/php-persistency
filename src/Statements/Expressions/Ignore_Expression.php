<?php

namespace Haijin\Persistency\Statements\Expressions;

class Ignore_Expression extends Expression
{
    use Expression_Trait;

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_ignore_expression( $this );
    }

    /// Asking

    public function is_ignore_expression()
    {
        return true;
    }
}
