<?php

namespace Haijin\Persistency\Sql\Query_Builder;

use Haijin\Persistency\Query_Builder\Visitors\Abstract_Query_Expression_Visitor;
use Haijin\Persistency\Query_Builder\Visitors\Expressions\Collection_Visitor;

class Sql_Collection_Builder extends Collection_Visitor
{
    use Sql_Builder_Trait;

    /// Visiting

    /**
     * Accepts a Collection_Expression.
     */
    public function accept_collection_expression($collection_expression)
    {
        return "from " . $this->escape_sql(
                $collection_expression->get_collection_name()
            );
    }
}