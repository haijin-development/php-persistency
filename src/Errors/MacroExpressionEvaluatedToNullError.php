<?php

namespace Haijin\Persistency\Errors;

class MacroExpressionEvaluatedToNullError extends \Exception
{
    protected $macro_expression_name;

    /// Initializing

    public function __construct($error_message, $macro_expression_name)
    {
        parent::__construct( $error_message );

        $this->macro_expression_name = $macro_expression_name;
    }

    /// Accessing

    public function get_macro_expression_name()
    {
        return $this->macro_expression_name;
    }
}