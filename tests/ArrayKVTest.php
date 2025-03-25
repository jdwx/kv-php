<?php


declare( strict_types = 1 );


use JDWX\KV\ArrayKV;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( ArrayKV::class )]
final class ArrayKVTest extends TestCase {


    public function testFlush() : void {
        $r = [];
        $kv = new ArrayKV( $r );
        $r[ 'foo' ] = 'bar';
        $kv->flush();
        /** @phpstan-ignore-next-line */
        self::assertEmpty( $r );
    }


    public function testOffsetExists() : void {
        $r = [];
        $kv = new ArrayKV( $r );
        self::assertFalse( $kv->offsetExists( 'foo' ) );
        $r[ 'foo' ] = 'bar';
        self::assertTrue( $kv->offsetExists( 'foo' ) );
        self::assertFalse( $kv->offsetExists( 'baz' ) );
    }


    public function testOffsetGet() : void {
        $r = [];
        $kv = new ArrayKV( $r );
        self::assertNull( $kv->offsetGet( 'foo' ) );
        $r[ 'foo' ] = 'bar';
        self::assertSame( 'bar', $kv->offsetGet( 'foo' ) );
        self::assertNull( $kv->offsetGet( 'baz' ) );
    }


    public function testOffsetSet() : void {
        $r = [];
        $kv = new ArrayKV( $r );
        self::assertFalse( isset( $r[ 'foo' ] ) );
        $kv->offsetSet( 'foo', 'bar' );
        self::assertSame( 'bar', $r[ 'foo' ] );
    }


    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public function testOffsetUnset() : void {
        $r = [];
        $kv = new ArrayKV( $r );
        $r[ 'foo' ] = 'bar';
        /** @phpstan-ignore-next-line */
        self::assertTrue( isset( $r[ 'foo' ] ) );
        $kv->offsetUnset( 'foo' );
        /** @phpstan-ignore-next-line */
        self::assertFalse( isset( $r[ 'foo' ] ) );
    }


    public function testWalk() : void {
        $r = [];
        $kv = new ArrayKV( $r );
        $r[ 'foo' ] = 'bar';
        $r[ 'baz' ] = 'qux';
        $r2 = iterator_to_array( $kv->walk() );
        self::assertSame( [ 'foo' => 'bar', 'baz' => 'qux' ], $r2 );
    }


}
