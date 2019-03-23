<?php

namespace Haijin\Persistency\Statements;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements\Expressions\Expression;

abstract class Statement extends Expression
{
    protected $collection_expression;
    protected $extra_parameters;
    protected $meta_model;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->collection_expression = null;
        $this->extra_parameters = null;
        $this->meta_model = null;
    }

    /// Accessing

    /**
     * Returns the collection expression.
     */
    public function get_collection_expression()
    {
        return $this->collection_expression;
    }

    /**
     * Sets the collection_expression.
     */
    public function set_collection_expression($collection_expression)
    {
        $this->collection_expression = $collection_expression;
        $this->context->set_current_collection( $collection_expression );
    }

    /**
     * Returns the extra_parameters expression.
     */
    public function get_extra_parameters()
    {
        return $this->extra_parameters;
    }

    /**
     * Sets the extra_parameters.
     */
    public function set_extra_parameters($extra_parameters)
    {
        $this->extra_parameters = $extra_parameters;
    }

    /**
     * Returns the meta_model of the expression.
     */
    public function get_meta_model()
    {
        return $this->meta_model;
    }

    /**
     * Sets the meta_model.
     */
    public function set_meta_model($meta_model)
    {
        $this->meta_model = $meta_model;

        return $this;
    }

    /// Accessing

    public function has_collection_expression()
    {
        return $this->collection_expression !== null;
    }
}