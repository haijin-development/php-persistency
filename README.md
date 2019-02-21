# Haijin Persistency

A simple, complete, adaptable and independent query builder and ORM for PHP.

[![Latest Stable Version](https://poser.pugx.org/haijin/persistency/version)](https://packagist.org/packages/haijin/persistency)
[![Latest Unstable Version](https://poser.pugx.org/haijin/persistency/v/unstable)](https://packagist.org/packages/haijin/persistency)
[![Build Status](https://travis-ci.org/haijin-development/php-persistency.svg?branch=v0.0.2)](https://travis-ci.org/haijin-development/php-persistency)
[![License](https://poser.pugx.org/haijin/persistency/license)](https://packagist.org/packages/haijin/persistency)

### Version 0.0.1

This library is under active development and no stable version was released yet.

If you like it a lot you may contribute by [financing](https://github.com/haijin-development/support-haijin-development) its development.

## Table of contents

1. [Installation](#c-1)
2. [Usage](#c-2)
    1. [Querying a database](#c-2-1)
        1. [A direct query to a database](#c-2-1-1)
        2. [Using semantic expressions](#c-2-1-2)
        3. [Calling query functions](#c-2-1-3)
        4. [Debugging the query](#c-2-1-4)
        5. [Creating records in a database](#c-2-1-5)
        6. [Updating records in a database](#c-2-1-6)
        7. [Deleting records in a database](#c-2-1-7)
        8. [Transactions](#c-2-1-8)
        9. [Implemented databases](#c-2-1-9)
    2. [Mapping objects](#c-2-2)
    3. [Migrations](#c-2-3)
3. [Running the tests](#c-3)
4. [Developing with Vagrant](#c-4)

<a name="c-1"></a>
## Installation

Include this library in your project `composer.json` file:

```json
{
    ...

    "require": {
        ...
        "haijin/persistency": "^0.0.1",
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

        $query->let( "matches_address", function($query) { return
            $query->brackets(
                $query ->field( "street_name" ) ->op( "like" ) ->value( "%Evergreen%" )
            );
        });

    });

    $query->let( "matches_name", function($query) { return
        $query->brackets(
            $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
        );
    });

    $query->let( "matches_last_name", function($query) { return
        $query->brackets(
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
#### Calling query functions

Each database engine defines and allows different functions, sometimes specific to that engine alone.

Just call any function the query, there is no need to declare it. The DSL uses a dynamic method to accept function calls.

For instance in the example above:

```php
$query->proyect(
    $query->concat(
        $query->field( "street_name" ), " ", $query->field( "street_number" )
    ) ->as( "address" )
);
```

the `concat(...)` function was not declared anywhere in the DSL for Mysql nor for any other engine.

<a name="c-2-1-4"></a>
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

            $query->let( "matches_address", function($query) { return
                $query->brackets(
                    $query ->field( "street_name" ) ->op( "like" ) ->value( "%Evergreen%" )
                );
            });
    });

    $query->let( "matches_name", function($query) { return
        $query->brackets(
            $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
        );
    });

    $query->let( "matches_last_name", function($query) { return
        $query->brackets(
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

<a name="c-2-1-5"></a>
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

<a name="c-2-1-6"></a>
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

<a name="c-2-1-7"></a>
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

<a name="c-2-1-8"></a>
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

<a name="c-2-1-9"></a>
#### Implemented databases

haijin/persistency implements its current functionality for the following databases:

1. Haijin\Persistency\Engines\Mysql\Mysql_Database
2. Haijin\Persistency\Engines\Postgresql\Postgresql_Database
3. Haijin\Persistency\Engines\Sqlite\Sqlite_Database

All of these databases can also execute and evaluate SQL string statements with:

```php
$records = $database->execute_sql_string( "select * from  users where name = ?;", [ "Lisa" ] );

$database->evaluate( "CREATE TABLE `users` (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(45) NULL,
    `last_name` VARCHAR(45) NULL
);" );
```

<a name="c-2-2"></a>
### Mapping objects


<a name="c-3"></a>
## Running the tests

```
composer specs
```

<a name="c-4"></a>
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
composer specs
```

In case you want to connect to any database from the host machine instead of the virtual machine, for instance using a GUI client, the virtual machine IP is `192.168.33.10` and the database credentials are

```
user: haijin
password: 123456
database: 'haijin-persistency'
```