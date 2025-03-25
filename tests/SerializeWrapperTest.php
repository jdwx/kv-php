<?php


declare( strict_types = 1 );


use JDWX\KV\ArrayKV;
use JDWX\KV\SerializeWrapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( SerializeWrapper::class )]
final class SerializeWrapperTest extends TestCase {


    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public function testOffsetExists() : void {
        $r = new ArrayKV();
        $kv = new SerializeWrapper( $r );
        self::assertFalse( isset( $kv[ 'foo' ] ) );
        $r[ 'foo' ] = 'bar';
        self::assertTrue( isset( $kv[ 'foo' ] ) );
    }


    public function testOffsetGet() : void {
        $r = new ArrayKV();
        $kv = new SerializeWrapper( $r );
        self::assertNull( $kv[ 'foo' ] );
        $r[ 'foo' ] = serialize( 'bar' );
        self::assertSame( 'bar', $kv[ 'foo' ] );
    }


    public function testOffsetGetForObject() : void {
        $r = new ArrayKV();
        $kv = new SerializeWrapper( $r );
        $x = new stdClass();
        $x->foo = 'bar';
        $x->baz = 42;
        $r[ 'foo' ] = serialize( $x );
        $x2 = $kv[ 'foo' ];
        self::assertEquals( $x, $x2 );
    }


    public function testOffsetSet() : void {
        $r = new ArrayKV();
        $kv = new SerializeWrapper( $r );
        $kv[ 'foo' ] = 'bar';
        self::assertSame( serialize( 'bar' ), $r[ 'foo' ] );
    }


    public function testOffsetSetForObject() : void {
        $r = new ArrayKV();
        $kv = new SerializeWrapper( $r );
        $x = new stdClass();
        $x->foo = 'bar';
        $x->baz = 42;
        $kv[ 'foo' ] = $x;
        self::assertSame( serialize( $x ), $r[ 'foo' ] );
    }


    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public function testOffsetUnset() : void {
        $r = new ArrayKV();
        $kv = new SerializeWrapper( $r );
        $r[ 'foo' ] = 'bar';
        unset( $kv[ 'foo' ] );
        self::assertFalse( isset( $r[ 'foo' ] ) );
    }


}
