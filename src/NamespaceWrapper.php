<?php


declare( strict_types = 1 );


namespace JDWX\KV;


class NamespaceWrapper extends AbstractWrapper implements StringKVInterface {


    public function __construct( StringKVInterface $i_rData, private readonly string $stNamespace ) {
        parent::__construct( $i_rData );
    }


    public function flush() : void {
        $stCheck = $this->stNamespace . ':';
        foreach ( $this->rData->walk() as $stKey => $stValue ) {
            if ( str_starts_with( $stKey, $stCheck ) ) {
                $this->rData->offsetUnset( $stKey );
            }
        }
    }


    /**
     * @param string $offset
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function offsetExists( mixed $offset ) : bool {
        return $this->rData->offsetExists( $this->key( $offset ) );
    }


    /**
     * @param string $offset
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function offsetGet( mixed $offset ) : mixed {
        return $this->rData->offsetGet( $this->key( $offset ) );
    }


    /**
     * @param string $offset
     * @param string|int|float|bool|mixed[]|null $value
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function offsetSet( mixed $offset, mixed $value ) : void {
        $this->rData->offsetSet( $this->key( $offset ), $value );
    }


    /**
     * @param string $offset
     * @suppress PhanTypeMismatchDeclaredParamNullable
     */
    public function offsetUnset( mixed $offset ) : void {
        $this->rData->offsetUnset( $this->key( $offset ) );
    }


    public function walk() : \Generator {
        $stCheck = $this->stNamespace . ':';
        $uCheckLen = strlen( $stCheck );
        foreach ( $this->rData->walk() as $sKey => $sValue ) {
            if ( str_starts_with( $sKey, $stCheck ) ) {
                yield substr( $sKey, $uCheckLen ) => $sValue;
            }
        }
    }


    private function key( string $i_stKey ) : string {
        return $this->stNamespace . ':' . $i_stKey;
    }


}
