<?php

$migration->definition( function() {

    $this->id = 1;

    $this->name = "Create users tables";

    $this->describe( "Create the users Postgresql table", function($database) {

        $database->evaluate_sql_string(
            "invalid script"
        );

    });

});