<?php

namespace SqlQueryBuilder\OrderByTest;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;

class OrderByTest extends \PHPUnit\Framework\TestCase
{
    public function test_order_by_fields()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "name" ),
                $query->field( "last_name" )
            );
        });

        $this->assertEquals(
            "select users.* from users order by name, last_name;",
            $sql
        );
    }

    public function test_order_by_desc()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "name" ) ->desc()
            );
        });

        $this->assertEquals(
            "select users.* from users order by name desc;",
            $sql
        );
    }

    public function test_order_by_asc()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "name" ) ->asc()
            );
        });

        $this->assertEquals(
            "select users.* from users order by name asc;",
            $sql
        );
    }

    public function test_order_by_aliased_fields()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ) ->as( "n" )
            );

            $query->order_by(
                $query->field( "n" )
            );
        });

        $this->assertEquals(
            "select users.name as n from users order by n;",
            $sql
        );
    }
}