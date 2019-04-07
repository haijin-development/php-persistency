<?php

use Haijin\Persistency\Populate_Scripts\Populate_Scripts_Evaluator;

$spec->describe( "When configuring the populate scripts", function() {

    $this->it( "sets and gets the configuration", function() {

        $populate_scripts_evaluator = new Populate_Scripts_Evaluator();

        $populate_scripts_evaluator->configure( function($config) {

            $config->folder = 'folder';

            $this->folder = $config->folder;

        });

        $this->expect( $this->folder ) ->to()
            ->equal( 'folder' );

    });

    $this->it( "configures the Populate_Scripts_Evaluator", function() {

        $populate_scripts_evaluator = new Populate_Scripts_Evaluator();

        $populate_scripts_evaluator->configure( function($config) {

            $config->folder = 'folder';

        });

        $this->expect( $populate_scripts_evaluator->get_scripts_folder() ) ->to()
            ->equal( 'folder' );

    });

});