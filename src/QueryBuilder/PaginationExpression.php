<?php

namespace Haijin\Persistency\QueryBuilder;

class PaginationExpression extends Expression
{
    protected $offset;
    protected $length;
    protected $page;

    /// Initializing

    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->offset = null;
        $this->length = null;
        $this->page = null;
    }

    /// Accessing

    public function get_offset()
    {
        return $this->offset;
    }

    public function set_offset($offset)
    {
        $this->offset = $offset;
    }

    public function get_length()
    {
        return $this->length;
    }

    public function set_length($length)
    {
        $this->length = $length;
    }

    public function get_page()
    {
        return $this->page;
    }

    public function set_page($page)
    {
        $this->page = $page;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_pagination_expression( $this );
    }
}
