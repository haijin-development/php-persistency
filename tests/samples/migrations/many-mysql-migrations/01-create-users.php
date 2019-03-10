<?php

use Haijin\Persistency\Engines\Mysql\Mysql_Database;

$migration->definition( function() {

    $this->id = 1;

    $this->name = "Create users tables";

    $this->describe( "Create the users Mysql table", function($mysql_database) {

        $mysql_database->evaluate_sql_string(
            "CREATE TABLE IF NOT EXISTS `users` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `email` VARCHAR(100) NULL,
                `name` VARCHAR(45) NULL,
                `last_name` VARCHAR(45) NULL,
                `password_hash` VARCHAR(255) NULL,
                PRIMARY KEY (`id`)
            );"
        );

    });

});