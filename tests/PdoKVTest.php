<?php


declare( strict_types = 1 );


use JDWX\KV\PdoKV;
use JDWX\KV\StringKVInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( PdoKV::class )]
final class PdoKVTest extends TestCase {


    public function testFlush() : void {
        $kv = $this->newKV();
        $kv[ 'foo' ] = 'bar';
        $kv->flush();
        self::assertNull( $kv[ 'foo' ] );
    }


    public function testOffsetExists() : void {
        $kv = $this->newKV();
        $kv[ 'foo' ] = 'bar';
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertTrue( isset( $kv[ 'foo' ] ) );
        self::assertFalse( isset( $kv[ 'baz' ] ) );
    }


    public function testOffsetGet() : void {
        $kv = $this->newKV();
        $kv[ 'foo' ] = 'bar';
        self::assertSame( 'bar', $kv[ 'foo' ] );

        self::assertNull( $kv[ 'baz' ] );
    }


    public function testOffsetSet() : void {
        $kv = $this->newKV();
        $kv[ 'foo' ] = 'bar';
        self::assertSame( 'bar', $kv[ 'foo' ] );
    }


    /** @noinspection PhpConditionAlreadyCheckedInspection */
    public function testOffsetUnset() : void {
        $kv = $this->newKV();
        $kv[ 'foo' ] = 'bar';
        unset( $kv[ 'foo' ] );
        self::assertFalse( isset( $kv[ 'foo' ] ) );
    }


    public function testWalk() : void {
        $kv = $this->newKV();
        $kv[ 'foo' ] = 'bar';
        $kv[ 'baz' ] = 'qux';
        $r = iterator_to_array( $kv->walk() );
        self::assertSame( [ 'foo' => 'bar', 'baz' => 'qux' ], $r );
    }


    private function newKV() : StringKVInterface {
        $pdo = new PDO( 'sqlite::memory:' );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $pdo->exec( /** @lang text */ 'CREATE TABLE IF NOT EXISTS kv (
            key TEXT PRIMARY KEY, 
            value TEXT 
        )' );
        return new PdoKV( $pdo, 'kv' );
    }


}
