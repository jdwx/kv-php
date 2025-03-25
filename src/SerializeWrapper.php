<?php


declare( strict_types = 1 );


namespace JDWX\KV;


class SerializeWrapper extends AbstractWrapper implements MixedKVInterface {


    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet( $offset ) : mixed {
        $x = $this->rData->offsetGet( $offset );
        if ( ! is_string( $x ) ) {
            return $x;
        }
        return unserialize( $x );
    }


    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet( $offset, mixed $value ) : void {
        $this->rData->offsetSet( $offset, serialize( $value ) );
    }


}
