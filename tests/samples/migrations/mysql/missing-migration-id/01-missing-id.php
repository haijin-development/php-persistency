<?php

$migration->definition( function() {

    $this->name = "Create users tables";

    $this->describe( "Create the users Mysql table", function($database) {

        $database->evaluate_sql_string(
            "invalid script"
        );

    });

});