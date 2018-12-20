<?php

namespace SqlQueryBuilder\FilterMacrosTest;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;

class FilterMacrosTest extends \PHPUnit\Framework\TestCase
{
    public function test_macro_definition()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->let( "matches_name", function($query) { return $query
                ->field( "name" ) ->op( "=" ) ->value( "Lisa" );
            });

            $query->filter( $query
                ->matches_name
            );
        });

        $this->assertEquals(
            "select users.* from users where users.name = 'Lisa';",
            $sql
        );
    }

    public function test_combining_macro_definitions()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->let( "matches_name", function($query) { return $query
                ->field( "name" ) ->op( "=" ) ->value( "Lisa" );
            });

            $query->let( "matches_last_name", function($query) { return $query
                ->field( "last_name" ) ->op( "=" ) ->value( "Simpson" );
            });

            $query->filter(
                $query ->matches_name ->and() ->matches_last_name
            );
        });

        $this->assertEquals(
            "select users.* from users where users.name = 'Lisa' and users.last_name = 'Simpson';",
            $sql
        );
    }
}