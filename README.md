# Haijin Persistency

A simple, complete, adaptable and independent query builder and ORM for PHP, loosely inspired by Ruby's [Hanami](https://guides.hanamirb.org/) [model](https://github.com/hanami/model).

[![Latest Stable Version](https://poser.pugx.org/haijin/persistency/version)](https://packagist.org/packages/haijin/persistency)
[![Latest Unstable Version](https://poser.pugx.org/haijin/persistency/v/unstable)](https://packagist.org/packages/haijin/persistency)
[![Build Status](https://travis-ci.org/haijin-development/php-persistency.svg?branch=v0.0.2)](https://travis-ci.org/haijin-development/php-persistency)
[![License](https://poser.pugx.org/haijin/persistency/license)](https://packagist.org/packages/haijin/persistency)

### Version 0.0.3

This library is under active development and no stable version was released yet.

If you like it a lot you may contribute by [financing](https://github.com/haijin-development/support-haijin-development) its development.

### Highlights

#### Uses simple PHP methods to persist and query objects:

```php
Users_Collection::do()->create( $user );
Users_Collection::do()->update( $user );
Users_Collection::do()->delete( $user );

Users_Collection::get()->all_sorted_by_name();
```

#### Does not require persisted models to extend from any class, implement any protocol nor follow any convention.

Usually model classes must extend from a super `class`, use a `trait`, implement an `interface` of comply with some conventions (like the name of its getters and setters) to be persisted.

In `haijin/persistency` almost any object can be persisted without requiring any modification on it:

```php
/**
 * A Persistent_Collection to persist User objects.
 */
class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            // or

            $mapping->field( "id" ) ->is_primary_key()
                ->read_with( "->id" )
                ->write_with( "->id" );

            // or even

            $mapping->field( "id" ) ->is_primary_key()
                ->read_with( "[id]" )
                ->write_with( "[id]" );

        };
    }
}
```

#### Shares a common query language between sql and no sql databases to persist and query objects.

Sql databases, like `Mysql`, `Postgres` and `Sqlite`, and no sql databases, like `Elasticsearch` and `MongoDb`, have very different APIs.

However most of the existing databases share a common query structure.

'haijin/persistency' shares the basic query structure among sql and no sql databases.

The following query works as it is in `Mysql`, `Postgres`, `Sqlite` and `Elasticsearch`:

```php
$database->create( function($query) {

    $query->collection( 'users' );

    $query->record(
        $query->set( 'id', 1 ),
        $query->set( 'name', 'Lisa' ),
        $query->set( 'last_name', 'Simpson' )
    );

});
```

The following query works in both sql databases and `Elasticsearch` and the only difference among them would be the `filter` expression:

```php
$records = $database->query( function($query) {

    $query->collection( 'users' );

    $query->proyect(
        $query->field( 'id' ),
        $query->field( 'name' )
    );

    $query->filter( ... );

    $query->order_by(
        $query->field( 'name' ) ->desc(),
        $query->field( 'id' ) ->desc(),
    );

});
```

#### Uses a highly expressive and adaptable query language that mimics each database engine, sql and no sql, query structure.

Most query builders usually compare a table field against a value. More complex filters are achieved by executing raw sql strings.

Query filters usually look like this:

```php
$users = table( 'users' )
    ->whereIn( 'name', ['Lisa', 'Bart', 'Maggie'] )
    ->orWhere( 'last_name', '=', 'Simpson' );
```

`hajin/persistency` expresses the same query like this:

```php
$users = Users_Collection::get()->all( function($query) {

    $query->filter(
        $query
            ->field( 'name' ) ->in( ['Lisa', 'Bart', 'Maggie'] )
            ->or()
            ->field( 'last_name' ) ->op( '=' ) ->value( 'Simpson' )
    );

});
``` 

At first glance the query structure may seem too verbose and explicit compared to other query languages, but with this explicitness comes a subtle but important advantage: it reduces the gap between the query the developer wants to write and the expression that implements it. The effort that the developer does to translate the query she wants to express in the database language to the query expression in the application language is minimum to all databases, sql and no sql.

It also has an additional and equally important feature: it allows to easily express arbitrarily complex queries involving any field, value and built-in function in `where`, `select`, `order by` or `group by` clauses without concatenanting strings.

For instance the following sql expression:

```sql
SELECT *
FROM users
WHERE lower( concat( users.name, ' ', users.last_name ) ) = lower( 'Lisa Simpson' ) AND users.address LIKE '%Evergreen%'
```

can be expressed almost without modifications like this:

```php
$users = Users_Collection::get()->all( function($query) {

    $query->filter(
        $query
            ->lower(
                ->concat(
                    $query->field( 'name' ), ' ', $query->field( 'last_name' )
                )
            )
            ->op( '=' )
            ->lower( 'Lisa Simpson' )

            ->and()

            ->field( 'address' ) ->op( 'like' ) ->value( '%Evergreen' )
    );

});
```

Elasticsearch filters are very different from sql ones. Instead of building an abstraction layer for both sql and no sql databases `haijin/persistency` uses a dynamic mechanism to allow to express any built-in function of each database.

An Elasticsearch filter might look like this:

```php
$users = Users_Collection::get()->all( function($query) {

    $query->filter(
        $query->bool(
            $query->should(
                $query->match( $query->field( 'name' ), 'Lisa' ),
                $query->match( $query->field( 'last_name' ), 'Simpson' )
            )
        )
    );

});
```

#### Adds a bit of sintactic sugar to improve expressiveness.

These are valid expressions:

```php
$query->field( 'name' ) ->upper()
$query->field( 'name' ) ->not_null()
$query->field( 'name' ) ->match( 'Lisa' )
$query->field( 'name', '=', 'Lisa' )
```

equivalent to:

```php
$query->upper( $query->field( 'name' ) )
$query->not_null( $query->field( 'name' ) )
$query->match( $query->field( 'name' ), 'Lisa' )
$query->field( 'name' ) ->op( '=') ->value( 'Lisa' )
```

#### Allows to define expression macros with significant semantic names to simplify complex queries and improve expressiveness

Complex SQL queries tend not to be easily read and understood.

Using `let` macros it is possible to divide complex queries into more simple and meaningful named expressions and later combine them with logical operands:

```php
$users = Users_Collection::get()->all( function($query) {

    $query->let( 'equals_name_and_last_name', function($query) {
        return $query
                ->concat(
                    $query->field( 'name' ), ' ', $query->field( 'last_name' )
                ) ->lower()

                ->op( '=' )

                ->lower( 'Lisa Simpson' );
    });

    $query->let( 'matches_address', function($query) {
        return $query
                ->field( 'address', 'like', '%Evergreen' );
    });

    $query->filter(
        $query
            ->equals_name_and_last_name ->and() ->matches_address
    );

});
```

#### Allows to inline query parameters as if they were constant values or to provide them as named paramaters.

Although it might seem from the previous examples that the query values are appended as constant strings to the query, internally they are stored as query parameters that are bound to the query as any other parameters.

So the previous example is equivalent to:

```php
$full_name = 'Lisa Simpson';
$address = '%Evergreen';

$users = Users_Collection::get()->all( function($query) use($full_name, $address) {

    $query->let( 'equals_name_and_last_name', function($query) {
        return $query
                ->concat(
                    $query->field( 'name' ), ' ', $query->field( 'last_name' )
                ) ->lower()

                ->op( '=' )

                ->lower( $full_name );
    });

    $query->let( 'matches_address', function($query) {
        return $query
                ->field( 'address', 'like', $address );
    });

    $query->filter(
        $query
            ->equals_name_and_last_name ->and() ->matches_address
    );

});
```

Instead of inlining the values in the query it is possible to explicitly parametrize them:


```php
$full_name = 'Lisa Simpson';
$address = '%Evergreen';

$users = Users_Collection::get()->all( function($query) use($full_name, $address) {

    $query->let( 'equals_name_and_last_name', function($query) {
        return $query
                ->concat(
                    $query->field( 'name' ), ' ', $query->field( 'last_name' )
                ) ->lower()

                ->op( '=' )

                ->lower( $query->param( 'full_name' ) );
    });

    $query->let( 'matches_address', function($query) {
        return $query
                ->field( 'address', 'like', $query->param( 'address' ) );
    });

    $query->filter(
        $query
            ->equals_name_and_last_name ->and() ->matches_address
    );

}, [ 'full_name' => $full_name, 'address' => $address ] );
```

#### Allows to ignore null parameters.

Usually some query parameters are optional and should be ignored if absent.

For instance when querying for a `full_name` and `address` if both are provided both should match, but if one or both of them are absent they should not be queried for:

```php
$users = Users_Collection::get()->all( function($query) use($full_name, $address) {

    if( $full_name !== null || $address !== null ) {
        if( $full_name === null ) {
            $query->filter(
                $query ->matches_address
            );
        } elseif( $address === null ) {
            $query->filter(
                $query ->equals_name_and_last_name
            );
        } else {
            $query->filter(
                $query
                    ->equals_name_and_last_name ->and() ->matches_address
            );
        }
    }

});
```

This approach has an exponential growth in the number of optional parameters and is not elegant.

Other approaches like using `ifs` to conditionaly concatenante strings or expressions if a parameter is present or not are better but are still not elegant.

In `haijin/persistency` is possible to tell the query to ignore a term in boolean conditions and function calls, greatly improving the expresiveness of the query and making its logic a lot more simple:

```php
$users = Users_Collection::get()->all( function($query) use($full_name, $address) {

    $query->let( 'equals_name_and_last_name', function($query) {

        if( $full_name === null ) {
            return $this->ignore();
        }

        return $query
                ->concat(
                    $query->field( 'name' ), ' ', $query->field( 'last_name' )
                ) ->lower()

                ->op( '=' )

                ->lower( $full_name );

    });

    $query->let( 'matches_address', function($query) {

        if( $address === null ) {
            return $this->ignore();
        }

        return $query
                ->field( 'address', 'like', $address );

    });

    $query->filter(
        $query
            ->equals_name_and_last_name ->and() ->matches_address
    );

});
```

#### Allows any nested amount of joins.

When joining with another tables it is possible to define, within the joined table, which fields of the joined table to proyect in the select clause and macro expressions.

This way the proyected fields and filter expressions are defined in the scope of the proper table and the query builder can correctly resolve the field namespace, improving the expresiveness.

Nesting joins like this also gives the developer reading the query a much more clear and simple view of which tables are being joined to which tables than if they were expressed in a flattened, one level expression.


```php
$users = Users_Collection::get()->all( function($query) use($full_name, $address) {

    $query->proyect(
        $query->field( 'id' ),
        $query->field( 'name' )
    );

    $query->join( "address" ) ->from( "id" ) ->to( "user_id" )
                                        ->eval( function($query) use($address) {

        $query->proyect(
            $query->ignore()
        );

        $query->let( 'matches_address', function($query) {

            if( $address === null ) {
                return $this->ignore();
            }

            return $query
                    ->field( 'street_name', 'like', $address );

        });

    });

    $query->let( 'equals_name_and_last_name', function($query) {

        if( $full_name === null ) {
            return $this->ignore();
        }

        return $query
                ->concat(
                    $query->field( 'name' ), ' ', $query->field( 'last_name' )
                ) ->lower()

                ->op( '=' )

                ->lower( $full_name );

    });

    $query->filter(
        $query
            ->equals_name_and_last_name ->and() ->matches_address
    );

});
```

#### Allows to define all queries related to a model in its `Persistent_Collection` improving expressiveness and ease of use.

Usually query builders allow to reuse and share commonly used queries by defining partial or full queries in the model class. This has the drawback of coupling a query to a model. Every time a context requires the model not to use a query defined in the model some [additional statement](https://guides.rubyonrails.org/active_record_querying.html#unscope) must be included in the query making it less expressive and harder to read and to understand for developers. This statement is merely an implementation need, not actually related to the semantics of the model.

In `haijin/persistency` each query on a model can be a method of some particular `Persistent_Collection`, greatly improving ease of use and cleary expressing the semantics of the query:


```php
$users = Users_Collection::get()->all_matching_full_name_and_address(
        'Lisa Simpson',
        '%Evergreen%'
    );
```

where `all_matching_full_name_and_address` is defined in the `Users_Persistent_Collection`, not in the `User` class:

```php
/**
 * A Persistent_Collection to persist User objects.
 */
class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            /// ...
        };
    }

    public function all_matching_full_name_and_address($full_name, $address)
    {
        return $this->all( function($query) use($full_name, $address) {

            $query->proyect(
                $query->field( 'id' ),
                $query->field( 'name' )
            );

            $query->join( "address" ) ->from( "id" ) ->to( "user_id" )
                                        ->eval( function($query) use($address) {

                $query->proyect(
                    $query->ignore()
                );

                $query->let( 'matches_address', function() {

                    if( $address === null ) {
                        return $this->ignore();
                    }

                    return $query
                            ->field( 'street_name', 'like', $address );

                });

            });

            $query->let( 'equals_name_and_last_name', function() {

                if( $full_name === null ) {
                    return $this->ignore();
                }

                return $query
                        ->concat(
                            $query->field( 'name' ), ' ', $query->field( 'last_name' )
                        ) ->lower()

                        ->op( '=' )

                        ->lower( $full_name );

            });

            $query->filter(
                $query
                    ->equals_name_and_last_name ->and() ->matches_address
            );

        });
    }
}
```

Another drawback of defining the query scopes in the model class is that it couples the model to a particular database. With that approach it is not possible to persist the same model in two or more different databases even if they are all sql databases.

Here's an example from Ruby's [ActiveRecord scopes](https://guides.rubyonrails.org/active_record_querying.html#passing-in-arguments).

The functions `lowercase` and `concat` are defined in `Mysql` and `Postgres` so the following scope would work on them:

```ruby
class User < ApplicationRecord
    scope :matches_full_name, ->(full_name) {
        where( "lowercase( concat( name, ' ', last_name )  ) = ?", full_name )
    }
end
```

But `Sqlite` does not implement `concat` and the `lowercase` function is called `lower`, so another scope implementation should be used instead:

```ruby
class User < ApplicationRecord
    scope :matches_full_name, ->(full_name) {
        where( "lower( printf( '%s %s', name, last_name ) ) = ?", full_name )
    }
end
```

The problem is that both implementations can not coexist in the same application.

In `haijin/persistency` each query is defined in a particular `Persistent_Collection` in the context of a model *and* a database, allowing to persist the same model in more than one database.

Here's an intereseting real scenario of such a feature:

```php
/**
 * Persists the user in the database and index it in Elasticsearch.
 */
function create_user($user)
{
    Users_Collection::do()->create( $user );
    Elasticsearch_Users_Collection::do()->create( $user );
}
```

Another use case might be the use of a database for regular persistency and a redundant database to perform heavy queries without relying on the database configuration, and both database can even be different persistency engines like Mysql for real persistency and Sqlite for cached queries:

```php
/**
 * Persists the user in the main database and in a redundant, read only database for
 * slow, heavy queries.
 */
function create_user($user)
{
    Users_Collection::do()->create( $user );
    Read_Only_Users_Collection::do()->create( $user );
}


// In a context the application performs a heavy query on the users table so it uses
// the read only redundant database instead of the regular one.
$users = Read_Only_Users_Collection::do()->find_users_with_some_heavy_query();


// In a different context performs a regular query on the users table.
$users = Read_Only_Users_Collection::do()->find_users_with_a_regular_query();
```


The model is the same all over the application and it's the context using the model the one that defines which queries and scopes to use with a clear and simple statement by choosing one `Persistent_Collection` or another one.


#### Allows to specify an arbitrary depth of nested eager fetches on each query call.

Usually the specification of which related entities to fetch eagerly is done on the query definition side.

`haijin/persistency` allows the caller of a query, instead of the query definition, to easily specify which entities to fetch eagerly, since the same query can be used in different contexts requiring different entities to be eagerly fetched:

```php
$users = Users_Collection::get()->all_users_sorted_by_name([
    'address' => true,
    'books' => [
        'author' => true
    ]
]);
```

#### Allows complex testing without mocking the database.

Usually the setup of the database for testing is complicated and unclear.

Fixtures are defined in a different file than the test, making it unclear for the developer what does the database contain.

Mocking the database, or any other object, does not execirse the real application so we discourage to use mocks.

`haijin/persistency` allows to easily and clearly populate in each test, in the same test file, the database:

```php
$spec->describe( "When calling the delete user endpoint", function() {

    $this->before_each( function() {

        Users_Collection::do()->clear_all();

        Users_Collection::do()->create_from_attributes([
            'id' => 1,
            'name' => 'Lisa',
            'last_name' => 'Simpson'
        ]);

        Users_Collection::do()->create_from_attributes([
            'id' => 2,
            'name' => 'Bart',
            'last_name' => 'Simpson'
        ]);

        Users_Collection::do()->create_from_attributes([
            'id' => 3,
            'name' => 'Maggie',
            'last_name' => 'Simpson'
        ]);

    });

    $this->after_all( function() {
        Users_Collection::do()->clear_all();
    });

    $this->it( "deletes the user", function() {

        $this->make_request( 'DELETE', '/users/2' );

        $users = Users_Collection::get()->all();

        $this->expect( $users ) ->to() ->be() ->like([
            [
                'get_id()' => 1,
                'name' => 'Lisa',
                'last_name' => 'Simpson'
            ],
            [
                'get_id()' => 3,
                'name' => 'Maggie',
                'last_name' => 'Simpson'
            ]
        ])
    });

});
```

## Table of contents

1. [Installation](#c-1)
2. [Usage](#c-2)
    1. [Querying a database](#c-2-1)
        1. [A direct query to a database](#c-2-1-1)
        2. [Using semantic expressions](#c-2-1-2)
        3. [Using named parameters](#c-2-1-3)
        4. [Calling query functions](#c-2-1-4)
        5. [Debugging the query](#c-2-1-5)
        6. [Creating records in a database](#c-2-1-6)
        7. [Updating records in a database](#c-2-1-7)
        8. [Deleting records in a database](#c-2-1-8)
        9. [Transactions](#c-2-1-9)
        10. [Implemented databases](#c-2-1-10)
    2. [Mapping objects](#c-2-2)
        1. [Models](#c-2-2-1)
        2. [Persistent_Collections](#c-2-2-2)
        3. [Persistent_Collection singleton pattern](#c-2-2-3)
        4. [Persistent_Collection definition](#c-2-2-4)
            1. [database](#c-2-2-4-1)
            2. [collection_name](#c-2-2-4-2)
            3. [objects_instantiator](#c-2-2-4-3)
                1. [class instantiator](#c-2-2-4-3-1)
                2. [callable instantiator](#c-2-2-4-3-2)
                3. [null instantiator](#c-2-2-4-3-3)
            4. [field_mappings](#c-2-2-4-4)
                1. [field](#c-2-2-4-4-1)
                2. [is_primary_key](#c-2-2-4-4-2)
                3. [type](#c-2-2-4-4-3)
                4. [read_with](#c-2-2-4-4-4)
                5. [write_with](#c-2-2-4-4-5)
                6. [reference_to($persistent_collection)](#c-2-2-4-4-6)
                7. [reference_from($persistent_collection, $id_field)](#c-2-2-4-4-7)
                8. [reference_collection_from($persistent_collection, $id_field)](#c-2-2-4-4-8)
                9. [reference_collection_through($middle_table, $left_id_field, $right_id_field, $other_collection)](#c-2-2-4-4-9)
        5. [Creating objects](#c-2-2-5)
            1. [create](#c-2-2-5-1)
            2. [create_from_attributes](#c-2-2-5-2)
        6. [Updating objects](#c-2-2-6)
            1. [update](#c-2-2-6-1)
            2. [update_from_attributes](#c-2-2-6-2)
            3. [update_all](#c-2-2-6-3)
        7. [Deleting objects](#c-2-2-7)
            1. [delete](#c-2-2-7-1)
            2. [delete_all](#c-2-2-7-2)
            3. [clear_all](#c-2-2-7-3)
        8. [Finding objects](#c-2-2-8)
            1. [find_by_id](#c-2-2-8-1)
            2. [find_by_id_if_absent](#c-2-2-8-2)
            3. [find_by](#c-2-2-8-3)
            4. [find_by_if_absent](#c-2-2-8-4)
        9. [Counting objects](#c-2-2-9)
        10. [Querying](#c-2-2-10)
            1. [all](#c-2-2-10-1)
            2. [first](#c-2-2-10-2)
            3. [last](#c-2-2-10-3)
        11. [Eager fetching](#c-2-2-11)
        12. [Persistent_Collection patterns](#c-2-2-12)
            1. [Singleton collection](#c-2-2-12-1)
            2. [Query methods](#c-2-2-12-2)
            3. [Default optional values](#c-2-2-12-3)
            4. [Cascade delete](#c-2-2-12-4)
            5. [Syncronized index](#c-2-2-12-5)
            6. [Default eager fetch](#c-2-2-12-6)
    3. [Migrations](#c-2-3)
3. [Elasticsearch specifics](#c-3)
4. [Running the tests](#c-4)
5. [Developing with Vagrant](#c-5)

<a name="c-1"></a>
## Installation

Include this library in your project `composer.json` file:

```json
{
    ...

    "require": {
        ...
        "haijin/persistency": "^0.0.3",
        ...
    },

    ...
}
```

<a name="c-2"></a>
## Usage

<a name="c-2-1"></a>
### Querying a database

<a name="c-2-1-1"></a>
#### A direct query to a database

Make a query to a database:

```php
$database = new Mysql_Database();
$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

$database->query( function($query) {

    $query->collection( "users" );

    $query->proyect(
        $query->field( "name" ),
        $query->field( "last_name" )
    );

    $query->join( "address" ) ->from( "id" ) ->to( "user_id" ) ->eval( function($query) {
        $query->proyect(
            $query->concat(
                $query->field( "street_name" ), " ", $query->field( "street_number" )
            ) ->as( "address" )
        );
    });

    $query->filter(
        $query->brackets(
            $query->brackets(
                $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
            )
            ->and()
            ->brackets(
                $query ->field( "last_name" ) ->op( "=" ) ->value( "Simpson" )
            )
        )
        ->or()
        ->brackets(
            $query ->field( "address.street_name" ) ->op( "like" ) ->value( "%Evergreen%" )
        )
    );

    $query->order_by(
        $query->field( "last_name" ),
        $query->field( "name" ),
        $query->field( "address.address" )
    );

    $query->pagination(
        $query
            ->offset( 0 )
            ->limit( 10 )
    );

});
```
It may seem that the constant values are appended as strings to a query string, which is unsafe. It is not the case, it is actually safe. Under the hood the DSL uses each database engine to make the values safe.

<a name="c-2-1-2"></a>
#### Using semantic expressions

Define semantic logical expressions and combine them using logical operands:

```php
$database = new Mysql_Database();
$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

$database->query( function($query) {

    $query->collection( "users" );

    $query->proyect(
        $query->field( "name" ),
        $query->field( "last_name" )
    );

    $query->join( "address" ) ->from( "id" ) ->to( "user_id" ) ->eval( function($query) {

        $query->proyect(
            $query->concat(
                $query->field( "street_name" ), " ", $query->field( "street_number" )
            ) ->as( "address" )
        );

        $query->let( "matches_address", function($query) {
            return $query->brackets(
                $query ->field( "street_name" ) ->op( "like" ) ->value( "%Evergreen%" )
            );
        });

    });

    $query->let( "matches_name", function($query) {
        return $query->brackets(
            $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
        );
    });

    $query->let( "matches_last_name", function($query) {
        return $query->brackets(
            $query ->field( "last_name" ) ->op( "=" ) ->value( "Simpson" )
        );
    });

    $query->filter(
        $query->brackets( $query
            ->matches_name ->and() ->matches_last_name
        )
        ->or()
        ->matches_address
    );

    $query->order_by(
        $query->field( "last_name" ),
        $query->field( "name" ),
        $query->field( "address.address" )
    );

    $query->pagination(
        $query
            ->offset( 0 )
            ->limit( 10 )
    );
});
```

##### ignore()

A special case of a semantic expression is the `ignore()` statement.

The `ignore()` statement ignores the expression in boolean expressions and in function parameters, making the query more expressive and clear when the filter depends on the values or the presence of its parameters.

Example:

```php
function find_by_name_and_last_name($searched_name, $searched_last_name)
{
    $this->database->query( function($query) {

        $query->collection( "users" );

        $query->proyect(
            $query->field( "name" ),
            $query->field( "last_name" )
        );

        $query->let( "matches_name", function($query) {

            if( $searched_name != null ) {

                return $query->brackets(
                    $query
                        ->field( "name" )
                        ->op( "=" )
                        ->value( $searched_name )
                );

            } else {

                return $query->ignore();

            }

        });

        $query->let( "matches_last_name", function($query) {

            if( $searched_last_name != null ) {

                return $query->brackets(
                    $query 
                        ->field( "last_name" )
                        ->op( "=" ) 
                        ->value( $searched_last_name )
                );

            } else {

                return $query->ignore();

            }

        });

        $query->filter(
            $query
                ->matches_name ->and() ->matches_last_name
        );

        $query->order_by(
            $query->field( "last_name" ),
            $query->field( "name" )
        );

        $query->pagination(
            $query
                ->offset( 0 )
                ->limit( 10 )
        );
    });
}
```

<a name="c-2-1-3"></a>
#### Using named parameters

It is possible to use parametrized values instead of values in a query using the `param()` statement and provinding its value in an extra parameter to the query:

```php
$database = new Mysql_Database();
$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

$database->query( function($query) {

    $query->collection( "users" );

    $query->filter(
        $query ->field( "name" ) ->op( "=" ) ->param( "q" )
    );

}, [  "q" => "Lisa" ] );
```

Or compile the query once and execute many times with different values:

```php
$database = new Mysql_Database();
$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

$compiled_statement = $database->compile( function($compiler) {

    $compiler->query( function($query) {

        $query->collection( "users" );

        $query->filter(
            $query ->field( "name" ) ->op( "=" ) ->param( "q" )
        );

    });

});


$rows = $database->execute( $compiled_statement, [ 
        "q" => "Lisa"
    ]);

$rows = $database->execute( $compiled_statement, [ 
        "q" => "Bart"
    ]);
```


<a name="c-2-1-4"></a>
#### Calling query functions

Each database engine defines and allows different functions, sometimes specific to that engine alone.

Just call any function in the query, there is no need to declare it. The DSL uses a dynamic method to accept function calls.

For instance in the example above:

```php
$query->proyect(
    $query->concat(
        $query->field( "street_name" ), " ", $query->field( "street_number" )
    ) ->as( "address" )
);
```

the `concat(...)` function was not declared anywhere in the DSL for Mysql nor for any other engine.

<a name="c-2-1-5"></a>
#### Debugging and logging queries

Each database makes announcements on the queries it executes.

To inspect a SQL query for debugging or logging do

```php
use Haijin\Persistency\Announcements\About_To_Execute_Statement;

$database = new Mysql_Database();

$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

$this->database->when(
    About_To_Execute_Statement::class,
    $this,
    function($announcement) {
        var_dump( $announcement->__to_string() );

        var_dump( $announcement->get_database_name() );
        var_dump( $announcement->get_sql() );
        var_dump( $announcement->get_parameters() );
});
```

To inspect a `Elasticsearch` query do:

```php
use Haijin\Persistency\Announcements\About_To_Execute_Statement;

$database = new Elasticsearch_Database();

$database->connect( function($handle) {
    $handle->setHosts([ '127.0.0.1:9200' ]);
});

$this->database->when(
    About_To_Execute_Statement::class,
    $this,
    function($announcement) {
        var_dump( $announcement->__to_string() );

        var_dump( $announcement->get_database_name() );
        var_dump( $announcement->get_parameters() );
});

$this->database->drop_all_announcements_to( $this );
```

To stop listening to the database announcements do:

```php
$this->database->drop_all_announcements_to( $this );
```

<a name="c-2-1-6"></a>
#### Creating records in a database

Create a record in a database with:

```php
$database = new Mysql_Database();
$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

$this->database->create( function($query) {

    $query->collection( "users" );

    $query->record(
        $query->set( "name", $query->value( "Lisa" ) ),
        $query->set( "last_name", $query->value( "Simpson" ) )
    );

});
```

It is possible to use the functions supported by each database engine:

```php
$database = new Mysql_Database();
$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

$this->database->create( function($query) {

    $query->collection( "users" );

    $query->record(
        $query->set( "name",
            $query->lower( $query->value( "Lisa" ) )
        ),
        $query->set( "last_name",
            $query->value( "Simpson" )->lower()
        )
    );

});
```

Get the id assigned by the database engine to the created record with:

```php
$database = new Mysql_Database();
$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

$this->database->create( function($query) {

    $query->collection( "users" );

    $query->record(
        $query->set( "name", $query->lower( $query->value( "Lisa" ) ) ),
        $query->set( "last_name", $query->value( "Simpson" )->lower() )
    );

});

$id = $this->database->get_last_created_id();
```

<a name="c-2-1-7"></a>
#### Updating records in a database

Update recods in a database with:

```php
$database = new Mysql_Database();
$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

$this->database->update( function($query) {

    $query->collection( "users" );

    $query->record(
        $query->set( "name", $query->value( "Margaret" ) ),
        $query->set( "last_name", $query->value( "Simpson" ) )
    );

    $query->filter(
        $query->field( "name" ) ->op( "=" ) ->value( "Maggie" )
    );

});
```

<a name="c-2-1-8"></a>
#### Deleting records in a database

Delete recods from a database with:

```php
$database = new Mysql_Database();
$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

$this->database->delete( function($query) {

    $query->collection( "users" );

    $query->filter(
        $query->field( "name" ) ->op( "=" ) ->value( "Maggie" )
    );

});
```

<a name="c-2-1-9"></a>
#### Transactions

If the database engine supports it commit or rollback transactions with:

```php
$database->begin_transaction();
$database->commit_transaction();
$database->rollback_transaction();
```

or with a callable that commits at the end of the evaluation or rolls it back if an `\Exception` is thrown:

```php
$database->during_transaction_do( function($database) {

    $database->update( function($query) {

        $query->collection( "users" );

        $query->record(
            $query->set( "name", $query->value( "Marjorie" ) ),
            $query->set( "last_name", $query->value( "simpson" ) )
        );

        $query->filter(
            $query->field( "id" ) ->op( "=" ) ->value( 3 )
        );

    });

});
```

<a name="c-2-1-10"></a>
#### Implemented databases

haijin/persistency implements its current functionality for the following databases:

1. Haijin\Persistency\Engines\Mysql\Mysql_Database

Connect with the same parameters as [mysqli_connect](http://php.net/manual/en/function.mysqli-connect.php):

```php
$database = new Mysql_Database();

// host, user, password, database
$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );
```

2. Haijin\Persistency\Engines\Postgresql\Postgresql_Database

Connect with the same parameters as [pg_connect](http://php.net/manual/en/function.pg-connect.php):

```php
$database = new Postgresql_Database();

$database->connect(
    "host=localhost port=5432 dbname=haijin-persistency user=haijin password=123456"
);
```

3. Haijin\Persistency\Engines\Sqlite\Sqlite_Database

Connect with the same parameters as [SQLite3](http://php.net/manual/en/sqlite3.construct.php):

```php
$database = new Sqlite_Database();

$database->connect( "/a_filename.sqlite" );
```


All of these databases can also execute and evaluate SQL string statements with:

```php
$records = $database->execute_sql_string( "select * from  users where name = ?;", [ "Lisa" ] );

$database->evaluate( "CREATE TABLE `users` (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(45) NULL,
    `last_name` VARCHAR(45) NULL
);" );
```

4. Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Database

Connect with the protocol that provides Elasticseach [ClientBuilder](https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/_configuration.html):

```php
$database = new Sqlite_Database();

$database->connect( function($client_builder) {

    $client_builder->setHosts([ '127.0.0.1:9200' ]);

});
```

Elasticsearch wrapper does not intend to wrap every available feature but to ease the most common use cases for indexing and searching documents.

For the rest of the features it provides the connection handle to allow custom calls:

```php
$params = [
    'index'  => 'test_missing',
    'type'   => 'test',
    'client' => [ 'ignore' => [400, 404] ] 
];

$result = $elasticsearch_database->with_handle_do( function($client) use($params) {

    return $client->get($params);

});
```

`Elasticsearch` mappings has some specifics that are documented in a separated section.

<a name="c-2-2"></a>
### Mapping objects

<a name="c-2-2-1"></a>
### Models

The library allows almost any object to be persisted.

As such it does not require the persisted object to inherit from any class nor to implement any interface nor protocol.

It can persist native PHP objects or classes defined in third party libraries that can not be modified.

<a name="c-2-2-2"></a>
### Persistent_Collections

A `Persistent_Collection` is a collection of persisted objects.

It has a protocol to create, update, delete and query objects and a simple DSL to define the mappings between objects and records in a database.

A simple mapping definition example:

```php
class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = Application::get_instance()->get_mysql_db();

        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "name" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "last_name" )
                ->read_with( "get_last_name()" )
                ->write_with( "set_last_name()" );
        };

    }
}
```

and a simple usage example:

```php
$users_collection = new Users_Persistent_Collection();

$all_users = $users_collection->all();

$first_user = $users_collection->first();

$last_user = $users_collection->last();
```

<a name="c-2-2-3"></a>
### Persistent_Collections singleton pattern

`Persistent_Collection` subclasses are not singletons and this library does not assume all developers will want to treat them as singletons in every context. `Persistent_Collection` subclasses are regular classes.

However the previous example could be improved in the context of an application making the `Users_Persistent_Collection` a singleton. That would have the advantage of evaluating its definition only once and would improve expressiveness.

The recomended pattern to treat `Persistent_Collection` subclasses as singletons is to declare a second class with the only purpose of making each `Persistent_Collection` subclass a singleton class.

Here is an example:


```php
class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = Application::get_instance()->get_mysql_db();

        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "name" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "last_name" )
                ->read_with( "get_last_name()" )
                ->write_with( "set_last_name()" );
        };

    }
}

class Users_Collection
{
    static public $instance;

    static public function get()
    {
        return self::$instance;
    }

    static public function do()
    {
        return self::$instance;
    }
}

Users_Collection::$instance = new Users_Persistent_Collection();
```

to be used like:

```php
$all_users = Users_Collection::get()->all();

$first_user = Users_Collection::get()->first();

Users_Collection::do()->create( $user );
Users_Collection::do()->update( $user );
Users_Collection::do()->delete( $user );
```

From now on this documentation will follow this pattern.

<a name="c-2-2-4"></a>
### Persistent_Collection definition

Define each `Persistent_Collection` subclass in a method named `definition`:

```php
class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = Application::get_instance()->get_mysql_db();

        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "name" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "last_name" )
                ->read_with( "get_last_name()" )
                ->write_with( "set_last_name()" );
        };

    }
}
```

The definition has the following parts:

<a name="c-2-2-4-1"></a>
#### database

Defines the database for the `Persistent_Collection`.

It can be defined in the definition at instantiation time of the `Persistent_Collection` with

```php
public function definition($collection)
{
    $collection->database = Application::get_instance()->get_mysql_db();
}
```

or at any other time with

```php
Users_Collection::do()->set_database(
    Application::get_instance()->get_mysql_db()
);
```

<a name="c-2-2-4-2"></a>
#### collection_name

Defines the name for the `Persistent_Collection`. That would be the table name in sql databases like Postgres and Mysql, the index name in indexers like Sphinx, Elastichsearch and Solr or the schema name in document databases like MongoDB.

It can be defined in the definition at instantiation time of the `Persistent_Collection` with

```php
public function definition($collection)
{
    $collection->collection_name = "users";
}
```

or at any other time with

```php
Users_Collection::do()->set_collection_name( "users" );
```

<a name="c-2-2-4-3"></a>
#### objects_instantiator

Defines how to instantiate new objects after reading records from the database and mapping them to objects.

Since this library makes no assumptions on the type or protocol of the mapped objects there are several ways to instantiate new objects.

<a name="c-2-2-4-3-1"></a>
###### class instantiator

A class instantiator is the most simple instantiator. It assumes that mapped objects can be created with a `new` statement taking no parameters in its `__constructor()`

```php
public function definition($collection)
{
    $collection->instantiate_objects_with = User::class;

    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->read_with( "get_id()" )
            ->write_with( "set_id()" );

        $mapping->field( "name" )
            ->read_with( "get_name()" )
            ->write_with( "set_name()" );

        $mapping->field( "last_name" )
            ->read_with( "get_last_name()" )
            ->write_with( "set_last_name()" );
    };
}
```
<a name="c-2-2-4-3-2"></a>
###### callable instantiator

Some classes may take parameters in their constructor or may require additional initialization
and configuration.

In such cases use a `callable` to instantiate objects. The callable receives the `$mapped_record` as its first parameter to make its values avaialable for the initialization in case they are required.

The `$mapped_record` contains only the mapped fields values and it already applied the conversions defined for each field, if any.

Optionaly the callable also receives the raw record as it was read from the database as a second parameter.

```php
public function definition($collection)
{
    $collection->instantiate_objects_with = function($mapped_record, $raw_record) {
        return new User( $mapped_record[ "name" ], $mapped_record[ "last_name" ] );
    }

    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->read_with( "get_id()" )
            ->write_with( "set_id()" );

        $mapping->field( "name" )
            ->read_with( "get_name()" );

        $mapping->field( "last_name" )
            ->read_with( "get_last_name()" );
    };
}
```
<a name="c-2-2-4-3-3"></a>
###### null instantiator

When no instantiator is defined the `Persistent_Collection` returns an associative array with the mapped fields instead of an object.

```php
public function definition($collection)
{
    $collection->instantiate_objects_with = null;

    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->read_with( "[id]" );

        $mapping->field( "name" )
            ->read_with( "[name]" );

        $mapping->field( "last_name" )
            ->read_with( "[last_name]" );

    };
}
```

<a name="c-2-2-4-4"></a>
#### field_mappings

This callable defines each field mapped from and to the database engine.

<a name="c-2-2-4-4-1"></a>
##### field

Defines the name of the field in the database engine, not in the model.

```php
public function definition($collection)
{
    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" );

        $mapping->field( "name" );

        $mapping->field( "last_name" );

    };
}
```

<a name="c-2-2-4-4-2"></a>
##### is_primary_key

The `Persistent_Collection` needs to know which field is the primary key to create, update and delete single records. This definition flags the record primary key. Currently `Persistent_Collection` supports only one field as a primary key. That is, currently it does not support compound fields as primary keys.

```php
public function definition($collection)
{
    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key();

        $mapping->field( "name" );

        $mapping->field( "last_name" );

    };
}
```

<a name="c-2-2-4-4-3"></a>
##### type

Defines how to convert a value from and to a database.

```php
public function definition($collection)
{
    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->type( "integer" );

        $mapping->field( "name" )
            ->type( "string" );

        $mapping->field( "last_name" )
            ->type( "string" );

    };
}
```

It can be any of:

```php
->type( "string" )
->type( "integer" )
->type( "double" )
->type( "boolean" )
->type( "date" )
->type( "time" )
->type( "date_time" )
->type( "json" )
```

The possibility to define custom types will be added in the future.


<a name="c-2-2-4-4-4"></a>
##### read_with

Defines how to read the value from the object to persist it in the database.

Valid values are:

###### an object method

```php
public function definition($collection)
{
    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->read_with( "get_id()" );

        $mapping->field( "name" )
            ->read_with( "get_name()" );

        $mapping->field( "last_name" )
            ->read_with( "get_last_name()" );

    };
}
```

###### an object property

```php
public function definition($collection)
{
    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->read_with( "->id" );

        $mapping->field( "name" )
            ->read_with( "->name" );

        $mapping->field( "last_name" )
            ->read_with( "->last_name" );

    };
}
```

###### an array key

```php
public function definition($collection)
{
    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->read_with( "[id]" );

        $mapping->field( "name" )
            ->read_with( "[name]" );

        $mapping->field( "last_name" )
            ->read_with( "[last_name]" );

    };
}
```

###### a callable

```php
public function definition($collection)
{
    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->read_with( function($object) {
                return $object->get_id();
            });

        $mapping->field( "name" )
            ->read_with( function($object) {
                return strtolower( trim( $object->get_name() ) );
            });

        $mapping->field( "last_name" )
            ->read_with( function($object) {
                return strtolower( trim( $object->get_last_name() ) );
            });

        $mapping->field( "last_modification_time" )
            ->read_with( function($object) {
                return time();
            });
    };
}
```

`read_with` might be absent, in which case the field will not be written to the database (`read_with` reads from the object to the database).

<a name="c-2-2-4-4-5"></a>
##### write_with

Defines how to write the value from the database to the object.

Valid values are:

###### an object method

```php
public function definition($collection)
{
    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->write_with( "get_id()" );

        $mapping->field( "name" )
            ->write_with( "get_name()" );

        $mapping->field( "last_name" )
            ->write_with( "get_last_name()" );

    };
}
```

###### an object property

```php
public function definition($collection)
{
    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->write_with( "->id" );

        $mapping->field( "name" )
            ->write_with( "->name" );

        $mapping->field( "last_name" )
            ->write_with( "->last_name" );

    };
}
```

###### an array key

```php
public function definition($collection)
{
    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->write_with( "[id]" );

        $mapping->field( "name" )
            ->write_with( "[name]" );

        $mapping->field( "last_name" )
            ->write_with( "[last_name]" );

    };
}
```

###### a callable

```php
public function definition($collection)
{
    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->write_with( function($object, $mapped_record, $raw_record) {
                $object->set_id( $mapped_record[ "id"] );
            });

        $mapping->field( "name" )
            ->write_with( function($object, $mapped_record, $raw_record) {
                $object->set_id( $mapped_record[ "name"] );
             });

        $mapping->field( "last_name" )
            ->write_with( function($object, $mapped_record, $raw_record) {
                $object->set_id( $mapped_record[ "last_name"] );
            });
    };
}
```

The `write_with` callable receives 3 parameters: the object being mapped, the record with the values converted according to the mapping definitions and the raw record as it came from the database.

`write_with` might be absent, in which case the field will not be read from database.


Using callables it is possible to map multiple fields into a single object attribute or multiple object attributes into a single field:

```php
public function definition($collection)
{
    $collection->field_mappings = function($mapping) {

        $mapping->field( "file_size_amount" )

            ->read_with( function($object)) {
                $object->get_file_size()->get_amount();
            })

            ->write_with( function($object, $mapped_record, $raw_record) {
                $object->set_file_size(
                    new SizeMeasurement(
                        $mapped_record[ "file_size_amount" ],
                        $mapped_record[ "file_size_unit" ]
                    )
                );
             });

        $mapping->field( "file_size_unit" )

            ->read_with( function($object)) {
                $object->get_file_size()->get_amount()->get_unit()->to_string();
            });
    };
}
```

<a name="c-2-2-4-4-6"></a>
##### reference_to($persistent_collection)

Declares that a field has an id that references to an object in another `$persistent_collection`.

Example:

```php
public function definition($collection)
{
    $collection->database = null;

    $collection->collection_name = "users";

    $collection->instantiate_objects_with = User::class;

    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->read_with( "get_id()" )
            ->write_with( "set_id()" );

        $mapping->field( "name" )
            ->read_with( "get_name()" )
            ->write_with( "set_name()" );

        $mapping->field( "last_name" )
            ->read_with( "get_last_name()" )
            ->write_with( "set_last_name()" );


        $mapping->field( "address_id" )
            ->reference_to( Addresses_Collection::get() )
            ->read_with( "get_address()" )
            ->write_with( "set_address()" );
    };

}
```

<a name="c-2-2-4-4-7"></a>
##### reference_from($persistent_collection, $id_field)

A `has_one` relationship.

Declares that a virtual field is referenced by an object in another `$persistent_collection` from its `$persistent_collection.id_field`.

Example:

```php
public function definition($collection)
{
    $collection->database = null;

    $collection->collection_name = "users";

    $collection->instantiate_objects_with = User::class;

    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->read_with( "get_id()" )
            ->write_with( "set_id()" );

        $mapping->field( "name" )
            ->read_with( "get_name()" )
            ->write_with( "set_name()" );

        $mapping->field( "last_name" )
            ->read_with( "get_last_name()" )
            ->write_with( "set_last_name()" );

        $mapping->field( "address" )
            ->reference_from( Addresses_Collection::get(), 'user_id' )
            ->write_with( "set_address()" );
    };

}
```

The declaration of the `$persistent_collection.id_field` is mandatory, the library does not assume any naming convention.

<a name="c-2-2-4-4-8"></a>
##### reference_collection_from($persistent_collection, $id_field)

A `has_many` relationship.

Declares that a virtual field is referenced by a collection of objects in another `$persistent_collection` from its `$persistent_collection.id_field`.

Example:

```php
public function definition($collection)
{
    $collection->database = null;

    $collection->collection_name = "users";

    $collection->instantiate_objects_with = User::class;

    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->read_with( "get_id()" )
            ->write_with( "set_id()" );

        $mapping->field( "name" )
            ->read_with( "get_name()" )
            ->write_with( "set_name()" );

        $mapping->field( "last_name" )
            ->read_with( "get_last_name()" )
            ->write_with( "set_last_name()" );

        $mapping->field( "addresses" )
            ->reference_collection_from( Addresses_Collection::get(), 'user_id' )
            ->write_with( "set_addresses()" );
    };

}
```

The declaration of the `$persistent_collection.id_field` is mandatory, the library does not assume any naming convention.

<a name="c-2-2-4-4-9"></a>
##### reference_collection_through($middle_table, $left_id_field, $right_id_field, $other_collection

A `many_to_many` relationship.

Declares that a virtual field is referenced by a collection of objects in `$other_collection` through a `$middle_table`.

```
main_collection_name.primary_key == middle_table_name.left_id_field

&&

middle_table_name.right_id_field == other_collection.primary_key
```

Example:

```php
public function definition($collection)
{
    $collection->database = null;

    $collection->collection_name = "users";

    $collection->instantiate_objects_with = User::class;

    $collection->field_mappings = function($mapping) {

        $mapping->field( "id" ) ->is_primary_key()
            ->read_with( "get_id()" )
            ->write_with( "set_id()" );

        $mapping->field( "name" )
            ->read_with( "get_name()" )
            ->write_with( "set_name()" );

        $mapping->field( "last_name" )
            ->read_with( "get_last_name()" )
            ->write_with( "set_last_name()" );

        $mapping->field( "addresses" )
            ->reference_collection_through(
                'users_addresses', 'user_id', 'address_id', Addresses_Collection::get()
            )
            ->write_with( "set_addresses()" );
    };

}
```

Note that the the first parameter of `->reference_collection_through` is the middle table name, not a `Persistent_Collection`. That is not to force the creation of a `Persistent_Collection` class for a middle table that implements a `many to many` relationship but does not persist an actual object.

The declaration of all of  fields in `->reference_collection_through` is mandatory, the library does not assume any naming convention.

##### About the declaration statements

The reasons that declarations of relations use `reference_...` rather than `has_...` are two.

The first one is because the declaration is stated on the field, not the entity. The entity `user` `has_many` something, but the field references other objects.

The second one is because the `has_...` states a relation of ownership and this library does not handle the ownership between entities in any form, neither with implicit conventions nor with explicit declarations. It's a design decision that developers express the ownership of relations between entities with explicit code in the `Persistent_Collection`s.

<a name="c-2-2-5"></a>
#### Creating objects

<a name="c-2-2-5-1"></a>
#### create

Persist an object with:

```php
$user = new User();

$user->set_name( "Lisa" );
$user->set_last_name( "Simpson" );

Users_Collection::do()->create( $user );
```

<a name="c-2-2-5-2"></a>
#### create_from_attributes

Create an object from its attributes with:

```php
$user = Users_Collection::do()->create_from_attributes([
        "name" => "Lisa",
        "last_name" => "Simpson",
    ]);
```

<a name="c-2-2-6"></a>
#### Updating objects

<a name="c-2-2-6-1"></a>
##### update

Update an object with:

```php
$user = Users_Collection::get()->find_by_id( $object_id );

$user->set_name( "Margaret" );

Users_Collection::do()->update( $user );
```

<a name="c-2-2-6-2"></a>
##### update_from_attributes

Update an object from its attributes with:

```php
$user = Users_Collection::get()->find_by_id( $object_id );

Users_Collection::do()->update_from_attributes( $user, [
    "name" => "Margaret"
]);
```

<a name="c-2-2-6-3"></a>
##### update_all

Update many objects in batch with:

```php
Users_Collection::do()->update_all( function($query) {

    $query->record(
        $query->set( "last_name", $query->field( "last_name" )->lower() )
    );

    $query->filter(

        $query->field( "id") ->op( "<=" ) ->value( 2 )

    );

});
```

or with named parameters:

```php
Users_Collection::do()->update_all( function($query) {

    $query->record(
        $query->set( "last_name", $query->param( "last_name" ) )
    );

    $query->filter(

        $query->field( "id") ->op( "<=" ) ->param( "id" )

    );

}, [
    "last_name" => "simpson",
    "id" => 2
]);
```

<a name="c-2-2-7"></a>
#### Deleting objects

<a name="c-2-2-7-1"></a>
##### delete

Delete an object with:

```php
$user = Users_Collection::get()->find_by_id( $object_id );

Users_Collection::do()->delete( $user );
```

<a name="c-2-2-7-2"></a>
##### delete_all

Delete many objects in batch with:

```php
Users_Collection::do()->delete_all( function($query) {

    $query->filter(

        $query->field( "id") ->op( "<=" ) ->value( 2 )

    );

});
```

or with named parameters:

```php
Users_Collection::do()->delete_all( function($query) {

    $query->filter(

        $query->field( "id") ->op( "<=" ) ->param( "id" )

    );

}, [
    "id" => 2
]);
```

<a name="c-2-2-7-3"></a>
##### clear_all

Delete all the objects in a Persistent_Collection with

```php
Users_Collection::do()->clear_all();
```

Clearing a `Persistent_Collection` is useful for easily setting up a database in place when implementing tests:

```php
$this->before_each( function() {

    Users_Collection::do()->clear_all();

    Users_Collection::do()->create_from_attributes([
        "id" => 1,
        "name" => "Lisa",
        "last_name" => "Simpson",
    ]);

    Users_Collection::do()->create_from_attributes([
        "id" => 2,
        "name" => "Bart",
        "last_name" => "Simpson",
    ]);

    Users_Collection::do()->create_from_attributes([
        "id" => 3,
        "name" => "Maggie",
        "last_name" => "Simpson",
    ]);

});

$this->after_all( function() {

    Users_Collection::do()->clear_all();

});
```

but it is not meant to be used in real applications. That's why it is a different method from `delete_all`.

<a name="c-2-2-8"></a>
#### Finding objects

Finds a single object matching one or more field values by equality.

<a name="c-2-2-8-1"></a>
##### find_by_id

Find an object by its primary key:

```php
$user = Users_Collection::do()->find_by_id( $user_id );
```

Returns `null` if the id does not exist.

<a name="c-2-2-8-2"></a>
##### find_by_id_if_absent

Find an object by its primary key or evaluate a callable if it does no exist:

```php
$user = Users_Collection::do()->find_by_id_if_absent( $user_id, function($id) {

    $this->raise_404_error( $id );

});
```

<a name="c-2-2-8-3"></a>
##### find_by

Find an object by matching some of its fields:

```php
$user = Users_Collection::do()->find_by([
    "name" => "Lisa",
    "last_name" => "Simpson"
]);
```

Returns `null` if no object is found.

Raises an error if more than one object is found.

<a name="c-2-2-8-4"></a>
##### find_by_if_absent

Find an object by matching some of its fields or evaluate a callable if absent:

```php
$user = Users_Collection::do()->find_by_if_absent([

    "name" => "Lisa",
    "last_name" => "Simpson"

], function($fields) {

    $this->raise_404_error( $fields );

});
```

Raises an error if more than one object is found.

<a name="c-2-2-9"></a>
#### Counting objects

Count all the objects with:

```php
$count = Users_Collection::get()->count();
```

Count the objects matching a filter with:

```php
$count = Users_Collection::get()->count( function($query) {

    $query->filter(

        $query->field( "last_name" ) ->op( "=" ) ->value( "Simpson" );

    );

});
```

or with named parameters:

```php
$count = Users_Collection::get()->count( function($query) {

    $query->filter(

        $query->field( "last_name" ) ->op( "=" ) ->param( "ln" );

    );

}, [
    "ln" => "Simpson"
]);
```

<a name="c-2-2-10"></a>
#### Querying

Search for objects matching a criteria.

<a name="c-2-2-10-1"></a>
##### all

Find all objects matching a query:

```php
$users = Users_Collection::get()->all( function($query) {

    $query->filter(

        $query->field( "last_name" ) ->op( "=" ) ->value( "Simpson" );

    );

});
```

or with named parameters:

```php
$users = Users_Collection::get()->all( function($query) {

    $query->filter(

        $query->field( "last_name" ) ->op( "=" ) ->param( "ln" );

    );

}, [
    "ln" => "Simpson"
]);
```

Get all the objects in the collection. Mostly useful when testing:

```php
$users = Users_Collection::get()->all();
```

<a name="c-2-2-10-2"></a>
##### first

Find the first object matching a query.

The difference with `find_by` is that `first` allows queries of any complexitiy instead of just matching fields for equality and that `find_by` raises an error if more than one record matches the criteria where `first` returns the first one if there are more than one.

```php
$user = Users_Collection::get()->first( function($query) {

    $query->filter(

        $query->field( "last_name" ) ->op( "=" ) ->value( "Simpson" );

    );

    $query->pagination(

        $query->limit( 1 )

    );
});
```

or with named parameters:

```php
$user = Users_Collection::get()->first( function($query) {

    $query->filter(

        $query->field( "last_name" ) ->op( "=" ) ->param( "ln" );

    );

    $query->pagination(

        $query->limit( 1 )

    );

}, [
    "ln" => "Simpson"
]);
```


Get the first object in the collection sorted by id. Mostly useful when testing:

```php
$user = Users_Collection::get()->first();
```

<a name="c-2-2-10-3"></a>
##### last

Find the last object in the collection sorted by id.

Mostly useful when testing.

```php
$user = Users_Collection::get()->last();
```

<a name="c-2-2-11"></a>
#### Eager fetching

When querying objects in a collection the references to objects in other collections are not resolved by default. Instead a proxy object is set until it receives the first message, when the actual object is fetched from the database and the reference is resolved.

This lazy loading of objects requires a query for each object referenced in any other object, is very inneficient and is known as the `n+1 problem` because it performs one query to fetch the main objects and n more queries to fetch each referenced object.

To avoid the `n+1 problem` the `Persistent_Collection` accepts an additional parameter on its queries specifying which references to other collections should be eagerly fetched and resolves those references in a more efficient manner.

To detect when a reference is lazily resolved its possible to make it to raise an error or a warning with an additional parameter:

```php
class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = Sample_App::instance()->get_database( 'mysql' );

        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->type( "integer" )
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "name" )
                ->type( "string" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "books" )
                ->reference_collection_from( Books_Collection::get(), 'id_user',
                    'lazy_fetch_error' => true,
                ])
                ->read_with( "get_books()" )
                ->write_with( "set_books()" );

            $mapping->field( "address" )
                ->reference_from( Addresses_Collection::get(), 'id_user', [
                    'lazy_fetch_warning' => true
                ])
                ->read_with( "get_address()" )
                ->write_with( "set_address()" );
        };

    }
}
```

To eagerly fetch references give to any query a specification of which references to must be eagerly fetched:

```php
$eager_fetch = [
    'books' => [
        'author' => true,
        'publisher' => true
    ],
    'address' => true
];

Users_Collection::get()->all( function($query) {

    $query->order_by(
        $query->field( 'name' )
    );

}, [], $eager_fetch );
```

See also the [default eager fetch pattern](#c-2-2-12-6).

<a name="c-2-2-12"></a>
#### Persistent_Collection patterns

The following conventions are not mandatory but they are recommended.

<a name="c-2-2-12-1"></a>
##### Singleton collection

Declare a second class for each `Persisted_Collection` subclass and make it a `Persisted_Collection` getter.


In file `Users_Collection.php`.

```php
namespace App\Users_Collection;

class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = Application::get_instance()->get_mysql_db();

        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "name" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "last_name" )
                ->read_with( "get_last_name()" )
                ->write_with( "set_last_name()" );
        };
    }
}

class Users_Collection
{
    static public $instance;

    static public function get()
    {
        return self::$instance;
    }

    static public function do()
    {
        return self::$instance;
    }
}

Users_Collection::$instance = new Users_Persistent_Collection();
```

to be used like:

```php
$all_users = Users_Collection::get()->all();

$first_user = Users_Collection::get()->first();

Users_Collection::do()->create( $user );
Users_Collection::do()->update( $user );
Users_Collection::do()->delete( $user );
```

<a name="c-2-2-12-2"></a>
##### Query methods

Define each query on a `Persistent_Collection` subclass in its own method.

This way all the queries will be in a single point in the source code, making it easier for developers to understand and debug the application.

```php
class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = null;

        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "name" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "last_name" )
                ->read_with( "get_last_name()" )
                ->write_with( "set_last_name()" );
        };
    }

    /// Queries

    /**
     * Returns all the users in a page sorted by (last_name, name, id).
     */
    public function all_sorted_by_name($page, $page_size)
    {
        return $this->all( function($query) use($page, $page_size) {

            $query->order_by(
                $query->field( "last_name" ),
                $query->field( "name" ),
                $query->field( "id" ),                
            );

            $query->pagination(
                $query->page( $page ),
                $query->page_size( $page_size ),
            );

        });
    }

    /**
     * Returns all the users in a page with a name matching a term sorted by (last_name, name, id).
     */
    public function all_matching_name($q, $page, $page_size)
    {
        return $this->all( function($query) use($q, $page, $page_size) {

            $query->filter(
                $query
                    ->concat( $query->field( "name" ), " ", $query->field( "last_name") )
                    ->op( "like" )
                    ->concat( "%", $query->value( $q ), "%" )
            );

            $query->order_by(
                $query->field( "last_name" ),
                $query->field( "name" ),
                $query->field( "id" ),                
            );

            $query->pagination(
                $query->page( $page ),
                $query->page_size( $page_size ),
            );

        });
    }
}

class Users_Collection
{
    static public $instance;

    static public function get()
    {
        return self::$instance;
    }

    static public function do()
    {
        return self::$instance;
    }
}
```

to be used like:

```php
$users = Users_Collection::get()->all_sorted_by_name( 0, 30 );

$users = Users_Collection::get()->all_matching_name( $search_term, 0, 30 );
```

<a name="c-2-2-12-3"></a>
##### Default optional values

Override `create` and `update` to assign default values to the optional fields instead of, or besides of, relying in the database definition.


```php
class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = null;

        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->type( "integer" )
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "name" )
                ->type( "string" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "last_name" )
                ->type( "string" )
                ->read_with( "get_last_name()" )
                ->write_with( "set_last_name()" );

            $mapping->field( "is_admin" )
                ->type( "boolean" )
                ->read_with( "is_admin()" )
                ->write_with( "set_is_admin()" );
        };
    }

    /// Creating

    public function create($user)
    {
        if( $user->is_admin() === null ) {
            $user->set_is_admin( false );
        }

        parent::create( $user );
    }
}

class Users_Collection
{
    static public $instance;

    static public function get()
    {
        return self::$instance;
    }

    static public function do()
    {
        return self::$instance;
    }
}
```

<a name="c-2-2-12-4"></a>
##### Cascade delete

Make cascade deletes explicit and procedural.

The library makes no assumptions on the database. It might support cascade delete or not.

The library makes no assumptions nor conventions on an entity relation belonging to another one. Declarative cascade owneship tend to be confusing for developers.

```php
class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = null;

        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->type( "integer" )
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "name" )
                ->type( "string" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "last_name" )
                ->type( "string" )
                ->read_with( "get_last_name()" )
                ->write_with( "set_last_name()" );

            $mapping->field( "is_admin" )
                ->type( "boolean" )
                ->read_with( "is_admin()" )
                ->write_with( "set_is_admin()" );

            $mapping->field( "address" )
                ->reference_from( Address_Collection::get(), "id_user" )
                ->write_with( "set_address()" );

            $mapping->field( "books" )
                ->many_reference_from( Books_Collection::get(), "id_user" )
                ->write_with( "set_books()" );
        };
    }

    /// Deleting

    public function delete($user)
    {
        Address_Collection::delete_all_belonging_to( $user );
        Books_Collection::delete_all_belonging_to( $user );

        parent::delete( $user );
    }
}

class Users_Collection
{
    static public $instance;

    static public function get()
    {
        return self::$instance;
    }

    static public function do()
    {
        return self::$instance;
    }
}
```

<a name="c-2-2-12-5"></a>
##### Syncronized index

Keep the index search engine in sync with the database overriding the write operations:

```php
class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = null;

        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->type( "integer" )
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "name" )
                ->type( "string" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "last_name" )
                ->type( "string" )
                ->read_with( "get_last_name()" )
                ->write_with( "set_last_name()" );

            $mapping->field( "is_admin" )
                ->type( "boolean" )
                ->read_with( "is_admin()" )
                ->write_with( "set_is_admin()" );

            $mapping->field( "address" )
                ->reference_from( Address_Collection::get(), "id_user" )
                ->write_with( "set_address()" );

            $mapping->field( "books" )
                ->many_reference_from( Books_Collection::get(), "id_user" )
                ->write_with( "set_books()" );
        };
    }

    public function create($user)
    {
        parent::create( $user );

        Elasticsearch_Users_Collection::create( $user );
    }

    public function update($user)
    {
        parent::update( $user );

        Elasticsearch_Users_Collection::update( $user );
    }

    public function delete($user)
    {
        parent::delete( $user );

        Elasticsearch_Users_Collection::delete( $user );
    }
}

class Users_Collection
{
    static public $instance;

    static public function get()
    {
        return self::$instance;
    }

    static public function do()
    {
        return self::$instance;
    }
}
```

<a name="c-2-2-12-6"></a>
##### Default eager fetch

In each query in a `Persistent_Collection` accept an eager fetch specification and define a default one for the most common use cases if none is given:

```php
class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = Sample_App::instance()->get_database( 'mysql' );

        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->type( "integer" )
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "name" )
                ->type( "string" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "books" )
                ->reference_collection_from( Books_Collection::get(), 'id_user',
                    'lazy_fetch_error' => true,
                ])
                ->read_with( "get_books()" )
                ->write_with( "set_books()" );

            $mapping->field( "address" )
                ->reference_from( Addresses_Collection::get(), 'id_user', [
                    'lazy_fetch_warning' => true
                ])
                ->read_with( "get_address()" )
                ->write_with( "set_address()" );
        };

    }

    public function all_sorted_by_name($eager_fetch = null)
    {
        if( $eager_fetch === null ) {
            $eager_fetch = [
                'books' => [
                    'author' => true,
                    'publisher' => true
                ]
            ];
        }

        return $this->all( function($query) {

            $query->order_by(
                $query->field( 'name' )
            );

        }, [], $eager_fetch );
    }
}
```


<a name="c-3"></a>
### Elasticsearch specifics

#### Persistent_Collection super class

`Elasticsearch` collections must extend from `Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Persistent_Collection` instead of `Persistent_Collection`.

```php
use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Persistent_Collection;

class Elasticsearch_Users_Persisted_Collection extends Elasticsearch_Persistent_Collection
{
    public function definition($collection)
    {
        /// ...
    }
}
```

#### `_id` field

A `Elasticsearch_Persistent_Collection` must have a field named `_id`.

The field is not written in Elasticsearch indices but is required.


```php
use Haijin\Persistency\Engines\Elasticsearch\Elasticsearch_Persistent_Collection;

class Elasticsearch_Users_Persisted_Collection extends Elasticsearch_Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = Databases::get_elasticsearch();

        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "_id" )
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "name" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "last_name" )
                ->read_with( "get_last_name()" )
                ->write_with( "set_last_name()" );
        };

    }
}

class Elasticsearch_Users_Collection
{
    static public $instance;

    static public function get()
    {
        return self::$instance;
    }

    static public function do()
    {
        return self::$instance;
    }
}

Elasticsearch_Users_Collection::$instance =
    new Elasticsearch_Users_Persisted_Collection();
```

#### Documents creation

To create a document the `_id` field must be defined. Elasticsearch does not assign ids to documents that do not have one.

```php
Elasticsearch_Users_Collection::do()->create_from_attributes([
    "id" => 7,
    "name" => "Lisa"
    "last_name" => "Simpson"
]);
```

#### Records update

Currently a document can not be udpated with named parameters.

Updating records in a collection works like with any other collection:

```php
Elasticsearch_Users_Collection::do()->update_from_attributes([
    "id" => 7,
    "name" => "Lisa"
    "last_name" => "Simpson"
]);
```

To update records with a query a special sintax is used.

```php
$elasticsearch_database->update( function($query) {

    $query->collection( "users" );

    $query->script([
        "lang" => "painless",
        "source" => "ctx._source.name = 'Margaret'"
    ]);

    $query->filter(
        $query->match( "name", "Maggie" )
    );

});
```

The [script](https://www.elastic.co/guide/en/elasticsearch/reference/current/modules-scripting-using.html) is the same a defined by Elasticsearch.

#### Elastic sintax

Elasticsearch supports different query sintax.

Currently `haijin/persistency` implements elastic [query dsl](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl.html) and maps one to one its sintax.

That means that while `haijin/persistency` library serves as an adaptor to index and retrieve documents from a Elasticsearch server it uses its sintax as it is. No new DSL is built on top of Elasticsearch one.

In the future `haijin/persistency` will support a wrapper of Elastic's [query dsl query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html) in to ease the most common search queries.

Examples:

* [match_all query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-all-query.html)

```php
Elasticsearch_Users_Collection::get()->all( function($query) {

    $query->filter(
        $query->match_all()
    );

});
```

```php
Elasticsearch_Users_Collection::get()->all( function($query) {

    $query->filter(
        $query->match_all( "boost", 1.2 )
    );

});
```

* [match query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html)

```php
Elasticsearch_Users_Collection::get()->all( function($query) {

    $query->filter(
        $query->match( "message", "this is a test" )
    );

});
```

```php
Elasticsearch_Users_Collection::get()->all( function($query) {

    $query->filter(
        $query->match(
            $query->message([
                "query" => "this is a test",
                "operator" => "and"
            ])
        )
    );

});
```

#### Elastic additional parameters

To configure additional parameters for elastic calls do:

```php
Elasticsearch_Users_Collection::get()->all( function($query) {

    $query->filter(
        $query->match(
            $query->message([
                "query" => "this is a test",
                "operator" => "and"
            ])
        )
    );

    $query->extra_parameters([
        'query_cache' => true,
        'lowercase_expanded_terms' => true
    ]);

});
```

#### Elastic refresh

When modifying a document in an `Elasticsearch` index it uses an asyncronous call unless told otherwise.

By default `haijin/library` tells Elastic to make it sycronous.

To make it asyncronous again pass an extra paramenter in the query:

```php
$this->elasticsearch_database->create( function($query) {

    $query->collection( "users" );

    $query->record(
        $query->set( "_id", $query->value( 1 ) ),
        $query->set( "name", $query->value( "Lisa" ) ),
        $query->set( "last_name", $query->value( "Simpson" ) )
    );

    $query->extra_parameters([
        "refresh" => false
    ]);

});

$id = $this->database->get_last_created_id();

$this->expect( $id ) ->to() ->equal( 1 );

});
```

<a name="c-4"></a>
## Running the tests

```
composer specs
```

<a name="c-5"></a>
## Developing with Vagrant

Vagrant eases the creation and setup of virtual machines to create development environments.

To use Vagrant to test or develop this project download [Vagrant](https://www.vagrantup.com/downloads.html) and [VirtualBox](https://www.virtualbox.org/wiki/Downloads).

Then clone this repository:

```
git clone https://github.com/haijin-development/php-persistency.git
```

and start the virtual machine with all the databases installed and configured for development with

```
cd php-persistency/
vagrant up
```

Connect to the virtual machine with

```
vagrant ssh
```

and run the tests

```
cd src/php-persistency
composer install
composer specs
```

In case you want to connect to any database from the host machine instead of the virtual machine, for instance using a GUI client, the virtual machine IP is `192.168.33.10` and the database credentials are

```
user: haijin
password: 123456
database: 'haijin-persistency'
```