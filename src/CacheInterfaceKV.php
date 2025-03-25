<?php


declare( strict_types = 1 );


namespace JDWX\KV;


use Psr\SimpleCache\CacheInterface;


/** @noinspection PhpClassCanBeReadonlyInspection */


class CacheInterfaceKV implements StringKVInterface {


    public function __construct( private readonly CacheInterface $cache,
                                 private readonly bool           $bAllowFlush = false ) {}


    public function flush() : void {
        if ( ! $this->bAllowFlush ) {
            throw new \LogicException( 'Cannot flush CacheInterfaceKV' );
        }
        $this->cache->clear();
    }


    /**
     * @param string $offset
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function offsetExists( mixed $offset ) : bool {
        return $this->cache->has( $offset );
    }


    /**
     * @param string $offset
     * @return ?string
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function offsetGet( mixed $offset ) : ?string {
        return $this->cache->get( $offset );
    }


    /**
     * @param string $offset
     * @param string $value
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function offsetSet( mixed $offset, mixed $value ) : void {
        $this->cache->set( $offset, $value );
    }


    /**
     * @param string $offset
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function offsetUnset( mixed $offset ) : void {
        $this->cache->delete( $offset );
    }


    public function walk() : \Generator {
        throw new \LogicException( 'Cannot walk a CacheInterfaceKV' );
    }


}