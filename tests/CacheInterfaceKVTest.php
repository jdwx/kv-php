<?php


declare( strict_types = 1 );


use JDWX\KV\CacheInterfaceKV;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( CacheInterfaceKV::class )]
final class CacheInterfaceKVTest extends TestCase {


    public function testFlush() : void {
        $cache = new JDWX\ArrayCache\ArrayCache();
        $kv = new CacheInterfaceKV( $cache, true );
        self::assertTrue( $cache->set( 'foo', 'bar', 60 ) );
        $kv->flush();
        self::assertNull( $cache->get( 'foo' ) );

        $kv = new CacheInterfaceKV( $cache, false );
        self::expectException( LogicException::class );
        $kv->flush();
    }


    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public function testOffsetExists() : void {
        $cache = new JDWX\ArrayCache\ArrayCache();
        $kv = new CacheInterfaceKV( $cache );
        self::assertFalse( isset( $kv[ 'foo' ] ) );
        $cache->set( 'foo', 'bar' );
        self::assertTrue( isset( $kv[ 'foo' ] ) );
    }


    public function testOffsetGet() : void {
        $cache = new JDWX\ArrayCache\ArrayCache();
        $kv = new CacheInterfaceKV( $cache );
        self::assertNull( $kv[ 'foo' ] );
        $cache->set( 'foo', 'bar' );
        self::assertSame( 'bar', $kv[ 'foo' ] );
    }


    public function testOffsetSet() : void {
        $cache = new JDWX\ArrayCache\ArrayCache();
        $kv = new CacheInterfaceKV( $cache );
        self::assertFalse( $cache->has( 'foo' ) );
        $kv[ 'foo' ] = 'bar';
        self::assertTrue( $cache->has( 'foo' ) );
    }


    public function testOffsetUnset() : void {
        $cache = new JDWX\ArrayCache\ArrayCache();
        $cache->set( 'foo', 'bar' );
        $kv = new CacheInterfaceKV( $cache );
        self::assertTrue( $cache->has( 'foo' ) );
        unset( $kv[ 'foo' ] );
        self::assertFalse( $cache->has( 'foo' ) );
    }


    public function testWalk() : void {
        $cache = new JDWX\ArrayCache\ArrayCache();
        $kv = new CacheInterfaceKV( $cache );
        self::expectException( LogicException::class );
        $kv->walk();
    }


}
