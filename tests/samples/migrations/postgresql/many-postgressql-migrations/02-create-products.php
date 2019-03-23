<?php

$migration->definition( function() {

    $this->id = 2;

    $this->name = "Create products tables";

    $this->describe( "Create the products Postgresql table", function($database) {

        $database->evaluate_sql_string(
            "CREATE TABLE products (
                id SERIAL PRIMARY KEY,
                name varchar(45) NULL,
                last_name varchar(45) NULL,
                address_id integer NULL
            );"
        );

    });

});