<?php

use Haijin\Persistency\Engines\Elasticsearch\Query_Builder\Elasticsearch_Filter_Builder;
use Haijin\Persistency\Statement_Compiler\Create_Statement_Compiler;
use Haijin\Persistency\Statement_Compiler\Update_Statement_Compiler;
use Haijin\Persistency\Statement_Compiler\Delete_Statement_Compiler;
use Haijin\Persistency\Statement_Compiler\Query_Statement_Compiler;

$spec->describe( "When adding extra parameters to a statement", function() {

    $this->it( "makes the extra parameters available on a Create_Statement", function() {

        $compiled_query = ( new Create_Statement_Compiler() )->compile( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->match_all()
            );

            $query->extra_parameters(
                [ 1, 2, 3 ]
            );

        });

        $this->expect( $compiled_query->get_extra_parameters() )
            ->to() ->equal( [ 1, 2, 3 ] );

    });

    $this->it( "makes the extra parameters available on an Update_Statement", function() {

        $compiled_query = ( new Update_Statement_Compiler() )->compile( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->match_all()
            );

            $query->extra_parameters(
                [ 1, 2, 3 ]
            );

        });

        $this->expect( $compiled_query->get_extra_parameters() )
            ->to() ->equal( [ 1, 2, 3 ] );

    });

    $this->it( "makes the extra parameters available on an Delete_Statement", function() {

        $compiled_query = ( new Delete_Statement_Compiler() )->compile( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->match_all()
            );

            $query->extra_parameters(
                [ 1, 2, 3 ]
            );

        });

        $this->expect( $compiled_query->get_extra_parameters() )
            ->to() ->equal( [ 1, 2, 3 ] );

    });

    $this->it( "makes the extra parameters available on an Query_Statement", function() {

        $compiled_query = ( new Query_Statement_Compiler() )->compile( function($query) {

            $query->collection( "users_read_only" );

            $query->filter(
                $query->match_all()
            );

            $query->extra_parameters(
                [ 1, 2, 3 ]
            );

        });

        $this->expect( $compiled_query->get_extra_parameters() )
            ->to() ->equal( [ 1, 2, 3 ] );

    });

});