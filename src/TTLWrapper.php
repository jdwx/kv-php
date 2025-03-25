<?php


declare( strict_types = 1 );


namespace JDWX\KV;


class TTLWrapper extends AbstractWrapper implements MixedKVInterface {


    public function __construct( MixedKVInterface      $i_rData, private readonly ?float $nfDefaultTTL,
                                 private readonly bool $bRefreshOnGet = false ) {
        parent::__construct( $i_rData );
        if ( $nfDefaultTTL < 0.0 ) {
            throw new \InvalidArgumentException( 'Default TTL must be non-negative' );
        }
    }


    /** @return mixed[] */
    private static function unwrap( mixed $x ) : array {
        if ( ! is_array( $x ) ) {
            throw new \RuntimeException( 'Invalid TTLWrapper value' );
        }
        if ( isset( $x[ 'expires' ] ) && $x[ 'expires' ] < microtime( true ) ) {
            return [ null, null ];
        }
        return [ $x[ 'data' ], $x[ 'ttl' ] ?? null ];
    }


    /** @return mixed[] */
    private static function wrap( mixed $x, ?float $fTTL ) : array {
        $r = [ 'data' => $x ];
        if ( ! is_null( $fTTL ) ) {
            $r[ 'ttl' ] = $fTTL;
            $r[ 'expires' ] = microtime( true ) + $fTTL;
        }
        return $r;
    }


    /** @param string $offset */
    public function offsetExists( $offset ) : bool {
        $x = $this->rData->offsetGet( $offset );
        if ( is_null( $x ) ) {
            return false;
        }
        [ $y ] = self::unwrap( $x );
        if ( is_null( $y ) ) {
            $this->rData->offsetUnset( $offset );
            return false;
        }
        return true;
    }


    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet( $offset ) : mixed {
        $x = $this->rData->offsetGet( $offset );
        if ( is_null( $x ) ) {
            return null;
        }
        [ $y, $fTTL ] = self::unwrap( $x );
        if ( is_null( $y ) ) {
            $this->rData->offsetUnset( $offset );
        } elseif ( $this->bRefreshOnGet && ! is_null( $fTTL ) ) {
            $this->setTTLInner( $offset, $y, $fTTL );
        }
        return $y;
    }


    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet( $offset, mixed $value ) : void {
        $this->setTTLInner( $offset, $value, $this->nfDefaultTTL );
    }


    public function setWithTTL( string $i_stOffset, mixed $i_xValue, ?float $i_fTTL ) : void {
        if ( $i_fTTL < 0.0 ) {
            throw new \InvalidArgumentException( 'TTL must be non-negative' );
        }
        $this->setTTLInner( $i_stOffset, $i_xValue, $i_fTTL );
    }


    private function setTTLInner( string $i_stOffset, mixed $x, ?float $fTTL ) : void {
        $this->rData->offsetSet( $i_stOffset, self::wrap( $x, $fTTL ) );
    }


}
