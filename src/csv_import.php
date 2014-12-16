<?php

if (php_sapi_name() != 'cli') {
	die('Must run from command line');
}

/*
 * PHP configuration
 * */
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('log_errors', 0);
ini_set('html_errors', 0);
$timestart=microtime(true);


/*
 * Tools
 * */
require_once __DIR__ . '/../vendor/autoload.php';
\cli\Colors::enable(); // Forcefully enable

function notice( $msg ) {
    \cli\line('%G' . $msg . '%n');
}

function warning( $msg ) {
    \cli\line('%Y' . $msg . '%n');
}

function error( $msg ) {
    \cli\line('%R' . $msg . '%n');
}


/*
 * Cli declaration
 * */
$arguments = new \cli\Arguments(compact('strict'));
$arguments->addFlag(array('verbose', 'v'), array(
    'description' => 'Turn on verbose output',
));
$arguments->addFlag(array('help'), 'Show this help screen');

$arguments->addOption(array('limit', 'l'), array(
    'default' => -1,
    'description' => 'Number of lines to import'));

$arguments->addOption(array('user', 'u'), array(
    'description' => 'Mysql user'));

$arguments->addOption(array('password', 'p'), array(
    'description' => 'Mysql password'));

$arguments->addOption(array('database', 'd'), array(
    'description' => 'Mysql database'));

$arguments->addOption(array('host', 'h'), array(
    'default' => 'localhost',
    'description' => 'Mysql host'));

$arguments->addOption(array('file', 'f'), array(
    'description' => 'Input csv file'));

$arguments->parse();
if ($arguments['help']) {
    echo $arguments->getHelpScreen();
    echo "\n\n";
}

/*
 * Get options
 * */
try {
    if ( isset( $arguments['user'] ) ) {
        $user = $arguments['user'];
    } else {
        throw new Exception( "MySQL user is required.");
    }
    if ( isset( $arguments['password'] ) ) {
        $password = explode( ' ', $arguments['password'] )[0];
    } else {
        throw new Exception( "MySQL password is required.");
    }
    if ( isset( $arguments['host'] ) ) {
        $host = $arguments['host'];
    } else {
        $host = 'localhost';
    }
    if ( isset( $arguments['database'] ) ) {
        $database = $arguments['database'];
    } else {
        throw new Exception( "MySQL database is required.");
    }
    if ( isset( $arguments['file'] ) && is_readable( $arguments['file'] ) ) {
        $file = $arguments['file'];
    } else {
        throw new Exception( "File is not readable." );
    }
} catch (Exception $e) {
    error( $e->getMessage() );
    \cli\line( $arguments->getHelpScreen() );
}


$query = "LOAD DATA LOCAL INFILE '" . $file . "' REPLACE INTO TABLE products FIELDS TERMINATED BY ',' IGNORE 1 LINES;\"; ";

/*$dsn = "mysql:host=$host;dbname=$database";
$pdo = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_LOCAL_INFILE=>1));
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec($query);*/

// L'appel MySQL se fait de cette manière à cause de l'option MySQl --local-infile.
// Cette option nécessite une configuration MySQL ; ainsi, la requête est faite comme ça pour la portabilité du code dans le cadre de cet exercice technique.
exec("mysql -u $user -p$password --local-infile -e \"USE $database; $query");

$timeend=microtime(true);
$time=$timeend-$timestart;
$page_load_time = number_format($time, 3);
\cli\line( "Début du script: ".date("H:i:s", $timestart) );
\cli\line( "Fin du script: ".date("H:i:s", $timeend) );
\cli\line( "Script exécuté en " . $page_load_time . " sec" );
?>
