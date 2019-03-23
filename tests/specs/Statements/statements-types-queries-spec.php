<?php

use Haijin\Persistency\Statements\Query_Statement;
use Haijin\Persistency\Statements\Create_Statement;
use Haijin\Persistency\Statements\Update_Statement;
use Haijin\Persistency\Statements\Delete_Statement;
use Haijin\Persistency\Statements\Expressions\Alias_Expression;
use Haijin\Persistency\Statements\Expressions\All_Fields_Expression;
use Haijin\Persistency\Statements\Expressions\Binary_Operator_Expression;
use Haijin\Persistency\Statements\Expressions\Brackets_Expression;
use Haijin\Persistency\Statements\Expressions\Collection_Expression;
use Haijin\Persistency\Statements\Expressions\Field_Expression;
use Haijin\Persistency\Statements\Expressions\Field_Value_Expression;
use Haijin\Persistency\Statements\Expressions\Filter_Expression;
use Haijin\Persistency\Statements\Expressions\Full_Outer_Join_Expression;
use Haijin\Persistency\Statements\Expressions\Function_Call_Expression;
use Haijin\Persistency\Statements\Expressions\Group_By_Expression;
use Haijin\Persistency\Statements\Expressions\Having_Expression;
use Haijin\Persistency\Statements\Expressions\Ignore_Expression;
use Haijin\Persistency\Statements\Expressions\Inner_Join_Expression;
use Haijin\Persistency\Statements\Expressions\Join_Expression;
use Haijin\Persistency\Statements\Expressions\Left_Outer_Join_Expression;
use Haijin\Persistency\Statements\Expressions\Named_Parameter_Expression;
use Haijin\Persistency\Statements\Expressions\Order_By_Expression;
use Haijin\Persistency\Statements\Expressions\Pagination_Expression;
use Haijin\Persistency\Statements\Expressions\Proyection_Expression;
use Haijin\Persistency\Statements\Expressions\Raw_Expression;
use Haijin\Persistency\Statements\Expressions\Record_Values_Expression;
use Haijin\Persistency\Statements\Expressions\Right_Outer_Join_Expression;
use Haijin\Persistency\Statements\Expressions\Value_Expression;
use Haijin\Persistency\Statements\Expressions\With_Expression;

use Haijin\Persistency\Persistent_Collection\Field_Mapping;
use Haijin\Persistency\Persistent_Collection\Field_Types\Reference_Collection_From_Collection_Type;

$spec->describe( "A subclass of Expression_Visitor", function() {

    $this->let( 'all_expressions', function() {
        $field_mapping = new Field_Mapping( null );
        $field_mapping->set_type(
            new Reference_Collection_From_Collection_Type(
                Users_Collection::get(),
                null,
                null
            )
        );

        return [
            new Query_Statement( null ),
            new Create_Statement( null ),
            new Update_Statement( null ),
            new Delete_Statement( null ),
            new Alias_Expression( null, null, null ),
            new All_Fields_Expression( null ),
            new Binary_Operator_Expression( null, null, null, null ),
            new Brackets_Expression( null ),
            new Collection_Expression( null ),
            new Field_Expression( null, null ),
            new Field_Value_Expression( null, null, null ),
            new Filter_Expression( null ),
            new Full_Outer_Join_Expression( null, null, null ),
            new Function_Call_Expression( null, null, null ),
            new Group_By_Expression( null ),
            new Having_Expression( null ),
            new Ignore_Expression( null ),
            new Inner_Join_Expression( null, null, null ),
            new Left_Outer_Join_Expression( null, null, null ),
            new Named_Parameter_Expression( null, null ),
            new Order_By_Expression( null ),
            new Pagination_Expression( null ),
            new Proyection_Expression( null ),
            new Raw_Expression( null, null ),
            new Record_Values_Expression( null ),
            new Right_Outer_Join_Expression( null, null, null ),
            new Value_Expression( null, null ),
            new With_Expression( null, null, $field_mapping )
        ];
    });

    $this->it( 'returns if an expression is of its type or not', function() {

        foreach( $this->all_expressions as $each_expression ) {

            $expression_parts = explode( '\\', get_class( $each_expression ) );

            $name = $expression_parts[ count( $expression_parts ) - 1 ];

            $is_type_function_name = 'is_' . strtolower( $name );

            $each_expression->$is_type_function_name();

            foreach( $this->all_expressions as $different_expression ) {
                if( $different_expression === $each_expression ) {
                    $this->expect( $each_expression->$is_type_function_name() )
                        ->to() ->be() ->true();
                } else {
                    $this->expect( $different_expression->$is_type_function_name() )
                        ->to() ->be() ->false();
                }
            }

        }

    });
});