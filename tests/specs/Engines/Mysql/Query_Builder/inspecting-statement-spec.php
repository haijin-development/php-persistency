<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;

$spec->describe( "When inspecting a statement of a MySql expression", function() {

    $this->let( "database", function() {

        $database = new Mysql_Database();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        return $database;

    });

    $this->it( "inspects a query statement", function() {

        $rows = $this->database->query( function($query) {

            $query->collection( "users" );

        }, [], function($sql, $sql_parameters) {

            \inspect( $sql );
            \inspect( $sql_parameters );

        });

    });

});