<?php

$migration->definition( function() {

    $this->id = 1;

    $this->describe( "Create the users Mysql table", function($database) {

        $database->evaluate_sql_string(
            "invalid script"
        );

    });

});