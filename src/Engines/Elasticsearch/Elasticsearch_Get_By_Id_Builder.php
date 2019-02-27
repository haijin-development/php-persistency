<?php

namespace Haijin\Persistency\Engines\Elasticsearch;

use Haijin\Instantiator\Global_Factory;
use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements_Visitors\Abstract_Query_Expression_Visitor;
use Haijin\Persistency\Statements_Visitors\Query_Visitor_Trait;
use Haijin\Persistency\Statements\Expressions\Brackets_Expression;
use Haijin\Persistency\Statements\Expressions\Binary_Operator_Expression;
use Haijin\Persistency\Statements\Expressions\Field_Expression;
use Haijin\Persistency\Statements\Expressions\Value_Expression;

class Elasticsearch_Get_By_Id_Builder extends Abstract_Query_Expression_Visitor
{
    use Query_Visitor_Trait;

    protected $id;

    public function get_id_from($statement)
    {
        if( ! $statement->has_filter_expression() ) {
            return null;
        }

        $filter = $statement->get_filter_expression()->get_matching_expression();

        if( is_a( $filter, Brackets_Expression::class ) ) {
            $filter = $filter->get_expression();
        }

        if( ! is_a( $filter, Binary_Operator_Expression::class ) ) {
            return null;
        }

        $param_1 = $filter->get_parameter_1();

        if( ! is_a( $param_1, Field_Expression::class ) || $param_1->get_field_name() != "id" ) {
            return null;
        }

        $param_2 = $filter->get_parameter_2();

        if( ! is_a( $param_2, Value_Expression::class ) ) {
            return null;
        }

        return $param_2->get_value();
    }
}