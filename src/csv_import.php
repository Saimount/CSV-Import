<?php
require_once __DIR__ . '/classes/Script.php';
require_once __DIR__ . '/classes/Import.php';

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

$script = new Script();

/*
 * Script parameters
 * */
$arguments = new \cli\Arguments(compact('strict'));
$arguments->addFlag(array('verbose', 'v'), array(
    'description' => 'Turn on verbose output',
));
$arguments->addFlag(array('help'), 'Show this help screen');

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

$arguments->addOption(array('delimiter'), array(
    'default' => ';',
    'description' => 'CSV delimiter'));

$arguments->parse();
if ($arguments['help']) {
    echo $arguments->getHelpScreen();
    echo "\n\n";
}

/*
 * Get parameters values
 * */
try {
    if ( isset( $arguments['user'] ) ) {
        $user = $arguments['user'];
    } else {
        throw new Exception( "MySQL user is required.");
    }
    if ( isset( $arguments['password'] ) ) {
        $password = $arguments['password'];
    } else {
        throw new Exception( "MySQL password is required.");
    }
    if ( isset( $arguments['host'] ) ) {
        $host = $arguments['host'];
    } else {
        $host = 'localhost';
    }
    if ( isset( $arguments['delimiter'] ) ) {
        $delimiter = $arguments['delimiter'];
    } else {
        $delimiter = ';';
    }
    if ( isset( $arguments['database'] ) ) {
        $database = $arguments['database'];
    } else {
        throw new Exception( "MySQL database is required.");
    }
    if ( isset( $arguments['file'] ) ) {
        $file = $arguments['file'];
    } else {
        throw new Exception( "File is required." );
    }
    /*
     * Start script
     * */

    try {
        $pdo = new PdoManager(
            $user,
            $password,
            $database,
            $host,
            array(
                PDO::MYSQL_ATTR_LOCAL_INFILE => 1,
                PDO::MYSQL_ATTR_FOUND_ROWS => false
            )
        );
        $import = new Import( $pdo );
        $result = $import->importFromCSV( $file, 'products', $delimiter );
        $script->notice( "Affected rows : " . $result['rows'] );
        $script->notice( "Query execution time : " . $result['execution_time'] . ' sec' );
    } catch( PDOException $e ) {
        $script->error( "MySQL error :" );
        $script->error( $e->getMessage() );
    } catch( Exception $e ) {
        $script->error( $e->getMessage() );
    }

} catch (Exception $e) {
    $script->error( $e->getMessage() );
    \cli\line( "" );
    \cli\line( "Usage :" );
    \cli\line( $arguments->getHelpScreen() );
    \cli\line( "" );
}



?>
