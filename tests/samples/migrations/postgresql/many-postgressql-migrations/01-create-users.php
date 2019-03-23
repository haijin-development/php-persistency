<?php

$migration->definition( function() {

    $this->id = 1;

    $this->name = "Create users tables";

    $this->describe( "Create the users Postgresql table", function($database) {

        $database->evaluate_sql_string(
            "CREATE TABLE users (
                id SERIAL PRIMARY KEY,
                name varchar(45) NULL,
                last_name varchar(45) NULL,
                address_id integer NULL
            );"
        );

    });

});