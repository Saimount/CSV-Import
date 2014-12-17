<?php

class PdoManager {

    protected $user;
    protected $password;
    protected $database;
    protected $host;
    protected $db;

    public function __construct( $user, $password, $database, $host='localhost', $options=array(PDO::MYSQL_ATTR_LOCAL_INFILE=>1) ) {
        // Create db connexion
        $dsn = "mysql:host=$host;dbname=$database";
        $this->db = new PDO( $dsn, $user, $password, $options );

        // Enable PDOException mode
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function query( $query, $params=null ) {
        $timestart = microtime(true);
        $result = $this->db->prepare( $query );
        if( is_array( $params ) ) {
            foreach( $params as $param ) {
                if( count( $param ) == 3 ) {
                    $result->bindParam( $param[0], $param[1], $param[2] );
                } else {
                    throw new Exception( 'Invalid query parameter' );
                }
            }
        }
        $result->execute();
        $timeend = microtime(true);
        $execution_time = number_format($timeend-$timestart, 3);
        return array(
            "execution_time" => $execution_time,
            "rows" => $result->rowCount(),
        );
    }
}
?>
