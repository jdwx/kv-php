<?php


declare( strict_types = 1 );


namespace JDWX\KV;


/**
 * When you absolutely, positively must have a string key and value.
 * (Mainly useful for testing & debugging.)
 */
class StrictWrapper extends AbstractWrapper implements StringKVInterface {


    /** @param string $offset */
    public function offsetExists( $offset ) : bool {
        /** @phpstan-ignore-next-line */
        if ( ! is_string( $offset ) ) {
            throw new \InvalidArgumentException( 'KV key must be a string' );
        }
        return $this->rData->offsetExists( $offset );
    }


    /**
     * @param string $offset
     * @return ?string
     */
    public function offsetGet( $offset ) : ?string {
        /** @phpstan-ignore-next-line */
        if ( ! is_string( $offset ) ) {
            throw new \InvalidArgumentException( 'KV key must be a string' );
        }
        $x = $this->rData->offsetGet( $offset );
        if ( is_string( $x ) || is_null( $x ) ) {
            return $x;
        } else {
            throw new \InvalidArgumentException( 'KV value must be a string' );
        }
    }


    /**
     * @param string $offset
     * @param string $value
     */
    public function offsetSet( $offset, $value ) : void {
        /** @phpstan-ignore-next-line */
        if ( ! is_string( $offset ) ) {
            throw new \InvalidArgumentException( 'KV key must be a string' );
        }
        /** @phpstan-ignore-next-line */
        if ( ! is_string( $value ) ) {
            throw new \InvalidArgumentException( 'KV value must be a string' );
        }
        $this->rData->offsetSet( $offset, $value );
    }


    /** @param string $offset */
    public function offsetUnset( $offset ) : void {
        /** @phpstan-ignore-next-line */
        if ( ! is_string( $offset ) ) {
            throw new \InvalidArgumentException( 'KV key must be a string' );
        }
        $this->rData->offsetUnset( $offset );
    }


}
