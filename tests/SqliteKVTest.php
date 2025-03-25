<?php


declare( strict_types = 1 );


use JDWX\KV\SqliteKV;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( SqliteKV::class )]
final class SqliteKVTest extends TestCase {


    public function testConstruct() : void {
        $kv = new SqliteKV( ':memory:' );
        $kv[ 'foo' ] = 'bar';
        self::assertSame( 'bar', $kv[ 'foo' ] );
    }


}