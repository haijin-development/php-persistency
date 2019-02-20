<?php

namespace Haijin\Persistency\Statements\Expressions;

class Pagination_Expression extends Expression
{
    protected $offset;
    protected $length;
    protected $page;

    /// Initializing

    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->offset = null;
        $this->limit = null;
        $this->page_number = null;
        $this->page_size = null;
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

    public function get_limit()
    {
        return $this->limit;
    }

    public function set_limit($limit)
    {
        $this->limit = $limit;
    }

    public function get_page_number()
    {
        return $this->page_number;
    }

    public function set_page_number($page_number)
    {
        $this->page_number = $page_number;
    }

    public function get_page_size()
    {
        return $this->page_size;
    }

    public function set_page_size($page_size)
    {
        $this->page_size = $page_size;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_pagination_expression( $this );
    }
}
