<?php

use Haijin\Persistency\Engines\Postgresql\Postgresql_Database;

$spec->describe( "When building the group by statement of a sql expression", function() {

    $this->before_all( function() {

        $this->clear_mysql_tables();

        pg_query(
            $this->postgresql, 
            "INSERT INTO users VALUES ( 1, 'Lisa', 'Simpson', null );"
        );

        pg_query(
            $this->postgresql, 
            "INSERT INTO users VALUES ( 2, 'Marge', 'Bouvier', null );"
        );

        pg_query(
            $this->postgresql, 
            "INSERT INTO users VALUES ( 3, 'Maggie', 'Simpson', null );"
        );

    });

    $this->after_all( function() {

        $this->clear_postgresql_tables();

    });

    $this->let( "database", function() {

        $database = new Postgresql_Database();

        $database->connect(
            "host=localhost port=5432 dbname=haijin-persistency user=haijin password=123456"
        );

        return $database;

    });

    $this->it( "builds the group by fields", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( 'last_name' ),
                $query->count() ->as( 'total' )
            );

            $query->group_by(
                $query->field( 'last_name' )
            );

            $query->order_by(
                $query->field( 'total' )
            );

        });

        $this->expect( $rows ) ->to() ->be() ->exactly_like([
            [
                'last_name' => 'Bouvier',
                'total' => 1
            ],
            [
                'last_name' => 'Simpson',
                'total' => 2
            ]
        ]);

    });

});