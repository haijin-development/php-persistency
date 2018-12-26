<?php

namespace SqlQueryBuilder\FilterMacrosTest;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;

class FilterMacrosTest extends \PHPUnit\Framework\TestCase
{
    use \Haijin\Testing\AllExpectationsTrait;

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

    public function test_macro_definition_with_missing_return()
    {
        $query_builder = new SqlBuilder();

        $this->expectExactExceptionRaised(
            \Haijin\Persistency\Errors\QueryExpressions\MacroExpressionEvaluatedToNullError::class,
            function() use($query_builder) {
                $query_builder->build( function($query) {

                    $query->collection( "users" );

                    $query->let( "matches_name", function($query) { $query
                        ->field( "name" ) ->op( "=" ) ->value( "Lisa" );
                    });

                    $query->filter( $query
                        ->matches_name
                    );
                });
            },
            function($error) {
                $this->assertEquals(
                    "The macro expression 'matches_name' evaluated to null. Probably it is missing the return statement.",
                    $error->getMessage()
                );
            }
        );
    }
}