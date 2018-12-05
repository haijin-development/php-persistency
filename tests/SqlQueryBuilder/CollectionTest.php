<?php

namespace Testing\SqlQueryBuilder\SqlQueryBuilderTest;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;

class CollectionTest extends \PHPUnit\Framework\TestCase
{
    public function test_collection_name()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

        });

        $this->assertEquals(
            "select users.* from users;",
            $sql
        );
    }

    public function test_collection_alias()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" ) ->as( "c" );

        });

        $this->assertEquals(
            "select c.* from users as c;",
            $sql 
        );
    }
}