<?php

require_once __DIR__ . "/mysql-methods.php";
require_once __DIR__ . "/sqlite-methods.php";

\Haijin\Specs\Specs_Runner::configure( function($specs) {

    Mysql_Methods::add_to( $this );
    Sqlite_Methods::add_to( $this );

    $this->before_all( function() {

        $this->setup_mysql();
        $this->setup_sqlite();

    });

    $this->after_all( function() {

        $this->tear_down_mysql();
        $this->tear_down_sqlite();

    });

});