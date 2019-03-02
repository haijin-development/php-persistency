<?php

namespace Haijin\Persistency\Sql\Expression_Builders;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Errors\Query_Expressions\Missing_Limit_Expression_Error;
use Haijin\Persistency\Errors\Query_Expressions\Missing_Page_Number_Expression_Error;
use Haijin\Persistency\Errors\Query_Expressions\Missing_Page_Size_Expression_Error;

class Sql_Pagination_Builder extends Sql_Expression_Builder
{
    /// Visiting

    /**
     * Accepts a Pagination_Expression.
     */
    public function accept_pagination_expression($pagination_expression)
    {
        $page_number = $pagination_expression->get_page_number();
        $page_size = $pagination_expression->get_page_size();

        if( $page_number !== null || $page_size !== null ) {
            return $this->page_sql($page_number, $page_size);
        }

        $limit = $pagination_expression->get_limit();
        $offset = $pagination_expression->get_offset();

        if( $offset !== null || $limit !== null ) {
            return $this->offset_and_limit_sql( $offset, $limit );
        }
    }

    protected function page_sql($page_number, $page_size)
    {
        if( $page_number === null ) {
            return $this->raise_missing_page_number_expression_error();
        }

        if( $page_size === null ) {
            return $this->raise_missing_page_size_expression_error();
        }

        return  "limit "   . $this->escape_sql( (string) $page_size ) . " " .
                "offset " . $this->escape_sql( (string) $page_size * $page_number );
    }

    protected function offset_and_limit_sql($offset, $limit)
    {
        if( $limit === null ) {
            return "offset " . $this->escape_sql( (string) $offset );
        }

        if( $offset === null ) {
            return "limit " . $this->escape_sql( (string) $limit );
        }

        return  "limit "  . $this->escape_sql( (string) $limit ) . " " .
                "offset " . $this->escape_sql( (string) $offset );
    }

    /// Raising errors

    protected function raise_missing_limit_expression_error()
    {
        throw Create::a( Missing_Limit_Expression_Error::class )->with(
            "The 'offset' expression must have a 'limit' expression as well. Please define a '\$query->limit(\$n)' expression."
        );
    }

    protected function raise_missing_page_number_expression_error()
    {
        throw Create::a( Missing_Page_Number_Expression_Error::class )->with(
            "The 'page_size' expression must have a 'page' expression as well. Please define a '\$query->page(\$n)' expression."
        );
    }

    protected function raise_missing_page_size_expression_error()
    {
        throw Create::a( Missing_Page_Size_Expression_Error::class )->with(
            "The 'page' expression must have a 'page_size' expression as well. Please define a '\$query->page_size(\$n)' expression."
        );
    }
}