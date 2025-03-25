<?php


declare( strict_types = 1 );


namespace JDWX\KV;


/** @phpstan-ignore-next-line */
interface StringKVInterface extends \ArrayAccess {


    public function flush() : void;


    /** @return \Generator<string, string> */
    public function walk() : \Generator;


}
