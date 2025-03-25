<?php /** @noinspection PhpComposerExtensionStubsInspection */


declare( strict_types = 1 );


namespace JDWX\KV;


use PDO;
use PDOStatement;


class PdoKV implements StringKVInterface {


    private PDOStatement $stmtExists;

    private PDOStatement $stmtGet;

    private PDOStatement $stmtSet;

    private PDOStatement $stmtDelete;


    public function __construct( private readonly PDO $pdo, readonly string $i_stTable = 'kv' ) {
        $this->stmtExists = $this->pdo->prepare( "SELECT COUNT(*) FROM {$i_stTable} WHERE key = :key" );
        $this->stmtGet = $this->pdo->prepare( "SELECT value FROM {$i_stTable} WHERE key = :key" );
        /** @noinspection SqlIdentifier */
        $this->stmtSet = $this->pdo->prepare( "INSERT OR REPLACE INTO {$i_stTable} 
            (key, value) VALUES 
            (:key, :value)"
        );
        $this->stmtDelete = $this->pdo->prepare( "DELETE FROM {$i_stTable} WHERE key = :key" );
    }


    public function flush() : void {
        /** @noinspection SqlWithoutWhere */
        $this->pdo->exec( "DELETE FROM {$this->i_stTable}" );
    }


    /** @param string $offset */
    public function offsetExists( $offset ) : bool {
        $this->stmtExists->execute( [ ':key' => $offset ] );
        return (bool) $this->stmtExists->fetchColumn();
    }


    /**
     * @param string $offset
     * @return ?string
     */
    public function offsetGet( $offset ) : ?string {
        $this->stmtGet->execute( [ ':key' => $offset ] );
        $row = $this->stmtGet->fetch( PDO::FETCH_ASSOC );
        if ( $row === false ) {
            return null;
        }
        return $row[ 'value' ];
    }


    /**
     * @param string $offset
     * @param string $value
     */
    public function offsetSet( $offset, $value ) : void {
        $this->stmtSet->execute( [ ':key' => $offset, ':value' => $value ] );
    }


    /** @param string $offset */
    public function offsetUnset( $offset ) : void {
        $this->stmtDelete->execute( [ ':key' => $offset ] );
    }


    public function walk() : \Generator {
        $stmt = $this->pdo->query( "SELECT key, value FROM {$this->i_stTable}" );
        if ( $stmt === false ) {
            throw new \RuntimeException( 'Failed to query keys.' );
        }
        while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
            yield $row[ 'key' ] => $row[ 'value' ];
        }
    }


}
