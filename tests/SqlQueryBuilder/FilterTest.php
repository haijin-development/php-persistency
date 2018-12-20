<?php

namespace SqlQueryBuilder\FilterTest;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;

class FilterTest extends \PHPUnit\Framework\TestCase
{
    public function test_relative_field_expression()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
            );
        });

        $this->assertEquals(
            "select users.* from users where users.name = 'Lisa';",
            $sql
        );
    }

    public function test_absolute_field_expression()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" ) ->as( "u" );

            $query->filter(
                $query ->field( "u.name" ) ->op( "=" ) ->value( "Lisa" )
            );
        });

        $this->assertEquals(
            "select u.* from users as u where u.name = 'Lisa';",
            $sql
        );
    }

    public function test_constant_value()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
            );
        });

        $this->assertEquals(
            "select users.* from users where users.name = 'Lisa';",
            $sql
        );
    }

    public function test_function_with_values()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->f(1, 2) ->op( "=" ) ->value(3)
            );
        });

        $this->assertEquals(
            "select users.* from users where f(1, 2) = 3;",
            $sql
        );
    }

    public function test_function_with_value_expressions()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->f( $query->value(1), $query->value(2) ) ->op( "=" ) ->value(3)
            );
        });

        $this->assertEquals(
            "select users.* from users where f(1, 2) = 3;",
            $sql
        );
    }

    public function test_function_nested_functions()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->f( $query->g(1), $query->h(2) ) ->op( "=" ) ->value(3)
            );
        });

        $this->assertEquals(
            "select users.* from users where f(g(1), h(2)) = 3;",
            $sql
        );
    }

    public function test_binary_operator()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                ( $query->value( 1 ) ->op( "+" ) ->value( 2 ) ) ->op( "=" ) ->value(3)
            );
        });

        $this->assertEquals(
            "select users.* from users where 1 + 2 = 3;",
            $sql
        );
    }

    public function test_is_null_operator()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->value( 1 ) ->is_null()
            );
        });

        $this->assertEquals(
            "select users.* from users where 1 is null;",
            $sql
        );
    }

    public function test_is_not_null_operator()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->value( 1 ) ->is_not_null()
            );
        });

        $this->assertEquals(
            "select users.* from users where 1 is not null;",
            $sql
        );
    }

    public function test_unary_function()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "name" ) ->uppercase() ->is_not_null()
            );
        });

        $this->assertEquals(
            "select users.* from users where uppercase(users.name) is not null;",
            $sql
        );
    }

    public function test_brackets()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->brackets( $query->value( 1 ) )
            );
        });

        $this->assertEquals(
            "select users.* from users where (1);",
            $sql
        );
    }

    public function test_and()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->value( 1 ) ->and() ->value( 1 )
            );
        });

        $this->assertEquals(
            "select users.* from users where 1 and 1;",
            $sql
        );
    }

    public function test_or()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->value( 1 ) ->or() ->value( 1 )
            );
        });

        $this->assertEquals(
            "select users.* from users where 1 or 1;",
            $sql
        );
    }
}