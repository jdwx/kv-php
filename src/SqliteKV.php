<?php /** @noinspection PhpComposerExtensionStubsInspection */


declare( strict_types = 1 );


namespace JDWX\KV;


use PDO;


class SqliteKV extends PdoKV {


    public function __construct( string $i_stPath = ':memory:', string $i_stTable = 'kv' ) {
        $pdo = new PDO( "sqlite:{$i_stPath}" );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $pdo->exec( "CREATE TABLE IF NOT EXISTS {$i_stTable} (
            key TEXT PRIMARY KEY, 
            value TEXT 
        )" );
        parent::__construct( $pdo, $i_stTable );
    }


}