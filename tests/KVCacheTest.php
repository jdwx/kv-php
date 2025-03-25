<?php


declare( strict_types = 1 );


use JDWX\KV\ArrayKV;
use JDWX\KV\JsonWrapper;
use JDWX\KV\KVCache;
use JDWX\KV\TTLWrapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;


readonly class CacheReturn {


    public function __construct( public CacheInterface $cache, public TTLWrapper $ttl ) {}


}


#[CoversClass( KVCache::class )]
final class KVCacheTest extends TestCase {


    public function testClear() : void {
        $x = $this->newCache();
        $x->ttl->offsetSet( 'foo', 'bar' );
        $x->ttl->offsetSet( 'baz', 'qux' );
        $x->cache->clear();
        self::assertNull( $x->cache->get( 'foo' ) );
        self::assertNull( $x->cache->get( 'baz' ) );
        self::assertFalse( $x->ttl->offsetExists( 'foo' ) );
        self::assertFalse( $x->ttl->offsetExists( 'baz' ) );
    }


    public function testConstructForStringKV() : void {
        $kv = new ArrayKV();
        $kvc = new KVCache( $kv, 5 );
        $kvc->set( 'foo', 'bar' );
        self::assertTrue( $kv->offsetExists( 'foo' ) );
        self::assertSame( 'bar', $kvc->get( 'foo' ) );
    }


    public function testDelete() : void {
        $x = $this->newCache();
        $x->ttl->offsetSet( 'foo', 'bar' );
        $x->cache->delete( 'foo' );
        self::assertFalse( $x->ttl->offsetExists( 'foo' ) );
    }


    public function testDeleteMultiple() : void {
        $x = $this->newCache();
        $x->ttl->offsetSet( 'foo', 'bar' );
        $x->ttl->offsetSet( 'baz', 'qux' );
        $x->ttl->offsetSet( 'quux', 'corge' );
        $x->cache->deleteMultiple( [ 'foo', 'quux' ] );
        self::assertFalse( $x->ttl->offsetExists( 'foo' ) );
        self::assertTrue( $x->ttl->offsetExists( 'baz' ) );
        self::assertFalse( $x->ttl->offsetExists( 'quux' ) );
    }


    public function testGet() : void {
        $x = $this->newCache();
        self::assertNull( $x->cache->get( 'foo' ) );
        self::assertSame( 5, $x->cache->get( 'foo', 5 ) );
        $x->ttl->offsetSet( 'foo', 'bar' );
        self::assertSame( 'bar', $x->cache->get( 'foo', 5 ) );
    }


    public function testGetForExpired() : void {
        $x = $this->newCache();
        $x->ttl->setWithTTL( 'foo', 'bar', 0.1 );
        self::assertSame( 'bar', $x->cache->get( 'foo' ) );
        usleep( 120_000 );
        self::assertNull( $x->cache->get( 'foo' ) );
        self::assertSame( 5, $x->cache->get( 'foo', 5 ) );
    }


    public function testGetMultiple() : void {
        $x = $this->newCache();
        $x->ttl->offsetSet( 'foo', 'bar' );
        $x->ttl->offsetSet( 'baz', 'qux' );
        self::assertSame(
            [ 'foo' => 'bar', 'baz' => 'qux' ],
            iterator_to_array( $x->cache->getMultiple( [ 'foo', 'quux', 'baz' ] ) )
        );
        self::assertSame(
            [ 'foo' => 'bar', 'quux' => 5, 'baz' => 'qux', ],
            iterator_to_array( $x->cache->getMultiple( [ 'foo', 'quux', 'baz' ], 5 ) )
        );
    }


    public function testHas() : void {
        $x = $this->newCache();
        self::assertFalse( $x->cache->has( 'foo' ) );
        $x->ttl->offsetSet( 'foo', 'bar' );
        self::assertTrue( $x->cache->has( 'foo' ) );
        self::assertFalse( $x->cache->has( 'baz' ) );
    }


    public function testHasForExpired() : void {
        $x = $this->newCache();
        $x->ttl->setWithTTL( 'foo', 'bar', 0.1 );
        self::assertTrue( $x->cache->has( 'foo' ) );
        usleep( 120_000 );
        self::assertFalse( $x->cache->has( 'foo' ) );
    }


    public function testSet() : void {
        $x = $this->newCache();
        $x->cache->set( 'foo', 'bar' );
        self::assertSame( 'bar', $x->ttl->offsetGet( 'foo' ) );
    }


    public function testSetForDateInterval() : void {
        $x = $this->newCache();
        $x->cache->set( 'foo', 'bar', new DateInterval( 'PT1S' ) );
        self::assertSame( 'bar', $x->ttl->offsetGet( 'foo' ) );
        usleep( 1_100_000 );
        self::assertNull( $x->ttl->offsetGet( 'foo' ) );
    }


    public function testSetForExpires() : void {
        $x = $this->newCache();
        $x->cache->set( 'foo', 'bar', 1 );
        self::assertSame( 'bar', $x->ttl->offsetGet( 'foo' ) );
        usleep( 1_100_000 );
        self::assertNull( $x->ttl->offsetGet( 'foo' ) );
    }


    public function testSetMultiple() : void {
        $x = $this->newCache();
        $x->cache->setMultiple( [ 'foo' => 'bar', 'baz' => 'qux' ] );
        self::assertSame( 'bar', $x->ttl->offsetGet( 'foo' ) );
        self::assertSame( 'qux', $x->ttl->offsetGet( 'baz' ) );
    }


    public function testSetMultipleForExpires() : void {
        $x = $this->newCache();
        $x->cache->setMultiple( [ 'foo' => 'bar', 'baz' => 'qux' ], 1 );
        self::assertSame( 'bar', $x->ttl->offsetGet( 'foo' ) );
        self::assertSame( 'qux', $x->ttl->offsetGet( 'baz' ) );
        usleep( 1_100_000 );
        self::assertNull( $x->ttl->offsetGet( 'foo' ) );
        self::assertNull( $x->ttl->offsetGet( 'baz' ) );
    }


    private function newCache() : CacheReturn {
        $kv = new ArrayKV();
        $kv = new JsonWrapper( $kv );
        $kv = new TTLWrapper( $kv, null );
        return new CacheReturn( new KVCache( $kv, 5 ), $kv );
    }


}
