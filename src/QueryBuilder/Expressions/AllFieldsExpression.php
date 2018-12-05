<?php

namespace Haijin\Persistency\QueryBuilder\Expressions;

use Haijin\Persistency\QueryBuilder\Expression;

class AllFieldsExpression extends Expression
{
    use ExpressionTrait;

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_all_fields_expression( $this );
    }
}
