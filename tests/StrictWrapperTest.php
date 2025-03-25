<?php


declare( strict_types = 1 );


use JDWX\KV\ArrayKV;
use JDWX\KV\JsonWrapper;
use JDWX\KV\StrictWrapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( StrictWrapper::class )]
final class StrictWrapperTest extends TestCase {


    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public function testOffsetExists() : void {
        $r = new ArrayKV();
        $kv = new StrictWrapper( $r );
        self::assertFalse( isset( $kv[ 'foo' ] ) );
        $r[ 'foo' ] = 'bar';
        self::assertTrue( isset( $kv[ 'foo' ] ) );
        self::expectException( InvalidArgumentException::class );
        /** @phpstan-ignore-next-line */
        $x = isset( $kv[ 2 ] );
        unset( $x );
    }


    public function testOffsetGet() : void {
        $r = new ArrayKV();
        $kv = new StrictWrapper( $r );
        self::assertNull( $kv[ 'foo' ] );
        $r[ 'foo' ] = 'bar';
        self::assertSame( 'bar', $kv[ 'foo' ] );
    }


    public function testOffsetGetForInvalidKey() : void {
        $r = new ArrayKV();
        $kv = new StrictWrapper( $r );
        self::expectException( InvalidArgumentException::class );
        /** @phpstan-ignore-next-line */
        $x = $kv[ true ];
        unset( $x );
    }


    public function testOffsetGetForInvalidValue() : void {
        $r = new JsonWrapper( new ArrayKV() );
        $kv = new StrictWrapper( $r );
        $r[ 'foo' ] = 4.2;
        self::expectException( InvalidArgumentException::class );
        $x = $kv[ 'foo' ];
        unset( $x );
    }


    public function testOffsetSet() : void {
        $r = new ArrayKV();
        $kv = new StrictWrapper( $r );
        $kv[ 'foo' ] = 'bar';
        self::assertSame( 'bar', $r[ 'foo' ] );
    }


    public function testOffsetSetForInvalidValue() : void {
        $r = new ArrayKV();
        $kv = new StrictWrapper( $r );
        self::expectException( InvalidArgumentException::class );
        /** @phpstan-ignore-next-line */
        $kv[ 'foo' ] = 4.2;
    }


    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public function testOffsetUnset() : void {
        $r = new ArrayKV();
        $kv = new StrictWrapper( $r );
        $r[ 'foo' ] = 'bar';
        self::assertTrue( isset( $kv[ 'foo' ] ) );
        unset( $kv[ 'foo' ] );
        self::assertFalse( isset( $kv[ 'foo' ] ) );
        self::expectException( InvalidArgumentException::class );
        /**
         * @noinspection PhpIllegalArrayKeyTypeInspection
         * @phpstan-ignore-next-line
         **/
        unset( $kv[ [ 'qux' => 'quux' ] ] );
    }


}
