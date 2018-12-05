<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Visitors\QueryExpressionVisitor;

class SqlPaginationBuilder extends QueryExpressionVisitor
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
            return "limit " . 
                $this->escape( (string) $length ) . ", " .
                $this->escape( (string) $length * $page );
        }

        if( $offset !== null && $length !== null ) {
            return "limit " .
                $this->escape( (string) $length ) . ", " . 
                $this->escape( (string) $offset );
        } else {

            if( $length !== null ) {
                return "limit " . $this->escape( (string) $length );
            }

            if( $offset !== null ) {
                return "offset " . $this->escape( (string) $offset );
            }
        }
    }
}