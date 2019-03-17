<?php

namespace Haijin\Persistency\Sql;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_Builder;
use Haijin\Persistency\Statement_Compiler\Query_Statement_Compiler;

use Haijin\Persistency\Sql\Expression_Builders\Sql_Collection_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Proyection_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Join_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Filter_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Group_By_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Having_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Order_By_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Pagination_Builder;


class Sql_Query_Statement_Builder extends Sql_Expression_Builder
{
    public function __construct()
    {
        parent::__construct( new Ordered_Collection() );
    }

     /// Building

     /**
      * Builds and returns a new SQL string.
      *
      * @param callable $expression_callable The callable to build the Query_Statement
      *      using a DSL.
      *
      * @return Query_Statement The built Query_Statement.
      */
     public function build($expression_callable)
     {
         $create_statement = $this->new_query_statement_compiler()
             ->compile( $expression_callable );

         return $this->build_sql_from( $create_statement );
     }


    /// Visiting

    /**
     * Accepts a Query_Statement.
     */
    public function accept_query_statement($query_statement)
    {
        $this->validate_statement( $query_statement );

        $sql = "";

        $sql .= $this->nested_proyections_sql_from( $query_statement );

        $sql .= " ";

        $sql .= $this->visit( $query_statement->get_collection_expression() );

        $sql .= $this->join_expressions_sql_from( $query_statement );

        if( $query_statement->has_filter_expression() ) {
            $sql .= $this->visit( $query_statement->get_filter_expression() );
        }

        if( $query_statement->has_group_by_expression() ) {
            $sql .= $this->visit( $query_statement->get_group_by_expression() );
        }

        if( $query_statement->has_having_expression() ) {
            $sql .= $this->visit( $query_statement->get_having_expression() );
        }

        if( $query_statement->has_order_by_expression() ) {
            $sql .= $this->visit( $query_statement->get_order_by_expression() );
        }

        if( $query_statement->has_pagination_expression() ) {
            $sql .= $this->visit( $query_statement->get_pagination_expression() );
        }

        $sql .= ";";

        return $sql;
    }

    public function nested_proyections_sql_from($expression)
    {
        return "select " . $this->get_nested_proyections_sql_from( $expression );
    }

    public function get_nested_proyections_sql_from($expression)
    {
        $proyected_fields = new Ordered_Collection();

        $proyected_fields[] = $this->proyected_fields_from( $expression );

        $expression->join_expressions_do( function($join_expression) use($proyected_fields) {
            $sql_builder = $this->new_sql_builder( $join_expression );

            $proyected_fields[] =
                $sql_builder->get_nested_proyections_sql_from( $join_expression );

        });

        $proyected_fields = array_filter( $proyected_fields->to_array() );

        return join( ', ', $proyected_fields );
    }

    public function proyected_fields_from($expression)
    {
        $proyection_builder = $this->new_sql_proyection_builder();

        return $proyection_builder->proyections_from(
            $expression->get_proyection_expression()
        );
    }

    /**
     * Accepts a Collection_Expression.
     */
    public function accept_collection_expression($collection_expression)
    {
        return $this->new_sql_collection_builder()
            ->build_sql_from( $collection_expression );
    }

    /**
     * Accepts an Inner_Join_Expression.
     */
    public function accept_inner_join_expression($join_expression)
    {
        return $this->new_sql_join_builder()
            ->build_sql_from( $join_expression );
    }

    /**
     * Accepts a Left_Outer_Join_Expression.
     */
    public function accept_left_outer_join_expression($join_expression)
    {
        return $this->new_sql_join_builder()
            ->build_sql_from( $join_expression );
    }

    /**
     * Accepts a Right_Outer_Join_Expression.
     */
    public function accept_right_outer_join_expression($join_expression)
    {
        return $this->new_sql_join_builder()
            ->build_sql_from( $join_expression );
    }

    /**
     * Accepts n Full_Outer_Join_Expression.
     */
    public function accept_full_outer_join_expression($join_expression)
    {
        return $this->new_sql_join_builder()
            ->build_sql_from( $join_expression );
    }

