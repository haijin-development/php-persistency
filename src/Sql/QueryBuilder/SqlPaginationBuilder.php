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
        $length = $pagination_expression->get_length();
        $offset = $pagination_expression->get_offset();
        $page = $pagination_expression->get_page();

        if( $page !== null && $length !== null ) {
            return $this->page_sql($page, $length);
        }

        if( $offset !== null && $length !== null ) {
            return $this->offset_and_limit_sql( $offset, $length );
        }

        if( $length !== null ) {
            return "limit " . $this->escape_sql( (string) $length );
        }

        if( $offset !== null ) {
            return "offset " . $this->escape_sql( (string) $offset );
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
        return "limit " .
                $this->escape_sql( (string) $limit ) . ", " . 
                $this->escape_sql( (string) $offset );
    }
}