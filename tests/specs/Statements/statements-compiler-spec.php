<?php

use Haijin\Persistency\Statement_Compiler\Compiler;
use Haijin\Persistency\Sql\Sql_Query_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Create_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Update_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Delete_Statement_Builder;

$spec->describe( "", function() {

    $this->let( "compiler", function() {
        return new Compiler();
    });

    $this->it( 'Compiles a query in several calls', function() {

        $statement = $this->compiler->query( function($query) {

            $query->collection( "users" );

        });

        $statement = $this->compiler->query( function($query) {

            $query->filter(
                $query->value( 1 )
            );

        });

        $sql = ( new Sql_Query_Statement_Builder() )->build_sql_from( $statement );

        $this->expect( $sql ) ->to() ->equal(
            "select users.* from users where 1;"
        );

    });

    $this->it( 'Compiles a create query in several calls', function() {

        $statement = $this->compiler->create( function($query) {

            $query->collection( "users" );

        });

        $statement = $this->compiler->create( function($query) {

            $query->record(
                $query->set( 'name', 'Lisa' )
            );

        });

        $sql = ( new Sql_Create_Statement_Builder() )->build_sql_from( $statement );

        $this->expect( $sql ) ->to() ->equal(
            "insert into users (name) values ('Lisa');"
        );

    });

    $this->it( 'Compiles an update query in several calls', function() {

        $statement = $this->compiler->update( function($query) {

            $query->collection( "users" );

        });

        $statement = $this->compiler->update( function($query) {

            $query->record(
                $query->set( 'name', 'Lisa' )
            );

        });

        $sql = ( new Sql_Update_Statement_Builder() )->build_sql_from( $statement );

        $this->expect( $sql ) ->to() ->equal(
            "update users set name = 'Lisa';"
        );

    });

    $this->it( 'Compiles a delete query in several calls', function() {

        $statement = $this->compiler->delete( function($query) {

            $query->collection( "users" );

        });

        $statement = $this->compiler->delete( function($query) {

            $query->filter(
                $query->value( 1 )
            );

        });

        $sql = ( new Sql_Delete_Statement_Builder() )->build_sql_from( $statement );

        $this->expect( $sql ) ->to() ->equal(
            "delete from users where 1;"
        );

    });

});