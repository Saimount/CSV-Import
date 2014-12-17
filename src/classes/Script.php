<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class Script {

    public $timestart;

    public function __construct() {
        \cli\Colors::enable(); // Forcefully enable
        $this->timestart = microtime(true);
        \cli\line( "Début du script: ".date("H:i:s", $this->timestart) );
    }

    public function notice( $msg ) {
        \cli\line('%G' . $msg . '%n');
    }

    public function warning( $msg ) {
        \cli\line('%Y' . $msg . '%n');
    }

    public function error( $msg ) {
        \cli\line('%R' . $msg . '%n');
    }

    public function __destruct() {
        $timeend=microtime(true);
        $time=$timeend-$this->timestart;
        $page_load_time = number_format($time, 3);
        \cli\line( "Fin du script: ".date("H:i:s", $timeend) );
        \cli\line( "Script exécuté en " . $page_load_time . " sec" );
    }

}

?>
