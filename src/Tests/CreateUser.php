<?php

$arr = [
    'firstName'=>'admin',
    'lastName' => 'admin',
    'login'=>'admin',
    'password'=>'qweqwe',
    'action'=>'registerUser',
    'role'=>'1'
];
$data = http_build_query($arr);
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://167.235.192.111:90/api?'.$data,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;