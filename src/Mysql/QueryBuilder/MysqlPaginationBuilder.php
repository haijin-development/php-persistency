<?php

namespace Haijin\Persistency\Mysql\QueryBuilder;

use Haijin\Persistency\Errors\QueryExpressions\MissingLimitExpressionError;
use Haijin\Persistency\Errors\QueryExpressions\MissingPageNumberExpressionError;
use Haijin\Persistency\Errors\QueryExpressions\MissingPageSizeExpressionError;
use Haijin\Persistency\QueryBuilder\Visitors\Expressions\PaginationVisitor;
use Haijin\Persistency\Sql\QueryBuilder\SqlBuilderTrait;

class MysqlPaginationBuilder extends PaginationVisitor
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

        return "limit " . 
                $this->escape_sql( (string) $page_size ) . ", " .
                $this->escape_sql( (string) $page_size * $page_number );
    }

    protected function offset_and_limit_sql($offset, $limit)
    {
        if( $limit === null ) {
            return $this->raise_missing_limit_expression_error();
        }

        if( $offset === null ) {
            return "limit " . $this->escape_sql( (string) $limit );
        }

        return "limit " .
                $this->escape_sql( (string) $limit ) . ", " . 
                $this->escape_sql( (string) $offset );
    }

    /// Raising errors

    protected function raise_missing_limit_expression_error()
    {
        throw new MissingLimitExpressionError(
            "The 'offset' expression must have a 'limit' expression as well. Please define a '\$query->limit(\$n)' expression."
        );
    }

    protected function raise_missing_page_number_expression_error()
    {
        throw new MissingPageNumberExpressionError(
            "The 'page_size' expression must have a 'page' expression as well. Please define a '\$query->page(\$n)' expression."
        );
    }

    protected function raise_missing_page_size_expression_error()
    {
        throw new MissingPageSizeExpressionError(
            "The 'page' expression must have a 'page_size' expression as well. Please define a '\$query->page_size(\$n)' expression."
        );
    }
}