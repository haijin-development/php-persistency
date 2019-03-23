<?php

use Haijin\Persistency\Sql\Sql_Query_Statement_Builder;
use Haijin\Persistency\Statement_Compiler\Query_Statement_Compiler;
use Haijin\Persistency\Errors\Query_Expressions\Invalid_Expression_Error;

$spec->describe( "When building a join with a collection statement", function() {

    $this->let( "query_builder", function() {
        return new Sql_Query_Statement_Builder();
    });

    $this->describe( "when joining with a reference_to_object field", function() {

        $this->it( "builds a left outer join", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "address_id" ) ->eval( function($query) {

                    $query->proyect(
                        $query->ignore()
                    );

                });

            });

            $expected_sql = 
                "select users.* " .
                "from users " .
                "left outer join addresses on users.address_id = addresses.id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds an aliased join", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "address_id" ) ->as( "a" );

            });

            $expected_sql = 
                "select users.*, a.* " .
                "from users " .
                "left outer join addresses as a on users.address_id = a.id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds multiple joins", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "address_id" ) ->as( "a_1" );
                $query->with( "address_id" ) ->as( "a_2" );

            });

            $expected_sql = 
                "select users.*, a_1.*, a_2.* " .
                "from users " .
                "left outer join addresses as a_1 on users.address_id = a_1.id " .
                "left outer join addresses as a_2 on users.address_id = a_2.id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds join proyections", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->proyect(
                    $query->field( "name" ),
                    $query->field( "last_name" )
                );

                $query->with( "address_id" ) ->eval( function($query) {
                    $query->proyect(
                        $query->field( "street" ),
                        $query->field( "number" )
                    );
                });

            });

            $expected_sql = 
                "select users.name, users.last_name, addresses.street, addresses.number " .
                "from users " .
                "left outer join addresses on users.address_id = addresses.id;";


            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds macro expressions within join expressions", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "address_id" ) ->eval( function($query) {

                    $query->let( "matches_street", function($query) {
                        return $query ->field( "street", "=", "Evergreen" );
                    });

                });

                $query->filter(
                    $query ->matches_street
                );

            });

            $expected_sql = 
                "select users.*, addresses.* " .
                "from users " .
                "left outer join addresses on users.address_id = addresses.id " .
                "where addresses.street = 'Evergreen';";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds nested joins", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Addresses_2_Collection::get() );

                $query->collection( "addresses" );

                $query->with( "user_id" ) ->eval( function($query) {

                    $query->with( "address_id" ) ->as( 'a' ) ->eval( function($query) {

                        $query->let( "matches_street", function($query) {
                            return $query ->field( "street", "=", "Evergreen" );
                        });

                    });

                });

                $query->filter(
                    $query ->matches_street
                );

            });

            $expected_sql = 
                "select addresses.*, users.*, a.* " .
                "from addresses " .
                "left outer join users on addresses.user_id = users.id " .
                "left outer join addresses as a on users.address_id = a.id " .
                "where a.street = 'Evergreen';";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

    });

    $this->describe( "when joining with a reference_from_object field", function() {

        $this->it( "builds a left outer join", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "address_2" ) ->eval( function($query) {
                    $query->proyect(
                        $query->ignore()
                    );
                });

            });

            $expected_sql = 
                "select users.* " .
                "from users " .
                "left outer join addresses on users.id = addresses.user_id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds an aliased join", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "address_2" ) ->as( 'a' ) ->eval( function($query) {
                    $query->proyect(
                        $query->ignore()
                    );
                });

            });

            $expected_sql = 
                "select users.* " .
                "from users " .
                "left outer join addresses as a on users.id = a.user_id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds multiple joins", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "address_2" ) ->as( 'a_1' );
                $query->with( "address_2" ) ->as( 'a_2' );

            });

            $expected_sql = 
                "select users.*, a_1.*, a_2.* " .
                "from users " .
                "left outer join addresses as a_1 on users.id = a_1.user_id " .
                "left outer join addresses as a_2 on users.id = a_2.user_id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds join proyections", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->proyect(
                    $query->field( "name" ),
                    $query->field( "last_name" )
                );

                $query->with( "address_2" ) ->eval( function($query) {
                    $query->proyect(
                        $query->field( "street" ),
                        $query->field( "number" )
                    );
                });

            });

            $expected_sql = 
                "select users.name, users.last_name, addresses.street, addresses.number " .
                "from users " .
                "left outer join addresses on users.id = addresses.user_id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds macro expressions within join expressions", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "address_2" ) ->eval( function($query) {

                    $query->let( "matches_street", function($query) {
                        return $query ->field( "street", "=", "Evergreen" );
                    });

                });

                $query->filter(
                    $query ->matches_street
                );

            });

            $expected_sql = 
                "select users.*, addresses.* " .
                "from users " .
                "left outer join addresses on users.id = addresses.user_id " .
                "where addresses.street = 'Evergreen';";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds nested joins", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "address_2" ) ->eval( function($query) {

                    $query->with( "user_id" ) ->as( 'u' ) ->eval( function($query) {

                        $query->with( "address_2" ) ->as( 'a' ) ->eval( function($query) {

                            $query->let( "matches_street", function($query) {
                                return $query ->field( "street", "=", "Evergreen" );
                            });

                        });

                    });


                });

                $query->filter(
                    $query ->matches_street
                );

            });

            $expected_sql = 
                "select users.*, addresses.*, u.*, a.* " .
                "from users " .
                "left outer join addresses on users.id = addresses.user_id " .
                "left outer join users as u on addresses.user_id = u.id " .
                "left outer join addresses as a on u.id = a.user_id " .
                "where a.street = 'Evergreen';";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

    });

    $this->describe( "when joining with a reference_from_collection field", function() {

        $this->it( "builds a left outer join", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "all_addresses" ) ->eval( function($query) {
                    $query->proyect(
                        $query->ignore()
                    );
                });

            });

            $expected_sql = 
                "select users.* " .
                "from users " .
                "left outer join addresses on users.id = addresses.user_id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds an aliased join", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "all_addresses" ) ->as( 'a' ) ->eval( function($query) {
                    $query->proyect(
                        $query->ignore()
                    );
                });

            });

            $expected_sql = 
                "select users.* " .
                "from users " .
                "left outer join addresses as a on users.id = a.user_id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds multiple joins", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "all_addresses" ) ->as( 'a_1' );
                $query->with( "all_addresses" ) ->as( 'a_2' );

            });

            $expected_sql = 
                "select users.*, a_1.*, a_2.* " .
                "from users " .
                "left outer join addresses as a_1 on users.id = a_1.user_id " .
                "left outer join addresses as a_2 on users.id = a_2.user_id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds join proyections", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->proyect(
                    $query->field( "name" ),
                    $query->field( "last_name" )
                );

                $query->with( "all_addresses" ) ->eval( function($query) {
                    $query->proyect(
                        $query->field( "street" ),
                        $query->field( "number" )
                    );
                });

            });

            $expected_sql = 
                "select users.name, users.last_name, addresses.street, addresses.number " .
                "from users " .
                "left outer join addresses on users.id = addresses.user_id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds macro expressions within join expressions", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "all_addresses" ) ->eval( function($query) {

                    $query->let( "matches_street", function($query) {
                        return $query ->field( "street", "=", "Evergreen" );
                    });

                });

                $query->filter(
                    $query ->matches_street
                );

            });

            $expected_sql = 
                "select users.*, addresses.* " .
                "from users " .
                "left outer join addresses on users.id = addresses.user_id " .
                "where addresses.street = 'Evergreen';";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->it( "builds nested joins", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "all_addresses" ) ->eval( function($query) {

                    $query->with( "user_id" ) ->as( 'u' ) ->eval( function($query) {

                        $query->with( "all_addresses" ) ->as( 'a' ) ->eval( function($query) {

                            $query->let( "matches_street", function($query) {
                                return $query ->field( "street", "=", "Evergreen" );
                            });

                        });

                    });


                });

                $query->filter(
                    $query ->matches_street
                );

            });

            $expected_sql = 
                "select users.*, addresses.*, u.*, a.* " .
                "from users " .
                "left outer join addresses on users.id = addresses.user_id " .
                "left outer join users as u on addresses.user_id = u.id " .
                "left outer join addresses as a on u.id = a.user_id " .
                "where a.street = 'Evergreen';";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

    });

    $this->describe( "when joining with a reference_from_collection field", function() {

        $this->it( "builds a left outer join", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "all_indirect_addresses" ) ->eval( function($query) {
                    $query->proyect(
                        $query->ignore()
                    );
                });

            });

            $expected_sql = 
                "select users.* " .
                "from users " .
                "left outer join users_addresses on users.id = users_addresses.user_id " .
                "left outer join addresses on users_addresses.address_id = addresses.id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

    });

    $this->it( "raises an error if the meta model is not present", function() {

        $this->expect( function() {

            $this->query_builder->build( function($query) {

                $query->collection( "users" );

                $query->with( "address_id" );

            });

        }) ->to() ->raise(
            Invalid_Expression_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->equal(
                    "Trying to use '\$query->with( \"address_id\" )' without setting a '\$query->set_meta_model( \$persistent_collection );' first."
                );

                $this->expect( $error->get_expression() ) ->to()
                    ->be() ->a( Query_Statement_Compiler::class );
        });

    });

    $this->it( "raises an error if the field does not exist", function() {

        $this->expect( function() {

            $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "street" );

            });

        }) ->to() ->raise(
            Invalid_Expression_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->equal(
                    "Field mapping at field 'street' in class Users_2_Persistent_Collection not found."
                );
        });

    });

    $this->it( "raises an error if the field to join with is not a relational field", function() {

        $this->expect( function() {

            $this->query_builder->build( function($query) {

                $query->meta_model( Users_2_Collection::get() );

                $query->collection( "users" );

                $query->with( "name" );

            });

        }) ->to() ->raise(
            Invalid_Expression_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->equal(
                    "Trying to use '\$query->with( \"name\" )' with a no relational field type."
                );
        });

    });

});