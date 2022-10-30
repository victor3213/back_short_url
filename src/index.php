<?php
include_once(__DIR__.'/vendor/autoload.php');

error_reporting(E_ALL);
ini_set('display_errors', '1');

use SRC\DataBase\Mysql;
use SRC\API\ApiWeb;
// phpinfo();
// Mysql::exec('show tables;');
try {
    $dataPOST = trim(file_get_contents('php://input'));
    var_dump($_GET);
    $data = new ApiWeb($_GET, $dataPOST);
    $data->response();
} catch (\Throwable $th) {
    //logg
   var_dump('error', 0000000000000000);
}

$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
// var_dump($actual_link);
// var_dump($_GET);
exit;