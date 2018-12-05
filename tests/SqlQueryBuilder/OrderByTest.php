<?php

namespace Testing\SqlQueryBuilderTest;

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
            "select users.* from users order by users.name, users.last_name;",
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
            "select users.* from users order by users.name desc;",
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
            "select users.* from users order by users.name asc;",
            $sql
        );
    }
}