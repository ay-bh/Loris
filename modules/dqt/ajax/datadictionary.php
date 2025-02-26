<?php declare(strict_types=1);

/**
 * Data Querying Module
 *
 * PHP Version 5
 *
 * @category Data_Querying_Module
 * @package  Loris
 * @author   Loris Team <loris-dev@bic.mni.mcgill.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://www.github.com/aces/Loris/
 */
$user =& User::singleton();
if (!$user->hasPermission('dataquery_view')) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}
require_once __DIR__ . '/../../../vendor/autoload.php';
$client = new NDB_Client();
$client->makeCommandLine();
$client->initialize(__DIR__ . "/../../../project/config.xml");

$config      = \NDB_Config::singleton();
$couchConfig = $config->getSetting('CouchDB');
$cdb         = \NDB_Factory::singleton()->couchDB(
    $couchConfig['dbName'],
    $couchConfig['hostname'],
    intval($couchConfig['port']),
    $couchConfig['admin'],
    $couchConfig['adminpass']
);

$results = [];
if (isset($_REQUEST['category']) && $_REQUEST['category']) {
    $category = urlencode($_REQUEST['category']);

    $results = $cdb->queryView(
        "DQG-2.0",
        "datadictionary",
        [
            "reduce"   => "false",
            "startkey" => "[\"$category\"]",
            "endkey"   => "[\"$category\", \"ZZZZZZZZ\"]",
        ]
    );
} else if (isset($_REQUEST['keys']) && $_REQUEST['keys']) {
    $keys = json_decode($_REQUEST['keys']);
    foreach (array_keys($keys) as $index) {
        $key   = explode('%2C', urlencode($keys[$index] ?? ''));
        $query = $cdb->queryView(
            "DQG-2.0",
            "datadictionary",
            [
                "reduce" => "false",
                "key"    => "[\"$key[0]\",\"$key[1]\"]",
            ]
        );

        array_push($results, $query[0]);
    }
}

print json_encode($results);
