<?php


declare( strict_types = 1 );


namespace JDWX\KV;


/**
 * This only exists because array doesn't implement ArrayAccess.
 */
class ArrayKV implements StringKVInterface {


    /** @param array<string, mixed> $rData */
    public function __construct( private array &$rData = [] ) {}


    public function flush() : void {
        $this->rData = [];
    }


    /** @param string $offset */
    public function offsetExists( $offset ) : bool {
        return isset( $this->rData[ $offset ] );
    }


    /**
     * @param string $offset
     * @return ?string
     */
    public function offsetGet( $offset ) : ?string {
        return $this->rData[ $offset ] ?? null;
    }


    /**
     * @param string $offset
     * @param string $value
     */
    public function offsetSet( $offset, $value ) : void {
        $this->rData[ $offset ] = $value;
    }


    /** @param string $offset */
    public function offsetUnset( $offset ) : void {
        unset( $this->rData[ $offset ] );
    }


    public function walk() : \Generator {
        foreach ( $this->rData as $sKey => $sValue ) {
            yield $sKey => $sValue;
        }
    }


}
