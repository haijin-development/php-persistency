<?php

$populate_scripts->definition( function() {

    $this->id = 1;

    $this->describe( "Create a user", function() {

        if( ! Users_Collection::do()->exists_with([ 'name' => 'Lisa' ]) ) {

            Users_Collection::do()->create_from_attributes([
                'name' => 'Lisa',
                'last_name' => 'Simpson'
            ]);

        }

    });

});