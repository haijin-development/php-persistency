<?php

namespace Haijin\Persistency\Engines\Elasticsearch;

use Haijin\Instantiator\Global_Factory;
use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements_Visitors\Abstract_Query_Expression_Visitor;
use Haijin\Persistency\Statements_Visitors\Query_Visitor_Trait;
use Haijin\Persistency\Errors\Query_Expressions\Missing_Page_Number_Expression_Error;
use Haijin\Persistency\Errors\Query_Expressions\Missing_Page_Size_Expression_Error;

class Elasticsearch_Query_Builder extends Abstract_Query_Expression_Visitor
{
    use Query_Visitor_Trait;

    protected $collection_name;
    protected $proyected_fields;
    protected $body;
    protected $order_by_fields;
    protected $record_values;
    protected $script;

    /// Initializing

    public function __construct()
    {
        $this->collection_name = null;
        $this->proyected_fields = null;
        $this->body = null;
        $this->order_by_fields = [];
        $this->record_values = [];
        $this->script = null;
        $this->offset = null;
        $this->limit = null;

    }

    /// Acessing

    public function get_collection_name()
    {
        return $this->collection_name;
    }

    public function get_proyected_fields(){
        return $this->proyected_fields;
    }

    public function get_body()
    {
        return $this->body;
    }

    public function get_order_by_fields()
    {
        return $this->order_by_fields;
    }

    public function get_record_values()
    {
        return $this->record_values;
    }

    public function get_script()
    {
        return $this->script;
    }

    public function get_offset()
    {
        return $this->offset;
    }

    public function get_limit()
    {
        return $this->limit;
    }

    /**
     * Accepts a Query_Statement.
     */
    public function accept_query_statement($query_statement)
    {
        $this->collection_name = $this->visit( $query_statement->get_collection_expression() );

        if( $query_statement->has_order_by_expression() ) {
            $this->order_by_fields = $this->visit( $query_statement->get_order_by_expression() );
        }

        if( $query_statement->has_proyection_expression() ) {

            $this->proyected_fields =
                $this->visit( $query_statement->get_proyection_expression() )->to_array();

        }

        if( $query_statement->has_filter_expression() ) {

            if( $this->body === null ) {
                $this->body = new \stdclass();
            }

            $this->body->query = $this->visit( $query_statement->get_filter_expression() );
        }

        if( $query_statement->has_pagination_expression() ) {
            $this->pagination = $this->visit( $query_statement->get_pagination_expression() );
        }
    }

    /**
     * Accepts a Create_Statement.
     */
    public function accept_create_statement($create_statement)
    {
        $this->collection_name = $this->visit( $create_statement->get_collection_expression() );

        $this->record_values = $this->visit( $create_statement->get_records_values_expression() );
    }

    /**
     * Accepts an Update_Statement.
     */
    public function accept_update_statement($update_statement)
    {
        $this->collection_name = $this->visit(

            $update_statement->get_collection_expression()

        );

        if( $update_statement->has_records_values_expression() ) {

            $this->record_values = $this->visit(
                $update_statement->get_records_values_expression()
            );

        }

        if( $update_statement->has_script_expression() ) {

            if( $this->body === null ) {
                $this->body = new \stdclass();
            }

            $this->body->script = $this->visit( $update_statement->get_script_expression() );
        }

        if( $update_statement->has_filter_expression() ) {

            if( $this->body === null ) {
                $this->body = new \stdclass();
            }

            $this->body->query = $this->visit(
                $update_statement->get_filter_expression()
            );

        }
    }

    /**
     * Accepts a Delete_Statement.
     */
    public function accept_delete_statement($delete_statement)
    {
        $this->collection_name = $this->visit( $delete_statement->get_collection_expression() );

        if( $delete_statement->has_filter_expression() ) {

            if( $this->body === null ) {
                $this->body = new \stdclass();
            }

            $this->body->query = $this->visit( $delete_statement->get_filter_expression() );

        }
    }

    /**
     * Accepts a Collection_Expression.
     */
    public function accept_collection_expression($collection_expression)
    {
        return $collection_expression->get_collection_name();
    }

    /**
     * Accepts a Proyection_Expression.
     */
    public function accept_proyection_expression($proyection_expression)
    {
        return $proyection_expression->get_proyected_expressions()->collect( function($exp) {

            return $this->visit( $exp );

        }, $this );
    }


    /**
     * Accepts a Filter_Expression.
     */
    public function accept_filter_expression($filter_expression)
    {
        return Create::a( Elasticsearch_Filter_Builder::class )->with()
            ->visit( $filter_expression );
    }

    /**
     * Accepts a Script_Expression.
     */
    public function accept_script_expression($script_expression)
    {
        return $script_expression->get_inner_expression();
    }

    /**
     * Accepts a Order_By_Expression.
     */
    public function accept_order_by_expression($order_by_expression)
    {
        $fields = [];

        foreach( $order_by_expression->get_order_by_expressions()->to_array() as $field_exp )
        {
           $fields[] = $this->visit( $field_exp );
        }

        return $fields;
    }

    /**
     * Accepts a Record_Values_Expression.
     */
    public function accept_record_values_expression($record_values_expression)
    {
        $values = [];

        foreach( $record_values_expression->get_field_values() as $record_value ) {
            $values[ $record_value->get_field_name() ] =
                $this->visit( $record_value->get_value_expression() );
        }

        return $values;
    }

    /**
     * Accepts a Pagination_Expression.
     */
    public function accept_pagination_expression($pagination_expression)
    {
        $limit = $pagination_expression->get_limit();
        $offset = $pagination_expression->get_offset();
        $page = $pagination_expression->get_page_number();
        $page_size = $pagination_expression->get_page_size();

        if( $limit !== null ) {
            $this->limit = $limit;

        }

        if( $offset !== null ) {
            $this->offset = $offset;

        }

        if( $page !== null || $page_size != null ) {

            if( $page === null ) {
                $this->raise_missing_page_number_expression_error();
            }

            if( $page_size === null ) {
                $this->raise_missing_page_size_expression_error();
            }

            $this->limit = $page_size;
            $this->offset = $page * $page_size;

        }

    }

    /**
     * Accepts an All_Fields_Expression.
     */
    public function accept_all_fields_expression($all_fields_expression)
    {
        return null;
    }

    /**
     * Accepts a Field_Expression.
     */
    public function accept_field_expression($field_expression)
    {
        return $field_expression->get_field_name();
    }

    /**
     * Accepts a Value_Expression.
     */
    public function accept_value_expression($value_expression)
    {
        return $value_expression->get_value();
    }

    public function accept_function_call_expression($function_call_expression)
    {
        if( $function_call_expression->get_function_name() == "desc" ) {
            $field_name = $this->visit( $function_call_expression->get_parameters()[ 0 ] );
            return $field_name . ":desc";
        }

        if( $function_call_expression->get_function_name() == "asc" ) {
            $field_name = $this->visit( $function_call_expression->get_parameters()[ 0 ] );
            return $field_name . ":asc";
        }

        parent::accept_function_call_expression( $function_call_expression );
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