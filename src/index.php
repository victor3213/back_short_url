<?php
include_once(__DIR__.'/vendor/autoload.php');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header("Access-Control-Expose-Headers: Content-Length, X-JSON");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
error_reporting(E_ALL);
ini_set('display_errors', '1');

use SRC\DataBase\Mysql;
use SRC\API\ApiWeb;

try {
    $dataPOST = trim(file_get_contents('php://input'));
    $data = new ApiWeb($_GET, $dataPOST);
    $data->response();
} catch (\Throwable $th) {
   var_dump('error');
}
