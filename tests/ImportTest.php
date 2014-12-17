<?php

class ImportTest extends PHPUnit_Framework_TestCase {

    public static $pdo = null;

    // Called at begin of tests
    public static function setUpBeforeClass()
    {
        if (self::$pdo == null) {
            self::$pdo = new PdoManager( $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'], $GLOBALS['DB_DBNAME'], $GLOBALS['DB_HOST'] );
        }
    }

    public function importFile( $file ) {
        $import = new Import( self::$pdo );
        self::$pdo->query("CREATE TEMPORARY TABLE IF NOT EXISTS temporary_products LIKE products;");
        $result = $import->importFromCSV( $file, 'temporary_products', ';' );
        self::$pdo->query("DROP TEMPORARY TABLE temporary_products;");
        return $result;
    }

    /**
     * @expectedException              PDOException
     * @expectedExceptionMessageRegExp |.*Syntax error.*|
     */
    public function testBaqQuery() {
        self::$pdo->query("Bad query.");
    }

    /**
     * @expectedException              Exception
     * @expectedExceptionMessageRegExp |.*File is not readable.*|
     */
    public function testBadFile() {
        $this->importFile( 'badfile.csv' );
    }

    public function testImport() {
        $result = $this->importFile( 'files/exemple.csv' );
		$this->assertEquals( $result['rows'], 5 );
    }
}
?>
