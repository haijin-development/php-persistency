<?php


$migration->definition( function() {

    $this->id = 2;

    $this->name = "Create products tables";

    $this->describe( "Create the products Sqlite table", function($mysql_database) {

        $mysql_database->evaluate_sql_string(
            "CREATE TABLE IF NOT EXISTS `products` (
                `id` INTEGER PRIMARY KEY,
                `name` VARCHAR(100) NULL,
                `description` VARCHAR(45) NULL,
                `last_name` VARCHAR(45) NULL,
                `password_hash` VARCHAR(255) NULL
            );"
        );

    });

});