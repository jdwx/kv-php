<?php


declare( strict_types = 1 );


namespace JDWX\KV;


use DateInterval;
use Psr\SimpleCache\CacheInterface;


/**
 * Provides a PSR-16 cache interface to a key-value store.
 *
 * "What happens if we back the KVCache with itself using a
 * CacheInterfaceKV?"
 *
 * "Try to imagine all data as you know it stopping instantaneously and
 * every molecule in your computer exploding at the speed of light."
 *
 * "Alright, that's bad. Important safety tip. Thanks, Egon."
 */
readonly class KVCache implements CacheInterface {


    private TTLWrapper $kv;


    public function __construct( StringKVInterface $i_kv, private ?float $nfDefaultTtl = null,
                                 bool              $i_bRefreshOnGet = false ) {
        if ( ! $i_kv instanceof TTLWrapper ) {
            if ( ! $i_kv instanceof MixedKVInterface ) {
                $i_kv = new SerializeWrapper( $i_kv );
            }
            $i_kv = new TTLWrapper( $i_kv, $this->nfDefaultTtl, $i_bRefreshOnGet );
        }
        $this->kv = $i_kv;
    }


    public function clear() : bool {
        $this->kv->flush();
        return true;
    }


    public function delete( string $key ) : bool {
        $this->kv->offsetUnset( $key );
        return true;
    }


    public function deleteMultiple( iterable $keys ) : bool {
        foreach ( $keys as $key ) {
            $this->delete( $key );
        }
        return true;
    }


    public function get( string $key, $default = null ) : mixed {
        return $this->kv[ $key ] ?? $default;
    }


    public function getMultiple( iterable $keys, $default = null ) : iterable {
        foreach ( $keys as $key ) {
            $x = $this->get( $key, $default );
            if ( ! is_null( $x ) ) {
                yield $key => $x;
            }
        }
    }


    public function has( string $key ) : bool {
        return isset( $this->kv[ $key ] );
    }


    public function set( string $key, mixed $value, DateInterval|int|null $ttl = null ) : bool {
        if ( $ttl instanceof DateInterval ) {
            $ttl = $ttl->s;
        }
        if ( ! is_int( $ttl ) ) {
            $ttl = $this->nfDefaultTtl;
        }
        $this->kv->setWithTTL( $key, $value, $ttl );
        return true;
    }


    /** @param iterable<string, mixed> $values */
    public function setMultiple( iterable $values, DateInterval|int|null $ttl = null ) : bool {
        foreach ( $values as $key => $value ) {
            $this->set( $key, $value, $ttl );
        }
        return true;
    }


}