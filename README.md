# Haijin Persistency

A simple, complete, adaptable and independent query builder and ORM for PHP.

[![Latest Stable Version](https://poser.pugx.org/haijin/persistency/version)](https://packagist.org/packages/haijin/persistency)
[![Latest Unstable Version](https://poser.pugx.org/haijin/persistency/v/unstable)](https://packagist.org/packages/haijin/persistency)
[![Build Status](https://travis-ci.com/haijin-development/php-persistency.svg?branch=v0.0.2)](https://travis-ci.com/haijin-development/php-persistency)
[![License](https://poser.pugx.org/haijin/persistency/license)](https://packagist.org/packages/haijin/persistency)

### Version 0.0.1

This library is under active development and no stable version was released yet.

If you like it a lot you may contribute by financing its development. Please contact us by email.

## Table of contents

1. [Installation](#c-1)
2. [Usage](#c-2)
    1. [Querying a database](#c-2-1)
    2. [Mapping objects](#c-2-2)
    3. [Migrations](#c-2-3)
3. [Running the tests](#c-3)

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

Make a query to a database:

```php
$database = new MysqlDatabase();
$database->connect( $connect_string );

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

Define semantic logical expressions and combine them using logical operands:

```php
$database = new MysqlDatabase();
$database->connect( $connect_string );

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


<a name="c-2-2"></a>
### Mapping objects


<a name="c-3"></a>
## Running the tests

```
composer test
```