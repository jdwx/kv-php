<?php


declare( strict_types = 1 );


use JDWX\KV\ArrayKV;
use JDWX\KV\StrictWrapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


/**
 * This test belongs in StrictWrapperTest, but currently
 * outright crashes Phan.
 */
#[CoversClass( StrictWrapper::class )]
final class LonelyTest extends TestCase {


    public function testOffsetSetForInvalidKey() : void {
        $r = new ArrayKV();
        $kv = new StrictWrapper( $r );
        self::expectException( InvalidArgumentException::class );
        /** @phpstan-ignore-next-line */
        $kv[ 4.2 ] = 'bar';
    }


}
