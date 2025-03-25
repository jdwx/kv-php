<?php


declare( strict_types = 1 );


use JDWX\KV\ArrayKV;
use JDWX\KV\JsonWrapper;
use JDWX\KV\TTLWrapper;
use PHPUnit\Framework\TestCase;


class TTLWrapperTest extends TestCase {


    public function testConstructForInvalidTTL() : void {
        self::expectException( InvalidArgumentException::class );
        $this->newKV( -1 );
    }


    /**
     * @noinspection PhpArrayIndexImmediatelyRewrittenInspection
     * @noinspection PhpConditionAlreadyCheckedInspection
     */
    public function testOffsetExists() : void {
        [ $kv, $r ] = $this->newKV();
        assert( $kv instanceof TTLWrapper );
        self::assertFalse( isset( $kv[ 'foo' ] ) );
        $r[ 'foo' ] = [ 'data' => '"bar"', 'ttl' => '1', 'expires' => time() + 5 ];
        self::assertTrue( isset( $kv[ 'foo' ] ) );
        $r[ 'foo' ] = [ 'data' => '"bar"', 'ttl' => '1', 'expires' => time() - 5 ];
        self::assertFalse( isset( $kv[ 'foo' ] ) );
    }


    /** @noinspection PhpArrayIndexImmediatelyRewrittenInspection */
    public function testOffsetGet() : void {
        [ $kv, $r ] = $this->newKV();
        assert( $kv instanceof TTLWrapper );
        self::assertNull( $kv[ 'foo' ] );
        $r[ 'foo' ] = [ 'data' => 'bar', 'ttl' => '1', 'expires' => time() + 5 ];
        self::assertSame( 'bar', $kv[ 'foo' ] );
        $r[ 'foo' ] = [ 'data' => '"bar"', 'ttl' => '1', 'expires' => time() - 5 ];
        self::assertNull( $kv[ 'foo' ] );
    }


    public function testOffsetGetForInvalidData() : void {
        [ $kv, $r ] = $this->newKV();
        assert( $kv instanceof TTLWrapper );
        $r[ 'foo' ] = 'bar';
        self::expectException( RuntimeException::class );
        $x = $kv[ 'foo' ];
        unset( $x );
    }


    public function testOffsetGetForRefreshTTL() : void {
        [ $kv, $r ] = $this->newKV( 5, true );
        assert( $kv instanceof TTLWrapper );
        $kv[ 'foo' ] = 'bar';
        $fExpires = $r[ 'foo' ][ 'expires' ];
        usleep( 100_000 );
        self::assertSame( 'bar', $kv[ 'foo' ] );
        self::assertGreaterThan( $fExpires, $r[ 'foo' ][ 'expires' ] );
    }


    public function testOffsetSet() : void {
        [ $kv, $r ] = $this->newKV();
        assert( $kv instanceof TTLWrapper );
        self::assertNull( $kv[ 'foo' ] );
        $kv[ 'foo' ] = 'bar';
        self::assertTrue( isset( $r[ 'foo' ] ) );
        self::assertSame( 'bar', $kv[ 'foo' ] );
        usleep( 200_000 );
        self::assertNull( $kv[ 'foo' ] );
    }


    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public function testOffsetUnset() : void {
        [ $kv, $r ] = $this->newKV();
        assert( $kv instanceof TTLWrapper );
        $r[ 'foo' ] = [ 'data' => 'bar', 'ttl' => '1', 'expires' => time() + 5 ];
        unset( $kv[ 'foo' ] );
        self::assertFalse( isset( $r[ 'foo' ] ) );
    }


    public function testSetWithTTL() : void {
        [ $kv, ] = $this->newKV( 5 );
        assert( $kv instanceof TTLWrapper );
        $kv->setWithTTL( 'foo', 'bar', 0.1 );
        self::assertSame( 'bar', $kv[ 'foo' ] );
        usleep( 200_000 );
        self::assertNull( $kv[ 'foo' ] );
    }


    public function testSetWithTTLForInvalidTTL() : void {
        [ $kv, ] = $this->newKV();
        self::expectException( InvalidArgumentException::class );
        $kv->setWithTTL( 'foo', 'bar', -1 );
    }


    /** @return mixed[] */
    private function newKV( float $i_fTTL = 0.1, bool $i_bRefreshTTL = false ) : array {
        $r = new JsonWrapper( new ArrayKV() );
        $kv = new TTLWrapper( $r, $i_fTTL, $i_bRefreshTTL );
        return [ $kv, $r ];
    }


}
