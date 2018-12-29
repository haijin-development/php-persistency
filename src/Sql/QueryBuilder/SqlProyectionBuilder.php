<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\Factory\GlobalFactory;
use Haijin\Persistency\QueryBuilder\Visitors\Expressions\ProyectionVisitor;
use Haijin\Persistency\Sql\QueryBuilder\ExpressionBuilders\SqlExpressionInProyectionBuilder;
use Haijin\Tools\OrderedCollection;

class SqlProyectionBuilder extends ProyectionVisitor
{
    use SqlBuilderTrait;

    public function proyections_from($proyection_expression)
    {
        if( $proyection_expression->is_empty() ) {
            return $this->empty_proyection_sql_from( $proyection_expression );
        }

        return $this->expressions_list(
            $proyection_expression->get_proyected_expressions()
        );
    }

    protected function empty_proyection_sql_from($proyection_expression)
    {
        return $proyection_expression->get_context_collection()
                    ->get_referenced_name() . ".*";
    }

    /// Creating instances

    protected function new_sql_expression_builder()
    {
        return GlobalFactory::new( SqlExpressionInProyectionBuilder::class );
    }
}