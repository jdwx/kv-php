<?php


declare( strict_types = 1 );


use JDWX\KV\ArrayKV;
use JDWX\KV\NamespaceWrapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( NamespaceWrapper::class )]
final class NamespaceWrapperTest extends TestCase {


    public function testCollisions() : void {
        $kv = new ArrayKV();
        $kvn = new NamespaceWrapper( $kv, 'test' );
        $kvn2 = new NamespaceWrapper( $kv, 'test2' );
        $kv[ 'foo' ] = 'bar';
        self::assertNull( $kvn[ 'foo' ] );
        self::assertNull( $kvn2[ 'foo' ] );
        $kvn[ 'foo' ] = 'baz';
        self::assertSame( 'baz', $kvn[ 'foo' ] );
        self::assertSame( 'bar', $kv[ 'foo' ] );
        self::assertNull( $kvn2[ 'foo' ] );
        self::assertSame( 'baz', $kv[ 'test:foo' ] );

        $kvn2[ 'foo' ] = 'qux';
        self::assertSame( 'qux', $kvn2[ 'foo' ] );
        self::assertSame( 'baz', $kvn[ 'foo' ] );
        self::assertSame( 'bar', $kv[ 'foo' ] );
    }


    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public function testFlush() : void {
        $kv = new ArrayKV();
        $kvn = new NamespaceWrapper( $kv, 'test' );
        $kv[ 'foo' ] = 'bar';
        $kvn[ 'baz' ] = 'qux';
        self::assertTrue( isset( $kv[ 'test:baz' ] ) );
        $kvn->flush();
        self::assertFalse( isset( $kv[ 'test:baz' ] ) );
        self::assertTrue( isset( $kv[ 'foo' ] ) );
    }


    public function testOffsetExists() : void {
        $kvn = $this->newKV();
        self::assertFalse( $kvn->offsetExists( 'foo' ) );
        $kvn[ 'foo' ] = 'bar';
        self::assertTrue( $kvn->offsetExists( 'foo' ) );
        self::assertFalse( $kvn->offsetExists( 'baz' ) );
    }


    public function testOffsetGet() : void {
        $kvn = $this->newKV();
        $kvn[ 'foo' ] = 'bar';
        self::assertSame( 'bar', $kvn->offsetGet( 'foo' ) );
        self::assertNull( $kvn->offsetGet( 'baz' ) );
    }


    public function testOffsetSet() : void {
        $kvn = $this->newKV();
        self::assertNull( $kvn[ 'foo' ] );
        $kvn->offsetSet( 'foo', 'bar' );
        self::assertSame( 'bar', $kvn[ 'foo' ] );
    }


    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public function testOffsetUnset() : void {
        $kvn = $this->newKV();
        $kvn[ 'foo' ] = 'bar';
        self::assertTrue( isset( $kvn[ 'foo' ] ) );
        $kvn->offsetUnset( 'foo' );
        self::assertFalse( isset( $kvn[ 'foo' ] ) );
    }


    public function testWalk() : void {
        $kv = new ArrayKV();
        $kvn = new NamespaceWrapper( $kv, 'test' );
        $kv[ 'foo' ] = 'bar';
        $kv[ 'test:baz' ] = 'qux';
        $kv[ 'test:quux' ] = 'corge';
        $r = iterator_to_array( $kvn->walk() );
        self::assertSame( [ 'baz' => 'qux', 'quux' => 'corge' ], $r );
    }


    private function newKV() : NamespaceWrapper {
        return new NamespaceWrapper( new ArrayKV(), 'test' );
    }


}
