<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Visitors\Expressions\PaginationVisitor;

class SqlPaginationBuilder extends PaginationVisitor
{
    use SqlBuilderTrait;

    /// Visiting

    /**
     * Accepts a PaginationExpression.
     */
    public function accept_pagination_expression($pagination_expression)
    {
        $page_number = $pagination_expression->get_page_number();
        $page_size = $pagination_expression->get_page_size();

        if( $page_number !== null && $page_size !== null ) {
            return $this->page_sql($page_number, $page_size);
        }

        $offset = $pagination_expression->get_offset();
        $limit = $pagination_expression->get_limit();

        if( $offset !== null || $limit !== null ) {
            return $this->offset_and_limit_sql( $offset, $limit );
        }
    }

    protected function page_sql($page_number, $page_size)
    {
        return "limit " . 
                $this->escape_sql( (string) $page_size ) . ", " .
                $this->escape_sql( (string) $page_size * $page_number );
    }

    protected function offset_and_limit_sql($offset, $limit)
    {
        if( $offset === null ) {
            return "limit " . $this->escape_sql( (string) $limit );
        }

        if( $limit === null ) {
            return "offset " . $this->escape_sql( (string) $offset );
        }

        return "limit " .
                $this->escape_sql( (string) $limit ) . ", " . 
                $this->escape_sql( (string) $offset );
    }
}