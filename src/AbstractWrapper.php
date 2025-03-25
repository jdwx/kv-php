<?php


declare( strict_types = 1 );


namespace JDWX\KV;


abstract class AbstractWrapper implements StringKVInterface {


    public function __construct( protected readonly StringKVInterface $rData ) {}


    public function flush() : void {
        $this->rData->flush();
    }


    /** @param string $offset */
    public function offsetExists( $offset ) : bool {
        return $this->rData->offsetExists( $offset );
    }


    /** @param string $offset */
    public function offsetUnset( $offset ) : void {
        $this->rData->offsetUnset( $offset );
    }


    public function walk() : \Generator {
        return $this->rData->walk();
    }


}