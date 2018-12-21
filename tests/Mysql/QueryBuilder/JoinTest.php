<?php

namespace Mysql\QueryBuilder\JoinTest;

require_once __DIR__ . "/MysqlQueryTestBase.php";

use Haijin\Persistency\Mysql\MysqlDatabase;

class JoinTest extends \MysqlQueryTestBase
{
    public function test_join()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "id" ) ->as( "user_id" ),
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {
                    $query->proyect(
                        $query->field( "id" ) ->as( "address_id" ),
                        $query->field( "street_name" ),
                        $query->field( "street_number" )
                    );
                });
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "user_id" => 1,
                    "name" => "Lisa",
                    "last_name" => "Simpson",
                    "address_id" => 10,
                    "street_name" => "Evergreen",
                    "street_number" => "742"
                ],
                [
                    "user_id" => 2,
                    "name" => "Bart",
                    "last_name" => "Simpson",
                    "address_id" => 20,
                    "street_name" => "Evergreen",
                    "street_number" => "742"
                ],
                [
                    "user_id" => 3,
                    "name" => "Maggie",
                    "last_name" => "Simpson",
                    "address_id" => 30,
                    "street_name" => "Evergreen",
                    "street_number" => "742"
                ]
            ]
        );
    }

    public function test_multiple_joins()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "id" ) ->as( "user_id" ),
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {
                    $query->proyect(
                        $query->field( "id" ) ->as( "address_1_id" ),
                        $query->field( "street_name" ) ->as( "street_name_1" ),
                        $query->field( "street_number" ) ->as( "street_number_1" )
                    );
                });

            $query->join( "address_2" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {
                    $query->proyect(
                        $query->field( "id" ) ->as( "address_2_id" ),
                        $query->field( "street_name" ) ->as( "street_name_2" ),
                        $query->field( "street_number" ) ->as( "street_number_2" )
                    );
                });
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "user_id" => 1,
                    "name" => "Lisa",
                    "last_name" => "Simpson",
                    "address_1_id" => 10,
                    "street_name_1" => "Evergreen",
                    "street_number_1" => "742",
                    "address_2_id" => 100,
                    "street_name_2" => "Evergreen 742",
                    "street_number_2" => ""
                ],
                [
                    "user_id" => 2,
                    "name" => "Bart",
                    "last_name" => "Simpson",
                    "address_1_id" => 20,
                    "street_name_1" => "Evergreen",
                    "street_number_1" => "742",
                    "address_2_id" => 200,
                    "street_name_2" => "Evergreen 742",
                    "street_number_2" => ""
                ],
                [
                    "user_id" => 3,
                    "name" => "Maggie",
                    "last_name" => "Simpson",
                    "address_1_id" => 30,
                    "street_name_1" => "Evergreen",
                    "street_number_1" => "742",
                    "address_2_id" => 300,
                    "street_name_2" => "Evergreen 742",
                    "street_number_2" => ""
                ]
            ]
        );
    }

    public function test_join_proyections()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {
                    $query->proyect(
                        $query->field( "street_name" ),
                        $query->field( "street_number" )
                    );
                });
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "name" => "Lisa",
                    "last_name" => "Simpson",
                    "street_name" => "Evergreen",
                    "street_number" => "742"
                ],
                [
                    "name" => "Bart",
                    "last_name" => "Simpson",
                    "street_name" => "Evergreen",
                    "street_number" => "742"
                ],
                [
                    "name" => "Maggie",
                    "last_name" => "Simpson",
                    "street_name" => "Evergreen",
                    "street_number" => "742"
                ]
            ]
        );
    }

    public function test_join_macro_expressions()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "id" ) ->as( "user_id" ),
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {

                    $query->proyect(
                        $query->field( "id" ) ->as( "address_id" ),
                        $query->field( "street_name" ),
                        $query->field( "street_number" )
                    );

                    $query->let( "matches_street", function($query) {
                        return $query ->field( "id" ) ->op( "=" ) ->value( "10" );
                    });

                });

            $query->filter(
                $query ->matches_street
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "user_id" => 1,
                    "name" => "Lisa",
                    "last_name" => "Simpson",
                    "address_id" => 10,
                    "street_name" => "Evergreen",
                    "street_number" => "742"
                ],
            ]
        );
    }

    public function test_nested_joins()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "id" ) ->as( "user_id" ),
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {

                    $query->proyect(
                        $query->field( "id" ) ->as( "address_id" ),
                        $query->field( "street_name" ),
                        $query->field( "street_number" )
                    );
                    $query->join( "cities" ) ->from( "id_city" ) ->to( "id" )
                        ->eval( function($query) {

                            $query->proyect(
                                $query->field( "id" ) ->as( "city_id" ),
                                $query->field( "name" ) ->as( "city" )
                            );

                            $query->let( "matches_city", function($query) {
                                return $query ->field( "name" )
                                    ->op( "=" )
                                    ->value( "Springfield_" );
                            });

                        });
                });

            $query->filter(
                $query ->matches_city
            );

        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "user_id" => 1,
                    "name" => "Lisa",
                    "last_name" => "Simpson",
                    "address_id" => 10,
                    "street_name" => "Evergreen",
                    "street_number" => "742",
                    "city_id" => 2,
                    "city" => "Springfield_"
                ]
            ]
        );
    }

    protected function populate_tables()
    {
        parent::populate_tables();

        $db = new \mysqli( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $db->query(
            "INSERT INTO address_1 VALUES ( 10, 1, 2, 'Evergreen', '742' );"
        );
        $db->query(
            "INSERT INTO address_1 VALUES ( 20, 2, 1, 'Evergreen', '742' );"
        );
        $db->query(
            "INSERT INTO address_1 VALUES ( 30, 3, 1, 'Evergreen', '742' );"
        );

        $db->query(
            "INSERT INTO address_2 VALUES ( 100, 1, 1, 'Evergreen 742', '' );"
        );
        $db->query(
            "INSERT INTO address_2 VALUES ( 200, 2, 1, 'Evergreen 742', '' );"
        );
        $db->query(
            "INSERT INTO address_2 VALUES ( 300, 3, 1, 'Evergreen 742', '' );"
        );

        $db->query(
            "INSERT INTO cities VALUES ( 1, 'Springfield' );"
        );

        $db->query(
            "INSERT INTO cities VALUES ( 2, 'Springfield_' );"
        );

        $db->close();
    }
}