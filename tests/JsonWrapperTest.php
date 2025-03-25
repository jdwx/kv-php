<?php


declare( strict_types = 1 );


use JDWX\KV\ArrayKV;
use JDWX\KV\JsonWrapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( JsonWrapper::class )]
final class JsonWrapperTest extends TestCase {


    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public function testOffsetExists() : void {
        $r = new ArrayKV();
        $kv = new JsonWrapper( $r );
        self::assertFalse( isset( $kv[ 'foo' ] ) );
        $r[ 'foo' ] = 'bar';
        self::assertTrue( isset( $kv[ 'foo' ] ) );
    }


    public function testOffsetGet() : void {
        $r = new ArrayKV();
        $kv = new JsonWrapper( $r );
        self::assertNull( $kv[ 'foo' ] );
        $r[ 'foo' ] = '"bar"';
        self::assertSame( 'bar', $kv[ 'foo' ] );
        $r[ 'baz' ] = '{"qux":"quux"}';
        self::assertSame( [ 'qux' => 'quux' ], $kv[ 'baz' ] );
    }


    public function testOffsetSet() : void {
        $r = new ArrayKV();
        $kv = new JsonWrapper( $r );
        $kv[ 'foo' ] = 'bar';
        self::assertSame( '"bar"', $r[ 'foo' ] );
        $kv[ 'baz' ] = [ 'qux' => 'quux' ];
        self::assertSame( '{"qux":"quux"}', $r[ 'baz' ] );

        $obj = new class implements JsonSerializable {


            /** @return array<string, string> */
            public function jsonSerialize() : array {
                return [ 'corge' => 'grault' ];
            }


        };
        $kv[ 'garply' ] = $obj;
        self::assertSame( '{"corge":"grault"}', $r[ 'garply' ] );
    }


    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public function testOffsetUnset() : void {
        $r = new ArrayKV();
        $kv = new JsonWrapper( $r );
        $r[ 'foo' ] = 'bar';
        self::assertTrue( isset( $kv[ 'foo' ] ) );
        unset( $kv[ 'foo' ] );
        self::assertFalse( isset( $kv[ 'foo' ] ) );
    }


}
