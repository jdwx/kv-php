<?php


declare( strict_types = 1 );


namespace JDWX\KV;


use JDWX\Json\Json;
use JsonSerializable;


class JsonWrapper extends AbstractWrapper implements MixedKVInterface {


    /**
     * @param string $offset
     * @return string|int|float|bool|mixed[]|null
     */
    public function offsetGet( $offset ) : string|int|float|bool|array|null {
        $x = $this->rData->offsetGet( $offset );
        if ( ! is_string( $x ) ) {
            return $x;
        }
        return Json::decode( $x );
    }


    /**
     * @param string $offset
     * @param string|int|float|bool|mixed[]|JsonSerializable|null $value
     */
    public function offsetSet( $offset, $value ) : void {
        if ( $value instanceof JsonSerializable ) {
            $value = $value->jsonSerialize();
        }
        $this->rData->offsetSet( $offset, Json::encode( $value ) );
    }


}
