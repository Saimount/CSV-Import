<?php

require_once "PdoManager.php";

class Import {
    
    protected $pdo;

    public function __construct( PdoManager $pdo ) {
        $this->pdo = $pdo;
    }

    public function importFromCSV( $file, $table, $delimiter=';' ) {
        if ( is_readable( $file ) ) {
            $query = "LOAD DATA LOCAL INFILE :file REPLACE INTO TABLE " . $table . " FIELDS TERMINATED BY :delimiter IGNORE 1 LINES;";
            $result = $this->pdo->query( $query,
                array(
                    array( ':file', $file, PDO::PARAM_STR ),
                    // array( ':table', $table, PDO::PARAM_STR ),
                    array( ':delimiter', $delimiter, PDO::PARAM_STR )
                )
            );
            return $result;
        } else {
            throw new Exception( "File is not readable." );
        }
    }
}
?>
