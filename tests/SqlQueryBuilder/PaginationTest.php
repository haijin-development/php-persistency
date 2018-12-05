<?php

namespace Testing\SqlQueryBuilderTest;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;

class PaginationTest extends \PHPUnit\Framework\TestCase
{
    public function test_offset()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->pagination(
                $query->offset( 10 )
            );
        });

        $this->assertEquals(
            "select users.* from users offset 10;",
            $sql
        );
    }

    public function test_limit()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->pagination(
                $query->limit( 10 )
            );
        });

        $this->assertEquals(
            "select users.* from users limit 10;",
            $sql
        );
    }

    public function test_limit_and_offset()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->pagination(
                $query
                    ->offset( 1 )
                    ->limit( 10 )
            );
        });

        $this->assertEquals(
            "select users.* from users limit 10, 1;",
            $sql
        );
    }

    public function test_page()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->pagination(
                $query
                    ->page( 3 )
                    ->page_size( 10 )
            );
        });

        $this->assertEquals(
            "select users.* from users limit 10, 30;",
            $sql
        );
    }
}