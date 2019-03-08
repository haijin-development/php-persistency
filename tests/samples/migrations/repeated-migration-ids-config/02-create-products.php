<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;

$migration->definition( function() {

    $this->id = 1;

    $this->name = "Create products tables";

    $this->describe( "Create the products Mysql table", function($mysql_database) {

        $mysql_database->evaluate_sql_string(
            "CREATE TABLE IF NOT EXISTS `products` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NULL,
                `description` VARCHAR(45) NULL,
                `last_name` VARCHAR(45) NULL,
                `password_hash` VARCHAR(255) NULL,
                PRIMARY KEY (`id`)
            );"
        );

    });

});