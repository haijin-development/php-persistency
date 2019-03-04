<?php

use Haijin\Debugger;

require_once __DIR__ . "/mysql-methods.php";
require_once __DIR__ . "/postgresql-methods.php";
require_once __DIR__ . "/sqlite-methods.php";
require_once __DIR__ . "/elasticsearch-methods.php";

require_once __DIR__ . "/sample-models/User.php";
require_once __DIR__ . "/sample-models/Address.php";
require_once __DIR__ . "/sample-models/Record_With_Types.php";
require_once __DIR__ . "/sample-models/Users_Collection.php";
require_once __DIR__ . "/sample-models/Addresses_Collection.php";
require_once __DIR__ . "/sample-models/Elasticsearch_Users_Collection.php";
require_once __DIR__ . "/sample-models/Types_Collection.php";
require_once __DIR__ . "/sample-models/Elasticsearch_Types_Collection.php";

\Haijin\Specs\Specs_Runner::configure( function($specs) {

    Mysql_Methods::add_to( $this );
    Postgresql_Methods::add_to( $this );
    Sqlite_Methods::add_to( $this );
    Elasticsearch_Methods::add_to( $this );

    $this->before_all( function() {

        $this->setup_mysql();
        $this->setup_postgresql();
        $this->setup_sqlite();
        $this->setup_elasticsearch();

    });

    $this->after_all( function() {

        $this->tear_down_mysql();
        $this->tear_down_postgresql();
        $this->tear_down_sqlite();
        $this->tear_down_elasticsearch();

    });

});

function inspect($object)
{
    Debugger::inspect( $object );
}