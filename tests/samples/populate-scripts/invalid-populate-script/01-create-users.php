<?php

$populate_scripts->definition( function() {

    $this->id = 1;

    $this->name = "Create users";

    $this->describe( "Create a user", function() {

        throw new \Exception( 'Error from populate script' );

    });

});