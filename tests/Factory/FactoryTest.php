<?php

namespace FactoryTest;

use  Haijin\Persistency\Factory\Create;


class FactoryTest extends \PHPUnit\Framework\TestCase
{
    public function test_instantiating_any_object()
    {
        $instance = Create::object( Sample::class );

        $this->assertEquals( true, ( $instance instanceof Sample ) );
    }

    public function test_instantiating_any_object_with_params()
    {
        $instance = Create::object( SampleWithParams::class, 1, 2, 3 );

        $this->assertEquals( true, ( $instance instanceof SampleWithParams ) );
        $this->assertEquals( 1, $instance->p1 );
        $this->assertEquals( 2, $instance->p2 );
        $this->assertEquals( 3, $instance->p3 );
    }

    public function test_instantiating_any_object_with_dsl()
    {
        $instance = Create::with( SampleWithParams::class )->params( 1, 2, 3 );

        $this->assertEquals( true, ( $instance instanceof SampleWithParams ) );
        $this->assertEquals( 1, $instance->p1 );
        $this->assertEquals( 2, $instance->p2 );
        $this->assertEquals( 3, $instance->p3 );
    }
}

class Sample
{
}

class SampleWithParams
{
    public $p1;
    public $p2;
    public $p3;

    public function __construct($p1, $p2, $p3)
    {
        $this->p1 = $p1;
        $this->p2 = $p2;
        $this->p3 = $p3;
    }
}
