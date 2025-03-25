<?php


declare( strict_types = 1 );


use JDWX\KV\AbstractWrapper;
use JDWX\KV\ArrayKV;
use PHPUnit\Framework\TestCase;


class AbstractWrapperTest extends TestCase {


    public function testFlush() : void {
        $r = [ 'foo' => 'bar' ];
        $wrapper = $this->newAbstractWrapper( $r );
        $wrapper->flush();
        self::assertEmpty( $r );
    }


    public function testWalk() : void {
        $r = [ 'foo' => 'bar', 'baz' => 'qux' ];
        $wrapper = $this->newAbstractWrapper( $r );
        $r2 = iterator_to_array( $wrapper->walk() );
        self::assertSame( $r, $r2 );
    }


    /** @param array<string, mixed> $r */
    private function newAbstractWrapper( array &$r = [] ) : AbstractWrapper {
        return new class( $r ) extends AbstractWrapper {


            /** @param array<string, mixed> &$r */
            public function __construct( array &$r ) {
                parent::__construct( new ArrayKV( $r ) );
            }


            public function offsetGet( mixed $offset ) : null {
                return null;
            }


            public function offsetSet( mixed $offset, mixed $value ) : void {}


        };
    }


}
