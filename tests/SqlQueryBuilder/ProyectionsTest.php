<?php

namespace Testing\SqlQueryBuilderTest;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;

class ProyectionsTest extends \PHPUnit\Framework\TestCase
{
    public function test_select_all()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->all()
            );
        });

        $this->assertEquals(
            "select users.* from users;",
            $sql
        );
    }

    public function test_select_fields()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );
        });

        $this->assertEquals(
            "select users.name, users.last_name from users;",
            $sql
        );
    }

    public function test_select_aliased_fields()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ) ->as( "n" ),
                $query->field( "last_name" ) ->as( "ln" )
            );
        });

        $this->assertEquals(
            "select users.name as n, users.last_name as ln from users;",
            $sql
        );
    }

    public function test_select_constant_values()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query ->value( 1 ),
                $query ->value( "2" )
            );
        });

        $this->assertEquals(
            "select 1, '2' from users;",
            $sql
        );
    }

    public function test_select_aliased_constant_values()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->value( 1 ) ->as( "v1" ),
                $query->value( "2" ) ->as( "v2" )
            );
        });

        $this->assertEquals(
            "select 1 as v1, '2' as v2 from users;",
            $sql
        );
    }

    public function test_function_with_values()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->f( 1, 2 )
            );
        });

        $this->assertEquals(
            "select f(1, 2) from users;",
            $sql
        );
    }

    public function test_function_with_value_expressions()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->f(
                    $query->value( 1 ),
                    $query->value( 2 )
                )
            );
        });

        $this->assertEquals(
            "select f(1, 2) from users;",
            $sql
        );
    }

    public function test_nested_functions_expressions()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->f(
                    $query->g( 1 ),
                    $query->h( 2 )
                )
            );
        });

        $this->assertEquals(
            "select f(g(1), h(2)) from users;",
            $sql
        );
    }

    public function test_binary_operator()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->value( 1 ) ->op( "+" ) ->value( 2 )
            );
        });

        $this->assertEquals(
            "select 1 + 2 from users;",
            $sql
        );
    }
}