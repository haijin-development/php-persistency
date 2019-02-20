<?php

namespace Haijin\Persistency\Statements\Expressions;

class All_Fields_Expression extends Expression
{
    use Expression_Trait;

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_all_fields_expression( $this );
    }
}
