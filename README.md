# Haijin Persistency

A simple, complete, adaptable and independent query builder and ORM for PHP.

[![Latest Stable Version](https://poser.pugx.org/haijin/persistency/version)](https://packagist.org/packages/haijin/persistency)
[![Latest Unstable Version](https://poser.pugx.org/haijin/persistency/v/unstable)](https://packagist.org/packages/haijin/persistency)
[![Build Status](https://travis-ci.org/haijin-development/php-persistency.svg?branch=v0.0.2)](https://travis-ci.org/haijin-development/php-persistency)
[![License](https://poser.pugx.org/haijin/persistency/license)](https://packagist.org/packages/haijin/persistency)

### Version 0.0.3

This library is under active development and no stable version was released yet.

If you like it a lot you may contribute by [financing](https://github.com/haijin-development/support-haijin-development) its development.

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
                2. [closure instantiator](#c-2-2-4-3-2)
                3. [null instantiator](#c-2-2-4-3-3)
            4. [field_mappings](#c-2-2-4-4)
                1. [field](#c-2-2-4-4-1)
                2. [is_primary_key](#c-2-2-4-4-2)
                3. [type](#c-2-2-4-4-3)
                4. [read_with](#c-2-2-4-4-4)
                5. [write_with](#c-2-2-4-4-5)
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
        11. [Persistent_Collection patterns](#c-2-2-11)
            1. [Singleton collection](#c-2-2-11-1)
            2. [Query methods](#c-2-2-11-2)
            3. [Default optional values](#c-2-2-11-3)
            4. [Cascade delete](#c-2-2-11-4)
            5. [Syncronized index](#c-2-2-11-5)
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

<a name="c-2-1-3"></a>
#### Using named parameters

It is possible to use parametrized values instead of values in a query using the `param()` statement and provinding its value in an extra parameter to the query:

```php
$database = new Mysql_Database();
$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

$database->compile_query_statement( function($query) {

    $query->collection( "users" );

    $query->filter(
        $query ->field( "name" ) ->op( "=" ) ->param( "q" )
    );

}, [ "q" => "Lisa" ] );
```

Or compile the query once and execute many times with different values:

```php
$database = new Mysql_Database();
$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

$compiled_statement = $database->compile_query_statement( function($query) {

    $query->collection( "users" );

    $query->filter(
        $query ->field( "name" ) ->op( "=" ) ->param( "q" )
    );

});

$rows = $database->execute( $compiled_statement, [ "q" => "Lisa" ] );

$rows = $database->execute( $compiled_statement, [ "q" => "Bart" ] );
```

To compile statements call:

```php
$compiled_statement = $database->compile_query_statement($query_closure);

$compiled_statement = $database->compile_create_statement($create_closure);

$compiled_statement = $database->compile_update_statement($update_closure);

$compiled_statement = $database->compile_delete_statement($delete_closure);
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
#### Debugging the query

The functions `query`, `create`, `update` and `delete` accepts another closure as its third parameter to debug the statement:

```php
$database = new Mysql_Database();
$database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

$database->query( function($query) use($database) {

    $query->collection( "users" );

    $query->proyect(
        $query->field( "name" ),
        $query->field( "last_name" )
    );

    $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
        ->eval( function($query) {

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
        $query->field( "users.last_name" ) ->desc(),
        $query->field( "users.name" ) ->desc(),
        $query->field( "address" ) ->desc()
    );

    $query->pagination(
        $query
            ->offset( 0 )
            ->limit( 10 )
    );

}, function($sql, $query_parameters) {
        var_dump( $sql );
        var_dump( $query_parameters );
});
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

or with a closure that commits at the end of the evaluation or rolls it back if an `\Exception` is thrown:

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

}, $this );
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

}, $this );
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
###### closure instantiator

Some classes may take parameters in their constructor or may require additional initialization
and configuration.

In such cases use a `closure` to instantiate objects. The closure receives the `$mapped_record` as its first parameter to make its values avaialable for the initialization in case they are required.

The `$mapped_record` contains only the mapped fields values and it already applied the conversions defined for each field, if any.

Optionaly the closure also receives the raw record as it was read from the database as a second parameter.

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

This closure defines each field mapped from and to the database engine.

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

###### a closure

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

###### a closure

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

The `write_with` closure receives 3 parameters: the object being mapped, the record with the values converted according to the mapping definitions and the raw record as it came from the database.

`write_with` might be absent, in which case the field will not be read from database.


Using closures it is possible to map multiple fields into a single object attribute or multiple object attributes into a single field:

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

}, [ "last_name" => "simpson", "id" => 2 ] );
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

}, [ "id" => 2 ] );
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

Find an object by its primary key or evaluate a closure if it does no exist:

```php
$user = Users_Collection::do()->find_by_id_if_absent( $user_id, function($id) {

    $this->raise_404_error( $id );

}, $this );
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

Find an object by matching some of its fields or evaluate a closure if absent:

```php
$user = Users_Collection::do()->find_by_if_absent([

    "name" => "Lisa",
    "last_name" => "Simpson"

], function($fields) {

    $this->raise_404_error( $fields );

}, $this );
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

}, [ "ln" => "Simpson" ] );
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

}, [ "ln" => "Simpson" ] );
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

}, [ "ln" => "Simpson" ] );
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
#### Persistent_Collection patterns

The following conventions are not mandatory but they are recommended.

<a name="c-2-2-11-1"></a>
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

<a name="c-2-2-11-2"></a>
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

<a name="c-2-2-11-3"></a>
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

<a name="c-2-2-11-4"></a>
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
                ->one_referenced_from( Address_Collection::class, "id_user" )
                ->write_with( "set_address()" );

            $mapping->field( "books" )
                ->many_referenced_from( Books_Collection::class, "id_user" )
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

<a name="c-2-2-11-5"></a>
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
                ->one_referenced_from( Address_Collection::class, "id_user" )
                ->write_with( "set_address()" );

            $mapping->field( "books" )
                ->many_referenced_from( Books_Collection::class, "id_user" )
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

Currently a document can be udpate within a `Persistent_Collection` with:

```php
Elasticsearch_Users_Collection::do()->update_from_attributes([
    "id" => 7,
    "name" => "Lisa"
    "last_name" => "Simpson"
]);
```

However the general `Elasticsearch_Database::update` has not yet been implemented.

At the moment only a single record can be updated.

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