<?php

namespace Haijin\Persistency\Sql\Expression_Builders;

class Sql_Collection_Builder extends Sql_Expression_Builder
{
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