    protected function join_expressions_sql_from($query_statement)
    {
        if( ! $query_statement->has_join_expressions() ) {
            return "";
        }

        $joins = new Ordered_Collection();

        $query_statement->join_expressions_do( function($join_expression) use($joins) {

            $join_expression->get_nested_join_expressions()->each_do( function($join_expression) use($joins) {

                $joins[] = $this->visit( $join_expression );

            });

        });

        return " " . $joins->join_with( " " );
    }

    /**
     * Accepts a Filter_Expression.
     */
    public function accept_filter_expression($filter_expression)
    {
        $sql = $this->new_sql_filter_builder()
                    ->build_sql_from( $filter_expression );

        if( $sql == '' ) {
            return '';
        }

        return ' where ' . $sql ;
    }

    /**
     * Accepts a Having_Expression.
     */
    public function accept_having_expression($having_expression)
    {
        $sql = $this->new_sql_having_builder()
                    ->build_sql_from( $having_expression );

        if( $sql == '' ) {
            return '';
        }

        return ' having ' . $sql ;
    }

    /**
     * Accepts a Group_By_Expression.
     */
    public function accept_group_by_expression($group_by_expression)
    {
        return " group by " . $this->new_sql_group_by_builder()
                                ->build_sql_from( $group_by_expression );
    }

    /**
     * Accepts a Order_By_Expression.
     */
    public function accept_order_by_expression($order_by_expression)
    {
        return " order by " . $this->new_sql_order_by_builder()
                                ->build_sql_from( $order_by_expression );
    }

    /**
     * Accepts a Pagination_Expression.
     */
    public function accept_pagination_expression($pagination_expression)
    {
        return " " . $this->new_sql_pagination_builder()
                        ->build_sql_from( $pagination_expression );
    }

    /**
     * Accepts a Alias_Expression. The alias at this DSL level is for the Collection_Expression.
     */
    public function accept_alias_expression($alias_expression)
    {
        $sql = $this->visit( $alias_expression->get_aliased_expression() );
        $sql .= " as ";
        $sql .= $this->escape_sql( $alias_expression->get_alias() );

        return $sql;
    }

    /// Creating instances

    //// Query expression

    protected function new_query_statement_compiler()
    {
        return Create::object( Query_Statement_Compiler::class );
    }

    /// Validating

    protected function validate_statement($query_statement)
    {
        if( $query_statement->get_collection_expression() === null ) {
            $this->raise_invalid_expression(
                "The query statement is missing the \$query->collection(...) expression.",
                $query_statement
            );
        }
    }

    //// Sql builders

    protected function new_sql_builder()
    {
        return Create::object( self::class );
    }

    protected function new_sql_proyection_builder()
    {
        return Create::object(
            Sql_Proyection_Builder::class,
            $this->collected_parameters
        );
    }

    protected function new_sql_collection_builder()
    {
        return Create::object(
            Sql_Collection_Builder::class,
            $this->collected_parameters
        );
    }

    protected function new_sql_join_builder()
    {
        return Create::object(
            Sql_Join_Builder::class,
            $this->collected_parameters
        );
    }

    protected function new_sql_group_by_builder()
    {
        return Create::object(
            Sql_Group_By_Builder::class,
            $this->collected_parameters
        );
    }

    protected function new_sql_order_by_builder()
    {
        return Create::object(
            Sql_Order_By_Builder::class,
            $this->collected_parameters
        );
    }

    protected function new_sql_pagination_builder()
    {
        return Create::object(
            Sql_Pagination_Builder::class,
            $this->collected_parameters
        );
    }

    protected function new_sql_filter_builder()
    {
        return Create::object(
            Sql_Filter_Builder::class,
            $this->collected_parameters
        );
    }

    protected function new_sql_having_builder()
    {
        return Create::object(
            Sql_Having_Builder::class,
            $this->collected_parameters
        );
    }   
}